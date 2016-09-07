<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  $source_title = '운전자/차량 정보 업로드';

  $debug = 1;
  $debug = 0;

### {{{

function _split_list(&$rows, $content) {
  $rows = preg_split("/\n/", $content);
}

function _split_cols(&$cols, &$line) {
  $cols = preg_split("/[,\t]/", $line);
}

function _dbq($qry) {
  global $debug;
  if ($debug) dd($qry);
  $ret = db_query($qry);
}

### }}}

### {{{
// 저장하기
if ($mode == 'add2do') {

  if ($form['ovwr'] == '1') { // 덮어쓰기
    $overwrite = true;
  } else if ($form['ovwr'] == '2') { // 추가하기
    $overwrite = false;
  } else die('오류');
//dd($form); exit;

  $content = $form['content'];
  _split_list($rows, $content);

  $count = 0;
  foreach ($rows as $line) {
    $line = trim($line);
    if (!$line) continue;
    _split_cols($cols, $line);

    if ($debug) dd($cols);

    $own1 = trim($cols[0]); // 지파명      0
    $own2 = trim($cols[1]); // 교회명      1
    $own3 = trim($cols[2]); // 실소유자    2
    $own4 = trim($cols[3]); // 연락처      3
    $own5 = trim($cols[4]); // 모델명      4
    $own6 = trim($cols[5]); // 차량번호    5
    $car_no = $own6;
    $own7 = trim($cols[6]); // 차종        6
    $own8 = trim($cols[7]); // 색상        7
    $own9 = trim($cols[8]); // 배기량      8
    $own10= trim($cols[9]); // 연식        9

    $drv1 = trim($cols[10]); // 지파        10
    $drv2 = trim($cols[11]); // 교회        11
    $drv3 = trim($cols[12]); // 이름        12
    $drv4 = trim($cols[13]); // 연령        13
    $drv5 = trim($cols[14]); // 연락처      14
    $drv6 = trim($cols[15]); // 고유번호    15
    $driver_id = trim($cols[16]); // DB번호 16
    $drv7 = trim($cols[17]); // 팀          17

    $s = array();
    $s[] = "own1='{$own1}'";
    $s[] = "own2='{$own2}'";
    $s[] = "own3='{$own3}'";
    $s[] = "own4='{$own4}'";
    $s[] = "own5='{$own5}'";
    $s[] = "own6='{$own6}'";
    $s[] = "own7='{$own7}'";
    $s[] = "own8='{$own8}'";
    $s[] = "own9='{$own9}'";
    $s[] = "own10='{$own10}'";

    $s1 = $s;
    $s1[] = "car_no='{$own6}'"; // 차량번호
    $s1[] = "car_model='{$own5}'"; // 모델명
    $s1[] = "car_color='{$own8}'"; // 색상
 
    if ($overwrite) {
      $qry = "select * from carinfo where car_no='$car_no'";
      $row = db_fetchone($qry);
      if (!$row) die("차량 $car_no 가 등록되어 있지 않습니다.");
      $car_id = $row['id'];

      $s1[] = "driver_id ='{$driver_id}'";
      $sql_set = " SET ".join(",", $s1);
      $qry = "UPDATE carinfo"
       .$sql_set
       ." WHERE id='$car_id'";
      $ret = _dbq($qry);

    } else {

      $sql_set = " SET ".join(",", $s);
      $qry = "INSERT INTO carinfo $sql_set";
      $ret = _dbq($qry);

      $qry = "SELECT LAST_INSERT_ID() as id";
      if ($debug) dd($qry);
      $row = db_fetchone($qry);
      $car_id = $row['id'];
    }

    $s[] = "drv1='{$drv1}'";
    $s[] = "drv2='{$drv2}'";
    $driver_name = $drv3;
    if ($driver_name == '') die('운전자 이름 미입력');
    $s[] = "drv3='{$driver_name}'";
    $driver_cho = cho_hangul($driver_name);
    $s[] = "driver_cho='{$driver_cho}'"; // driver_cho
    $s[] = "drv4='{$drv4}'";
    $tel = $drv5;
    if (strlen($tel) == 9) $tel = "010-$tel";
    $s[] = "drv5='{$tel}'"; // 전화번호
    $s[] = "drv6='{$drv6}'"; // 고유번호
    $s[] = "drv7='{$drv7}'"; // 팀

    if ($overwrite) {

      $s[] = "car_id='{$car_id}'";
      $s[] = "driver_name='$driver_name'";
      $s[] = "driver_tel='$tel'";
      $s[] = "driver_no='$drv6'";
      //$s[] = "driver_stat='DS_STOP'";
      $s[] = "driver_team='$drv7'";
      $sql_set = " SET ".join(",", $s);

      $qry = "UPDATE driver"
       .$sql_set
      ." where id='$driver_id'";
      if ($debug) dd($qry);
      $ret = db_query($qry);

      $qry = "UPDATE carinfo"
       ." SET driver_id='$driver_id', car_no=own6, car_model=own5, car_color=own8"
       ." WHERE id='$car_id'";
      if ($debug) dd($qry);
      $ret = db_query($qry);

    } else {

      $s[] = "id='{$driver_id}'";
      $sql_set = " SET ".join(",", $s);
      $qry = "INSERT INTO driver $sql_set";
      if ($debug) dd($qry);
      $ret = db_query($qry);

    }

    $count++;
  }

  // 전화번호에서 - 제거
  $qry = "update driver set driver_tel = concat( substring(driver_tel,1,3), substring(driver_tel,5,4), substring(driver_tel,10,4)) where length(driver_tel)=13;";
  if ($debug) dd($qry);
  $ret = db_query($qry);

  // 고유번호에서 - 제거
  $qry = "update driver set driver_no=concat(substring(driver_no,1,8), substring(driver_no,10,5)) where length(driver_no)=14";
  if ($debug) dd($qry);
  $ret = db_query($qry);

  print<<<EOS
<p>$count 건 완료.
<p><a href='/home.php'>업로드 완료. 돌아가기</a>
EOS;
  exit;
}

