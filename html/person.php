<?php

// 인사

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.person.php");

  $source_title = '인사정보';

  $debug = true;
  $debug = false;

  $clsperson = new person();

  $sql_select = "SELECT p.*, Nat.*"
    //.", IF(p.person_fflag, 'O', 'X') _fflag"
    ;
  $sql_from = " FROM person p";
  $sql_join = " LEFT JOIN Nat ON p.person_nation=Nat.nnum";


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
  $qry = "SELECT * FROM person WHERE id='$id'";
  $row = db_fetchone($qry);
  return $row;
}

function _sqlset(&$s) {
  global $form;

  $person_cho = cho_hangul($form['person_name']);

  $s[] = "person_group='{$form['person_group']}'";
  $s[] = "person_name='{$form['person_name']}'";
  $s[] = "person_cho='{$person_cho}'";

  $s[] = "person_fflag='{$form['person_fflag']}'";
  $s[] = "person_nation='{$form['person_nation']}'";
  $s[] = "memo='{$form['memo']}'";
//dd($form); dd($s); exit;
}

function _edit_link($title, $id) {
  if (!$title) $title = '--';
  $html = <<<EOS
<span class=link onclick="_edit('$id')">{$title}</span>
EOS;
  return $html;
}

function _person_split($line) {
  $cols = preg_split("/[,\t]/", $line);
  return $cols;
}

### }}}

### {{{
if ($mode == 'dodel') {
  $id = $form['id'];

  $qry = "DELETE FROM person WHERE id='$id'";
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

  $qry = "UPDATE person $sql_set WHERE id='$id'";
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

  $qry = "INSERT INTO person $sql_set";
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
    $title = "인사정보 수정";
  } else {
    $row = array();
    $nextmode = 'doadd';
    $title = "인사정보 입력";
  }

  MainPageHead($source_title);
  ParagraphTitle($source_title);
  ParagraphTitle($title, 1);

  print<<<EOS
<table class='table table-striped'>
<form name='form' action="$env[self]" method='post'>
EOS;

  print<<<EOS
