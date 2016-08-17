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

function member_card(key,mode) {
  var op = "width=650, height=700, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, status=no, left=0, top=0";
  if (mode == 'view')
    var w = window.open("/member/card/member_card.php?InputNo="+key,"_blank",op);
  else if (mode == 'move')
    var w = window.open("/member/card/apply_church.php?InputNo="+key,"_blank",op);
  else if (mode == 'edit')
    var w = window.open("/member/card/edit_member.php?InputNo="+key,"_blank",op);
  else if (mode == 'chhis')
    var w = window.open("/member/card/member_card.php?mode=chhis&InputNo="+key,"_blank",op);
  else if (mode == 'newno')
    var w = window.open("/member/card/member_card.php?NewNo="+key,"_blank",op);

  else if (mode == 'suryo')
    var w = window.open("/shc/member_card.php?InputNo="+key,"_blank",op);

  else if (mode == 'chul')
    var w = window.open("/chul/member_card.php?InputNo="+key,"_blank",op);

  else if (mode == 'shc_view')
    var w = window.open("/shc/member_card.php?InputNo="+key,"_blank",op);
  else if (mode == 'shc_edit')
    var w = window.open("/shc/edit_member.php?InputNo="+key,"_blank",op);

  else if (mode == 'itvcard')
    var w = window.open("/shc/itv_card.php?IntervieweeNo="+key,"_blank",op);
}

// 26.3.30 추가 member_card() 를 대체
function mbcard(key,mode) {
  var op = "width=650, height=700, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, status=no, left=0, top=0";
  var w;
  if (mode == 'view' || mode == '교적')
    w = window.open("/member/card/member_card.php?InputNo="+key,"_blank",op);
  else if (mode == 'edit' || mode == '교적수정')
    w = window.open("/member/card/edit_member.php?InputNo="+key,"_blank",op);
  else if (mode == 'chhis')
    w = window.open("/member/card/member_card.php?mode=chhis&InputNo="+key,"_blank",op);
  else if (mode == 'newno')
    w = window.open("/member/card/member_card.php?NewNo="+key,"_blank",op);
  else if (mode == 'suryo')
    w = window.open("/member/suryo_apply.php?InputNo="+key,"_blank",op);
  else if (mode == '직책')
    w = window.open("/member/card/apply_jikchek.php?InputNo="+key,"_blank",op);
  else if (mode == '직분')
    w = window.open("/member/card/apply_jikbun.php?InputNo="+key,"_blank",op);
  else if (mode == '부서')
    w = window.open("/member/card/apply_dept.php?InputNo="+key,"_blank",op);
  else if (mode == '출판')
    w = window.open("/pub/member_card.php?InputNo="+key,"_blank",op);

  else if (mode == 'chul' || mode == '출결교적')
    w = window.open("/chul/member_card.php?InputNo="+key,"_blank",op);

  else if (mode == 'shc_view' || mode == '수강카드')
    w = window.open("/shc/member_card.php?InputNo="+key,"_blank",op);
  else if (mode == 'shc_view' || mode == '교적수강카드')
    w = window.open("/shc/member_card.php?msf=1&InputNo="+key,"_blank",op);
  else if (mode == 'shc_edit')
    w = window.open("/shc/edit_member.php?InputNo="+key,"_blank",op);
  else if (mode == 'itvcard')
    w = window.open("/shc/itv_card.php?IntervieweeNo="+key,"_blank",op);

  else if (mode == 'cjcard' || mode == '교회재정')
    w = window.open("/cj/card.php?no="+key,"_blank",op);
}

// 31.8.22 추가
// e.g. membercard(InputNo,'view',this);
function membercard(key,mode,span) {
  try {
    span.style.backgroundColor = '#80ff00';
  } catch(e){}
  mbcard(key, mode);
}


function find_zipcode(form_name,zc,addr1,addr2) {
  inputnames = form_name + ":" + zc + ":" + addr1 + ":" + addr2;
  window.open('/member/address.php?inputnames='+inputnames,'','width=350,height=300,scrollbars=0,resizable=0');
}

