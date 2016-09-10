<html lang=ko xml:lang="ko" xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta content="text/html; charset=ks_c_5601-1987" http-equiv=Content-Type>

<title>매뉴얼</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0, user-scalable=no, target-densitydpi=medium-dpi" />
<link href="/e.css"  rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>

<script>
$( function() {
  $( "div#accordion" ).accordion({
    header: "h3", collapsible: true, active: false
  });

$(document).ready(function() {
    $("img").load(function() {
        var h = $(this).height();
        var w = $(this).width();
        console.log("h="+h+"w="+w);
    });
});
  
} );
</script>

</head>

<body>

<style>
img.how { width:100%; }
div#accordion div { height:500px; }
</style>

<?php

function _item($title, $src) {
  print<<<EOS
  <h3>$title</h3>
  <div>
    <p><a href='manual/$src'><img src='manual/$src' class='how'></a></p>
  </div>
EOS;
}

  print<<<EOS
<div>
이미지를 클릭하시면 확대해서 보실 수 있습니다.
</div>

<div id="accordion">
EOS;
  _item('설치파일 다운 받는 방법', '1.download_apk.png');
  _item('설치방법', '2.install.png');
  _item('사용자 등록방법', '3.registration.png');
  _item('기존 앱을 삭제하는 방법', '4.delete_app.png');
  _item('안드로이드 6.0 권한설정', 'android_6.png');
  _item('어플사용방법(1)', 'howto_1.png');
  _item('어플사용방법(2)', 'howto_2.png');
  _item('운행기록 조회하기', 'run_record.png');
  _item('텔레그램과 연동', 'telegram.png');
  print<<<EOS
</div>

</body>
</html>
EOS;

?>
