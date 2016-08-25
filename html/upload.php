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

### }}}

### {{{
// 저장하기
if ($mode == 'add2do') {

  $content = $form['content'];
  _split_list($rows, $content);

  foreach ($rows as $line) {
    $line = trim($line);
    if (!$line) continue;
    _split_cols($cols, $line);

    if ($debug) dd($cols);

    $s = array();
    $s[] = "own1='{$cols[0]}'";
    $s[] = "own2='{$cols[1]}'";
    $s[] = "own3='{$cols[2]}'";
    $s[] = "own4='{$cols[3]}'";
    $s[] = "own5='{$cols[4]}'";
    $s[] = "own6='{$cols[5]}'";
    $s[] = "own7='{$cols[6]}'";
    $s[] = "own8='{$cols[7]}'";
    $s[] = "own9='{$cols[8]}'";
    $s[] = "own10='{$cols[9]}'";
    $sql_set = " SET ".join(",", $s);
    $qry = "INSERT INTO carinfo $sql_set";
    if ($debug) dd($qry);
    else $ret = db_query($qry);

    $s[] = "drv1='{$cols[10]}'";
    $s[] = "drv2='{$cols[11]}'";
    $s[] = "drv3='{$cols[12]}'";
    $s[] = "drv4='{$cols[13]}'";
    $s[] = "drv5='{$cols[14]}'";
    $s[] = "drv6='{$cols[15]}'";
    $sql_set = " SET ".join(",", $s);
    $qry = "INSERT INTO driver $sql_set";
    if ($debug) dd($qry);
    else $ret = db_query($qry);
  }

  $qry = "UPDATE driver SET driver_name=drv3,driver_tel=drv5,driver_no=drv6 WHERE driver_name=''";
  if ($debug) dd($qry);
  else $ret = db_query($qry);

  $qry = "UPDATE carinfo SET car_no=own6, car_model=own5, car_color=own8 WHERE car_no=''";
  if ($debug) dd($qry);
  else $ret = db_query($qry);

  print<<<EOS
<a href='/home.php'>업로드 완료. 돌아가기</a>
EOS;
  exit;
}

### }}}

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  print<<<EOS
<form name='form' action='$env[self]' method='post'>

<a href='car_form.xlsx'>양식엑셀파일 다운받기</a>

<p> 아래 내용을 지우고 엑셀양식 파일의 내용을 복사해서 붙이세요.

<input type='hidden' name='mode' value='add2b'>
EOS;

  // 차량정보 (지파명~연식)
  // 운전자 정보 (지파명~고유번호)
  $content = $form['content'];
  if (!$content) $content =<<<EOS
지파명	교회명	실소유자명	연락처	모델명	차량번호	차종	색상	배기량	연식	지파	교회	이름	연령	연락처	고유번호
요한	과천	임O환	2614-1440	BMW330d	31서9814	S	검정	3000	2010	요한	과천	임세환	37	2614-1440	00010101-00001
요한	과천	양O환	5272-6550	토요타 켐리	26구0837	S	은색	2500	2016	요한	과천	양명환	54	5272-6550	00010101-00002
요한	과천	이O완	2343-1440	BMW730D	67두3733	S	회색	3500	2014	요한	과천	김용곤	34	9429-5962	00010101-00003
요한	과천	김O수	7135-7532	BMW735i	47오7982	S	흰색	3500	2004	요한	과천	이성구	52	8419-4567	00010101-00004
요한	과천	박O홍	8351-1331	BMW X5	14수1418	S	은색	3000	2004	요한	과천	최창렬	53	9563-0787	00010101-00005
EOS;

 print<<<EOS
<textarea rows='10' cols='80' name='content' style='width:100%' onclick='this.select()'>
$content
</textarea>

<input type='button' value='미리보기' onclick='sf_1()'>
<input type='button' value='저장하기' onclick='sf_2()'>
</form>

<script>
function sf_1() { document.form.mode.value = ''; document.form.submit(); }
function sf_2() { document.form.mode.value = 'add2do'; document.form.submit(); }
</script>
EOS;

  $content = $form['content'];
  _split_list($rows, $content);

  print<<<EOS
<table class='table table-striped'>
<tr>
<th colspan='10'>차량정보</th>
<th colspan='6'>운전자정보</th>
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
    for ($i = 0; $i < 16; $i++) {
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
