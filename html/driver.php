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

function _summary() {
  global $clsdriver;
  $info = $clsdriver->driver_summary();
  $a = array();
  foreach ($info as $ds=>$count) {
    $a[] = "$ds: {$count}명";
  }
  print join(", ", $a);
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
<!--
<input type='button' value='로그보기' onclick='sf_log()' class='btn btn-primary'>
<input type='button' value='운행기록' onclick='sf_runs()' class='btn btn-primary'>
-->
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

  $preset = $row['person_id'];
  $opt = $personObj->select_option_person($preset);
  $html=<<<EOS
<select name='person_id'>$opt</select>
EOS;
  print _data_tr('의전인사', $html);

  $opt = $clscar->car_select_option($row['car_id']);
  $html = "<select name='car_id'>$opt</select>";
  print _data_tr('차량', $html);

  $opt = $clsdriver->select_team_option($row['driver_team']);
  $html = "<select name='driver_team'>$opt</select>";
  print _data_tr('소속팀', $html);

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
<!--
<input type='button' value='로그보기' onclick='sf_log()' class='btn btn-primary'>
<input type='button' value='운행기록' onclick='sf_runs()' class='btn btn-primary'>
-->
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
function sf_log() { var url = "driverlog.php?mode=log&id=$id"; urlGo(url); }
function sf_runs() { var url = "run.php?driver_id=$id"; urlGo(url); }

$(function() {
  _address();
});
</script>
EOS;

  script_daum_map();
  MainPageTail();
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
  ParagraphTitle($source_title);

  _summary();


  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='', $class='btn');
  print<<<EOS
<form name='search_form' method='get'>
$btn
<input type='hidden' name='mode' value='$mode'>
<input type='hidden' name='page' value='{$form['page']}'>
EOS;

  $v = $form['driver_name'];
  $ti = textinput_general('driver_name', $v, $size='10', 'keypress_text()', $click_select=true, $maxlength=0, $id='');
  print("운전자이름:$ti");

  $v = $form['person_name'];
  $ti = textinput_general('person_name', $v, $size='10', 'keypress_text()', $click_select=true, $maxlength=0, $id='');
  print("VIP이름:$ti");

  $ds = $form['ds'];
  $opt = $clsdriver->driver_status_option($ds);
  print("상태:<select name='ds'>$opt</select>");

  $v = $form['team'];
  $opt = $clsdriver->select_team_option($v);
  print("팀:<select name='team'>$opt</select>");

  print("</form>");

  print<<<EOS
<script>
function sf_1() {
  document.search_form.submit();
}

function _page(page) { document.search_form.page.value = page; sf_1(); }
function keypress_text() { if (event.keyCode != 13) return; sf_1(); }
</script>
EOS;

  $page = $form['page'];

  $total = 100000;
  $ipp = 30;
  list($start, $last, $page) = calc_page($ipp, $total);

  print pagination_bootstrap2($page, $total, $ipp, '_page');
  ## }}

  $btn = array();
  $btn[] = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  $btn[] = button_general('운전자/차량 업로드', 0, "_add2()", $style='', $class='btn btn-info');
  $btn[] =<<<EOS
검색(이름,초성):<input type='text' name='search' onkeyup="searchq();" onclick='this.select()'>
EOS;

  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; urlGo(url); }
function _add2() { var url = "upload.php"; urlGo(url); }

var qcall = 0;
var tbody_origin = null;
function searchq() {
  //console.log(qcall);

  var searchTxt = $("input[name='search']").val();
  var i = 0
  //console.log(searchTxt);

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
   +"<td>"+i+"</td>"
   +"<td><span class=link onclick=\"_edit('"+driver_id+"')\">"+item['driver_name']+"</span></td>"
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

  $w = array('1');

  $v = $form['driver_name'];
  if ($v) $w[] = "(d.driver_name LIKE '%$v%' OR d.driver_cho LIKE '%$v%')";

  $v = $form['person_name'];
  if ($v) $w[] = "(p.person_name LIKE '%$v%' OR p.person_cho LIKE '%$v%')";

  $v = $form['team'];
  if ($v && $v != 'all') $w[] = "d.driver_team='$v'";

  $ds = $form['ds'];
  if ($ds != '' && $ds != 'all') $w[] = "d.driver_stat='$ds'";

  $sql_where = sql_where_join($w, $d=0, 'AND');

  $sql_select = $clsdriver->sql_select_run_1();
  $sql_join   = $clsdriver->sql_join_4();

  $qry = $sql_select.$sql_from.$sql_join.$sql_where
    ." LIMIT $start,$ipp";

  $ret = db_query($qry);

  $buttons = join(' ', $btn);
  print<<<EOS
<div class="panel panel-default">
<div class="panel-heading">
$buttons
</div>
EOS;
  print("<table class='table table-striped dataC' id='resultTable'>");

  $head = array();
  $head[] = '번호';
  $head[] = '이름';
  $head[] = '팀';
  $head[] = '상태';
  $head[] = '차량';
  $head[] = '운행기록';
  $head[] = '출발시간';
  $head[] = '도착시간';
  $head[] = '출발지';
  $head[] = '목적지';
  $head[] = '의전인사';
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

    $ds = $row['DsName'];
    if ($clsdriver->is_driving_status($ds)) $ds = "<span class='ds_driving'>$ds</span>";
    else $ds = "<span class='ds_not_driving'>$ds</span>";

    //$info[] = array($run_id, $row['lat'], $row['lng']);

    $btn = "<input type='button' value='운행기록' onclick=\"_run('$driver_id')\" class='btn btn-primary'>";
    print<<<EOS
<tr>
<td>{$driver_id}</td>
<td>{$edit}</td>
<td>{$row['driver_team']}</td>
<td>{$ds}</td>
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
  print<<<EOS
</tbody>
</table>
</div>
EOS;

  print("<div id='detailView'></div>");

  $json = json_encode($info);
  print<<<EOS
<script>
function _run(id) { var url = "run.php?driver_id="+id; urlGo(url); }
function _edit(id) { var url = "$env[self]?mode=edit&id="+id; urlGo(url); }

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

  script_daum_map();
  MainPageTail();
  exit;

?>
