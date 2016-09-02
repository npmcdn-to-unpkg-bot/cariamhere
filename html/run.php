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

function _summary() {
  global $clsdriver;
  $info = $clsdriver->driver_summary();

  print("<div class='btn-group' role='group' aria-label='...' style=''>");
  foreach ($info as $ds=>$count) {
         if ($ds == '운전중') $cls = "btn btn-success btn-lg";
    else if ($ds == '대기중') $cls = "btn btn-info btn-lg";
    else if ($ds == '비상상황') $cls = "btn btn-danger btn-lg";
    else $cls = "btn btn-default btn-lg";
    print("<button type='button' class='$cls' onclick=\"_summgo('$ds')\">$ds<span class='badge'>$count</span></button>");
  }
  print("</div>");
  print<<<EOS
<script>
function _summgo(ds) {
  var form = document.search_form;
       if (ds == '운전중') form.ds.value = 'DS_DRIVE';
  else if (ds == '대기중') form.ds.value = 'DS_STOP';
  else if (ds == '비상상황') form.ds.value = 'DS_EMERGEN';
  else form.ds.value = 'all';
  form.submit();
}
</script>
EOS;
}


### }}}

### {{{
if ($mode == 'map') {

  $run_id = $form['id'];

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

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  //dd($info);
  print<<<EOS
<div>
운전자:{$info['driver_name']}
시작:{$info['stime']}
종료:{$info['etime']}
데이터:$num_points
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
  dd($points);

  MainPageTail();
  exit;
}


  MainPageHead($source_title);
  ParagraphTitle($source_title);

  _summary();

  $driver_id = $form['driver_id']; //  driver_id

  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='', $class='btn btn-primary');
  print<<<EOS
<form name='search_form' method='get'>
$btn
<input type='hidden' name='mode' value='$mode'>
<input type='hidden' name='page' value='{$form['page']}'>
EOS;

  print<<<EOS
운전자ID:<input type='text' name='driver_id' value='$driver_id' size='2'>
EOS;

  $v = $form['driver_name'];
  $ti = textinput_general('driver_name', $v, $size='10', 'keypress_text()', $click_select=true, $maxlength=0, $id='');
  print("운전자이름:$ti");

  $v = $form['person_name'];
  $ti = textinput_general('person_name', $v, $size='10', 'keypress_text()', $click_select=true, $maxlength=0, $id='');
  print("VIP이름:$ti");

  $list = array('=선택=:all','기록중:r','종료:d');
  $preset = $form['rs']; if (!$preset) $preset = 'all';
  $opt = option_general($list, $preset);
  print("기록상태:<select name='rs'>$opt</select>");

  $ds = $form['ds'];
  $opt = $clsdriver->driver_status_option($ds);
  print("운전자상태:<select name='ds'>$opt</select>");

  print("<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");

  $d1 = $form['date1']; if (!$d1) $d1 = get_now();
  $d2 = $form['date2']; if (!$d2) $d2 = get_now();
  print<<<EOS
기간:
<input type="text" name='date1' class="form-control datetimepicker" style='width:120px; display:inline' value='$d1'>
~
<input type="text" name='date2' class="form-control datetimepicker" style='width:120px; display:inline' value='$d2'>

<script>
$('input.datetimepicker').datetimepicker({
  format: "YYYY-MM-DD"
});

function sf_1() {
  document.search_form.submit();
}

function _page(page) { document.search_form.page.value = page; sf_1(); }
function keypress_text() { if (event.keyCode != 13) return; sf_1(); }

</script>
EOS;

  print<<<EOS
</form>
EOS;

  $page = $form['page'];

  $total = 100000;
  $ipp = 30;
  //$last = $total / $ipp;
  list($start, $last, $page) = calc_page($ipp, $total);

  print pagination_bootstrap2($page, $total, $ipp, '_page');
  ## }}

  //dd($form);

  $w = array('1');

  $v = $form['driver_id'];
  if ($v) $w[] = "r.driver_id='$v'";

  $v = $form['driver_name'];
  if ($v) $w[] = "(d.driver_name LIKE '%$v%' OR d.driver_cho LIKE '%$v%')";

  $v = $form['person_name'];
  if ($v) $w[] = "(p.person_name LIKE '%$v%' OR p.person_cho LIKE '%$v%')";

  $v = $form['rs'];
  if ($v && $v != 'all') {
         if ($v == 'r') $w[] = "(r.is_driving=1)";
    else if ($v == 'd') $w[] = "(r.is_driving=0)";
  }

  $ds = $form['ds'];
  if ($ds != '' && $ds != 'all') $w[] = "d.driver_stat='$ds'";

  $d1 = $form['date1']; if ($d1) $w[] = "DATE(r.idate) >= '$d1'";
  $d2 = $form['date2']; if ($d2) $w[] = "DATE(r.idate) <= '$d2'";

  $sql_where = sql_where_join($w, $d=0, 'AND');

  $sql_from = " FROM run r";

  $sql_join   = $clsdriver->sql_join_3();
  $sql_select = $clsdriver->sql_select_run_1();

  $qry = $sql_select.$sql_from.$sql_join.$sql_where
    ." ORDER BY r.idate DESC"
    ." LIMIT $start,$ipp";

  //dd($qry);

  $ret = db_query($qry);

  print("<div class='panel panel-default'>");
  print("<table class='table table-striped'>");

  print table_head_general(array('ID','이름','상태','차량','운행기록' ,'출발시간','도착시간',
'소요시간',
'출발지','목적지','VIP'));
  print("<tbody>");

  //$a = array();
  //$b = array();
  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;

    //dd($row);

    $run_id = $row['run_id'];

    $driver_id = $row['driver_id'];
    $name = _edit_link($row['driver_name'], $driver_id);

    $lcnt = $clsdriver->run_log_count($run_id);
    $btn = button_general("($lcnt)건", 0, "_map('$run_id')", $style='', $class='btn btn-primary');

    $rdg = $row['run_driving'];
    $ft = $row['flagTerm'];
    if ($rdg) {
      $ds = "<span class='drs ds_driving'>기록중</span>";
    } else {
      if ($ft) $str = '강제종료'; else $str = '종료';
      $ds = "<span class='drs ds_not_driving'>$str</span>";
    }

    if ($row['start_time'] && $row['end_time']) {
      $et = mktime_date_string($row['end_time']);
      $st = mktime_date_string($row['start_time']);
      $elap = getHumanTime($et-$st);
    } else $elap = '';

    print<<<EOS
<tr>
<td nowrap>{$run_id}</td>
<td nowrap>{$name}</td>
<td nowrap>{$ds}</td>
<td nowrap>{$row['car_no']}</td>
<td nowrap>{$btn}</td>
<td nowrap>{$row['start_time']}</td>
<td nowrap>{$row['end_time']}</td>
<td nowrap>{$elap}</td>
<td nowrap>{$row['loc1']}</td>
<td nowrap>{$row['loc2']}</td>
<td nowrap>{$row['person_name']}</td>
</tr>
EOS;
  }
  print("</tbody>");
  print("</table>");
  print("</div>");
  //dd($a);

  print<<<EOS
<script>
function _map(id) { var url = "$env[self]?mode=map&id="+id; urlGo(url); }
function _edit(id) { var url = "driver.php?mode=edit&id="+id; wopen(url,600,600,1,1); }
</script>
EOS;


  MainPageTail();
  exit;

### }}}

  exit;

?>
