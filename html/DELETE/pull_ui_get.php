<?php

  include("./path.php");
  include("$env[prefix]/inc/common.php");


  $qry = "SELECT * FROM carinfo ORDER BY idate DESC LIMIT 1";
  $row = db_fetchone($qry);
  //print_r($row);

  $lat = $row['lat'];
  $lng = $row['lng'];

  $info = array('lat'=>$lat, 'lng'=>$lng);
  $str = json_encode($info);
  print $str;


?>
