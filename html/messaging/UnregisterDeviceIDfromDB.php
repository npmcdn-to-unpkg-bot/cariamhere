<?php

  include("./path.php");
  include("$env[prefix]/inc/common.php");

  include ("settings.php");

  $regID = strip_tags($_POST["regID"]);

  // Remove deviceID from DB
  $query="SELECT * FROM driver WHERE did = '$regID'";
  $result=db_affetcted_rows($query);
  if(db_numrows($result) > 0) {
    $query="update driver set did = '' WHERE did ='$regID'";
    db_query($query);
  }
  echo "0";

  //mysql_close();

?>
