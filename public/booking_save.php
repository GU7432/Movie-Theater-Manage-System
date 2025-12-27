<?php
session_start();
require_once "../config/db_conn.php";

// 檢查是否登入
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../LoginView/login.html?error=login_required');
    exit();
}

// 1. 檢查必要參數
if (
    !isset($_POST['screening_id']) ||
    !isset($_POST['seat'])
) {
    die("資料不完整");
}

$screening_id = intval($_POST['screening_id']);
$customer = $_SESSION['username'];
$seat = trim($_POST['seat']);

// 2. 檢查場次是否存在 & 剩餘座位
$sql_check = "SELECT AvailableSeats FROM screening WHERE ScreeningID = ?";
$stmt = $db->prepare($sql_check);
$stmt->execute([$screening_id]);
$screening = $stmt->fetch();

if (!$screening) {
    die("找不到該場次");
}

if ($screening['AvailableSeats'] <= 0) {
    die("該場次已無剩餘座位");
}

// 3. 新增訂票（ticket）
$sql_insert = "
    INSERT INTO ticket (ScreeningID, UserName, SeatNumber, PurchaseTime)
    VALUES (?, ?, ?, NOW())
";
$stmt2 = $db->prepare($sql_insert);
$stmt2->execute([$screening_id, $customer, $seat]);

// 4. 更新剩餘座位
$sql_update = "
    UPDATE screening
    SET AvailableSeats = AvailableSeats - 1
    WHERE ScreeningID = ?
";
$stmt3 = $db->prepare($sql_update);
$stmt3->execute([$screening_id]);

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>訂票完成</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
<div class="container">

    <div class="alert alert-success">
        <h4 class="alert-heading">訂票成功！</h4>
        <p>顧客姓名：<?= htmlspecialchars($customer) ?></p>
        <p>座位號碼：<?= htmlspecialchars($seat) ?></p>
    </div>

    <a href="movie_list.php" class="btn btn-primary">回到電影列表</a>

</div>
</body>
</html>
