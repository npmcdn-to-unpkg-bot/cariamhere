<?php

  include_once("../path.php");
  include_once("$env[prefix]/inc/common.login.php");
  include_once("$env[prefix]/inc/class.driver.php");

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
  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);
    $pts[] = array($row['lat'], $row['lng']);
    $dts[] = $row['idate'];
  }
  //dd($pts);
  //dd($dts);
  $points = json_encode($pts);
  $dates = json_encode($dts);
  $num_points = $cnt;

  PopupPageHead($source_title);
  ParagraphTitle($source_title);

  //dd($info);
  print<<<EOS
<div>
시작:{$info['start_time']}<br>
종료:{$info['end_time']}<br>
데이터:$num_points 개
</div>
EOS;

  script_daum_map();
  print<<<EOS
<div id="map" style="width:300px; height:300px;"></div>

<script>
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

  var marker = new daum.maps.Marker({
      position: position,
      title: date,
  });
  marker.setMap(map);

  markers.push(marker);
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
});

</script>
EOS;
  //dd($points);

  exit;

?>
