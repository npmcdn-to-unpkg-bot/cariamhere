<?php

  print<<<EOS
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>template</title>

<!--
    <script src="/js/jquery.js"></script>
-->
    <script src="/js/jquery-1.9.1.min.js"></script>

    <link href="/css/bootstrap.css" rel="stylesheet">
    <link href="/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="/css/bootstrap-theme.min.css" rel="stylesheet">
    <script src="/js/bootstrap.min.js"></script>

    <link href="/css/jquery-ui.min.css" rel="stylesheet">
    <script src="/js/jquery-ui.min.js"></script>

    <link href="/css/bootstrap-select.min.css" rel="stylesheet">
    <script src="/js/bootstrap-select.js"></script>

<!--
    <link rel=stylesheet href="/utl/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
    <script src="/utl/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
-->

  <link rel="stylesheet" href="/utl/datetimepicker/css/bootstrap-datetimepicker.min.css" />
  <script type="text/javascript" src="/utl/datetimepicker/js/moment-with-locales.js"></script>
  <script type="text/javascript" src="/utl/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>


    <script src="/js/notify.js"></script>

    <link href="/css/simple-sidebar.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">

    <script src="/js/script.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
EOS;

 print("<li class='sidebar-brand'><a href='/'>Home</a>");
 //print("<a href='/index.php?mode=logout'>로그아웃</a>");
 print("</li>");

 print("<li><a href='/driver.php'>운전자(driver)</a></li>");
 print("<li><a href='/person.php'>의전인사(person)</a></li>");
 print("<li><a href='/car.php'>차량(car)</a></li>");
 print("<li><a href='/location.php'>장소(location)</a></li>");
 print("<li><a href='/notice.php'>공지(notice)</a></li>");
 print("<li><a href='/run.php'>운행기록(run)</a></li>");
 print("<li><a href='/map.php'>실시간 차량위치</a></li>");
 print("<li><a href='/alert.php'>알람(alert)</a></li>");
 print("<li><a href='/download.php'>업로드/다운로드</a></li>");
 print("<li><a href='/index.php?mode=logout'>로그아웃</a></li>");
 print("<li><a href='#'>-----------------</a></li>");

 print("<li><a href='/apidoc.php'>API 설명서</a></li>");
 print("<li><a href='/push_ui.php'>위치입력(개발자용)</a></li>");
 print("<li><a href='/app_version.php'>어플버전(app version)</a></li>");

 print<<<EOS
</ul>
</div>
<!-- /#sidebar-wrapper -->

<div id="page-content-wrapper">
EOS;

  print<<<EOS
<div>
<a href='/driver.php'>운전자</a> ::
<a href='/person.php'>의전인사</a> ::
<a href='/car.php'>차량</a> ::
<a href='/location.php'>장소</a> ::
<a href='/notice.php'>공지</a> ::
<a href='/run.php'>운행기록</a> ::
<a href='/alert.php'>알람</a> ::
<a href='/map.php'>실시간위치</a>
</div>
EOS;

?>

