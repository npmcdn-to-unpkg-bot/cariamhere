<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  MainPageHead('Home');
  ParagraphTitle('Home');

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
