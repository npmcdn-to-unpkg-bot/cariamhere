<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.driver.php");
  include_once("$env[prefix]/inc/class.carinfo.php");

  $source_title = '운행기록 데이터';

  $clsdriver= new driver();
  $clscar = new carinfo();


  MainPageHead($source_title);
  ParagraphTitle($source_title);

  ## {{
  $btn = button_general('조회', 0, "sf_1()", $style='width:70px; height:50px;', $class='btn btn-primary');
  print<<<EOS
<form name='search_form' method='get'>
$btn
<input type='hidden' name='mode' value='$mode'>
<input type='hidden' name='page' value='{$form['page']}'>
EOS;

/*
  $driver_id = $form['driver_id']; //  driver_id
  $ti = textinput_general('driver_id', $driver_id, 6, 'keypress_text()', true, 0, '','ui-corner-all','운전자번호');
  print $ti;

  $v = $form['driver_name'];
  $ti = textinput_general('driver_name', $v, 10, 'keypress_text()', true, 0, '','ui-corner-all','운전자이름');
  print $ti;

  $v = $form['person_name'];
  $ti = textinput_general('person_name', $v, 10, 'keypress_text()', true, 0, '','ui-corner-all','VIP인사');
  print $ti;

  $list = array('=선택=:all','기록중:r','종료:d');
  $preset = $form['rs']; if (!$preset) $preset = 'all';
  $opt = option_general($list, $preset);
  print("기록상태:<select name='rs'>$opt</select>");

  $ds = $form['ds'];
  $opt = $clsdriver->driver_status_option($ds);
  print("운전자상태:<select name='ds'>$opt</select>");

  print("<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
*/

  $d1 = $form['date1']; if (!$d1) $d1 = get_now();
  $d2 = $form['date2']; if (!$d2) $d2 = get_now();
  print<<<EOS
기간:
<input type="text" name='date1' class="form-control datetimepicker" style='width:120px; display:inline' value='$d1'>
~
<input type="text" name='date2' class="form-control datetimepicker" style='width:120px; display:inline' value='$d2'>
<script>
$('input.datetimepicker').datetimepicker({
  format: "YYYY-MM-DD"
});
</script>
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

  ## }}

  //dd($form);

  $w = array('1');

  $v = $form['driver_id'];
  if ($v) $w[] = "r.driver_id='$v'";

  $v = $form['driver_name'];
  if ($v) $w[] = "(d.driver_name LIKE '%$v%' OR d.driver_cho LIKE '%$v%')";

  $v = $form['person_name'];
  if ($v) $w[] = "(p.person_name LIKE '%$v%' OR p.person_cho LIKE '%$v%')";

  $v = $form['rs'];
  if ($v && $v != 'all') {
         if ($v == 'r') $w[] = "(r.is_driving=1)";
    else if ($v == 'd') $w[] = "(r.is_driving=0)";
  }

  $ds = $form['ds'];
  if ($ds != '' && $ds != 'all') $w[] = "d.driver_stat='$ds'";

  $d1 = $form['date1']; if ($d1) $w[] = "DATE(l.idate) >= '$d1'";
  $d2 = $form['date2']; if ($d2) $w[] = "DATE(l.idate) <= '$d2'";

  $sql_where = sql_where_join($w, $d=0, 'AND');

  $sql_from = " FROM run_log l";

  $sql_join = '';
  $sql_select = "SELECT * ";

  $sql_order = " ORDER BY l.idate desc";

  $qry = "select count(*) count".$sql_from.$sql_join.$sql_where;
  $row = db_fetchone($qry);
  $total = $row['count'];
  $page = $form['page'];
  $ipp = 30;
  list($start, $last, $page) = calc_page($ipp, $total);
  print pagination_bootstrap2($page, $total, $ipp, '_page');

  $qry = $sql_select.$sql_from.$sql_join.$sql_where.$sql_order
    ." LIMIT $start,$ipp";

  //dd($qry);

  $ret = db_query($qry);

  print("<div class='panel panel-default'>");
  print("<table class='table table-striped'>");

  $head = array();
  $head[] = 'ID';
  $head[] = 'driver';
  $head[] = 'run';
  $head[] = 'lat';
  $head[] = 'lng';
  $head[] = 'idate';
  print table_head_general($head);
  print("<tbody>");

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;

    //dd($row);

    $fields = array();

    $run_id = $row['run_id'];
    $fields[] = $row['id'];
    $fields[] = $row['driver_id'];
    $fields[] = $row['run_id'];
    $fields[] = $row['lat'];
    $fields[] = $row['lng'];
    $fields[] = $row['idate'];

    print("<tr>");
    foreach ($fields as $f) {
      print("<td nowrap>$f</td>");
    }
    print("</tr>");

  }
  print("</tbody>");
  print("</table>");
  print("</div>");
  //dd($a);

  print<<<EOS
<script>
</script>
EOS;

  MainPageTail();
  exit;

?>
