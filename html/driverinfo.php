<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.driver.php");
  include_once("$env[prefix]/inc/class.carinfo.php");
  include_once("$env[prefix]/inc/class.location.php");

  $source_title = '운전자';

  $clsdriver = new driver();
  $clscar = new carinfo();
  $clsloc = new location();

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
  $s[] = "driver_stat='{$form['driver_stat']}'";
  $s[] = "person_id='{$form['person_id']}'";

  $s[] = "lat='{$form['lat']}'";
  $s[] = "lng='{$form['lng']}'";
  $s[] = "car_id='{$form['car_id']}'";
//dd($s); exit;

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

  // 차량 정보를 할당함
  $driver_id = $id;
  $car_id = $form['car_id'];
  $clscar->set_driver($car_id, $driver_id);

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

  $qry = "INSERT INTO driver $sql_set";
  $ret = db_query($qry);

  $url = $env['self'];
  Redirect($url);
  exit;
}

// 일괄입력
if ($mode == 'add2') {
  MainPageHead($source_title);
  ParagraphTitle('운전자 일괄입력');
  print<<<EOS
<form name='form' action='$env[self]' method='post'>

<a href='driver_form.xlsx'>양식엑셀파일 다운받기</a>

<p> 아래 내용을 지우고 엑셀양식 파일의 내용을 복사해서 붙이세요.

<input type='hidden' name='mode' value='add2b'>
EOS;

  $content = $form['content'];
  if (!$content) $content =<<<EOS
지파	교회	이름	전화번호	모델	차량번호	차종	색상	배기량	연식	지파	교회	이름	나이	전화번호	고유번호
요한	과천	임세환	1234-1440	BMW330d	12서1234	SE	검정	3000	2010	요한	과천	홍길동	27	1234-4411	00221122-44444
요한	과천	임세환	1234-1440	BMW330d	12서1234	SE	검정	3000	2016	요한	과천	홍길동	27	1234-4411	00221122-44444
EOS;

  print<<<EOS
<textarea rows='10' cols='80' name='content' style='width:100%' onclick='this.select()'>
$content
</textarea>
<input type='button' value='미리보기' onclick='sf_1()'>
<input type='button' value='저장하기' onclick='sf_2()'>
</form>

<script>
function sf_1() { document.form.mode.value = 'add2'; document.form.submit(); }
function sf_2() { document.form.mode.value = 'add2do'; document.form.submit(); }
</script>
EOS;

  $content = $form['content'];
  $rows = preg_split("/\n/", $content);

  print<<<EOS
<table class='table table-striped'>
<tr>
<th>지파명</th>
<th>교회명</th>
<th>실소유자</th>
<th>연락처</th>
<th>모델명</th>
<th>차량번호</th>
<th>차종</th>
<th>색상</th>
<th>배기량</th>
<th>연식</th>
<th>지파</th>
<th>교회</th>
<th>이름</th>
<th>연령</th>
<th>연락처</th>
<th>고유번호</th>
</tr>
EOS;

  foreach ($rows as $line) {
    $line = trim($line);
    if (!$line) continue;
    $cols = preg_split("/[ ,\t]/", $line);
    print<<<EOS
<tr>
<td>{$cols[0]}</td>
<td>{$cols[1]}</td>
<td>{$cols[2]}</td>
<td>{$cols[3]}</td>
<td>{$cols[4]}</td>
<td>{$cols[5]}</td>
<td>{$cols[6]}</td>
<td>{$cols[7]}</td>
<td>{$cols[8]}</td>
<td>{$cols[9]}</td>
<td>{$cols[10]}</td>
<td>{$cols[11]}</td>
<td>{$cols[12]}</td>
<td>{$cols[13]}</td>
<td>{$cols[14]}</td>
<td>{$cols[15]}</td>
</tr>
EOS;
  }
  print<<<EOS
</table>
EOS;


  MainPageTail();
  exit;
}
if ($mode == 'add2do') {

  $content = $form['content'];
  $rows = preg_split("/\n/", $content);

  foreach ($rows as $line) {
    $line = trim($line);
    if (!$line) continue;
    $cols = preg_split("/[ ,\t]/", $line);

    $s = array();
    $s[] = "own1='{$cols[0]}'";
    $s[] = "own2='{$cols[1]}'";
    $s[] = "own3='{$cols[2]}'";
    $s[] = "own4='{$cols[3]}'";
    $s[] = "own5='{$cols[4]}'";
    $s[] = "own6='{$cols[5]}'";
    $s[] = "own7='{$cols[6]}'";
    $s[] = "own8='{$cols[7]}'";
    $s[] = "own9='{$cols[8]}'";
    $s[] = "own10='{$cols[9]}'";
    $s[] = "drv1='{$cols[10]}'";
    $s[] = "drv2='{$cols[11]}'";
    $s[] = "drv3='{$cols[12]}'";
    $s[] = "drv4='{$cols[13]}'";
    $s[] = "drv5='{$cols[14]}'";
    $s[] = "drv6='{$cols[15]}'";
    $sql_set = " SET ".join(",", $s);
    $qry = "insert into driver $sql_set";
    $ret = db_query($qry);
  }

  $qry = "update driver set driver_name=drv3,driver_tel=drv5,driver_no=drv6 where driver_name=''";
  $ret = db_query($qry);
  print<<<EOS
<a href='$env[self]'>업로드 완료. 돌아가기</a>
EOS;
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

  print<<<EOS
<tr>
<td colspan='2' class='c'>
<input type='button' value='확인' onclick='sf_1()' class='btn btn-primary'>
<input type='button' value='삭제' onclick='sf_del()' class='btn btn-danger'>
<input type='button' value='로그보기' onclick='sf_log()' class='btn btn-primary'>
<input type='button' value='운행기록' onclick='sf_sess()' class='btn btn-primary'>
</td>
</tr>
EOS;

  $click_select = true;

  $html = textinput_general('driver_no', $row['driver_no'], '20', '', $click_select, $maxlength=0);
  print _data_tr('고유번호', $html);

  $html = textinput_general('driver_name', $row['driver_name'], '20', '', $click_select, $maxlength=0);
  print _data_tr('이름', $html);

  $html = textinput_general('driver_tel', $row['driver_tel'], '20', '', $click_select, $maxlength=0);
  print _data_tr('전화번호', $html);

  $opt = $clsdriver->driver_status_option($row['driver_stat']);
  $html = "<select name='driver_stat'>$opt</select>";
  print _data_tr('상태', $html);

# $html = $row['is_driving'];
# print _data_tr('is_driving', $html);

  $preset = $row['person_id'];
  $opt = $personObj->select_option_person($preset);
  $html=<<<EOS
<select name='person_id'>$opt</select>
EOS;
  print _data_tr('의전대상자', $html);

# $html = $row['start_time'];
# print _data_tr('start_time', $html);


  $opt = $clscar->car_select_option($row['car_id']);
  $html = "<select name='car_id'>$opt</select>";
  print _data_tr('차량', $html);

# $opt = $clsloc->select_option_location($row['dep_id']);
# $html = "<select name='dep_id'>$opt</select>";
# $ti = textinput_general('dep_name', $row['dep_name'], '20', '', $click_select, $maxlength=0);
# print _data_tr('출발지', $html.$ti);

# $opt = $clsloc->select_option_location($row['des_id']);
# $html = "<select name='des_id'>$opt</select>";
# $ti = textinput_general('des_name', $row['des_name'], '20', '', $click_select, $maxlength=0);
# print _data_tr('목적지', $html.$ti);

  $html = $row['phone_hash'];
  print _data_tr('phone_hash', $html);

  $html = $row['did'];
  print _data_tr('DID', $html);

  $html = $row['pushkey'];
  print _data_tr('pushkey', $html);

  $apikey = $row['apikey'];
  $html = $row['apikey'];
  print _data_tr('AppKey', $html);

  $lat = textinput_general('lat', $row['lat'], '15', '', $click_select, $maxlength=0);
  $lng = textinput_general('lng', $row['lng'], '15', '', $click_select, $maxlength=0);
  $html =<<<EOS
($lat, $lng) <span onclick="_address()" class='link'>주소조회</span>
<span id='address'></span>
EOS;
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
  _address();
}

