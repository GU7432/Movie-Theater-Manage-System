<?php
session_start();
require_once "../config/db_conn.php";

// 假設使用者登入後，會把 UserName 存在 session
if (!isset($_SESSION['logged_in'])) {
    die("請先登入才能查看訂票紀錄。<br><a href='../index.php'>回首頁</a>");
}

$username = $_SESSION['username'];

// 查詢使用者訂票紀錄
$sql = "SELECT t.TicketID, t.SeatNumber, t.PurchaseTime, s.StartTime, s.Hall, s.Price, m.Title
        FROM ticket t
        JOIN screening s ON t.ScreeningID = s.ScreeningID
        JOIN movie m ON s.MovieID = m.MovieID
        WHERE t.UserName = ?
        ORDER BY t.PurchaseTime DESC";

$stmt = $db->prepare($sql);
$stmt->execute([$username]);
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>我的訂票紀錄</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
<div class="container">

    <!-- 導航按鈕 -->
    <div class="mb-4">
        <a href="../index.php" class="btn btn-secondary">Home</a>
    </div>

    <h2 class="mb-4">我的訂票紀錄</h2>

    <?php if (count($tickets) === 0): ?>
        <p>你目前沒有訂票紀錄。</p>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>電影名稱</th>
                    <th>場次時間</th>
                    <th>廳別</th>
                    <th>座位號碼</th>
                    <th>票價</th>
                    <th>訂票時間</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['Title']) ?></td>
                        <td><?= $t['StartTime'] ?></td>
                        <td><?= htmlspecialchars($t['Hall']) ?></td>
                        <td><?= htmlspecialchars($t['SeatNumber']) ?></td>
                        <td><?= $t['Price'] ?> 元</td>
                        <td><?= $t['PurchaseTime'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
