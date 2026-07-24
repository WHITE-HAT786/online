# Asterisk integration

This folder is the **only** place where telephony code lives.  
Backend endpoints in `backend/dialer/*` include `AsteriskManager.php`  
to place calls, hang up, transfer, send DTMF and control media.

## Files

| File                    | Purpose                                                                 |
| ----------------------- | ----------------------------------------------------------------------- |
| `config.php`            | Editable connection info (host / port / AMI user / secret).             |
| `AsteriskManager.php`   | Tiny AMI (Asterisk Manager Interface) client — no external deps.        |

## Enable in real-time

1. On your Asterisk server, enable AMI in `/etc/asterisk/manager.conf`:

   ```
   [general]
   enabled = yes
   port    = 5038
   bindaddr = 0.0.0.0

   [webdialer]
   secret = your_ami_password
   read   = system,call,log,verbose,command,agent,user,config,dtmf,reporting,cdr,dialplan
   write  = system,call,agent,user,command,reporting,originate
   ```

2. Reload manager:  `asterisk -rx "manager reload"`

3. Edit `asterisk/config.php` in this project and set:

   ```php
   'host'    => 'YOUR_PBX_IP',
   'port'    => 5038,
   'username'=> 'webdialer',
   'secret'  => 'your_ami_password',
   'enabled' => true,      // ← flip to TRUE when ready
   ```

That's it. All `backend/dialer/*.php` endpoints will start hitting your PBX.

## Mock mode

When `enabled => false` (default), every AMI action returns
`['ok'=>true,'mocked'=>true, …]` so the frontend keeps working while
you're still setting up the PBX.

## API surface

```php
$ast = new AsteriskManager();
$ast->originate($sipChannel, $extension, $callerIdName);
$ast->hangup($channel);
$ast->transfer($channel, $extension);
$ast->playDtmf($channel, $digit);
$ast->mute($channel, true|false);
$ast->hold($channel, true|false);
$ast->send('AnyAction', ['Key' => 'Val']);   // raw AMI escape hatch
```

Returned payload:

```
['ok' => true|false, 'raw' => "full AMI response", 'mocked' => true]
```
