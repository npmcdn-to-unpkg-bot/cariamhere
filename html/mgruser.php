<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");


### {{{

if ($mode == 'change') {
  $pw1 = $form['pw1'];
  $pw2 = $form['pw2'];
  $user = $form['user'];
//print_r($form);
//exit;

  if ($pw1 != $pw2) {
    iError('비밀번호가 일치하지 않습니다.'); exit;
  }

  $hash = md5($pw1);

  $qry = "UPDATE login SET password='$hash' WHERE username='$user'";
  $ret = db_query($qry);

  print<<<EOS
<script>
alert('비밀번호가 변경되었습니다.');
document.location = "$env[self]";
</script>
EOS;
  exit;

} else if ($mode == 'doadduser') {

  $username = $form['username'];

  $qry = "INSERT INTO login SET username='$username'";
  $ret = db_query($qry);

  print<<<EOS
<script>
alert('사용자가 추가되었습니다.');
document.location = "$env[self]";
</script>
EOS;
  exit;

} else if ($mode == 'dodeluser') {

  $username = $form['username'];

  $qry = "DELETE FROM login WHERE username='$username'";
  $ret = db_query($qry);

  print<<<EOS
<script>
alert('사용자가 삭제되었습니다.');
document.location = "$env[self]";
</script>
EOS;
  exit;
}

### }}}

  $title = '사용자 관리';
  MainPageHead($title);
  ParagraphTitle($title);

  $qry = "SELECT * FROM login";
  $ret = db_query($qry);

  print<<<EOS
<a href='$env[self]?mode=adduser'>사용자추가</a>
::
<a href='$env[self]?mode=deluser'>사용자삭제</a>
<br>
EOS;

  print<<<EOS
<table class='table table-striped'>
<tr>
<th>아이디</td>
<th>비밀번호</td>
<th>변경</td>
</tr>
EOS;

  while ($row = db_fetch($ret)) {

    $username = $row['username'];
    $change = "<a href='$env[self]?mode=chpw&user=$username'>변경</a>";
    print<<<EOS
<tr>
<td>{$row['username']}</td>
<td>{$row['password']}</td>
<td>{$change}</td>
</tr>
EOS;
  }
  print<<<EOS
</table>
EOS;

if ($mode == 'adduser') {
  ParagraphTitle('사용자 추가', 1);
  print<<<EOS
<table class='main'>
<form name='form' method='post' action='$env[self]'>
<tr>
 <th>아이디</th>
 <td><input type='text' name='username' size='30'></td>
</tr>

<tr>
 <td colspan='2' align='center'>
<input type='hidden' name='mode' value='doadduser'>
<button onclick='submit_form()'
  style="border:#ccc 1px solid; background:#fff; width:100; height:30;">확인</button>
 </td>
</tr>

</form>
</table>

<script>
function submit_form() {
  var form = document.form;
  form.submit();
}
</script>
EOS;


} else if ($mode == 'deluser') {
  ParagraphTitle('사용자 삭제', 1);
  print<<<EOS
<table class='main'>
<form name='form' method='post' action='$env[self]'>
<tr>
 <th>아이디</th>
 <td><input type='text' name='username' size='30'></td>
</tr>

<tr>
 <td colspan='2' align='center'>
<input type='hidden' name='mode' value='dodeluser'>
<button onclick='submit_form()'
  style="border:#ccc 1px solid; background:#fff; width:100; height:30;">확인</button>
 </td>
</tr>

</form>
</table>

<script>
function submit_form() {
  var form = document.form;
  form.submit();
}
</script>
EOS;


} else if ($mode == 'chpw') {

  $username = $form['user'];

  ParagraphTitle('사용자 비빌번호 변경', 1);

  print<<<EOS
<p class=desc>사용자 로그인 비밀번호를 변경합니다.</p>

<table class='main'>
<form name='form' method='post' action='$env[self]'>
<tr>
 <th>아이디</th>
 <td>$username</td>
</tr>

<tr>
 <th>새 비밀번호</th>
 <td><input type='text' name='pw1' size='30'></td>
</tr>

<tr>
 <th>한번 더 입력</th>
 <td><input type='text' name='pw2' size='30'></td>
</tr>

<tr>
 <td colspan='2' align='center'>
<input type='hidden' name='mode' value='change'>
<input type='hidden' name='user' value='$username'>
<button onclick='submit_form()'
  style="border:#ccc 1px solid; background:#fff; width:100; height:30;">확인</button>
 </td>
</tr>

</form>
</table>

<script>
function submit_form() {
  var form = document.form;

  if (form.pw1.value == '') {
    alert("비밀번호를 입력하세요"); form.pw1.focus(); return;
  }
  if (form.pw2.value == '') {
    alert("비밀번호를 입력하세요"); form.pw2.focus(); return;
  }
  if (form.pw1.value != form.pw2.value) {
    alert("비밀번호가 서로 같지 않습니다."); form.pw1.focus(); return;
  }

  form.submit();
}
</script>
EOS;

}

  PageTail();
  exit;

?>




