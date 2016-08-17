<?php

  include("./path.php");
  include("$env[prefix]/inc/common.php");

  include ("settings.php");

  $regID = strip_tags($_POST["regID"]);
  $unityID = strip_tags($_POST["user"]);
  $OS = strip_tags($_POST["OS"]);
  $midx = "0";
  if($_POST["midx"]) {
    $midx = strip_tags($_POST["midx"]);
  }

  // Register user-regID in DB
  // check if unity ID is already in the database. 
  // If so, delete it and store it again (useful in situations where you may have different unityIDs linked to the same device
  $query="SELECT * FROM driver WHERE pushkey = '$unityID' or id = '$midx' ";
  $result = db_query($query);
  if(db_affected_rows() > 0) {
    $query="update driver set pushkey = '', did = ''  WHERE pushkey ='$unityID' or id = '$midx' ";
    db_query($query);
    store_user($unityID,$regID,$OS,$midx);
  } else {
    store_user($unityID,$regID,$OS,$midx);
  }
  echo "0";

  function store_user($user,$regID,$OS,$midx) {
    $query     = "update driver set did = '$regID', pushkey = '$user', phone_os = '$OS' where id = '$midx'";
    //if ($midx)
    //  $query     = "INSERT INTO ECPN_table (deviceID, unityID, os, midx) VALUES ('$regID','$user','$OS','$midx')";
  //else
    //  $query     = "INSERT INTO ECPN_table (deviceID, unityID, os) VALUES ('$regID','$user','$OS')";
    db_query($query);
  }


?>
