<?php

// 백그라운드 서비스 처리

  include_once("../path.php");
  include_once("$env[prefix]/inc/common.background.php");

### {{{
function driving_drivers(&$info) {
  $qry = "SELECT id, run_id FROM driver WHERE is_driving=1";
  $ret = db_query($qry);

  $info = array();
  while ($row = db_fetch($ret)) {
    $id = $row['id'];
    $run_id = $row['run_id'];
    $info[] = array($id, $run_id);
  }
}

function alert_log($msg, $grp='') {

  $s = array();
  $s[] = "group1='$grp'";
  $s[] = "message='$msg'";
  $s[] = "idate=now()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO alert".$sql_set;
  $ret = db_query($qry);
}
### }}}

  alert_log("테스트입니다.");
  
  driving_drivers($info);
  dd($info);

  exit;

?>
