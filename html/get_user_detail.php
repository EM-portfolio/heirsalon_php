<?php
include 'functions.php';
$csrfToken = getToken();

$conn = getDbConnection();

if (isset($_GET['data-user-id'])) {
    $user_id = intval($_GET['data-user-id']);

    $sql = "
        SELECT
        user_table.user_id AS user_id,
        user_table.mailaddress AS mailaddress,
        user_card_table.name AS name,
        user_card_table.birthdate AS birthdate,
        user_card_table.address AS address,
        user_card_table.phone AS phone,
        user_card_table.dm_flg AS dm_flg,
        user_card_table.admin_comment AS comment,
        reservations.reservation_datetime AS datetime,
        reservations.reservation_id AS reservation_id,
        reservations.menu AS menu,
        reservations.status AS status,
        reservations.price AS price,
        reservations.created_at AS create_at
        FROM user_table
        INNER JOIN user_card_table
        ON user_table.user_id = user_card_table.user_id
        LEFT JOIN reservations
        ON user_table.user_id = reservations.user_id
        WHERE user_table.user_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $list = $stmt->get_result();
    $count = 0;
   // ユーザー情報の1行目を取得
   $user_info = $list->fetch_assoc();
   if ($user_info) {
       echo '<form id="userCard" action="update.php" method="post" name="updateUserCard">';
       echo '氏名：<br><input type="text" name="name" value="' . htmlspecialchars($user_info['name']) . '" readonly><br>';
       echo '生年月日：<br><input type="text" name="birthdate" value="' . htmlspecialchars($user_info['birthdate']) . '" readonly><br>';
       echo '住所：<br><input type="text" name="address" value="' . htmlspecialchars($user_info['address']) . '" readonly><br>';
       echo '電話番号：<br><input type="tel" name="phone" value="' . htmlspecialchars($user_info['phone']) . '" readonly><br>';
       echo 'DMフラグ：<br><input type="text" name="dm_flg" value="' . htmlspecialchars($user_info['dm_flg']) . '" readonly><br>';
       echo '管理者コメント：<br><textarea name="comment" id="" cols="30" rows="10" readonly>' . htmlspecialchars($user_info['comment']) . '</textarea><br>';
       echo 'DM希望<br><select name="dm_flg" readonly>';
       echo '<option value="1" ' . ($user_info['dm_flg'] == 1 ? 'selected' : '') . '>希望する</option>';
       echo '<option value="0" ' . ($user_info['dm_flg'] == 0 ? 'selected' : '') . '>希望しない</option>';
       echo '</select><br>';
       echo '<input type="hidden" name="user_id" value="' . $user_id . '">';
       echo '<input type="hidden" name="adminupdateUserCard">';
       echo '<input type="hidden" name="csrfToken" value="' . htmlspecialchars($csrfToken) . '">';
       echo '<button id="userCardChange" type="button">ユーザー情報を変更する</button>';
       echo '</form>';
       echo '<h3 class="card-header">予約詳細</h3>';
       // 予約情報の出力
       if (empty($user_info['create_at']) && $list->num_rows <= 1) {
           echo '<p>予約情報はありませんでした。</p>';
       } else {
           // 予約情報の繰り返し処理
           do {
            if (!empty($user_info['create_at'])) {
                echo '<hr>';
                echo '<form id="reservationChangeForm" action="update.php" method="post">';
                echo '予約作成日：' . htmlspecialchars($user_info['create_at']) . "予約作成日は変更不可です。<br>";
                echo '予約日：<br><input type="text" name="reservationDate" value="' . htmlspecialchars($user_info['datetime']) . '" readonly><br>';
                echo 'メニュー：<br><input type="text" name="menu" value="' . htmlspecialchars($user_info['menu']) . '" readonly><br>';
                echo '予約時の金額：<br><input type="text" name="price" value="' . htmlspecialchars(intval($user_info['price'])) . '" readonly><br>';
                echo '<p>ステータス：' . htmlspecialchars(getStatus($user_info['status'])) . '</p>';
                echo '<input type="hidden" name="adminupdateReservation">';
                echo '<input type="hidden" name="user_id" value="' . $user_id . '">';
                echo '<input type="hidden" name="reservation_id" value="' . $user_info['reservation_id'] . '">';
                echo '<input type="hidden" name="csrfToken" value="' . htmlspecialchars($csrfToken) . '">';
                echo '<button id="canselbtn" class="canselbtn" data-cansel-btn="'. $user_info['reservation_id'] .'" type="button">予約内容をキャンセルする</button>';
                echo '<button id="submitbtnReservation' . ++$count . '" class="rsvbtn" type="button">予約内容を変更する</button>';
                echo '</form>';
               }
           } while ($user_info = $list->fetch_assoc());
       }
   } else {
       echo "<p>ユーザー情報が見つかりませんでした。</p>";
   }
   $stmt->close();
} else {
   echo "<p>ユーザー情報が見つかりませんでした。</p>";
}