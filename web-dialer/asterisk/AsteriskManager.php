<?php
/**
 * AsteriskManager — talks to your PBX either via:
 *   • FastAPI bridge  (mode = 'bridge', preferred)  → http://<PBX>:8766/asterisk/*
 *   • Direct AMI      (mode = 'ami')                → TCP socket to Manager port
 *   • Mock            (mode = 'mock' or enabled=false) → returns fake success
 */
class AsteriskManager
{
  private array $cfg;
  private $sock = null;

  public function __construct(?array $cfg = null)
  {
    $this->cfg = $cfg ?? require __DIR__ . '/config.php';
  }

  public function isEnabled(): bool { return !empty($this->cfg['enabled']); }
  public function mode(): string    { return $this->cfg['mode'] ?? 'mock'; }

  // -------------- High-level actions --------------

  public function originate(string $sipChannel, string $extension, string $callerId = ''): array
  {
    if (!$this->isEnabled() || $this->mode() === 'mock') {
      return ['ok'=>true,'mocked'=>true,'action'=>'originate','channel'=>$sipChannel,'exten'=>$extension];
    }

    if ($this->mode() === 'bridge') {
      return $this->bridgePost('/asterisk/originate', [
        'channel'  => $sipChannel,
        'exten'    => $extension,
        'context'  => $this->cfg['context'],
        'priority' => 1,
        'timeout'  => 30000,
        'callerid' => $callerId ?: $this->cfg['caller_id'],
      ]);
    }

    // AMI direct
    return $this->amiSend('Originate', [
      'Channel'  => $sipChannel,
      'Exten'    => $extension,
      'Context'  => $this->cfg['context'],
      'Priority' => 1,
      'CallerID' => $callerId ?: $this->cfg['caller_id'],
      'Timeout'  => 30000,
      'Async'    => 'true',
    ]);
  }

  public function hangup(string $channel): array { return $this->action('Hangup',   ['Channel' => $channel]); }
  public function transfer(string $channel, string $ext): array {
    return $this->action('Redirect', ['Channel' => $channel, 'Exten' => $ext, 'Context' => $this->cfg['context'], 'Priority' => 1]);
  }
  public function playDtmf(string $channel, string $digit): array { return $this->action('PlayDTMF', ['Channel'=>$channel,'Digit'=>$digit]); }
  public function mute(string $channel, bool $on = true): array   { return $this->action('MuteAudio',['Channel'=>$channel,'Direction'=>'in','State'=>$on?'on':'off']); }
  public function hold(string $channel, bool $on = true): array   { return $this->action($on?'MOHStart':'MOHStop', ['Channel'=>$channel]); }

  public function command(string $cli): array
  {
    if ($this->mode() === 'bridge') return $this->bridgePost('/asterisk/command', ['command' => $cli]);
    return $this->amiSend('Command', ['Command' => $cli]);
  }

  // -------------- Internal dispatch --------------

  private function action(string $ami, array $params): array
  {
    if (!$this->isEnabled() || $this->mode() === 'mock') {
      return ['ok'=>true,'mocked'=>true,'action'=>$ami,'params'=>$params];
    }
    if ($this->mode() === 'bridge') {
      // FastAPI bridge exposes /asterisk/command for arbitrary CLI.
      // Map common AMI actions into CLI equivalents where possible.
      $ch = $params['Channel'] ?? '';
      $map = [
        'Hangup'   => "channel request hangup {$ch}",
        'Redirect' => "channel redirect {$ch} " . ($params['Context']??'') . ',' . ($params['Exten']??'') . ',1',
        'PlayDTMF' => "channel send dtmf {$params['Digit']} to {$ch}",
        'MOHStart' => "moh start {$ch}",
        'MOHStop'  => "moh stop {$ch}",
        'MuteAudio'=> ($params['State']??'')==='on' ? "mixmonitor mute {$ch}" : "mixmonitor unmute {$ch}",
      ];
      $cli = $map[$ami] ?? "core show channel {$ch}";
      return $this->bridgePost('/asterisk/command', ['command' => $cli]);
    }
    return $this->amiSend($ami, $params);
  }

  // -------------- FastAPI bridge --------------

  private function bridgePost(string $path, array $body): array
  {
    $url = rtrim($this->cfg['bridge_url'], '/') . $path;
    $ch  = curl_init($url);
    $headers = ['Content-Type: application/json'];
    if (!empty($this->cfg['bridge_key'])) $headers[] = 'Authorization: Bearer ' . $this->cfg['bridge_key'];
    curl_setopt_array($ch, [
      CURLOPT_POST           => true,
      CURLOPT_POSTFIELDS     => json_encode($body),
      CURLOPT_HTTPHEADER     => $headers,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => (int)$this->cfg['timeout'],
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($err) return ['ok'=>false, 'message'=>$err];
    $data = json_decode($res, true);
    return ['ok'=>$code >= 200 && $code < 300, 'status'=>$code, 'response'=>$data ?? $res];
  }

  // -------------- Raw AMI (only used in 'ami' mode) --------------

  private function amiSend(string $action, array $params): array
  {
    if (!$this->connect() || !$this->login()) return ['ok'=>false,'message'=>'AMI connection failed'];
    $msg = "Action: {$action}\r\n";
    foreach ($params as $k => $v) $msg .= "{$k}: {$v}\r\n";
    $msg .= "\r\n";
    fwrite($this->sock, $msg);
    $resp = $this->readResponse();
    $this->logoff();
    return ['ok'=>str_contains($resp, 'Response: Success'), 'raw'=>$resp];
  }

  private function connect(): bool
  {
    if (is_resource($this->sock)) return true;
    $errno = 0; $errstr = '';
    $this->sock = @fsockopen($this->cfg['host'], (int)$this->cfg['port'], $errno, $errstr, (int)$this->cfg['timeout']);
    if (!$this->sock) return false;
    stream_set_timeout($this->sock, (int)$this->cfg['timeout']);
    fgets($this->sock, 1024);
    return true;
  }
  private function login(): bool
  {
    fwrite($this->sock, "Action: Login\r\nUsername: {$this->cfg['username']}\r\nSecret: {$this->cfg['secret']}\r\n\r\n");
    return str_contains($this->readResponse(), 'Response: Success');
  }
  private function logoff(): void
  {
    if (!is_resource($this->sock)) return;
    fwrite($this->sock, "Action: Logoff\r\n\r\n");
    fclose($this->sock);
    $this->sock = null;
  }
  private function readResponse(): string
  {
    $out = ''; $start = microtime(true);
    while (!feof($this->sock)) {
      $line = fgets($this->sock, 1024);
      if ($line === false) break;
      $out .= $line;
      if (rtrim($line) === '') break;
      if (microtime(true) - $start > $this->cfg['timeout']) break;
    }
    return $out;
  }
}
