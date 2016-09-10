<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  $source_title = '정보 다운로드';

  $debug = 1;
  $debug = 0;

### {{{


### }}}

### {{{
if ($mode == 'car_and_driver') {
  if ($form['preview']) $debug = 1;
  download_head('carmax', $debug);

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
  download_th('폰기종');
  download_th('텔레그램연동');
  download_th('API최종접속');
  print("</tr>");

  $ret = db_query($qry);
  while ($row = db_fetch($ret)) {
  print("<tr>");
  download_td($row['own1']);
  download_td($row['own2']);
  download_td($row['own3']);
  download_td("'".$row['own4']);
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
  download_td("'".$row['driver_tel']);
  download_td("'".$row['driver_no']);
  download_td($row['driver_team']);
  download_td($row['driver_id']);
  download_td($row['phone_os']);
  download_td($row['chat_id']);
  download_td($row['apikey_date']);
  print("</tr>");
  }

  download_tail();
  exit;
}

if ($mode == 'run') {
  if ($form['preview']) $debug = 1;
  download_head('carmax', $debug);

  $qry = "select r.*
, d.driver_name
, d.driver_team
, d.driver_no
, round((UNIX_TIMESTAMP(r.end_time) - UNIX_TIMESTAMP(r.start_time))/60) AS elapsed
, l1.loc_title loc1
, l2.loc_title loc2
, p.person_name
, p.person_group
, p.person_hotel
 from run r
 left join driver d on r.driver_id=d.id
 left join location l1 on r.depart_from=l1.id
 left join location l2 on r.going_to=l2.id
 left join person p on r.person_id=p.id
 ";

  print("<tr>");
  download_th('번호');
  download_th('ID');
  download_th('출발시간');
  download_th('도착시간');
  download_th('걸린시간(분)');
  download_th('출발지');
  download_th('목적지');
  download_th('person_id');
  download_th('person_name');
  download_th('person_group');
  download_th('person_hotel');
  download_th('운전자');
  download_th('팀');
  download_th('운전자번호');
  print("</tr>");

  $ret = db_query($qry);

  $cnt = 0;
  while ($row = db_fetch($ret)) {
  $cnt++;
  print("<tr>");
  download_td($cnt);
  download_td($row['id']);
  download_td($row['start_time']);
  download_td($row['end_time']);
  download_td($row['elapsed']);
  download_td($row['loc1']);
  download_td($row['loc2']);
  download_td($row['person_id']);
  download_td($row['person_name']);
  download_td($row['person_group']);
  download_td($row['person_hotel']);
  download_td($row['driver_name']);
  download_td($row['driver_team']);
  download_td($row['driver_id']);
  print("</tr>");
  }

  download_tail();
  exit;
}

if ($mode == 'person') {
  if ($form['preview']) $debug = 1;
  download_head('carmax', $debug);

  $qry = "select p.*, Nat.nname
 from person p
 left join Nat on p.person_nation=Nat.nnum";

  print("<tr>");
  download_th('번호');
  download_th('ID');
  download_th('인사번호');
  download_th('공식한글이름');
  download_th('행사계층');
  download_th('소속및직책');
  download_th('레벨');
  download_th('호텔');
  download_th('국가코드');
  download_th('국가명');
  print("</tr>");

  $ret = db_query($qry);

  $cnt = 0;
  while ($row = db_fetch($ret)) {
  $cnt++;
  //dd($row);
  print("<tr>");
  download_td($cnt);
  download_td($row['id']);
  download_td($row['per_no']);
  download_td($row['person_name']);
  download_td($row['person_group']);
  download_td($row['person_position']);
  download_td($row['person_level']);
  download_td($row['person_hotel']);
  download_td($row['person_nation']);
  download_td($row['nname']);
  print("</tr>");
  }

  download_tail();
  exit;
}


### }}}

  MainPageHead($source_title);

  ParagraphTitle('차량/운전자');
  print<<<EOS
<form name='form' action='$env[self]' method='post'>
<input type='hidden' name='mode' value=''>
<label><input type='checkbox' name='preview' checked>미리보기</label>
<input type='button' value='다운로드' onclick='sf_1()' class='btn btn-warning'>
<input type='button' value='업로드' onclick='up1()' class='btn btn-primary'>
</form>
<script>
function sf_1() { document.form.mode.value = 'car_and_driver'; document.form.submit(); }
function up1() { urlGo("/upload.php"); }
</script>
EOS;

  ParagraphTitle('운행기록');
  print<<<EOS
<form name='form2' action='$env[self]' method='post'>
<input type='hidden' name='mode' value=''>
<label><input type='checkbox' name='preview' checked>미리보기</label>
<input type='button' value='다운로드' onclick='sf_2()' class='btn btn-warning'>
</form>
<script>
function sf_2() { document.form2.mode.value = 'run'; document.form2.submit(); }
</script>
EOS;

  ParagraphTitle('VIP인사');
  print<<<EOS
<form name='form3' action='$env[self]' method='post'>
<input type='hidden' name='mode' value=''>
<label><input type='checkbox' name='preview' checked>미리보기</label>
<input type='button' value='다운로드' onclick='sf_3()' class='btn btn-warning'>
<input type='button' value='업로드' onclick='up3()' class='btn btn-primary'>
</form>
<script>
function sf_3() { document.form3.mode.value = 'person'; document.form3.submit(); }
function up3() { var url = "/person.php?mode=add2"; urlGo(url); }
</script>
EOS;

  MainPageTail();
  exit;

?>
