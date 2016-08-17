<?php

  print<<<EOS
<html lang="ko">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<title>I am Here</title>
EOS;

  //print('<script src="/js/jquery.js"></script>');
  print('<script  type="text/javascript" src="/theme/giga/js/jquery-1.9.1.min.js"></script>');

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

  print('<link rel="stylesheet" href="/theme/giga/css/style.css" type="text/css" />');
  print('<script  type="text/javascript" src="/theme/giga/js/script.js"></script><!--  기존 롤링배너 위해 사용  -->');

 //print('<!--  회원정보찾기(div불러오기) 위해 사용  -->');
 //print('<script  type="text/javascript" src="/theme/giga/js/yetii.js"></script> ');

  print<<<EOS
</head>
<body>
EOS;

/*
  print<<<EOS
<div id="back_wrap">
<div id="sub_contents_wrap">
EOS;
*/

  print<<<EOS
<!--  상단 블루바 -->
<div id="top_blue"></div>

<!--  메뉴시작 {{{ -->
<div id="menu_wrap">

<div id="header">
<h1><a href="/"><img src="/img/iamhere.jpg" alt="로고" width='100' height='100'/></a></h1>
</div>

<!-- 메뉴 시작 {{{ -->
<div id="gnb">
      
      <h2 class="blind">메인메뉴</h2>
            
            <ul class="menu">
              <li class="depth1 gnb1"><a href="#;" class="menu1">메뉴</a>
          <ul class="depth2">
            <li><a href="#" class=" on">개요</a></li><!--현재페이지표시-->
            <li><a href="#" class="">전략</a></li>
            <li><a href="#" class="">전략</a></li>
          </ul>
        </li>
                
        <li class="depth1 gnb2"><a href="#" class="menu1">사업내용</a>
          <ul class="depth2">
            <li><a href="#" class="">구성</a></li>
            <li><a href="#" class="">구성</a></li>
            <li><a href="#" class="">구성</a></li>
          </ul>
        </li>
                
        <li class="depth1 gnb3"><a href="#" class="menu1">공지&뉴스</a>
          <ul class="depth2">
            <li><a href="#" class="">공지사항</a></li>
            <li><a href="#" class="">새소식</a></li>
            <li><a href="#" class="">뉴스레터</a></li>
            <li><a href="#" class="">Q&A</a></li>
            <li><a href="#" class="">일정표</a></li>
          </ul>
        </li>
                
        <li class="depth1 gnb4"><a href="#" class="menu1">자료모음</a>
          <ul class="depth2">
            <li><a href="#" class="">자료실</a></li>
            <li><a href="#" class="">자료실</a></li>
            <li><a href="#" class="">자료실</a></li>
          </ul>
        </li>
                
      </ul>
      
      <!-- 메뉴 마지막 라인 -->
      <div class="end_line"></div>
</div>
<!-- 메뉴 끝 }}} -->
EOS;

  print<<<EOS
<!-- 이용안내 시작 -->
<div id="guide_wrap">
  <ul class="step1">
        <li><a href="#" class="f12" >HOME</a></li>
        <li><a href="#" class="f12" >로그인</a></li>
        <li><a href="#" class="f12" >사이트맵</a></li>
    </ul>
    
     <ul class="step2">
        <li><a href="#" class="f12">ENGLISH</a></li>
        <li><a href="#" class="f12" >회원가입</a></li>
        <li><a href="#" class="f12" >오시는길</a></li>
    </ul>
</div>
<!-- 이용안내 끝 -->

<!-- 하단 공백 -->
<div style="clear:both; height:200px;"></div>

</div>
<!-- 메뉴 끝 }}} -->
EOS;

  print<<<EOS
<div id="sub_wrap">
<div id="sub_cont">
EOS;

?>
