<?php

  include("../path.php");
  include("$env[prefix]/inc/common.php");

if ($mode == 'login') {
  //dd($form);

  $tel = $form['tel'];
  $qry = "select * from user where user_tel='$tel'";
  $row = db_fetchone($qry);
  //dd($row);

  $_SESSION['apikey'] = $row['apikey'];

  Redirect("home.php");
  exit;
}

  MainPageHead('Home');
  ParagraphTitle('Home');

  print<<<EOS
<form>
<input type='text' name='tel'>
<input type='hidden' name='mode' value='login'>
<input type='submit' value='login'>
</form>
EOS;

  MainPageTail();
  exit;

?>
