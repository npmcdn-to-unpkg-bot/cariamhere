<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.user.php");
  include_once("$env[prefix]/inc/class.carinfo.php");

  $source_title = '역할';

  //$clsdriver= new driver();
  //$clscar = new carinfo();

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
  $qry = "SELECT * FROM role WHERE id='$id'";
  $row = db_fetchone($qry);
  return $row;
}

function _sqlset(&$s) {
  global $form;
  $s[] = "role='{$form['role']}'";
  $s[] = "role_title='{$form['role_title']}'";
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

  $qry = "DELETE FROM role WHERE id='$id'";
  $ret = db_query($qry);

  $url = $env['self'];
  Redirect($url);
  exit;
}

if ($mode == 'doedit') {
  $id = $form['id'];

  $s = array();
  _sqlset($s);
  //$s[] = "udate=NOW()";
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE role $sql_set WHERE id='$id'";
  $ret = db_query($qry);


  $url = $env['self'];
  Redirect($url);
  exit;
}

if ($mode == 'doadd') {
  //dd($form);

  $s = array();
  _sqlset($s);
  //$s[] = "idate=NOW()";
  //$s[] = "udate=NOW()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO role $sql_set";
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

  $html = textinput_general('role', $row['role'], '20', '', $click_select, $maxlength=0);
  print _data_tr('role', $html);

  $html = textinput_general('role_title', $row['role_title'], '20', '', $click_select, $maxlength=0);
  print _data_tr('role_title', $html);


  print<<<EOS
<tr>
<td colspan='2' class='c'>
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

  $btn = array();
  $btn[] = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; urlGo(url); }
</script>
EOS;

  $qry = "SELECT r.*"
." FROM role r"
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
  print table_head_general(array('번호','role','role_title'));

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];

    $edit = _edit_link($row['role'], $id);

    print<<<EOS
<tr>
<td>{$cnt}</td>
<td>{$edit}</td>
<td>{$row['role_title']}</td>
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
