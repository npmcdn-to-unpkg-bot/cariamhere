<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  MainPageHead('Home');
  ParagraphTitle('Home');

 print("<li><a href='/push_ui.php'>위치입력(개발자용)</a></li>");
 print("<li><a href='/dbdoc.php'>DB 테이블</a></li>");
 print("<li><a href='/apidoc.php'>API 설명서</a></li>");
 print("<li><a href='/samples/'>참고(samples)</a></li>");
 print("<li><a href='/telegram/'>텔레그램</a></li>");
 print("<li><a href='/apilog.php'>apilog</a></li>");

 print("<li><a href='http://apis.map.daum.net/web/' target='_blank'>Daum Map API</a></li>");
 print("<li><a href='http://developers.google.com/maps/documentation/javascript/examples/?hl=ko' target='_blank'>Google Map API</a></li>");
 print("<li><a href='http://getbootstrap.com/components/' target='_blank'>Bootstrap Docs</a></li>");
 print("<li><a href='http://www.airportal.go.kr/life/airinfo/RbHanFrmMain.jsp' target='_blank'>항공기 출/도착현황</a></li>");
 
  $now = get_now();
  print<<<EOS
<input type='button' onclick="notifyMe('알람 테스트','테스트입니다.','http://m.daum.net')" value='알람 테스트'>
현재시간: $now
EOS;

  // 알람 메시지를 얻어오기
  $info = get_alert_messages($limit=3);
  //dd($info);

  $script = "";
  $count = 0;
  foreach ($info as $item) {
    $count++;
    $msg = $item['message'];
    $idate = $item['idate'];
    // notifyMe('알람 테스트','테스트입니다.','http://m.daum.net');
    $script .=<<<EOS
notifyMe('알람 테스트','[$idate] $msg','');
EOS;
  }
  print("새로운 알람: $count 개 ");

  print<<<EOS
<script>
$(function() {
  $script
});

setTimeout("location.reload();",10000);
</script>
EOS;

  MainPageTail();
  exit;

?>
