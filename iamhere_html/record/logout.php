<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.login.php");

  # 세션 삭제
  session_unset();
  session_destroy();

  $url = $form['url'];
  Redirect("index.php");
  exit;

?>
