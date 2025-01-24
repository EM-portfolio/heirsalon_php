<?php
include 'functions.php';
$csrfToken = getToken();
  $errors = [];
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
      if (isset($_POST['newcard']) && hash_equals($csrfToken, $_POST['csrfToken'])) {

        $mailaddress = $_SESSION['mailaddress'];
        $password = $_SESSION['password'];

        // ユーザーカルテ情報
        $name = htmlspecialchars($_POST['name']);
        $tel = htmlspecialchars($_POST['tel']);
        $barthday = htmlspecialchars($_POST['barthday']);
        $postal_code = htmlspecialchars($_POST['postal-code']);
        $ken = htmlspecialchars($_POST['ken']);
        $city = htmlspecialchars($_POST['city']);
        $address = htmlspecialchars($_POST['address']);
        $building = htmlspecialchars($_POST['building']);
        $dm_flg = isset($_POST['dm_flg']) ? htmlspecialchars(intval($_POST['dm_flg'])) : 0;
        // --------------------------------------- バリデーションここから
        // 名前
        if(empty($name) || mb_strlen($name) >= 20  ){
          $errors[] = "氏名の入力は必須です。氏名は20文字以内で入力してください";
        }
        // 電話番号
        if(empty($tel) || !preg_match("/\A0[5789]0[-(]?\d{4}[-)]?\d{4}\z/", $tel)){
          $errors[] = "電話番号の入力は必須です。携帯の番号を入力してください。";
        }
        // 誕生日check1
        if(empty($barthday) || 10 > mb_strlen($barthday)){
          $errors[] ="生年月日の入力は必須です。(スラッシュ含めた10文字以内で入力してください)";
        }
        // 誕生日check2
        list($year, $month, $day) = explode('/', $barthday);
        if(!checkdate($month, $day, $year)){
          $errors[] = "無効な日付が入力されました。有効な日付を入力してください。";
        }
        // 郵便番号
        if(empty($postal_code) || 8 > mb_strlen($postal_code)){
          $errors[] ="郵便番号の入力は必須です(ハイフン含めた8文字以内で入力してください)";
        }
        // 住所
        if(empty($ken) || empty($city) || empty($address) ){
          $errors[] = "都道府県、市町村、その他住所が未入力です。";
        }
        if(!empty($errors)){
          
          $_SESSION['errors'] = $errors;
          header("Location: user_card.php");
          exit();
        }

        // --------------------------------------- バリデーションここまで
      }else if(isset($_POST['addnewcard']) && hash_equals($csrfToken, $_POST['csrfToken'])){
        $mailaddress = $_SESSION['mailaddress'];
        $password = $_SESSION['password'];
        $dm_flg = intval($_POST['dm_flg']);

        // db接続確認
        $conn = getDbConnection();

        // user_tableのIDを取得
        $sql = 'SELECT * FROM user_table WHERE mailaddress = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $mailaddress);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
          $user = $result->fetch_assoc();
          if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user'] = $mailaddress;
          }
        }
        // 登録情報の準備
        $name = $_POST['name'];
        $tel = $_POST['tel'];
        $barthday = $_POST['barthday'];
        // 住所の生成
        $postal_code = htmlspecialchars($_POST['postal-code'], ENT_QUOTES, 'UTF-8');
        $ken = htmlspecialchars($_POST['ken'], ENT_QUOTES, 'UTF-8');
        $city = htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
        $building = htmlspecialchars($_POST['building'], ENT_QUOTES, 'UTF-8');
        $fulladdress = $postal_code . ' ' . $ken . ' ' . $city . ' ' . $address . ' ' . $building;
        // insert
        $table = "user_card_table";
        $page = "userdashbord.php";
        $message = newusercard($conn, $table, $mailaddress, $password, $_SESSION['user_id'], $page, $name, $tel, $barthday, $fulladdress, $dm_flg);
                   
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
  <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Guides:wght@400..700&family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  
  <link rel="stylesheet" href="assets/css/styleform.css">
  <title>ログインページ</title>
  <style>
    span {
      font-weight: bold;
    }
  </style>
</head>
<body>
  <main>
    <div class="content-on">
      <h3>カルテ内容確認</h3>
      <p>
        以下の内容をご確認ください。<br>
        <?php
          echo isset($message) ? $message: null;
        ?>
      </p>
      <h4>基本情報</h4>
      <hr>
      
      <p><span>名前 : </span><?php echo  htmlspecialchars($name); ?></p> 
      
      <p><span>電話番号 : </span><?php echo  htmlspecialchars($tel); ?></p>
      

      <p><span>生年月日 : </span><?php echo  htmlspecialchars($barthday); ?></p>
      
      <h4>住所</h4> 
      <hr>
      <p><span>郵便番号 : </span><?php echo  htmlspecialchars($postal_code); ?></p>
      
      <p><span>都道府県 : </span><?php echo  htmlspecialchars($ken); ?></p>
      
      <p><span>市町村 : </span><?php echo  htmlspecialchars($city); ?></p>
      
      <p><span>番地 : </span><?php echo  htmlspecialchars($address); ?></p>
      
      <p><span>建物名・部屋番号 : </span><?php echo  htmlspecialchars($building); ?></p>
      
      <p>DMの有無 : 
        <?php
          if($dm_flg == 1){
            echo "DM希望";
          }else if($dm_flg == 0){
            echo "DM希望しない";
          }
        
        ?></p>
      <form action="validation.php" method="post">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="tel" value="<?php echo htmlspecialchars($tel, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="barthday" value="<?php echo htmlspecialchars($barthday, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="postal-code" value="<?php echo htmlspecialchars($postal_code, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="ken" value="<?php echo htmlspecialchars($ken, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="city" value="<?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="address" value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="building" value="<?php echo htmlspecialchars($building, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="dm_flg" value="<?php echo htmlspecialchars($dm_flg, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="csrfToken" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <input type="hidden" name="addnewcard">
        <input type="submit" value="送信する">
      </form>
    </div>
  </main>
</body>
<script src="assets/js/form.js"></script>
</html>
