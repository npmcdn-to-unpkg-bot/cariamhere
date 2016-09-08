<?php

  $env['path_include'] = $env['prefix']."/inc";
  include("$env[prefix]/config/config.php");
  include("$env[path_include]/func.php");

  error_reporting(0);

  db_connect();

  $form = $_REQUEST;
  @$mode = $form['mode'];

?>
