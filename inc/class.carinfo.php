<?php

class carinfo {

function carinfo() {

}

// 차량 선택 select 옵션
function car_select_option($preset='') {
  $opt = '';
  $list = $this->list_car();

  $flag = false;
  foreach ($list as $item) {
    //dd($item);
    $v = $item['car_id'];
    $dv = $item['driver_name'];
    if (!$dv) $dv = "**운전자없음**";
    $t = sprintf("[$v] %s (%s) @%s", $item['car_no'], $item['car_model'], $dv);

    if ($preset == $v) {
      $sel = ' selected';
      $flag = true;
    } else $sel = '';
    $opt .= "<option value='$v'$sel>$t</option>";
  }
  if (!$flag) {
    $opt .= "<option value='$preset' selected>$preset</option>";
  }
  return $opt;
}


// 모든 차량 정보
// sql_where 는 사용안함
function list_car($sql_where='', $debug=false, $opt=null) {

  $w = array('1');
  if ($opt['좌표정보있음']) {
    $w[] = "c.lat != 0";
    $w[] = "c.lng != 0";
  }
  if ($opt['운행중인차량']) {
    $w[] = "d.is_driving='1'";
  }
  if ($opt['팀']) {
    $v = $opt['팀'];
    if ($v != 'all') $w[] = "d.driver_team='$v'";
  }
  $sql_where = " WHERE ".join(" AND ", $w);

  $qry = "SELECT c.*, d.driver_name, d.driver_stat, Ds.DsName, d.driver_team"
    ."  FROM carinfo c"
    ." LEFT JOIN driver d on c.driver_id=d.id"
    ." LEFT JOIN Ds on d.driver_stat=Ds.Ds"
    .$sql_where;

  $ret = db_query($qry);

  $info = array();
  while ($row = db_fetch($ret)) {
    $item = array(
      'car_id'=>$row['id'],
      'car_no'=>$row['car_no'],
      'car_model'=>$row['car_model'],
      'car_color'=>$row['car_color'],
      'lat'=>$row['lat'],
      'lng'=>$row['lng'],
      'driver_name'=>$row['driver_name'],
      'status_code'=>$row['driver_stat'],
      'status_name'=>$row['DsName'],
      'des_name1'=>$row['des_name1'],
      'dep_name1'=>$row['dep_name1'],
      'driver_team'=>$row['driver_team'],
    );
    if ($debug) $item['_debug_'] = $row;
    $info[] = $item;
  }
  return $info;
}


// 차량 위치 설정
function set_position($car_id, $lat, $lng) {
  if (!$car_id) return;

  // carinfo 업데이트
  $qry = "UPDATE carinfo SET lat='$lat',lng='$lng' WHERE id='$car_id'";
  $ret = db_query($qry);

}

// 차량에 운전자를 할당
// driver_id 는 검증하지 않음
function set_driver($car_id, $driver_id) {
  if (!$car_id) return;
  if (!$driver_id) return;

  // carinfo 업데이트
  $qry = "update carinfo SET driver_id=0 WHERE driver_id='$driver_id'";
  $ret = db_query($qry);

  $qry = "update carinfo SET driver_id='$driver_id' WHERE id='$car_id'";
  $ret = db_query($qry);

  // driver 정보 업데이트
  $qry = "update driver SET car_id='0' WHERE car_id='$car_id'";
  $ret = db_query($qry);

  $qry = "update driver SET car_id='$car_id' WHERE id='$driver_id'";
  $ret = db_query($qry);

}


};

?>
