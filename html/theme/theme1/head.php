<?php

  $url = '';
  $theme = '/theme/theme1';

## {{{

function _left_menu() {

  print<<<EOS
<div id="menu_wrap">

<div id="header">
<!--
<h1><a href="/"><img src="/img/iamhere.jpg" alt="로고" width='100' height='100'/></a></h1>
-->
</div>

<!-- 메뉴 시작 -->
<div id="gnb">
      
<h2 class="blind">메인메뉴</h2>
            
<ul class="menu">
EOS;

  global $env;
  $c = array();

  for ($i = 1; $i < 10; $i++) {
  for ($j = 1; $j < 10; $j++) {
    $key = "$i-$j";
    //if ($env['menu'][$key]) $c[$key] = 'on';
    $c[$key] = 'on';
  }
  }


  print<<<EOS
<li class="depth1 gnb1"><a href="#;" class="menu1">메뉴1</a>
<ul class="depth2">
<li><a href="/carinfo.php" class="{$c['1-1']}">차량(carinfo)</a></li>
<li><a href="/userinfo.php" class="{$c['1-2']}">사용자(user)</a></li>
<li><a href="/personinfo.php" class="{$c['1-3']}">의전대상자(person)</a></li>
<li><a href="/locationinfo.php" class="{$c['1-4']}">장소(location)</a></li>
<li><a href="/noticeinfo.php" class="{$c['1-5']}">공지(notice)</a></li>
<li><a href="/roleinfo.php" class="{$c['1-6']}">역할(role)</a></li>
</ul>
</li>

<li class="depth1 gnb1"><a href="#" class="menu1">메뉴2</a>
<ul class="depth2">
<li><a href="/map_daum.php" class="{$c['2-1']}">실시간 차량위치</a></li>
<li><a href="/push_ui.php" class="{$c['2-2']}">위치입력(개발자용)</a></li>
<li><a href="/dbdoc.php" class="{$c['2-3']}">DB 테이블</a></li>
<li><a href="/apidoc.php" class="{$c['2-4']}">API 설명서</a></li>
<li><a href="/app_version.php" class="{$c['2-5']}">버전</a></li>
</ul>
</li>

<!--
<li class="depth1 gnb3"><a href="#" class="menu1">공지&뉴스</a>
<ul class="depth2">
<li><a href="$url/board/lst.asp?BrdField=BC001" class="">공지사항</a></li>
<li><a href="$url/board/lst.asp?BrdField=BC002" class="">새소식</a></li>
<li><a href="$url/board/lst.asp?BrdField=BC003" class="">뉴스레터</a></li>
<li><a href="$url/board/lst.asp?BrdField=BC004" class="">Q&A</a></li>
<li><a href="$url/board/calendar.asp" class="">사업추진일정표</a></li>
</ul>
</li>
                
<li class="depth1 gnb4"><a href="#" class="menu1">자료모음</a>
<ul class="depth2">
<li><a href="$url/board/lst.asp?BrdField=BC005" class="">R&D자료실</a></li>
<li><a href="$url/board/lst.asp?BrdField=BC006" class="">규정자료실</a></li>
<li><a href="$url/board/lst.asp?BrdField=BC007" class="">서식자료실</a></li>
<li><a href="$url/board/lst.asp?BrdField=BC008" class="">연구관리 Q&A</a></li>
</ul>
</li>
                
<li class="depth1 gnb5"><a href="#" class="menu1">사업단소개</a>
<ul class="depth2">
<li><a href="$url/sub/intro/intro_1.asp" class="">인사말씀</a></li>
</ul>
</li>
                
<li class="depth1 gnb6"><a href="#" class="menu1">회원안내</a>
<ul class="depth2">
<li><a href="$url/member/login.asp" class="">로그인</a></li>
<li><a href="$url/member/join_1.asp" class="">회원가입</a></li>
<li><a href="$url/member/idpw.asp" class="">아이디/비밀번호찾기</a></li>
</ul>
</li>
-->
        
</ul>
      
<!-- 메뉴 마지막 라인 -->
<div class="end_line"></div>
</div>
<!-- 메뉴 끝 -->


<!-- 이용안내 시작 -->
<div id="guide_wrap">
<ul class="step1">
<li><a href="$url/" class="f12" >HOME</a></li>
</ul>
    
<ul class="step2">
<li><a href="/index.php?mode=logout" class="f12">로그아웃</a></li>
</ul>
</div>

<div align="center" style="line-height:30px;">

</div>
<!-- 이용안내 끝 -->
EOS;

  print<<<EOS
<!-- 하단 공백 -->
<div style="clear:both; height:100px;"></div>
<!-- 하단 공백 -->
</div>
EOS;

}


