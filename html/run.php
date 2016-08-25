<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.driver.php");
  include_once("$env[prefix]/inc/class.carinfo.php");

  $source_title = '운행기록';

  $clsdriver= new driver();
  $clscar = new carinfo();

### {{{

function _edit_link($title, $id) {
  if (!$title) $title = '--';
  $html = <<<EOS
<span class=link onclick="_edit('$id')">{$title}</span>
EOS;
  return $html;
}

### }}}

### {{{
if ($mode == 'map') {

  $run_id = $form['id'];

  $qry = "SELECT * FROM run WHERE id='$run_id'";
  $run_row = db_fetchone($qry);

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


  MainPageHead($source_title);
  ParagraphTitle($source_title);
  //dd($a);
dd($run_row);

  print<<<EOS
<div>
{$row['start_time']}
{$row['end_time']}
$num_points
</div>
EOS;

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

  $driver_id = $form['id']; //  driver_id

  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='', $class='btn');
  print<<<EOS
<form name='search_form' method='get'>
$btn
<input type='hidden' name='mode' value='$mode'>
<input type='hidden' name='id' value='$driver_id'>
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
  format: "YYYY-MM-DD"
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

  //dd($form);

  $driver_id = $form['id'];

  $w = array('1');
  $w[] = "r.driver_id='$driver_id'";

  //$d1 = $form['date1']; if ($d1) $w[] = "l.idate >= '$d1'";
  //$d2 = $form['date2']; if ($d2) $w[] = "l.idate <= '$d2'";

  $sql_where = sql_where_join($w, $d=0, 'AND');

  $sql_from = " FROM run r";

  $pj = " LEFT JOIN driver d ON r.driver_id=d.id";
  $sql_join   = $clsdriver->sql_join_run_1($pj);

  $sql_select = $clsdriver->sql_select_run_1();

  $qry = $sql_select.$sql_from.$sql_join.$sql_where
    ." ORDER BY r.idate DESC";

 dd($qry);

  $ret = db_query($qry);

  print<<<EOS
<div class="panel panel-default">
<div class="panel-heading">
운전자:
</div>
<table class='table table-striped'>
EOS;
  print table_head_general(array('ID','이름','상태','차량','운행기록' ,'출발시간','도착시간','출발지','목적지','VIP'));
  print("<tbody>");

  $a = array();
  $b = array();
  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;

    //dd($row);

    $id = $row['run_id'];

    $name = _edit_link($row['driver_name'], $id);
    $btn = button_general('지도', 0, "_map('$id')", $style='', $class='btn btn-primary');

    print<<<EOS
<tr>
<td>{$id}</td>
<td>{$name}</td>
<td>{$row['DsName']}</td>
<td>{$row['car_no']}</td>
<td>{$btn}</td>
<td>{$row['stime']}</td>
<td>{$row['etime']}</td>
<td>{$row['loc1']}</td>
<td>{$row['loc2']}</td>
<td>{$row['person_name']}</td>
</tr>
EOS;
  }
  print("</tbody>");
  print("</table>");
  print("</div>");
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

  print<<<EOS
<script>
function _map(id) {
  var url = "$env[self]?mode=map&id="+id;
  urlGo(url);
}
</script>
EOS;


  MainPageTail();
  exit;
}

### }}}

  exit;

?>
