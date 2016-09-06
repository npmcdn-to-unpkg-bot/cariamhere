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

  $sql_from = " FROM driver d";

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

  $driver_no = $form['driver_no'];
  $s[] = "driver_no='{$driver_no}'";

  $driver_tel = $form['driver_tel'];
  $s[] = "driver_tel='{$driver_tel}'";

  $s[] = "driver_name='{$form['driver_name']}'";

  $s[] = "driver_team='{$form['driver_team']}'";

  $driver_cho = cho_hangul($form['driver_name']);
  $s[] = "driver_cho='{$driver_cho}'";

  $s[] = "driver_stat='{$form['driver_stat']}'";

  $s[] = "person_id='{$form['person_id']}'";

  $v = $form['gperiod']; if ($v < 5) $v = 5;
  $s[] = "gperiod='{$v}'";

  $s[] = "lat='{$form['lat']}'";
  $s[] = "lng='{$form['lng']}'";

  $s[] = "car_id='{$form['car_id']}'";
//dd($s); exit;

}

function _edit_link($title, $id) {
  if (!$title) $title = '--';
  $html = <<<EOS
<span class=link onclick="_edit('$id',this)">{$title}</span>
EOS;
  return $html;
}

function _summary() {
  global $form;
  global $clsdriver;

  if ($form['smtm']) {
    $f_team = $form['team'];
  } else $f_team = '';
  $info = $clsdriver->driver_summary($f_team);

  $teams = $clsdriver->driver_all_teams();
  $teams[] = '전체';
  print("<div class='btn-group' role='group' aria-label='...' style=''>");
  foreach ($teams as $team) {
    if ($team == $f_team) $cls = "btn btn-warning btn-lg";
    else $cls = "btn btn-default btn-lg";
    print("<button type='button' class='$cls' onclick=\"_summgo2('$team')\">$team</button>");
  }
  print("</div>");

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
  form.smtm.value = '';
  form.submit();
}
function _summgo2(team) {
  var form = document.search_form;
  if (team == '전체') {
    team = 'all';
    form.smtm.value = '';
  } else {
    form.smtm.value = '1';
  }
  form.team.value=team;
  form.submit();
}
</script>
EOS;
}

### }}}

### {{{

if ($mode == 'dodel') {
  $id = $form['id'];

  $qry = "DELETE FROM driver WHERE id='$id'";
  $ret = db_query($qry);

  CloseAndReloadOpenerWindow();
  exit;
}

if ($mode == 'doedit') {
  $id = $form['id'];

  $s = array();
  _sqlset($s);
  //$s[] = "udate=NOW()";
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

  $person_id = $form['person_id'];
  $clsdriver->set_run_person($driver_id, $person_id);

  CloseAndReloadOpenerWindow();
  exit;
}

if ($mode == 'doadd') {
  //dd($form);

  $s = array();
  _sqlset($s);
  $s[] = "idate=NOW()";
  //$s[] = "udate=NOW()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO driver $sql_set";
  $ret = db_query($qry);

  CloseAndReloadOpenerWindow();
  exit;
}

