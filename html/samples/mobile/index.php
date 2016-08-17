<?php

  include("../../path.php");
  include("$env[prefix]/inc/common.login.php");

  //MainPageHead('Home');
  //ParagraphTitle('Home');

  $base1 = "/samples/mobile";
  $base2 = "https://carmaxscj.cafe24.com/samples/mobile";

  print<<<EOS
<meta name='theme-color' content='#990000'>
<p><a href='$base1/1.php'>1. GPS 지원 여부 알아보기</a>
<p><a href='$base2/2.php'>2. 현재 위치 얻기(https 필요)</a>
<p><a href='$base2/3.php'>3. 에러처리</a>
<p><a href='$base2/4.php'>4. maximumAge</a>
<p><a href='$base2/5.php'>5. 정확도 높이기</a>
<p><a href='$base2/6.php'>6. 내 위치 지도에 표시하기(5분)</a>
EOS;

  MainPageTail();
  exit;

?>