function find_new_zipcode(form_name,zc,addr1,addr2) {
  inputnames = form_name + ":" + zc + ":" + addr1 + ":" + addr2;
  window.open('/member/address_new.php?inputnames='+inputnames,'','width=400,height=360,scrollbars=0,resizable=0');
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



/*
// 툴팁
function showToolTip(e,text) {
  if (document.all) e = event;
  var obj = document.getElementById('bubble_tooltip');
  var obj2 = document.getElementById('bubble_tooltip_content');
  obj2.innerHTML = text;
  obj.style.display = 'block';
  var st = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
  if(navigator.userAgent.toLowerCase().indexOf('safari')>=0)st=0; 
  var leftPos = e.clientX - 100;
  if(leftPos<0)leftPos = 0;
  obj.style.left = leftPos + 'px';
  obj.style.top = e.clientY - obj.offsetHeight -1 + st + 'px';
}  
function hideToolTip() {
  document.getElementById('bubble_tooltip').style.display = 'none';
}
*/


/******************************************************************************/
/*  날짜자동세팅                                                              */
/******************************************************************************/
// 250519 윤상훈 : 2월은 무조건 29일로 함
// 혼동예: 1957년은 양력으로 하면 28일까지 있고 음력으로하면 29일까지 있음
function valid_date_setting(objYear, objMonth, objDay) {
  yy = parseInt(objYear.value, 10);
  mm = parseInt(objMonth.options[objMonth.selectedIndex].value, 10);

  if (mm == 1) {
    max_days = 31;
  } else if (mm == 2) {
    if (((yy % 4 == 0) && (yy % 100 != 0)) || (yy % 400 == 0)) {
      max_days = 29;
    } else {
      max_days = 29;
    }
  }
  else if (mm == 3)  { max_days = 31; }
  else if (mm == 4)  { max_days = 30; }
  else if (mm == 5)  { max_days = 31; }
  else if (mm == 6)  { max_days = 30; }
  else if (mm == 7)  { max_days = 31; }
  else if (mm == 8)  { max_days = 31; }
  else if (mm == 9)  { max_days = 30; }
  else if (mm == 10) { max_days = 31; }
  else if (mm == 11) { max_days = 30; }
  else if (mm == 12) { max_days = 31; }
  else { max_days = 31; }

  for (i=0; i<objDay.length; i++) {
    objDay.options[i].value = "";
    objDay.options[i].text  = "";
  }

  objDay.length = max_days;

  // 자료 Setting
  for (i=1; i<=objDay.length; i++) {
    if (i <= 9) {
      objDay.options[i-1].value = '0'+i;
      objDay.options[i-1].text  = i;
    } else {
      objDay.options[i-1].value = i;
      objDay.options[i-1].text  = i;
    }
  }
  return;
}

function view_map_by_addr(mapaddr,width,height) {
  var url = "/member/search_map.php?mode=addr&addr="+mapaddr+"&width="+width+"&height="+height;
  script_wopen(url,width+60,height+160,1,1);
}

function view_map_by_xy(x,y,width,height) {
  var url = "/member/search_map.php?mode=xy&x="+x+"&y="+y+"&width="+width+"&height="+height;
  script_wopen(url,width+60,height+160,1,1);
}



// usage:
function js_view_sheet(serial, span, color) {
  span.style.backgroundColor = color;
  var str = "__js_view_sheet('"+serial+"')";
  setTimeout(str, 100);
}
function __js_view_sheet(serial) {
  var url = "sheet_card.php?SerialNo="+serial;
  wopen(url, 600,600,1,1);
}


// 쪽지보내기
function MemoWrite(rid) {
  var url = "/myinfo/mm.php?mode=write&rid="+rid;
  script_wopen(url,450,400,0,0);
}

function i_alert(msg, spanid) {
  try {
    var span = document.getElementById(spanid);
  } catch(e) {}
  span.innerHTML = span.innerHTML + "<div class='alert'>"+msg+"</div>";
}

function isNumeric(n) {
  try {
    return !isNaN(parseFloat(n)) && isFinite(n);
  } catch (err) {
    alert('isNumeric error');
  }
}


function layer_close() {
  $('.layer').fadeOut();
}

function layer_open(el) {

  var temp = $('#' + el);
  var bg = temp.prev().hasClass('bg');  //dimmed 레이어를 감지하기 위한 boolean 변수

  if (bg) {
    $('.layer').fadeIn();  //'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
  } else {
    temp.fadeIn();
  }

  // 화면의 중앙에 레이어를 띄운다.
  if (temp.outerHeight() < $(document).height() ) temp.css('margin-top', '-'+temp.outerHeight()/2+'px');
  else temp.css('top', '0px');
  if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/2+'px');
  else temp.css('left', '0px');

  temp.find('a.cbtn').click(function(e) {
    if (bg) {
      $('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
    } else {
      temp.fadeOut();
    }
    e.preventDefault();
  });

  //배경을 클릭하면 레이어를 사라지게 하는 이벤트 핸들러
  $('.layer .bg').click(function(e) {
    $('.layer').fadeOut();
    e.preventDefault();
  });
}



function Person(gender) {
  this.gender = gender;
}



