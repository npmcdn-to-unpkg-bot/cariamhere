<?php

  include_once("../path.php");
  include_once("$env[prefix]/inc/common.api.php");
  include_once("$env[prefix]/inc/class.carinfo.php");
  include_once("$env[prefix]/inc/class.driver.php");
  include_once("$env[prefix]/inc/class.role.php");
  include_once("$env[prefix]/inc/class.location.php");
  include_once("$env[prefix]/inc/class.person.php");
  include_once("$env[prefix]/inc/class.notice.php");

### {{{
function ok_response($resp) {
  $resp['result'] = 'ok';
  print json_encode($resp);
  exit;
}

function error_response($msg) {
  $resp = array('result'=>'error', 'message'=>$msg);
  print json_encode($resp);
  exit;
}

function _check_appkey() {
  global $data;
  $appkey = $data['appkey'];
  if (!$appkey) error_response('null appkey');
  return $appkey;
}
// null_error 가 true 인경우 값이 없으면 에러를 발생
function _get_data($key, $null_error=true) {
  global $data;
  $value = $data[$key];
  if ($null_error) {
    if (!$value) error_response("$key value is null");
  }
  return $value;
}

function _get_driver($appkey) {
  global $clsdriver;

  $row = $clsdriver->get_driver_by_appkey($appkey);
  if (!$row) error_response('invalid appkey');
  return $row;
}

### }}}

  $test = $_GET['test'];
  if ($test) {
    print("ok"); exit;
  }

  $json = file_get_contents('php://input');
  //print($json);

  $data = json_decode($json, true);
  //dd($data);

  $action = $data['action'];

  $clsdriver = new driver();
  $clscar = new carinfo();
  $clsrole = new role();
  $clslocation = new location();
  $clsperson = new person();
  $clsnotice = new notice();

  //print_r($_REQUEST);
  //print_r($_SERVER);

  // 디버깅용
  # $hdrs = getallheaders();
  # print_r($hdrs);

  $debug_echo = $data['echo'];
  if ($debug_echo) { ok_response($data); exit; }


// 회원가입
if ($action == 'register') {

  $goyu = $data['goyu'];
  $tel = $data['tel'];
  $did = $data['did'];
  $pushkey = $data['pushkey'];
  $phone_os = $data['phone_os'];

  $phone_hash = '';

  $ver = app_version(); // version info

  list($ret, $errmsg) = $clsdriver->register_driver($goyu, $tel, $sosok, $did, $pushkey, $phone_os, $phone_hash, $name);
  if ($ret == false) error_response($errmsg);

  $resp = array(
     'phone_hash'=>$phone_hash,
     'name'=>$name,
     'app_version'=>$ver,
  );
  ok_response($resp);
  exit;
}

// appKey 받아오기
if ($action == 'get_appkey') {

  $did = $data['did'];
  $phone_hash = $data['phone_hash'];

  $appkey = $clsdriver->authenticate($did, $phone_hash);
  if (!$appkey) error_response('authentication fail');

  $row = $clsdriver->get_driver($phone_hash);
  $name = $row['driver_name'];
  $driver_id = $row['id'];

  $url = $conf['notice_url']."?appkey=$appkey";

  $ver = app_version(); // version info

  $resp = array(
   'appkey'=>$appkey,
   'name'=>$name,
   'driver_id'=>$driver_id,
   'notice_url'=>$url,
   'app_version'=>$ver
  );
  ok_response($resp);
  exit;
}

if ($action == 'latest_version') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  $url = $conf['notice_url']."?appkey=$appkey";
  $update_url = $conf['update_url'];

  $ver = app_version(); // version info
  $resp = array(
    'app_version'=>$ver,
    'notice_url'=>$url,
    'update_url'=>$update_url,
    'iphone_update_url'=>$conf['iphone_update_url'],
  );
  ok_response($resp);
  exit;
}


// 사용금지
if ($action == 'get_driver_info') {

  error_response("deprecated");

  $appkey = _check_appkey();

  $row = $clsdriver->get_driver_by_appkey($appkey);
  if (!$row) error_response('invalid appkey');

  $resp = array(
    'driver_id'=>$row['id'],
    'role'=>$row['role']
  );
  ok_response($resp);
  exit;
}


