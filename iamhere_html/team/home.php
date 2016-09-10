<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.record.php");
  include_once("$env[prefix]/inc/class.driver.php");


### {{{
if ($mode == 'get_log') {
  //dd($form);

  $run_id = $form['id'];

  $clsdriver = new driver();

  $qry = "SELECT * FROM run_log where run_id='$run_id'";
  $ret = db_query($qry);
  $pts = array();
  $dts = array();
  $list = array();
  $cnt = 0;
  $html2='';
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);
    $pts[] = array($row['lat'], $row['lng']);
    $dts[] = $row['idate'];
    $list[] = array($row['lat'], $row['lng'], $row['idate']);
  }

  $data = array();
  $data['points'] = $pts;
  $data['dates'] = $dts;
  $data['npoints'] = $cnt;

  $json = json_encode($data);
  print $json;
  exit;
}

if ($mode == 'map') {

  $run_id = $form['id'];

  $clsdriver = new driver();

  $sql_from = " FROM run r";
  $sql_select = $clsdriver->sql_select_run_1();
  $sql_join   = $clsdriver->sql_join_3();
  $sql_where = " WHERE r.id='$run_id'";
  $qry = $sql_select.$sql_from.$sql_join.$sql_where;
  $info = db_fetchone($qry);

  $qry = "SELECT * FROM run_log where run_id='$run_id'";
  $ret = db_query($qry);
  $pts = array();
  $dts = array();
  $list = array();
  $cnt = 0;
  $html2='';
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);
    $pts[] = array($row['lat'], $row['lng']);
    $dts[] = $row['idate'];
    $list[] = array($row['lat'], $row['lng'], $row['idate']);
  }
  //dd($pts);
  //dd($dts);
  $points = json_encode($pts);
  $dates = json_encode($dts);
  $num_points = $cnt;

  record_head('운행 기록');

  print("<link href='/map.css' rel='stylesheet'>");

  //dd($info);
  print<<<EOS
<div>
<p>운전자:{$info['driver_name']}
</div>
EOS;

  script_daum_map(2); // 
  print<<<EOS
<style>
div#map_wrap {
 position:relative;
 overflow:hidden;
 width:100%;
 height:300px;
}
</style>
<div id="map_wrap">
 <div id="map" style="width:100%;height:100%;position:relative;overflow:hidden;"></div> 
</div>

<div style='text-align:center'>
<input type='button' onclick='zoomIn()' value='확대(+)' class='btn btn-primary'
 style='width:80px;height:50px;font-size:10pt;font-weight:bold;'>
<input type='button' onclick='zoomOut()' value='축소(-)' class='btn btn-warning'
 style='width:80px;height:50px;font-size:10pt;font-weight:bold;'>
<input type='button' onclick='reloadData()' value='새로고침' class='btn btn-success' style='width:80px;height:50px;'>
</div>

<script>
// 전역변수
var map;
var markers = [];
var points = $points;
var dates = $dates;
var latlngs = [];
//console.log(points);

var clickLike;


// markers 정보를 기준으로 지도의 표시 범위를 조정
function _map_range() {
  var points = [];
  for (var i = 0; i < markers.length; i++) {
    var p = markers[i].getPosition();
    points.push(p);
  }

  var bounds = new daum.maps.LatLngBounds();
  for (var i = 0; i < points.length; i++) {
    bounds.extend(points[i]);
  }
  map.setBounds(bounds);
}


function addMarker(position, date) {

  var src = "dot2.png";
  var size = new daum.maps.Size(10,10);
  var option = { offset: new daum.maps.Point(5,5)};
  var markerImage = new daum.maps.MarkerImage(src, size, option);

  var marker = new daum.maps.Marker({
    position: position,
    title: date,
    image: markerImage,
  });
  marker.setMap(map);

  markers.push(marker);
}

function zoomIn() { map.setLevel(map.getLevel() - 1); }
function zoomOut() { map.setLevel(map.getLevel() + 1); }
function reloadData() {
  _clear_all();
  _get_data(function(info) {
    //console.log("데이터 개수:"+info.length);
    data = info['points'];
    //console.log(data);

    markers = [];
    for (var i = 0; i < data.length; i++) {
      var item = data[i];
      //console.log(item);
      var lat = item[0];
      var lng = item[1];
      var pos = new daum.maps.LatLng(lat, lng);
      //markers[i].setPosition(pos);
      addMarker(pos, '');
    }

  });
}

