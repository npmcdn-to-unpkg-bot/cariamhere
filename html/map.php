<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  $source_title = '실시간 차량위치';

  $debug = '1';
  $debug = '0';

### {{{
### }}}

  MainPageHead($source_title);

  $count = get_keycount(1);

  print<<<EOS
<h3>
MAP KEY USAGE $count / 500
</h3>

<form name='form'>

<!-- 지도 영역 -->
<div style='width:100%; height:500px;'>
<div id="map"></div>
</div>

<div>
<label><input type='checkbox' name='drvc' onclick='_clk_drvc()'>운행중인차량만</label>
</div>


<input type='button' value='교통정보On/Off' onclick='_traffic()' class=''>
<input type='button' value='팝업On/Off' onclick='_toggle_info_window()' class=''>

<input type='button' value='자동맞춤' onclick='_map_range()' class=''>

<select id='time' name='time' class="" data-style="btn-primary" onchange="_change_time()">
<option value='0'>수동</option>
<option value='1'>자동 1초</option>
<option value='2'>자동 2초</option>
<option value='3'>자동 3초</option>
<option value='5'>자동 5초</option>
<option value='10'>자동 10초</option>
<option value='30'>자동 30초</option>
<option value='60'>자동 60초</option>
</select>

<input type='button' value='수동업데이트' onclick='_update_markers()' class=''>
<input type='button' value='정보조회' onclick='_btn_car_stat()' class=''>
</form>

<!-- 로그 출력 -->
<div id='logMessage'>
</div>
EOS;
  script_daum_map(1);

  print<<<EOS
<script>

// 전역변수

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

  var opt_only_driving = 0;

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


function _clk_drvc() {
  if (document.form.drvc.checked) {
    opt_only_driving = 1;
  } else {
    opt_only_driving = 0;
  }

  _update_markers();

}

var _int = null;

function _change_time() {
  var time = $('#time option:selected').val();

  var ms = 0;
       if (time == 1) ms = 1000;
  else if (time == 5) ms = 5000;
  else if (time == 10) ms = 10000;
  else if (time == 30) ms = 30000;
  else if (time == 60) ms = 60000;
  else ms = 0;


  if (_int) clearInterval(_int);

  if (ms > 0) _int = setInterval(function() { _update_markers(); }, ms);
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

// 차량 정보를 로그에 표시
function _show_car_information(info) {
  for (var i = 0; i < info.length; i++) {
    var item = info[i];
    var msg = ""+item['car_no']+'/'+item['car_model'];

    if (item['user_name']) msg += "("+item['user_name']+")";
    if (item['status_name']) msg += "["+item['status_name']+"]";
    if (item['dep_name1']) msg += "{"+item['dep_name1']+"}";
    if (item['des_name1']) msg += "-->{"+item['des_name1']+"}";
    _log(msg);
  }
  _log("-----차량정보 "+info.length+"대------");
}

// 버튼을 누를때
function _btn_car_stat() {
  _get_carinfo(function(info) {
    _show_car_information(info);
  });
}


// 차량정보 얻어오기 ajax 호출
function _get_carinfo(callback) {

  var data = {};
  data['mode'] = 'car_status';
  // ajax.php?mode=car_status

  if (opt_only_driving) data['driving'] = '1'; // 운행중인 차량만 조회

  $.ajax({
    method: "GET",
    url: "ajax.php",
    data: data,
  })
  .done(function( msg ) {
    console.log( "data : " + msg );
    var car_info = $.parseJSON( msg );
    callback(car_info);
  });
}

function _close_windows() {
  for (var i = 0; i < windows.length; i++) {
    var infowindow = windows[i];
    infowindow.close();
  }
}
function _clear_all() {
  _close_windows();
  windows = [];

  for (var i = 0; i < markers.length; i++) {
    var marker = markers[i];
    marker.setMap(null);
  }
  markers = [];
}

function _make_a_infowindow(content) {
  var infowindow = new daum.maps.InfoWindow({
    content : content
  });
  return infowindow;
}

function _make_a_marker(pos, infowindow, driver_team) {

  var src;
       if (driver_team == '1팀') src = "/img/marker/1.png";
  else if (driver_team == '2팀') src = "/img/marker/2.png";
  else if (driver_team == '3팀') src = "/img/marker/3.png";
  else if (driver_team == '4팀') src = "/img/marker/4.png";
  else if (driver_team == '5팀') src = "/img/marker/5.png";
  else if (driver_team == '6팀') src = "/img/marker/6.png";
  else if (driver_team == '7팀') src = "/img/marker/7.png";
  else                           src = "";

  if (src) {
    var size = new daum.maps.Size(20,30);
    var option = { offset: new daum.maps.Point(10,30)};
    var markerImage = new daum.maps.MarkerImage(src, size, option);
  } else {
    var markerImage = null;
  }

  var marker = new daum.maps.Marker({
    position: pos,
    title: "",
    image: markerImage,
  });
  marker.setMap(map);

  daum.maps.event.addListener(marker, 'mouseover', makeOverListener(map, marker, infowindow));
  daum.maps.event.addListener(marker, 'mouseout', makeOutListener(infowindow));

  return marker;
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

// 인포윈도우 내용
function _marker_info_content(item) {
  var iwContent = '<div style="padding:0px;">'
    +"차량:"+item['car_no']+"<br>"
    +"운전자:"+item['user_name']+"<br>"
    +"모델:"+item['car_model']+"<br>"
    +"상태:"+item['status_name']+"<br>";
    +'</div>';
  return iwContent;
}

// 마커를 처음으로 셋팅
function _make_markers() {
  _get_carinfo(function(info) {

    _show_car_information(info);

    for (var i = 0; i < info.length; i++) {
      var item = info[i];
      //console.log( item );
      _logd( JSON.stringify(item));

      var lat = item['lat'];
      var lng = item['lng'];
      var pos = new daum.maps.LatLng(lat, lng);
      var driver_team = item['driver_team'];

      var content = _marker_info_content(item);
      var infowindow = _make_a_infowindow(content);
      var marker = _make_a_marker(pos, infowindow, driver_team);
      markers.push(marker);
      windows.push(infowindow);

    }

  });
}

function _update_markers() {
  _log('위치 업데이트');

  _clear_all();

  _get_carinfo(function(info) {
    _logd("차량대수:"+info.length);
    markers = [];
    for (var i = 0; i < info.length; i++) {
      var item = info[i];
      var lat = item['lat'];
      var lng = item['lng'];
      var pos = new daum.maps.LatLng(lat, lng);
      var driver_team = item['driver_team'];

      //markers[i].setPosition(pos);

      var content = _marker_info_content(item);
      var infowindow = _make_a_infowindow(content);
      var marker = _make_a_marker(pos, infowindow, driver_team);
      markers.push(marker);
      windows.push(infowindow);
    }

  });

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
  setTimeout(function() { _map_range(); }, 1000);
});
</script>
EOS;


  MainPageTail();
  exit;

?>
