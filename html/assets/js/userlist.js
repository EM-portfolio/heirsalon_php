// 各レコード取得
function attachRowClickEvent() {
  document.querySelectorAll('.tbody').forEach(row =>{
    row.addEventListener('click', function(){
      // user_id取得
      const userId = this.getAttribute('data-user-id');

      // ajaxリクエスト
      fetch(`get_user_detail.php?data-user-id=${userId}`)
      .then(response => response.text())
      .then(data => {
        document.getElementById('userDetailContent').innerHTML = data;
        document.getElementById('userDetail').classList.remove('hidden');
        const userCardChangeButton = document.getElementById('userCardChange');
        const rsvbtn = document.querySelectorAll('.rsvbtn');
        
        // admin: ユーザー情報内変更ボタン
        if (userCardChangeButton) {
          let userCardEditMode = false;
          userCardChangeButton.addEventListener('click', function() {
            if (!userCardEditMode) {
              var formElm = document.getElementById('userCard').elements;
              for (var i = 0; i < formElm.length; i++) {
                formElm[i].removeAttribute('readonly');
              }
              userCardChangeButton.textContent = "内容を更新する";
              userCardEditMode = true;
            } else {
              this.type = "submit";
            }                   
          });
        }
        // admin: 予約内容変更ボタン
        for (var i = 0; i < rsvbtn.length; i++) {
          const submitbtnReservation = rsvbtn[i];
          let reservationEditMode = false;
          if (submitbtnReservation) {
            submitbtnReservation.addEventListener('click', function() {
              if (!reservationEditMode) {
                var formElm = this.closest('form').elements;
                for (var j = 0; j < formElm.length; j++) {
                  formElm[j].removeAttribute('readonly');
                }
                this.textContent = "内容を更新する";
                reservationEditMode = true; // 外部変数を変更
              } else {
                this.type = "submit"; // ボタンの type を submit に変更
              }
            });
          }
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

        
        })
      .catch(error => {
        console.error('error : ', error);
      });
    })
  });
}





// 検索ボタン
const searchBtn = document.getElementById("searchBtn");
searchBtn.addEventListener('click', (e)=>{
  e.preventDefault();
  const keyword = document.getElementById('keyword');
  const value = keyword.value;
  if(!value){
    alert('検索キーワードを入力してください。');
    return;
  }
  fetch(`search.php?query=${encodeURIComponent(value)}`, {
    method:'GET',
  })
  .then(response =>{
    if(!response.ok){
      throw new Error('HTTPエラー: ' + response.status);
    }
    return response.text();
  })
  .then(data => {
    // responseが返ってきたときの処理
    const resultContainer = document.getElementById('result');
    resultContainer.innerHTML = "";
    resultContainer.innerHTML = data;
    attachRowClickEvent();
  })
  .catch(error => {
    console.error('error : ' , error);
  });
})


attachRowClickEvent();