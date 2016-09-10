<?php

  if (@!$env['path_include']) @$env['path_include'] = '.';
  include_once("$env[path_include]/class.carinfo.php");
  include_once("$env[path_include]/class.person.php");
  include_once("$env[path_include]/class.location.php");
  include_once("$env[path_include]/class.telegram.php");

class driver {
  var $debug = false;
  var $telegram_enable = true; // 텔레그렘 연동 전체 on/off

  var $log_enable = true; // apilog
  var $chat_id = 0; // 운전자 chat_id (method간 공유)
  var $driver_row = null; // 운전자 정보 (method간 공유)

  function driver() {
  }
  function apilog($msg) {
    if ($this->log_enable) apilog($msg);
  }

  // 메시지로 알린다.
  // 소속 팀장과 상황실로도 알린다
  // chat_id 는 사용자 본인의 것임
  function chat_notice($chat_id, $msg) {
    $this->apilog("chat_notice chat_id=$chat_id");
    $clstg = new telegram();
    if (!$this->telegram_enable) return;

    $list = array();
    $list[$chat_id] = true; // 본인에게 알림

    $driver_row = $this->driver_row;
    $team = $driver_row['driver_team'];
    $chatids = $this->team_leader_chat_ids($team); // 소속팀 팀장
    foreach ($chatids as $id) {
      $list[$id] = true;
    }
    //apilog($list);

    // 대상자들에게 보냄
    foreach ($list as $id=>$tmp) {
      $clstg->send_monitor_bot($id, $msg);
    }
  }

  function team_leader_chat_ids($team) {
    $qry = "select chat_id from driver where driver_team='$team'";
    $ret = db_query($qry);
    $a = array();
    while ($row = db_fetch($ret)) {
      $chatid = $row['chat_id'];
      if (!$chatid) continue;
      $a[] = $chatid;
    }
    return $a;
  }

  function char($c) {
    $clstg = new telegram();
    return $clstg->char($c);
  }

  // apikey 갱신
  function update_appkey($phone_hash, $appkey) {
    $qry = "UPDATE driver SET apikey='$appkey', apikey_date=now()"
        ." where phone_hash='$phone_hash'";
    $ret = db_query($qry);
  }

  // 인증키 생성
  function authenticate($did, $phone_hash) {

    $hash = md5($pass);
    $qry = "SELECT * FROM driver WHERE did='$did' AND phone_hash='$phone_hash'";
    $row = db_fetchone($qry);

    if ($row) {
      $str = time().$phone_hash.$did;
      $token = md5($str);

      $driver_id = $row['id'];
      $this->apilog("인증키생성 $driver_id");

      $this->update_appkey($phone_hash, $token);
      return $token;

    } else {
      $this->apilog("인증키생성 실패 hash:$phone_hash");
      return false; // 실패
    }
  }

