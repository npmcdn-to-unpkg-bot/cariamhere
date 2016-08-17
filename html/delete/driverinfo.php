<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.driver.php");
  include_once("$env[prefix]/inc/class.carinfo.php");
  include_once("$env[prefix]/inc/class.role.php");

  $source_title = '사용자';

  $clsdriver= new driver();
  $clscar = new carinfo();
  $clsrole = new role();

### {{{
function _data_tr($title, $html) {
  $str=<<<EOS
<tr>
<th>$title</th>
<td>$html</td>
</tr>
EOS;
  return $str;
}

function _get($id) {
  $qry = "SELECT * FROM driver WHERE id='$id'";
  $row = db_fetchone($qry);
  return $row;
}

function _sqlset(&$s) {
  global $form;
  $s[] = "driver_no='{$form['driver_no']}'";
  $s[] = "driver_name='{$form['driver_name']}'";
  $s[] = "driver_tel='{$form['driver_tel']}'";
  $s[] = "role='{$form['role']}'";
  //$s[] = "driver_sosok='{$form['driver_sosok']}'";
  $s[] = "did='{$form['did']}'";
  $s[] = "apikey='{$form['apikey']}'";
  $s[] = "driver_stat='{$form['driver_stat']}'";
  $s[] = "lat='{$form['lat']}'";
  $s[] = "lng='{$form['lng']}'";
  $s[] = "car_id='{$form['car_id']}'";
  $s[] = "phone_hash='{$form['phone_hash']}'";
}

function _edit_link($title, $id) {
  if (!$title) $title = '--';
  $html = <<<EOS
<span class=link onclick="_edit('$id')">{$title}</span>
EOS;
  return $html;
}


### }}}

### {{{
/*
if ($mode == 'map') {
  //dd($form);
  $pos = $form['pos'];
  $points = urldecode($pos);
  //dd($a);

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  script_daum_map();
  print<<<EOS
<div id="map" style="width:100%;height:350px;"></div>

<script>
var map;
var markers = [];
var points = $points;

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


function addMarker(position) {

  var marker = new daum.maps.Marker({
      position: position
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
    var lat = p[0];
    var lng = p[1];
    addMarker(new daum.maps.LatLng(lat, lng));
  }

  //_map_range();
});

</script>
EOS;

  MainPageTail();
  exit;
}
*/

if ($mode == 'dodel') {
  $id = $form['id'];

  $qry = "DELETE FROM driver WHERE id='$id'";
  $ret = db_query($qry);

  $url = $env['self'];
  Redirect($url);
  exit;
}

if ($mode == 'doedit') {
  $id = $form['id'];

  $s = array();
  _sqlset($s);
  $s[] = "udate=NOW()";
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE driver $sql_set WHERE id='$id'";
  $ret = db_query($qry);

  //$pass = $form['password'];
  //$clsdriver->update_password($id, $pass);

  $url = $env['self'];
  Redirect($url);
  exit;
}

if ($mode == 'doadd') {
  //dd($form);

  $s = array();
  _sqlset($s);
  $s[] = "idate=NOW()";
  $s[] = "udate=NOW()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO driver $sql_set";
  $ret = db_query($qry);

  $url = $env['self'];
  Redirect($url);
  exit;
}

if ($mode == 'add' || $mode == 'edit') {

  if ($mode == 'edit') {
    $id = $form['id'];
    $row = _get($id);
    $nextmode = 'doedit';
    $title = "수정";
  } else {
    $row = array();
    $nextmode = 'doadd';
    $title = "입력";
  }

  MainPageHead($source_title);
  ParagraphTitle($source_title);
  ParagraphTitle($title, 1);

  print<<<EOS
<table class='table table-striped'>
<form name='form'>
EOS;

  $click_select = true;

  $html = textinput_general('driver_no', $row['driver_no'], '20', '', $click_select, $maxlength=0);
  print _data_tr('고유번호', $html);

  $html = textinput_general('driver_name', $row['driver_name'], '20', '', $click_select, $maxlength=0);
  print _data_tr('이름', $html);

  $html = textinput_general('driver_tel', $row['driver_tel'], '20', '', $click_select, $maxlength=0);
  print _data_tr('전화번호', $html);

  $html = textinput_general('phone_hash', $row['phone_hash'], '20', '', $click_select, $maxlength=0);
  print _data_tr('phone_hash', $html);

  //$html = textinput_general('driver_sosok', $row['driver_sosok'], '20', '', $click_select, $maxlength=0);
  //print _data_tr('소속', $html);

  $opt = $clsdriver->driver_status_option($row['driver_stat']);
  $html = "<select name='driver_stat'>$opt</select>";
  print _data_tr('상태', $html);

  $opt = $clsrole->select_option_role($row['role']);
  $html = "<select name='role'>$opt</select>";
  print _data_tr('역할', $html);

  $opt = $clscar->car_select_option($row['car_id']);
  $html = "<select name='car_id'>$opt</select>";
  print _data_tr('차량', $html);

  $html = textinput_general('DID', $row['did'], '50', '', $click_select, $maxlength=0);
  print _data_tr('DID', $html);

  $html = textinput_general('apikey', $row['apikey'], '50', '', $click_select, $maxlength=0);
  print _data_tr('AppKey', $html);

  $lat = textinput_general('lat', $row['lat'], '15', '', $click_select, $maxlength=0);
  $lng = textinput_general('lng', $row['lng'], '15', '', $click_select, $maxlength=0);
  $html = "($lat, $lng)";
  print _data_tr('위치좌표', $html);

  print<<<EOS
<script>
function get_position() {
  var lat = document.form.lat.value;
  var lng = document.form.lng.value;
  return {'lat':lat, 'lng':lng};
}
function set_position(lat, lng) {
  document.form.lat.value = lat;
  document.form.lng.value = lng;
}
</script>
<tr>
<td></td>
<td>
<div id="map" style='width:400px; height:400px;'></div>
</td>
</tr>
EOS;
  google_select_location_general('map', 'get_position', 'set_position', 13);


  print<<<EOS
<tr>
<td colspan='2' class='c'>
<input type='hidden' name='mode' value='$nextmode'>
<input type='hidden' name='id' value='$id'>
<input type='button' value='확인' onclick='sf_1()' class='btn btn-primary'>
<input type='button' value='삭제' onclick='sf_del()' class='btn btn-danger'>
<input type='button' value='로그보기' onclick='sf_log()' class='btn btn-primary'>
</td>
</tr>

</form>
</table>

<script>
function sf_1() {
  var form = document.form;
  form.submit();
}
function sf_del() {
  if (!confirm('삭제할까요?')) return;
  var url = "$env[self]?mode=dodel&id=$id";
  urlGo(url);
}
function sf_log() {
  var url = "driverlog.php?mode=log&id=$id";
  urlGo(url);
}
</script>
EOS;

  MainPageTail();
  exit;
}

