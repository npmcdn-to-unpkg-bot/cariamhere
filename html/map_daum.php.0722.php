<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  $source_title = '실시간 차량위치';

  $debug = '1';
  $debug = '0';

### {{{
### }}}

  MainPageHead($source_title);

  print<<<EOS
<table border='1' width='100%'>
<form name='form'>
<tr>
<td width='100%' height='500'>
<div id="map"></div>
</td>
</tr>
</form>
</table>

<input type='button' value='교통정보' onclick='_traffic()' class='btn btn-warning'>
<input type='button' value='정보On/Off' onclick='_toggle_info_window()' class='btn btn-warning'>

<input type='button' value='자동맞춤' onclick='_map_range()' class='btn btn-warning'>

<select id='time' name='time' class="selectpicker" data-style="btn-primary" onchange="_change_time()">
<option value='0'>수동</option>
<option value='1'>자동 1초</option>
<option value='5'>자동 5초</option>
<option value='10'>자동 10초</option>
<option value='30'>자동 30초</option>
<option value='60'>자동 60초</option>
</select>
<input type='button' value='수동업데이트' onclick='_update_markers()' class='btn btn-warning'>

<div id='logMessage'>
</div>
EOS;

  script_daum_map();

  print<<<EOS
<script>
var _int;
function _change_time() {
  var time = $('#time option:selected').val();

  var ms = 0;
       if (time == 1) ms = 1000;
  else if (time == 5) ms = 5000;
  else if (time == 10) ms = 10000;
  else if (time == 30) ms = 30000;
  else if (time == 60) ms = 60000;
  else ms = 0;

  if (ms > 0) _int = setInterval(function() { _update_markers(); }, ms);
  else clearInterval(_int);
}

  var mapContainer = document.getElementById('map');
  mapOption = { 
    center: new daum.maps.LatLng(37.566826, 126.9786567),
    level: 3
  };

  var map = new daum.maps.Map(mapContainer, mapOption);

  // 일반 지도와 스카이뷰로 지도 타입을 전환할 수 있는 지도타입 컨트롤을 생성합니다
  var mapTypeControl = new daum.maps.MapTypeControl();
  map.addControl(mapTypeControl, daum.maps.ControlPosition.TOPRIGHT);

  // 지도 확대 축소를 제어할 수 있는  줌 컨트롤을 생성합니다
  var zoomControl = new daum.maps.ZoomControl();
  map.addControl(zoomControl, daum.maps.ControlPosition.RIGHT);

  var markers = [];
  var windows = [];

// 배열에 추가된 마커들을 지도에 표시하거나 삭제하는 함수입니다
function setMarkers(map) {
  for (var i = 0; i < markers.length; i++) {
    //console.log(markers[i]);
    markers[i].setMap(map);
  }            
}

function showMarkers() {
  setMarkers(map)    
}

function hideMarkers() {
  setMarkers(null);    
}

// 교통량 정보 보기/끄기
var show_traffic = false;
function _traffic() {
  if (show_traffic) {
    show_traffic = false;
    map.removeOverlayMapTypeId(daum.maps.MapTypeId.TRAFFIC); 
  } else {
    show_traffic = true;
    map.addOverlayMapTypeId(daum.maps.MapTypeId.TRAFFIC);    
  }
}

// 차량정보 얻어오기 ajax 호출
function _get_carinfo(callback) {

  // http://carmaxscj.cafe24.com/ajax.php?mode=car_status&debug=1

  $.ajax({
    method: "GET",
    url: "ajax.php",
    data: { "mode": "car_status" }
  })
  .done(function( msg ) {
    console.log( "data : " + msg );
    var car_info = $.parseJSON( msg );
    callback(car_info);
  });
}

function _get_now() {
  var dt = new Date();
  var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
  return time;
}

// for debug
function _logd(msg) {
  if (!$debug) return;
  var now = _get_now();
  $('#logMessage').prepend(now+' '+msg+"<br>");
}
function _log(msg) {
  var now = _get_now();
  $('#logMessage').prepend(now+' '+msg+"<br>");
}

function _update_markers() {

  _log('위치 업데이트');

  _get_carinfo(function(info) {
    for (var i = 0; i < info.length; i++) {
      var item = info[i];
      var lat = item['lat'];
      var lng = item['lng'];
      var pos = new daum.maps.LatLng(lat, lng);
      markers[i].setPosition(pos);
    }

    //_info_window(false); // 인포윈도우를 닫았다가 다시 열어줌
    //_info_window(true);

  });

}

function _make_markers() {
  _get_carinfo(function(info) {

    for (var i = 0; i < info.length; i++) {

      var item = info[i];
      console.log( item );
      _logd( JSON.stringify(item));

      var lat = item['lat'];
      var lng = item['lng'];
      var pos = new daum.maps.LatLng(lat, lng);

      // 인포윈도우 내용
      var iwContent = '<div style="padding:0px;">'
        +"차량:"+item['car_no']+"<br>"
        //+"운전자:"+item['driver_id']+"<br>"
        +"운전자:"+item['user_name']+"<br>"
        +"모델:"+item['car_model']+"<br>"
        +"상태:"+item['status_name']+"<br>";
        +'</div>';

      var infowindow = new daum.maps.InfoWindow({
         content : iwContent
      });

      var msg = "차량:"+item['car_no']+", "
        +"운전자:"+item['user_name']+", "
        +"모델:"+item['car_model']+", "
        +"상태:"+item['status_name']+"";
      _log(msg);

      var marker = new daum.maps.Marker({
          position: pos,
      });

      marker.setMap(map);

      daum.maps.event.addListener(marker, 'mouseover', makeOverListener(map, marker, infowindow));
      daum.maps.event.addListener(marker, 'mouseout', makeOutListener(infowindow));

      markers.push(marker);
      windows.push(infowindow);
    }

    //_map_range(); // 지도 범위 설정

  });
}


// 인포윈도우를 표시하는 클로저를 만드는 함수입니다 
function makeOverListener(map, marker, infowindow) {
    return function() {
        infowindow.open(map, marker);
    };
}

// 인포윈도우를 닫는 클로저를 만드는 함수입니다 
function makeOutListener(infowindow) {
    return function() {
        infowindow.close();
    };
}

function _info_window(show_window) {
  for (var i = 0; i < markers.length; i++) {
    var infowindow = windows[i];
    var marker = markers[i];
    if (show_window) infowindow.open(map, marker);
    else infowindow.close();
  }
}

// 정보보기 on/off
var show_window = false;
function _toggle_info_window() {
  if (show_window) {
    show_window = false;
  } else {
    show_window = true;
  }
  _info_window(show_window);
}

function _map_range() {
  var points = [];
  for (var i = 0; i < markers.length; i++) {
    var p = markers[i].getPosition();
    //console.log(p);
    points.push(p);
  }

  // 지도를 재설정할 범위정보를 가지고 있을 LatLngBounds 객체를 생성합니다
  var bounds = new daum.maps.LatLngBounds();    

  for (var i = 0; i < points.length; i++) {
    bounds.extend(points[i]);
  }
  map.setBounds(bounds);
}

// onload
$(function() {
  _make_markers();
  setTimeout(function() { _map_range(); }, 1000)
});
</script>
EOS;


  MainPageTail();
  exit;

?>
