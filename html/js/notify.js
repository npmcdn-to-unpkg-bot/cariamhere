
// request permission on page load
document.addEventListener('DOMContentLoaded', function () {
  if (Notification.permission !== "granted")
    Notification.requestPermission();
});

/*
https://developer.mozilla.org/ko/docs/Web/API/notification

Summary
Notification 객체는 사용자에게 데스크탑 알림을 설정하고 표시하는데 사용 할 수 있다.

Constructor
var notification = new Notification(title, options)
Parameters

title
title 은 알림에 반드시 나타나야 한다.

options
An object that allows to configure the notification. It can have the following properties:

dir : The direction of the notification, it can be auto, ltr or rtl
lang: 알림안에 특정 언어를 설정할 수 있다. 문자열은 BCP 47 language tag만 유효하다.
body: A string representing an extra content to display within the notification
tag: An ID for a given notification that allow to retrieve, replace remove it if necessary
icon: 알림에 쓰일 아이콘 이미지의 URL이다.
*/
function notifyMe(title, text, url) {
  if (!Notification) {
    alert('Desktop notifications not available in your browser. Try Chromium.'); return;
  }

  if (Notification.permission !== "granted") {
    alert('permission error');
    Notification.requestPermission();
  } else {
    var opt = {
      title: title,
      dir : 'ltr',
      body: text,
      icon: '/img/noti_icon/car.png',
    };

    var notification = new Notification(title, opt, function(id) {
      alert('id');
    });
    notification.onclick = function () {
      if (url) window.open(url);      
    };
  }

//chrome.notifications.create("", opt, function(id) {
//  timer = setTimeout(function(){chrome.notifications.clear(id);}, 2000);
//});

}

