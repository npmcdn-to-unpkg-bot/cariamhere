<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  $source_title = '차량';

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
  $qry = "SELECT * FROM carinfo WHERE id='$id'";
  $row = db_fetchone($qry);
  return $row;
}

function _sqlset(&$s) {
  global $form;
  $s[] = "lat='{$form['lat']}'";
  $s[] = "lng='{$form['lng']}'";
  $s[] = "car_no='{$form['car_no']}'";
  $s[] = "car_model='{$form['car_model']}'";
  $s[] = "car_color='{$form['car_color']}'";
  $s[] = "car_memo='{$form['car_memo']}'";
}

function _edit_link($title, $id) {
  if (!$title) $title = '--';
  $html = <<<EOS
<span class=link onclick="_edit('$id',this)">{$title}</span>
EOS;
  return $html;
}

### }}}

### {{{
// 차량모델 자동완선
$term = $form['term'];
if ($term) $mode = 'auto_car_model';
if ($mode == 'auto_car_model') {
  $qry = "select car_model from carmodel WHERE car_model like '$term%'";
  $ret = db_query($qry);

  $a = array();
  while ($row = db_fetch($ret)) {
    $m = $row['car_model'];
    $a[] = $m;
  }
  print json_encode($a);
  exit;
}

if ($mode == 'dodel') {
  $id = $form['id'];

  $qry = "DELETE FROM carinfo WHERE id='$id'";
  $ret = db_query($qry);

  CloseAndReloadOpenerWindow();
  exit;
}

if ($mode == 'doedit') {
  $id = $form['id'];

  $s = array();
  _sqlset($s);
  $s[] = "udate=NOW()";
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE carinfo $sql_set WHERE id='$id'";
  $ret = db_query($qry);

  CloseAndReloadOpenerWindow();
  exit;
}

if ($mode == 'doadd') {
  //dd($form);

  $s = array();
  _sqlset($s);
  $s[] = "idate=NOW()";
  $s[] = "udate=NOW()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO carinfo $sql_set";
  $ret = db_query($qry);

  CloseAndReloadOpenerWindow();
  exit;
}

if ($mode == 'add' || $mode == 'edit') {

  if ($mode == 'edit') {
    $id = $form['id'];
    $row = _get($id);
    $nextmode = 'doedit';
    $title = "차량수정";
  } else {
    $row = array();
    $nextmode = 'doadd';
    $title = "차량입력";
  }

  PopupPageHead($source_title);
  ParagraphTitle($source_title);
  ParagraphTitle($title, 1);

  print<<<EOS

<table class='table table-striped'>
<form name='form'>
EOS;

  print<<<EOS
<tr>
<td colspan='2' class='c'>
<input type='hidden' name='mode' value='$nextmode'>
<input type='hidden' name='id' value='$id'>
<input type='button' value='확인' onclick='sf_1()' class='btn btn-primary'>
<input type='button' value='차량 삭제' onclick='sf_del()' class='btn btn-danger'>
</td>
</tr>
EOS;


  $click_select = true;

  $html = textinput_general('car_no', $row['car_no'], '20', '', $click_select, $maxlength=0);
  print _data_tr('차량번호', $html);

  $html = textinput_general('car_model', $row['car_model'], '20', '', $click_select, $maxlength=0, 'car_model');
  print _data_tr('차량모델', $html);
  print<<<EOS
<script>
  $(function() {
    $( "#car_model" ).autocomplete({
      source: "$env[self]",
    });
  });
</script>
EOS;

  $html = textinput_general('car_color', $row['car_color'], '20', '', $click_select, $maxlength=0);
  print _data_tr('차량색상', $html);

  $html = textinput_general('car_memo', $row['car_memo'], '20', '', $click_select, $maxlength=0);
  print _data_tr('차량메모', $html);

  $lat = textinput_general('lat', $row['lat'], '15', '', $click_select, $maxlength=0);
  $lng = textinput_general('lng', $row['lng'], '15', '', $click_select, $maxlength=0);
  $html = "($lat, $lng)";
  print _data_tr('차량좌표', $html);

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
<input type='button' value='차량 삭제' onclick='sf_del()' class='btn btn-danger'>
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
  if (!confirm('삭제할까요?')) return;
  var url = "$env[self]?mode=dodel&id=$id";
  urlGo(url);
}
</script>
EOS;

  PopupPageTail();
  exit;
}

### }}}

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='', $class='btn btn-primary');
  print<<<EOS
<form name='search_form' method='get'>
$btn
<input type='hidden' name='mode' value='$mode'>
<input type='hidden' name='page' value='{$form['page']}'>
EOS;

  $v = $form['cno'];
  $ti = textinput_general('cno', $v, $size='10', 'keypress_text()', $click_select=true, $maxlength=0, $id='');
  print("차량번호:$ti");

  $v = $form['clr'];
  $ti = textinput_general('clr', $v, $size='10', 'keypress_text()', $click_select=true, $maxlength=0, $id='');
  print("차량색상:$ti");

  $ipp = get_ipp(20,$min=10,$max=500);
  $opts = option_ipp($ipp, array(10,20,50,200,500));
  print("출력:<select name='ipp'>$opts</select>");

  $sel = array(); $sort = $form['sort'];
  if ($sort == '') $sel[1] = ' selected'; else $sel[$sort] = ' selected';
  print<<<EOS
