<?php

  include("./path.php");
  include("$env[prefix]/inc/common.php");

  $form = $_REQUEST;
  //print_r($form);

  $lat = $form['lat'];
  $lng = $form['lng'];

  $s = array();
  $s[] = "lat='$lat'";
  $s[] = "lng='$lng'";
  $s[] = "idate=now()";
  $s[] = "udate=now()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO carinfo $sql_set";
  //print("$lat,$lng,$qry");

  $ret = db_query($qry);
  print db_error();

?>
