<?php

  include("$env[prefix]/config/config.php");
  include("$env[prefix]/inc/func.php");

  db_connect();

  $form = $_REQUEST;
  @$mode = $form['mode'];

  //$env['self'] = $_SERVER['SCRIPT_NAME'];
  //session_start();
  //print_r($_SESSION);

?>
