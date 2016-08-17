<?php

// 공지사항 확인

  include("../path.php");
  include("$env[prefix]/inc/common.login.php");
  include("$env[prefix]/inc/classes.php");

  $path = "/utl/jquery-mobile/demos";
  $now = date('Y-m-d H:i:s');

### {{{
function _head($title='') {
  global $path, $now;
  print<<<EOS
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>jQuery Mobile Demos</title>
	<link rel="shortcut icon" href="favicon.ico">
	<link rel="stylesheet" href="$path/css/themes/default/jquery.mobile-1.4.5.min.css">
	<link rel="stylesheet" href="./jqm-demos.css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
	<script src="$path/js/jquery.js"></script>
	<script src="$path/js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
<div data-role="page" class="jqm-demos jqm-home">

	<div data-role="header" class="jqm-header">
		<h2>공지사항</h2>
		<p>현재시간 $now</p>
		<a href="#" class="jqm-navmenu-link ui-btn ui-btn-icon-notext ui-corner-all ui-icon-bars ui-nodisc-icon ui-alt-icon ui-btn-left">Menu</a>
	</div><!-- /header -->

	<div role="main" class="ui-content jqm-content">

<h1>$title</h1>
EOS;
}


function _tail() {
  global $path, $now;
  print<<<EOS
	</div><!-- /content -->

<!--
	    <div data-role="panel" class="jqm-navmenu-panel" data-position="left" data-display="overlay" data-theme="a">
	    	<ul class="jqm-list ui-alt-icon ui-nodisc-icon">
<li data-icon="home"><a href=".././">Home</a></li>

<li><a href="../intro/" data-ajax="false">Introduction</a></li>

<li><a href="../button-markup/" data-ajax="false">Buttons</a></li>

<li><a href="../button/" data-ajax="false">Button widget</a></li>

		     </ul>
		</div>
-->


	<div data-role="footer" data-position="fixed" data-tap-toggle="false" class="jqm-footer">
		<p>$now</p>
	</div><!-- /footer -->


</div><!-- /page -->

</body>
</html>
EOS;
}


function _item($title, $content, $date) {
  print<<<EOS
<div class="ui-corner-all custom-corners">
  <div class="ui-bar ui-bar-a">
    <h3>$title</h3>
  </div>
  <div class="ui-body ui-body-a">
    <p>$content</p>
    <p>$date</p>
  </div>
</div>
EOS;
}

### }}}


  $appkey = $form['appkey'];
  $user = $userObj->get_user_by_appkey($appkey);
  if (!$user) die('invalid appkey');
  //dd($user);

  $role = $user['role'];

  $qry = "SELECT * FROM notice WHERE role='$role' ORDER BY idate DESC";
  $ret = db_query($qry);

  $role_title = $roleObj->query_role_title($user['role']);

  _head('공지');

  //print("<p>팀: $role_title");
  print("<p>사용자: {$user['user_name']}");
  print("<p>상태: {$user['DsName']}");

  while ($row = db_fetch($ret)) {
    //dd($row);

    $title = $row['title'];
    $idate = $row['idate'];

    $content =<<<EOS
{$row['content']}
EOS;
    _item($title, $content, $idate);

  }

  _tail();

  exit;

?>
