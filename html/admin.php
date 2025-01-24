<?php
include 'functions.php';
$csrfToken = getToken();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['login']) && hash_equals($csrfToken, $_POST['csrfToken'])){
        $mailaddress = $_POST['email'];
        $password = $_POST['password'];

        // db接続確認
        $conn = getDbConnection();

        // sqlで確認
        $table = "admin_table";
        $page = "dashboard.php";
        $message = loginCheck($conn, $table, $mailaddress, $password, $page);

    } else if (isset($_POST['newadmin']) && hash_equals($csrfToken, $_POST['csrfToken'])){
        //新規登録の場合
        $mailaddress = $_POST['newmail'];
        $password = $_POST['newpassword'];

        // db接続確認
        $conn = getDbConnection();

        // 重複チェック
        $table = "admin_table";
        $page = "dashboard.php";
        $message = newadin($conn, $table, $mailaddress, $password, $page);
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
  <title>管理者ログインページ</title>
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
        <li class="admin form-li active-tab">ログイン</li>
        <li class="admin form-li">管理者追加</li>
      </ul>
      <div class="login tab-content active-content">
        <?php
          echo isset($message) ? $message: "";
        ?>
        <h3>こんにちわ</h3>
        <form action="admin.php" method="POST">
          <label>
            メールアドレス<br>
            <input type="email" name="email" placeholder="example@example.com" required><br>
          </label>
          <label>
            パスワード<br>
            <input type="password" name="password" required><a href="#">パスワードをお忘れですか？</a><br>
          </label>
          <input type="hidden" name="login" value="login">
          <input type="hidden" name="csrfToken" value="<?php echo htmlspecialchars($csrfToken); ?>">
          <input type="submit" style=" background-color: #ffc58f;" value="ログインする">
        </form>
      </div>
      <div class="new-user tab-content">
        <?php
          echo isset($message) ? $message: "";
        ?>
        <h3>はじめまして</h3>
        <form action="admin.php" method="post">
          <label>
            メールアドレス<br>
            <input name="newmail" type="email" placeholder="example@example.com" required><br>
          </label>
          <label>
            パスワード<br>
            <input name="newpassword" type="password" required><br>
            <br>
          </label>
          <input type="hidden" name="newadmin" value="newadmin">
          <input type="hidden" name="csrfToken" value="<?php echo htmlspecialchars($csrfToken); ?>">
          <input type="submit" style=" background-color: #ffc58f; "value="新規登録する">
        </form>
      </div>
    </div>
  </main>
</body>
</html>