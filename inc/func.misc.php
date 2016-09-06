<?php

# display an error message and terminate program
function iError($msg, $go_back=1, $win_close=0, $exit=1) {
  $msg = preg_replace("/\n/", "\\n", $msg);
  print("<script>\n");
  print("alert(\"$msg\");\n");
  if ($go_back) print("history.go(-1);\n");
  if ($win_close) print("window.close();");
  print("</script>\n");
  if ($exit) exit;
}

function Redirect($url, $http=true) {
  # HTTP 헤더가 이미 전송되었으면 자바스크립트 방식을 이용해야함
  if (headers_sent()) $http=false;
  if ($http) {
    header("Location: $url");
    exit;
  } else {
    print("<script>\n");
    print("window.location='$url';\n");
    print("</script>\n");
    exit;
  }
}

# display an error message and redirect to url
function ErrorRedir($msg, $url) {
  $msg = preg_replace("/\n/", "\\n", $msg);
  print("<script>\n");
  print("alert(\"$msg\");\n");
  print("window.location='$url';\n");
  print("</script>\n");
  exit;
}

// $msg = "완료되었습니다.";
// $url = "$env[self]";
// InformRedir($msg, $url);
function InformRedir($msg, $url) {
  ErrorRedir($msg, $url);
}
function InformAndCloseWindow($msg) {
  print<<<EOS
<script>
alert("$msg");
window.close();
</script>
EOS;
}
function CloseAndChangeParentWindow($url) {
  print<<<EOS
<script>
window.opener.document.location = "$url";
window.close();
</script>
EOS;
}
function CloseAndReloadParentWindow() {
  print<<<EOS
<script>
window.opener.document.location.reload();
window.close();
</script>
EOS;
}
function CloseAndReloadOpenerWindow() {
  print<<<EOS
<script>
window.opener.document.location.reload();
window.close();
</script>
EOS;
}
function CloseAndReloadOpener($msg='') {
  print<<<EOS
<script>
if ("$msg" != '') alert("$msg");
window.opener.document.location.reload();
window.close();
</script>
EOS;
}
function AlertAndReloadParentFrame($msg) {
  print<<<EOS
<script>
  if ("$msg" != '') alert("$msg");
parent.document.location.reload();
</script>
EOS;
}

