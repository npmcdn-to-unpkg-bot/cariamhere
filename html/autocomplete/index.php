<?php

  include("../path.php");
  include("$env[prefix]/inc/common.php");

  MainPageHead('Home');
  ParagraphTitle('Home');

  $base = ".";

  print<<<EOS
<p><a href='$base/1.php'>1.php 기본</a>
<p><a href='$base/2.php'>2.php 한글테스트</a>
<p><a href='$base/3.php'>3.php</a>
<p><a href='$base/4.php'>4.php</a>
<p><a href='$base/5.php'>5.php</a>
<p><a href='$base/6.php'>6.php</a>
<p><a href='$base/7.php'>7.php</a>
<p><a href='$base/8.php'>8.php</a>
<p><a href='$base/9.php'>9.php</a>
EOS;

  MainPageTail();
  exit;

?>
