<?php
session_start();
require_once "../config/db_conn.php";

// 檢查是否為 POST 請求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$screening_id = intval($_POST['screening_id']);
$customer = $_SESSION['username'];
$seat = trim($_POST['seat']);

if(!$customer || !$seat){
    die("顧客姓名或座位號碼不得為空");
}

// 1. 檢查座位是否已被訂過
$stmt = $db->prepare("SELECT COUNT(*) FROM ticket WHERE ScreeningID=? AND SeatNumber=?");
$stmt->execute([$screening_id,$seat]);
if($stmt->fetchColumn() > 0){
    die("該座位已被訂過");
}

// 2. 檢查剩餘座位是否足夠
$stmt = $db->prepare("SELECT AvailableSeats FROM screening WHERE ScreeningID=?");
$stmt->execute([$screening_id]);
$available = $stmt->fetchColumn();
if($available <= 0){
    die("本場次座位已滿");
}

// 3. 插入訂票
$stmt = $db->prepare("INSERT INTO ticket(ScreeningID, UserName, SeatNumber, PurchaseTime) VALUES(?,?,?,NOW())");
$stmt->execute([$screening_id, $customer, $seat]);

// 4. 扣剩餘座位
$stmt = $db->prepare("UPDATE screening SET AvailableSeats=AvailableSeats-1 WHERE ScreeningID=?");
$stmt->execute([$screening_id]);

echo "訂票成功！<br>";
echo "<a href='booking.php?movie_id=".$screening_id."'>回訂票頁面</a>";
?>