<tr>
<td colspan='2' class='c'>
<input type='button' value='확인' onclick='sf_1()' class='btn btn-primary'>
<input type='button' value='삭제' onclick='sf_del()' class='btn btn-danger'>
</td>
</tr>
EOS;


  $click_select = true;

  $preset = $row['person_group'];
  $opt = $clsperson->person_group_option($preset);
  $html = "<select name='person_group'>$opt</select>";
  print _data_tr('분류', $html);

  $html = textinput_general('person_name', $row['person_name'], '20', $onkeypress='', $click_select, $maxlength=0);
  print _data_tr('이름', $html);

  print _data_tr('이름초성', $row['person_cho']);

  $list = array('일반:0','깃발부착 대상:1');
  $preset = $row['person_fflag']; if (!$preset) $preset = '0';
  $html = radio_list_general('person_fflag', $list, $preset, '', '&nbsp;&nbsp;');
  print _data_tr('깃발', $html);

  list($html, $script) = nation_select($row['person_nation'], 'person_nation', '', '', true);
  print _data_tr('국가', $html);
  print $script;

  $html = textarea_general('memo', $row['memo'], 60, 5, true);
  print _data_tr('메모', $html);

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
if ($mode == 'detail') {
  //dd($form);
  $id = $form['id'];

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

if ($mode == 'searchq') {
  //dd($form);
  $s = $form['searchVal'];
  if ($s == '') exit;

  $k = trim($s);
  $sql_where = " WHERE (person_name LIKE '%$k%') OR (person_cho LIKE '%$k%')";

  $qry = $sql_select.$sql_from.$sql_join.$sql_where;
  $ret = db_query($qry);
//dd($qry);

  $data = array();
  while ($row = db_fetch($ret)) {
    //dd($row);
    //print($row);
    $data[] = $row;
  }

  print json_encode($data);
  exit;
}


// 일괄입력
if ($mode == 'add2') {
  MainPageHead($source_title);
  ParagraphTitle('인사정보 일괄입력');

  print<<<EOS
<form name='form' action='$env[self]' method='post'>

<p> 형식 : 인사번호,공식한글이름,행사계층,대표직책(한),호텔명,국적

<input type='hidden' name='mode' value='add2b'>
EOS;
  $content = $form['content'];
  if (!$content) $content =<<<EOS
인사번호	공식한글이름	행사계층	대표직책(한)	호텔명	국적
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
<th>인사번호</th>
<th>공식한글이름</th>
<th>행사계층</th>
<th>대표직책(한)</th>
<th>호텔명</th>
<th>국적</th>
</tr>
EOS;

  foreach ($rows as $line) {
    $line = trim($line);
    if (!$line) continue;
    $cols = _person_split($line);

    print<<<EOS
<tr>
<td>{$cols[0]}</td>
<td>{$cols[1]}</td>
<td>{$cols[2]}</td>
<td>{$cols[3]}</td>
<td>{$cols[4]}</td>
<td>{$cols[5]}</td>
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

  // 업로드전 모두 삭제
  $qry = "DELETE FROM person";
  $ret = db_query($qry);

  $content = $form['content'];
  $rows = preg_split("/\n/", $content);

  foreach ($rows as $line) {
    $line = trim($line);
    if (!$line) continue;
    $cols = _person_split($line);


    $s = array();
    $s[] = "per1='{$cols[0]}'"; // 번호
    $s[] = "per2='{$cols[1]}'"; // 이름
    $s[] = "per3='{$cols[2]}'";
    $s[] = "per4='{$cols[3]}'";
    $s[] = "per5='{$cols[4]}'";
    $s[] = "per6='{$cols[5]}'";

    $nation = $cols[5];
    $nation_code = $personObj->get_nation_code($nation);
    $s[] = "person_nation='{$nation_code}'"; // 국가

    $person_cho = cho_hangul($cols[1]);
    $s[] = "person_cho='{$person_cho}'"; // 초성

    $sql_set = " SET ".join(",", $s);
    $qry = "INSERT INTO person $sql_set";
    if ($debug) dd($qry);
    else $ret = db_query($qry);
  }

  $qry = "UPDATE person"
     ." SET per_no=per1, person_name=per2, person_group=per3, person_position=per4, person_hotel=per5"
     ." WHERE person_name=''";
  $ret = db_query($qry);

  print<<<EOS
<a href='$env[self]'>업로드 완료. 돌아가기</a>
EOS;
  exit;
}

### }}}

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  $btn = array();
  $btn[] = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  $btn[] = button_general('일괄입력', 0, "_add2()", $style='', $class='btn btn-info');
  $btn[] =<<<EOS
검색(이름/초성):<input type='text' name='search' onkeyup="searchq();">
EOS;

  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; urlGo(url); }
function _add2() { var url = "$env[self]?mode=add2"; urlGo(url); }


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
  var name = item['person_name'];
  var row = "<tr>"
    +"<td>"+i+"</td>"
    +"<td><span class=link onclick=\"_edit('"+id+"')\">"+name+"</span></td>"
    +"<td>"+item['person_group']+"</td>"
    +"<td>"+item['nname']+"</td>"
    +"<td>"+item['_fflag']+"</td>"
    +"</tr>";
  return row;
}

</script>
EOS;

  $qry = $sql_select.$sql_from.$sql_join;
  $ret = db_query($qry);

  $buttons = join('', $btn);
  print<<<EOS
<div class="panel panel-default">
<div class="panel-heading">
$buttons
</div>

<div id='output'>
</div>

<table class='table table-striped dataC' id='resultTable'>
EOS;
  print table_head_general(array('인사번호','이름','그룹','직책','국가','호텔'));
  print("<tbody>");

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];

    $edit = _edit_link($row['person_name'], $id);

    print<<<EOS
<tr>
<td>{$row['per_no']}</td>
<td>{$edit}</td>
<td>{$row['person_group']}</td>
<td>{$row['person_position']}</td>
<td>{$row['nname']}</td>
<td>{$row['person_hotel']}</td>
</tr>
EOS;
  }
  print<<<EOS
</tbody>
</table>
</div>

<div id='detailView'> </div>

<script>
function _edit(id) { var url = "$env[self]?mode=edit&id="+id; urlGo(url); }

$(function() {
  $("input[name='search']").focus();
});
</script>
EOS;

  MainPageTail();
  exit;

?>
