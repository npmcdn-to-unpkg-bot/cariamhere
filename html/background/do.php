<?php

// 백그라운드 서비스 처리

  include_once("../path.php");
  include_once("$env[prefix]/inc/common.background.php");
  include_once("$env[prefix]/inc/class.driver.php");
  include_once("$env[prefix]/inc/class.location.php");

### {{{
// 운전중인 운전자
function list_driving_drivers(&$info) {
  $clsdriver = new driver();

  $qry = "SELECT d.id, d.run_id, d.driver_name, r.going_to, loc2.loc_title destination"
    ." FROM driver d"
    ." LEFT JOIN run r ON d.run_id=r.id"
    ." LEFT JOIN location loc2 ON r.going_to=loc2.id"
    ." WHERE d.is_driving=1";
  $ret = db_query($qry);

  $info = array();
  while ($row = db_fetch($ret)) {
    $driver_id = $row['id'];
    $run_id = $row['run_id'];

    list($lat, $lng) = $clsdriver->get_last_position($run_id);

    $info[] = array(
      'driver_id'=>$driver_id,
      'run_id'=>$run_id,
      'driver_name'=>$row['driver_name'],
      'destination'=>$row['destination'],
      'lat'=>$lat, 'lng'=>$lng
    );
  }
}

// 경유지 리스트
function passby_points() {
  $cls = new location();
  $list = $cls->list_passby_locations();
  return $list;
}

### }}}

  list_driving_drivers($dlist);
  //dd($dlist);

  $dcount = count($dlist);
  //if ($dcount > 100) alert_log("운전중인 운전자 $dcount 명", '');

  $plist = passby_points();
  //dd($plist);

  foreach ($dlist as $ditem) {
    //dd($ditem);
    $driver_id = $ditem['driver_id'];
    $driver_name = $ditem['driver_name'];
    $run_id = $ditem['run_id'];
    $destination = $ditem['destination'];

    foreach ($plist as $pitem) {
      //dd($pitem);
      $loc_id = $pitem['location_id'];
      $loc_title = $pitem['title'];

      $dist = distance($ditem['lat'], $ditem['lng'], $pitem['lat'], $pitem['lng'], 'K');
      $dist = sprintf("%3.1f", $dist);
      $msg = "운전자:$driver_name, 목적지:$destination, 경유지:$loc_title, {$dist}km";

      $diff = $conf['passby_point_nearby']; // 반경 km 이내

      if ($dist <= $diff) {
        dd("<p>$msg");
        alert_log("[B] $msg", '경유지근처');
      }
    }
  }

  exit;

?>
