<?php

class notice {
  var $debug = false;

function notice() {

}


function select_option_notice($preset='') {
  $opt = '';
  $list = $this->list_notice();

  $flag = false;
  foreach ($list as $item) {
    $notice = $item['notice'];
    $title = $item['title'];
    if ($preset == $notice) {
      $sel = ' selected';
      $flag = true;
    } else $sel = '';
    $opt .= "<option value='$notice'$sel>$title</option>";
  }
  if (!$flag) {
    $opt .= "<option value='$preset' selected>$preset</option>";
  }
  return $opt;
}


function get_notice() {

  //if (!$role) $role = $user_role;

  $qry = "SELECT * FROM notice WHERE 1 ORDER BY idate DESC";
  $ret = db_query($qry);

  $info = array();
  while ($row = db_fetch($ret)) {
    $info[] = array(
      'notice_id'=>$row['id'],
      'title'=>$row['title'],
      'content'=>$row['content'],
      'idate'=>$row['idate'],
    );
  }
  return $info;
}


};

?>