if ($mode == 'add' || $mode == 'edit') {

  if ($mode == 'edit') {
    $id = $form['id'];
    $row = _get($id);
    if (!$row) iError('에러');
    $nextmode = 'doedit';
    $title = "수정";
  } else {
    $row = array();
    $nextmode = 'doadd';
    $title = "입력";
  }

  PopupPageHead($source_title);
  ParagraphTitle($source_title);
  ParagraphTitle($title, 1);

  print<<<EOS
<table class='table table-striped'>
<form name='form'>
EOS;

  $btn = array();
  $btn[] = "<input type='button' value='확인' onclick='sf_1()' class='btn btn-primary'>";
  if ($mode == 'edit') {
  $btn[] = "<input type='button' value='삭제' onclick='sf_del($id)' class='btn btn-danger'>";
  }
  $buttons = join(' ', $btn);
  print<<<EOS
<tr>
<td colspan='2' class='c'>
$buttons
</td>
</tr>
EOS;

  $click_select = true;

  $html = $row['id'];
  print _data_tr('ID', $html);

  $html = textinput_general('driver_no', $row['driver_no'], '20', '', $click_select, $maxlength=0);
  print _data_tr('고유번호', $html);

  $html = textinput_general('driver_name', $row['driver_name'], '20', '', $click_select, $maxlength=0);
  print _data_tr('이름', $html);

  $html = $row['driver_cho'];
  print _data_tr('초성', $html);

  $html = textinput_general('driver_tel', $row['driver_tel'], '20', '', $click_select, 0, '', 'ui-input');
  print _data_tr('전화번호', $html);

  $v = $row['driver_stat'];
  $opt = $clsdriver->driver_status_option($v);
  $html = "<select name='driver_stat'>$opt</select>";
  print _data_tr('운전자상태', $html);

  print _data_tr('비상상황구분', $row['emergency']);

  $preset = $row['person_id'];
  $opt = $personObj->select_option_person($preset);
  $html=<<<EOS
<select name='person_id'>$opt</select>
EOS;
  print _data_tr('의전인사', $html);

  $opt = $clscar->car_select_option($row['car_id']);
  $car_id = $row['car_id'];
  $html = "<select name='car_id'>$opt</select>"
    ."<p><span class='link' onclick=\"_car()\">차량정보</span>";
  print _data_tr('차량', $html);

  $opt = $clsdriver->select_team_option($row['driver_team']);
  $html = "<select name='driver_team'>$opt</select>";
  print _data_tr('소속팀', $html);

  $opt = $clsdriver->select_team_option($row['driver_team']);

  $list = array('5초:5초','10초:10','20초:20','30초:30','60초:60');
  $preset = $row['gperiod']; if (!$preset) $preset = '30';
  $opt = option_general($list, $preset);
  $html = "<select name='gperiod'>$opt</select>";
  print _data_tr('GPS주기', $html);

  $html = $row['phone_os'];
  print _data_tr('phone_os', $html);

  $html = $row['phone_hash'];
  print _data_tr('phone_hash', $html);

  $html = $row['did'];
  print _data_tr('DID', $html);

  $html = $row['pushkey'];
  print _data_tr('pushkey', $html);

  $apikey = $row['apikey'];
  $html = $row['apikey'];
  print _data_tr('appKey', $html);

  print _data_tr('is_driving', $row['is_driving']);

  print _data_tr('idate', $row['idate']);
  print _data_tr('udate', $row['udate']);
  print _data_tr('chat_id', $row['chat_id']);

  $lat = textinput_general('lat', $row['lat'], '15', '', $click_select, $maxlength=0);
  $lng = textinput_general('lng', $row['lng'], '15', '', $click_select, $maxlength=0);
  $html =<<<EOS
($lat, $lng) <span onclick="_address()" class='link'>주소조회</span>
<span id='address'></span>
EOS;
  print _data_tr('위치좌표', $html);

  print<<<EOS
<script>
function _car() {
  var id = document.form.car_id.value;
  var url = "car.php?mode=edit&id="+id; wopen(url,600,600,1,1);
}
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


  if ($mode == 'edit') {
  print _data_tr('소속1', $row['own1']);
  print _data_tr('소속2', $row['own2']);
  print _data_tr('실소유자', $row['own3']);
  print _data_tr('연락처', $row['own4']);
  print _data_tr('모델명', $row['own5']);
  print _data_tr('차량번호', $row['own6']);
  print _data_tr('차종', $row['own7']);
  print _data_tr('색상', $row['own8']);
  print _data_tr('배기량', $row['own9']);
  print _data_tr('연식', $row['own10']);

  print _data_tr('소속1', $row['drv1']);
  print _data_tr('소속2', $row['drv2']);
  print _data_tr('이름', $row['drv3']);
  print _data_tr('연령', $row['drv4']);
  print _data_tr('연락처', $row['drv5']);
  print _data_tr('고유번호', $row['drv6']);
  print _data_tr('팀', $row['drv7']);
  }

  print<<<EOS
<tr>
<td colspan='2' class='c'>
<input type='hidden' name='mode' value='$nextmode'>
<input type='hidden' name='id' value='$id'>
<input type='button' value='확인' onclick='sf_1()' class='btn btn-primary'>
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
function sf_del(id) { var url = "$env[self]?mode=dodel&id="+id; urlGo(url); }
function sf_log() { var url = "driverlog.php?mode=log&id=$id"; urlGo(url); }
function sf_runs() { var url = "run.php?driver_id=$id"; urlGo(url); }

$(function() {
  _address();
});
</script>
EOS;

  script_daum_map();
  PopupPageTail();
  exit;
}

