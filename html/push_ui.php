<?php

  //https://developers.google.com/maps/documentation/javascript/events?hl=ko#EventsOverview

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");
  include_once("$env[prefix]/inc/class.driver.php");

  $source_title = '차량 위치 설정(개발자용)';

### {{{

if ($mode == 'push') {

  $id = $form['driver_id'];
  $lat = $form['lat'];
  $lng = $form['lng'];

  $objUser = new driver();
  $row = $objUser->get_driver_by_id($id);
  $is_driving = $objUser->status_is_driving($row['driver_stat']);
//dd($is_driving);

  $objUser->set_driver_location($id, $lat, $lng, $car_location_update=true);

  exit;
}

if ($mode == 'status') {

  $qry = "SELECT d.*, c.car_no
 FROM driver d
 LEFT JOIN carinfo c ON d.car_id=c.id";

  $ret = db_query($qry);

  $info = array();
  while ($row = db_fetch($ret)) {
    $id = $row['id'];
    $car_no = $row['car_no'];
    $lat = $row['lat'];
    $lng = $row['lng'];

    $item = array('id'=>$id, 'car_no'=>$car_no,
       'driver_name'=>$row['driver_name'],
       'lat'=>$lat, 'lng'=>$lng
    );
    $info[] = $item;
  }
  print json_encode($info);
  exit;
}
### }}}


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
EOS;


  print<<<EOS
<tr>
<td>
</td>
</tr>
</form>
</table>

<div id='log'></div>

<script>

var map;
var markers = [];

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 13,
  });

}


function _push_position(marker) {
  console.log(marker);
  var driver_id = marker.driver_id;
  
  var p = marker.getPosition();
  var lat = p.lat();
  var lng = p.lng();
  //console.log(lat);
  //console.log(lng);

  var str = "POST push_ui.php {"+"<br>"
    +"mode: post, "+"<br>"
    +"lat:"+lat+", "+"<br>"
    +"lng:"+lng+"<br>"
    +"driver_id:"+driver_id+"<br>"
    +"}<br>";
  console.log(str);
  $('#log').html(str);

  $.ajax({
    method: "POST",
    url: "$env[self]",
    data: {
      "mode": "push",
      "lat": lat,
      "lng": lng,
      "driver_id": driver_id
    }
  })
  .done(function( msg ) {
    console.log( "Data Saved: " + msg );
  });

}

function _get_carinfo(callback) {
  $.ajax({
    method: "GET",
    url: "$env[self]",
    data: {
      "mode": "status"
    }
  })
  .done(function( msg ) {
    console.log( "data : " + msg );
    var car_info = $.parseJSON( msg );
    callback(car_info);
  });
}

function _map_range() {
  var points = [];
  for (var i = 0; i < markers.length; i++) {
    var p = markers[i].getPosition();
    points.push(p);
  }

  var bounds = new google.maps.LatLngBounds();    

  for (var i = 0; i < points.length; i++) {
    bounds.extend(points[i]);
  }
  map.fitBounds(bounds);
}


function _make_markers() {

  _get_carinfo(function(info) {

    for (var i = 0; i < info.length; i++) {
      console.log( info[i] );
      var item = info[i];
      var lat = item['lat'];
      var lng = item['lng'];
      var car_no = item['car_no'];
      var driver_name = item['driver_name'];
      var driver_id = item['id'];

      var title = driver_name;
      if (car_no) title += "/" + car_no;

      var p = new google.maps.LatLng(lat, lng);
      //console.log(p);

      var marker = new google.maps.Marker({
        position: p,
        map: map,
        title: title,
        animation: google.maps.Animation.DROP,
        draggable:true,
        driver_id: driver_id
      });
      //console.log(marker);

      markers.push(marker);

      marker.addListener('dragend', function() {
        _push_position(this);
        //map.setZoom(14);
        //map.setCenter(this.getPosition());
      });

    }
    _map_range();

  });
}



$(function() {
  _make_markers();
});
</script>
EOS;

  script_google_map();
  MainPageTail();
  exit;

?>
