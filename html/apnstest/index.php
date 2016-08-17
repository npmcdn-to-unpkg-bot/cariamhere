<?php

  $payload['aps'] = array('alert' => 'This is the alert text', 'badge' => 1, 'sound' => 'default');
  $payload = json_encode($payload);

  $deviceToken = '8e58c01ca1953f555b396fefea06dfef';
print $payload;


  $apnsHost = 'gateway.sandbox.push.apple.com';
  $apnsPort = 2195;
  $apnsCert = 'apns-dis.pem';

  $streamContext = stream_context_create();
  stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

  $url = "ssl://$apnsHost:$apnsPort";
  $apns = stream_socket_client($url, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
print_r($apns);
print("error: $error");
print("error: $errorString");

  $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
//print_r($apnsMessage);

  fwrite($apns, $apnsMessage);

  socket_close($apns);
  fclose($apns);
print("done");

 
?>
