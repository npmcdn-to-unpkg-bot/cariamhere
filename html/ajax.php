<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  include_once("$env[prefix]/inc/class.role.php");
  include_once("$env[prefix]/inc/class.person.php");
  include_once("$env[prefix]/inc/class.user.php");
  include_once("$env[prefix]/inc/class.carinfo.php");

  $objCar = new carinfo();
  $objUser = new user();
  $objPerson = new person();

  $debug = $form['debug'];
  if ($debug) $debug = true; else $debug = false;


// 차량 정보 얻기
// http://carmaxscj.cafe24.com/ajax.php?mode=car_status&debug=1
if ($mode == 'car_status') {

  $opt = array();
  $opt['좌표정보있음'] = true;
  if ($form['driving']) $opt['운행중인차량'] = true;

  $data = $objCar->list_car($sql_where='', $debug, $opt);
  if ($debug) dd($data);

  $info = array();

  foreach ($data as $row) {
    $id = $row['car_id'];
    $car_no = $row['car_no'];
    $lat = $row['lat'];
    $lng = $row['lng'];

    $item = array();
    $k = 'id';     $item[$k] = $row['car_id'];
    $k = 'car_no'; $item[$k] = $row[$k];
    $k = 'lat';    $item[$k] = $row[$k];
    $k = 'lng';    $item[$k] = $row[$k];
    $k = 'driver_id'; $item[$k] = $row[$k];
    $k = 'car_model'; $item[$k] = $row[$k];
    $k = 'user_name'; $item[$k] = $row[$k];
    $k = 'status_code'; $item[$k] = $row[$k];
    $k = 'status_name'; $item[$k] = $row[$k];
    $k = 'des_name1'; $item[$k] = $row[$k];
    $k = 'dep_name1'; $item[$k] = $row[$k];
    $info[] = $item;
  }
  if ($debug) dd($info);

  print json_encode($info);
  exit;
}

  exit;

?>