  // 운전자 정보 취득
  function _get_driver($sql_where) {
    $qry = "SELECT d.*"
  .", p.person_name, p.person_group, p.per_no person_id, p.person_level"
  ." FROM driver d"
  ." LEFT JOIN person p ON d.person_id=p.per_no"
  .$sql_where;
    $row = db_fetchone($qry);
    return $row;
  }
  function get_driver_by_id($driver_id) {
    $sql_where = " WHERE d.id='$driver_id'";
    $row = $this->_get_driver($sql_where);
    return $row;
  }
  function get_driver_by_chat_id($chat_id) {
    $sql_where = " WHERE d.chat_id='$chat_id'";
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

    $goyu = trim($goyu); // - 포함
    $goyu = preg_replace("/-/", "", $goyu);

    $tel = trim($tel); // - 미포함
    $tel = preg_replace("/-/", "", $tel);

    // 고유번호와 전화번호로 확인
    $qry = "SELECT * FROM driver WHERE driver_no='$goyu' and driver_tel='$tel'";
    $row = db_fetchone($qry);
    if (!$row) {
      $this->apilog('등록 실패');
      alert_log("등록 실패 *$goyu*, *$tel*", '등록실패');
      return array(false,"info not found *$goyu*, *$tel*"); // 정보가 없으면 실패
    }

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

    $this->apilog('운전자등록 성공');
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

  function is_driving_status($st) {
    if ($st == '운전중') return true;
    return false;
  }
  function is_emergency_status($st) {
    if ($st == '비상상황') return true;
    return false;
  }

  // 상태별로 간단한 통계
  function driver_summary($team='', &$sum) {

    $sql_where = " WHERE 1";
    if ($team && $team!='all' & $team!='전체') $sql_where .= " AND d.driver_team='$team'";

    $qry = "SELECT COUNT(*) count, d.driver_stat, Ds.DsName"
 ." FROM driver d"
 ." LEFT JOIN Ds ON d.driver_stat=Ds.Ds"
 .$sql_where
 ." GROUP BY d.driver_stat";
    $ret = db_query($qry);

    $sum = 0;
    $info = array();
    while ($row = db_fetch($ret)) {
       $ds = $row['DsName'];
       if (!$ds) $ds = 'Unknown';
       $count = $row['count'];
       $info[$ds] = $count;
       $sum += $count;
    }
    //dd($info);
    return $info;
  }

  function driver_summary_team() {
    $qry = "SELECT COUNT(*) count, d.driver_stat, Ds.DsName, d.driver_team"
 ." FROM driver d"
 ." LEFT JOIN Ds ON d.driver_stat=Ds.Ds"
 ." GROUP BY d.driver_stat, d.driver_team";
    $ret = db_query($qry);

    $info = array();
    while ($row = db_fetch($ret)) {
       $ds = $row['DsName'];
       if (!$ds) $ds = 'Unknown';
       $team = $row['driver_team'];
       $count = $row['count'];
       $info[$team][$ds] = $count;
    }
    return $info;
  }

  // 운전자가 현재 운행중일때, 운행기록의 인사정보를 바꾼다.
  function set_run_person($driver_id, $person_id) {
    $row = $this->get_driver_by_id($driver_id);
    if ($row['is_driving']) {
      //dd($row);
      $run_id = $row['run_id'];
      $qry = "update run set person_id='$person_id' where id='$run_id'";
      db_query($qry);
    }
  }

  function driver_all_teams() {
    return array('1팀', '2팀', '3팀','4팀','5팀','6팀','7팀',
      '공항팀','숙소1팀','숙소2팀','정비1팀','정비2팀','관리팀','운영팀');
  }

  function select_team_option($preset='') {
    $opt = '';
    $opt .= "<option value='all'>=선택=</option>";
    $list = $this->driver_all_teams();
    foreach ($list as $team) {
      $v = $team;
      $t = $team;
      if ($preset == $v) $sel = ' selected'; else $sel = '';
      $opt .= "<option value='$v'$sel>$t</option>";
    }
    return $opt;
  }

  // 운전자 상태 select 옵션
  function driver_status_option($preset='', $show_code=false) {
    $opt = '';
    $opt .= "<option value='all'>=전체=</option>";
    $list = $this->driver_all_status();

    $match = false;
    foreach ($list as $item) {
      list($ds, $dsname) = $item;

      if ($show_code) $title = "($ds) $dsname";
      else $title = "$dsname";

      if ($preset == $ds) {
        $sel = ' selected';
        $match = true;
      } else $sel = '';
      $opt .= "<option value='$ds'$sel>$title</option>";
    }
    if (!$match) {
      $opt .= "<option value='$preset' selected>$preset</option>";
    }
    $opt .= "<option value='unknown'>=미입력=</option>";
    return $opt;
  }

  // 운전자 위치 설정 (디버깅용)
  function set_driver_location($driver_id, $lat, $lng) {
    $row = $this->get_driver_by_id($driver_id);

    // 운전자 상태에 따라서 is_driving 결정
    $is_driving = $row['is_driving'];

    // 차량도 함께 위치 설정
    $car_id = $row['car_id'];
    if ($car_id) {
      $qry = "UPDATE carinfo SET lat='$lat',lng='$lng' WHERE id='$car_id'";
      //dd($qry);
      $ret = db_query($qry);
    }

    $s = array();
    $s[] = "lat='$lat'";
    $s[] = "lng='$lng'";
    $s[] = "udate=now()";
    $sql_set = " SET ".join(",", $s);

    $qry = "UPDATE driver $sql_set WHERE id='$driver_id'";
    //dd($qry);
    $ret = db_query($qry);
  }

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
    $this->apilog("운행시작 $driver_id");

    // run 정보를 새로 만든다
    $s = array();
    $s[] = "driver_id='$driver_id'";
    $s[] = "depart_from='$depart_from'";
    $s[] = "going_to='$going_to'";
    $s[] = "start_time=now()";
    $s[] = "is_driving=1";
    $s[] = "idate=now()";
    $s[] = "udate=now()";

    $person_id = $driver_row['person_id'];
    $s[] = "person_id='$person_id'";

    $sql_set = " SET ".join(",", $s);
    $qry = "INSERT INTO run $sql_set";
    $ret = db_query($qry);

    //  추가된 run_id 값
    $qry = "SELECT LAST_INSERT_ID() as id";
    $row = db_fetchone($qry);
    $run_id = $row['id'];

    // 해당 운전자의 다른 모든 run 기록은 is_driving=0으로 설정
    $qry = "UPDATE run SET is_driving=0 WHERE driver_id='$driver_id' AND id != '$run_id'";
    $ret = db_query($qry);

    // 전송주기는 개인별로 다름
    $interval = $driver_row['gperiod'];

    // run 정보를 driver 에 반영
    $qry = "UPDATE driver"
     ." SET run_id='$run_id'"
     .", is_driving=1"
     .", driver_stat='DS_DRIVE'"
     .", udate=now()"
     ." WHERE id='$driver_id'";
    $ret = db_query($qry);

    $name = $driver_row['driver_name'];
    $team = $driver_row['driver_team'];
    $tel = $driver_row['driver_tel'];
    $person_id = $driver_row['person_id'];
    $person_name = $driver_row['person_name'];
    $person_id = $driver_row['person_id'];

    // 목적지명
    $clslocation = new location();
    $going = $clslocation->id2name($going_to);
    $depart = $clslocation->id2name($depart_from);

    $now = get_now();

    $msg = "[출발] $name($team) \n"
     ."$depart ---> $going\n"
     ."출발시간: $now\n"
     ."인사: $person_name(No.$person_id)\n"
     ;
    alert_log($msg, '운행시작');

    $chat_id = $driver_row['chat_id'];
    $this->chat_id = $driver_row['chat_id'];
    $this->driver_row = $driver_row;
    $emoji = $this->char('start');
    $this->chat_notice($chat_id, $emoji.$msg); // 메신저로 알림
  }

