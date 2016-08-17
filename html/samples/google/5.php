<?php

  //https://developers.google.com/maps/documentation/javascript/events?hl=ko#EventsOverview

  include("./path.php");
  include("$env[prefix]/inc/common.php");

  $key = $conf['google_map_key'];

  print<<<EOS
<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">

    <script src="https://code.jquery.com/jquery-1.11.3.js"></script>

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
</td>
</table>

<div id='log'></div>

    <script>

function initMap() {
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 13,
    center: {lat: 37.442, lng: 127.001 }
  });

  map.addListener('click', function(e) {
    placeMarkerAndPanTo(e.latLng, map);
  });
}

function placeMarkerAndPanTo(latLng, map) {

  var lat = latLng.lat();
  var lng = latLng.lng();
  console.log(lat);
  console.log(lng);
  var str = "("+lat+", "+lng+")<br>";
  $('#log').append(str);

  $.ajax({
    method: "POST",
    url: "5post.php",
    data: { "lat": lat, "lng": lng }
  })
  .done(function( msg ) {
    console.log( "Data Saved: " + msg );
  });

  var marker = new google.maps.Marker({
    position: latLng,
    map: map
  });
  //map.panTo(latLng);
}

    </script>

<script src="https://maps.googleapis.com/maps/api/js?key=$key&callback=initMap" defer></script>

  </body>
</html>
EOS;

?>
