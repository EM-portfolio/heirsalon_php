<?php
include 'functions.php';
$csrfToken = getToken();

$conn = getDbConnection();


if (isset($_GET['data-cansel-btn'])) { 
  $reservation_id = $_GET['data-cansel-btn'];
  
  $sql = "
    UPDATE reservations 
    SET 
    delflg = 1,
    status = 2
    WHERE reservation_id = ?
  ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $reservation_id);
  $stmt->execute();
}
