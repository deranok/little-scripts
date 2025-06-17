<?php
  $client_secret_file_path = '/home/jeff/oauth/client_secret_msmtp.json';
  $user_token_file_path = '/home/jeff/oauth/user_token.json';
  
  $client_secret = file_get_contents($client_secret_file_path);
  $client_secret = json_decode($client_secret);
  $auth_url = $client_secret->web->auth_uri . '?' . http_build_query([
    'client_id' => $client_secret->web->client_id,
    'response_type' => 'code',
    'prompt' => 'consent',
    'access_type' => 'offline',
    'scope' => 'https://mail.google.com',
    'redirect_uri' => 'http://localhost:8999'
  ]);
  
  $token_url = $client_secret->web->token_uri;

  if(file_exists($user_token_file_path)) {
    // refresh the token
    $user_tokens = file_get_contents($user_token_file_path);
    $user_tokens = json_decode($user_tokens);
    
    $token_request = curl_init($token_url);
      curl_setopt_array($token_request, [
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POSTFIELDS => [
        'grant_type' => 'refresh_token',
        'refresh_token' => $user_tokens->refresh_token,
        'redirect_uri' => 'http://localhost:8999',
        'client_id' => $client_secret->web->client_id,
        'client_secret' => $client_secret->web->client_secret
      ]
    ]);
  
    $token_response = curl_exec($token_request);
    curl_close($token_request);
    
    // return the user token
    $user_tokens = json_decode($token_response);
    print($user_tokens->access_token);
    exit(0);
  }
  
  exec(escapeshellcmd("firefox --private-window $auth_url"));
  $sock_failure = 
    (($sock = socket_create(AF_INET, SOCK_STREAM,  SOL_TCP)) === false) | 
    (socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1) === false) |
    (socket_bind($sock, '127.0.0.1', 8999) === false) |
    (socket_listen($sock) === false);

  if($sock_failure) {
    print(socket_strerror(socket_last_error($sock)));
    print(PHP_EOL);
    exit(1);
  }
  
  $conn = socket_accept($sock);
  if($conn === false) {
    print(socket_strerror(socket_last_error($sock)));
    print(PHP_EOL);
    exit(1);
  }
  
  $buffer = "";
  while(true) {
    $msg_fragment = socket_read($conn, 1024);
    $buffer .= $msg_fragment;
    if($msg_fragment === false) {break;}
    if(strlen($msg_fragment) < 1024) {break;}
  }
  
  $response_msg = "Authz Code received.\n";
  
  $response_buffer = 
    "HTTP/1.1 200 OK\r\n".
    "Content-Type: text/html; charset=utf-8\r\n".
    "Content-Length: " . strlen($response_msg) . "\r\n".
    "Date: " . date('D, d M Y G:i:s e') . "\r\n".
    "Connection: close" . "\r\n".
    "\r\n".
    $response_msg;
  
  socket_write($conn, $response_buffer, strlen($response_buffer));
  
  socket_close($conn);
  
  $authz_line = "";
  $buffer = explode("\r\n", $buffer);
  $authz_codes = [];
  foreach($buffer as $line) {
    if(str_starts_with($line, 'GET')) {
      $authz_line = $line;
      $authz_line = substr($authz_line, 6);
      $authz_lines = explode('&', $authz_line);
      foreach($authz_lines as $sline) {
        $sline = explode('=', $sline, 2);
        $authz_codes[$sline[0]] = urldecode($sline[1]);
      }
      break;
    }
  }
  
  $token_request = curl_init($token_url);
  curl_setopt_array($token_request, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => [
      'grant_type' => 'authorization_code',
      'code' => $authz_codes['code'],
      'redirect_uri' => 'http://localhost:8999',
      'client_id' => $client_secret->web->client_id,
      'client_secret' => $client_secret->web->client_secret
    ]
  ]);
  
  $token_response = curl_exec($token_request);
  curl_close($token_request);
  $fp = fopen($user_token_file_path, 'w');
  fwrite($fp, $token_response);
  fclose($fp);
  
  $conn = socket_accept($sock);
  if($conn === false) {
    print(socket_strerror(socket_last_error($sock)));
    print(PHP_EOL);
    exit(1);
  }
  
  $buffer = "";
  while(true) {
    $msg_fragment = socket_read($conn, 1024);
    $buffer .= $msg_fragment;
    if($msg_fragment === false) {break;}
    if(strlen($msg_fragment) < 1024) {break;}
  }
  
  socket_close($conn);
  socket_close($sock);
  
  $token_response = json_decode($token_response);
  print($token_response->access_token);
