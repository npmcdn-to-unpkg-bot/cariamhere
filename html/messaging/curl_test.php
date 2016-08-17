<?php

  $ch = curl_init();

  $url = "http://m.naver.com";

  // Set the url, number of POST vars, POST data
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  // Disabling SSL Certificate support temporarly
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

  // Execute post
  $result = curl_exec($ch);
  if ($result === FALSE) {
    die('Curl failed: ' . curl_error($ch));
  }
  echo $result;

  // Close connection
  curl_close($ch);

?>
