<?php

  $env['path_include'] = $env['prefix']."/inc";
  include("$env[prefix]/config/config.php");
  include("$env[path_include]/func.php");

  db_connect();

  $form = $_REQUEST;
  @$mode = $form['mode'];

  $env['self'] = $_SERVER['SCRIPT_NAME'];

  session_start();

?>