  // 경유지 리스트
  function passby_list() {
    $cls = new location();
    $list = $cls->list_passby_locations();
    return $list;
  }

  // API at_location 에서 호출
  function passby_point_check($driver_id, $run_id, $lat, $lng) {
    $plist = $this->passby_list();
    //apilog($plist);

    foreach ($plist as $pitem) {
      //dd($pitem);
      $loc_id = $pitem['location_id'];

      $dist = sprintf("%d", distance($lat, $lng, $pitem['lat'], $pitem['lng'], 'K')*1000); // 단위 미터

      // 경유지 중 한곳이라도 걸리면 중단
      global $conf; $diff = $conf['pass_nearby']; // 반경 m 이내
      if ($dist < $diff) {
        //apilog("dist < diff");
        $this->passby_event($driver_id, $run_id, $loc_id, $lat, $lng, $dist);
        return true; // 경유지근처
      }
    }
    return false; // 근처 아님
  }


  // 차량이 경유지 근처를 지남
  function passby_event($driver_id, $run_id, $location_id, $lat, $lng, $dist) {
    $this->apilog("경유지근처 $driver_id, $run_id, $location_id, $lat, $lng, $dist");
    $qry = "select * from pass_event where run_id='$run_id' and location_id='$location_id'";
    $row = db_fetchone_api($qry);
    $ncount = $row['ncount'];

    if ($row) {

      $this->apilog("경유지근처 update $driver_id, $run_id, $location_id, $dist");
      $qry = "update pass_event"
         ." set ncount=ncount+1" // 카운트를 증가
         ." where run_id='$run_id' and location_id='$location_id'";
      db_query_api($qry);

      // update 할때마다 알람을 보내면 너무 많이 가게됨
      //$this->passby_notice($location_id, $ncount, $dist); // 알람

    } else {
      $this->apilog("경유지근처 insert $driver_id, $run_id, $location_id, $dist");
      $qry = "insert into pass_event"
         ." set driver_id='$driver_id', run_id='$run_id', location_id='$location_id'"
         .", lat1='$lng', lng1='$lng', time1=now(), dist1='$dist', ncount=1"
         .", idate=now()";
      db_query_api($qry);

      $this->passby_notice($location_id, $ncount=1, $dist); // 알람
    }
  }