function _address() {
  var lat = document.form.lat.value;
  var lng = document.form.lng.value;

  var geocoder = new daum.maps.services.Geocoder();

  var coord = new daum.maps.LatLng(lat, lng);

  var callback = function(status, result) {
    if (status === daum.maps.services.Status.OK) {
      // 요청위치에 건물이 없는 경우 도로명 주소는 빈값입니다
      //console.log('도로명 주소 : ' + result[0].roadAddress.name);
      //console.log('지번 주소 : ' + result[0].jibunAddress.name);
      var addr = result[0].jibunAddress.name; 
      console.log('지번 주소 : ' + addr);
      $('#address').html(addr);
    }   
  };

  geocoder.coord2detailaddr(coord, callback);
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

  print _data_tr('own1', $row['own1']);
  print _data_tr('own2', $row['own2']);
  print _data_tr('own3', $row['own3']);
  print _data_tr('own4', $row['own4']);
  print _data_tr('own5', $row['own5']);
  print _data_tr('own6', $row['own6']);
  print _data_tr('own7', $row['own7']);
  print _data_tr('own8', $row['own8']);
  print _data_tr('own9', $row['own9']);
  print _data_tr('own10', $row['own10']);

  print _data_tr('drv1', $row['drv1']);
  print _data_tr('drv2', $row['drv2']);
  print _data_tr('drv3', $row['drv3']);
  print _data_tr('drv4', $row['drv4']);
  print _data_tr('drv5', $row['drv5']);
  print _data_tr('drv6', $row['drv6']);

  print<<<EOS
<tr>
<td colspan='2' class='c'>
<input type='hidden' name='mode' value='$nextmode'>
<input type='hidden' name='id' value='$id'>
<input type='button' value='확인' onclick='sf_1()' class='btn btn-primary'>
<input type='button' value='삭제' onclick='sf_del()' class='btn btn-danger'>
<input type='button' value='로그보기' onclick='sf_log()' class='btn btn-primary'>
<input type='button' value='운행기록' onclick='sf_sess()' class='btn btn-primary'>
</td>
</tr>
EOS;

  print<<<EOS
</form>
</table>

<script>
function sf_1() {
  var form = document.form;
  form.submit();
}
function sf_del() {
  //if (!confirm('삭제할까요?')) return;
  var url = "$env[self]?mode=dodel&id=$id";
  urlGo(url);
}
function sf_log() {
  var url = "driverlog.php?mode=log&id=$id";
  urlGo(url);
}
function sf_sess() {
  var url = "driversess.php?mode=sess&id=$id";
  urlGo(url);
}

$(function() {
  _address();
});
</script>
EOS;

  script_daum_map();
  MainPageTail();
  exit;
}


