<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  $api_endpoint = $conf['api_endpoint'];

### {{{

if ($mode == 'apicall') {
  //dd($form);

  unset($form['mode']);
  $js = json_encode($form);
  //dd($js);
  $data = urlencode($js);
  //dd($data);

  $_SESSION['saved_appkey'] = $form['appkey'];

  PopupPageHead('Mobile API');
  print<<<EOS
<style>
pre {outline: 1px solid #ccc; padding: 5px; margin: 5px; }
.string { color: green; }
.number { color: darkorange; }
.boolean { color: blue; }
.null { color: magenta; }
.key { color: red; }
</style>

<p>Request: $js
<p>Result:
<pre>
<div id='result1'></div>
<div id='result2'></div>
</pre>

<script>

function output(inp) {
   document.body.appendChild(document.createElement('pre')).innerHTML = inp;
}

function syntaxHighlight(json) {
    if (typeof json != 'string') {
         json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}

$(function() {
  $.ajax({
    method: "POST",
    url: "$api_endpoint",
    data: JSON.stringify($js)
  })
  .done(function( msg ) {
    console.log( "data : " + msg );
    $('#result1').html(msg);

    try {
      var obj = JSON.parse(msg);
      //console.log(obj);
      var str = JSON.stringify(obj, undefined, 4);
      //console.log(str);

      var s = syntaxHighlight(str);
      console.log(s);
      $('#result2').html(s);

    } catch(erro) {
      console.log('error');
      $('#result2').html('JSON parse error');
    }

  });
});

</script>
EOS;

  PopupPageTail();
  exit;
}


$anchors = array();
function _panel($title, $content, $comment='') {
  global $anchors;

  $h = substr(md5($title.rand(1000,2000)),0,10);
  $anchors[] = array($title, $h);

  $cmt = preg_replace("/\n/", "<br>", $comment);

  $top = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='#top'>[맨위로]</a>";

  $html=<<<EOS
<a name='$h'></a>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">$title $top</h3>
$cmt
  </div>
  <div class="panel-body">
$content
  </div>
</div>
EOS;
  return $html;
}

function _content($action, $info, $rinfo) {
  $content =<<<EOS
<table class='table request table-inverse'>
<form name='form' action='$env[self]' method='post'>
<thead>
<tr>
<td>요청</td>
<td>값</td>
</tr>
</thead>

<tr>
<td>action</td>
<td>$action <input type='hidden' name='action' value='$action'></td>
</tr>
EOS;

  foreach ($info as $key=>$str) {
    list($a, $b) = preg_split("/:/", $str);
    $desc = $a;
    $val = $b;

    if ($val == '') {
      if ($key == 'appkey') {
        $val = $_SESSION['saved_appkey'];
      }
    }

    $_key = $key;
    if ($desc) $_key .= " ($desc)";

    $content .=<<<EOS
<tr>
<td class='a'>$_key</td>
<td class='b'><input type='text' name='$key' value='$val' style='width:100%' onclick='this.select()'></td>
</tr>
EOS;
  }

  $content .=<<<EOS
<thead>
<tr>
<td>응답</td>
<td>설명</td>
</tr>
</thead>
EOS;

  foreach ($rinfo as $key=>$str) {
    //list($a, $b) = preg_split("/:/", $str);

    $content .=<<<EOS
<tr>
<td class='a'>$key</td>
<td class='b'>$str</td>
</tr>
EOS;
  }


  $content .=<<<EOS
<tr>
<td></td>
<td>
<input type='button' onclick='_sf(this.form)' value='확인'>
<input type='hidden' name='mode' value='apicall'>
</td>
</tr>

</form>
</table>
EOS;
  return $content;
}

### }}}

  MainPageHead('Mobile API');
  ParagraphTitle('Mobile API');

  print<<<EOS
<style>
table.request { }
table.request td.a { width:40%; }
table.request td.b { width:60%; }
</style>
EOS;

  $html = '';


  $title = "운전자 등록(register)";
  $info = array(
 'goyu'=>'고유번호',
 'tel'=>'휴대폰 번호',
 'did'=>'DID',
 'pushkey'=>'푸쉬알람 키',
 'phone_os'=>'android 또는 ios',
 'user_name'=>'이름(optional)',
  );
  $comment = <<<EOS
운전자 등록
- 어플을 처음 설치후에 사용자를 등록하는 기능
- DID 값을 전송한다.
- 앱 설치전 고유번호, 전화번호, 이름 정보는 서버에 등록이 되어 있어야 함
- 결과로 암호키를 받아옴
EOS;
  $rinfo = array(
    'result'=>'성공여부',
    'phone_hash'=>'암호화된 폰번호',
  );
  $content = _content('register', $info, $rinfo);
  $html .= _panel($title, $content, $comment);


  $title = "AppKey 받아오기(get_appkey)";
  $info = array(
 'did'=>'DID',
 'phone_hash'=>'phone_hash 값');
  $rinfo = array('result'=>'성공여부', 'appkey'=>'appKey값', 'notice_url'=>'공지사항주소');
  $content = _content('get_appkey', $info, $rinfo);
  $comment = <<<EOS
- 어플 실행시 먼저 새로운 appKey를 받아오는 기능
- 이미 받아온 appKey를 저장하고 있으면 그대로 사용할 수도 있음
- DID값과 암호키값을 전송한다
EOS;
  $html .= _panel($title, $content, $comment);


  $title = "버전정보(latest_version)";
  $info = array( 'appkey'=>'',);
  $rinfo = array('result'=>'성공여부', 'version'=>'버전정보',
    'notice_url'=>'공지사항주소',
    'update_url'=>'업데이트 주소');
  $content = _content('latest_version', $info, $rinfo);
  $comment = <<<EOS
- 최신 버전 정보를 얻어옴
EOS;
  $html .= _panel($title, $content, $comment);


  $title = "사용자 정보 (get_driver_info) (------------ 사용금지)";
  $action = 'get_driver_info';
  $info = array( 'appkey'=>'',);
  $rinfo = array('result'=>'성공여부', 'driver_id'=>'운전자 ID값', 'role'=>'사용자 역할');
  $content = _content($action, $info, $rinfo);
  $comment = <<<EOS
- 사용자 정보(ID, 역할)를 조회함
EOS;
  $html .= _panel($title, $content, $comment);


  $title = "차량정보 리스트(list_car)";
  $action = 'list_car';
  $info = array( 'appkey'=>'',);
  $rinfo = array('result'=>'성공여부', 'list'=>'차량 정보 리스트');
  $content = _content($action, $info, $rinfo);
  $comment = <<<EOS
- 서버에 등록되어 있는 차량 정보를 받아온다
- 차량 ID값, 번호, 차종 정보를 받아옴
EOS;
  $html .= _panel($title, $content, $comment);


  $title = "장소정보 리스트(list_location)";
  $action = 'list_location';
  $info = array( 'appkey'=>'',
    'group'=>'(선택) 장소구분(행사장,공항,숙소,기타)',
    'treeflag'=>'(선택) (treeflag=1 이면) 장소구분별로 묶어서 조회',
  );
  $rinfo = array('result'=>'성공여부', 'list'=>'장소 정보 리스트');
  $content = _content($action, $info, $rinfo);
  $comment = <<<EOS
- 서버에 등록되어 있는 장소 정보를 받아온다
- ID값, 장소명, 좌표를 받아옴
EOS;
  $html .= _panel($title, $content, $comment);


  $title = "의전대상자 리스트(list_person)";
  $action = 'list_person';
  $info = array( 'appkey'=>'');
  $rinfo = array('result'=>'성공여부', 'list'=>'의전대상자 리스트');
  $content = _content($action, $info, $rinfo);
  $comment = <<<EOS
- 의전대상자 리스트 정보를 받아옴
- 이름, 국적, 현재상태등 정보
EOS;
  $html .= _panel($title, $content, $comment);


  $title = "공지사항 가져오기(list_notice)";
  $action = 'list_notice';
  $info = array( 'appkey'=>'');
  $rinfo = array('result'=>'성공여부', 'list'=>'공지사항 리스트');
  $content = _content($action, $info, $rinfo);
  $comment = <<<EOS
- 공지사항 리스트를 가져옴
EOS;
  $html .= _panel($title, $content, $comment);


/*
  $title = "운전할 차량을 선택(bind_driver_car)";
  $action = 'bind_driver_car';
  $info = array( 'appkey'=>'', 'user_id'=>'사용자 ID값', 'car_id'=>'차량ID값');
  $rinfo = array('result'=>'성공여부');
  $content = _content($action, $info, $rinfo);
  $comment = "
- 사용자(운전자)가 운전할 차량을 선택함
";
  $html .= _panel($title, $content, $comment);
*/

/*
  $title = "운전자 현재 상태 받아오기(get_driver_status)";
  $action = 'get_driver_status';
  $info = array( 'appkey'=>'','user_id'=>'운전자ID값');
  $rinfo = array('result'=>'성공여부','status'=>'운전자상태(코드)', 'status_string'=>'운전자상태');
  $content = _content($action, $info, $rinfo);
  $comment = "
- 운전자의 현재 상태를 받아옴
";
  $html .= _panel($title, $content, $comment);
*/

  $title = "가능한 운전자 상태 전체 리스트(get_all_driver_status)";
  $action = 'get_all_driver_status';
  $info = array( 'appkey'=>'');
  $rinfo = array('result'=>'성공여부','list'=>'상태코드 리스트');
  $content = _content($action, $info, $rinfo);
  $comment = "
- 운전자 상태 리스트 정보를 받아옴
";
  $html .= _panel($title, $content, $comment);

  $title = "운전자 현재 상태 변경(set_driver_status) (------------ 사용금지)";
  $action = 'set_driver_status';
  $info = array( 'appkey'=>'',
    'user_id'=>'사용자ID값',
    'status'=>'운전자상태',
    'echo'=>'echo');
  $rinfo = array('result'=>'성공여부', 'gps_interval'=>'위치전송 시간주기');
  $content = _content($action, $info, $rinfo);
  $comment = "
- 운전자의 현재 상태 정보를 변경함
";
  $html .= _panel($title, $content, $comment);


/*
  $title = "역할 리스트(list_role)";
  $action = 'list_role';
  $info = array( 'appkey'=>'');
  $rinfo = array('result'=>'성공여부');
  $content = _content($action, $info, $rinfo);
  $comment = "
- 사용자 역할 리스트 정보
";
  $html .= _panel($title, $content, $comment);
*/



  $title = "출발지/도착지 설정(set_schedule) (------------ 사용금지)";
  $action = 'set_schedule';
  $info = array( 'appkey'=>'',
    'user_id'=>'운전자ID값',
    'depart_from'=>'출발지장소ID',
    'depart_from_etc'=>'(optional)출발지장소명',
    'going_to'=>'도착지장소ID',
    'going_to_etc'=>'(option)도착지장소명',
    //'starttime'=>'(option)출발시간',
    //'arrivetime'=>'(option)도착예정시간',
  );
  $rinfo = array('result'=>'성공여부');
  $content = _content($action, $info, $rinfo);
  $comment = " ";
  $html .= _panel($title, $content, $comment);


  $title = "운행시작(start_driving)";
  $action = 'start_driving';
  $info = array( 'appkey'=>'',
    'depart_from'=>'출발지장소ID',
    'going_to'=>'도착지장소ID',
  );
  $rinfo = array('result'=>'성공여부',
    'gps_interval'=>'위치전송주기',
    'run_id'=>'운행 ID값',
  );
  $content = _content($action, $info, $rinfo);
  $comment = " ";
  $html .= _panel($title, $content, $comment);


  $title = "위치 정보 전송(at_location)";
  $action = 'at_location';
  $info = array( 'appkey'=>'',
    'run_id'=>'운행 ID값',
    'lat'=>'위도', 'lng'=>'경도',
    'echo'=>'echo',
  );
  $rinfo = array('result'=>'성공여부',
    'elasped'=>'운행시간(초)',
  );
  $content = _content($action, $info, $rinfo);
  $comment = "
- 운전자의 현재 위치 정보를 전송함
";
  $html .= _panel($title, $content, $comment);


  $title = "운행종료(finish_driving)";
  $action = 'finish_driving';
  $info = array( 'appkey'=>'',
    'run_id'=>'run_id',
  );
  $rinfo = array('result'=>'성공여부');
  $content = _content($action, $info, $rinfo);
  $comment = " ";
  $html .= _panel($title, $content, $comment);

  $title = "VIP조회(query_person)";
  $action = 'query_person';
  $info = array( 'appkey'=>'',
  );
  $rinfo = array('result'=>'성공여부',
    'person'=>'VIP 정보',
  );
  $content = _content($action, $info, $rinfo);
  $comment = " ";
  $html .= _panel($title, $content, $comment);

  $title = "VIP설정(set_person)";
  $action = 'set_person';
  $info = array( 'appkey'=>'',
    'person_id'=>'VIP ID(person.ID)'
  );
  $rinfo = array('result'=>'성공여부',
  );
  $content = _content($action, $info, $rinfo);
  $comment = " ";
  $html .= _panel($title, $content, $comment);



  // 목차
  print("<a name='top'></a>");
  ParagraphTitle('API 리스트', 1);
  foreach ($anchors as $item) {
    list($title, $hash) = $item;
    print<<<EOS
<p><a href="#$hash">$title</a>
EOS;
  }

  // 본문
  print $html;

  print<<<EOS
<script>
var wincount = 0;
function _sf(form) {
  console.log(form);
  wincount = wincount + 1;
  var winname = "win"+wincount;
  form.target = winname;
  wopen2("", winname, 500,500,1,1);
  form.submit();
}
</script>
EOS;


  MainPageTail();
  exit;

?>
