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
<span class=link onclick="_edit('$id',this)">{$title}</span>
EOS;
  return $html;
}

function _summary() {
  global $clsdriver;
  $info = $clsdriver->driver_summary();

  //$large = ' btn-lg';

  print("<div class='btn-group' role='group' aria-label='...' style=''>");
  foreach ($info as $ds=>$count) {
         if ($ds == '운전중') $cls = "btn btn-success $large";
    else if ($ds == '대기중') $cls = "btn btn-info $large";
    else if ($ds == '비상상황') $cls = "btn btn-danger $large";
    else $cls = "btn btn-default $large";
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
if ($mode == 'delete') {
  $run_id = $form['id'];
  $qry = "delete FROM run_log where run_id='$run_id'";
  $ret = db_query($qry);

  $qry = "delete FROM run where id='$run_id'";
  $ret = db_query($qry);

  CloseAndReloadOpenerWindow();
  exit;
}

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
  $pdts = array();

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);
    $pts[] = array($row['lat'], $row['lng']);
    $dts[] = $row['idate'];
    $pdts[] = array($row['lat'], $row['lng'], $row['idate'], $cnt);
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

  var src = "/img/marker/dot1.png";
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

  print<<<EOS
<style>
p.detail { margin-top: 0; margin-bottom:0; }
</style>
EOS;
  $pdts = array_reverse($pdts);
  foreach ($pdts as $item) {
    list($lat, $lng, $date, $count) = $item;
    print("<p class='detail'>[$count] $date ($lat, $lng)</p>");
  }

  PopupPageTail();
  exit;
}


  MainPageHead($source_title);
  ParagraphTitle($source_title);

  //_summary();

  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='width:70px; height:50px;', $class='btn btn-primary');
  print<<<EOS
<form name='search_form' method='get'>
$btn
<input type='hidden' name='mode' value='$mode'>
<input type='hidden' name='page' value='{$form['page']}'>
EOS;

  $driver_id = $form['driver_id']; //  driver_id
  $ti = textinput_general('driver_id', $driver_id, 6, 'keypress_text()', true, 0, '','ui-corner-all','운전자번호');
  print $ti;

  $v = $form['driver_name'];
  $ti = textinput_general('driver_name', $v, 10, 'keypress_text()', true, 0, '','ui-corner-all','운전자이름');
  print $ti;

  $v = $form['person_name'];
  $ti = textinput_general('person_name', $v, 10, 'keypress_text()', true, 0, '','ui-corner-all','VIP인사');
  print $ti;

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
</script>
EOS;

  $sel = array(); $sort = $form['sort'];
  if ($sort == '') $sel[1] = ' selected'; else $sel[$sort] = ' selected';
  print<<<EOS
정렬:<select name='sort'>
<option value='1'$sel[1]>최근변경</option>
<option value='2'$sel[2]>이름</option>
</select>
EOS;

  print("<input type='button' onclick='_vopt()' value='표시정보' class='btn'>");

  $fck = array(); // field check '' or ' checked'
  fck_init($fck, $defaults='1,2,3,5');
  print<<<EOS
<div id="vopt" style='display:none;'>
<label><input type='checkbox' name='fd01' $fck[1]>차량</label>
<label><input type='checkbox' name='fd02' $fck[2]>출발지</label>
<label><input type='checkbox' name='fd03' $fck[3]>목적지</label>
<label><input type='checkbox' name='fd04' $fck[4]>출발,도착시간</label>
<label><input type='checkbox' name='fd05' $fck[5]>최종업데이트</label>
<label><input type='checkbox' name='fd06' $fck[6]>운전자상태</label>
<label><input type='checkbox' name='fd08' $fck[8]>5분간주행거리</label>
<label><input type='checkbox' name='fd07' $fck[7]>삭제</label>
</div>
<script>
function _vopt() { $('#vopt').toggle(); }
</script>
EOS;

  print<<<EOS