### }}}

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  $btn = array();
  $btn[] = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  $btn[] = button_general('운전자 일괄입력', 0, "_add2()", $style='', $class='btn btn-info');
  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; urlGo(url); }
function _add2() { var url = "$env[self]?mode=add2"; urlGo(url); }
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
  print table_head_general(array('ID','이름','상태','차량','출발지','목적지'));

  $cnt = 0;
  $info = array();
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];

    $edit = _edit_link($row['driver_name'], $id);
    $pos = sprintf("%s,%s", $row['lat'], $row['lng']);

    $info[] = array($id, $row['lat'], $row['lng']);

//<td>{$row['_rflag']}</td>
    print<<<EOS
<tr>
<td>{$id}</td>
<td>{$edit}</td>
<td>{$row['DsName']}</td>
<td>{$row['car_no']}</td>
<td>{$row['des_name1']}</td>
<td>{$row['dep_name1']}</td>
</tr>
EOS;
  }
  print<<<EOS
</table>
</div>
EOS;

  $json = json_encode($info);
  print<<<EOS
<script>
function _edit(id) {
  var url = "$env[self]?mode=edit&id="+id;
  urlGo(url);
}

// 주소를 업데이트하기.. 이렇게 하면 API 요청 횟수가 너무 많아질듯.
function _update_address_all() {

  var info = $json;
  console.log(info);

  var geocoder = new daum.maps.services.Geocoder();

  var callback = function(status, result) {
    if (status === daum.maps.services.Status.OK) {
      // 요청위치에 건물이 없는 경우 도로명 주소는 빈값입니다
      //console.log('도로명 주소 : ' + result[0].roadAddress.name);
      //console.log('지번 주소 : ' + result[0].jibunAddress.name);
      console.log(result[0]);
      var addr = result[0].jibunAddress.name; 
      console.log(addr); // 지번주소
      //$('#address').html(addr);
    }   
  };

  for (var i = 0; i < info.length; i++) {
    var item = info[i];
    console.log(item);
    var id = item[0];
    var lat = item[1];
    var lng = item[2];
    var coord = new daum.maps.LatLng(lat, lng);
    geocoder.coord2detailaddr({coord: coord, callback:callback, options:{index:i}});
  }

}

// onload
$(function() {
  //_update_address_all();
});
</script>
EOS;

  script_daum_map();
  MainPageTail();
  exit;

?>
