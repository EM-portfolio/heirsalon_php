<?php
// Google Clientの初期設定
require_once 'vendor/autoload.php';
use Google\Client;
use Google\Service\Calendar;

$client = new Client();
$client->setAuthConfig('/var/www/html/credentials.json');
$client->setScopes(Calendar::CALENDAR_EVENTS);
$client->setRedirectUri('http://localhost:8080');

// トークンを保存するパス
$tokenPath = '/var/www/html/token.json';

if (isset($_GET['code'])) {
  $code = $_GET['code'];

  try {
      // 認証コードを使ってアクセストークンを取得
      $accessToken = $client->fetchAccessTokenWithAuthCode($code);
      
      // アクセストークンが正常に取得できたか確認
      if (isset($accessToken['access_token'])) {
          // トークンを保存する
          file_put_contents($tokenPath, json_encode($accessToken));
          echo "認証が成功しました。";
      } else {
          echo "アクセストークンの取得に失敗しました。";
      }
  } catch (Exception $e) {
      echo "トークンの交換に失敗しました: " . $e->getMessage();
  }
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Guides:wght@400..700&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="assets/js/main.js"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <title>HairSalon-portfolio-</title>
</head>
<body>
  <header>
    <div id="sticky" class="sticky-header">
      <a href="login.php" class="header-link">ログイン</a>
      <a href="#" class="header-link">管理者用</a>
    </div>
    <img class="mobile-img" src="assets/img/Mfirst.webp" alt="">
    <img class="desktop-img" src="assets/img/Pfirst.webp" alt="">
  </header>
  <main>
    <nav class="container">
      <ul class="nav-list" >
        <li class="color-on edu-au-vic-wa-nt-guides"><a href="#">Concept</a></li>
        <li class="edu-au-vic-wa-nt-guides"><a href="#menu">Menu</a></li>
        <li class="edu-au-vic-wa-nt-guides"><a href="#reservation">Reservation</a></li>
      </ul>
    </nav>
    <div id="concept" class="content-on concept edu-au-vic-wa-nt-guides container">
      <h3>A salon that brings out the natural beauty of the body</h3>
      <p>
        At our salon, we value each customer's individuality and offer styles that bring out their natural beauty. <br>
         We provide attentive counseling and techniques so that you can spend a pleasant time in a relaxing space.
      </p>
    </div>
    <div id="menu" class="content-on menu edu-au-vic-wa-nt-guides container">
      <h3>Menu</h3>
      <caption>Cut</caption>
      <ul>
        <li>Cut ￥4,000-</li>
        <li>KidsCut ￥1,000-</li>
        <li>Bangs Cut ￥1,100-</li>
      </ul>
      <caption>Collar</caption>
      <ul>
        <li>Retouch Color ￥4,000-</li>
        <li>FullColor ￥1,000-</li>
        <li>Highlight ￥1,100-</li>
      </ul>
      <caption>Perm</caption>
      <ul>
        <li>Digital Perm￥4,000-</li>
        <li>Point Perm ￥1,000-</li>
        <li>Straight Perm￥1,100-</li>
      </ul>
      <caption>Other</caption>
      <ul>
        <li>HeadSpa￥4,000-</li>
        <li>Shampoo&Blow ￥1,000-</li>
        <li>SetUp￥1,100-</li>
      </ul>
    </div>
    <div id="reservation" class="content-on reservation edu-au-vic-wa-nt-guides container">
      <h3>Reservation</h3>
      <p>予約はこちらから <a href="login.php">login</a></p>
    </div>
  </main>
</body>
</html>