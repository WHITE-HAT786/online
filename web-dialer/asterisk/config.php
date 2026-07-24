<?php
/**
 * Asterisk connection config.
 *
 *   Edit the values below to match your Asterisk server.
 *   Enable AMI in /etc/asterisk/manager.conf:
 *
 *     [general]
 *     enabled = yes
 *     port = 5038
 *     bindaddr = 0.0.0.0
 *
 *     [webdialer]
 *     secret = your_ami_password
 *     read  = system,call,log,verbose,command,agent,user,config,dtmf,reporting,cdr,dialplan
 *     write = system,call,agent,user,command,reporting,originate
 */
return [
  'host'        => '127.0.0.1',
  'port'        => 5038,
  'username'    => 'webdialer',
  'secret'      => 'your_ami_password',
  'timeout'     => 5,             // socket timeout in seconds
  'context'     => 'from-internal',
  'channel_tech'=> 'PJSIP',       // or 'SIP'
  'caller_id'   => 'WebDialer <1000>',
  'enabled'     => false,         // set true when your Asterisk server is ready
];
