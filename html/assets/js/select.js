// ********************************* クラス切り替え
// クーポン選択
document.querySelectorAll('.use-menu-cupon').forEach(button => {
  button.addEventListener('click', function() {
    if (this.classList.contains('active')) {
      this.classList.remove('active');
      this.textContent = "このクーポン使う";
      // メニュー有効化
      enableMenu();
      removeGrayFromMenus();

    } else {
      document.querySelectorAll('.use-menu-cupon').forEach(btn => {
        btn.classList.remove('active');
        btn.textContent = "このクーポンを使う";
      });
      this.classList.add('active');
      this.textContent = "選択中";
      // メニュー選択無効化
      disableMenu();
      addGrayToMenus();
    }
  });
});

// メニュー選択
document.querySelectorAll('.use-menu-cut, .use-menu-color, .use-menu-parma, .use-menu-other').forEach(button => {
  button.addEventListener('click', function() {
    if (this.classList.contains('active')) {
      this.classList.remove('active');
      this.textContent = "このメニューを選ぶ";
      // クーポン有効化
      enableCoupon();
      removeGrayFromCoupons();
    } else {
      document.querySelectorAll('.use-menu-cut, .use-menu-color, .use-menu-parma, .use-menu-other').forEach(btn => {
        btn.classList.remove('active');
        btn.textContent = "このメニューを選ぶ";
      });
      this.classList.add('active');
      this.textContent = "選択中";
      //クーポン選択無効化
      disableCoupon();
      addGrayToCoupons();
    }
  });
});

// クーポン有効化
function enableCoupon(){
  document.querySelectorAll('.use-menu-cupon').forEach(btn => {
    console.log("クーポン有効化！");
    btn.disabled = false;
  })
}

// クーポン無効化
function disableCoupon(){
  document.querySelectorAll('.use-menu-cupon').forEach(btn => {
    console.log("クーポン無効化！");
    btn.disabled = true;
  })
}

// メニュー有効化
function enableMenu(){
  document.querySelectorAll('.use-menu-cut, .use-menu-color, .use-menu-parma, .use-menu-other').forEach(btn => {
    // console.log("メニュー有効化！");
    btn.disabled = false;
  })
}

// メニュー無効化
function disableMenu(){
  document.querySelectorAll('.use-menu-cut, .use-menu-color, .use-menu-parma, .use-menu-other').forEach(btn => {
    // console.log("メニュー無効化！");
    btn.disabled = true;
  });
}

// メニューに.grayを追加
function addGrayToMenus(){
  document.querySelectorAll('.use-menu-cut, .use-menu-color, .use-menu-parma, .use-menu-other').forEach(btn => {
    btn.classList.add('gray');
  });
}

// メニューから.grayを削除
function removeGrayFromMenus(){
  document.querySelectorAll('.use-menu-cut, .use-menu-color, .use-menu-parma, .use-menu-other').forEach(btn => {
    btn.classList.remove('gray');
  });
}

// クーポンに.grayを追加
function addGrayToCoupons(){
  document.querySelectorAll('.use-menu-cupon').forEach(btn => {
    btn.classList.add('gray');
  });
}

// クーポンから.grayを削除
function removeGrayFromCoupons(){
  document.querySelectorAll('.use-menu-cupon').forEach(btn => {
    btn.classList.remove('gray');
  });
}

// stylist-単一選択
document.querySelectorAll('.use-stylist').forEach(button => {
  button.addEventListener('click', function() {
    if (this.classList.contains('active')) {
      this.classList.remove('active');
      this.textContent = "このスタイリストを選ぶ";
    } else {
      document.querySelectorAll('.use-stylist').forEach(btn => {
        btn.classList.remove('active');
        btn.textContent = "このスタイリストを選ぶ";
      });
      this.classList.add('active');
      this.textContent = "選択中";
    }
  });
});

// *********************************  ajax予約確認
let reservationData = {};
let totalMenuTime = null;
let startTime = null;
let endTime = null;
let coponMenu = null;
let coponTime = null;
let currentEventId = 'currentEvent';
let isCouponSelected = false;
const TAX = 1.10;
const user_id = document.querySelector('#user')?.getAttribute('data-user-id');
const user_mailaddress = document.querySelector('#mailaddress')?.getAttribute('data-user-mail');