  function passby_notice($location_id, $ncount, $dist) {
    global $conf; $diff = $conf['pass_nearby']; // 반경 m 이내
    $ref = "* 경유지 근처를 지날때 알려드립니다. (설정된 좌표 $diff 미터 이내 최초 진입시)";

    // 경유지 알림
    $emoji = $this->char('bell');
    $driver_row = $this->driver_row;
    //apilog($driver_row);
    $name = $driver_row['driver_name'];
    $team = $driver_row['driver_team'];
    $pname = $driver_row['person_name'];
    $pgroup = $driver_row['person_group'];
    $clsperson = new person(); 
    $plevel= $clsperson->get_level_string($driver_row['person_level']);
    $clsloc = new location();
    $title = $clsloc->location_name($location_id);
    $a = array("$emoji 경유지근처 알림 $ncount",
       "운전자:$name($team)",
       "경유지:$title ({$dist}m)",
       "탑승자: $pname(그룹:$pgroup, $plevel)",
       $ref);
    $cid = $this->chat_id;
    if ($cid) $this->chat_notice($cid, $a); // 메신저로 알림
  }

  // 이동 거리
  function traversed_distance($run_id) {
    $qry = "SELECT * FROM run_log WHERE run_id='$run_id'"
        ." and idate > date_sub(now(), interval 5 minute)"
        ." ORDER BY idate DESC";
    //$this->apilog($qry);
    $ret = db_query($qry);

    $list = array();
    while ($row = db_fetch($ret)) {
      $list[] = array($row['lat'], $row['lng']);
    }

    $plat = $plng = 0;
    $cnt = 0;
    $d = 0;
    foreach ($list as $p) {
      $cnt++;
      list($lat, $lng) = $p;
      if ($cnt > 2) {
        //$this->apilog("$lat, $lng, $plat, $plng");
        $d += distance($lat, $lng, $plat, $plng, "K");
      }
      $plat = $lat; $plng = $lng;
    }
    $dist = sprintf("%4.1f", $d * 1000); // 미터

    //$this->apilog("$dist 주행");
    return $dist;
  }

  // 운전자 위치 설정
  function at_location($driver_row, $driver_id, $run_id, $lat, $lng, &$elapsed) {
    if (!$run_id) return 'run_id is null';

    $this->chat_id = $driver_row['chat_id'];
    $this->driver_row = $driver_row;

    // 경유지 체크
    $this->passby_point_check($driver_id, $run_id, $lat, $lng);

    // run 정보 확인
    $qry = "SELECT r.*"
       .", UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(r.start_time) AS e"
       ." FROM run r "
       ." WHERE r.driver_id='$driver_id' and r.id='$run_id'";
    $row_run = db_fetchone($qry);
    if (!$row_run) return 'run is is invalid';
    $elapsed = $row_run['e'];

    // 차량도 함께 위치 설정
    $car_id = $driver_row['car_id'];
    if ($car_id) {
      $clscar = new carinfo();
      $clscar->set_position($car_id, $lat, $lng);
    }

    // 로그 기록
    //$this->run_log($driver_id, $run_id, $lat, $lng);
    $s = array();
    $s[] = "driver_id='$driver_id'";
    $s[] = "run_id='$run_id'";
    $s[] = "lat='$lat'";
    $s[] = "lng='$lng'";
    $s[] = "idate=now()";
    $sql_set = " SET ".join(",", $s);
    $qry = "INSERT INTO run_log $sql_set";
    $ret = db_query($qry);

    // 이동 거리 
    $dist5 = $this->traversed_distance($run_id);

    // run 정보 갱신
    $s = array();
    $s[] = "lat='$lat'";
    $s[] = "lng='$lng'";
    $s[] = "udate=now()";
    $s[] = "dist5='$dist5'";
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
    $s[] = "udate=NOW()";
    $sql_set = " SET ".join(",", $s);
    $qry = "UPDATE driver $sql_set where id='$driver_id'";
    $ret = db_query($qry);

  }

  // 최종 위치
  function get_last_position($run_id) {
    $qry = "SELECT r.* FROM run r WHERE r.id='$run_id' ORDER BY idate DESC LIMIT 0,1";
    $row = db_fetchone($qry);
    //dd($row);
    return array($row['lat'], $row['lng']);
  }

