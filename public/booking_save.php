<?php
session_start();
require_once "../config/db_conn.php";

// 只允許 POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$screening_id = intval($_POST['screening_id'] ?? 0);
$customer = $_SESSION['username'] ?? null;
$seat = trim($_POST['seat'] ?? '');

if (!$customer || !$seat || $screening_id <= 0) {
    die("資料不完整");
}

try {
    // 開始交易
    $db->beginTransaction();

    // 1. 檢查座位是否已被訂
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM ticket
         WHERE ScreeningID = ? AND SeatNumber = ?"
    );
    $stmt->execute([$screening_id, $seat]);

    if ($stmt->fetchColumn() > 0) {
        throw new Exception("該座位已被訂過");
    }

    // 2. 插入訂票（Trigger 會處理剩餘座位）
    $stmt = $db->prepare(
        "INSERT INTO ticket (ScreeningID, UserName, SeatNumber, PurchaseTime)
         VALUES (?, ?, ?, NOW())"
    );
    $stmt->execute([$screening_id, $customer, $seat]);

    // 成功
    $db->commit();

    $_SESSION['flash_success'] = '訂票成功！座位：' . htmlspecialchars($seat);
    header('Location: ../index.php');
    exit();

} catch (Exception $e) {
    $db->rollBack();
    die("訂票失敗：" . $e->getMessage());
}
?>
