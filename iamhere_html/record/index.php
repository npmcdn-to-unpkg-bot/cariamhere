<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.login.php");

## {{
if ($mode == 'dologin') {
  //dd($form);

  $goyu = $form['goyu'];
  $tel = $form['tel'];
  $qry = "select * from driver where driver_no='$goyu' and driver_tel='$tel'";
  $row = db_fetchone($qry);
  //dd($row);
  if (!$row) iError('로그인 실패');

  if ($form['save']) {
    setcookie("goyu", $goyu, time()+3600*24*30);
    setcookie("tel", $tel, time()+3600*24*30);
  }

  $_SESSION['logined'] = true;
  $_SESSION['driver'] = $row;

  $url = $form['url'];
  if ($url == '') $url = "/record/home.php";
  Redirect($url);
  exit;
}
## }}

  record_head('로그인');

  $goyu = $_COOKIE['goyu'];
  $tel = $_COOKIE['tel'];
  if ($goyu && $tel) $chk = ' checked'; else $chk = '';

  $url = $form['url'];
  print<<<EOS
<form name='form' action='$env[self]'>
<div style='text-align:center'>
<input type='hidden' name='url' value='$url'>
<input type='hidden' name='mode' value='dologin'>
<p>고유번호:<input type='text' name='goyu' placeholder="-없이 13자리 입력" maxlength='13' value='$goyu'>
<p>전화번호:<input type='text' name='tel' placeholder="-없이 010 포함" maxlength='11' value='$tel'>
<p><label><input type='checkbox' name='save' $chk> 정보저장</label>
<p><input type='button' value='로그인' style="width:200px; height:50px;" onclick='sf_1()'>
</div>
</form>
EOS;

  print<<<EOS
<script>
function sf_1() {
  document.form.submit();
}
</script>
EOS;

  record_tail();
  exit;

?>
