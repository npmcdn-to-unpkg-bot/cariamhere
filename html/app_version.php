<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  $source_title = '어플 버전정보';


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
  $qry = "SELECT * FROM app_version WHERE id='$id'";
  $row = db_fetchone($qry);
  return $row;
}

function _sqlset(&$s) {
  global $form;
  $s[] = "phone_os='{$form['phone_os']}'";
  $s[] = "version_int='{$form['version_int']}'";
  $s[] = "version_str='{$form['version_str']}'";
  $s[] = "version_date='{$form['version_date']}'";
  $s[] = "protocol_ver='{$form['protocol_ver']}'";
//dd($form); dd($s); exit;
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

  $qry = "DELETE FROM app_version WHERE id='$id'";
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

  $qry = "UPDATE app_version $sql_set WHERE id='$id'";
//dd($qry); exit;
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

  $qry = "INSERT INTO app_version $sql_set";
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
<form name='form' action="$env[self]" method='post'>
EOS;

  $click_select = true;

  $html = textinput_general('phone_os', $row['phone_os'], '20', $onkeypress='', $click_select, 0);
  print _data_tr('OS', $html);

  $html = textinput_general('version_int', $row['version_int'], '20', $onkeypress='', $click_select, 0);
  print _data_tr('어플 버전(정수값)', $html);

  $html = textinput_general('version_str', $row['version_str'], '20', $onkeypress='', $click_select, 0);
  print _data_tr('어플 버전(문자열)', $html);

  $d = $row['version_date'];
  if (!$d) $d = get_now();
  $html = textinput_general('version_date', $d, '20', $onkeypress='', $click_select, 0);
  print _data_tr('어플 버전(날짜)', $html);

  $html = textinput_general('protocol_ver', $row['protocol_ver'], '20', $onkeypress='', $click_select, 0);
  print _data_tr('프로토콜버전(숫자)', $html);


  print<<<EOS
<tr>
<td colspan='2' class='c'>
<input type='hidden' name='_group' value=''>
<input type='hidden' name='mode' value='$nextmode'>
<input type='hidden' name='id' value='$id'>
<input type='button' value='확인' onclick='sf_1()' class='btn btn-primary'>
<input type='button' value='삭제' onclick='sf_del()' class='btn btn-danger'>
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
</script>
EOS;

  MainPageTail();
  exit;
}
### }}}

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  $btn = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  print<<<EOS
<script>
function _add() {
  var url = "$env[self]?mode=add";
  urlGo(url);
}
</script>
EOS;

  $qry = "SELECT p.*"
   ." FROM app_version p"
   ;
  $ret = db_query($qry);

  print<<<EOS
<div class="panel panel-default">
<div class="panel-heading">
$btn
</div>
<table class='table table-striped dataC'>
EOS;
  print table_head_general(array('번호','OS','어플 버전(INT)','어플 버전(STR)','어플 버전(날짜)','프로토콜 버전'));

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];

    $edit = _edit_link($row['phone_os'], $id);

    print<<<EOS
<tr>
<td>{$cnt}</td>
<td>{$edit}</td>
<td>{$row['version_int']}</td>
<td>{$row['version_str']}</td>
<td>{$row['version_date']}</td>
<td>{$row['protocol_ver']}</td>
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
