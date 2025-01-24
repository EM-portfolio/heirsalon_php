<?php
include 'functions.php';
$csrfToken = getToken();
$mailaddress = $_SESSION['mailaddress'];
$password = $_SESSION['password'];  
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Guides:wght@400..700&family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  
  <link rel="stylesheet" href="assets/css/styleform.css">
  <title>ログインページ</title>
</head>
<body>
  <main>
    <div class="content-on">
      <h3>お客様カルテの入力</h3>
      <p>ハイフンやスラッシュは自動入力されます。</p>
      <p>電話番号・生年月日・郵便番号は半角英数字で入力してください。</p>
      <?php 
        if(!empty($_SESSION['errors']) && isset($_SESSION['errors'])){
          echo "<ul>";
          foreach ($_SESSION['errors'] as $error) {
            echo '<li style="color:red;">' . $error . '</li>';
          }
          echo "</ul>";
          unset($_SESSION['error']);
        }
      ?>
        <form action="validation.php" method="post">
          <h4>基本情報</h4> 
          <hr color="#ccc">
          <label>
            <p>
              名前
              <input type="text" name="name" autocomplete="name" maxlength="20" placeholder="山田 花子" /><br>
            </p>
          </label>
          <label>
            <p>
              電話番号<br>
              <input type="tel" name="tel" autocomplete="tel" maxlength="13"/><br>
            </p>
          </label>
          <label>
            <p>
              生年月日 <br>
              <input type="text" name="barthday" pattern="\d{4}/\d{2}/\d{2}" placeholder="1990/01/01" maxlength="10" required /><br>
            </p>
          </label>
          <label>
            <h4>住所</h4> 
            <hr color="#ccc">
            <p>
              郵便番号<br>
              <input type="text" name="postal-code" autocomplete="postal-code" maxlength="8" required /><br>
            </p>
            <p>
              都道府県<br>
              <select name="ken">
                <option value="選択してください" >都道府県</option>
                <option value="北海道">北海道</option>
                <option value="青森県">青森県</option>
                <option value="岩手県">岩手県</option>
                <option value="宮城県">宮城県</option>
                <option value="秋田県">秋田県</option>
                <option value="山形県">山形県</option>
                <option value="福島県">福島県</option>
                <option value="茨城県">茨城県</option>
                <option value="栃木県">栃木県</option>
                <option value="群馬県">群馬県</option>
                <option value="埼玉県">埼玉県</option>
                <option value="千葉県">千葉県</option>
                <option value="東京都">東京都</option>
                <option value="神奈川県">神奈川県</option>
                <option value="新潟県">新潟県</option>
                <option value="富山県">富山県</option>
                <option value="石川県">石川県</option>
                <option value="福井県">福井県</option>
                <option value="山梨県">山梨県</option>
                <option value="長野県">長野県</option>
                <option value="岐阜県">岐阜県</option>
                <option value="静岡県">静岡県</option>
                <option value="愛知県">愛知県</option>
                <option value="三重県">三重県</option>
                <option value="滋賀県">滋賀県</option>
                <option value="京都府">京都府</option>
                <option value="大阪府">大阪府</option>
                <option value="兵庫県">兵庫県</option>
                <option value="奈良県">奈良県</option>
                <option value="和歌山県">和歌山県</option>
                <option value="鳥取県">鳥取県</option>
                <option value="島根県">島根県</option>
                <option value="岡山県">岡山県</option>
                <option value="広島県">広島県</option>
                <option value="山口県">山口県</option>
                <option value="徳島県">徳島県</option>
                <option value="香川県">香川県</option>
                <option value="愛媛県">愛媛県</option>
                <option value="高知県">高知県</option>
                <option value="福岡県">福岡県</option>
                <option value="佐賀県">佐賀県</option>
                <option value="長崎県">長崎県</option>
                <option value="熊本県">熊本県</option>
                <option value="大分県">大分県</option>
                <option value="宮崎県">宮崎県</option>
                <option value="鹿児島県">鹿児島県</option>
                <option value="沖縄県">沖縄県</option>
              </select><br>
            </p>
            <p>
              市町村<br>
              <input type="text" name="city" placeholder="市町村" /><br>
            </p>
            <p>
              番地<br>
              <input type="text" name="address" placeholder="番地" /><br>
            </p>
            <p>
              建物名・部屋番号<br>
              <input type="text" name="building" placeholder="建物名・部屋番号" /><br>
            </p>
          </label>
          <label>
            <input type="checkbox" name="dm_flg" value="1">DM希望<br>
            <span>当店より、お知らせをメール宛てにお送りいたします。<br></span>
            <span>不要の場合は、チェックは不要です。</span>
          </label>
            <input type="hidden" name="newcard">
            <input type="hidden" name="csrfToken" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="submit" value="送信する">
        </form>
    </div>
  </main>
</body>
<script src="assets/js/form.js"></script>
</html>
