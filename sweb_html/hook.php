<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.sweb.php");
  include_once("$env[prefix]/inc/class.telegram.php");


### {{{
function _register($appkey, $from_chat_id) {
  $clstg = new telegram();

  $qry = "select * from driver where apikey='$appkey'";
  $driver_row = db_fetchone($qry);
  if ($driver_row) {
    $cid = $driver_row['chat_id'];

      $qry = "update driver set chat_id='$from_chat_id', bot1con=1  where apikey='$appkey'";
      db_query($qry);

      $name = $driver_row['driver_name'];
      $text = "반갑습니다. $name 님!\n"
        ."\n"
        ."918 의전차량 봉사를 해주셔서 감사드립니다.\n"
        ."이 대화창을 통해 의전차량 이동 현황을 실시간 알려드립니다.\n"
        ."\n"
        ."안전운전 하세요~~~~\n"
        ;
      $ret = $clstg->send_msg_post($from_chat_id, $text, 0);

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

  $chat = $message['chat'];
  $chat_id = $chat['id']; // chat_id (group or person)
  $chat_type = $chat['type']; // group or ??

  $text = $message['text'];

  $mtype = 0; // this hook's mtype=0

  $clstg = new telegram();
  $clstg->hook_log($from_chat_id, $data, $mtype);

  // 사용자 등록 (/start)
  if (preg_match("/\/start /", $text)) {
    list($a, $appkey) = preg_split("/ /", $text);
    _register($appkey, $from_chat_id);
  }

  if ($chat_type == 'group') {
    $text = "그룹방에 저를 초대해 주셨네요.b^b";
    $clstg->send_msg_post($chat_id, $text, $mtype);

  } else if ($chat_type == 'private') {
    $clstg->private_message($chat_id, $text, $mtype);
  }

?>
