<?php
include 'functions.php';
$csrfToken = getToken();
$errors = [];
$message1 = '';
$message2 = '';


// 必須項目
$address = htmlspecialchars($_POST['address']);
$dm_flg = htmlspecialchars(intval($_POST['dm_flg']));

// ユーザーカルテ情報
$name = htmlspecialchars($_POST['name']);
$mailaddress = htmlspecialchars($_POST['mailaddress']);
$tel = htmlspecialchars($_POST['phone']);
$dm_flg = htmlspecialchars(intval($_POST['dm_flg']));
$birthdate = isset($_POST['birthdate']) ? htmlspecialchars($_POST['birthdate']) : null;

// なかったらnullで
$postal_code = htmlspecialchars($_POST['postal_code']) ??  null;

//予約テーブル必要情報(なかったらnullで)
$reservationDate = isset($_POST['reservationDate']) ? htmlspecialchars($_POST['reservationDate']) : null;
$menu = isset($_POST['menu']) ? htmlspecialchars($_POST['menu']) : null;
$price = isset($_POST['price']) ? htmlspecialchars(intval($_POST['price'])) : null;
$admin_comment = isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : null;
$reservation_id = isset($_POST['reservation_id']) ? htmlspecialchars($_POST['reservation_id']) : null;


// --------------------------------------- バリデーションここから
// 名前
    if(empty($name) || mb_strlen($name) >= 20  ){
        $errors[] = "氏名の入力は必須です。氏名は20文字以内で入力してください";
    }
    // 電話番号
    if(empty($tel) || !preg_match("/\A0[5789]0[-(]?\d{4}[-)]?\d{4}\z/", $tel)){
        $errors[] = "電話番号の入力は必須です。携帯の番号を入力してください。";
    }
    // 郵便番号
    if(empty($postal_code) || 8 > mb_strlen($postal_code)){
        $errors[] ="郵便番号の入力は必須です(ハイフン含めた8文字以内で入力してください)";
    }
    // 住所
    if(empty($address) ){
        $errors[] = "住所が未入力です。";
    }
    if(!empty($errors)){    
        $_SESSION['errors'] = $errors;
    }
    // --------------------------------------- バリデーションここまで

$user_id = intval($_POST['user_id']);
$id = intval($_POST['id']);

if (isset($_POST['updateUserCard']) && hash_equals($csrfToken, $_POST['csrfToken'])) {

    $fulladdress = $postal_code . ' ' . $address;
    try {
        $conn = getDbConnection();

        // user_card_tableの更新
        $result1 = userCardTableUpdate($conn, $name, $tel, $fulladdress, $admin_comment, $dm_flg, $user_id);
        
        // user_tableの更新
        $result2 = userTableUpdate($conn, $mailaddress, $user_id);
        if(is_string($result1) || is_string($result2)){
            echo $result1 . "<br>";
            echo $result2 . "<br>";
        }else {
            header('Location: userdashbord.php');
            exit();
        }

    } catch (Exception $e) {
        $conn->rollback();
        echo "エラー: " . $e->getMessage();
    }

    $conn->close();
    }else if(isset($_POST['adminupdateUserCard']) && hash_equals($csrfToken, $_POST['csrfToken'])) {
    // 管理者側からのユーザーカルテアップデート
    try {
        $conn = getDbConnection();
        // user_card_tableの更新
        $message = userCardTableUpdate($conn, $name, $tel, $birthdate, $address, $admin_comment, $dm_flg, $user_id);
        // 確認
        echo $name, $tel, $birthdate, $address, $admin_comment, $dm_flg, $user_id;
        // header('Location: dashboard.php');
        // exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "エラー: " . $e->getMessage();
    }

    }else if(isset($_POST['adminupdateReservation']) && hash_equals($csrfToken, $_POST['csrfToken'])) {
    // 管理者側からの予約内容や金額の変更
    // user_tableの更新
    try {
        $conn = getDbConnection();
        // reservationsの更新
        reservationsUpdate($conn, $reservationDate, $menu, $price, $user_id, $reservation_id);
        header('Location: dashboard.php');
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "エラー: " . $e->getMessage();
    }
}