<?php

  include("./path.php");
  include("$env[prefix]/inc/common.php");

  $source_title = '지도 보기';

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

  print<<<EOS
<div id='log'></div>

<script>

var map;
var marker1;
var pos;

function initMap() {
  var where = new google.maps.LatLng(36.348382, 127.385230);
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 13,
    center: {lat: 37.442, lng: 127.001 }
  });

  marker1 = new google.maps.Marker({ position: where, map: map, title: 'Hello World!',
   animation: google.maps.Animation.DROP, draggable:true });
}

$(document).ready(function() {
  console.log('start');
  _change();
});

function _change() {

  setInterval(function() {
    try {
      var p = _get_position();
      lat = pos['lat'];
      lng = pos['lng'];

      var latlng = new google.maps.LatLng(lat, lng);
      //console.log(latlng);
      //console.log(marker1);

      marker1.setPosition(latlng);
      map.panTo(latlng);

    } catch(e) {}

  }, 1000);
}

function _get_position() {

  $.ajax({
    method: "GET",
    url: "pull_ui_get.php"
  })
  .done(function( msg ) {
    console.log(msg);
    pos = $.parseJSON( msg );
    console.log(pos);
    //return pos;
  });

}

</script>
EOS;


  script_google_map();
  MainPageTail();
  exit;

?>
