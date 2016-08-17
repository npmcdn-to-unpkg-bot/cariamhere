<?php

  include("common.php");

  print<<<EOS
<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        width: 100%;
        height: 100%;
      }
    </style>
  </head>
  <body>
<table border='0' width='500'>
<td width='500' height='500'>
    <div id="map"></div>
</td>
<td>
<input type='button' onclick="_change()" value='change'>
</td>
</table>
    <script>

var map;
var marker1;
var marker2;
function initMap() {
  var where = new google.maps.LatLng(36.348382, 127.385230);
  //var sanfrancisco = new google.maps.LatLng(37.727, -122.449);

  map = new google.maps.Map(document.getElementById('map'), {
    center: where,
    zoom: 15 
  });

  marker1 = new google.maps.Marker({ position: where, map: map, title: 'Hello World!',
   animation: google.maps.Animation.DROP, draggable:true });
  //marker2 = new google.maps.Marker({ position: sanfrancisco, map: map, title: 'Hello World!' });
}

function _change() {
  //console.log(marker1.position);

  setInterval(function() {
    var lat = marker1.position.lat();
    var lng = marker1.position.lng();
    lat += 0.0001;
    lng += 0.0001;
    var latlng = new google.maps.LatLng(lat, lng);
    marker1.setPosition(latlng);
  }, 1000);
}

    </script>

<script src="https://maps.googleapis.com/maps/api/js?key=$key&callback=initMap" defer></script>

  </body>
</html>
EOS;

?>
