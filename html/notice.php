<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.role.php");

  $source_title = '공지사항';

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
  $qry = "SELECT * FROM notice WHERE id='$id'";
  $row = db_fetchone($qry);
  return $row;
}

function _sqlset(&$s) {
  global $form;

  $s[] = "title='{$form['title']}'";

//dd($form);
  $str = db_escape_string($form['content']);
//dd($str);
  $s[] = "content='$str'";
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

  $qry = "DELETE FROM notice WHERE id='$id'";
  $ret = db_query($qry);

  $url = $env['self'];
  Redirect($url);
  exit;
}

if ($mode == 'doedit') {
  $id = $form['id'];
//dd($form); exit;

  $s = array();
  _sqlset($s);
  $s[] = "udate=NOW()";
//dd($s);
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE notice $sql_set WHERE id='$id'";
//dd($qry);
//exit;
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

  $qry = "INSERT INTO notice $sql_set";
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
    $title = "공지 수정";
  } else {
    $row = array();
    $nextmode = 'doadd';
    $title = "공지 입력";
  }

  MainPageHead($source_title);
  ParagraphTitle($source_title);
  ParagraphTitle($title, 1);

  print<<<EOS
<table class='table table-striped'>
<form name='form' action="$env[self]" method='post'>
EOS;

  $click_select = true;

  $html = textinput_general('title', $row['title'], '40', '', $click_select, 0);
  print _data_tr('제목', $html);

  $html = textarea_general('content', $row['content'], $cols='80', $rows='10', $click_select=true, '');
  print _data_tr('내용', $html);

  $html = $row['idate'];
  print _data_tr('작성시간', $html);

  $html = $row['udate'];
  print _data_tr('수정시간', $html);

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

  $b1 = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; urlGo(url); }
</script>
EOS;

  $qry = "SELECT n.*"
   ." FROM notice n"
   ." ORDER BY n.udate DESC"
   ;
  $ret = db_query($qry);

  print<<<EOS
<div class="panel panel-default">
<div class="panel-heading">$b1</div>
<table class='table table-striped dataC'>
EOS;
  print table_head_general(array('번호','제목/내용','수정일시'));

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];
    $edit = _edit_link($row['title'], $id);
  
    $content = $row['content'];
    $cont = cut_str($content,$len=100,$tail="...");

    print<<<EOS
<tr>
<td rowspan='2'>{$cnt}</td>
<td>{$edit}</td>
<td rowspan='2'>{$row['udate']}</td>
</tr>
<tr>
<td>{$cont}</td>
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