if ($mode == 'down') {

  download_head('carmax', $debug=0);

  $qry = "select d.*,c.*
 ,d.id driver_id
 from driver d
 left join carinfo c on d.car_id=c.id";

  print("<tr>");
  download_th('지파');
  download_th('교회');
  download_th('차량소유자');
  download_th('연락처');
  download_th('차종');
  download_th('차량번호');
  download_th('모델');
  download_th('색상');
  download_th('배기량');
  download_th('연식');
  download_th('지파');
  download_th('교회');
  download_th('운전자');
  download_th('나이');
  download_th('전화번호');
  download_th('고유번호');
  download_th('팀');
  download_th('운전자번호');
  print("</tr>");

  $ret = db_query($qry);
  while ($row = db_fetch($ret)) {
  print("<tr>");
  download_td($row['own1']);
  download_td($row['own2']);
  download_td($row['own3']);
  download_td($row['own4']);
  download_td($row['own5']);
  download_td($row['car_no']);
  download_td($row['own7']);
  download_td($row['own8']);
  download_td($row['own9']);
  download_td($row['own10']);
  download_td($row['drv1']);
  download_td($row['drv2']);
  download_td($row['driver_name']);
  download_td($row['drv4']);
  download_td($row['driver_tel']);
  download_td($row['driver_no']);
  download_td($row['driver_team']);
  download_td($row['driver_id']);
  print("</tr>");
  }

  download_tail();
  exit;
}

### }}}

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  print<<<EOS
<form name='form' action='$env[self]' method='post'>

<p> 아래 내용을 지우고 엑셀양식 파일의 내용을 복사해서 붙이세요.

<input type='hidden' name='mode' value='add2b'>
EOS;

  // 차량정보 (지파명~연식)
  // 운전자 정보 (지파명~고유번호)
  $content = $form['content'];
  if (!$content) $content =<<<EOS
지파명	교회명	실소유자명	연락처	모델명	차량번호	차종	색상	배기량	연식	지파	교회	이름	연령	연락처	고유번호	번호
EOS;

 print<<<EOS
<textarea rows='10' cols='80' name='content' style='width:100%' onclick='this.select()'>
$content
</textarea>

<input type='button' value='다운로드' onclick='sf_down()' class='btn btn-warning'>
<input type='button' value='미리보기' onclick='sf_1()' class='btn btn-primary'>
<input type='button' value='저장하기' onclick='sf_2()' class='btn btn-primary'>
<label><input type='radio' name='ovwr' value='1'>덮어쓰기</label>
<label><input type='radio' name='ovwr' value='2' checked>추가하기</label>
</form>

<script>
function sf_1() { document.form.mode.value = ''; document.form.submit(); }
function sf_2() { document.form.mode.value = 'add2do'; document.form.submit(); }
function sf_down() { document.form.mode.value = 'down'; document.form.submit(); }
</script>
EOS;

  $content = $form['content'];
  _split_list($rows, $content);

  print<<<EOS
<table class='table table-striped'>
<tr>
<th colspan='10'>차량정보</th>
<th colspan='7'>운전자정보</th>
</tr>

<tr>
<th>지파</th>
<th>교회</th>
<th>실소유자</th>
<th>연락처</th>
<th>모델명</th>
<th>차량번호</th>
<th>차종</th>
<th>색상</th>
<th>배기량</th>
<th>연식</th>
<th>지파</th>
<th>교회</th>
<th>운전자이름</th>
<th>연력</th>
<th>연락처</th>
<th>고유번호</th>
<th>번호</th>
</tr>
EOS;

  $cnt = 0;
  foreach ($rows as $line) {
    $line = trim($line);
    if (!$line) continue;
    _split_cols($cols, $line);
    if ($cols[0] == '지파명') continue;

    $cnt++;
    print("<tr>");
    for ($i = 0; $i < 17; $i++) {
      print("<td>{$cols[$i]}</td>");
    }
    print("</tr>");
  }
  print<<<EOS
</table>
총 $cnt 건
EOS;

  MainPageTail();
  exit;

?>
