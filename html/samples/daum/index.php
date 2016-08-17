<?php

  include("../../path.php");
  include("$env[prefix]/inc/common.login.php");

  $base = "/samples/daum";

  print<<<EOS
<meta name='theme-color' content='#990000'>
<p><a href='$base/1.php'>1. 기본 지도 표시하기</a>
<p><a href='$base/2.php'>2. 지도 중심 좌표 이동</a>
<p><a href='$base/3.php'>3. 마커에 인포윈도우 표시하기</a>
<p><a href='$base/4.php'>4. 마커 on/off</a>
EOS;

  MainPageTail();
  exit;

?>
