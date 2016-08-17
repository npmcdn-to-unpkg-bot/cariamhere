<?php

class driver {
  var $debug = false;

function driver() {

}

function register_driver($goyu, $name, $tel, $sosok, $did, &$phone_hash) {

  // 고유번호와 전화번호로 확인
  $qry = "SELECT * FROM driver WHERE driver_no='$goyu' and driver_tel='$tel'";
  $row = db_fetchone($qry);
  if (!$row) return false; // 정보가 없으면 실패

  $id = $row['id'];
  $phone_hash = md5($tel);

  $s = array();
  //$s[] = "driver_name='$name'";
  //$s[] = "driver_sosok='$sosok'";
  $s[] = "did='$did'";
  $s[] = "phone_hash='$phone_hash'";
  $s[] = "rflag='1'";
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE driver"
        .$sql_set
        ." WHERE id='$id'";
  $ret = db_query($qry);

  return true; // 성공
}


function driver_all_status() {
  $qry = "SELECT * FROM Ds";
  $ret = db_query($qry);
  $a = array();
  while ($row = db_fetch($ret)) {
    $a[] = array($row['Ds'], $row['DsName']);
  }
  return $a;
}

function user_change_role($user_id, $role) {
  $qry = "UPDATE driver SET role='$role' WHERE id='$user_id'";
  $ret = db_query($qry);
  return true;
}

function driver_status_option($preset='') {
  $opt = '';
  $list = $this->driver_all_status();

  $match = false;
  foreach ($list as $item) {
    list($ds, $dsname) = $item;

    if ($preset == $ds) {
      $sel = ' selected';
      $match = true;
    } else $sel = '';
    $opt .= "<option value='$ds'$sel>$dsname</option>";
  }
  if (!$match) {
    $opt .= "<option value='$preset' selected>$preset</option>";
  }
  return $opt;
}


function set_driver_status($driver_id, $status) {
  $s = array();
  $s[] = "driver_stat='$status'";
  $s[] = "udate=now()";
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE driver $sql_set where id='$driver_id'";
  $ret = db_query($qry);

  $this->driver_log($driver_id, 0, $status, 0,0);
}

function set_driver_location($driver_id, $lat, $lng) {
  $s = array();
  $s[] = "lat='$lat'";
  $s[] = "lng='$lng'";
  $s[] = "udate=now()";
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE driver $sql_set where id='$driver_id'";
  $ret = db_query($qry);

  $this->driver_log($driver_id, 0, '', $lat, $lng);
}

function driver_log($driver_id, $car_id=0, $status='', $lat=0, $lng=0) {
  $s = array();
  $s[] = "driver_id='$driver_id'";
  $s[] = "car_id='$car_id'";
  $s[] = "driver_stat='$status'";
  $s[] = "lat='$lat'";
  $s[] = "lng='$lng'";
  $s[] = "idate=now()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO driver_log $sql_set";
  $ret = db_query($qry);
}


// $cls->update_password($id, $pass);
function update_password($id, $pass) {
  $hash = md5($pass);
  $qry = "UPDATE driver SET password='$hash' where id='$id'";
  $ret = db_query($qry);
}

function update_token($phone_hash, $token) {
  $qry = "UPDATE driver SET apikey='$token' where phone_hash='$phone_hash'";
  $ret = db_query($qry);
}

function _get_driver($sql_where) {
  $qry = "SELECT d.*"
.", Ds.DsName"
." FROM driver d"
." LEFT JOIN Ds ON d.driver_stat=Ds.Ds" // left join
.$sql_where;
  $row = db_fetchone($qry);
  return $row;
}

function get_driver_by_id($driver_id) {
  $sql_where = " WHERE d.id='$driver_id'";
  $row = $this->_get_driver($sql_where);
  return $row;
}
 
function get_driver($phone_hash) {
  $sql_where = " WHERE d.phone_hash='$phone_hash'";
  $row = $this->_get_driver($sql_where);
  return $row;
}

function get_driver_by_appkey($appkey) {
  $sql_where = " WHERE d.apikey='$appkey'";
  $row = $this->_get_driver($sql_where);
  return $row;
}

function authenticate($did, $phone_hash) {

  $hash = md5($pass);
  $qry = "SELECT * FROM driver WHERE did='$did' AND phone_hash='$phone_hash'";
  $row = db_fetchone($qry);
  //dd($row);

  if ($row) {

    $str = time().$phone_hash.$did;
    $token = md5($str);

    $this->update_token($phone_hash, $token);

    return $token;

  } else {
    return false; // 실패
  }
}

};

?>
