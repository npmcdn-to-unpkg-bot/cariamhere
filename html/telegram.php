<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.telegram.php");

  $clstg = new telegram();

/*
// 사용자가 봇 대화창에 남긴 전화번호를 이용하여 사용자를 찾는다.
if ($mode == 'register') {
  $messages = $clstg->getUpdate();
  //dd($messages);

  $cnt = 0;
  foreach ($messages as $msg) {
    $cnt++;

    $from_id = $msg['message']['from']['id'];
    $text = $msg['message']['text'];
    print("$from_id $text <br>");

    $tel = $text;
    $qry = "select * from driver where driver_tel='$tel'";
    $row = db_fetchone($qry);
    if ($row) {
      $driver_id = $row['id'];
      //dd($row);
      $driver_name = $row['driver_name'];

      $qry = "update driver set chat_id='$from_id' where id='$driver_id'";
      //db_query($qry);

      $text = "**$tel** **$driver_name** 님 등록 되었습니다.";
      $clstg->sendMessage($from_id, $text);
      dd($text);

    } else {
      $text = "전화번호를 입력하세요. -빼고 숫자만 입력하세요. 예) 01012341111";
      $clstg->sendMessage($from_id, $text);
    }

  }
  print("$cnt messages done<br>");
  exit;
}
*/

if ($mode == 'dosend') {
  //dd($form);
  $msg = $form['msg'];
  $chat_id = $form['chat_id'];

  $clstg->send_msg_post($chat_id, $msg, 1);

  $msg = "전송하였습니다.";
  $url = "$env[self]";
  InformRedir($msg, $url);
  exit;
}

  MainPageHead('메시지 전송');
  ParagraphTitle('메시지 전송');

  $chat_id = $form['cid'];

  $html = textarea_general('msg', '', $cols='40', $rows='5', true, '');
  print<<<EOS
<form name='form' action='$env[self]' method='post'>
메시지:
$html
<input type='hidden' name='chat_id' value='$chat_id'>
<input type='hidden' name='mode' value='dosend'>
<input type='submit' value='전송'>
</form>
EOS;
  MainPageTail();
  exit;

?>
