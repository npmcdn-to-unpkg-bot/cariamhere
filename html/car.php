<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  $source_title = '차량';

  $env['menu']['1-1'] = true;

  $pathh = $env['prefix']."/www/theme/theme1/head.php";
  $pathf = $env['prefix']."/www/theme/theme1/foot.php";

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

  $qry = "UPDATE carinfo $sql_set WHERE id='$id'";
  $ret = db_query($qry);

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

  $qry = "INSERT INTO carinfo $sql_set";
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
    $title = "차량수정";
  } else {
    $row = array();
    $nextmode = 'doadd';
    $title = "차량입력";
  }

  MainPageHead($source_title, $pathh);
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

  MainPageTail($pathf);
  exit;
}

// 일괄입력
if ($mode == 'add2') {
  MainPageHead($source_title);
  ParagraphTitle('차량 일괄입력');
  print<<<EOS
<form name='form' action='$env[self]' method='post'>

<a href='car_form.xlsx'>양식엑셀파일 다운받기</a>

<p> 아래 내용을 지우고 엑셀양식 파일의 내용을 복사해서 붙이세요.

<input type='hidden' name='mode' value='add2b'>
EOS;

  $content = $form['content'];
  if (!$content) $content =<<<EOS
지파	교회	이름	전화번호	모델	차량번호	차종	색상	배기량	연식
요한	과천	임세환	1234-1440	BMW330d	12서1234	SE	검정	3000	2010
요한	과천	임세환	1234-1440	BMW330d	12서1234	SE	검정	3000	2016
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
    $sql_set = " SET ".join(",", $s);
    $qry = "insert into carinfo $sql_set";
    $ret = db_query($qry);
  }

  $qry = "update carinfo set car_no=own6, car_model=own5, car_color=own8 where car_no=''";
  $ret = db_query($qry);
  print<<<EOS
<a href='$env[self]'>업로드 완료. 돌아가기</a>
EOS;
  exit;
}

### }}}

  MainPageHead($source_title, $pathh);
  ParagraphTitle($source_title);

  $btn = array();
  $btn[] = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  $btn[] = button_general('차량 일괄입력', 0, "_add2()", $style='', $class='btn btn-info');

  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; urlGo(url); }
function _add2() { var url = "$env[self]?mode=add2"; urlGo(url); }
</script>
EOS;

  $qry = "SELECT c.*, d.driver_name"
." FROM carinfo c"
." LEFT JOIN driver d ON c.driver_id=d.id"
  ;
  $ret = db_query($qry);

  $buttons = join(' ', $btn);
  print<<<EOS
<div class="panel-heading">
$buttons
</div>

<table class='table table-striped'>
EOS;
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
  print<<<EOS
</table>

<script>
function _edit(id) {
  var url = "$env[self]?mode=edit&id="+id;
  urlGo(url);
}
</script>
EOS;

  MainPageTail($pathf);
  exit;

?>
