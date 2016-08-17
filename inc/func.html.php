<?php

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
  global $conf, $env;
  @$urlprefix = $conf['urlprefix'];
  $html = '';
  if ($level == 1) {
    $html=<<<EOS
<div style="padding:10 0 5 5px;" class="print_off">
<table border='0'><tr>
  <td><img src='$urlprefix/img/menu/lv2.gif' width=3 height=6></td>
<td><strong>$title</strong></td>
</tr></table>
</div>
EOS;
  } else { // level 0
    $html=<<<EOS
<div style="padding:10 0 5 5px;" class="print_off">    
<table border='0'><tr>
<td><img src='$urlprefix/img/menu/lv4.gif' width=11 height=11></td>
<td><strong>$title</strong></td>
</tr></table>
</div>
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

function MainPageHead($title='') {
  global $env, $conf;

  $pagewidth = '970';
  $env['pagewidth'] = $pagewidth;
  $menuwidth = 155;
  if ($env['no_sidemenu']) $menuwidth = 0;
  $bodywidth = '100%';

  print<<<EOS
<html>
<head>
<title>$title</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">

<link rel='stylesheet' type='text/css' href='/css/style.css'>
EOS;
  include_custom_style();
  include_custom_script();
  print<<<EOS
<script type="text/javascript" src="/js/script.js" charset='utf-8'></script>
<script type="text/javascript" src="/js/menu.js" charset='utf-8'></script>
EOS;

  print<<<EOS
<script>
// 윈도우창의 제목을 바꾼다.
parent.top.document.title = "홈 - $title";
</script>
EOS;

  print<<<EOS
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
EOS;

  if (function_exists('_style')) _style();

  flush(); ob_flush();
}

function MainPageTail() {
  print<<<EOS
</body>
</html>
EOS;
}

function PopupPageHead($title, $bgimg='/img/bg/popuphead.gif') {

  print<<<EOS
<html>
<head>
<title>$title</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<link rel=stylesheet type=text/css href=/css/style.css>
EOS;
  include_custom_style();
  print<<<EOS
<script type="text/javascript" src="/js/script.js" charset='utf-8'></script>
<script type="text/javascript" src="/js/menu.js" charset='utf-8'></script>
EOS;
  include_custom_script();

  print<<<EOS
</head>

<body topmargin='0' leftmargin='0' marginwidth='0' marginheight='0'>
<style>
td.popuphead {
 background-color:#bbbbbb; text-align:left; height:40;
 color:#ffffff; font-size:18; font-family:돋움,굴림; font-weight:bold;
 background:url($bgimg);
}
a.close:link    { color:#ffffff; text-decoration:none; }
a.close:visited { color:#ffffff; text-decoration:none; }
</style>
<table border='0' width='100%' cellpadding='0' cellspacing='0' _height='100%'>
<tr>
<td class='popuphead' colspan='3'>
<table border='0' width='100%' cellpadding='0' cellspacing='0'>
<tr>
<td>

<table border='0' width='100%' cellpadding='0' cellspacing='0'>
<tr>
<td width='100%' class='popuphead' style='padding-left:20px;'>:: $title ::</td>
<td align='right' nowrap>
EOS;
  print<<<EOS
<span onclick="document.location.reload()" style='cursor:pointer'><img src='/img/btn/reload/4.gif' alt='다시읽기'></span>
<span onclick="window.close()" style='cursor:pointer'><img src='/img/btn/close/4.gif' alt='창닫기'></span>
</td>
<td align='right'>&nbsp;&nbsp;&nbsp;</td>
</tr></table>

</td>
</tr>
</table>
</td>
</tr>
<tr>
<td height='100%' width='100%' valign='top'>
<table border='0' cellpadding='0' cellspacing='5'><tr><td>
EOS;

  set_menu_set();

  if (function_exists('_style')) _style();

  flush(); ob_flush();
}


function PopupPageTail() {

  print<<<EOS
</td></tr></table>
</td>
</tr>
</table>
<div style="text-align:center; color:#eee;">
ESC를 눌러 창을 닫을수 있습니다.
</div>
EOS;

  print<<<EOS
<script> page_loading_done(); </script>
</body>
</html>
EOS;
}

# TabView 를 출력한다.
# widthall: 전체 폭
# info = array(
#   array(title, width, url, on),
#   ...
# )
/* example
  $info = array(
    array('등록구분변경',  120, "$env[self]?mode=regs",    false),
    array('지파/교회이동', 120, "$env[self]?mode=church",  false),
    array('부서이동',      120, "$env[self]?mode=dept",    false),
    array('직분신청',      120, "$env[self]?mode=jikbun",  false),
    array('직책신청',      120, "$env[self]?mode=jikchek", false),
    array('상벌신청',      120, "$env[self]?mode=pp",      false),
  );
       if ($mode == 'regs')    $info[0][3] = true;
  else if ($mode == 'church')  $info[1][3] = true;
  else if ($mode == 'dept')    $info[2][3] = true;
  else if ($mode == 'jikbun')  $info[3][3] = true;
  else if ($mode == 'jikchek') $info[4][3] = true;
  else if ($mode == 'pp')      $info[5][3] = true;

  $html = MyTabView(700, $info);
  print $html;
*/
function MyTabView($widthall=600,$info=null) {
  if (!$info) {
    $info = array(array('텝제목', 90, '', true)); // title, width, url, on
    // width는 90, 120, 150 중 하나 
  }

  $html=<<<EOS
<table border='0' cellpadding='0' cellspacing='0'>
<tr>
<td width='10' background='/img/tab/1/bg.gif' style='padding-top:3px;'>
EOS;

  $sum_width = 0;
  $n = count($info);
  for ($i = 0; $i < $n; $i++) {
    list($title, $width, $url, $on) = $info[$i];

    if ($width == 90) {
      if ($on) $img = "/img/tab/1/on90.gif";
      else     $img = "/img/tab/1/off90.gif";
    } else if ($width == 120) {
      if ($on) $img = "/img/tab/1/on120.gif";
      else     $img = "/img/tab/1/off120.gif";
    } else if ($width == 150) {
      if ($on) $img = "/img/tab/1/on150.gif";
      else     $img = "/img/tab/1/off150.gif";
    } else {
      if ($on) $img = "/img/tab/1/on90.gif";
      else     $img = "/img/tab/1/off90.gif";
    }
      $w = $width;

    $sum_width += $w+ 1;
    $html.=<<<EOS
<td width='3' background='/img/tab/1/bg.gif' style='padding-top:3px;'>
<td width='$w' height='29' align='center'
 style='font-weight:bold;padding-top:3px; background:url($img)'><a href='$url'>$title</a></td>
EOS;
  }

  $w = $widthall - $sum_width;
  $html.=<<<EOS
<td width='$w' background='/img/tab/1/bg.gif' style='padding-top:3px;'></td>
</tr>
</table>
EOS;
  return $html;
}

?>
