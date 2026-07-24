<?php
/**
 * AsteriskManager — very small AMI (Asterisk Manager Interface) client.
 *
 * Handles Login, Originate, Hangup, Redirect (transfer), PlayDTMF, and
 * arbitrary Action commands over a raw TCP socket.
 *
 * When `asterisk/config.php` returns `enabled => false`, all methods return a
 * mocked success response so the rest of the app keeps working while your PBX
 * is being wired up.
 *
 * -----------------------------------------------------------------------
 * Usage:
 *   $ast = new AsteriskManager();
 *   $ast->originate('SIP/twilio', '+13055550147', 'John Doe');
 *   $ast->hangup('SIP/twilio-00000123');
 *   $ast->transfer($channel, '2000');
 *   $ast->playDtmf($channel, '1');
 * -----------------------------------------------------------------------
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

  // -------------- High-level actions --------------

  public function originate(string $sipChannel, string $extension, string $callerId = ''): array
  {
    return $this->send('Originate', [
      'Channel'  => $sipChannel,
      'Exten'    => $extension,
      'Context'  => $this->cfg['context'],
      'Priority' => 1,
      'CallerID' => $callerId ?: $this->cfg['caller_id'],
      'Timeout'  => 30000,
      'Async'    => 'true',
    ]);
  }

  public function hangup(string $channel): array
  {
    return $this->send('Hangup', ['Channel' => $channel]);
  }

  public function transfer(string $channel, string $extension): array
  {
    return $this->send('Redirect', [
      'Channel'  => $channel,
      'Exten'    => $extension,
      'Context'  => $this->cfg['context'],
      'Priority' => 1,
    ]);
  }

  public function playDtmf(string $channel, string $digit): array
  {
    return $this->send('PlayDTMF', ['Channel' => $channel, 'Digit' => $digit]);
  }

  public function mute(string $channel, bool $on = true): array
  {
    return $this->send('MuteAudio', ['Channel'=>$channel, 'Direction'=>'in', 'State'=>$on?'on':'off']);
  }

  public function hold(string $channel, bool $on = true): array
  {
    return $this->send($on ? 'MOHStart' : 'MOHStop', ['Channel' => $channel]);
  }

  // -------------- Low-level AMI --------------

  public function send(string $action, array $params = []): array
  {
    if (!$this->isEnabled()) {
      return ['ok'=>true, 'mocked'=>true, 'action'=>$action, 'params'=>$params];
    }
    if (!$this->connect() || !$this->login()) {
      return ['ok'=>false, 'message'=>'AMI connection failed'];
    }
    $msg = "Action: {$action}\r\n";
    foreach ($params as $k => $v) $msg .= "{$k}: {$v}\r\n";
    $msg .= "\r\n";
    fwrite($this->sock, $msg);

    $response = $this->readResponse();
    $this->logoff();
    return ['ok'=>str_contains($response, 'Response: Success'), 'raw'=>$response];
  }

  private function connect(): bool
  {
    if (is_resource($this->sock)) return true;
    $errno = 0; $errstr = '';
    $this->sock = @fsockopen($this->cfg['host'], (int)$this->cfg['port'], $errno, $errstr, (int)$this->cfg['timeout']);
    if (!$this->sock) return false;
    stream_set_timeout($this->sock, (int)$this->cfg['timeout']);
    // Read banner
    fgets($this->sock, 1024);
    return true;
  }

  private function login(): bool
  {
    $msg = "Action: Login\r\nUsername: {$this->cfg['username']}\r\nSecret: {$this->cfg['secret']}\r\n\r\n";
    fwrite($this->sock, $msg);
    $r = $this->readResponse();
    return str_contains($r, 'Response: Success');
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
    $out = '';
    $start = microtime(true);
    while (!feof($this->sock)) {
      $line = fgets($this->sock, 1024);
      if ($line === false) break;
      $out .= $line;
      if (rtrim($line) === '') break; // AMI packets end with blank line
      if (microtime(true) - $start > $this->cfg['timeout']) break;
    }
    return $out;
  }
}