if ($mode == 'searchq') {
  //dd($form);
  $s = $form['searchVal'];
  if ($s == '') exit;

  $k = trim($s);
  $sql_where = " WHERE (driver_name LIKE '%$k%') OR (driver_cho LIKE '%$k%')";

  $sql_select = $clsdriver->sql_select_run_1();
  $sql_join   = $clsdriver->sql_join_2();

  $qry = $sql_select.$sql_from.$sql_join.$sql_where;
  $ret = db_query($qry);

  $data = array();
  while ($row = db_fetch($ret)) {
    //dd($row);
    //print($row);
    $data[] = $row;
  }

  print json_encode($data);
  exit;
}

if ($mode == 'detail') {
  dd('mode = detail');
  dd($form);
  $id = $form['id'];
exit;

  $row = $personObj->get_person($id);
  //dd($row);

  print<<<EOS
<table class='table table-striped'>
EOS;

  print _data_tr('이름', $row['person_name']);
  print _data_tr('그룹', $row['person_group']);
  print _data_tr('국가', $row['nname']);
  print _data_tr('메모', $row['memo']);

  print("</table>");
  exit;
}

### }}}

  MainPageHead($source_title);
  //ParagraphTitle($source_title);

/*
  print<<<EOS
<div id='topmenu' style='margin-bottom:10px;'>
<input type='button' value='메뉴' class='btn' value='메뉴' style="width:100%;" onclick="showmenu()">
</div>
<script>
function showmenu() {
  var str = ""
    +"<a href='driver.php'>운전자</a>" + " :: "
    +"<a href='person.php'>의전인사</a>" + " :: "
    +"<a href='car.php'>차량</a>" + " :: "
    +"<a href='location.php'>장소</a>" + " :: "
    +"<a href='notice.php'>공지</a>" + " :: "
    +"<a href='run.php'>운행기록</a>"
    ;
  $("#topmenu").html(str);
}
</script>
EOS;
*/

  _summary();

  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='width:50px;height:50px;', $class='btn btn-primary');
  print<<<EOS
<table border='0' style='margin-top:10px;'>
<form name='search_form' method='get'>
<tr>
<td>$btn</td>
<td align='left'>
<input type='hidden' name='mode' value='$mode'>
<input type='hidden' name='page' value='{$form['page']}'>
<input type='hidden' name='smtm' value='0'>
EOS;

  $v = $form['search'];
  $ti = textinput_general('search', $v, 20, 'keypress_text()', true, 0, '', 'ui-corner-all','이름/고유번호/전화번호');
  print("$ti");

  $v = $form['dno'];
  $ti = textinput_general('dno', $v, $size='8', 'keypress_text()', true, 0, '', 'ui-corner-all','운전자번호');
  print("$ti");

  $v = $form['sosk'];
  $ti = textinput_general('sosk', $v, $size='8', 'keypress_text()', true, 0, '', 'ui-corner-all','소속');
  print("$ti");

  $v = $form['person_name'];
  $ti = textinput_general('person_name', $v, 10, 'keypress_text()', true, 0, '', 'ui-corner-all','의전이름');
  print("$ti");

  print("<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");

  $ds = $form['ds'];
  $opt = $clsdriver->driver_status_option($ds);
  print("상태:<select name='ds'>$opt</select>");

  $v = $form['team'];
  $opt = $clsdriver->select_team_option($v);
  print("팀:<select name='team'>$opt</select>");

  $sel = array(); $sort = $form['sort'];
  if ($sort == '') $sel[1] = ' selected'; else $sel[$sort] = ' selected';
  print<<<EOS
