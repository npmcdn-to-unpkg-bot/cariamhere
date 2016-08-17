<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  MainPageHead('Home');
  ParagraphTitle('Home');

 print("<li><a href='/carinfo.php'>차량(car)</a></li>");
 print("<li><a href='/driverinfo.php'>운전자(driver)</a></li>");
 print("<li><a href='/personinfo.php'>의전대상자(person)</a></li>");
 print("<li><a href='/locationinfo.php'>장소(location)</a></li>");
 print("<li><a href='/map.php'>실시간 차량위치</a></li>");
 print("<li><a href='/index.php?mode=logout'>로그아웃</a></li>");
 print("<li><a href='#'>-----------------</a></li>");

 print("<li><a href='/push_ui.php'>위치입력(개발자용)</a></li>");
 print("<li><a href='/dbdoc.php'>DB 테이블</a></li>");
 print("<li><a href='/apidoc.php'>API 설명서</a></li>");
 print("<li><a href='/samples/'>참고(samples)</a></li>");

 print("<li><a href='http://apis.map.daum.net/web/' target='_blank'>Daum Map API</a></li>");
 print("<li><a href='http://developers.google.com/maps/documentation/javascript/examples/?hl=ko' target='_blank'>Google Map API</a></li>");
 print("<li><a href='http://getbootstrap.com/components/' target='_blank'>Bootstrap Docs</a></li>");
 print("<li><a href='http://www.airportal.go.kr/life/airinfo/RbHanFrmMain.jsp' target='_blank'>항공기 출/도착현황</a></li>");
 
 //print("<li><a href='/mgruser.php'>사용자관리</a></li>");
 //print("<li><a href='/mobile/'>mobile</a></li>");
  print<<<EOS
<input type='button' onclick="notifyMe('알람 테스트','테스트입니다.','http://m.daum.net')" value='알람'>
EOS;

  MainPageTail();
  exit;

?>