// 공지사항
if ($action == 'list_notice') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  //$role = _get_data('role', false);

  //$driver_role = $row['role'];
  //if (!$driver_role) error_response('driver role is null');

  $list = $clsnotice->get_notice();

  $resp = array(
    'list'=>$list,
  );
  ok_response($resp);
  exit;
}


// 드라이버 상태 정보 얻기
if ($action == 'get_driver_status') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);
  //dd($row);

  $resp = array(
    'driver_id'=>$row['id'],
    'status'=>$row['driver_stat'],
    'status_string'=>$row['DsName']
  );
  ok_response($resp);
  exit;
}

// 드라이버 상태 정보 설정 (사용금지)
if ($action == 'set_driver_status') {

  error_response("deprecated");

  $appkey = _check_appkey();

  $status = $data['status'];
  $driver_id = $data['driver_id'];

  $clsdriver->set_driver_status($driver_id, $status);

  $row = _get_driver($appkey);
  //dd($row);

  // 위치 전송 주기
  $interval = $clsdriver->get_interval($row['driver_stat']);

  $resp = array(
    'driver_id'=>$row['id'],
    'status'=>$row['driver_stat'],
    'status_string'=>$row['DsName'],
    'gps_interval'=>$interval,
  );
  ok_response($resp);
  exit;
}


// 차량 리스트
if ($action == 'list_car') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  $info = $clscar->list_car();
  //print_r($info); 
  $resp = array('list'=>$info);
  ok_response($resp);
}

// 장소 리스트
if ($action == 'list_location') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  $group = $data['group']; // 장소구분 (옵션)
  $treeflag = $data['treeflag']; // 장소구분별로 묶음

  $info = $clslocation->list_location($group, $treeflag);
  $resp = array('list'=>$info);
  ok_response($resp);
}


if ($action == 'list_role') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  $info = $clsrole->list_role();
  //print_r($info); 
  $resp = array('list'=>$info);
  ok_response($resp);
}

// 의전대상자 리스트
if ($action == 'list_person') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  $info = $clsperson->list_person();
  //print_r($info); 
  $resp = array('list'=>$info);
  ok_response($resp);
}


// 역할 변경 (사용안함)
if ($action == 'change_role') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  $role = $data['role'];
  $driver_id = $data['driver_id'];

  $ret = $clsdriver->driver_change_role($driver_id, $role);
  $resp = array('ret'=>$ret);
  ok_response($resp);
}


// 차량 설정
if ($action == 'bind_driver_car') {
  $appkey = _check_appkey();

  $driver_id = _get_data('driver_id');
  $car_id = _get_data('car_id');

  $clscar->set_driver($car_id, $driver_id);
  $resp = array();
  ok_response($resp);
  exit;
}

// 운전자 상태 리스트
if ($action == 'get_all_driver_status') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  $list = $clsdriver->driver_all_status();

  $resp = array('list'=>$list);
  ok_response($resp);
  exit;
}

// 드라이버 위치 전송
if ($action == 'set_driver_location') {

  error_response("deprecated");

  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  $driver_id = $row['id'];
  $lat = $data['lat'];
  $lng = $data['lng'];

  // 운전자 위치 변경
  $clsdriver->set_driver_location($driver_id, $lat, $lng, true);

  $resp = array(
    'driver_id'=>$row['id'],
    'status'=>$row['driver_stat'],
    'status_string'=>$row['DsName']
  );
  ok_response($resp);
  exit;
}

// 출발지,목적지 설정
if ($action == 'set_schedule') {

  error_response("deprecated");

  $appkey = _check_appkey();
  $row = _get_driver($appkey);
  $driver_id = $data['driver_id'];

  $dep_id   = $data['depart_from']; //depart
  $dep_name = $data['depart_from_etc']; 

  $des_id= $data['going_to']; // destination
  $dep_name= $data['going_to_etc']; // destination

  // 출발지, 목적지 설정
  $clsuser->set_scheudle($user_id, $dep_id, $des_id, $dep_name, $des_name);

  $resp = array(
    'user_id'=>$row['id'],
    'status'=>$row['driver_stat'],
    'status_string'=>$row['DsName']
  );
  ok_response($resp);
  exit;
}

