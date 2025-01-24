<?php
require_once 'vendor/autoload.php';
$csrfToken = getToken();
header('Content-Type: application/json; charset=utf-8');

// Google Clientの初期設定
$client = new Google\Client();
$client->setAuthConfig('/var/www/html/credentials.json');
$client->setScopes(Google\Service\Calendar::CALENDAR_EVENTS);
$client->setRedirectUri('http://localhost:8080');

// 保存済みトークンを確認
$tokenPath = '/var/www/html/token.json';

if (file_exists($tokenPath)) {
    // 保存済みトークンを読み込む
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);

    // トークンが期限切れの場合はリフレッシュ
    if ($client->isAccessTokenExpired()) {
        $refreshToken = $client->getRefreshToken();
        if ($refreshToken) {
            try {
                $newAccessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                file_put_contents($tokenPath, json_encode($newAccessToken));
            } catch (Exception $e) {
                error_log('リフレッシュトークンの取得に失敗しました: ' . $e->getMessage());
                http_response_code(401);
                echo json_encode(['error' => '認証が必要です。リフレッシュに失敗しました。']);
                exit;
            }
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'リフレッシュトークンが存在しません。']);
            exit;
        }
    }
} else {
    // 認証用URLを生成
    $authUrl = $client->createAuthUrl();
    echo json_encode([
        'error' => '初回認証が必要です。以下のURLで認証してください。',
        'authUrl' => $authUrl
    ]);
    exit; // 初回認証ではここで終了
}

// クライアントを返す（この後API呼び出しを続ける）
$service = new Google\Service\Calendar($client);

// POSTリクエストデータを取得
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'リクエストデータが無効です']);
    exit;
}

// POSTデータを処理
if (isset($data['title'])) {
    $title = $data['title'];
    $start = $data['reservationStartTime'];
    $end = $data['reservationEndTime'];
    $description = $data['description'];

    try {
        // Google Calendar APIに登録処理を実行
        $event = new Google\Service\Calendar\Event([
            'summary' => $title,
            'start' => ['dateTime' => $start, 'timeZone' => 'Asia/Tokyo'],
            'end' => ['dateTime' => $end, 'timeZone' => 'Asia/Tokyo'],
            'description' => $description
        ]);

        $calendarId = '4bf5d6988266f2863cad011f45143ba28089a7f5b991b7f35f0da9c89721b0dc@group.calendar.google.com';
        $createdEvent = $service->events->insert($calendarId, $event);

        // イベントIDを返す
        echo json_encode(['success' => true, 'eventId' => $createdEvent->getId()]);
    } catch (Exception $e) {
        error_log('GoogleカレンダーAPIエラー: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'GoogleカレンダーAPIエラー: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'データが不足しています']);
}