정렬방법:<select name='sort'>
<option value='1'$sel[1]>번호</option>
<option value='2'$sel[2]>최근변경</option>
<option value='3'$sel[3]>이름</option>
<option value='4'$sel[4]>소속</option>
<option value='5'$sel[5]>단말OS</option>
<option value='6'$sel[6]>상태</option>
<option value='7'$sel[7]>차량번호</option>
</select>
EOS;

  $ipp = get_ipp(20,$min=10,$max=500);
  $opts = option_ipp($ipp, array(10,20,50,200,500));
  print("출력:<select name='ipp'>$opts</select>");

  print("<input type='button' onclick='_vopt()' value='표시정보' class='btn'>");

  $fck = array(); // field check '' or ' checked'
  fck_init($fck, $defaults='1,2,5,10');
  print<<<EOS
<div id="vopt" style='display:none;'>
<label><input type='checkbox' name='fd01' $fck[1]>팀</label>
<label><input type='checkbox' name='fd10' $fck[10]>상태</label>
<label><input type='checkbox' name='fd02' $fck[2]>차량</label>
<label><input type='checkbox' name='fd03' $fck[3]>출발지</label>
<label><input type='checkbox' name='fd04' $fck[4]>목적지</label>
<label><input type='checkbox' name='fd05' $fck[5]>의전인사</label>
<label><input type='checkbox' name='fd06' $fck[6]>출발,도착시간</label>
<label><input type='checkbox' name='fd07' $fck[7]>단말OS</label>
<label><input type='checkbox' name='fd08' $fck[8]>소속</label>
<label><input type='checkbox' name='fd09' $fck[8]>전화번호,고유번호</label>
<label><input type='checkbox' name='fd11' $fck[11]>GPS좌표</label>
<label><input type='checkbox' name='fd12' $fck[12]>최종업데이트</label>
</div>
EOS;

  print("</td>");
  print("</tr>");
  print("</form>");
  print("</table>");
  //dd($form);

  print<<<EOS
<script>
function _vopt() {
  $('#vopt').toggle();
}
</script>
EOS;

  print<<<EOS
<script>
function sf_0() {
  document.search_form.submit();
}
function sf_1() {
  document.search_form.page.value = '1';
  document.search_form.smtm.value = '';
  sf_0();
}

function _page(page) { document.search_form.page.value = page; sf_0(); }
function keypress_text() { if (event.keyCode != 13) return; sf_0(); }
</script>
EOS;

  ## }}

  $w = array('1');

  $v = $form['search'];
  if ($v) $w[] = "(d.driver_name LIKE '%$v%' OR d.driver_cho LIKE '%$v%' OR d.driver_tel LIKE '%$v%' OR d.driver_no LIKE '%$v%')";

  $v = $form['dno'];
  if ($v) $w[] = "(d.id='$v')";

  $v = $form['person_name'];
  if ($v) $w[] = "(p.person_name LIKE '%$v%' OR p.person_cho LIKE '%$v%')";

  $v = $form['sosk'];
  if ($v) $w[] = "(d.drv1 LIKE '%$v%' OR d.drv2 LIKE '%$v%')";

  $v = $form['team'];
  if ($v && $v != 'all') $w[] = "d.driver_team='$v'";

  $ds = $form['ds'];
  if ($ds != '' && $ds != 'all') $w[] = "d.driver_stat='$ds'";

  $sql_where = sql_where_join($w, $d=0, 'AND');

  $sql_select = $clsdriver->sql_select_run_1()
       .", d.lat, d.lng"
       .", d.car_id, d.emergency, d.udate driver_udate"
       .", d.phone_os, d.drv1, d.drv2"
       .", d.driver_tel, d.driver_no";

  $sql_join   = $clsdriver->sql_join_4();

  $sort = $form['sort']; if ($sort == '') $sort = '1';
       if ($sort == '1') $o = "d.id";
  else if ($sort == '2') $o = "d.udate DESC";
  else if ($sort == '3') $o = "d.driver_name";
  else if ($sort == '4') $o = "d.drv1, d.drv2";
  else if ($sort == '5') $o = "d.phone_os DESC";
  else if ($sort == '6') $o = "d.driver_stat";
  else if ($sort == '7') $o = "c.car_no";
  else                   $o = "d.id";
  $sql_order = " ORDER BY $o";
  //dd($sql_order);

  $qry = "select count(*) count".$sql_from.$sql_join.$sql_where;
  $row = db_fetchone($qry);
  $total = $row['count'];
  $page = $form['page'];
  $ipp = get_ipp(20,$min=10,$max=500);
  list($start, $last, $page) = calc_page($ipp, $total);
  print pagination_bootstrap2($page, $total, $ipp, '_page');

  $qry = $sql_select.$sql_from.$sql_join.$sql_where.$sql_order
    ." LIMIT $start,$ipp";
  $ret = db_query($qry);

  $btn = array();
  $btn[] = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  $btn[] = button_general('운전자/차량 업로드', 0, "_add2()", $style='', $class='btn btn-info');
  $btn[] =<<<EOS
