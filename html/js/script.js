/*
"use strict";
*/

function script_wopen(url, width, height, scrollbars, resizable) {
  option = "width="+width
          +",height="+height
          +",scrollbars="+scrollbars
          +",resizable="+resizable;
          //+",status="+status; 
  return open(url, '', option);
}
function wopen(url, width, height, scrollbars, resizable) {
  option = "width="+width
          +",height="+height
          +",scrollbars="+scrollbars
          +",resizable="+resizable;
          //+",status="+status; 
  return open(url, '', option);
}
function wopen2(url, name, width, height, scrollbars, resizable) {
  option = "width="+width
          +",height="+height
          +",scrollbars="+scrollbars
          +",resizable="+resizable;
          //+",status="+status; 
  open(url, name, option);
}

function script_Question(url, msg) {
  if (confirm(msg)) document.location = url;
  else return;
}
function Question(url, msg) {
  if (confirm(msg)) document.location = url;
  else return;
}
function script_Go(url) {
  document.location = url;
}
function urlGo(url) {
  document.location = url;
}

var _dom = 0;
function keypresshandler(e) {
  if (document.all) e=window.event; // for IE
  if (_dom == 3) var EventStatus = e.srcElement.tagName;
  else if (_dom == 1) var EventStatus = e.target.nodeName; // for Mozilla

  if (EventStatus == 'INPUT' || EventStatus == 'TEXTAREA' || _dom == 2) return;

  var cc = '';
  var ch = '';

  if (_dom == 3) {  // for IE
    if (e.keyCode > 0) {
    ch = String.fromCharCode(e.keyCode);
    cc = e.keyCode;
    }
  } else {   // for Mozilla
    cc = (e.keyCode);
    if (e.charCode > 0) {
      ch = String.fromCharCode(e.charCode);
    }
  }

  if (e.altKey || e.ctrlKey) return;

  var loc = kph_get_location(ch); // key press handler get location
  if (loc != '') self.location = loc;

  return;
}

// 다음 주소검색 연동
function search_zipcode_daum(form_name,zipcode,addrJibun,addrRoad) {
  inputnames = form_name + ":" + zipcode + ":" + addrJibun + ":" + addrRoad;
  window.open('/member/search_zipcode_daum.php?inputnames='+inputnames,'','width=500,height=700,scrollbars=0,resizable=0');
}

// Postcodify 주소검색 연동
function search_zipcode_postcodify(form_name,zipcode,addrJibun,addrRoad) {
  inputnames = form_name + ":" + zipcode + ":" + addrJibun + ":" + addrRoad;
  window.open('/member/search_zipcode_postcodify.php?inputnames='+inputnames,'','width=480,height=600,scrollbars=0,resizable=0');
}

// 윤상훈: 241128
function script_lb(InputNo) {
  var member_card = window.open("/member/lb_main.php?mode=view&InputNo="+InputNo,"_blank","width=800, height=700, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, status=no, left=0, top=0");
}
function script_lb_e(InputNo) {
  var member_card = window.open("/member/lb_main.php?mode=edit&InputNo="+InputNo,"_blank","width=800, height=700, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, status=no, left=0, top=0");
}

function Person(gender) {
  this.gender = gender;
}

function lcolor(span) { try { span.style.backgroundColor = '#80ff00'; } catch(e){} }