function Pager_f($formname, $page, $total, $ipp) {
  global $conf, $env;
  $html = '';

  $btn_prev = "<img src='/img/calendar/l.gif' border=0 width=11 height=11>";
  $btn_next = "<img src='/img/calendar/r.gif' border=0 width=11 height=11>";
  $btn_prev10 = "<img src='/img/calendar/l2.gif' border=0 width=11 height=11>";
  $btn_next10 = "<img src='/img/calendar/r2.gif' border=0 width=11 height=11>";

  $last = ceil($total/$ipp);
  if ($last == 0) $last = 1;

  $start = floor(($page - 1) / 10) * 10 + 1;
  $end = $start + 9;

  //print("$formname / page=$page / total=$total / ipp=$ipp / start=$start / last=$last / end=$end <br>");

  $html =<<<EOS
<div class='pager'>
<table border='0' cellpadding='2' cellspacing='0'>
<tr>
EOS;

  $attr1 = " onmouseover=\"this.className='pager_on'\""
         ." onmouseout=\"this.className='pager_off'\""
         ." class='pager_off' align='center' style='cursor:pointer;'";
  $attr2 = " onmouseover=\"this.className='pager_sel_on'\""
         ." onmouseout=\"this.className='pager_sel_off'\""
         ." class='pager_sel_off' align='center' style='cursor:pointer;'";
 
  # previous link
  if ($start > 1) {
    $prevpage = $start - 1;
    $html .= "<td$attr1 align=center onclick=\"pager_Go('$prevpage')\">$btn_prev10</td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_prev10</td>\n";

  if ($page > 1) {
    $prevpage = $page - 1;
    $html .= "<td$attr1 align=center onclick=\"pager_Go('$prevpage')\">$btn_prev</td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_prev</td>\n";


  if ($end > $last) $end = $last;
  $html .= "</td>";
  for ($i = $start; $i <= $end; $i++) {
    $s = "$i";
    if ($i != $page) {
      $html .= "<td$attr1 onclick=\"pager_Go('$i')\">$s</td>\n";
    } else {
      $html .= "<td$attr2>$s</td>\n";
    }
  }

  # next link
  if ($page < $last) {
    $nextpage = $page + 1;
    $html .= "<td$attr1 align=center onclick=\"pager_Go('$nextpage')\">$btn_next</td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_next</td>\n";

  if ($end < $last) {
    $nextpage = $end + 1;
    $html .= "<td$attr1 align=center onclick=\"pager_Go('$nextpage')\">$btn_next10</td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_next10</td>\n";

  $html .=<<<EOS
</tr>
</table>
</div>
EOS;
  $html .=<<<EOS
<script>
function pager_Go(page) {
  document.$formname.page.value = page;
  document.$formname.submit();
}
</script>
EOS;
  return $html;
}

# 전체 건수를 구한다.
# $qry = "SELECT COUNT(*) AS total".$sql_from.$sql_join.$sql_where;
# $row = db_fetchone($qry);
# $total = $row['total'];
#  $ipp = get_ipp(20,$min=10,$max=500);
#  $opts = option_ipp($ipp, array(10,20,50,200,500));
#  print<<<EOS
#<span class='label'>출력</span><select name='ipp'>$opts</select>/페이지
#EOS;
# $ipp = get_ipp(20,$min=10,$max=500);
# list($start, $last, $page) = calc_page($ipp, $total);
# $qry = "SELECT ...."
#  .$sql_from.$sql_join.$sql_where.$sql_order
#." LIMIT $start,$ipp";
#<form name='search_form'>
#<input type='hidden' name='page' value='{$form['page']}'>
# // 페이지 이동
# $html = pager_general($total, $page, $last, $ipp, $formname='search_form');
# print $html;

// 페이지 이동할 수 있는 컨트롤
//$html = pager_general($total, $page, $last, $ipp, $formname='form');
function pager_general($total, $page, $last, $ipp, $formname='form') {
  
  if (!$total) $tot_s = '0';
  else $tot_s = number_format($total);

  $pager = Pager_f($formname, $page, $total, $ipp);
  $last = number_format($last);

  $html=<<<EOS
<table border='0' cellpadding='3' cellspacing='1'>
<tr><td style="border:3px solid #eeeeee;">
<table border='0' width='600'>
<tr>
<td align='center'>$pager</td>
<td align='center'>전체 {$tot_s}건&nbsp;&nbsp;$page/{$last}페이지</td>
</tr>
</table>
</td></tr></table>
EOS;
  return $html;
}

# URL 형식에 맞는 base64 인코드
function b64encode($str) {
  $data = base64_encode($str);
  // MIME::Base64::URLSafe implementation
  $data = str_replace(array('+','/','='),array('-','_',','),$data);
  //$data = str_replace(array('+','/'),array('-','_'),$data); // Python raise "TypeError: Incorrect padding" if you remove "=" chars when decoding 
  return $data;
}
# URL 형식에 맞는 base64 디코드
function b64decode($data) {
  $data = str_replace(array('-','_',','),array('+','/','='),$data);
  $str = base64_decode($data);
  return $str;
}


# 'yyyy-mm-dd hh:mm:ss' 형식의 datetime 문자열을
# mktime을 이용하여 타임스템프값(초단위)을 구함
function GetTimeStamp($date) {
  $y = substr($date,0,4);
  $m = substr($date,5,2);
  $d = substr($date,8,2);
  $h = substr($date,11,2);
  $n = substr($date,14,2);
  $s = substr($date,17,2);
  $t = mktime($h,$n,$s, $m,$d,$y);
  return $t;
}

function human_time_before($date) {
  if (!$date) return '';
  $now = time();
  $ts = GetTimeStamp($date);
  $diff = $now - $ts;
  return getHumanTime($diff);
}

// 가로 방향으로 버튼을 나열
// usage button_box($btn1, $btn2, $btn3, ....)
function button_box() {
  $len = func_num_args();
  $args = func_get_args();
  $html = "<table border='0' class='noborder' style='display:inline;'><tr>";
  for ($i = 0; $i < $len; $i++) {
    $btn = $args[$i];
    if ($btn == '') continue;
    $html.="<td style='border:0px;'>";
    $html.=$btn;
    $html.="</td>";
  }
  $html.="</tr></table>";
  return $html;
}


function  is_developer() {
  return true;
}

function dd($msg) {
  // 개발자에게만 보임
  if (is_developer()) {
         if (is_string($msg)) print($msg);
    else if (is_array($msg)) { print("<pre>"); print_r($msg); print("</pre>"); }
    else print_r($msg);
  }
}

// 사용법  form value, session value 중에서 선택한다.
// $sday = getvalue($form['sday'], $_SESSION['drpt_sday'], '');
function getvalue($formv, $sessv, $default_value) {
  if ($formv) return $formv;
  else if ($sessv) return $sessv;
  else return $default_value;
}

// 세션 값
function get_session_value($key) {
  return $_SESSION[$key];
}
function set_session_value($key, $value) {
  $_SESSION[$key] = $value;
}

// 페이지 계산
//   list($start, $last, $page) = calc_page($ipp, $total);
function calc_page($ipp, $total) {
  global $form;

  $page = $form['page'];
  if ($page == '') $page = 1;
  $last = ceil($total/$ipp);
  if ($last == 0) $last = 1;
  if ($page > $last) $page = $last;
  $start = ($page-1) * $ipp;

  return array($start, $last, $page);
}
// $page = get_page();
function get_page() {
  global $form;
  $page = $form['page'];
  if ($page == '') $page = 1;
  return $page;
}

# $list = array('=선택=:null','전체요청:all','개명신청:name');
# $html = select_general('rtype', $form['rtype'], 'null', $list);
function select_general($fname, $preset='', $default='', $list=null) {
  global $form;
  if (!$list) $list = array('=선택=:all');
  if ($preset = '') $preset = $default;
  $opt = option_general($list, $preset);
  $attr = " id='$fname' class='selectpicker'";
  $html=<<<EOS
<select name='$fname'$attr>$opt</select>
EOS;
  return $html;
}

// select option
# $list = array('=선택=:null','전체요청:all','개명신청:name');
# $preset = $form['rtype']; if (!$preset) $preset = 'name';
# $opt = option_general($list, $preset);
function option_general($list, $preset) {
  $opts = "";
  $tlist = $vlist = array();
  foreach ($list as $item) {
    list($t, $v) = preg_split("/:/", $item);
    if ($v=='') $v = $t;
    if ($v == 'null') $v = '';
    $tlist[] = $t;
    $vlist[] = $v;
  }
  //dd($tlist); dd($vlist);
  // preset 이 리스트에 없으면 추가
  if (!in_array($preset, $vlist)) { $tlist[] = $preset; $vlist[] = $preset; }
  $len = count($vlist);
  for ($i = 0; $i < $len; $i++) {
    $v = $vlist[$i];
    $t = $tlist[$i];
    if ($v == $preset) $s = ' selected'; else $s = '';
    $opts .= "<option value='$v'$s>$t</option>";
  }
  return $opts;
}

// $cb = checkbox_general('name', $preset, '제목', $onclick='');
function checkbox_general($fname, $preset, $title='', $onclick='') {
  if ($onclick) { $onclick_attr = " onclick=\"$onclick\""; }
  if ($preset) $chk = ' checked'; else $chk = '';
  $html=<<<EOS
<label><input type='checkbox' name='$fname' $chk $onclick_attr>$title</label>
EOS;
  return $html;
}

#   $list = array('chul_attd:1','chul_attd_yyyymmdd:2');
#   $preset = $form['ttype']; if (!$preset) $preset = '1'
#   print radio_list_general('ttype', $list, $preset, $onclick='', $sep='');
function radio_list_general($fname, $list, $preset, $onclick='', $sep='') {
  $html = '';

  $tlist = $vlist = array();
  foreach ($list as $item) {
    list($t, $v) = preg_split("/:/", $item);
    if ($v == '') $v = $t;
    if ($v == 'null') $v = '';
    $tlist[] = $t;
    $vlist[] = $v;
  }

  // preset 이 리스트에 없으면 추가
  if (!in_array($preset, $vlist)) { $tlist[] = $preset; $vlist[] = $preset; }

  $len = count($vlist);
  $a = array();
  for ($i = 0; $i < $len; $i++) {
    $v = $vlist[$i];
    $t = $tlist[$i];

    if ($onclick) { $onclick_attr = " onclick=\"$onclick\""; }
    if ($preset == $v) $sel = ' checked'; else $sel = '';
    $html =<<<EOS
<label class='radiolist'><input type='radio' name='$fname' value='$v'$sel$onclick_attr>$t</label>
EOS;
    $a[] = $html;
  }
  return join($sep, $a);
}

// 페이지당 아이템 수
// $ipp = get_ipp(20,$min=10,$max=500);
//<span class='label'>출력</span><select name='ipp'>$opts</select>명/페이지
function get_ipp($default=20, $min=10, $max=500) {
  global $form;
  $ipp = $form['ipp'];
  if ($ipp == '') $ipp = $default;
  if ($ipp < $min) $ipp = $min;
  else if ($ipp > $max) $ipp = $max;
  return $ipp;
}


//$opts = option_ipp($ipp, array(10,20,50,200,500));
function option_ipp($preset='', $values='') {
  $opts = '';
  if (!$values) $values = array(10,20,50,100,200,500);
  foreach ($values as $v) {
     if ($preset == $v) $sel = ' selected'; else $sel = '';
     $opts .= <<<EOS
<option value='$v'$sel>$v</option>
EOS;
  }
  return $opts;
}

// form['fd01'] ,form['fd02'], .. 값을 검사하여 $fck 를 설정
//  $fck = array(); // field check '' or ' checked'
//   fck_init($fck, $defaults='1,4,5,6');
function fck_init(&$fck, $defaults = '', $max=100) {
  global $form;

  $flag = false;
  for ($i = 1; $i <= $max; $i++) {
    $key = sprintf("fd%02d", $i);
    $v = $form[$key];
    if ($v != '') $flag = true;
  }
  if ($flag == false) {
    $a = preg_split("/,/", $defaults);
    foreach ($a as $idx) {
      $key = sprintf("fd%02d", $idx); // fd01 fd02 fd03 ...
      $form[$key] = 'on';
    }
  }

  $fck = array(); // field check '' or ' checked'
  $flag = false;
  for ($i = 1; $i <= $max; $i++) {
    $key = sprintf("fd%02d", $i); // fd01 fd02 fd03 ...
    $v = $form[$key];
    if ($v != '') $fck[$i] = ' checked';
    else $fck[$i] = '';
    if ($v != '') $flag = true;
  }
}

// hiddenframe($debug, 'hiddenframe', 600, 600);
function hiddenframe($debug, $name='hiddenframe', $w=600, $h=600) {
  if ($debug) {
    print("<iframe name='$name' width='$w' height='$h' style='display:block'></iframe>");
  } else {
    print("<iframe name='$name' width='0' height='0' style='display:none'></iframe>");
  }
}

// $script = "form.search.focus()";
//page_onload($script);
function page_onload($script) {
  print<<<EOS
<script>
function _onload() {
$script;
}
if (window.addEventListener) {
  window.addEventListener("load", _onload, false);
} else if (document.attachEvent) {
  window.attachEvent("onload", _onload);
}
</script>
EOS;
}

// 창크기 조절
// resize_window_onload(500,500);
function resize_window_onload($w=500, $h=500) {
  $script = " window.resizeTo($w, $h);";
  page_onload($script);
}

// $btn = button_general('제목', 0, "onclick()", $style='', $class='');
function button_general($title='', $width=0, $onclick='', $style='', $class='') {
  if ($width) $w = " width='$width'"; else $w = '';
  if ($style) $s = " style='$style'"; else $s = '';
  if ($class) $l = " class='$class'"; else $l = '';
  if ($onclick) $c = " onclick=\"$onclick\"";

  $html =<<<EOS
<input type='button' value='$title'$w$c$s$l>
EOS;
  return $html;
}

// $ti = textinput_general('name', $preset='', $size='10', $onkeypress='', $click_select=true, $maxlength=0, $id='');
function textinput_general($fname, $preset='', $size='10', $onkeypress='', $click_select=true, $maxlength=0, $id='', $class='',$placeholder='') {

  if ($click_select) { $onclick = "this.select()"; }
  if ($maxlength) $ml = " maxlength='$maxlength'";

  $html =<<<EOS
<input type='text' name='$fname' size='$size' id='$id'
 value='$preset' style="IME-MODE:active;" onkeypress='$onkeypress' onclick='$onclick'$ml class='$class' placeholder='$placeholder'>
EOS;
  return $html;
}

function textarea_general($fname, $content='', $cols='40', $rows='5', $click_select=true, $id='') {

  $html =<<<EOS
<textarea name='$fname' cols='$cols' rows=$rows'' id='$id' onclick='$onclick'>$content</textarea>
EOS;
  return $html;
}


// $list = get_checked_list($prefix='cb');
function get_checked_list($prefix='cb', $frm='') {
  global $form;
  if (!$frm) $frm = $form;

  $keys = array_keys($frm);
  $count = 0;
  $info = array();
  for ($i = 0; $i < count($keys); $i++) {
    $key = $keys[$i];
    $val = $form[$key];
    list($a, $b) = explode('_', $key);
    if ($a == $prefix) {
      $count++;
      $info[] = $b;
    }
  }
  return $info;
}

// sql_where_match($w, 'realm', 'table.realm');
function sql_where_match(&$w, $fn, $col) {
  global $form;
  $v = $form[$fn];
  if ($v != '' & $v != 'all') { $w[] = "$col='$v'"; }
}
// sql_where_match($w, 'realm', 'table.col', array(v1,v2,v3));
function sql_where_match_list_or(&$w, $col, $list) {
  $a = array();
  foreach ($list as $v) $a[] = "$col='$v'";
  $x = join(" OR ", $a);
  $w[] = "($x)";
}

// sql_where_like($w, 'search', 'm.Name,m.NewNo');
//if ($v != '') $w[] = "((m.Name LIKE '%$v%') OR (m.NewNo LIKE '%$v%'))";
function sql_where_like(&$w, $fn, $columns) {
  global $form;
  $v = $form[$fn];
  if (!$v) return;
  $cols = preg_split("/,/", $columns);
  $b = array();
  foreach ($cols as $col) {
    $b[] = "($col LIKE '%$v%')";
  }
  $str = join(" OR ", $b);
  $w[] = "($str)";
}
// sql_where_case_by($w, 'alive', 'table.alive', '1:1,2:0');
function sql_where_case_by(&$w, $fn, $col, $cases) {
  global $form;
  $v = $form[$fn];
  if ($v == '') return;

  $cas = preg_split("/,/", $cases);
  foreach ($cas as $case) {
    list($fv, $dv) = preg_split("/:/", $case); // form value, db value
    if ($v == "$fv") $w[] = "$col='$dv'";
  }
}

// $sql_where = sql_where_join($w, $d=0, 'AND');
function sql_where_join(&$w, $debug=0, $operator='AND') {
  if ($debug) dd($w);
  return " WHERE ".join(" $operator ", $w);
}

function btn_style_generator($classname='myButton') {
   // http://www.bestcssbuttongenerator.com/#/lESVHdPQHg
   $html =<<<EOS
.$classname {
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ffffff), color-stop(1, #f6f6f6));
	background:-moz-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:-webkit-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:-o-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:-ms-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:linear-gradient(to bottom, #ffffff 5%, #f6f6f6 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#f6f6f6',GradientType=0);
	background-color:#ffffff;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #dcdcdc;
	display:inline-block;
	cursor:pointer;
	color:#666666;
	font-family:arial;
	font-size:11px;
	font-weight:bold;
	padding:4px 20px;
	text-decoration:none;
	text-shadow:0px 1px 0px #ffffff;
}
.$classname:hover {
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #f6f6f6), color-stop(1, #ffffff));
	background:-moz-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
	background:-webkit-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
	background:-o-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
	background:-ms-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
	background:linear-gradient(to bottom, #f6f6f6 5%, #ffffff 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f6f6f6', endColorstr='#ffffff',GradientType=0);
	background-color:#f6f6f6;
}
.$classname:active {
	position:relative;
	top:1px;
}
EOS;
  return $html;
}

// 국적선택 select + 이미지
// list($html, $script) = nation_select(preset, 'Nat', '', '', false);
function nation_select($preset=410, $fld='Nat', $selfn='', $onchange_callback='', $select_all=false) {
  if (!$selfn) $selfn = "_sel_nation";

  $qry = "SELECT * FROM Nat WHERE inuse=1 ORDER BY nname, nnum";
  $ret = db_query_select($qry);

  $opts = '';
  if ($select_all) $opts .= "<option value='all'>==국가선택==</option>";
  $imgsrc = '';
  $scr = " var nat=[]; ";
  while ($row = db_fetch($ret)) {
    $n = $row['nname'];
    $v = $row['nnum'];
    $ncode3 = $row['ncode3'];
    //dd($ncode3);

    if ($v == $preset) $sel = ' selected'; else $sel = '';
    if ($v == $preset && $imgsrc == '') $imgsrc = "/img/nation/$ncode3.png";

    $opts .= "<option value='$v'$sel>$n</option>";
    $scr.="nat[$v] = '$ncode3';\n";
  }
  $opts_nat = $opts;

  $html =<<<EOS
<table class=noborder border='0' cellpadding='2' cellspacing='2'><tr>
<td><select name='$fld' onchange='$selfn()'>$opts_nat</select></td>
<td><img name='n_img' src='$imgsrc' width=30 height=20/></td>
</tr></table>
EOS;

  if ($onchange_callback) {
    $callback = "$onchange_callback;";
  }

  $script=<<<EOS
<script>
$scr
function $selfn() {
  var idx = form.$fld.selectedIndex;
  var v = form.$fld.options[idx].value;
  var t = form.$fld.options[idx].text;
  var ncode3 = nat[v];
  var img = document.all['n_img'];
  img.src = "/img/nation/" + ncode3 + ".png";
  $callback
}
</script>
EOS;

  return array($html, $script);
}

function nation_show($code) {
  if (!$code) return;
  $qry = "SELECT * FROM Nat WHERE nnum='$code'";
  $row = db_fetchone($qry);
  $ncode3 = $row['ncode3'];
  $imgsrc = "/img/nation/$ncode3.png";
  $nation_flag = "&nbsp;<img src='$imgsrc'>"; # 국기
  $html = button_box($row['nname'], $nation_flag);
  return $html;
}


// 드래그를 이용한 복사 방지
function disallow_drag_text() {
  if (is_developer()) return; // 개발자는 드래그 가능 ^^
  print<<<EOS
<script>
document.onselectstart = nocopy;
document.oncontextmenu = nocopy;
document.ondragstart = nocopy;
function nocopy() { return false; }
</script>
EOS;
}


// row 에 keys 가 모두 있는지 체크함 (내부 로직 체크용도로만 사용)
// $keys = array('Name', 'NewNo', 'BirthDay', 'SorM', 'Sex');
// check_array_keys($row, $keys);
// check_array_keys($row, array('Name','NewNo'));
function check_array_keys(&$row, $keys) {
  foreach ($keys as $k) {
    if (!array_key_exists($k, $row)) die("key '$k' error !!");
  }
}

function message_div($divid='message') {
  $html=<<<EOS
<div id='$divid' style="display:none; border-radius: 10px; background: #cfc; padding: 5px;"></div> 
EOS;
  return $html;
}

function get_now() {
  return date('Y-m-d H:i:s');
}
function get_time() {
  return date('H:i:s');
}

function get_today() {
  return date('Y-m-d');
}
function get_this_year() {
  return date('Y');
}

function iassert($cond, $identifier='') {
  if (!$cond) die("iassert fail. id:$identifier");
}

function get_chrome_version() {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  preg_match( "#Chrome/(.+?)\.#", $ua, $match );
  if (@!$match[0]) return null;
  //print_r($match);
  return @$match[1];
}

// 문자열 길이 체크 str 길이가 min 이상이면 true, 아니면 false 리턴
// if (check_length_min("test", 10)) { ... }
function check_length_min($str, $min=1) {
  $l = strlen($str);
  if ($l >= $min) return true;
  return false;
}


// $form 에서 값을 읽음
// trim=true 이면 앞뒤 공백을 제거
// default 가 주어지면 값이 없을 경우 디폴트 값을 리턴
// die_if_null=true 이면 값이 없을 경우 die
// $name = form_value('name', true, '', true);
function form_value($key, $trim=true, $default='', $die_if_null=false) {
  global $form;
  $value = $form[$key];

  if ($trim) $value = trim($value);

  if ($default) {
    if (!$value) $value = $default;
  }

  if ($die_if_null) {
    if (!$value) die("$key value is null");
  }

  return $value;
}

function encode_array($info) {
  $v = urlencode(base64_encode(serialize($info)));
  return $v;
}
function decode_array($value) {
  $info = unserialize(base64_decode($value));
  return $info;
}

// checkbox 리스트
// $list = array(array('3','장년회'),array('4','부녀회'),array(5,'청년회'),...)
// $chk = array(3,4)
// $sep = "<br>";
// $html = checkbox_list($list, $fname, $chk, $sep);
function checkbox_list($list, $fname, $chk=null, $sep='') {

  if (!$chk) $chk = array();
  $chk = array_map(function($x) { return true; }, array_flip($chk));

  $a = array();
  foreach ($list as $item) {
    list($v, $t) = $item;
    $c = ($chk[$v]) ? ' checked' : '';
    $a[] =<<<EOS
<label><input type="checkbox" name="{$fname}[]" value='$v'$c>$t</label>
EOS;
  }
  $html = join($sep, $a);
  return $html;
}

// change=true 이면 $n 을 call by reference 로 변경시킴
function number_zero(&$n, $change=false) {
  if (!$n) {
    if ($change) $n = '0';
    return '0';
  }
  $r = number_format($n);
  if ($change) $n = $r;
  return $r;
}


// $html = popup_layer($div_id='layer9', $content_id='content9', $content='hello', $close_title='닫기');
// layer_open('layer9');
// layer_close();
function popup_layer($div_id='_layer', $content_id='_content', $content='', $close_title='닫기') {
  $html=<<<EOS
<div class="layer">
<div class="bg"></div>
<div id="$div_id" class="pop-layer">
<div class="pop-container">
<div class="pop-conts">
<div id='$content_id'>$content</div>
</p>
<div class="btn-r">
<a href="#" class="cbtn">$close_title</a>
</div>
</div>
</div>
</div>
</div>
EOS;
  return $html;
}

//print table_head_general(array('번호','차량번호','모델','색상','메모'));
function table_head_general($titles) {
  $html = "<thead><tr>";
  foreach ($titles as $t) {
    $html .= "<th nowrap>$t</th>";
  }
  $html .= "</tr></thead>";
  return $html;
}

// utl/jquery 아래 있는 jquery를 인클루드
// $env['jquery_version'] = '2.0.0'; MainPageHead();
// $jquery = jquery_script($ver='1.8.3'); print $jquery;
function jquery_script($ver='1.8.3') {
  global $env;
  if (@$env['jquery_version']) $ver = $env['jquery_version'];

  $file = "jquery-{$ver}.min.js";
  $url = "/utl/jquery/$file";
  $path = "$env[prefix]/html/utl/jquery/$file";
  if (!file_exists($path)) return '';

  $html = "<script type='text/javascript' src='$url'></script>";
  return $html;
}

function ParagraphTitle($title, $level=0, $tostring=false) {
  $html = '';
  if ($level == 1) {
    $html=<<<EOS
<h4 class="title">$title</h4>
EOS;
  } else { // level 0
    $html=<<<EOS
<h3 class="title">$title</h3>
EOS;
  }
  if ($tostring) { return $html; }
  else { print $html; return null; }
}


function MyButton($width=60, $text='수정', $url='about:blank', $onclick='') {
  $w = $width - 10;
  if ($onclick != '') {
    $onclick_attr = $onclick;
  } else {
    $onclick_attr = "document.location='$url'";
  }

  $html =<<<EOS
<table border='0' cellpadding='0' cellspacing='0' width='$width'>
<tr style='cursor:pointer' _nclick="$onclick_attr">
<td width='5' height='21'><img src='/img/btn/1/btn_l.gif' width='5' height='21'></td>
<td width='$w' height='21' background='/img/btn/1/btn_c.gif'
 align='center' valign='middle' style='cursor:pointer;'
 onclick="$onclick_attr"><div style='margin:4 0 0 0;'>$text</div></td>
<td width='5' height='21'><img src='/img/btn/1/btn_r.gif' width='5' height='21'></td>
</tr>
</table>
EOS;
  return $html;
}

function MyButton2($width=60, $text='', $url='about:blank', $onclick='') {
  $w = $width - 10;
  if ($onclick != '') {
    $onclick_attr = $onclick;
  } else {
    $onclick_attr = "document.location='$url'";
  }

  $html =<<<EOS
<table border='0' cellpadding='0' cellspacing='0' width='$width'>
<tr style='cursor:pointer' _nclick="$onclick_attr">
<td width='5' height='21'><img src='/img/btn/2/btn_l.gif' width='5' height='21'></td>
<td width='$w' height='21' background='/img/btn/2/btn_c.gif'
 align='center' valign='middle' style='cursor:pointer;'
 onclick="$onclick_attr"><div style='margin:4 0 0 0;'>$text</div></td>
<td width='5' height='21'><img src='/img/btn/2/btn_r.gif' width='5' height='21'></td>
</tr>
</table>
EOS;
  return $html;
}

function include_custom_style() {
  // 헤더에 포함되는 스타일 인클루드. 사용법은  member/mpw.php 참고
  if (function_exists('cb_head_style')) {
    $html = cb_head_style();
    print $html;
  }
}

function include_custom_script() {

  // 다른 것보다 jquery를 먼저 include해야 한다.
  print jquery_script();

  // 헤더에 포함되는 스크립트 인클루드. 사용법은  member/mpw.php 참고
  if (function_exists('cb_head_script')) {
    $html = cb_head_script();
    print $html;
  }
}

function MainPageHead($title='', $path='') {
  global $env, $conf;

  $path = $env['prefix']."/html/head.php";
  include_once($path);

  if ($title) $env['PageTitle'] = $title;

  print<<<EOS
<script>
// 윈도우창의 제목을 바꾼다.
parent.top.document.title = "홈 - $title";
</script>
EOS;
}

function MainPageTail($path='') {

  global $env, $conf;

  $path = $env['prefix']."/html/foot.php";

  include_once($path);
}

function PopupPageHead($title='') {
  global $env, $conf;
  //print_r($env);

  $path = $env['prefix']."/html/head2.php";
  include_once($path);
  print<<<EOS
<script>
// 윈도우창의 제목을 바꾼다.
parent.top.document.title = "홈 - $title";
</script>
EOS;

}

function PopupPageTail() {
  global $env, $conf;
  $path = $env['prefix']."/html/foot2.php";
  include_once($path);
  xkey_close_window();
}


// script_google_map();
function script_google_map() {
  global $conf;
  $key = $conf['google_map_key'];
  print<<<EOS
<script src="https://maps.googleapis.com/maps/api/js?key=$key&callback=initMap" defer></script>
EOS;
}
function keycountup($k) {
  $today = get_today();
  if ($k == 1) $col = 'count1';
  else if ($k == 2) $col = 'count2';
  else return;
  $qry = "update mapkeycount set $col=$col+1 where today='$today'";
  db_query($qry);
}

function get_keycount($k) {
  $today = get_today();
  if ($k == 1) $col = 'count1';
  else if ($k == 2) $col = 'count2';
  else return;
  $qry = "select $col c from mapkeycount where today='$today'";
//dd($qry);
  $row = db_fetchone($qry);
//dd($row);
  return $row['c'];
}

// script_daum_map();
function script_daum_map($k=1) {
  global $conf;
  keycountup($k);
       if ($k == 1) $key = $conf['daum_map_key'];
  else if ($k == 2) $key = $conf['daum_map_key2'];
  print<<<EOS
<script type="text/javascript" src="//apis.daum.net/maps/maps3.js?apikey=$key&libraries=services,clusterer"></script>
EOS;
}

function google_select_location_general($mapdiv='map',
  $get_cb='get_position', $set_cb='set_position', $zoomlevel='10') {

  $default_lat = '37.426428259253335';
  $default_lng = '126.98952913284302';

  print<<<EOS
<script>
var map;
var marker1;

function initMap() {
  map = new google.maps.Map(document.getElementById('$mapdiv'), {
    zoom: $zoomlevel,
    center: {lat: $default_lat, lng: $default_lng }
  });
  //console.log(map);

  var  marker_loc = $get_cb();
  //console.log(marker_loc);
  var lat = marker_loc['lat'];
  var lng = marker_loc['lng'];
  if (lat == '' || lng == '') {
    lat = $default_lat;
    lng = $default_lng;
  }

  var latLng = new google.maps.LatLng(lat, lng);
  map.panTo(latLng);

  map.addListener('click', function(e) {
    onclick(e.latLng, map);
  });

  marker1 = new google.maps.Marker({
    position: latLng,
    map: map,
    animation: google.maps.Animation.DROP,
    draggable: false,
  });

}

function onclick(latLng, map) {
  var lat = latLng.lat();
  var lng = latLng.lng();
  console.log(lat);
  console.log(lng);

  var latLng = new google.maps.LatLng(lat, lng);
  marker1.setPosition(latLng);

  $set_cb(lat, lng);
}

</script>
EOS;
  script_google_map();
}

function app_version() {
  $qry = "select * from app_version";
  $ret = db_query_select($qry);

  $a = array();
  while ($row = db_fetch($ret)) {
    $os = $row['phone_os'];
    $a[$os] = array(
      'version_int'=>$row['version_int'],
      'version_str'=>$row['version_str'],
      'protocol_ver'=>$row['protocol_ver'],
    );
    //dd($row);
  }
  return $a;
}

// $formdata = array('mode'=>'log', 'id'=>$id);
//  print _pagination_bootstrap($formdata, $page, $total, $ipp);
function pagination_bootstrap($formdata, $page, $total, $ipp) {
print("deprecated ------------ pagination_bootstrap");

  $html=<<<EOS
<nav>
  <ul class="pagination">
    <li>
      <a href="#" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
EOS;

  $last = ceil($total/$ipp);
  if ($last == 0) $last = 1;

  $start = floor(($page - 1) / 10) * 10 + 1;
  $end = $start + 9;

  if ($end > $last) $end = $last;
  for ($i = $start; $i <= $end; $i++) {

    $formdata['page'] = $i;
    $query = http_build_query($formdata);
    if ($i == $page)
      $html .= "<li class='active'><a href='$env[self]?$query'>$i</a></li>";
    else
      $html .= "<li><a href='$env[self]?$query'>$i</a></li>";
  }

  $html.=<<<EOS
    <li>
      <a href="#" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>
EOS;
  return $html;
}

//  print pagination_bootstrap2($page, $total, $ipp, '_page');
function pagination_bootstrap2($page, $total, $ipp, $callback) {

  $html=<<<EOS
<nav>
<ul class="pagination">
EOS;

  $last = ceil($total/$ipp);
  if ($last == 0) $last = 1;

  $start = floor(($page - 1) / 10) * 10 + 1;
  $end = $start + 9;

  if ($start > 1) {
    $i = $start - 1;
    $html .= "<li><a href=\"javascript:$callback('$i')\" aria-label='Previous'>&laquo;</a></li>";
  }

  if ($end > $last) $end = $last;
  for ($i = $start; $i <= $end; $i++) {

    if ($i == $page)
      $html .= "<li class='active'><a href=\"javascript:$callback('$i')\">$i</a></li>";
    else
      $html .= "<li><a href=\"javascript:$callback('$i')\">$i</a></li>";
  }

  if ($i <= $last) {
    $html .= "<li><a href=\"javascript:$callback('$i')\" aria-label='Next'>&raquo;</a></li>";
  }
  $html.=<<<EOS
<li>
<span aria-hidden="true">Total $total</span>
</li>
</ul>
</nav>
EOS;
  return $html;
}


// 'x' 또는 ESC 를 누르면 팝업창을 닫게 한다.
function xkey_close_window() {
  global $env;

  if (@$env['_xkey_']) return;
  $env['_xkey_'] = true;

  //$script=" if (ch == 'x') { window.close(); return; }";
  //keypresshandler_javascript($script);

  $script = " if (e.keyCode == 27) { window.close(); return; }";
  keyuphandler_javascript($script);
}


function keyuphandler_javascript($script) {
  print<<<EOS
<script>
function keyuphandler(e) {
  $script
}
document.onkeyup = keyuphandler;
</script>
EOS;
}


function get_human_time($s) {
  //$unit = array('D'=>' days','H'=>' hours','M'=>' mins','S'=>' secs');
  $unit = array('D'=>'일','H'=>'시간','M'=>'분','S'=>'초');

  $m = $s / 60;
  $h = $s / 3600;
  $d = $s / 86400;
  if ($m > 1) {
    if ($h > 1) {
      if ($d > 1) {
        return (int)$d.$unit['D'];
      } else {
        return (int)$h.$unit['H'];
      }
    } else {
      return (int)$m.$unit['M'];
    }
  } else {
    return (int)$s.$unit['S'];
  }
}


function mktime_date_string($str) {
  $y = (int)substr($str, 0, 4);
  $m = (int)substr($str, 5, 2);
  $d = (int)substr($str, 8, 2);
  $h = (int)substr($str, 11, 2);
  $i = (int)substr($str, 14, 2);
  $s = (int)substr($str, 17, 2);
  return mktime($h,$i,$s,$m,$d,$y);
}


// 한글 초성 분리
function utf8_strlen($str) { return mb_strlen($str, 'UTF-8'); }
function utf8_charAt($str, $num) { return mb_substr($str, $num, 1, 'UTF-8'); }
function utf8_ord($ch) {
  $len = strlen($ch);
  if($len <= 0) return false;
  $h = ord($ch{0});
  if ($h <= 0x7F) return $h;
  if ($h < 0xC2) return false;
  if ($h <= 0xDF && $len>1) return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
  if ($h <= 0xEF && $len>2) return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);          
  if ($h <= 0xF4 && $len>3) return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);
  return false;
}

function cho_hangul($str) {
  $cho = array("ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");
  $result = "";
  for ($i=0; $i<utf8_strlen($str); $i++) {
    $code = utf8_ord(utf8_charAt($str, $i)) - 44032;
    if ($code > -1 && $code < 11172) {
      $cho_idx = $code / 588;      
      $result .= $cho[$cho_idx];
    }
  }
  //return $result;
  return trim($result);
}

//echo cho_hangul("안녕하세요");


// 한글 영타를 한글로
// http://phpschool.com/gnuboard4/bbs/board.php?bo_table=tipntech&wr_id=27132&sca=&sfl=wr_subject%7C%7Cwr_content&stx=%C3%CA%BC%BA+%C1%DF%BC%BA+%C1%BE%BC%BA&sop=and
function eng2han ($str) {
  static $convTable = null;
  if (is_null($convTable)) {
    // 초성
    $convTable[] = array('r'=>0, 'R'=>1, 's'=>2, 'e'=>3, 'E'=>4, 'f'=>5, 'a'=>6, 'q'=>7, 'Q'=>8, 't'=>9, 'T'=>10, 'd'=>11, 'w'=>12, 'W'=>13, 'c'=>14, 'z'=>15, 'x'=>16, 'v'=>17, 'g'=>18);

    // 중성
    $convTable[] = array('k'=>0, 'o'=>1, 'i'=>2, 'O'=>3, 'j'=>4, 'p'=>5, 'u'=>6, 'P'=>7, 'h'=>8, 'hk'=>9, 'ho'=>10, 'hl'=>11, 'y'=>12, 'n'=>13, 'nj'=>14, 'np'=>15, 'nl'=>16, 'b'=>17, 'm'=> 18, 'ml'=>19, 'l'=>20);

    // 종성
    $convTable[] = array('r'=>1, 'R'=>2, 'rt'=>3, 's'=>4, 'sw'=>5, 'sg'=>6, 'e'=>7, 'f'=>8, 'fr'=>9, 'fa'=>10, 'fq'=>11, 'ft'=>12, 'fx'=>13, 'fv'=>14, 'fg'=>15, 'a'=>16, 'q'=>17, 'qt'=>18, 't'=>19, 'T'=>20, 'd'=>21, 'w'=>22, 'c'=>23, 'z'=>24, 'x'=>25, 'v'=>26, 'g'=>27);
  }

  $retText = array(); $hanChar = '';
  $len = strlen($str);
  for ($idx = 0; $idx < $len; $idx++) {
    if ( is_numeric($convTable[0][$str[$idx]]) ) {
      // 초성
      $hanChar = 0xAC00 + $convTable[0][$str[$idx]]*21*28;
      $idx++;

      // 중성
      if ( $convTable[1][$str[$idx].$str[$idx+1]] ) {
        $hanChar += $convTable[1][$str[$idx].$str[$idx+1]]*28;
        $idx+=2;
      } elseif ( is_numeric($convTable[1][$str[$idx]]) ) {
        $hanChar += $convTable[1][$str[$idx]]*28;
        $idx++;
      }

      // 종성
      if ( $convTable[2][$str[$idx].$str[$idx+1]] && (!is_numeric($convTable[1][$str[$idx+2]]) || $idx+2 >= $len) ) {
        $hanChar += $convTable[2][$str[$idx].$str[$idx+1]];
        $idx++;
      } elseif ( $convTable[2][$str[$idx]] && (!is_numeric($convTable[1][$str[$idx+1]]) || $idx+1 >= $len) ) {
        $hanChar += $convTable[2][$str[$idx]];
      } else {
        $idx--;
      }

      $hanChar = dechex($hanChar);
      $hanChar = iconv("UCS-2", "UTF-8", chr(hexdec(substr($hanChar, 0, 2))).chr(hexdec(substr($hanChar, -2))));

      $retText[] = $hanChar;
    } else {
      $retText[] = $str[$idx];
      continue;
    }
  }

  return implode('', $retText);
}

//테스트
//echo eng2han('dkssudgktpdy?');

function getHumanTime($s) {
  //$unit = array('D'=>' days','H'=>' hours','M'=>' mins','S'=>' secs');
  $unit = array('D'=>'일','H'=>'시간','M'=>'분','S'=>'초');

  $m = $s / 60;
  $h = $s / 3600;
  $d = $s / 86400;
  if ($m > 1) {
    if ($h > 1) {
      if ($d > 1) {
        return (int)$d.$unit['D'];
      } else {
        return (int)$h.$unit['H'];
      }
    } else {
      return (int)$m.$unit['M'];
    }
  } else {
    return (int)$s.$unit['S'];
  }
}

// 알람 메시지 띄우기
// 최대 $limit 개수만
function get_alert_messages($limit=10) {
  $sessrand = $_SESSION['sessrand'];
  //dd($sessrand);
  $qry = "select * from loginsess where sessrand='$sessrand'";
  $row = db_fetchone($qry);
  $laid = $row['last_alert_id'];

  $info = array();
  if (!$laid) {

    $qry = "select max(id) max from alert";
    $row = db_fetchone($qry);
    $max = $row['max'];
    $qry = "update loginsess set last_alert_id='$max' where sessrand='$sessrand'";
    $ret = db_query($qry);

  } else {
    $qry = "select * from alert WHERE id > '$laid' order by idate DESC LIMIT 0,$limit";
    $ret = db_query($qry);
    $max = 0;
    while ($row = db_fetch($ret)) {
      if ($max < $row['id']) $max = $row['id'];
      $info[] = $row;
    }
    $qry = "update loginsess set last_alert_id='$max' where sessrand='$sessrand'";
    $ret = db_query($qry);

  }
  return $info;

}


// 알람 메시지 추가
function alert_log($msg, $grp='') {
  $s = array();
  $s[] = "group1='$grp'";
  $s[] = "message='$msg'";
  $s[] = "idate=now()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO alert".$sql_set;
  $ret = db_query($qry);
}

function record_head($title='', $path='') {
  global $env, $conf;
  $path = "./head.php";
  include_once($path);
  print<<<EOS
<script>
parent.top.document.title = "홈 - $title";
</script>
EOS;
}

function record_tail($path='') {
  global $env, $conf;
  $path = "./foot.php";
  include_once($path);
}


# 한글 자르기
function cut_str($str,$len,$tail="") {
    $checkmb = true;

    preg_match_all('/[\xE0-\xFF][\x80-\xFF]{2}|./', $str, $match);

    $m    = $match[0];

    $slen = strlen($str);  // length of source string
    $tlen = strlen($tail); // length of tail string
    $mlen = count($m); // length of matched characters

    if ($slen <= $len) return $str;
    if (!$checkmb && $mlen <= $len) return $str;

    $ret   = array();
    $count = 0;

    for ($i=0; $i < $len; $i++) {
        $count += ($checkmb && strlen($m[$i]) > 1)?2:1;

        if ($count + $tlen > $len) break;
        $ret[] = $m[$i];
    }

    return join('', $ret).$tail;
}



?>
