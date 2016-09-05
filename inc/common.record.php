<?php

  $env['path_include'] = $env['prefix']."/inc";
  include("$env[prefix]/config/config.php");
  include("$env[path_include]/func.php");
  //include("$env[path_include]/classes.php");

  db_connect();

  # GET/POST 방식 어느 경우이든 $form 에 저장된다.
  $form = $_REQUEST;
  @$mode = $form['mode'];

  $env['self'] = $_SERVER['SCRIPT_NAME'];

  session_start();
  //print_r($_SESSION);

  $url = urlencode($_SERVER['REQUEST_URI']);
  if ($url=='') $url = urlencode("/record/home.php");

  if (@$_SESSION['logined'] != true) {
    ErrorRedir("로그인 후 사용하세요", "/record/index.php?url=$url");
    exit;
  }

  //error_reporting(E_ALL);

?>
