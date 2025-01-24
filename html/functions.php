<?php
function getToken() {
    // セッションが開始されていない場合は開始
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // トークンがセットされていない場合に生成
    if (!isset($_SESSION['csrfToken'])) {
        $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
    }
    
    // セッションに保存したトークンを返す
    return $_SESSION['csrfToken'];
}

  // db接続
  function getDbConnection(){
    $conn = new mysqli("mysql", "test", "test", "heirsalon_php");
    if($conn->connect_error){
      die("データベース接続失敗" . $conn->connect_error);
    }
    return $conn;
  }

  // login(admin/user)
  function loginCheck($conn, $table, $mailaddress, $password, $page){
    $sql = 'SELECT * FROM ' . $table . ' WHERE mailaddress = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mailaddress);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      if(password_verify($password, $user['password'])){
        // user: sessionにidを入れる
        if(isset($table) && $table == "user_table"){
          $_SESSION['user_id'] = $user['user_id'];
        }
        $_SESSION['mailaddress'] = $mailaddress;
        $_SESSION['admin_login'] = true;
        header('Location: '. $page);
        exit();
      } else {
        $message = $stmt->error . '<span style="color:red";>メールアドレス、またはパスワードが間違っています。</span>';
      }
    } else {
        $message = $stmt->error . '<span style="color:red";>メールアドレス、またはパスワードが間違っています。</span>';
    }

    return $message;
  }

  //newadmin(admin)
  function newadin($conn, $table, $mailaddress, $password, $page){
    $message = "";
    $sql = 'SELECT * FROM '. $table . ' WHERE mailaddress = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mailaddress);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $message = '<span style="color:red";>メールアドレスは既に使われているため登録できません。</span>';
    } else {
        // パスワードのハッシュ化
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = 'INSERT INTO ' . $table . ' (mailaddress, password) VALUES(?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $mailaddress, $hashedPassword);

        if ($stmt->execute()) {
            $_SESSION['user'] = $mailaddress;
            $_SESSION['admin_login'] = true;
            header('Location: '. $page);
            exit();
        }else{
          $errorMessage = $stmt->error; // エラーメッセージを取得
          $message = '<span style="color:red";>登録中にエラーが発生しました: ' . $errorMessage . '</span>';
        }
    }
    return $message;
  }

  // ユーザーカルテ新規追加
  function newusercard($conn, $table, $mailaddress, $password, $user_id, $page, $name, $tel, $barthday, $fulladdress, $dm_flg){
    // user_card_tableチェック
    $message = "";
    $dm_flg = strval($dm_flg);
    $sql = 'SELECT * FROM '. $table . ' WHERE user_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // user_tableチェック
    $sql1 = 'SELECT * FROM user_table WHERE user_id = ? AND mailaddress = ?';
    $stmt1 = $conn -> prepare($sql1);
    $stmt1->bind_param("is", intval($user_id), $mailaddress);
    $stmt1->execute();
    if($stmt1->error){
      return $stmt1->error;
    }
    $result1 = $stmt1->get_result();
    $row_cnt1 = mysqli_num_rows($result1);    

    if ($result->num_rows >= 1 && $row_cnt1 >= 1) {
        $message = '<span style="color:red";>登録が複数あるのを確認しました。再度登録しなおしてください。</span>';
    } else if($result->num_rows == 0 && $row_cnt1 == 1) {
        $sql = 'INSERT INTO ' . $table . ' (user_id, name, birthdate, address, phone, dm_flg) VALUES(?, ?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssi", $user_id, $name, $barthday, $fulladdress, $tel, $dm_flg);

        if ($stmt->execute()) {
            $_SESSION['mailaddress'] = $mailaddress;
            $_SESSION['admin_login'] = true;
            header('Location: '. $page);
            exit();
        }else{
          $errorMessage = $stmt->error; // エラーメッセージを取得
          $message = '<span style="color:red";>登録中にエラーが発生しました: ' . $errorMessage . '</span>';
        }
    }
    return $message;
  }

  // 予約登録
  function newReservation($conn, $checktable, $addtable, $user_id, $response) {
    $message = "";
    $sql = 'SELECT * FROM '. $checktable . ' WHERE user_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 1) {
      $message = '<span style="color:red;">登録がすでに存在します。管理者に連絡してください。</span>';
    } else {
      $startTime = (new DateTime($response['reservationStartTime']))->format('Y-m-d H:i:s');
      $endTime = (new DateTime($response['reservationEndTime']))->format('Y-m-d H:i:s');
      $selectMenu = implode(",", $response['selectMenu']);
      $selectMenuPrice = $response['selectMenuPrice'];
  
      $sql = 'INSERT INTO ' . $addtable . ' (user_id, reservation_datetime, duration, menu, price, stylist) VALUES(?, ?, ?, ?, ?, ?)';
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("isssis", $user_id, $startTime, $endTime, $selectMenu, $selectMenuPrice, $response['selectStylist']);
      
      if ($stmt->execute()) {
        $message = "予約が完了しました。";
      } else {
        $message = 'エラーが発生しました: ' . $stmt->error;
      }
    }
    return $message;
  }


  // 予約一覧表示
  function reservationList($conn, $table, $user_id){
    $message = "";
    $sql = "SELECT * FROM " . $table . " WHERE user_id = ?";
    $stmt = $conn -> prepare($sql);
    $stmt -> bind_param("i", $user_id);
    if ($stmt->execute()) {
      return $result = $stmt->get_result();
    } else {
      $message = 'エラーが発生しました: ' . $stmt->error;
      return $message;
    }
  }

  // ユーザーカルテ表示
  function showUserCard($conn, $user_id, $mailaddress){
    // ユーザー情報確認
    $sql = '
      SELECT
      user_table.user_id AS user_id,
      user_table.mailaddress AS mailaddress,
      user_card_table.id AS id,
      user_card_table.name AS name,
      user_card_table.birthdate AS birthdate,
      user_card_table.address AS address,
      user_card_table.phone AS phone,
      user_card_table.dm_flg AS dm_flg
      FROM user_table
      INNER JOIN user_card_table
      ON user_table.user_id = user_card_table.user_id
      WHERE user_table.user_id = ? AND user_table.mailaddress = ?
    ';
    $stmt = $conn -> prepare($sql);
    $stmt -> bind_param("is", $user_id, $mailaddress);
    if ($stmt->execute()) {
      return $usercard = $stmt->get_result();
    }else if($stmt->affected_rows === 0){
      return $usercard = null;
    } else {
      $message = 'エラーが発生しました: ' . $stmt->error;
      return $message;
    }

  }
  
  //ユーザー一覧取得
  function getUserList($conn){
    $sql = "
      SELECT 
      user_table.user_id AS user_id,
      user_card_table.name AS name,
      user_table.mailaddress AS mailaddress,
      user_card_table.phone AS phone
      FROM 
      user_table
      INNER JOIN 
      user_card_table ON user_table.user_id = user_card_table.user_id
      LIMIT 10;
    ";
    $stmt = $conn -> prepare($sql);
    if ($stmt->execute()) {
      return $usercard = $stmt->get_result();
    } else {
      $message = 'エラーが発生しました: ' . $stmt->error;
      return $message;
    }

  }
  // user_card_table更新
  function userCardTableUpdate($conn, $name, $tel, $fulladdress, $admin_comment, $dm_flg, $user_id){
    
    $user_card_tablesql = "
    UPDATE user_card_table
    SET
    name = ?,
    phone = ?,
    birthdate = ?,
    address = ?,
    dm_flg = ?,
    admin_comment = ?
    WHERE user_id = ?;
    ";
  
    $stmt1 = $conn->prepare($user_card_tablesql);
    $stmt1->bind_param("ssssisi", $name, $tel, $birthdate, $fulladdress, $dm_flg, $admin_comment, $user_id);
    $stmt1->execute();
    // エラーメッセージの取得 
    if (!empty($stmt1->error)) { 
      return $message = $stmt1->error;
    }
    
  }
  
  // user_table更新
  function userTableUpdate($conn, $mailaddress, $user_id){
    $user_table_sql = "
      UPDATE user_table
      SET
      mailaddress = ?
      WHERE user_id = ?
    ";
  
    $stmt2 = $conn->prepare($user_table_sql);
    $stmt2->bind_param("si", $mailaddress, $user_id);
    $stmt2->execute();
    if (!empty($stmt2->error)) { 
      return $message = $stmt2->error;
    }
    
  }

  // reservationsの更新
  function reservationsUpdate($conn, $reservationDate, $menu, $price, $user_id, $reservation_id){
    $user_table_sql = "
    UPDATE reservations
    SET
    reservation_datetime = ?,
    menu = ?,
    price = ?
    WHERE user_id = ?
    AND reservation_id = ?
    ";
  
    $stmt2 = $conn->prepare($user_table_sql);
    $stmt2->bind_param("ssiii", $reservationDate, $menu, $price, $user_id, $reservation_id);
    $stmt2->execute();
  }


  // ステータスチェック
  function getStatus($status){
    switch ($status) {
      case '0':
        return "未完了";
        break;
      case '1':
        return "完了";
        break;
      case '2':
        return "キャンセル";
        break;
      case '3':
        return "変更済み";
        break;
      case '4':
        return "その他";
        break; 
      default:
        return "予約状態を確認できませんでした。";
        break;
    }
  }
  // 最近の予約1件取得
  function lastResrtvation($conn, $user_id){
    
    $sql = "
      SELECT * FROM reservations  WHERE user_id = ? ORDER BY reservation_id DESC LIMIT 1;
    ";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("i", intval($user_id));
    $stmt2->execute();
    if ($stmt2->error) { 
      return $stmt2->error;
    }
    $result = $stmt2->get_result();
    $lastReservation = $result->fetch_assoc();
    return $lastReservation;
  }
  
  // 本日の件数
  function getNumberOfEvent($conn){
    $sql = '
      SELECT
      user_table.user_id AS user_id,
      reservations.reservation_id AS reservation_id,
      user_card_table.name AS name,
      user_card_table.phone AS phone,
      reservations.stylist AS stylist,
      reservations.menu AS menu,
      reservations.reservation_datetime AS datetime,
      reservations.price AS price,
      reservations.status AS status
      FROM user_table
      INNER JOIN user_card_table
      ON user_table.user_id = user_card_table.user_id
      INNER JOIN reservations
      ON reservations.user_id = user_table.user_id
      WHERE DATE(reservation_datetime) = CURDATE();
    ';
    $stmt = $conn -> prepare($sql);
    $stmt->execute();
    if($stmt->error){
      return $stmt->error;
    }
    $result = $stmt->get_result();
    // $todayReservations = $result->fetch_assoc();
    return $result;
  }
  function sessionCheck($conn, $user_id, $mailaddress){
    // user_tableチェック
    $sql1 = 'SELECT * FROM user_table WHERE user_id = ? AND mailaddress = ?';
    $stmt1 = $conn -> prepare($sql1);
    $stmt1->bind_param("is", intval($user_id), $mailaddress);
    $stmt1->execute();
    if($stmt1->error){
      return $stmt1->error;
    }
    $result1 = $stmt1->get_result();
    $row_cnt1 = mysqli_num_rows($result1);

    // user_card_tableチェック
    $sql2 = "SELECT * FROM user_card_table WHERE user_id = ?";
    $stmt2 = $conn -> prepare($sql2);
    $stmt2->bind_param("i", $user_id);
    $stmt2 -> execute();
    if($stmt2->error){
      return $stmt2->error;
    }
    $result2 = $stmt2->get_result();
    $row_cnt2 = mysqli_num_rows($result2);


    if($row_cnt1 == 1 && $row_cnt2 == 0){
      header('location: user_card.php');
      exit();
    }
  }