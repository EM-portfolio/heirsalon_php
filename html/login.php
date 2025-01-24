<?php
include 'functions.php';
$csrfToken = getToken();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['userlogin']) && hash_equals($csrfToken, $_POST['csrfToken'])) {
        $mailaddress = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        // db接続確認
        $conn = getDbConnection();

        // sqlで確認
        $table = "user_table";
        $page = "userdashbord.php";
        $message = loginCheck($conn, $table, $mailaddress, $password, $page);

    } else if (isset($_POST['newuser']) && hash_equals($csrfToken, $_POST['csrfToken'])) {
        //新規登録の場合
        $_SESSION['mailaddress'] = htmlspecialchars($_POST['newmail']);
        $_SESSION['password'] = htmlspecialchars($_POST['newpassword']);

        // db接続確認
        $conn = getDbConnection();

        // 重複チェック
        $table = "user_table";
        $page = "user_card.php";
        $message = newadin($conn, $table, $_SESSION['mailaddress'], $_SESSION['password'], $page);
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="assets/js/main.js"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <title>ログインページ</title>
</head>
<body>
  <header>
    <div id="sticky" class="sticky-header">
      <a href="login.php" class="header-link">ログイン</a>
      <a href="admin.php" class="header-link">管理者用</a>
    </div>
  </header>
  <main>
    <div class="content-on loginform ">
      <ul class="form-ul">
        <li class="form-li active-tab">ログイン</li>
        <li class="form-li">新規登録</li>
      </ul>
      <div class="login tab-content active-content">
        <?php
          echo isset($message) ? $message: null;
        ?>
        <h3>こんにちわ</h3>
        <form action="login.php" method="post">
          <label>
            メールアドレス<br>
            <input type="email" name="email" placeholder="example@example.com" required><br>
          </label>
          <label>
            パスワード<br>
            <input type="password" name="password" required><a href="#">パスワードをお忘れですか？</a><br>
            <input type="hidden" name="userlogin" value="login">
          </label>
          <input type="hidden" name="csrfToken" value="<?php echo htmlspecialchars($csrfToken); ?>">
          <input type="submit" value="ログインする">
        </form>
      </div>

      <div class="new-user tab-content">
        <?php
          echo isset($message) ? $message: "";
        ?>
        <h3>はじめまして</h3>
        <form action="login.php" method="post">
          <label>
            メールアドレス<br>
            <input type="email" name="newmail" placeholder="example@example.com" required><br>
          </label>
          <label>
            パスワード<br>
            <input type="password" name="newpassword" required><br>
            <br>
          </label>
          <input type="hidden" name="newuser" value="newuser">
          <input type="hidden" name="csrfToken" value="<?php echo htmlspecialchars($csrfToken); ?>">
          <input type="submit" value="新規登録する">
        </form>
      </div>
    </div>
  </main>
</body>
</html>