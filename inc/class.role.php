<?php

class role {
  var $debug = false;

function role() {

}


function select_option_role($preset='') {
  $opt = '';
  $list = $this->list_role();

  $flag = false;
  foreach ($list as $item) {
    $role = $item['role'];
    $title = sprintf("%s(%s)", $item['title'], $item['role']);
    if ($preset == $role) {
      $sel = ' selected';
      $flag = true;
    } else $sel = '';
    $opt .= "<option value='$role'$sel>$title</option>";
  }
  if (!$flag) {
    $opt .= "<option value='$preset' selected>$preset</option>";
  }
  return $opt;
}


function list_role() {
  $qry = "SELECT * FROM role";
  $ret = db_query($qry);
  $info = array();
  while ($row = db_fetch($ret)) {
    $info[] = array(
      'role'=>$row['role'],
      'title'=>$row['role_title'],
    );
  }
  return $info;
}

// $title = $roleObj->query_role_title($role);
function query_role_title($role) {
  $qry = "SELECT * FROM role where role='$role'";
  $row = db_fetchone($qry);
  return $row['role_title'];
}


};

?>
