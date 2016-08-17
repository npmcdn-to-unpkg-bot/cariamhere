<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.location.php");

  $source_title = '장소';
  $env['menu']['1-4'] = true;

  $clslocation = new location();

### {{{
function _data_tr($title, $html) {
  $str=<<<EOS
<tr>
<th>$title</th>
<td>$html</td>
</tr>
EOS;
  return $str;
}

function _get($id) {
  $qry = "SELECT * FROM location WHERE id='$id'";
  $row = db_fetchone($qry);
  return $row;
}

function _sqlset(&$s) {
  global $form;
  $s[] = "loc_group='{$form['loc_group']}'";
  $s[] = "loc_title='{$form['loc_title']}'";
  $s[] = "lat='{$form['lat']}'";
  $s[] = "lng='{$form['lng']}'";
//dd($form); dd($s); exit;
}

function _edit_link($title, $id) {
  if (!$title) $title = '--';
  $html = <<<EOS
<span class=link onclick="_edit('$id')">{$title}</span>
EOS;
  return $html;
}


### }}}

### {{{
if ($mode == 'dodel') {
  $id = $form['id'];

  $qry = "DELETE FROM location WHERE id='$id'";
  $ret = db_query($qry);

  $url = $env['self'];
  Redirect($url);
  exit;
}

if ($mode == 'doedit') {
  $id = $form['id'];

  $s = array();
  _sqlset($s);
  $s[] = "udate=NOW()";
  $sql_set = " SET ".join(",", $s);

  $qry = "UPDATE location $sql_set WHERE id='$id'";
//dd($qry); exit;
  $ret = db_query($qry);

  $url = $env['self'];
  Redirect($url);
  exit;
}

if ($mode == 'doadd') {
  //dd($form);

  $s = array();
  _sqlset($s);
  $s[] = "idate=NOW()";
  $s[] = "udate=NOW()";
  $sql_set = " SET ".join(",", $s);

  $qry = "INSERT INTO location $sql_set";
  $ret = db_query($qry);

  $url = $env['self'];
  Redirect($url);
  exit;
}

if ($mode == 'add' || $mode == 'edit') {

  if ($mode == 'edit') {
    $id = $form['id'];
    $row = _get($id);
    $nextmode = 'doedit';
    $title = "장소 수정";
  } else {
    $row = array();
    $nextmode = 'doadd';
    $title = "장소 입력";
  }

  MainPageHead($source_title);
  ParagraphTitle($source_title);
  ParagraphTitle($title, 1);

  print<<<EOS
<table class='table table-striped'>
<form name='form' action="$env[self]" method='post'>
EOS;

  $click_select = true;

  $opt = $clslocation->option_location_group($row['loc_gropu']);
  $html = "<select name='loc_group'>$opt</select>";
  print _data_tr('장소구분', $html);

  $html = textinput_general('loc_title', $row['loc_title'], '20', '', $click_select, 0);
  print _data_tr('장소명', $html);

  $lat = textinput_general('lat', $row['lat'], '15', $onkeypress='', $click_select, $maxlength=0);
  $lng = textinput_general('lng', $row['lng'], '15', $onkeypress='', $click_select, $maxlength=0);
  $html = "($lat, $lng)";
  print _data_tr('좌표', $html);

  print<<<EOS
<script>
function get_position() {
  var lat = document.form.lat.value;
  var lng = document.form.lng.value;
  return {'lat':lat, 'lng':lng};
}
function set_position(lat, lng) {
  document.form.lat.value = lat;
  document.form.lng.value = lng;
}
</script>
<tr>
<td></td>
<td>
<div id="map" style='width:400px; height:400px;'></div>
</td>
</tr>
EOS;
  google_select_location_general('map', 'get_position', 'set_position', 13);


  print<<<EOS
<tr>
<td colspan='2' class='c'>
<input type='hidden' name='_group' value=''>
<input type='hidden' name='mode' value='$nextmode'>
<input type='hidden' name='id' value='$id'>
<input type='button' value='확인' onclick='sf_1()' class='btn btn-primary'>
<input type='button' value='삭제' onclick='sf_del()' class='btn btn-danger'>
</td>
</tr>

</form>
</table>

<script>
function sf_1() {
  var form = document.form;
  var grp = $('#location_group option:selected').val();
  form._group.value = grp;
  form.submit();
}
function sf_del() {
  if (!confirm('삭제할까요?')) return;
  var url = "$env[self]?mode=dodel&id=$id";
  urlGo(url);
}
</script>
EOS;

  MainPageTail();
  exit;
}

