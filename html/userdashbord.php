<?php
include 'functions.php';
$csrfToken = getToken();
if(!isset($_SESSION['mailaddress']) && !isset($_SESSION['user_id'])){
  header('Location: login.php');
  exit();
}else{
  $mailaddress = $_SESSION['mailaddress'];
  $user_id = $_SESSION['user_id'];
  $conn = getDbConnection();
  sessionCheck($conn, $user_id, $mailaddress);
}
  // error
  if(!empty($_SESSION['error'])){
    $errormsg = $_SESSION['error'];
  }

  // ログアウト
  if(!empty($_GET['logout_btn'])){
    unset($_SESSION['admin_login']);
    unset($_SESSION['user']);
    header('Location: login.php');
    exit();
  }
  // 予約履歴取得
  $conn = getDbConnection();
  $table = "reservations";
  $result1 = reservationList($conn, $table, $user_id);
  $row_cnt1 = mysqli_num_rows($result1);

  // 最近の予約取得
  $result2 = lastResrtvation($conn, $user_id);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- フォント読み込み -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Guides:wght@400..700&family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    <!-- google icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=unfold_more_double" />
    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- イージング -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <!-- フルカレンダー -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/daygrid/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/daygrid/main.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.0/google-calendar.min.js'></script>
    
    <script src="assets/js/main.js"></script>
    <link rel="stylesheet" href="assets/css/userdashbord.css">
  <title>ログインページ</title>
