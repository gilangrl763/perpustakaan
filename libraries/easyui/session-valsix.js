var vtimeoutTimer;
interval= moveTimer= "";
var diffmilis = 0;
var milis;

if(typeof getsessionwaktu == "undefined"){}
else
{
  $(window).load(function(){
    /*console.log("load");
    resettime();*/

    vStartTimers();
  })

  $(document).ready( function () {
    if(getsessionwaktu.settime == "1")
    {
      // resettime();
    }

    var test = $('body');
    // var test = document;

    test.on("mouseout",function(){
      vStartTimers();

      /*clearTimeout(interval);
      clearTimeout(moveTimer);
      resetvaltime();*/
    });

    test.on("mousemove",function(){
      // start_stop();
      /*clearTimeout(interval);
      // console.log("I'm moving!");
      clearTimeout(moveTimer);
      resetvaltime();*/

      moveTimer = setTimeout(function(){
        // console.log("I stopped moving!");
        // resettime();
        vStartTimers();
      },100);
    });



    /*test = $('.konten-area');
    test.on("mouseout",function(){
      clearTimeout(moveTimer);
    });

    test.on("mousemove",function(){
      // start_stop();
      clearTimeout(interval);
      // console.log("I'm moving!");
      clearTimeout(moveTimer);
      moveTimer = setTimeout(function(){
        // console.log("I stopped moving!");
        resettime();
      },100);
    });

    test = $('.container-fluid');
    test.on("mouseout",function(){
      clearTimeout(moveTimer);
    });

    test.on("mousemove",function(){
      // start_stop();
      clearTimeout(interval);
      console.log("I'm moving!");
      clearTimeout(moveTimer);
      moveTimer = setTimeout(function(){
        console.log("I stopped moving!");
        resettime();
      },100);
    });*/
    

    /*$(document).mousemove(function(cursor) {
      resettime();
    });*/

    /*$("#konten-kanan").contents().mousemove(function(e) {
      resettime();
    });*/
  });
}

// Start timer
function vStartTimers() {
  var timoutNow= parseFloat(getsessionwaktu.minutes);
  // var timoutNow= parseFloat(getsessionwaktu.second);
  timoutNow= timoutNow * (60 * 1000);
  // console.log("vStartTimers:"+timoutNow);
  vtimeoutTimer = setTimeout("vIdleTimeout()", timoutNow);
}

// Reset timer
function ResetTimers() {
  clearTimeout(vtimeoutTimer);
  vStartTimers();
}

// Logout user
function vIdleTimeout() {
  // console.log("vIdleTimeout");
  window.location = getsessionwaktu.logout;
}

function resetvaltime()
{
  // console.log("xx");
  clearTimeout(interval);
  clearTimeout(moveTimer);
  vdefault= getsessionwaktu.default;
  $('.labelSessionCounter', window.top.document).text(vdefault);
  $('.labelSessionCounter', window.parent.document).text(vdefault);
  // $('.labelSessionCounter', window.parent.parent.document).text(vdefault);
  $(".labelSessionCounter").text(vdefault);
  window.top.localStorage.removeItem("localsessiontime");
  window.parent.localStorage.removeItem("localsessiontime");
  // window.parent.parent.localStorage.removeItem("localsessiontime");
  localStorage.removeItem("localsessiontime");
}

function resettime()
{
  resetvaltime()
  settime();
}

