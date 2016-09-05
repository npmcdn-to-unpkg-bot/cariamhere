<?php

  include_once("../path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.telegram.php");

  $clstg = new telegram();

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
if ($mode == 'dosend') {
  //dd($form);
  $msg = $form['msg'];

  $clstg = new telegram(1);
  $clstg->send_all($msg);

  $msg = "전송하였습니다.";
  $url = "$env[self]";
  InformRedir($msg, $url);
  exit;
}
if ($mode == 'send') {
  MainPageHead('Home');
  ParagraphTitle('Home');

  $html = textarea_general('msg', '', $cols='40', $rows='5', true, '');
  print<<<EOS
<form name='form' action='$env[self]' method='post'>
메시지:
$html
<input type='hidden' name='mode' value='dosend'>
<input type='submit' value='전송'>
</form>
EOS;
  MainPageTail();
  exit;
}

  MainPageHead('Home');
  ParagraphTitle('Home');

  print<<<EOS
<p><a href='$env[self]?mode=register'>사용자 등록</a>
<p><a href='$env[self]?mode=send'>메시지 전송(전체)</a>
<p><a href='send.php'>메시지 전송 (개인)</a>
EOS;

  MainPageTail();
  exit;

?>
