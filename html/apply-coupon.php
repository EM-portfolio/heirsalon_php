<?php
// *************************************** それぞれクリックして確認画面に ******************************************************
  $requestData = json_decode(file_get_contents('php://input'), true);

  $couponId = $requestData['couponId'] ?? null;
  $coponMenu = $requestData['coponMenu'] ?? null;
  $coponTime = $requestData['coponTime'] ?? null;
  $useDiscount = $requestData['useDiscount'] ?? null;
  $stylistId = $requestData['stylistId'] ?? null;
  $menuIds = $requestData['menuIds'] ?? [];
  $menuTimes = $requestData['menuTimes'] ?? [];
  $menuPrice = $requestData['menuPrice'] ?? [];
  $startTime = $requestData['startTime'] ?? null;
  $endTime = $requestData['endTime'] ?? null;

  $response = [
      'couponId' => $couponId,
      'coponMenu' => $coponMenu,
      'coponTime' => $coponTime,
      'useDiscount' => $useDiscount,
      'stylistId' => $stylistId,
      'menuIds' => $menuIds,
      'menuTimes' => $menuTimes,
      'menuPrice' => $menuPrice,
      'startTime' => $startTime,
      'endTime' => $endTime,
  ];

  header('Content-Type: application/json');
  // json-select.jsに返すデータ
  echo json_encode($response);