function _clear_all() {
  for (var i = 0; i < markers.length; i++) {
    var marker = markers[i];
    marker.setMap(null);
  }
  markers = [];
}

// 정보 얻어오기 ajax 호출
function _get_data(callback) {

  var data = {};
  data['mode'] = 'get_log';
  data['id'] = '$run_id';

  $.ajax({
    method: "GET",
    url: "$env[self]",
    data: data,
  })
  .done(function( msg ) {
    //console.log( "data : " + msg );
    var response = $.parseJSON( msg );
    callback(response);
  });
}


$(function() {

  var mapContainer = document.getElementById('map');
  var mapOption = {
    center: new daum.maps.LatLng(33.450701, 126.570667),
    level: 3 // 지도의 확대 레벨
  };
  map = new daum.maps.Map(mapContainer, mapOption);

  for (var i = 0; i < points.length; i++) {
    var p = points[i];
    var d = dates[i];
    var lat = p[0];
    var lng = p[1];
    if (lat == 0 && lng == 0) continue;
    //console.log("lat, lng = "+lat+", "+lng);
    //console.log("date = "+d);
    latlngs.push(new daum.maps.LatLng(lat, lng));
    addMarker(new daum.maps.LatLng(lat, lng), d);
  }

  //console.log(points);
  //console.log(latlngs);

  clickLine = new daum.maps.Polyline({
    map: map,
    path: latlngs,
    strokeWeight: 3,
    strokeColor: '#db4040',
    strokeOpacity: 1,
    strokeStyle: 'solid',
  });

  _map_range();

  // 지도영역 크기를 조절
  var h = $( window ).height() - 100;
  //console.log(h);
  $('div#map_wrap').height(h);
  $('div#map').height(h);
  map.relayout();
});
</script>
EOS;

  record_tail();
  exit;
}

if ($mode == 'botconnect') {
  $bot = $form['bot'];

  $row = $_SESSION['driver'];
  $appkey = $row['apikey'];

       if ($bot == '1') $url = "http://telegram.me/IamHere_330918_bot?start=$appkey";
  else if ($bot == '2') $url = "http://telegram.me/IamHere_notice_330918_bot?start=$appkey";
  Redirect($url);
  exit;
}

### }}}

  record_head('팀장 메뉴');

  $mysess = $_SESSION['driver'];
  //dd($mysess);
  $driver_id = $mysess['id'];
  $driver_name = $mysess['driver_name'];

  print<<<EOS
작업중입니다.
EOS;

/*
  $w = array('1');
  $w[] = "r.driver_id='$driver_id'";
  $sql_where = sql_where_join($w, $d=0, 'AND');
  
  $clsdriver = new driver();
  $sql_from = " FROM run r";

  $sql_join   = $clsdriver->sql_join_3();
  $sql_select = $clsdriver->sql_select_run_1();

  $qry = $sql_select.$sql_from.$sql_join.$sql_where
    ." ORDER BY r.idate DESC"
    ." LIMIT 0,10";

  print<<<EOS
<style>
div.head { text-align:center; }
</style>

<div class='head'>
<p>$driver_name (운행기록 최근10건)</p>
</div>
EOS;

  ## {{
  print("<table class='table table-striped' width='100%'>");
  print table_head_general(array('출발지<br>목적지','출발시간<br>도착시간','소요시간','지도'));

  $ret = db_query($qry);
  while ($row = db_fetch($ret)) {
    //dd($row);

    $run_id = $row['run_id'];
    $map =<<<EOS
<input type='button' onclick="_map('$run_id')" class='btn btn-primary' value='지도'>
EOS;
    list($d, $t) = preg_split("/ /", $row['start_time']);
    if ($row['end_time']) {
      $e = GetTimeStamp($row['end_time']);
      $el = $e - GetTimeStamp($row['start_time']);
      $elapsed = sprintf("%d분", $el/60);
    } else {
      $e = time();
      $el = $e - GetTimeStamp($row['start_time']);
      $elapsed = sprintf("현재%d분", $el/60);
    }

    print<<<EOS
<tr>
<td>&lt; {$row['loc1']}<br>&gt; {$row['loc2']}</td>
<td>$d<br>{$row['stime']}<br>~{$row['etime']}</td>
<td>$elapsed</td>
<td>$map</td>
</tr>
EOS;
  }

  print("</table>");
  print<<<EOS
<script>
function _map(id) { var url = "$env[self]?mode=map&id="+id; urlGo(url); }
</script>
EOS;
  ## }}
*/

  record_tail();
  exit;

?>
