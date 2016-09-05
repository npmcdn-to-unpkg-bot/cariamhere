<?php

  include_once("../path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.telegram.php");

  $clstg = new telegram();

### {{{
if ($mode == 'dosend') {
  //dd($form); exit;
  $msg = $form['msg'];
  $chat_id = $form['chat_id'];

  $r = $clstg->sendMessage($chat_id, $msg);
  if ($r) $msg = "전송성공";
  else $msg = "전송실패!!!!";

  $url = "$env[self]";
  InformRedir($msg, $url);
  exit;
}
### }}}

  MainPageHead('Home');
  ParagraphTitle('Home');

  $html = textarea_general('msg', '', $cols='40', $rows='5', true, '');
  print<<<EOS
<form name='form' action='$env[self]' method='post'>
메시지:
<p>chat_id: <input type='text' name='chat_id'>
<p>$html
<p>
<input type='hidden' name='mode' value='dosend'>
<input type='submit' value='전송'>
</form>
EOS;
  MainPageTail();
  exit;

?>
