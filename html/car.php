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
<span class=link onclick="_edit('$id')">{$title}</span>
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

  MainPageTail();
  exit;
}

### }}}

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='', $class='btn');
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

  $qry = "SELECT c.*, d.driver_name"
    ." FROM carinfo c"
    ." LEFT JOIN driver d ON c.driver_id=d.id"
    .$sql_where
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
  print table_head_general(array('ID','차량번호','모델','색상','메모','현위치','운전자'));

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];

    $edit = _edit_link($row['car_no'], $id);

    print<<<EOS
<tr>
<td>{$row['id']}</td>
<td>{$edit}</td>
<td>{$row['car_model']}</td>
<td>{$row['car_color']}</td>
<td>{$row['car_memo']}</td>
<td>({$row['lat']}, {$row['lng']})</td>
<td>{$row['driver_name']}</td>
</tr>
EOS;
  }
  print("</table>");
  ## }}

  print<<<EOS
<script>
function _edit(id) { var url = "$env[self]?mode=edit&id="+id; wopen(url,600,600,1,1); }
</script>
EOS;

  MainPageTail();
  exit;

?>
