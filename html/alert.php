<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.driver.php");
  include_once("$env[prefix]/inc/class.carinfo.php");

  $source_title = '알림 메시지';

### {{{
function _alert_css($group) {
       if ($group == '운행시작') $c = 'alert alert_start';
  else if ($group == '운행종료') $c = 'alert alert_stop';
  else if ($group == '긴급') $c = 'alert alert_emergency';
  else if ($group == '경유지근처') $c = 'alert alert_passby';
  else if ($group == '등록실패') $c = 'alert alert_regfail';
  else $c = '';
  return $c;
}

function option_alert_group($preset) {
  $list = array('=선택=:all','운행시작','운행종료','긴급','경유지근처','등록실패');
  if (!$preset) $preset = 'all';
  $opt = option_general($list, $preset);
  return $opt;
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

  $v = $form['group1'];
  $opt = option_alert_group($v);
  print("구분:<select name='group1'>$opt</select>");

  if ($form['dtno']) $chk = ' checked'; else $chk = '';
  print<<<EOS
<label><input type='checkbox' name='dtno'$chk>데스크톱 알람 끄기</label>
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

  print<<<EOS
</form>
EOS;

  $page = $form['page'];

  $total = 99999;
  $ipp = 30;
  //$last = $total / $ipp;
  list($start, $last, $page) = calc_page($ipp, $total);

  print pagination_bootstrap2($page, $total, $ipp, '_page');
  ## }}

  //dd($form);

  $w = array('1');

  $v = $form['group1'];
  if ($v && $v != 'all') $w[] = "a.group1='$v'";

  $sql_where = sql_where_join($w, $d=0, 'AND');

  $sql_from = " FROM alert a";

  $sql_select = "SELECT *";

  $qry = $sql_select.$sql_from.$sql_join.$sql_where
    ." ORDER BY a.idate DESC"
    ." LIMIT $start,$ipp";

  //dd($qry);

  $ret = db_query($qry);

  $now = get_now();
  print<<<EOS
<input type='button' onclick="notifyMe('알람 테스트','테스트입니다.','http://m.daum.net')" value='알람 테스트'>
현재시간: $now
EOS;

  print<<<EOS
<div class="panel panel-default">
<table class='table table-striped'>
EOS;
  print table_head_general(array('시간','구분','메시지'));
  print("<tbody>");

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;

    //dd($row);

    $id = $row['id'];
    $group = $row['group1'];
    $cls = _alert_css($group);
    $grp = "<span class='$cls'>$group</span>";

    print<<<EOS
<tr>
<td nowrap>{$row['idate']}</td>
<td nowrap>{$grp}</td>
<td nowrap>{$row['message']}</td>
</tr>
EOS;
  }
  print("</tbody>");
  print("</table>");
  print("</div>");
  //dd($a);

  print<<<EOS
<script>
function _edit(id) { var url = "driver.php?mode=edit&id="+id; urlGo(url); }
</script>
EOS;

  // 알람 메시지를 얻어오기
  $info = get_alert_messages($limit=3);
  dd($info);

  $script = "";
  $count = 0;
  foreach ($info as $item) {
    $count++;
    $msg = $item['message'];
    $grp = $item['group1'];
    $idate = $item['idate'];
    // notifyMe('알람 테스트','테스트입니다.','http://m.daum.net');
    $script .=<<<EOS
notifyMe('$grp','[$idate] $msg','');
EOS;
  }
  print("새로운 알람: $count 개 ");

  print<<<EOS
<p><a href='/background/do.php'>background</a>
EOS;

  print<<<EOS
<script>
$(function() {
  $script
});

setTimeout("location.reload();",10000);
</script>
EOS;

  MainPageTail();
  exit;

?>