function settime()
{
  var difflog= "";
  var tempmin= "";

  var hoursleft= parseFloat(getsessionwaktu.hours);
  var minutesleft= parseFloat(getsessionwaktu.minutes);
  var secondsleft= parseFloat(getsessionwaktu.second);
  // console.log(hoursleft);return false;
  // console.log(minutesleft);return false;
  // console.log(secondsleft);return false;

  var finishedtext = "Countdown finished!";
  var end;

  /*// kalau nilai 1 maka yg bergerak parent
  if(getsessionwaktu.settime == "1")
  {
    if(window.parent.localStorage.getItem("localsessiontime")) {
      end = new Date(window.parent.localStorage.getItem("localsessiontime"));
    } else {
       end = new Date();
       end.setHours(end.getHours()+hoursleft);
       end.setMinutes(end.getMinutes()+minutesleft);
       end.setSeconds(end.getSeconds()+secondsleft);
    }
  }
  else
  {
    if(localStorage.getItem("localsessiontime")) {
      end = new Date(localStorage.getItem("localsessiontime"));

      // end.setTime(end.getTime()+diffmilis);
    } else {
       end = new Date();
       end.setHours(end.getHours()+hoursleft);
       end.setMinutes(end.getMinutes()+minutesleft);
       end.setSeconds(end.getSeconds()+secondsleft);

       // end.setTime(end.getTime()+diffmilis);
    }
  }*/

  if(localStorage.getItem("localsessiontime")) {
    end = new Date(localStorage.getItem("localsessiontime"));

    // end.setTime(end.getTime()+diffmilis);
  } else {
     end = new Date();
     end.setHours(end.getHours()+hoursleft);
     end.setMinutes(end.getMinutes()+minutesleft);
     end.setSeconds(end.getSeconds()+secondsleft);

     // end.setTime(end.getTime()+diffmilis);
  }

  window.parent.localStorage.setItem("localsessiontime", end);
  // window.parent.parent.localStorage.setItem("localsessiontime", end);
  localStorage.setItem("localsessiontime", end);

  // console.log("parent:"+window.parent.localStorage.getItem("localsessiontime"));
  // console.log("child:"+localStorage.getItem("localsessiontime"));

  var counter = function () {

    var now = new Date();
    var sec_now = now.getSeconds();
    var min_now = now.getMinutes(); 
    var hour_now = now.getHours(); 
    
    var sec_end = end.getSeconds();
    var min_end = end.getMinutes(); 
    var hour_end = end.getHours();
    
    var date1 = new Date(2000, 0, 1, hour_now,  min_now, sec_now); // 9:00 AM
    var date2 = new Date(2000, 0, 1, hour_end, min_end, sec_end); // 5:00 PM
    if (date2 < date1) {
      date2.setDate(date2.getDate() + 1);
    }
    var diff = date2 - date1;
    
    var msec = diff;
    var hh = Math.floor(msec / 1000 / 60 / 60);
    msec -= hh * 1000 * 60 * 60;
    var mm = Math.floor(msec / 1000 / 60);
    msec -= mm * 1000 * 60;
    var ss = Math.floor(msec / 1000);
    msec -= ss * 1000;
    
    var sec = ss;
    var min = mm; 
    var hour = hh; 
  
    if (min < 10) {
      min = "0" + min;
    }
    if (sec < 10) { 
      sec = "0" + sec;
    }
    
    // console.log(hour + ":" + min + ":" + sec);
    if(now >= end) {
      $('.labelSessionCounter', window.top.document).text("00:00");
      $('.labelSessionCounter', window.parent.document).text("00:00");
      $(".labelSessionCounter").text("00:00");
      clearTimeout(interval);
      logoutsessionhabis();
    } 
    else 
    {
      var value = hour + ":" + min + ":" + sec;

      window.parent.localStorage.setItem("localsessiontime", end);
      // window.parent.parent.localStorage.setItem("localsessiontime", end);
      localStorage.setItem("localsessiontime", end);

      $('.labelSessionCounter', window.top.document).text(value);
      $('.labelSessionCounter', window.parent.document).text(value);
      // $('.labelSessionCounter', window.parent.parent.document).text(value);

      if(min.toString() == 'NaN')
      {
          clearTimeout(interval);
          logoutsessionhabis();
      }
    }

  }
  interval = setInterval(counter, 1);

}

function start_stop()
{
    if(interval == "") {
        endmilis = new Date().getTime();
        diffmilis = endmilis - milis;
        settime();
    } else {
        milis = new Date().getTime();
        clearInterval(interval);
        interval = "";
    }
}

function logoutsessionhabis()
{
  // console.log(getsessionwaktu);
  window.top.location = getsessionwaktu.logout;
}