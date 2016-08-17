<?php

  $path = "/utl/jquery-mobile/demos";
  $now = date('Y-m-d H:i:s');

function _head() {
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
	<link rel="stylesheet" href="$path/_assets/css/jqm-demos.css">
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

		<h1>공지사항</h1>
		<p><strong>공지사항이 여기에 표시됩니다.</strong></p>
EOS;
}


function _tail() {
  global $path, $now;
  print<<<EOS
	</div><!-- /content -->

	    <div data-role="panel" class="jqm-navmenu-panel" data-position="left" data-display="overlay" data-theme="a">
	    	<ul class="jqm-list ui-alt-icon ui-nodisc-icon">
<li data-icon="home"><a href=".././">Home</a></li>

<li><a href="../intro/" data-ajax="false">Introduction</a></li>

<li><a href="../button-markup/" data-ajax="false">Buttons</a></li>

<li><a href="../button/" data-ajax="false">Button widget</a></li>

		     </ul>
		</div><!-- /panel -->


	<div data-role="footer" data-position="fixed" data-tap-toggle="false" class="jqm-footer">
		<p>$now</p>
	</div><!-- /footer -->


</div><!-- /page -->

</body>
</html>
EOS;
}



 _head();
  print<<<EOS
<div class="ui-grid-a ui-responsive">
<div class="ui-block">
<div class="jqm-block-content">
<h3>제목..</h3>
<p>내용 ..........</p>
</div>
</div>
</div>
EOS;
 _tail();


?>
