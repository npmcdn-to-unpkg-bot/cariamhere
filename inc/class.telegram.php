<?php

class telegram {
  var $debug = false;
  var $token;

  function telegram($which=0) {
     global $conf;
          if ($which == 0) $token = $conf['telegram_token'];
     else if ($which == 1) $token = $conf['telegram_token_notice'];
     $this->url = "https://api.telegram.org/bot$token";
  }

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