document.querySelectorAll('.select-btn').forEach(button => {
  // ボタンがクリックされたときの処理を追加
  button.addEventListener('click', () => {
    setTimeout(() => {
      // スタイリスト取得
      const selectedStylist = document.querySelector('.use-stylist.active')?.getAttribute('data-stylist-id');

      // クーポンと通常メニュー取得
      const selecterMenu = Array.from(document.querySelectorAll('.menu-select.active'))
                                .map(menu => menu.getAttribute('data-menu-id')); 
      // 所要時間取得
      const selecterMenuTime = Array.from(document.querySelectorAll('.menu-select.active'))
                                    .map(menu => menu.getAttribute('data-menu-time'));
      // 金額
      const selecterMenuPrice = Array.from(document.querySelectorAll('.menu-select.active'))
                                      .map(menu => menu.getAttribute('data-menu-price'));

      // クーポン分岐
      const requestData = {
        user_id: user_id || null,
        stylistId: selectedStylist || null,
        menuIds: selecterMenu.length > 0 ? selecterMenu : null,
        menuTimes: selecterMenuTime || null,
        menuPrice: selecterMenuPrice || null,
        startTime: reservationData.startTime || null,
        endTime: reservationData.endTime || null
      };

      // サーバーにデータ送信
      fetch('apply-coupon.php', {
        method: 'post',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
      })
      .then(response => response.json())
      .then(data => {
        // 画面に選択内容を表示
        reservationData = data;
        totalMenuTime = reservationData.menuTimes.reduce((total, time) => parseInt(total) + parseInt(time), 0); 
          // 表示内容の更新
          document.getElementById('menulist-main').innerHTML = `
            <p id="selectmenu" data-select-menu="${reservationData.menuIds}">メニュー: ${reservationData.menuIds ? reservationData.menuIds : '選択なし'}</p>
            <p id="selectstylist" data-select-stylist="${reservationData.stylistId}">スタイリスト: ${reservationData.stylistId ? reservationData.stylistId : '選択なし'}</p>
            <p id="time" data-select-time="${totalMenuTime}">所要時間: ${totalMenuTime}分</p>
            <p id="totalprice" data-select-price="${parseInt(reservationData.menuPrice*TAX)}">合計金額: ${parseInt(reservationData.menuPrice*TAX)}円(税込み価格)</p>
          `;

      })
      .catch(error => {
        // エラー対策
        console.error('エラーが発生しました:', error);
      });
    }, 0);
  });
});

// ********************************* fullcalendar
function addMinutesToDate(start, end){
  return new Date(start.getTime() + end * 60000);
}

function getPriceTotal(cupon, price){
  return price * cupon;
}
function getDiscountPrice(cupon, price){
  var discount = getPriceTotal(cupon, price);
  return price - discount;
}

function updateReservationDisplay(reservationData) {
   startTime = new Date(reservationData.startTime); 
   endTime = addMinutesToDate(startTime, totalMenuTime);
   document.getElementById('schedule').innerHTML = `
    <span id="start" data-select-starttime="${startTime}">時間: ${formatDateTime(startTime) || '未選択'} </span>～<span id="end" data-select-endtime="${endTime}"> ${formatDateTime(endTime) || ''} </span>
  `; 
}

// 各要素にactiveがついていたらボタンを表示する
function makeReservationButton(){
  const activeElement = document.querySelectorAll('.select-btn.active');
  const scheduleElement = document.querySelector('.fc-event-time');
    if(activeElement.length >= 2 && scheduleElement.classList.contains('fc-event-time')){

      var reservationButton = document.createElement('button');
      reservationButton.classList.add('reservationButton');
      reservationButton.textContent = "上記の内容で予約する";
      document.getElementById('menulist').appendChild(reservationButton);
      
    }
}