<script>
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
  $sql_select = $clsdriver->sql_select_run_1()
     .", d.emergency"
     .", r.udate run_udate, r.dist5";

  $sort = $form['sort']; if ($sort == '') $sort = '1';
       if ($sort == '1') $o = "r.udate DESC";
  else if ($sort == '2') $o = "d.driver_name";
  else                   $o = "r.udate DESC";
  $sql_order = " ORDER BY $o";
  //dd($sql_order);

  $qry = "select count(*) count".$sql_from.$sql_join.$sql_where;
  $row = db_fetchone($qry);
  $total = $row['count'];
  $page = $form['page'];
  $ipp = 30;
  list($start, $last, $page) = calc_page($ipp, $total);
  print pagination_bootstrap2($page, $total, $ipp, '_page');

  $qry = $sql_select.$sql_from.$sql_join.$sql_where.$sql_order
    ." LIMIT $start,$ipp";

  //dd($qry);

  $ret = db_query($qry);

  print("<div class='panel panel-default'>");
  print("<table class='table table-striped'>");

  $head = array();
  $head[] = 'ID';
  $head[] = '이름';
  $head[] = '상태'; if ($form['fd01'])
  $head[] = '차량';
  $head[] = '운행기록';
  if ($form['fd04']) { $head[] = '출발시간'; $head[] = '도착시간'; }
  $head[] = '소요시간';
  if ($form['fd02']) $head[] = '출발지';
  if ($form['fd03']) $head[] = '목적지';
  $head[] = 'VIP인사';
  if ($form['fd05']) $head[] = '최종업데이트';
  if ($form['fd06']) $head[] = '운전자상태';
  if ($form['fd08']) $head[] = '5분간주행거리';
  if ($form['fd07']) $head[] = '기록삭제';
  print table_head_general($head);
  print("<tbody>");

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;

    //dd($row);

    $fields = array();

    $run_id = $row['run_id'];
    $fields[] = $run_id;

    $driver_id = $row['driver_id'];
    $name = _edit_link($row['driver_name'], $driver_id);
    $fields[] = $name;

    $rdg = $row['run_driving'];
    $ft = $row['flagTerm'];
    if ($rdg) {
      $ds = "<span class='drs ds_driving'>기록중</span>";
    } else {
      if ($ft) $str = '강제종료'; else $str = '종료';
      $ds = "<span class='drs ds_not_driving'>$str</span>";
    }
    $fields[] = $ds;

    if ($form['fd01']) $fields[] = $row['car_no'];

    $lcnt = $clsdriver->run_log_count($run_id);
    $btn = button_general("($lcnt)건", 0, "_map('$run_id')", $style='', $class='btn btn-primary');
    $fields[] = $btn;

    if ($form['fd04']) {
      $fields[] = $row['start_time'];
      $fields[] = $row['end_time'];
    }

    if ($row['start_time'] && $row['end_time']) {
      $et = mktime_date_string($row['end_time']);
      $st = mktime_date_string($row['start_time']);
      $elap = getHumanTime($et-$st);
    } else $elap = '';
    $fields[] = $elap;

    if ($form['fd02']) $fields[] = $row['loc1'];
    if ($form['fd03']) $fields[] = $row['loc2'];
    $fields[] = $row['person_name'];

    if ($form['fd05']) {
      $htb = human_time_before($row['run_udate']);
      if ($htb) $str = "{$htb}전"; else $str = '';
      $fields[] = $str;
    }

    if ($form['fd06']) {
      $ds = $clsdriver->driver_status_html($row); // 운전자상태
      $fields[] = $ds;
    }
    if ($form['fd08']) {
      $fields[] = $row['dist5'].'m';
    }
    if ($form['fd07']) {
      $btn = button_general("삭제", 0, "_delete('$run_id')", $style='', $class='btn btn-danger');
      $fields[] = $btn;
    }

    print("<tr>");
    foreach ($fields as $f) {
      print("<td nowrap>$f</td>");
    }
    print("</tr>");

  }
  print("</tbody>");
  print("</table>");
  print("</div>");
  //dd($a);

  print<<<EOS
<script>
function _map(id) { var url = "$env[self]?mode=map&id="+id; wopen(url,630,730,1,1); }
function _edit(id,span) { lcolor(span); var url = "driver.php?mode=edit&id="+id; wopen(url,600,600,1,1); }
function _delete(id) { 
  var msg = "삭제할까요?"; if (!confirm(msg)) return;
  var url = "$env[self]?mode=delete&id="+id; wopen(url,630,730,1,1);
}
</script>
EOS;


  MainPageTail();
  exit;

### }}}

  exit;

?>