// 운행시작
if ($action == 'start_driving') {

  $appkey = _check_appkey();
  $row = _get_driver($appkey);
  $driver_id = $row['id'];

  $depart_from = $data['depart_from'];
  $going_to    = $data['going_to'];

  // $depart_from : 출발지 ID
  // $going_to : 목적지 ID
  // $interval : 위치 전송 주기 (output)
  // $driving_session_id : 세션ID (output)
  $clsdriver->start_driving($row, $driver_id, $depart_from, $going_to, $interval, $driving_session_id);

  $resp = array(
    'driver_id'=>$driver_id,
    'gps_interval'=>$interval,
    'run_id'=>$driving_session_id,
    'person'=> array(
      'person_id'=>$row['person_id'],
      'name'=>$row['person_name'],
      'group'=>$row['person_group'],
     )
  );
  ok_response($resp);
  exit;
}

// 운전자 위치 전송
if ($action == 'at_location') {

  $appkey = _check_appkey();
  $row_driver = _get_driver($appkey);

  $driver_id = $row_driver['id'];
  $lat = $data['lat'];
  $lng = $data['lng'];
  $run_id = $data['run_id'];

  // 운전자 위치 변경
  $error = $clsdriver->at_location($row_driver, $driver_id, $run_id, $lat, $lng, $elapsed);
  if ($error) error_response($error);

  $resp = array(
    'run_id'=>$run_id,
    'elapsed'=>$elapsed,
  );
  ok_response($resp);
  exit;
}

// 운행종료
if ($action == 'finish_driving') {

  $appkey = _check_appkey();
  $row_driver = _get_driver($appkey);

  $driver_id = $row_driver['id'];
  $run_id = $data['run_id'];

  // 운전자 위치 변경
  $error = $clsdriver->finish_driving($row_driver, $driver_id, $run_id, $elapsed);
  if ($error) error_response($error);

  //$url = $conf['map_view_url']."?id=$run_id";

  $resp = array(
    'run_id'=>$run_id,
    'elapsed'=>$elapsed,
    //'map_url'=>$url,
  );
  ok_response($resp);
  exit;
}

// VIP 조회
if ($action == 'query_person') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);
  $driver_id = $row['id'];

  if ($row['person_id']) {
    $per_no = $row['person_id'];
    $info = $clsperson->person_information($per_no);
  } else {
    $info = null;
  }

  $resp = array(
    'driver_id'=>$driver_id,
    'person'=> $info,
  );
  ok_response($resp);
  exit;
}

// VIP 설정
if ($action == 'set_person') {
  $appkey = _check_appkey();
  $row = _get_driver($appkey);

  $driver_id = $row['id'];

  $per_no = $data['person_id'];

  // VIP 설정
  $error = $clsdriver->set_person($row, $per_no);
  if ($error) error_response($error);

  $info = $clsperson->person_information($per_no);

  $resp = array(
    'driver_id'=>$driver_id,
    'person'=> $info,
  );
  ok_response($resp);
  exit;
}

// 비상상황
if ($action == 'list_emergency') {
  $appkey = _check_appkey();
  $driver_row = _get_driver($appkey);
  $driver_id = $driver_row['id'];

  $info = $clsdriver->emergency_list();

  $resp = array(
    'driver_id'=>$driver_id,
    'select'=> $info,
  );
  ok_response($resp);
  exit;
}

// 비상상황
if ($action == 'do_emergency') {
  $appkey = _check_appkey();
  $driver_row = _get_driver($appkey);
  $driver_id = $driver_row['id'];

  $code = $data['code'];
  $e_name = $clsdriver->emergency_code2name($code);

  $clsdriver->do_emergency($driver_row, $code);

  $call = $conf['emergency_call']; // 비상전화

  $resp = array(
    'message'=> "$e_name $code 비상상황 접수 완료. 긴급 전화 $call",
    'e_phone'=> $call,
  );
  ok_response($resp);
  exit;
}

// 비상상황 해제
if ($action == 'exit_emergency') {
  $appkey = _check_appkey();
  $driver_row = _get_driver($appkey);
  $driver_id = $driver_row['id'];

  $clsdriver->exit_emergency($driver_row);

  $resp = array(
    'message'=> "비상상황 해제 완료.",
  );
  ok_response($resp);
  exit;
}



// 4자리 숫자 인사 번호로 정보를 조회
if ($action == 'person_information') {
  $appkey = _check_appkey();
  $driver_row = _get_driver($appkey);
  $per_no = $data['per_no'];

  $info = $clsperson->person_information($per_no);
  if (!$info) error_response('person not found');

  $resp = array(
    'info'=> $info,
  );
  ok_response($resp);
  exit;
}






  exit;

?>