  // 운행 종료
  function finish_driving($driver_row, $driver_id, $run_id, &$elapsed) {
    $this->apilog("운행종료 $driver_id, $run_id");

    $this->chat_id = $driver_row['chat_id'];
    $this->driver_row = $driver_row;

    if (!$run_id) return 'run_id is null';

    // run 정보 확인
    $qry = "SELECT r.*"
       .", UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(r.start_time) AS e"
       ." FROM run r "
       ." WHERE r.driver_id='$driver_id' and r.id='$run_id'";
    $row_run = db_fetchone($qry);
    if (!$row_run) return 'run_id is invalid';
    $elapsed = $row_run['e'];
    $elapsed = sprintf("%d", $elapsed/60);

    $lat_e = $row_run['lat_e']; // 최종 좌표
    $lng_e = $row_run['lng_e'];

    #if ($lat_e || $lng_e) { // 최종 좌표 입력됨
    #  return '운행이 이미 종료되었습니다.';
    #}

    $going_to = $row_run['going_to'];
    $depart_from = $row_run['depart_from'];

    $clsloc = new location();
    $row_location = $clsloc->get_location($going_to);
    // 목적지명
    $going_to_title = $row_location['loc_title'];

    // 출발지명
    $depart_location = $clsloc->get_location($depart_from);
    $depart_from_title = $depart_location['loc_title'];

    $dlat = $driver_row['lat'];
    $dlng = $driver_row['lng'];
    if ($dlat && $dlng) {
      // 최종 좌표와 목적지까지 거리
      $dist1 = distance($row_location['lat'], $row_location['lng'], $dlat, $dlng, "K");
      $dist1 = sprintf("%3.1f", $dist1);
    } else $dist1 = '';

    // run 정보 갱신
    $s = array();
    $s[] = "end_time=NOW()";
    $s[] = "udate=NOW()";
    $s[] = "lat_e=lat"; // 최종 좌표
    $s[] = "lng_e=lng";
    $s[] = "is_driving=0";
    $sql_set = " SET ".join(",", $s);
    $qry = "UPDATE run $sql_set"
      ." WHERE id='$run_id' AND driver_id='$driver_id'";
    $ret = db_query($qry);

    // driver 에 반영
    $qry = "UPDATE driver"
      // run_id 는 지우지 않음 (최근 운행 기록을 조회하기 위해)
      ." SET is_driving=0, driver_stat='DS_STOP'"
      .", udate=now()"
      ." WHERE id='$driver_id'";
    $ret = db_query($qry);

    $name = $driver_row['driver_name'];
    $team = $driver_row['driver_team'];
    $person_id = $driver_row['person_id'];
    $person_name = $driver_row['person_name'];

    $now = get_now();

    global $conf;
    $url = $conf['iamhere_record_url'];
    $a = array();
    $a[] = "[도착] $name($team)";
    $a[] = "도착시간: $now";
    $a[] = "출발지: $depart_from_title";
    $a[] = "목적지: $going_to_title";
    $a[] = "인사: $person_name(No.$person_id)";
    $a[] = "목적지와 거리: {$dist1}km";
    $a[] = "소요시간: {$elapsed}분";
    $a[] = "운행기록: $url/home.php?mode=map&id=$run_id";
    $msg = join("\n", $a);
    alert_log($msg, '운행종료');

    $emoji = $this->char('stop');
    $a[0] = $emoji.$a[0];
    $chat_id = $driver_row['chat_id'];
    $this->chat_notice($chat_id, $a); // 메신저로 알림
  }

  // VIP 변경
  // API
  function set_person($driver_row, $per_no) {
    $driver_id = $driver_row['id'];
    // 운행중에는 변경 불가?
    #if ($driver_row['is_driving']) {
    #  return 'cannot change person during driving';
    #}

    $qry = "UPDATE driver SET person_id='$per_no' WHERE id='$driver_id'";
    $ret = db_query($qry);
  }


  // 현재 지정된 VIP 정보
  // API
  function query_person($driver_row) {
    $clsperson = new person();
    $clsperson->person_information($per_no);
  }