if ($mode == 'map') {
  MainPageHead($source_title);
  ParagraphTitle($source_title);

  print<<<EOS
<table border='1' width='100%'>
<form name='form'>
<tr>
<td width='100%' height='500'>
<div id="map"></div>
</td>
</tr>
</form>
</table>
EOS;
  script_daum_map();

  $qry = "SELECT l.*"
   ." FROM location l"
   ." ORDER BY l.loc_title"
   ;
  $ret = db_query($qry);

  $cnt = 0;
  $a = array();
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);
    $title = $row['loc_title'];
    $lat = $row['lat'];
    $lng = $row['lng'];
    $a[] = "{title: '$title', latlng: new daum.maps.LatLng($lat, $lng)}";
  }
  $positions = join(",", $a);
  //dd($positions);

  print<<<EOS
<script>

  var markers = [];

function _map_range() {
  var points = [];
  for (var i = 0; i < markers.length; i++) {
    var p = markers[i].getPosition();
    points.push(p);
  }

  var bounds = new daum.maps.LatLngBounds();
  for (var i = 0; i < points.length; i++) {
    bounds.extend(points[i]);
  }
  map.setBounds(bounds);
}


  var mapContainer = document.getElementById('map');
  mapOption = {
    center: new daum.maps.LatLng(37.566826, 126.9786567),
    level: 3,
  };

  var map = new daum.maps.Map(mapContainer, mapOption);

  var mapTypeControl = new daum.maps.MapTypeControl();
  map.addControl(mapTypeControl, daum.maps.ControlPosition.TOPRIGHT);

  var zoomControl = new daum.maps.ZoomControl();
  map.addControl(zoomControl, daum.maps.ControlPosition.RIGHT);

  var positions = [ $positions ];

  // 마커 이미지의 이미지 주소입니다
  var imageSrc = "/img/marker/markerStar.png"; 
    
  for (var i = 0; i < positions.length; i ++) {
    
    var imageSize = new daum.maps.Size(24, 35); 
    var markerImage = new daum.maps.MarkerImage(imageSrc, imageSize); 
    
    var marker = new daum.maps.Marker({
      map: map,
      position: positions[i].latlng,
      title : positions[i].title,
      image : markerImage,
    });
    markers.push(marker);

  }
  _map_range();

</script>
EOS;

  MainPageTail();
  exit;
}

### }}}

  MainPageHead($source_title);
  ParagraphTitle($source_title);

  $b1 = button_general('입력', 0, "_add()", $style='', $class='btn btn-primary');
  $b2 = button_general('지도보기', 0, "_map()", $style='', $class='btn btn-warning');
  print<<<EOS
<script>
function _add() { var url = "$env[self]?mode=add"; urlGo(url); }
function _map() { var url = "$env[self]?mode=map"; urlGo(url); }
</script>
EOS;

  $qry = "SELECT l.*"
   ." FROM location l"
   ." ORDER BY l.loc_title"
   ;
  $ret = db_query($qry);

  print<<<EOS
<div class="panel panel-default">
<div class="panel-heading">$b1 $b2</div>
<table class='table table-striped dataC'>
EOS;
  print table_head_general(array('ID','장소구분','장소명','좌표'));

  $cnt = 0;
  while ($row = db_fetch($ret)) {
    $cnt++;
    //dd($row);

    $id = $row['id'];
    $edit = _edit_link($row['loc_title'], $id);
    $coo = "($row[lat], $row[lng])";

    print<<<EOS
<tr>
<td>{$id}</td>
<td>{$row['loc_group']}</td>
<td>{$edit}</td>
<td>{$coo}</td>
</tr>
EOS;
  }
  print<<<EOS
</table>
</div>

<script>
function _edit(id) {
  var url = "$env[self]?mode=edit&id="+id;
  urlGo(url);
}
</script>
EOS;

  MainPageTail();
  exit;

?>
