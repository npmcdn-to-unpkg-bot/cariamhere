<?php

// 사용자 정보 관리

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.user.php");
  include_once("$env[prefix]/inc/class.carinfo.php");
  include_once("$env[prefix]/inc/class.role.php");

  $source_title = '사용자';

  $env['menu']['1-2'] = true;

  $clsuser = new user();
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
  $qry = "SELECT * FROM user WHERE id='$id'";
  $row = db_fetchone($qry);
  return $row;
}

function _sqlset(&$s) {
  global $form;
  $s[] = "user_no='{$form['user_no']}'";
  $s[] = "user_name='{$form['user_name']}'";
  $s[] = "user_tel='{$form['user_tel']}'";
  $s[] = "role='{$form['role']}'";
  //$s[] = "did='{$form['did']}'";
  //$s[] = "apikey='{$form['apikey']}'";
  $s[] = "driver_stat='{$form['driver_stat']}'";
  $s[] = "lat='{$form['lat']}'";
  $s[] = "lng='{$form['lng']}'";
  $s[] = "car_id='{$form['car_id']}'";
  //$s[] = "phone_hash='{$form['phone_hash']}'";
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

if ($mode == 'dodel') {
  $id = $form['id'];

  $qry = "DELETE FROM user WHERE id='$id'";
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

  $qry = "UPDATE user $sql_set WHERE id='$id'";
  $ret = db_query($qry);

  // 차량 정보를 할당함
  $user_id = $id;
  $car_id = $form['car_id'];
  $clscar->set_driver($car_id, $user_id);

  // 할당된 차량이 있으면 차량 위치 정보를 바꾼다.
  $lat = $form['lat'];
  $lng = $form['lng'];
  $clscar->set_position($car_id, $lat, $lng);


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

  $qry = "INSERT INTO user $sql_set";
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

  $html = textinput_general('user_no', $row['user_no'], '20', '', $click_select, $maxlength=0);
  print _data_tr('고유번호', $html);

  $html = textinput_general('user_name', $row['user_name'], '20', '', $click_select, $maxlength=0);
  print _data_tr('이름', $html);

  $html = textinput_general('user_tel', $row['user_tel'], '20', '', $click_select, $maxlength=0);
  print _data_tr('전화번호', $html);

  $html = $row['phone_hash'];
  print _data_tr('phone_hash', $html);

  $opt = $clsuser->driver_status_option($row['driver_stat']);
  $html = "<select name='driver_stat'>$opt</select>";
  print _data_tr('상태', $html);

  $opt = $clsrole->select_option_role($row['role']);
  $html = "<select name='role'>$opt</select>";
  print _data_tr('역할', $html);

  $opt = $clscar->car_select_option($row['car_id']);
  $html = "<select name='car_id'>$opt</select>";
  print _data_tr('차량', $html);

  $html = $row['did'];
  print _data_tr('DID', $html);

  $html = $row['pushkey'];
  print _data_tr('pushkey', $html);

  $apikey = $row['apikey'];
  $html = $row['apikey'];
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
지도를 클릭하여 위치를 지정
<div id="map" style='width:400px; height:400px;'></div>
</td>
</tr>
EOS;
  google_select_location_general('map', 'get_position', 'set_position', 13);

  $apikey = $row['apikey'];
  $url = sprintf("%s?appkey=%s", $conf['notice_url'], $apikey);
  print<<<EOS
<tr>
<td></td>
<td>
<a href='$url' target=_blank>공지사항 확인 $url</a>
</td>
</tr>
EOS;

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
.", r.role_title"
." FROM user d"
." LEFT JOIN carinfo c ON d.car_id=c.id"
." LEFT JOIN role r ON d.role=r.role"
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
  print table_head_general(array('ID','이름','역할','전화번호','상태','차량','가입','위치','appKey'));

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];

    $edit = _edit_link($row['user_name'], $id);
    $pos = sprintf("%s,%s", $row['lat'], $row['lng']);

    print<<<EOS
<tr>
<td>{$id}</td>
<td>{$edit}</td>
<td>{$row['role_title']}</td>
<td>{$row['user_tel']}</td>
<td>{$row['DsName']}</td>
<td>{$row['car_no']}</td>
<td>{$row['_rflag']}</td>
<td>{$pos}</td>
<td>{$row['apikey']}</td>
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