검색(이름,초성):<input type='text' name='searchq' onkeyup="searchq();" onclick='this.select()' onclick='this.select()'>
EOS;
  $buttons = join(' ', $btn);

  ## {{
  print("<div class='panel panel-default'>");
  print<<<EOS
<div class="panel-heading">$buttons</div>
EOS;

  ## {{
  print("<table class='table table-striped dataC' id='resultTable'>");

  $head = array();
  $head[] = '번호';
  $head[] = '이름';
  if ($form['fd01']) $head[] = '팀';
  if ($form['fd10']) $head[] = '상태';
  if ($form['fd02']) $head[] = '차량';
  $head[] = '운행기록';
  if ($form['fd06']) { $head[] = '출발시간'; $head[] = '도착시간'; }
  if ($form['fd03']) $head[] = '출발지';
  if ($form['fd04']) $head[] = '목적지';
  if ($form['fd05']) $head[] = '의전인사';
  if ($form['fd07']) { $head[] = '단말OS'; }
  if ($form['fd08']) { $head[] = '소속1'; $head[] = '소속2'; }
  if ($form['fd09']) { $head[] = '전화번호'; $head[] = '고유번호'; }
  if ($form['fd11']) { $head[] = 'GPS좌표'; }
  if ($form['fd12']) { $head[] = '최종업데이트'; }
  print table_head_general($head);
  print("<tbody>");

  $cnt = 0;
  $info = array();
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $driver_id = $row['driver_id'];

    $edit = _edit_link($row['driver_name'], $driver_id);
    $pos = sprintf("%s,%s", $row['lat'], $row['lng']);

    $ds = $clsdriver->driver_status_html($row); // 운전자상태

    $rcnt = $clsdriver->run_count($driver_id);
    $btn = "<input type='button' value='($rcnt)건' onclick=\"_run('$driver_id')\" class='btn btn-primary'>";

    $fields = array();
    $fields[] = $driver_id;
    $fields[] = $edit;
    if ($form['fd01']) $fields[] = $row['driver_team'];
    if ($form['fd10']) $fields[] = $ds;

    $car_id = $row['car_id'];
    $edit =<<<EOS
<span class=link onclick="_edit_car($car_id,this)">{$row['car_no']}</span>
EOS;
    if ($form['fd02']) $fields[] = $edit;

    $fields[] = $btn;
    if ($form['fd06']) { $fields[] = $row['stime']; $fields[] = $row['etime']; }
    if ($form['fd03']) $fields[] = $row['loc1'];
    if ($form['fd04']) $fields[] = $row['loc2'];

    $per_id = $row['person_id'];
    $edit =<<<EOS
