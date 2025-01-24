<?php
include 'functions.php';
$csrfToken = getToken();

  if(!isset($_SESSION['mailaddress']) || !$_SESSION['admin_login']){
    header('Location: login.php');
    exit();
  }

  // ログアウト
  if(!empty($_GET['logout_btn'])){
    unset($_SESSION['admin_login']);
    unset($_SESSION['mailaddress']);
    header('Location: admin.php');
    exit();
  }
  // db接続確認
  $conn = getDbConnection();
  
  // // 本日の予約取得
  $setResult = getNumberOfEvent($conn);
  if(is_string($setResult)){
    echo "エラーが発生しました。";
  }else{
    $row_cnt = mysqli_num_rows($setResult);
  }


?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Guides:wght@400..700&family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- フルカレンダー -->
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/daygrid/main.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/daygrid/main.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.0/google-calendar.min.js'></script>
  <script src="assets/js/main.js"></script>
  <link rel="stylesheet" href="assets/css/styles.css">
  <title>管理者ダッシュボード</title>
</head>
<body>
  <header>
    <h1>管理者ダッシュボード</h1>
    <form action="" method="get">
      <input class="button" type="submit" name="logout_btn" value="ログアウト">
    </form>
  </header>
  <div class="container">
    <aside class="sidebar">
      <ul>
        <li class="card-header">メニュー</li>
        <li class="list"><a href="#">ダッシュボード</a></li>
        <li class="list userlist"><a href="#">ユーザー管理</a></li>
        <li class="list"><a href="#">予約スケジュール表</a></li>
      </ul>
    </aside>
    <main class="main-content">
      <section class="dashbord section active-section">
        <h3 class="card-header">ダッシュボード</h3>
        <p>こんにちは、<?php echo htmlspecialchars($_SESSION['mailaddress']) . " さん"; ?></p>
        <p>本日の予約件数: <?php echo $row_cnt;?>件</p>
        <div class="info">
        
          <div class="userList">
            <table class="reservation">
            <caption>本日の予約者様</caption>
              <tr>
                <th>予約時間</th>
                <th>名前</th>
                <th>電話番号</th>
                <th>メニュー</th>
                <th>スタイリスト</th>
                <th>金額</th>
                <th>ステータス</th>
              </tr>
            <?php
              while($row = $setResult -> fetch_assoc()){
                echo '<tr>';
                echo "<td>" . htmlspecialchars($row['datetime']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['stylist']) . "</td>";
                echo "<td>" . htmlspecialchars($row['menu']) . "</td>";
                echo "<td>" . htmlspecialchars(intval($row['price'])) . "円</td>";
                echo "<td>" . htmlspecialchars(getStatus($row['status'])). "</td>";
                echo '</tr>';
              }
            ?>
            </table>
          </div>
        </div>
      </section>
      <section class="users section">
        <h3 class="card-header">ユーザー一覧</h3>
        <div class="box">
        <form id="search-form" class="search_box">
          <input type="text" name="search_input" id="keyword" placeholder="ユーザー検索">
          <input type="submit" name="search_user" id="searchBtn">
        </form>
        <!-- ユーザー一覧box -->
        <div id="result" class="userbox">
          <?php
            $list = getUserList($conn);
            if(is_string($list)){
              echo "<p>エラーが発生しました。" . $list . "</p>"; 
            }else{
              echo '<table class="userRservationList">';
              echo '<tr class="thead">';
              echo "<th>氏名</th>";
              echo "<th>メールアドレス</th>";
              echo "<th>電話番号</th>";
              echo "</tr>";
              while($result = $list -> fetch_assoc()){
                echo '<tr class="tbody" data-user-id="' . $result['user_id'] . '">';
                echo '<td>' . htmlspecialchars($result['name']) . "</td>";
                echo '<td>' . htmlspecialchars($result['mailaddress']) . "</td>";
                echo '<td>' . htmlspecialchars($result['phone']) . "</td>";
                echo "</tr>";
              }
              echo "</table>";
            }            
          ?>
          </div>
        </div>
        <div id="userDetail" class="hidden">
          <h3 class="card-header">ユーザー詳細</h3>
          <div id="userDetailContent"><?php //userlist.jsとget_user_detail.phpで中身を表示?></div>
          <p></p>
        </div>
      </section>
      <section class="schedule section">
        <h3 class="card-header">予約表</h3>
          <div id="adminCalendar"></div>
          <button id="reloadButton" type="button">リロード</button>
        <div id="reservationDetail">
          <h3 class="card-header detail">予約詳細</h3>
          <p id="resultdetail"></p>
        </div>
        
      </section>
    </main>
  </div>
  <script src="assets/js/userlist.js"></script>
  <script src="assets/js/adminCalendar.js"></script>
</body>
</html>
