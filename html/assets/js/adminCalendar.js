document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('adminCalendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    selectable: true,
    firstDay: (new Date()).getDay(),
    validRange: {
      start: new Date()
    },
    views: {
      timeGridWeek: {
        slotMinTime: '11:00:00',
        slotMaxTime: '19:00:00'
      },
      timeGridDay: {
        slotMinTime: '11:00:00',
        slotMaxTime: '19:00:00'
      },
    },
    eventColor: '#e07a5f',
    allDaySlot: false,
    locale: 'ja',
    timezone: 'Asia/Tokyo',
    eventClick: function(info){
      // リダイレクト防止
      info.jsEvent.preventDefault();
      info.jsEvent.stopImmediatePropagation();
      // HTMLに表示
      const resultContainer = document.getElementById('resultdetail');
      resultContainer.innerHTML = `
        <p> スタイリスト: ${info.event.title}<br><span id="user_info" >${info.event.extendedProps.description}</span> </p>
      `;
    },
    googleCalendarApiKey: 'AIzaSyC4KW8_z8igZc1SnMoL7dPXP3SEEme7fZ0',
    events: {
      googleCalendarId: '4bf5d6988266f2863cad011f45143ba28089a7f5b991b7f35f0da9c89721b0dc@group.calendar.google.com'
    }
  });
  
  calendar.render();
});

document.getElementById('reloadButton').addEventListener('click', function() {
  var calendarEl = document.getElementById('adminCalendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    selectable: true,
    firstDay: (new Date()).getDay(),
    validRange: {
      start: new Date()
    },
    views: {
      timeGridWeek: {
        slotMinTime: '11:00:00',
        slotMaxTime: '19:00:00'
      },
      timeGridDay: {
        slotMinTime: '11:00:00',
        slotMaxTime: '19:00:00'
      },
    },
    eventColor: '#e07a5f',
    allDaySlot: false,
    locale: 'ja',
    timezone: 'Asia/Tokyo',
    eventClick: function(info){
      info.jsEvent.preventDefault();
      info.jsEvent.stopImmediatePropagation();
      
      console.log(info);
      console.log(info.event.title);
      console.log(info.event.extendedProps.description);
      // HTMLに表示
      const resultContainer = document.getElementById('resultdetail');
      const userinfo = info.event.extendedProps.description;
      resultContainer.innerHTML = `
        <p> スタイリスト: ${info.event.title}<br><span id="user_info">${info.event.extendedProps.description}</span> </p>
      `;
    },
    googleCalendarApiKey: 'AIzaSyC4KW8_z8igZc1SnMoL7dPXP3SEEme7fZ0',
    events: {
      googleCalendarId: '4bf5d6988266f2863cad011f45143ba28089a7f5b991b7f35f0da9c89721b0dc@group.calendar.google.com'
    }
  });
  
  calendar.render();
});


