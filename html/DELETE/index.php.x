<?php

  include("./path.php");
  include("$env[prefix]/inc/common.login.php");

### {{{
if ($mode == 'login') {
 //dd($form); exit;

  $f_password = $form['pass'];
  // 비밀번호는 hash 되서 넘어온다.
  //$hash = md5($f_password);
  $hash = $f_password;

  // 개발자 접속
  if ($hash == $conf['developer_pass']) {
    $_SESSION['adminlogin'] = true;
    $_SESSION['logined'] = true;
    Redirect("/home.php");
    exit;
  }

  $qry = "SELECT * FROM login"
        ." WHERE username='admin'";
  $lrow = db_fetchone($qry);
  //dd($lrow); exit;

  if ($hash != $lrow['password']) {
    iError('비밀번호가 틀렸습니다.'); exit;
  }

  // 세션 설정
  $_SESSION['adminlogin'] = true;
  $_SESSION['logined'] = true;

  $url = "/home.php";
  Redirect($url);
  exit;
}

if ($mode == 'logout') {
  //세션 삭제
  session_unset();
  session_destroy();

  Redirect("/index.php");
  exit;
}

### }}}


# 로그인 하였으면
if ($_SESSION['adminlogin']) {
  $url = "/admin/home.php";
  Redirect($url);
  exit;
}



  print<<<EOS
<html>
<head>
<title>mvod</title>
<style type='text/css'> 
body {  font-size: 12px; font-family: 굴림,돋움,verdana;
  font-style: normal; line-height: 12pt;
  text-decoration: none; color: #333333;
}
table,td,th { font-size: 12px; font-family: 돋움,verdana; white-space: nowrap; }
</style>

<script src='/js/script.md5.js' type='text/javascript'></script>
 
</head>
<body>

<center>

<form action='$env[self]' method='post' name='form' onsubmit="return false">
admin 로그인:<input type='password' name='pass' size='20' onkeypress='keypress_text()'><input
 type='button' value='확인' onclick="sf_1()" style='width:60;height:25;'>
<input type='hidden' name='mode' value='login'>
</form>
<br>

</center>

<script>
function keypress_text() {
  if (event.keyCode != 13) return;
  sf_1();
}

function sf_1() {
  var form = document.form;
  // password hash
  form.pass.value = MD5(form.pass.value);
  form.submit();
}

function _onload() {
  document.form.pass.focus();
}

if (window.addEventListener) {
  window.addEventListener("load", _onload, false);
} else if (document.attachEvent) {
  window.attachEvent("onload", _onload);
}
</script>

</body>
</html>
EOS;
  exit;



?>
