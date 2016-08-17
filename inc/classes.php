<?php

  include_once("$env[path_include]/class.carinfo.php");
  #include_once("$env[path_include]/class.driver.php");
  include_once("$env[path_include]/class.location.php");
  include_once("$env[path_include]/class.person.php");
  include_once("$env[path_include]/class.role.php");
  include_once("$env[path_include]/class.driver.php");

  $carObj = new carinfo();
  $driverObj = new driver();
  $personObj = new person();
  $roleObj = new role();
  $locationObj = new location();

?>