function formatDateTime(isoString){
  if(isoString == null){
    return "--年--月--日--時--分";
  }
  const date = new Date(isoString);
  const year = date.getFullYear();
  const month = date.getMonth()+1;
  const day = date.getDate();
  const hours = date.getHours();
  const minis = date.getMinutes();
  
  return `${year}年${month}月${day}日${hours}時${minis}分`;
}

document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',

    selectable: true,
    // 今日から表示 以前非表示
    firstDay:(new Date()).getDay(),
    validRange:{
      start: new Date()
    },
    // 重複不可
    eventOverlap: false,
    overlap: false,
    select: function(info){
      // リダイレクト防止
      info.jsEvent.preventDefault();
      info.jsEvent.stopImmediatePropagation();

      reservationData.startTime = info.startStr;
      reservationData.endTime = info.endStr;
      updateReservationDisplay(reservationData);
    },
    // 時間表示範囲変更
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
    // イベントクリック時リダイレクト処理無し
    eventClick: function(info){
      info.jsEvent.preventDefault();
      info.jsEvent.stopImmediatePropagation();
    },
    // 付箋表示
    dateClick: function(info){
      const existingEvent = calendar.getEventById(currentEventId);
      if(existingEvent){
        const delbtn = document.querySelectorAll('.reservationButton');
        delbtn.forEach(btn => btn.remove());
        existingEvent.remove();
      }
      calendar.addEvent({
        id: currentEventId, 
        start: info.date,
        end: endTime,
        allDay:false,
        eventOverlap: false,
        overlap: false,
      });
      // 予約確定ボタン生成
      makeReservationButton();
    },
    eventColor: '#e07a5f',
    // 終日非表示
    allDaySlot:false,
    locale: 'ja',
    timezone: 'Asia/Tokyo',
    // googleカレンダー
    googleCalendarApiKey: 'AIzaSyC4KW8_z8igZc1SnMoL7dPXP3SEEme7fZ0',
    events: {
      googleCalendarId: '4bf5d6988266f2863cad011f45143ba28089a7f5b991b7f35f0da9c89721b0dc@group.calendar.google.com'
    }
  });
  
  calendar.render();
});

document.getElementById('reloadButton').addEventListener('click', function() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    selectable: true,
    // 今日から表示 以前非表示
    firstDay:(new Date()).getDay(),
    validRange:{
      start: new Date()
    },
    // 重複不可
    eventOverlap: false,
    overlap: false,
    select: function(info){ // infoにはユーザーが選択した時間情報が入る
      //alert('予約時間: ' + info.startStr + ' ~ ' + info.endStr);
      reservationData.startTime = info.startStr;
      reservationData.endTime = info.endStr;
      updateReservationDisplay(reservationData);
    },
    // 時間表示範囲変更
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
    // イベントクリック時リダイレクト処理無し
    eventClick: function(info){
      info.jsEvent.preventDefault();
      info.jsEvent.stopImmediatePropagation();
    },
    // 付箋表示
    dateClick: function(info){
      const existingEvent = calendar.getEventById(currentEventId);
      if(existingEvent){
        const delbtn = document.querySelectorAll('.reservationButton');
        delbtn.forEach(btn => btn.remove());
        existingEvent.remove();
      }
      calendar.addEvent({
        id: currentEventId, 
        start: info.date,
        end: endTime,
        allDay:false,
        color: '#ccc',
        backgroundColor: '#f2a60c',
        eventOverlap: false,
        overlap: false,
      });
      makeReservationButton();
    },
    eventColor: '#e07a5f',
    // 終日非表示
    allDaySlot:false,
    locale: 'ja',
    timezone: 'Asia/Tokyo',
    // googleカレンダー
    googleCalendarApiKey: '',
    events: {
      googleCalendarId: ''
    }
  });
  
  calendar.render();
});

// ********************************* 予約ボタンが押された時の処理ajax



