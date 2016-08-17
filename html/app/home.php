<?php

  include("../path.php");
  include("$env[prefix]/inc/common.php");


  MainPageHead('Home');
  ParagraphTitle('Home');

  dd($_SESSION);
  print<<<EOS
EOS;

  MainPageTail();
  exit;

?>
