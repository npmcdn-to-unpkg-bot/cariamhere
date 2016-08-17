<?php

  include("../../path.php");
  include("$env[prefix]/inc/common.login.php");

  print<<<EOS
<div id="map" style='width:100%; height:300px;'></div>

<div id='message'></div>

<script>
var map;
var marker1;
function initMap() {
  var chicago = new google.maps.LatLng(41.850, -87.650);

  map = new google.maps.Map(document.getElementById('map'), {
    center: chicago,
    zoom: 15,
  });

  marker1 = new google.maps.Marker({
    position: chicago,
    map: map,
    title: 'You are here',
    animation: google.maps.Animation.DROP,
    draggable:false,
  });

  _get_current_position(_update_marker);
}
function _current_time() {
  var currentdate = new Date(); 
  var datetime = ""
 + currentdate.getFullYear() + "-"  
 + (currentdate.getMonth()+1)  + "-" 
 + currentdate.getDate() + " "
 + currentdate.getHours() + ":"  
 + currentdate.getMinutes() + ":" 
 + currentdate.getSeconds();
  return datetime;
}
function _update_marker(lat, lng) {
  var p = new google.maps.LatLng(lat, lng);
  marker1.setPosition(p);
  map.panTo(p);
  _showmessage(lat, lng);
}
function _update() {
  _get_current_position(_update_marker);
}
var count_up = 0;
function _showmessage(lat, lng) {
  count_up ++;
  var now = _current_time();
  str = "<p>("+count_up+", "+now+", "+lat+", "+lng+")</p>";
  $('#message').html(str);
}

function _get_current_position(callback) {
  var startPos;
  var geoOptions = {
    maximumAge: 5 * 60 * 1000,
    enableHighAccuracy: true,
  }

  var geoSuccess = function(position) {
    startPos = position;
    var lat = startPos.coords.latitude;
    var lng = startPos.coords.longitude;

    callback(lat, lng);
  };

  var geoError = function(error) {
    console.log('Error occurred. Error code: ' + error.code);
    // error.code can be:
    //   0: unknown error
    //   1: permission denied
    //   2: position unavailable (error response from location provider)
    //   3: timed out
  };

  navigator.geolocation.getCurrentPosition(geoSuccess, geoError, geoOptions);
}

window.onload = function() {
  setInterval(_update, 5000);
};
</script>
EOS;
  script_google_map();


  MainPageTail();
  exit;

?>
