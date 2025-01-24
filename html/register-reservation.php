<?php
include 'functions.php';
  try {
    $reservation = json_decode(file_get_contents('php://input'), true);
    $errors = [];
    $user_id = $reservation['user_id'] ?? null;
    
    if ($user_id == null) {
      $errors[] = "ログイン情報が不足しています。再度ログインしてください。";
    }

    $selectStylist = $reservation['selectStylist'] ?? "指定なし";
    $selectMenu = $reservation['selectMenu'] ?? null;
    
    if ($selectMenu == null) {
      $errors[] = "メニューが指定されていません。クーポンかメニューより一つは選択してください。";
    }

    $selectMenuPrice = $reservation['selectMenuPrice'] ?? null;
    $reservationStartTime = $reservation['reservationStartTime'] ?? null;
    $reservationEndTime = $reservation['reservationEndTime'] ?? null;
    
    if ($reservationStartTime == null && $reservationEndTime == null) {
      $errors[] = "予約時間が指定されていないか、予約時間を取得できませんでした。カレンダーをリロードして再度指定しなおしてください。";
    }

    if (!empty($errors)) {
      header('Content-Type: application/json', true, 400);
      echo json_encode(['errors' => $errors]);
      exit();
    }

    // 予約時間のチェック
    $startTime = (new DateTime($reservation['reservationStartTime']))->format('Y-m-d H:i:s');
    $endTime = (new DateTime($reservation['reservationEndTime']))->format('Y-m-d H:i:s');

    $response = [
      'selectStylist' => $selectStylist,
      'selectMenu' => $selectMenu,
      'selectMenuPrice' => $selectMenuPrice,
      'reservationStartTime' => $startTime,
      'reservationEndTime' => $endTime
    ];

    // DB登録処理
    $conn = getDbConnection();
    $checktable = "user_table";
    $addtable = "reservations";

    $message = newReservation($conn, $checktable, $addtable, $user_id, $response);
    
    if (!$message) {
      throw new Exception("予約登録に失敗しました。");
    }

    // 成功レスポンス
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => $message]);
    exit();
  } catch (Exception $e) {
    // エラーレスポンス
    header('Content-Type: application/json', true, 400);
    echo json_encode(['errors' => [$e->getMessage()]]);
    exit();
  }
?>