/*
function _quick_menu() {
  global $theme;

  print<<<EOS
        <!--  퀵 메뉴 시작 -->
    
<div id="divMenu_quick"  style="position:absolute; left:1103px; width:92px; z-index:1">

<div class="quick_title"><img src="$theme/images/include/tit_quick.gif" alt="Quick Link" /></div>
    <ul>
        <li><a href="$url/board/lst.asp?BrdField=BC006"><img src="$theme/images/include/q_1.png" alt="규정 및 서식" /><p><font color="#4173C8">규정 및 서식</font></p></a></li>
        <li><a href="$url/board/lst.asp?BrdField=BC005"><img src="$theme/images/include/q_2.png" alt="자료실" /><p><font color="#4173C8">자료실</font></p></a></li>
        <li><a href="$url/board/calendar.asp"><img src="$theme/images/include/q_3.png" alt="사업일정표" /><p><font color="#4173C8">사업일정표</font></p></a></li>
        <li><a href="$url/board/lst.asp?BrdField=BC004"><img src="$theme/images/include/q_4.png" alt="묻고 답하기" /><p><font color="#4173C8">묻고 답하기</font></p></a></li>
    </ul>

</div>



<!--스크롤 시작-->
<script type="text/javascript">
    var slidemenu_X = 5; //상단 제한 값
    var slidemenu_Y = 400; //하단 제한 값
    var isDOM = (document.getElementById ? true : false);
    var isIE4 = ((document.all && !isDOM) ? true : false);
    var isNS4 = (document.layers ? true : false);
    var isNS = navigator.appName == "Netscape";
 
 
    function getRef(id) {
      if (isDOM) return document.getElementById(id);
      if (isIE4) return document.all[id];
      if (isNS4) return document.layers[id];
    }
 
    function getSty(id) {
      x = getRef(id);
      return (isNS4 ? getRef(id) : getRef(id).style);
    }
 
    function moveRightEdge() {
      var yMenuFrom, yMenuTo, yOffset, timeoutNextCheck;
 
      if (isNS4) {
        yMenuFrom   = document.getElementById('divMenu_quick').style.top;
        yMenuTo     = windows.pageYOffset + slidemenu_X;   // 위쪽 위치
      } else if (isDOM) {
        yMenuFrom   = parseInt (document.getElementById('divMenu_quick').style.top, 10);
        yMenuTo     = (isNS ? window.pageYOffset : document.documentElement.scrollTop) + slidemenu_X; // 위쪽 위치
      }
      timeoutNextCheck = 30;
      
      divMenu_quick_H = document.getElementById('divMenu_quick');
      limit_H = (parseInt(document.documentElement.scrollHeight)-slidemenu_Y)-parseInt(divMenu_quick_H.offsetHeight);
      divMenu_quick_t = parseInt(document.getElementById('divMenu_quick').style.top) ;
      if (yMenuFrom != yMenuTo) {
        yOffset = Math.ceil(Math.abs(yMenuTo - yMenuFrom) / 20);
        if (yMenuTo < yMenuFrom){
          yOffset = -yOffset;
        }
        if (isNS4){
          if(yOffset > 0){
            if ( divMenu_quick_t < limit_H) {
              document.getElementById('divMenu_quick').style.top += yOffset+"px";
            }
          }else{
            document.getElementById('divMenu_quick').style.top += yOffset+"px";
          }
          
        }else if (isDOM){
          if(yOffset > 0){
            if ( divMenu_quick_t < limit_H) {
              document.getElementById('divMenu_quick').style.top = parseInt (document.getElementById('divMenu_quick').style.top) + yOffset+"px";
            }
          }else{
            document.getElementById('divMenu_quick').style.top = parseInt (document.getElementById('divMenu_quick').style.top) + yOffset+"px";
          }
        }
        timeoutNextCheck = 10;
      }
 
      setTimeout ("moveRightEdge()", timeoutNextCheck);
    }
 
 
    if (isNS4) {    
      var divMenu_quick = document["divMenu_quick"];
      document.getElementById('divMenu_quick').style.top = slidemenu_X+"px";
      document.getElementById('divMenu_quick').style.visibility = "visible";
      moveRightEdge();
    } else if (isDOM) {
      var divMenu_quick = getRef('divMenu_quick');    
      document.getElementById('divMenu_quick').style.top = slidemenu_X+"px";    
      document.getElementById('divMenu_quick').style.visibility = "visible";    
      moveRightEdge();
    }
    </script>
<!--스크롤 끝-->
        <!--  퀵 메뉴 끝 -->
EOS;
}
*/

