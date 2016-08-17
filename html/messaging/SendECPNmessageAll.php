<?php

  include("../path.php");
  include("$env[prefix]/inc/common.api.php");
  include("settings.php");

### {{{
function send_android_notification($deviceIDs,$message) {
  $message = array("price" => $message);
    
    // Set POST variables
    $url = 'https://android.googleapis.com/gcm/send';
 
    $fields = array(
        'registration_ids' => $deviceIDs,
        'data' => $message,
    );
 
    $headers = array(
        'Authorization: key=' . GOOGLE_API_KEY,
        'Content-Type: application/json'
    );
    // Open connection
    $ch = curl_init();
    
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
    //echo $result;
 
    // Close connection
    curl_close($ch);
}

function send_ios_notification($deviceIDs,$message) {
  $ctx = stream_context_create();
  stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
  stream_context_set_option($ctx, 'ssl', 'passphrase', 'ecpn'); // Add your own ck.pem passphrase here
  
  foreach($deviceIDs as $deviceToken) {
    // Open a connection to the APNS server
    $fp = stream_socket_client(
      'ssl://gateway.sandbox.push.apple.com:2195', $err,
      $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    if (!$fp) exit("Failed to connect: $err $errstr" . PHP_EOL);
    
    echo 'Connected to APNS' . PHP_EOL;
    
    // Create the payload body
    $body['aps'] = array(
      'alert' => $message,
      'sound' => 'default'
      );
    $payload = json_encode($body);
    
    // Build & send notification
    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
    $result = fwrite($fp, $msg, strlen($msg));
    
    /*if (!$result)
      echo 'Message not delivered' . PHP_EOL;
    else
      echo 'Message successfully delivered' . PHP_EOL;*/
  }  
  // Close the connection to the server
  fclose($fp);

}
### }}}


  header("Content-Type:text/html;charset:utf-8");

  //mysql_query("set session character_set_connection=utf8;");
  //mysql_query("set session character_set_results=utf8;");
  //mysql_query("set session character_set_client=utf8;");

  $message = " says hi";
  if($_POST["mmm"])
  $message =  ($_POST["mmm"]);
  if($_POST["midx"]) $midx = strip_tags($_POST["midx"]);
  $sendid = strip_tags($_POST["sendid"]);
  
  $androidIDs = array();
  $iosIDs = array();

  //$temp = iconv('utf8', 'euckr', $message);

  // Get all device ids from table
  $sql = "SELECT e.pushkey FROM user e ";
  if($midx) {
    $sql = "select e.pushkey from user e where e.id = '$midx' ";
    $i_query = "insert into messaging(id, send_id, mmm, reg_date) values('$midx','$sendid','$message', now())";
    db_query($i_query);
  }
  $result = db_query($sql);

  while($row = db_fetch($result)) {
   $contacts[] = $row['pushkey'];
  }

  // For each deviceID, add to either android or iOS bucket
  if(count($contacts) > 0) {
    foreach($contacts as $contact) {

      $query = "SELECT phone_os, id, did FROM user WHERE pushkey = '$contact'";
      $row = db_fetchone($query);

      if ($row) {
      //if(db_affected_rows($result) > 0) {}

        //$os = mysql_result($result,0,"phone_os");
        //$regId = mysql_result($result,0,"did");
        //$midx = mysql_result($result,0,"id");

        $os = $row['phone_os'];
        $regId = $row['did'];
        $midx = $row['id'];

        if ($os == "android") $androidIDs[] = $regId;
        else $iosIDs[] = $regId;
      }
    }

    // sending notifications to android and ios users (if any)
    if(count($androidIDs) > 0) send_android_notification($androidIDs,$message);
    //if(count($iosIDs) > 0) send_ios_notification($iosIDs,$message);
  
    //echo "Messages delivered: " . count($androidIDs) . " android / " . count($iosIDs) . " iOS";
    echo count($androidIDs); //. "/" . count($iosIDs);

  } else {
    echo "0/0";
  }



?>
