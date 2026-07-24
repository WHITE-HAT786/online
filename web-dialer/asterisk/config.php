<?php
/**
 * Asterisk connection config.
 * Two modes:
 *   1) `bridge`  → call the FastAPI bridge on your Debian PBX (recommended)
 *   2) `ami`     → open a direct TCP socket to Asterisk AMI
 */
return [
  // -------- Mode -------------------------------------------------
  'mode'        => 'bridge',   // 'bridge' | 'ami' | 'mock'

  // -------- Bridge mode (FastAPI) --------------------------------
  // Reads DIALER_API_URL from env vars (recommended) or falls back to the value below.
  'bridge_url'  => getenv('DIALER_API_URL') ?: 'http://209.38.71.228:8766',
  'bridge_key'  => getenv('DIALER_API_KEY') ?: '',   // optional Bearer token

  // -------- AMI mode (direct TCP socket) -------------------------
  'host'        => '127.0.0.1',
  'port'        => 5038,
  'username'    => 'webdialer',
  'secret'      => 'your_ami_password',
  'timeout'     => 5,

  // -------- Common defaults --------------------------------------
  'context'     => 'from-internal',
  'channel_tech'=> 'PJSIP',           // or 'SIP'
  'caller_id'   => 'WebDialer <1000>',
  'enabled'     => true,              // flip to false → mock
];