&nbsp;&nbsp;정렬:<select name='sort'>
<option value='1'$sel[1]>최근변경</option>
<option value='2'$sel[2]>차량번호</option>
<option value='3'$sel[3]>운전자</option>
<option value='4'$sel[4]>소속</option>
</select>
EOS;

  print("<input type='button' onclick='_vopt()' onmouseover='_vopt()' value='표시정보' class='btn'>");

  $fck = array(); // field check '' or ' checked'
  fck_init($fck, $defaults='1,2,3,4,5,7,8');
  print<<<EOS
<div id="vopt" style='display:none;'>
<label><input type='checkbox' name='fd01' $fck[1]>번호</label>
<label><input type='checkbox' name='fd02' $fck[2]>모델</label>
<label><input type='checkbox' name='fd03' $fck[3]>색상</label>
<label><input type='checkbox' name='fd04' $fck[4]>메모</label>
<label><input type='checkbox' name='fd05' $fck[5]>운전자</label>
<label><input type='checkbox' name='fd06' $fck[6]>GPS좌표</label>
<label><input type='checkbox' name='fd07' $fck[7]>소유자소속</label>
<label><input type='checkbox' name='fd08' $fck[8]>실소유자</label>
<label><input type='checkbox' name='fd09' $fck[9]>차종,배기량,연식</label>
</div>
EOS;


  print("</form>");
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
function sf_0() { document.search_form.submit(); }
function sf_1() { document.search_form.page.value = '1'; sf_0(); }
function _page(page) { document.search_form.page.value = page; sf_0(); }
function keypress_text() { if (event.keyCode != 13) return; sf_0(); }
</script>
EOS;

  ## }}

  $btn = array();
  $btn[] = button_general('입력', 0, "_add()", $style='', $class='btn');
  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; wopen(url,600,600,1,1); }
</script>
EOS;

  $w = array('1');

  $v = $form['cno'];
  if ($v) $w[] = "(c.car_no LIKE '%$v%')";

  $v = $form['clr'];
  if ($v) $w[] = "(c.car_color LIKE '%$v%')";

  $sql_where = sql_where_join($w, $d=0, 'AND');

  $sort = $form['sort']; if ($sort == '') $sort = '1';
       if ($sort == '1') $o = "c.udate DESC";
  else if ($sort == '2') $o = "c.car_no";
  else if ($sort == '3') $o = "d.driver_name";
  else if ($sort == '4') $o = "c.own1, c.own2";
  else                   $o = "c.udate DESC";
  $sql_order = " ORDER BY $o";
  //dd($sql_order);

  $sql_from = " FROM carinfo c";
  $sql_join = " LEFT JOIN driver d ON c.driver_id=d.id";

  $qry = "select count(*) count".$sql_from.$sql_join.$sql_where;
  $row = db_fetchone($qry);
  $total = $row['count'];
  $page = $form['page'];
  $ipp = get_ipp(20,$min=10,$max=500);
  list($start, $last, $page) = calc_page($ipp, $total);
  print pagination_bootstrap2($page, $total, $ipp, '_page');

  $qry = "SELECT c.*, d.driver_name"
    .$sql_from.$sql_join.$sql_where.$sql_order
    ." LIMIT $start,$ipp";
  $ret = db_query($qry);

  $buttons = join(' ', $btn);
  print<<<EOS
<div class="panel panel-default">
<div class="panel-heading">
$buttons
</div>
EOS;

  ## {{
  print("<table class='table table-striped'>");

  $head = array();
  $head[] = 'ID';
  if ($form['fd01']) $head[] = '차량번호';
  if ($form['fd04']) $head[] = '메모';
  if ($form['fd05']) $head[] = '운전자';
  if ($form['fd06']) $head[] = '현위치';
  if ($form['fd07']) { $head[] = '소속1'; $head[] = '소속2'; }
  if ($form['fd08']) $head[] = '실소유자';
  if ($form['fd02']) $head[] = '모델';
  if ($form['fd03']) $head[] = '색상';
  if ($form['fd09']) { $head[] = '차종'; $head[] = '배기량'; $head[] = '연식'; }

  print table_head_general($head);
  print("<tbody>");

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];
    $driver_id = $row['driver_id'];

    $fields = array();
    $fields[] = $id;

    $edit = _edit_link($row['car_no'], $id);
    if ($form['fd01']) $fields[] = $edit;

    if ($form['fd04']) $fields[] = $row['car_memo'];
    if ($form['fd05']) {
      $dname = $row['driver_name'];
      $str = "<span class=link onclick=\"_edit_driver('$driver_id',this)\">$dname</span>";
      $fields[] = $str;
    }

    $pos = "({$row['lat']}, {$row['lng']})";
    if ($form['fd06']) $fields[] = $pos;
    if ($form['fd07']) {
      $fields[] = $row['own1'];
      $fields[] = $row['own2'];
    }
    if ($form['fd08']) {
      $fields[] = $row['own3'];
    }
    if ($form['fd02']) $fields[] = $row['car_model'];
    if ($form['fd03']) $fields[] = $row['car_color'];
    if ($form['fd09']) {
      $fields[] = $row['own7'];
      $fields[] = $row['own9'];
      $fields[] = $row['own10'];
    }

    print("<tr>");
    foreach ($fields as $f) {
      print("<td>$f</td>");
    }
    print("</tr>");

  }
  print("</table>");
  ## }}

  print<<<EOS
<script>
function _edit(id,span) { lcolor(span); var url = "$env[self]?mode=edit&id="+id; wopen(url,600,600,1,1); }
function _edit_driver(id,span) { lcolor(span); var url = "driver.php?mode=edit&id="+id; wopen(url,600,600,1,1); }
</script>
EOS;

  MainPageTail();
  exit;

?>
