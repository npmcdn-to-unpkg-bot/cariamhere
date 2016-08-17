<?php

  include("../../path.php");
  include("$env[prefix]/inc/common.login.php");

  //MainPageHead('Home');
  //ParagraphTitle('Home');

  $base = "/samples/google";

  print<<<EOS
<meta name='theme-color' content='#990000'>
<p><a href='$base/1.php'>1. 기본 지도 표시하기</a>
<p><a href='$base/2.php'>2. 인포 윈도우 활용하기</a>
<p><a href='$base/3.php'>3. 마커의 위치를 변경하기</a>
<p><a href='$base/4.php'>4. 마커 위치 자동 변경(타이머)</a>
<p><a href='$base/5.php'>5. 클릭한 곳에 마커를 표시하기</a>
EOS;

  MainPageTail();
  exit;

?>
