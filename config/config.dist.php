<?php

  # database connection
  $conf['dbhost']   = "localhost";
  $conf['dbuser']   = "root";
  $conf['dbpasswd'] = "******";
  $conf['dbname']   = "cariamhere";
  $conf['dbport']   = 3306;
  $conf['dbsocket']   = "/tmp/mysql.sock";

  $conf['google_map_key'] = "#############################";
  $conf['daum_map_key']   = "#############################";

  $site = "car.example.net";

  $conf['api_endpoint'] = "http://$site/api/do.php";
  $conf['notice_url'] = "http://$site/notice/list.php";
  $conf['update_url'] = "http://$site/notice/update.php";

  // 위치정보 전송 주기 (일단 설정파일에서)
  $conf['interval_driving'] = 30;
  $conf['interval_waiting'] = 300;


?>
