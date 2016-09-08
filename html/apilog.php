<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.driver.php");
  include_once("$env[prefix]/inc/class.carinfo.php");
  include_once("$env[prefix]/inc/class.location.php");

  $source_title = 'apilog';

  $sql_from = " FROM apilog l";

### {{{

### }}}

  MainPageHead($source_title);

  $sql_select = "SELECT * ";

  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='width:70px;height:50px;', $class='btn btn-primary');
  print<<<EOS
<table border='0' style='margin-top:10px;'>
<form name='search_form' method='get'>
<tr>
<td>$btn</td>
<td align='left'>
<input type='hidden' name='mode' value='$mode'>
<input type='hidden' name='page' value='{$form['page']}'>
<input type='hidden' name='smtm' value='0'>
EOS;

  $ipp = get_ipp(20,$min=10,$max=500);
  $opts = option_ipp($ipp, array(10,20,50,200,500));
  print("출력:<select name='ipp'>$opts</select>");

  print("</td>");
  print("</tr>");
  print("</form>");
  print("</table>");
  //dd($form);

  print<<<EOS
<script>
</script>
EOS;

  print<<<EOS
<script>
function sf_0() {
  document.search_form.submit();
}
function sf_1() {
  document.search_form.page.value = '1';
  document.search_form.smtm.value = '';
  sf_0();
}

function _page(page) { document.search_form.page.value = page; sf_0(); }
function keypress_text() { if (event.keyCode != 13) return; sf_0(); }
</script>
EOS;

  ## }}


  $qry = "select count(*) count".$sql_from;
  $row = db_fetchone($qry);
  $total = $row['count'];
  $page = $form['page'];
  $ipp = get_ipp(20,$min=10,$max=500);
  list($start, $last, $page) = calc_page($ipp, $total);
  print pagination_bootstrap2($page, $total, $ipp, '_page');

  $sql_order = " ORDER BY l.idate DESC";

  $qry = $sql_select.$sql_from.$sql_order
    ." LIMIT $start,$ipp";
  $ret = db_query($qry);

  ## {{
  print("<div class='panel panel-default'>");
  ## {{
  print("<table class='table table-striped dataC' id='resultTable'>");

  $head = array();
  $head[] = 'ID';
  $head[] = 'log';
  $head[] = '시간';
  print table_head_general($head);
  print("<tbody>");

  $cnt = 0;
  $info = array();
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $fields = array();
    $fields[] = $row['id'];
    $fields[] = $row['log'];
    $fields[] = $row['idate'];

    print("<tr>");
    foreach ($fields as $f) {
      print("<td class='l'>$f</td>");
    }
    print("</tr>");

  }
  print("</tbody>");
  print("</table>");
  ## }}
  print("</div>");
  ## }}

  MainPageTail();
  exit;

?>
