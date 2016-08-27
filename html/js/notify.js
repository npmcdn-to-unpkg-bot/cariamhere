
// request permission on page load
document.addEventListener('DOMContentLoaded', function () {
  if (Notification.permission !== "granted")
    Notification.requestPermission();
});

function notifyMe(title, text, url) {
  if (!Notification) {
    alert('Desktop notifications not available in your browser. Try Chromium.'); return;
  }

  if (Notification.permission !== "granted")
    Notification.requestPermission();
  else {
    var notification = new Notification(title, {
      icon: '/img/noti_icon/car.png',
      body: text,
    });

    notification.onclick = function () {
      if (url) window.open(url);      
    };
  }

}