  // API
  // 비상상황
  function do_emergency($driver_row, $code) {
    $driver_id = $driver_row['id'];
    $name = $driver_row['driver_name'];
    $team = $driver_row['driver_team'];
    $tel = $driver_row['driver_tel'];

    $e_name = $this->emergency_code2name($code);

    $qry = "UPDATE driver SET driver_stat='DS_EMERGEN', emergency='$code' WHERE id='$driver_id'";
    $ret = db_query($qry);

    $msg = "비상상황 [$code/$e_name] $name($team) \n[ Tel: $tel ]";
    alert_log($msg, '긴급');

    $emoji = $this->char('siren');
    $a = array(); $a[] = "===================="; $a[] = $emoji.$msg; $a[] = "====================";
    $chat_id = $driver_row['chat_id'];
    $this->chat_notice($chat_id, $a); // 메신저로 알림
  }

  // API
  // 비상상황 해제
  function exit_emergency($driver_row) {
    $driver_id = $driver_row['id'];
    $name = $driver_row['driver_name'];
    $team = $driver_row['driver_team'];

    $qry = "UPDATE driver SET driver_stat='DS_STOP', emergency='' WHERE id='$driver_id'";
    $ret = db_query($qry);

    $msg = "비상상황해제 $name ($team)";
    alert_log($msg, '긴급');

    $emoji = $this->char('ok');
    $a = array(); $a[] = "===================="; $a[] = $emoji.$msg; $a[] = "====================";
    $chat_id = $driver_row['chat_id'];
    $this->chat_notice($chat_id, $a); // 메신저로 알림
  }


  // $sql_select = $clsdriver->sql_select_run_1();
  // $sql_join   = $clsdriver->sql_join_##($pj);
  //   $qry = $sql_select.$sql_from.$sql_join.$sql_where;
  function sql_select_run_1() {
    $sql_select = "SELECT d.driver_name, d.id driver_id, d.driver_team"
    .", r.id run_id, r.is_driving run_driving"
    .", c.car_no"
    .", Ds.DsName"
    .", IF(d.rflag,'O','X') _rflag"
    .", l1.loc_title loc1"
    .", l2.loc_title loc2"
    .", r.start_time "
    .", r.end_time"
    .", TIME(r.start_time) stime"
    .", TIME(r.end_time) etime"
    .", p.id person_id, p.person_name, p.person_group"
    ;
    return $sql_select;
  }
  function sql_join_common_1() {
    $sql_join = ''
    ." LEFT JOIN carinfo c ON d.car_id=c.id"
    ." LEFT JOIN Ds ON d.driver_stat=Ds.Ds"
    ." LEFT JOIN location l1 ON r.depart_from=l1.id"
    ." LEFT JOIN location l2 ON r.going_to=l2.id"
    ;
    return $sql_join;
  }
  function sql_join_2() {
    $sql_join = ''
       ." LEFT JOIN run r ON d.run_id=r.id"
       ." LEFT JOIN person p ON d.person_id=p.per_no" // per_no 와 조인
       .$this->sql_join_common_1();
    return $sql_join;
  }
  function sql_join_3() {
    $sql_join = ''
       ." LEFT JOIN driver d ON r.driver_id=d.id"
       ." LEFT JOIN person p ON r.person_id=p.per_no" // per_no 와 조인
       .$this->sql_join_common_1();
    return $sql_join;
  }
  function sql_join_4() {
    $sql_join = ''
       ." LEFT JOIN run r ON d.run_id=r.id"
       ." LEFT JOIN person p ON d.person_id=p.per_no" // per_no 와 조인
       .$this->sql_join_common_1();
    return $sql_join;
  }

  function emergency_list() {
    $info = array();
    $info['EMER1'] = '차량고장';
    $info['EMER2'] = '접촉사고';
    $info['EMER9'] = '기타사항';
    return $info;
  }
  function emergency_code2name($code) {
    $list = $this->emergency_list();
    return $list[$code];
  }

  function driver_status_html(&$row) {
    $em = $row['emergency'];
    $ds = $row['DsName'];
    if ($this->is_driving_status($ds)) $ds = "<span class='drs ds_driving'>$ds</span>";
    else if ($this->is_emergency_status($ds)) $ds = "<span class='drs ds_emergency'>$ds($em)</span>";
    else $ds = "<span class='drs ds_not_driving'>$ds</span>";
    return $ds;
  }

  function run_log_count($run_id) {
    $qry = "select count(*) count from run_log where run_id='$run_id'";
    $row = db_fetchone($qry);
    return $row['count'];
  }
  function run_count($driver_id) {
    $qry = "select count(*) count from run where driver_id='$driver_id'";
    $row = db_fetchone($qry);
    return $row['count'];
  }

}; // class

?>