function _footer() {
  global $theme;
  print<<<EOS
<div id="footer_new">
<div id="f_wrap_new">

<div id="f_left_new" style="position:fixed; left:0px; top:0px; width:100%; "></div>

 <div id="f_left_new" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="107" align="left" bgcolor="#555555" scope="col">

<div class="logo">
<a href="$url/"><img src="$theme/images/include/footer_logo.png" alt="기가코리아 로고" /></a>
</div>
          
          <ul class="link">
      <li><a href="$url/member/privacy.asp"><font color="#81B8D5"><strong>개인정보처리방침</strong></font></a><img src="$theme/images/include/footerbar.png" style="position:relative;top:3px;" alt="" /></li>
      <li><a href="$url/member/agree.asp">서비스이용약관</a><img src="$theme/images/include/footerbar.png" style="position:relative;top:3px;" alt="" /></li>
      <li><a href="javascript:alert('준비중 입니다.');">이메일무단수집거부</a><img src="$theme/images/include/footerbar.png" style="position:relative;top:3px;" alt="" /></li>
            <li><a href="$url/member/site_map.asp">사이트맵</a><img src="$theme/images/include/footerbar.png" style="position:relative;top:3px;" alt="" /></li>
            <li><a href="http://www.juso.go.kr/openIndexPage.do" target="_blank">도로명주소안내</a></li>
     </ul>
         
<div class="map">
<address style="color:#BABABA;font-size:12px;  line-height:23px;">
대전광역시 주소<br />
</address>
<p style="color:#BABABA">Copyright(c)2013 Giga KOREA Foundation.ALL RIGHT RESERVED.</p>
</div>

</td>
</tr>
</table>
</div>

</div>
</div>
EOS;
}


## }}}


  print<<<EOS
<html lang="ko">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<title>sample</title>
EOS;

  print("<script  type='text/javascript' src='$theme/js/jquery-1.9.1.min.js'></script>");

  print('<link href="/css/bootstrap.min.css" rel="stylesheet">');
  print('<link href="/css/bootstrap-theme.min.css" rel="stylesheet">');
  print('<link href="/css/bootstrap-theme.min.css" rel="stylesheet">');
  print('<script src="/js/bootstrap.min.js"></script>');

  print('<link href="/css/jquery-ui.min.css" rel="stylesheet">');
  print('<script src="/js/jquery-ui.min.js"></script>');

  print('<link href="/css/bootstrap-select.min.css" rel="stylesheet">');
  print('<script src="/js/bootstrap-select.js"></script>');

  print('<link href="/css/simple-sidebar.css" rel="stylesheet">');

  print('<script src="/js/script.js"></script>');

  print("<link rel='stylesheet' href='$theme/css/style.css' type='text/css' />");

  //print('<script type="text/javascript" src="/theme/giga/js/script.js"></script>');


  print<<<EOS
</head>
<body>

<div id="back_wrap">

<div id="sub_contents_wrap">
    
<div id="top_blue"></div>
EOS;

  _left_menu();

  //_quick_menu();

  ## {{
  print<<<EOS
<div id="sub_wrap">
<div id="sub_cont">

<div class="titWrap_0">
<h2 id='PageTitle'></h2>
</div>

<div id="sub_back">
EOS;

?>
