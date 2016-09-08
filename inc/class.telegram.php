<?php

class telegram {
  var $debug = false;
  var $token;

  // 0 은 모니터링 봇
  // 1 은 공지 봇
  function telegram($which=0) {
     global $conf;
          if ($which == 0) $token = $conf['telegram_token'];
     else if ($which == 1) $token = $conf['telegram_token_notice'];
     $this->url = "https://api.telegram.org/bot$token";
  }

  function dd($obj) {
    $str = var_export($obj, true);
    file_put_contents("/tmp/log.txt", $str, FILE_APPEND);
  }

  function enqueue($chat_id, $text, $mtype=0) {
    $t = db_escape_string($text);
    $qry = "INSERT INTO telegram_send_queue set chat_id='$chat_id', msg='$t', mtype='$mtype', idate=NOW()";
    db_query($qry);
  }

  // 등록된 사용자에게 전체 메시지를 보냄
  function enqueue_all($msg, $mtype=0) {
    $qry = "select chat_id from driver where chat_id != 0";
    $ret = db_query($qry);
    while ($row = db_fetch($ret)) {
      $chat_id = $row['chat_id'];
      $this->enqueue($chat_id, $msg, $mtype);
    }
  }

  function process_queue() {
    $qry = "select * from telegram_send_queue where sflag=0 order by idate";
    $ret = db_query($qry);
    while ($row = db_fetch($ret)) {
      $chat_id = $row['chat_id'];
      $msg = $row['msg'];
      $mtype = $row['mtype'];
      $this->send_msg($chat_id, $msg, $mtype);
    }
  }

  function url($mtype) {
    global $conf;
         if ($mtype == 0) $token = $conf['telegram_token'];
    else if ($mtype == 1) $token = $conf['telegram_token_notice'];
    $url = "https://api.telegram.org/bot$token";
    return $url;
  }

  // 메시지 전송 (사용금지)
  function send_msg($chat_id, $text, $mtype) {
    $url = $this->url($mtype);

    $t = urlencode($text);
    $url .= "/sendMessage?chat_id=$chat_id&text=$t";

    $this->dd($url);
    $r = file_get_contents($url);
    $info = json_decode($r, true);
    //dd($info);
    if ($info['ok']) return true; // success
    return false; // fail
  }



  // 메시지 전송
  function send_msg_post($chat_id, $text, $mtype) {
    if (!$chat_id) return;
    $url = $this->url($mtype)."/sendMessage";

    $postfields = array('chat_id'=>$chat_id, 'text'=>$text);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
    $result = curl_exec($ch);
    //$this->dd($result);

    $info = json_decode($result, true);
    //$this->dd($info);
    if ($info['ok']) return true; // success
    return false; // fail
  }
  // 모니터링용봇
  function send_monitor_bot($chat_id, $obj) {
    if (!$chat_id) return;
    $this->send_log($chat_id, $obj, 0);
    if (is_array($obj)) $text = join("\n", $obj); else $text = "$obj";
    return $this->send_msg_post($chat_id, $text, 0);
  }
  // 공지용봇
  function send_notice_bot($chat_id, $obj) {
    if (!$chat_id) return;
    $this->send_log($chat_id, $obj, 1);
    if (is_array($obj)) $text = join("\n", $obj); else $text = "$obj";
    return $this->send_msg_post($chat_id, $text, 1);
  }
  // 전송 로그
  function send_log($chat_id, $obj, $mtype) {
    $str = json_encode($obj);
    $str = db_escape_string($str);
    $qry = "insert into telegram_send_log set chat_id='$chat_id', msg='$str', mtype='$mtype', idate=now()";
    db_query($qry);
  }

  // 후킹 로그 (webhook 쪽에서 insert)
  function hook_log($chat_id, $postraw, $mtype) {
    $str = json_encode($postraw);
    $str = db_escape_string($str);
    $qry = "insert into telegram_hook_log set chat_id='$chat_id', postraw='$str', mtype='$mtype', idate=now()";
    db_query($qry);
  }


  // 메시지 전송 사용금지
  function sendMessage($chat_id, $text) {
    $t = urlencode($text);
    $url = $this->url."/sendMessage?chat_id=$chat_id&text=$t";
    $r = file_get_contents($url);
    $info = json_decode($r, true);
    //dd($info);
    if ($info['ok']) return true; // success
    return false; // fail
  }

  function getUpdate() {

    $qry = "select * from telegram_update_id";
    $row = db_fetchone($qry);
    $offset = $row['update_id'];

    $url = $this->url."/getUpdates?offset=$offset";
    $c = file_get_contents($url);

    $info = json_decode($c, true);

    if (!$info['ok']) return;

    $max = 0;
    $re = array();
    foreach ($info['result'] as $item) {
      //dd($item);
      $update_id = $item['update_id'];
      if ($update_id > $max) $max = $update_id;
      $re[] = $item;
    }
    $max++;

    $qry = "update telegram_update_id set update_id='$max'";
    db_query($qry);

    return $re;
  }

  // 등록된 사용자에게 전체 메시지를 보냄
  function send_all($msg) {
    $qry = "select chat_id from driver where chat_id != 0";
    $ret = db_query($qry);
    while ($row = db_fetch($ret)) {
      $chat_id = $row['chat_id'];
      $this->sendMessage($chat_id, $msg);
    }
  }



};

?>
