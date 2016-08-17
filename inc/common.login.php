<?php

  $env['path_include'] = $env['prefix']."/inc";
  include("$env[prefix]/config/config.php");
  include("$env[path_include]/func.php");

  db_connect();

  # GET/POST 방식 어느 경우이든 $form 에 저장된다.
  $form = $_REQUEST;
  @$mode = $form['mode'];

  $env['self'] = $_SERVER['SCRIPT_NAME'];

  session_start();
  //print_r($_SESSION);

?>