document.getElementById('menulist').addEventListener('click', function (event) {
  if (event.target.classList.contains('reservationButton')) {
    setTimeout(() => {
      // 入力データの準備
      const selectStylist = document.querySelector('#selectstylist')?.getAttribute('data-select-stylist');
      const selectMenu = Array.from(document.querySelectorAll('#selectmenu'))
        .map(menu => menu.getAttribute('data-select-menu'));
      const selectMenuPrice = document.querySelector('#totalprice')?.getAttribute('data-select-price');
      
      const reservationStartTime = new Date(document.querySelector('#start')?.getAttribute('data-select-starttime'));
      const reservationEndTime = new Date(document.querySelector('#end')?.getAttribute('data-select-endtime'));
      
      // 日本時間（JST）に変換する関数
      function convertToJST(date) {
        const offset = 9 * 60; 
        const jstDate = new Date(date.getTime() + (date.getTimezoneOffset() + offset) * 60000);
        return jstDate;
      }
      
      const StartTime = convertToJST(reservationStartTime);
      const EndTime = convertToJST(reservationEndTime);
      
      const reservation = {
        user_id: typeof user_id !== 'undefined' ? user_id : null,
        selectStylist: selectStylist || null,
        selectMenu: selectMenu.length > 0 ? selectMenu : null,
        selectMenuPrice: selectMenuPrice || null,
        reservationStartTime: StartTime.toLocaleString(), // JSTでそのままISO形式を使わず
        reservationEndTime: EndTime.toLocaleString() // JSTでそのままISO形式を使わず
      };
      
      // サーバーにデータ送信
      fetch('register-reservation.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(reservation)
      })
      .then(response => {
        if (!response.ok) {
          return response.text().then(errorMsg => {
            console.error('サーバーエラー:', errorMsg);
            throw new Error('サーバーエラー: ' + errorMsg);
          });
        }
        return console.log(response.text());
      })
      .then(data => {
        console.log(data);
        // 画面に選択内容を表示
        document.getElementById('menu-head').innerHTML = '予約完了しました！';
        document.getElementById('menulist-main').innerHTML = '予約内容確認はこちらをクリック！';
        
        const elements = document.querySelectorAll('.reservationButton');
        elements.forEach(element => {
          element.remove();
        });
        
        var reloadShowBtn = document.createElement('button');
        reloadShowBtn.classList.add('reloadShowBtn');
        reloadShowBtn.textContent = "予約内容を確認する";
        document.getElementById('menulist').appendChild(reloadShowBtn);
        
        resercationCheck();
      
        // Googleカレンダー登録データ
        const addData = {
          title: `予約${selectStylist}`,
          reservationStartTime: StartTime.toISOString(),
          reservationEndTime: EndTime.toISOString(),
          description: `メニュー：${selectMenu}<br>\n予約時金額：${selectMenuPrice}<br>\nユーザーID：${user_id}<br>\nメール：${user_mailaddress}`
        };
        
        // Googleカレンダー登録処理（例）
        fetch('add_event.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(addData)
        })
        .then(response => {
          if (!response.ok) {
            return response.text().then(errorMsg => {
              console.error('Googleカレンダーへの登録に失敗:', errorMsg);
              throw new Error('Googleカレンダーへの登録に失敗: ' + errorMsg);
            });
          }
          return response.text();
        })
        .catch(error => {
          console.error('Googleカレンダー登録中にエラーが発生しました:', error);
          alert('Googleカレンダー登録中にエラーが発生しました');
        });
      })
      .catch(error => {
        console.error('送信中にエラーが発生しました:', error);
        alert('予約送信中にエラーが発生しました');
      });
    }, 0);
  }
});

// 予約確認ボタン
function resercationCheck(){
  document.getElementById("menulist").addEventListener("click", function() {
    location.reload();
    setTimeout(function() {
        window.location.href = "/userdashbord.php";
    }, 1000);
  });
}

// キャンセルボタン
const canselButn = document.querySelectorAll('.canselbtn');
canselButn.forEach(btn =>{
  btn.addEventListener('click', function(){
    const reservationId = this.getAttribute('data-cansel-btn');
    // 取得したらfetchで飛ばす。
    fetch(`cancel.php?data-cansel-btn=${reservationId}`)
    .then(response => response.text())
    .then(data => {
      // CSSで処理完了の通知
    })
  
  });
});