/*
if ($mode == 'log') {
  MainPageHead($source_title);
  ParagraphTitle($source_title);
//dd($form);

  $page = $form['page'];

  $total = 100000;
  $ipp = 100;
  //$last = $total / $ipp;
  list($start, $last, $page) = calc_page($ipp, $total);

  $id = $form['id'];
  $qry = "SELECT l.*"
." FROM driver_log l"
." WHERE l.driver_id='$id'"
." ORDER BY idate DESC"
." LIMIT $start,$ipp"
 ;

  $ret = db_query($qry);

  $formdata = array('mode'=>'log', 'id'=>$id);
  print pagination_bootstrap($formdata, $page, $total, $ipp);

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
$btn
</div>
<table class='table table-striped'>
EOS;
  print table_head_general(array('번호','driver_id','status','car_id','lat','lng','idate'));

  $a = array();
  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];

    $lat = $row['lat'];
    $lng = $row['lng'];
    $a[] = array($lat, $lng);

    print<<<EOS
<tr>
<td>{$cnt}</td>
<td>{$row['driver_id']}</td>
<td>{$row['driver_stat']}</td>
<td>{$row['car_id']}</td>
<td>{$row['lat']}</td>
<td>{$row['lng']}</td>
<td>{$row['idate']}</td>
</tr>
EOS;
  }
  print<<<EOS
</table>
</div>
EOS;
  //dd($a);
  $positions = urlencode(json_encode($a));

  print<<<EOS
<form name='form' action='$env[self]' method='post'>
<input type='hidden' name='pos' value="$positions">
<input type='hidden' name='mode' value="map">
</form>
EOS;

  MainPageTail();
  exit;
}
*/

### }}}

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  $btn = array();
  $btn[] = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  $btn[] = button_general('역할', 0, "_role()", $style='', $class='btn btn-info');
  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; urlGo(url); }
function _role() { var url = "roleinfo.php"; urlGo(url); }
</script>
EOS;

  $qry = "SELECT d.*"
.", c.car_no"
.", Ds.DsName"
.", IF(d.rflag,'O','X') _rflag"
." FROM driver d"
." LEFT JOIN carinfo c ON d.car_id=c.id"
." LEFT JOIN Ds ON d.driver_stat=Ds.Ds"
 ;
  $ret = db_query($qry);

  $buttons = join(' ', $btn);
  print<<<EOS
<div class="panel panel-default">
<div class="panel-heading">
$buttons
</div>
<table class='table table-striped'>
EOS;
  print table_head_general(array('번호','이름','고유번호','전화번호','상태','차량','가입','위치'));

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];

    $edit = _edit_link($row['driver_name'], $id);
    $pos = sprintf("%s,%s", $row['lat'], $row['lng']);

    print<<<EOS
<tr>
<td>{$cnt}</td>
<td>{$edit}</td>
<td>{$row['driver_no']}</td>
<td>{$row['driver_tel']}</td>
<td>{$row['DsName']}</td>
<td>{$row['car_no']}</td>
<td>{$row['_rflag']}</td>
<td>{$pos}</td>
</tr>
EOS;
  }
  print<<<EOS
</table>
</div>

<script>
function _edit(id) {
  var url = "$env[self]?mode=edit&id="+id;
  urlGo(url);
}
</script>
EOS;

  MainPageTail();
  exit;

?>
