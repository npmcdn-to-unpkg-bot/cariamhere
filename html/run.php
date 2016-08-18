<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.driver.php");
  include_once("$env[prefix]/inc/class.carinfo.php");

  $source_title = '운행기록';

  $clsdriver= new driver();
  $clscar = new carinfo();


### {{{
if ($mode == 'map') {

  //dd($form);
  $pos = $form['pos'];
  $points = urldecode($pos);
  //dd($points);

  $date = $form['date'];
  $dates = urldecode($date);

  MainPageHead($source_title);
  ParagraphTitle($source_title);
  //dd($a);

  script_daum_map();
  print<<<EOS
<div id="map" style="width:600px;height:600px;"></div>

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

  MainPageTail();
  exit;
}


if ($mode == 'sess') {
  MainPageHead($source_title);
  ParagraphTitle($source_title);

  $id = $form['id'];

/*
  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='', $class='btn');
  print<<<EOS
<form name='search_form' method='get'>
$btn
<input type='hidden' name='mode' value='log'>
<input type='hidden' name='id' value='$id'>
<input type='hidden' name='page' value='{$form['page']}'>
EOS;

  $d1 = $form['date1']; if (!$d1) $d1 = get_now();
  $d2 = $form['date2']; if (!$d2) $d2 = get_now();
  print<<<EOS
기간:
<input type="text" name='date1' class="form-control datetimepicker" style='width:160px; display:inline' value='$d1'>
~
<input type="text" name='date2' class="form-control datetimepicker" style='width:160px; display:inline' value='$d2'>

<script>
$('input.datetimepicker').datetimepicker({
  format: "YYYY-MM-DD HH:mm:ss"
});
function sf_1() {
  document.search_form.submit();
}

function _page(page) {
  document.search_form.page.value = page;
  sf_1();
}
</script>
EOS;

  print<<<EOS
</form>
EOS;


  $page = $form['page'];

  $total = 100000;
  $ipp = 200;
  //$last = $total / $ipp;
  list($start, $last, $page) = calc_page($ipp, $total);

  print pagination_bootstrap2($page, $total, $ipp, '_page');
  ## }}
*/

//dd($form);

  $w = array('1');
  $w[] = "r.driver_id='$id'";

  //$d1 = $form['date1']; if ($d1) $w[] = "l.idate >= '$d1'";
  //$d2 = $form['date2']; if ($d2) $w[] = "l.idate <= '$d2'";

  $sql_where = sql_where_join($w, $d=0, 'AND');

  $id = $form['id'];
  $qry = "SELECT r.*"
." FROM run r"
.$sql_where
." ORDER BY idate DESC"
 ;

 //dd($qry);

  $ret = db_query($qry);


  $btn = button_general('지도에서보기', 0, "_map()", $style='', $class='btn btn-primary');
  print<<<EOS
<script>
function _map() {
  document.form.mode = 'map';
  document.form.submit();
}
</script>
EOS;

  print<<<EOS
<div class="panel panel-default">
<div class="panel-heading">
<!--
$btn
-->
</div>
<table class='table table-striped'>
EOS;
  print table_head_general(array('번호','시작','종료'));

  $a = array();
  $b = array();
  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];

    //$lat = $row['lat'];
    //$lng = $row['lng'];
    //$idate = $row['idate'];
    //$a[] = array($lat, $lng);
    //$b[] = $idate;

    print<<<EOS
<tr>
<td>{$cnt}</td>
<td>{$row['start_time']}</td>
<td>{$row['end_time']}</td>
</tr>
EOS;
  }
  print<<<EOS
</table>
</div>
EOS;
  //dd($a);

  // 지도 표시를 위한 폼
  $positions = urlencode(json_encode($a));
  $idate = urlencode(json_encode($b));
  print<<<EOS
<form name='form' action='$env[self]' method='post'>
<input type='hidden' name='pos' value="$positions">
<input type='hidden' name='date' value="$idate">
<input type='hidden' name='mode' value="map">
</form>
EOS;

  MainPageTail();
  exit;
}

### }}}

  exit;

?>
