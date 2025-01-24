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
    googleCalendarApiKey: '*******',
    events: {
      googleCalendarId: '*******'
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
    googleCalendarApiKey: '*******',
    events: {
      googleCalendarId: '*******'
    }
  });
  
  calendar.render();
});


