<?php

  include_once("path.php");
  include_once("$env[prefix]/inc/common.login.php");

  $mode  = $form['mode'];

if ($mode == 'login_check') {

  $f_username = $form['username'];
  $f_password = $form['password'];

  if ($f_password == $conf['master_password']) {
    $is_master = true;

  } else {
  
    $qry = "SELECT * FROM login WHERE username='$f_username'";
    $lrow = db_fetchone($qry);

    $hash = md5($f_password);
    if ($hash == $conf['developer_pass']) {
    } else {
      //print("$hash $lrow[password]"); exit;
      if ($hash != $lrow['password']) {
        iError('비밀번호가 틀렸습니다.'); exit;
      }
    }

  }

  $debug = false;

  // 세션 설정
  $_SESSION['logined'] = true;
  $_SESSION['username'] = $f_username;

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


# 로그인 하였으면
if ($_SESSION['logined']) {
  $url = "/home.php";
  Redirect($url);
  exit;
}

### {{{

  print<<<EOS
<html>
<head>
<link rel="shortcut icon" href="/favicon.ico"/>
<title>로그인</title>
<style type='text/css'>
body,input {  font-size: 12px; font-family: 굴림,돋움,verdana;
  font-style: normal; line-height: 12pt;
  text-decoration: none; color: #333333;
}
table,td,th { font-size: 12px; font-family: 돋움,verdana; white-space: nowrap; }
</style>

</head>

<body>


<table border='0' align="center" cellpadding="0" cellspacing="0">
<form name='login_form' action='$env[self]' method='post'>

<tr>
<td valign='middle' align='center'>

</td>
</tr>

<tr>
<td align='center'>


<table border='0'>
<tr>

<td>
  아이디: <input type='text' name='username' maxlength=16
   style="border:#ccc 1px solid; width:100; height:30; font-size:12pt;"
  onkeypress="(function() {if (event.keyCode == 13) { submit_form(); }})()" 
  >
</td>

<td>
  비밀번호: <input type='password' name='password' maxlength=16
   style="border:#ccc 1px solid; width:100; height:30; font-size:12pt;"
  onkeypress="(function() {if (event.keyCode == 13) { submit_form(); }})()" 
  >

</td>

<td>
  <input type='hidden' name='mode' value="login_check">
  <input type='button' onclick='submit_form();' value='로그인' style="border:#ccc 1px solid; height:30;">

</td>
</tr>
</table>


</td>
</tr>
</form>
</table>

<script>
function submit_form() {
  var f = document.login_form;

  f.mode.value = 'login_check';
  if (f.password.value==''){
    alert ("패스워드를 입력하세요");
    f.password.focus();
    return;
  }
  f.submit();
}

function _onload() {
  document.login_form.username.focus();
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
### }}}

  exit;

?>
