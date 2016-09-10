<?php

class person {
  var $debug = false;

function person() {

}

function get_person($per_no) {
  $sql_select = "SELECT p.*, Nat.*";
  $sql_from = " FROM person p";
  $sql_join = " LEFT JOIN Nat ON p.person_nation=Nat.nnum";

  $qry = $sql_select.$sql_from.$sql_join
    ." WHERE p.per_no='$per_no'" ;
  //apilog("get person $qry");
  $row = db_fetchone($qry);
  return $row;
}

function person_groups() {
  $list = array(
    array('title'=>'1', 'value'=>'1'),
    array('title'=>'2', 'value'=>'2'),
    array('title'=>'4', 'value'=>'4'),
    array('title'=>'5', 'value'=>'5'),
    array('title'=>'6', 'value'=>'6'),
    array('title'=>'7', 'value'=>'7'),
    array('title'=>'8', 'value'=>'8'),
  );
  return $list;
}

function person_group_option($preset='') {
  $opt = '';
  $opt .= "<option value='all'>=선택=</option>";
  $list = $this->person_groups();

  $flag = false;
  foreach ($list as $item) {

    $value = $item['value'];
    $title = $item['title'];

    if ($preset == $value) {
      $sel = ' selected';
      $flag = true;
    } else $sel = '';

    $opt .= "<option value='$value'$sel>$title</option>";
  }
  if (!$flag) {
    $opt .= "<option value='$preset' selected>$preset</option>";
  }
  return $opt;
}

function person_levels() {
  $list = array(
    array('title'=>'VVIP', 'value'=>'vvip'),
    array('title'=>'VIP', 'value'=>'vip'),
    array('title'=>'일반', 'value'=>'general'),
  );
  return $list;
}

function person_level_option($preset='') {
  $opt = '';
  $opt .= "<option value='all'>=선택=</option>";
  $list = $this->person_levels();

  $flag = false;
  foreach ($list as $item) {

    $value = $item['value'];
    $title = $item['title'];

    if ($preset == $value) {
      $sel = ' selected'; $flag = true;
    } else $sel = '';
    $opt .= "<option value='$value'$sel>$title</option>";
  }
  if (!$flag) {
    $opt .= "<option value='$preset' selected>$preset</option>";
  }
  return $opt;
}

function select_option_person($preset='') {
  $opt = '';
  $list = $this->list_person_raw();
  //dd($list);

  $flag = false;
  foreach ($list as $item) {
    $id = $item['id'];
    $name = $item['person_name'];
    $group = $item['person_group'];
    $post = $item['person_position'];

    $v = $item['per_no'];
    $_no = sprintf("%04d", $item['per_no']);

    $tit = "[$_no] $name (그룹:$group)";

    if ($preset == $v) {
      $sel = ' selected';
      $flag = true;
    } else $sel = '';
    $opt .= "<option value='$v'$sel>$tit</option>";
  }
  if (!$flag) {
    $opt .= "<option value='$preset' selected>$preset</option>";
  }
  return $opt;
}


function list_person_raw() {
  $qry = "SELECT * FROM person";
  $ret = db_query($qry);
  $info = array();
  while ($row = db_fetch($ret)) {
    $info[] = $row;
  }
  return $info;
}

function get_level_string($person_level) {
  if ($person_level == 'vvip') return 'VVIP';
  else if ($person_level == 'vip') return 'VIP';
  else return '일반';
}

// 클라이언트로 전송되는 정보
function get_person_obj(&$row) {
  $lev = $this->get_level_string($row['person_level']);
  $a = array(
    'person_id'=>$row['per_no'], // id 가 아니라 per_no
    'per_no'=>$row['per_no'], // id 가 아니라 per_no
    'name'=>$row['person_name'],
    'group'=>$row['person_group'],
    'level'=>$lev,
    'position'=>$row['person_position'],
    'hotel'=>$row['person_hotel'],
    'nation'=>$row['person_nation'],
  );
  return $a;
}
// 클라이언트로 전송되는 정보 (목록보기, 간략버전)
function get_person_obj_simple(&$row) {
  $lev = $this->get_level_string($row['person_level']);
  $a = array(
    //'person_id'=>$row['per_no'],
    'per_no'=>$row['per_no'],
    'name'=>$row['person_name'],
    'group'=>$row['person_group'],
    'level'=>$lev,
    //'position'=>$row['person_position'],
    //'hotel'=>$row['person_hotel'],
    //'nnum'=>$row['person_nation'],
    'nation'=>$row['nname'],
  );
  return $a;
}


// API
function list_person() {
  $qry = "SELECT p.*, Nat.nname
 FROM person p
 left join Nat on p.person_nation=Nat.nnum";
  $ret = db_query($qry);
  $info = array();
  while ($row = db_fetch($ret)) {
    $info[] = $this->get_person_obj_simple($row);
  }
  return $info;
}


function get_nation_code($nname) {
  $qry = "select * from Nat where nname='$nname'";
  $row = db_fetchone($qry);
  return $row['nnum'];
}

// API
function person_information($per_no) {
  $qry = "SELECT p.*, n.nname
 FROM person p
 LEFT JOIN Nat n ON p.person_nation=n.nnum WHERE p.per_no='$per_no'";
  $row = db_fetchone($qry);

  $info = $this->get_person_obj($row);
# array(
#   'person_id'=>$row['per_no'],
#   'per_no'=>$row['per_no'],
#   'name'=>$row['person_name'],
#   'group'=>$row['person_group'],
#   'level'=>$row['person_level'],
#   'position'=>$row['person_position'],
#   'nation'=>$row['nname'],
#   'hotel'=>$row['person_hotel'],
# );
  return $info;
}

// API
// 번호,초성,이름 검색
function person_information_v2($search) {
  $v = $search;

  // 검색결과가 없을 경우 리턴값
  $info = array(
    'person_id'=>null,
    'per_no'=>null,
    'name'=>null,
  );

  $sql_from = " FROM person p";
  $sql_join = " LEFT JOIN Nat n ON p.person_nation=n.nnum";
  $sql_where = " WHERE p.per_no='$v' OR p.person_name LIKE '%$v%' OR p.person_cho LIKE '%$v%'";

  // 검색결과가 없거나 1개 이상인경우
  $qry = "SELECT count(*) count".$sql_from.$sql_join.$sql_where;
  $row = db_fetchone($qry);
  if ($row['count'] != 1) return $info;

  $qry = "SELECT p.*, n.nname".$sql_from.$sql_join.$sql_where;
  $row = db_fetchone($qry);

  $p = $this->get_person_obj($row);

  $lvl = $this->get_level_string($row['person_level']);

  $name = sprintf("(G%s,$lvl)%s", $row['person_group'], $row['person_name']);
  $p['name_long'] = $name;
  $info = $p;

  return $info;
}



};

?>
