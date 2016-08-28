<?php

class person {
  var $debug = false;

function person() {

}

function get_person($id) {

  $sql_select = "SELECT p.*, Nat.*"
    .", IF(p.person_fflag, 'O', 'X') _fflag";
  $sql_from = " FROM person p";
  $sql_join = " LEFT JOIN Nat ON p.person_nation=Nat.nnum";

  $qry = $sql_select.$sql_from.$sql_join
    ." WHERE p.id='$id'"
     ;
  $row = db_fetchone($qry);
  return $row;
}

function person_groups() {
  $list = array(
    array('title'=>'A그룹', 'value'=>'A'),
    array('title'=>'B그룹', 'value'=>'B'),
    array('title'=>'C그룹', 'value'=>'C'),
  );
  return $list;
}

function person_group_option($preset='') {
  $opt = '';
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

// API
function list_person() {
  $qry = "SELECT * FROM person";
  $ret = db_query($qry);
  $info = array();
  while ($row = db_fetch($ret)) {
    $info[] = array(
      'person_id'=>$row['per_no'], // id 가 아니라 per_no
      'name'=>$row['person_name'],
      'group'=>$row['person_group'],
      'flag'=>$row['person_fflag'],
      'nation'=>$row['person_nation'],
    );
  }
  return $info;
}


function get_nation_code($nname) {
  $qry = "select * from Nat where nname='$nname'";
  $row = db_fetchone($qry);
  return $row['nnum'];
/*

MariaDB [carmaxscj]> select * from Nat;
+-----------------------------------------------+------+--------+--------+-------+
| nname                                         | nnum | ncode3 | ncode2 | inuse |
+-----------------------------------------------+------+--------+--------+-------+
| 해외                                          |    0 | 해외   | 해외   |     1 |
| 아프가니스탄                                  |    4 | AFG    | AF     |     1 |
| 알바니아                                      |    8 | ALB    | AL     |     1 |

*/
}

// API
function person_information($per_no) {
  $qry = "SELECT p.*, n.nname
 FROM person p
 LEFT JOIN Nat n ON p.person_nation=n.nnum WHERE p.per_no='$per_no'";
  $row = db_fetchone($qry);

  $info = array(
    'person_id'=>$row['per_no'],
    'per_no'=>$row['per_no'],
    'name'=>$row['person_name'],
    'group'=>$row['person_group'],
    'position'=>$row['person_position'],
    'nation'=>$row['nname'],
    'hotel'=>$row['person_hotel'],
  );
  return $info;
}


};

?>
