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

  // 메시지 전송
  function send_msg($chat_id, $text, $mtype) {
    global $conf;
         if ($mtype == 0) $token = $conf['telegram_token'];
    else if ($mtype == 1) $token = $conf['telegram_token_notice'];
    $url = "https://api.telegram.org/bot$token";

    $t = urlencode($text);
    $url .= "/sendMessage?chat_id=$chat_id&text=$t";

    $r = file_get_contents($url);
    $info = json_decode($r, true);
    //dd($info);
    if ($info['ok']) return true; // success
    return false; // fail
  }


  // 메시지 전송
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
