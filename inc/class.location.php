<?php

class location {
  var $debug = false;

function location() {

}

function option_location_group($preset='') {

  $a = array('=선택=:null');
  $b = $this->location_groups();
  $list = array_merge($a, $b);

  $opt = option_general($list, $preset);
  return $opt;
}
function location_groups() {
  return array('공항','숙소','행사장','기타','경유지');
}

function select_option_location($preset='') {
  $opt = '';
  $list = $this->list_location();

  $flag = false;
  foreach ($list as $item) {
    $id = $item['location_id'];

    $title = $item['title'];
    $group = $item['group'];
    $tit = "$title($group)";

    if ($preset == $id) {
      $sel = ' selected';
      $flag = true;
    } else $sel = '';
    $opt .= "<option value='$id'$sel>$tit</option>";
  }
  if (!$flag) {
    $opt .= "<option value='$preset' selected>$preset</option>";
  }
  return $opt;
}


function list_location($group='', $treeflag=false) {

  $w = array('1');
  if ($group) $w[] = "loc_group='$group'";
  $sql_where = " WHERE ".join(" AND ", $w);

  $qry = "SELECT * FROM location $sql_where";
  $ret = db_query($qry);

  if ($treeflag) {

    $info = array();
    while ($row = db_fetch($ret)) {
      $group = $row['loc_group'];
      $info[$group][] = array(
        'location_id'=>$row['id'],
        'title'=>$row['loc_title'],
        'group'=>$row['loc_group'],
        'lat'=>$row['lat'],
        'lng'=>$row['lng'],
      );
    }
    $groups = $this->location_groups();
    $info['groups'] = $groups;

  } else {

    $info = array();
    while ($row = db_fetch($ret)) {
      $info[] = array(
        'location_id'=>$row['id'],
        'group'=>$row['loc_group'],
        'title'=>$row['loc_title'],
        'lat'=>$row['lat'],
        'lng'=>$row['lng'],
      );
    }

  }
  return $info;
}


};

?>