</head>
<body>
  <header >
    <h1>ユーザーダッシュボード</h1>
    <form action="userdashbord.php" method="get">
      <input class="button" type="submit" name="logout_btn" value="ログアウト">
    </form>
  </header>
  <div class="container">
    <aside class="sidebar">
      <ul>
        <li class="card-header">メニュー</li>
        <li class="list active"><a href="#">ダッシュボード</a></li>
        <li class="list"><a href="#">予約する</a></li>
        <li class="list"><a href="#">予約履歴</a></li>
        <li class="list"><a href="#">会員情報変更</a></li>
      </ul>
    </aside>
    <main class="main-content">
      <section class="users dashbord section active-section">
        <h3 class="card-header">ダッシュボード</h3>
        <p id="user" data-user-id="<?php echo $user_id; ?>">こんにちは、<?php echo '<span id="mailaddress" data-user-mail="' . $mailaddress. '">' . htmlspecialchars($mailaddress) . " さん</span>"; ?></p>
        
        <div class="info">
        <?php
          if(is_string($result2)){
            echo "<p>エラーが発生しました。" .$result2. "</p>";
          }else if($row_cnt1 >= 1 ){
            echo '
            <table class="reservation">
              <caption> 最近の予約 </caption>
              <tr>
                <th>予約日</th>
                <th>メニュー</th>
                <th>スタイリスト</th>
                <th>金額</th>
                <th>ステータス</th>
              </tr>
              <tr>
                <td>' .$result2['reservation_datetime']. '</td>
                <td>' .$result2['menu']. '</td>
                <td>' .$result2['stylist']. '</td>
                <td>' .intval($result2['price']). '円</td>
                <td>' .getStatus($result2['status']). '</td>
              </tr>
            </table>
            ';
        }else{
          echo "<p>まだ予約がありません。</p>";
        }
        ?>
        </div>
      </section>
      <section class="users section">
        <div class="cupon">
          <h3 class="card-header">クーポン</h3>
          <?php
          if($row_cnt1 == 0){
          ?>
          <p class="dis">初回来店時利用できるクーポン</p>
          <div class="cupon-section">
            <div class="cupon-card">
              <p class="title">初回来店時のみ適用</p>
              <p>カット・カラー20%off</p>
              <button href="#" class="select-btn use-menu-cupon menu-select" data-menu-time="180" data-menu-id="カット・カラー(クーポン)" data-menu-price="7200" >このクーポンを使う</button>
            </div>
            <div class="cupon-card">
              <p class="title">初回来店時のみ適用</p>
              <p>ヘッドスパ20%off</p>
              <button href="#" class="select-btn use-menu-cupon menu-select" data-menu-time="45" data-menu-id="ヘッドスパ(クーポン)" data-menu-price="3200" >このクーポンを使う</button>
            </div>
            <div class="cupon-card">
              <p class="title">初回来店時のみ適用</p>
              <p>前髪カット50%off</p>
              <button href="#" class="select-btn use-menu-cupon menu-select" data-menu-time="30" data-menu-id="前髪カット(クーポン)" data-menu-price="550" >このクーポンを使う</button>
            </div>
          </div>
          <?php } ?>
          <p class="dis">再来時利用できるクーポン</p>
          <div class="cupon-section">
            <div class="cupon-card">
              <p class="title">再来店時のみ適用</p>
              <p>リタッチ20%off</p>
              <button class="select-btn use-menu-cupon menu-select" data-menu-id="リタッチ(クーポン)" data-menu-time="90" data-menu-price="2400">このクーポンを使う</button>
            </div>
            <div class="cupon-card">
              <p class="title">再来店時のみ適用</p>
              <p>ヘッドスパ20%off</p>
              <button class="select-btn use-menu-cupon menu-select" data-menu-id="ヘッドスパ(クーポン)" data-menu-time="60" data-menu-price="3200">このクーポンを使う</button>
            </div>
            <div class="cupon-card">
              <p class="title">再来店時のみ適用</p>
              <p>ヘアセット20%off</p>
              <button class="select-btn use-menu-cupon menu-select" data-coupon-id="ヘアセット(クーポン)" data-menu-time="45" data-menu-price="3200">このクーポンを使う</button>
            </div>
          </div>
        </div>
        <div class="stylist">
          <h3 class="card-header">スタイリスト</h3>
          <div class="stylist-block">
          <div class="stylist-card">
            <img src="assets/img/mens.png" alt="スタイリスト画像">
            <div class="stylist-info">
              <p class="name">スタイリストA</p>
              <p>ナチュラルスタイルが得意</p>
              <button class="select-btn use-stylist selectstylist" data-stylist-id="スタイリストA">このスタイリストを選ぶ</button>
            </div>
          </div>
          <div class="stylist-card">
            <img src="assets/img/woman.png" alt="スタイリスト画像">
            <div class="stylist-info">
              <p class="name">スタイリストB</p>
              <p>カラー・ブリーチが得意</p>
              <button class="select-btn use-stylist selectstylist" data-stylist-id="スタイリストB">このスタイリストを選ぶ</button>
            </div>
          </div>
          </div>
        </div>
        <div class="menu">
          <h3 class="card-header">メニュー</h3>
          <p class="dis">カット</p>
          <div class="menu-block">
            <div class="menu-card">
              <div class="menu-info">
                <p>カット: ￥4,000- <br>(所要時間: 60分)</p>
                <button class="select-btn  menu-select use-menu-cut" data-menu-time="60" data-menu-price="4000" data-menu-id="カット">このメニューを選ぶ</button>
              </div>
            </div>
            <div class="menu-card">
              <div class="menu-info">
                  <p>キッズカット: ￥1,000- <br>(所要時間: 30分)</p>
                  <button class="select-btn  menu-select use-menu-cut" data-menu-time="30" data-menu-price="1000" data-menu-id="キッズカット">このメニューを選ぶ</button>
              </div>
            </div>
            <div class="menu-card">
              <div class="menu-info">
                  <p>前髪カット(前髪のみ): ￥1,100- <br>(所要時間: 15分)</p>
                  <button class="select-btn  menu-select use-menu-cut" data-menu-time="15" data-menu-price="1100" data-menu-id="前髪カット">このメニューを選ぶ</button>
              </div>
            </div>
          </div>
          <p class="dis">カラー</p>
          <div class="menu-block">
            <div class="menu-card">
              <div class="menu-info">
                <p>リタッチカラー: ￥3,000- <br>(所要時間: 90分)</p>
                <button class="select-btn  menu-select use-menu-color" data-menu-time="90" data-menu-price="3000" data-menu-id="リタッチカラー">このメニューを選ぶ</button>
              </div>
            </div>
            <div class="menu-card">
              <div class="menu-info">
                  <p>フルカラー: ￥5,000- <br>(所要時間: 120分)</p>
                  <button class="select-btn  menu-select use-menu-color" data-menu-time="120" data-menu-price="5000" data-menu-id="フルカラー">このメニューを選ぶ</button>
              </div>
            </div>
            <div class="menu-card">
              <div class="menu-info">
                  <p>ハイライトカラー: ￥6,000- <br>(所要時間: 150分)</p>
                  <button class="select-btn  menu-select use-menu-color" data-menu-time="150" data-menu-price="6000" data-menu-id="ハイライトカラー">このメニューを選ぶ</button>
              </div>
            </div>
          </div>
          <p class="dis">パーマ</p>
          <div class="menu-block">
            <div class="menu-card">
              <div class="menu-info">
                <p>デジタルパーマ: ￥8,000- <br>(所要時間: 180分)</p>
                <button class="select-btn  menu-select use-menu-parma" data-menu-time="180" data-menu-price="8000" data-menu-id="デジタルパーマ">このメニューを選ぶ</button>
              </div>
            </div>
            <div class="menu-card">
              <div class="menu-info">
                  <p>ポイントパーマ: ￥4,000- <br>(所要時間: 90分)</p>
                  <button class="select-btn  menu-select use-menu-parma" data-menu-time="90" data-menu-price="4000" data-menu-id="ポイントパーマ">このメニューを選ぶ</button>
              </div>
            </div>
            <div class="menu-card">
              <div class="menu-info">
                  <p>縮毛矯正: ￥10,000- <br>(所要時間: 240分)</p>
                  <button class="select-btn  menu-select use-menu-parma" data-menu-time="240" data-menu-price="10000" data-menu-id="縮毛矯正">このメニューを選ぶ</button>
              </div>
            </div>
          </div>
          <p class="dis">その他</p>
          <div class="menu-block">
            <div class="menu-card">
              <div class="menu-info">
                <p>ヘッドスパ: ￥4,000- <br> (所要時間: 60分)</p>
                <button class="select-btn  menu-select use-menu-other" data-menu-time="60" data-menu-price="4000" data-menu-id="その他：ヘッドスパ">このメニューを選ぶ</button>
              </div>
            </div>
            <div class="menu-card">
              <div class="menu-info">
                  <p>シャンプー&ブロー: ￥1,000- <br> (所要時間: 30分)</p>
                  <button class="select-btn  menu-select use-menu-other" data-menu-time="30" data-menu-price="1000" data-menu-id="その他：シャンプーブロー">このメニューを選ぶ</button>
              </div>
            </div>
            <div class="menu-card">
              <div class="menu-info">
                  <p>ヘアセット: ￥3,000- <br>(所要時間: 45分)</p>
                  <button class="select-btn  menu-select use-menu-other" data-menu-time="45" data-menu-price="3000" data-menu-id="その他：ヘアセット">このメニューを選ぶ</button>
              </div>
            </div>
          </div>
        </div>
        <div id="menulist" >
            <h3 id="menu-head" class="card-header">現在選択しているメニュー<span class="material-symbols-outlined">unfold_more_double</span></h3>
            <div id="menulist-main">
            </div>
            <p id="schedule"></p>
          </div>
        <div class="schedule">
          <h3 class="card-header">予約カレンダー</h3>
          <div id="calendar"></div>
          <p>カレンダーが正常に表示されない場合は下のリロードボタンを押してください</p>
          <button id="reloadButton" class="reload" style="z-index:99999;">カレンダーをリロードする</button>
        </div>
      </section>
      <section class="schedule section">
        <h3 class="card-header">予約履歴</h3>
        <!-- これまでの予約一覧 -->
        <table class="reservation">
          <?php 
            if($row_cnt1 < 1){
              echo "<p>まだ予約がありません。</p>";
            }else{
          ?>
          <tr class="thead">
            <th>番号</th>
            <th>予約日</th>
            <th>メニュー</th>
            <th>スタイリスト</th>
            <th>金額</th>
            <th>ステータス</th>
            <th>キャンセル</th>
          </tr>
          <?php 
            }
            $conn = getDbConnection();
            $num = 0; 
            if(is_string($result1)){
              echo '<p>エラーが発生しました。再度ログインしてください。</p>';
            }else {
              while($row = $result1 -> fetch_assoc()){
                echo '<tr class="tbody">';
                echo "<td>" . $num++ . "</td>";
                echo "<td>" . $row['reservation_datetime'] . "</td>";
                echo "<td>" . $row['menu'] . "</td>";
                echo "<td>" . $row['stylist'] . "</td>";
                echo "<td>" . $row['price'] . "円</td>";
                echo "<td>" . getStatus($row['status']). "</td>";
                if(getStatus($row['status']) !== "キャンセル"){
                  echo '<td><button id="canselbtn" class="canselbtn" data-cansel-btn="'. $row['reservation_id'] .'" type="button">予約内容をキャンセルする</button></td>';
                }else{
                  echo '<td class="no-cansel"> --- </td>';
                }
                echo "</tr>";
              }
            }
          ?>
        </table>

      </section>
      <section class="Member information section">
        <h3 class="card-header">会員情報変更</h3>
        
        <!-- 会員情報変更フォーム -->
        <?php
          if(!empty($errormsg)){
            echo '<p>' . $errormsg . '</p>';
          }
          $conn = getDbConnection();
          $usercard = showUserCard($conn, $user_id, $mailaddress);
          $fulladdress = "";
          if(is_string($usercard)){
            echo '<p>エラーが発生しました。再度ログインしてください。</p>';
          }else if($usercard->num_rows === 0){
            echo '<p>まだ会員情報がありません。カルテ入力をお願いします。</p>';
          }else{
            while($list = $usercard -> fetch_assoc()){
              $parts = explode(" ", $list['address']);
              $postal_code = $parts[0];
              for ($i = 1; $i < count($parts); $i++) {
                $fulladdress .= $parts[$i];

              }       
        ?>

        <form action="update.php" method="post">
          <label>
            名前<br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($list['name']); ?>" required><br>
          </label>
          <label>
            メールアドレス<br>
            <input type="email" name="mailaddress" value="<?php echo htmlspecialchars($list['mailaddress']); ?>" required><br>
          </label>
          <label>
            郵便番号<br>
            <input type="text" name="postal_code" value="<?php echo $postal_code; ?>" required><br>
            住所<br>
            <input type="text" name="address" value="<?php echo htmlspecialchars($fulladdress); ?>" required><br>
          </label>
          <label>
            電話番号<br>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($list['phone']); ?>" required><br>
          </label>
          <label>
          DM希望<br>
          <select name="dm_flg">
            <option value="1" <?php echo $list['dm_flg'] == 1 ? 'selected' : ''; ?>>希望する</option>
            <option value="0" <?php echo $list['dm_flg'] == 0 ? 'selected' : ''; ?>>希望しない</option>
          </select><br>
          </label>
          <input type="hidden" name="updateUserCard">
          <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" required>
          <input type="hidden" name="id" value="<?php echo $list['id']; ?>" required>
          <input type="hidden" name="csrfToken" value="<?php echo htmlspecialchars($csrfToken); ?>" required>
          <input type="submit" value="変更する">
        </form>
        <?php }} ?>

      </section>
    </main>
  </div>
  <script src="assets/js/form.js"></script>
  <script src="assets/js/select.js"></script>
</body>
</html>
