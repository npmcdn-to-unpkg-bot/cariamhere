<?php

  include_once("$env[path_include]/class.carinfo.php");

class driver {
  var $debug = false;

  function driver() {

  }

  // apikey 갱신
  function update_appkey($phone_hash, $appkey) {
    $qry = "UPDATE driver SET apikey='$appkey' where phone_hash='$phone_hash'";
    $ret = db_query($qry);
  }

  function authenticate($did, $phone_hash) {

    $hash = md5($pass);
    $qry = "SELECT * FROM driver WHERE did='$did' AND phone_hash='$phone_hash'";
    $row = db_fetchone($qry);

    if ($row) {
      $str = time().$phone_hash.$did;
      $token = md5($str);

      $this->update_appkey($phone_hash, $token);
      return $token;

    } else {
      return false; // 실패
    }
  }


  function _get_driver($sql_where) {
    $qry = "SELECT d.*"
  .", p.person_name, p.person_group, p.id person_id"
  ." FROM driver d"
  ." LEFT JOIN person p ON d.person_id=p.id"
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


  // 운전자 등록
  function register_driver($goyu, $tel, $sosok, $did, $push_key, $phone_os, &$phone_hash, &$name) {

    $goyu = trim($goyu);
    $tel = trim($tel);

    // 고유번호와 전화번호로 확인
    $qry = "SELECT * FROM driver WHERE driver_no='$goyu' and driver_tel='$tel'";
    $row = db_fetchone($qry);
    if (!$row) return array(false,"info not found *$goyu*, *$tel*"); // 정보가 없으면 실패

    $name = $row['driver_name'];
    // TODO 이미 가입되어 있으면 에러/

    $id = $row['id'];
    $phone_hash = md5($tel);

    $s = array();
    $s[] = "did='$did'";
    $s[] = "phone_hash='$phone_hash'";
    $s[] = "phone_os='$phone_os'";
    $s[] = "pushkey='$push_key'";
    $s[] = "rflag='1'";
    $sql_set = " SET ".join(",", $s);

    $qry = "UPDATE driver"
        .$sql_set
        ." WHERE id='$id'";
    $ret = db_query($qry);

    return array(true, 'success'); // 성공
  }

  // 운전자 상태 (코드->제목)
  function get_ds_name($code) {
    $qry = "SELECT * FROM Ds WHERE Ds='$code'";
    $row = db_fetchone($qry);
    if ($row) return $row['DsName'];
    else return "ERROR";
  }

  // API call
  // 운전자 상태 리스트
  function driver_all_status() {
    $qry = "SELECT * FROM Ds";
    $ret = db_query($qry);
    $a = array();
    while ($row = db_fetch($ret)) {
      $a[] = array($row['Ds'], $row['DsName']);
    }
    return $a;
  }


  // 운전자 상태 select 옵션
  function driver_status_option($preset='') {
    $opt = '';
    $list = $this->driver_all_status();

    $match = false;
    foreach ($list as $item) {
      list($ds, $dsname) = $item;

      $title = "($ds) $dsname";
      if ($preset == $ds) {
        $sel = ' selected';
        $match = true;
      } else $sel = '';
      $opt .= "<option value='$ds'$sel>$title</option>";
    }
    if (!$match) {
      $opt .= "<option value='$preset' selected>$preset</option>";
    }
    return $opt;
  }

/*
  function status_is_driving($status) {
    $is_driving = false;
    if ($status == 'DS_AIRPORT') $is_driving = true;
    if ($status == 'DS_TOVENUE') $is_driving = true;
    if ($status == 'DS_TOHOTEL') $is_driving = true;
    return $is_driving;
  }

// API call
// 운전자 상태 변경 (사용금지)
function set_driver_status($driver_id, $status) {
  $s = array();

  // 상태에 따라 운행 시작인지 아닌지 판단
  $is_driving = $this->status_is_driving($status);
  if ($is_driving) {
    $s[] = "is_driving='1'"; 
    $s[] = "sess_st=NOW()"; // 운행시작시간을 기록
    $s[] = "sess_fg1=0";
    //$s[] = "sess_lat=0";
    //$s[] = "sess_lng=0";
  } else {
    $s[] = "is_driving='0'";
  }

  $s[] = "driver_stat='$status'";
  $s[] = "udate=now()";
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE driver $sql_set where id='$driver_id'";
  $ret = db_query($qry);

  // 로그에 저장
  $this->accu_driver_log($driver_id, 0, $status, 0,0, $is_driving);

  if (!$is_driving) { // 운행이 끝남, 세션 정보를 따로 보관시킴
    $this->record_session($driver_id);
  }

}

function record_session($driver_id) {
  $row = $this->get_driver_by_id($driver_id);
  $st = $row['start_time']; // 운행시작시간

  $s = array();
  $s[] = "start_time='$st'";
  $s[] = "end_time=now()";
  $s[] = "driver_id='$driver_id'";
  $s[] = "idate=now()";
  $s[] = "udate=now()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO driving_session $sql_set";
  $ret = db_query($qry);
}
*/

/*
// API call
// 경로 설정
// des_id : 목적지 장소 ID     des_name : 목적지 장소 기타
// dep_id : 출발지 장소 ID     dep_name : 출발지 장소 기타
function set_scheudle($driver_id, $dep_id, $des_id, $dep_name, $des_name) {

  $s = array();
  $s[] = "des_id='$des_id'";
  $s[] = "dep_id='$dep_id'";
  $s[] = "udate=now()";
  $sql_set = " SET ".join(",", $s);
  $qry = "UPDATE driver $sql_set WHERE id='$driver_id'"; 
  $ret = db_query($qry);

}

// API call
// 운전자 위치 설정
// $objUser->set_driver_location($driver_id, $lat, $lng, $car_location_update=false);
function set_driver_location($driver_id, $lat, $lng) {
  $row = $this->get_driver_by_id($driver_id);

  // 운전자 상태에 따라서 is_driving 결정
  $is_driving = $row['is_driving'];

  // 차량도 함께 위치 설정
  $car_id = $row['car_id'];
  if ($car_id) {
    $clscar = new carinfo();
    $clscar->set_position($car_id, $lat, $lng);
  }

  $s = array();
  $s[] = "lat='$lat'";
  $s[] = "lng='$lng'";
  $s[] = "udate=now()";
  if ($row['sess_fg1'] == 0) { // 출발 위치 설정이 안되었으면
    $s[] = "sess_fg1='1'";
    $s[] = "sess_lat='$lat'";
    $s[] = "sess_lng='$lng'";
  }
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE driver $sql_set where id='$driver_id'";
  $ret = db_query($qry);

  $this->accu_driver_log($driver_id, 0, '', $lat, $lng, $is_driving);
}

  // 운전자 로그
  function accu_driver_log($driver_id, $car_id=0, $status='', $lat=0, $lng=0, $is_driving=0) {
    $s = array();
    $s[] = "driver_id='$driver_id'";
    $s[] = "car_id='$car_id'";
    $s[] = "driver_stat='$status'";
    $s[] = "lat='$lat'";
    $s[] = "lng='$lng'";
    $s[] = "is_driving='$is_driving'";
    $s[] = "idate=now()";
    $sql_set = " SET ".join(",", $s);

    $qry = "INSERT INTO driver_log $sql_set";
    $ret = db_query($qry);
  }
*/

  // 위치 로그
  function run_log($driver_id, $run_id, $lat, $lng) {
    $s = array();
    $s[] = "driver_id='$driver_id'";
    $s[] = "run_id='$run_id'";
    $s[] = "lat='$lat'";
    $s[] = "lng='$lng'";
    $s[] = "idate=now()";
    $sql_set = " SET ".join(",", $s);
    $qry = "INSERT INTO run_log $sql_set";
    $ret = db_query($qry);
  }

  //------------------------------
  // 운행 시작
  // $depart_from : 출발지 ID
  // $going_to : 목적지 ID
  // $interval : 위치 전송 주기
  function start_driving($driver_row, $driver_id, $depart_from, $going_to, &$interval, &$run_id) {


    // run 정보를 새로 만든다
    $s = array();
    $s[] = "driver_id='$driver_id'";
    $s[] = "depart_from='$depart_from'";
    $s[] = "going_to='$going_to'";
    $s[] = "start_time=now()";
    $s[] = "idate=now()";
    $s[] = "udate=now()";

    $person_id = $driver_row['person_id'];
    $s[] = "person_id='$person_id'";

    $sql_set = " SET ".join(",", $s);
    $qry = "INSERT INTO run $sql_set";
    $ret = db_query($qry);

    $qry = "SELECT LAST_INSERT_ID() as id";
    $row = db_fetchone($qry);
    $run_id = $row['id'];

    global $conf;
    $interval = $conf['interval_driving'];

    // run 정보를 driver 에 반영
    $qry = "UPDATE driver"
     ." SET run_id='$run_id', is_driving=1, driver_stat='DS_DRIVE'"
     ." WHERE id='$driver_id'";
    $ret = db_query($qry);
  }


  // 운전자 위치 설정
  function at_location($row_driver, $driver_id, $run_id, $lat, $lng, &$elapsed) {

    if (!$run_id) return 'run_id is null';

    // run 정보 확인
    $qry = "SELECT r.*
 , UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(r.start_time) AS e
 FROM run r 
 WHERE r.driver_id='$driver_id' and r.id='$run_id'";
    $row_run = db_fetchone($qry);
    if (!$row_run) return 'run is is invalid';
    $elapsed = $row_run['e'];

    // 차량도 함께 위치 설정
    $car_id = $row_driver['car_id'];
    if ($car_id) {
      $clscar = new carinfo();
      $clscar->set_position($car_id, $lat, $lng);
    }

    // run 정보 갱신
    $s = array();
    $s[] = "lat='$lat'";
    $s[] = "lng='$lng'";
    $s[] = "udate=now()";
    $lat_s = $row_run['lat_s'];
    $lng_s = $row_run['lng_s'];
    if (!$lat_s || !$lng_s) { // 출발 좌표 없음
      if ($lat && $lng) {
        $s[] = "lat_s='$lat'"; // 출발 좌표를 입력
        $s[] = "lng_s='$lng'";
      }
    }
    $sql_set = " SET ".join(",", $s);
    $qry = "UPDATE run $sql_set where id='$run_id'";
    $ret = db_query($qry);

    // driver 정보 갱신
    $s = array();
    $s[] = "lat='$lat'";
    $s[] = "lng='$lng'";
    $sql_set = " SET ".join(",", $s);
    $qry = "UPDATE driver $sql_set where id='$driver_id'";
    $ret = db_query($qry);

    // 로그 기록
    $this->run_log($driver_id, $run_id, $lat, $lng);
  }

  // 운행 종료
  function finish_driving($row_driver, $driver_id, $run_id, &$elapsed) {

    if (!$run_id) return 'run_id is null';

    // run 정보 확인
    $qry = "SELECT r.*
 , UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(r.start_time) AS e
 FROM run r 
 WHERE r.driver_id='$driver_id' and r.id='$run_id'";
    $row_run = db_fetchone($qry);
    if (!$row_run) return 'run is is invalid';
    $elapsed = $row_run['e'];

    $lat_e = $row_run['lat_e'];
    $lng_e = $row_run['lng_e'];
    if ($lat_e || $lng_e) { // 최종 좌표 입력됨
      return '운행이 이미 종료되었습니다.';
    }

    // run 정보 갱신
    $s = array();
    $s[] = "end_time=NOW()";
    $s[] = "udate=NOW()";
    $s[] = "lat_e=lat"; // 최종 좌표
    $s[] = "lng_e=lng";
    $sql_set = " SET ".join(",", $s);
    $qry = "UPDATE run $sql_set"
      ." WHERE id='$run_id' AND driver_id='$driver_id'";
    $ret = db_query($qry);

    // driver 에 반영
    $qry = "UPDATE driver"
      // run_id 는 지우지 않음 (최근 운행 기록을 조회하기 위해)
      ." SET is_driving=0, driver_stat='DS_STOP'"
      ." WHERE id='$driver_id'";
    $ret = db_query($qry);
  }

  // VIP 변경
  function set_person($row_driver, $person_id) {
    $driver_id = $row_driver['id'];
    if ($row_driver['is_driving']) {
      return 'cannot change person during driving';
    }

    $qry = "UPDATE driver SET person_id='$person_id' WHERE id='$driver_id'";
    $ret = db_query($qry);
 //dd($qry);
  }

  function sql_select_run_1() {
    $sql_select = "SELECT d.driver_name, d.id driver_id"
    .", r.id run_id"
    .", c.car_no"
    .", Ds.DsName"
    .", IF(d.rflag,'O','X') _rflag"
    .", l1.loc_title loc1"
    .", l2.loc_title loc2"
    .", TIME(r.start_time) stime"
    .", TIME(r.end_time) etime"
    .", p.id person_id, p.person_name, p.person_group"
    ;
    return $sql_select;
  }
  // driver와 run 테이블에 대한 join 은 pre_join에 넣을 것
  function sql_join_run_1($pre_join='') {
    $sql_join = ''
    .$pre_join
    ." LEFT JOIN carinfo c ON d.car_id=c.id"
    ." LEFT JOIN Ds ON d.driver_stat=Ds.Ds"
    ." LEFT JOIN location l1 ON r.depart_from=l1.id"
    ." LEFT JOIN location l2 ON r.going_to=l2.id"
    ." LEFT JOIN person p ON r.person_id=p.id"
     ;
    return $sql_join;
  }

}; // class

?>
