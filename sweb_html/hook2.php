<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.sweb.php");
  include_once("$env[prefix]/inc/class.telegram.php");

  $clstg = new telegram();

### {{{
function _register($appkey, $from_chat_id) {
  $clstg = new telegram();

  $qry = "select * from driver where apikey='$appkey'";
  $driver_row = db_fetchone($qry);
  if ($driver_row) {
    $cid = $driver_row['chat_id'];

      $qry = "update driver set chat_id='$from_chat_id', bot2con=1  where apikey='$appkey'";
      db_query($qry);

      $name = $driver_row['driver_name'];
      $text = "감사합니다. $name 님!\n"
        ."\n"
        ."이 대화창을 통해 의전차량 중요 공지사항을 알려드립니다.\n"
        ."\n"
        ;
      $ret = $clstg->send_msg_post($from_chat_id, $text, 1);

  } else {

    $text = "운전자 등록 정보를 확인할 수 없습니다. 봉교부 관계자에게 문의해 주세요.";
    $ret = $clstg->send_msg_post($from_chat_id, $text, 0);
  }
}
### }}}

  $data = json_decode($HTTP_RAW_POST_DATA, true);

  $update_id = $data['update_id'];
  $message = $data['message'];
  $from = $message['from'];
  $from_chat_id = $from['id'];
  $text = $message['text'];

  // 사용자 등록
  if (preg_match("/\/start /", $text)) {
    list($a, $appkey) = preg_split("/ /", $text);
    if ($appkey != '') _register($appkey, $from_chat_id);
  }

?>
