<?php

  $key = "b2854aeb35c0ddf8dbf953453e4fad23";

  print<<<EOS
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>지도 이동시키기</title>

<script type="text/javascript" src="//apis.daum.net/maps/maps3.js?apikey=$key"></script>
    
</head>


<body>
<div id="map" style="width:100%;height:350px;"></div>
<p>
    <button onclick="setCenter()">지도 중심좌표 이동시키기</button> 
    <button onclick="panTo()">지도 중심좌표 부드럽게 이동시키기</button> 
</p>

<script>
var mapContainer = document.getElementById('map'), // 지도를 표시할 div 
    mapOption = { 
        center: new daum.maps.LatLng(33.450701, 126.570667), // 지도의 중심좌표
        level: 3 // 지도의 확대 레벨
    };

var map = new daum.maps.Map(mapContainer, mapOption); // 지도를 생성합니다

function setCenter() {            
    // 이동할 위도 경도 위치를 생성합니다 
    var moveLatLon = new daum.maps.LatLng(33.452613, 126.570888);
    
    // 지도 중심을 이동 시킵니다
    map.setCenter(moveLatLon);
}

function panTo() {
    // 이동할 위도 경도 위치를 생성합니다 
    var moveLatLon = new daum.maps.LatLng(33.450580, 126.574942);
    
    // 지도 중심을 부드럽게 이동시킵니다
    // 만약 이동할 거리가 지도 화면보다 크면 부드러운 효과 없이 이동합니다
    map.panTo(moveLatLon);            
}        
</script>
</body>
</html>
EOS;

?>