<span class=link onclick="_edit_person('$per_id',this)">{$row['person_name']}</span>
EOS;
    if ($form['fd05']) $fields[] = $edit;

    if ($form['fd07']) $fields[] = $row['phone_os'];
    if ($form['fd08']) { $fields[] = $row['drv1']; $fields[] = $row['drv2']; }
    if ($form['fd09']) { $fields[] = $row['driver_tel']; $fields[] = $row['driver_no']; }
    if ($form['fd11']) { $str = "({$row['lat']},{$row['lng']})"; $fields[] = $str; }

    if ($form['fd12']) {
      $htb = human_time_before($row['driver_udate']);
      if ($htb) $str = "{$htb}전"; else $str = '';
      $fields[] = $str;
    }

    print("<tr>");
    foreach ($fields as $f) {
      print("<td nowrap>$f</td>");
    }
    print("</tr>");

  }
  print("</tbody>");
  print("</table>");
  ## }}
  print("</div>");
  ## }}

  print("<div id='detailView'></div>");

  $json = json_encode($info);
  print<<<EOS
<script>
function _run(id) { var url = "run.php?driver_id="+id; urlGo(url); }
function _edit(id,span) { lcolor(span); var url = "$env[self]?mode=edit&id="+id; wopen(url, 600,600,1,1); }
function _edit_person(id,span) { lcolor(span); var url = "person.php?mode=edit&id="+id; wopen(url, 600,600,1,1); }
function _edit_car(id,span) { lcolor(span); var url = "car.php?mode=edit&id="+id; wopen(url, 600,600,1,1); }

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
  $("input[name='search']").focus();
});
</script>
EOS;

  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; wopen(url,600,600,1,1); }
function _add2() { var url = "upload.php"; urlGo(url); }

var qcall = 0;
var tbody_origin = null;
function searchq() {
  //console.log(qcall);

  var searchTxt = $("input[name='searchq']").val();
  var i = 0
  console.log("searchTxt = "+searchTxt);

  if (searchTxt == '') {
    if (tbody_origin) {
      $("#resultTable > tbody").remove();
      tbody_origin.appendTo("#resultTable");
    }
  } else {
    qcall++;
    if (qcall == 1) {
      tbody_origin = $("#resultTable > tbody").detach();
    }
  }

  $.post("$env[self]", {searchVal: searchTxt, mode:'searchq'}, function(data) {

    try {
      //console.log(data);

      var list = JSON.parse(data);
      //console.log(list);
      //console.log(list.length);

      if (qcall == 1) {
        //console.log(tbody_origin);
      } else {
        $("#resultTable > tbody").remove();
        $("#resultTable ").append("<tbody></tbody>");
      }

      if (list.length == 1) {
        id = list[0]['id'];
        _detail_view(id);
      }

      for (i = 0; i < list.length; i++) {
        var item = list[i];
        //console.log(item);
        var id = item['id'];
        var row = _data_row(i, id, item);
        $("#resultTable ").append(row);
      }

    } catch(e) {
    }
  });
}
function _detail_view(id) {
  console.log("detail view "+ id);
  $.post("$env[self]", {id: id, mode:'detail'}, function(data) {
    //console.log(data);
    $("#detailView").html(data);
  });
}

function _data_row(i, id, item) {
  console.log(item);
  var driver_id= item['driver_id'];
  var row = "<tr>"
   +"<td>"+driver_id+"</td>"
   +"<td><span class=link onclick=\"_edit('"+driver_id+"',this)\">"+item['driver_name']+"</span></td>"
   +"<td>"+item['DsName']+"</td>"
   +"<td>"+item['car_no']+"</td>"
   +"<td><input type='button' value='운행기록' onclick=\"_run("+id+")\" class='btn btn-primary'></td>"
   +"<td>"+item['stime']+"</td>"
   +"<td>"+item['etime']+"</td>"
   +"<td>"+item['loc1']+"</td>"
   +"<td>"+item['loc2']+"</td>"
   +"<td>"+item['person_name']+"</td>"
   +"</tr>";
  return row;
}
</script>
EOS;


  script_daum_map();
  MainPageTail();
  exit;

?>
