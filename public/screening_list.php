<?php
    session_start();
    
    if(!isset($_GET['movie_id'])) {
        die("movie_id not provided.");
    }

    $movie_id = intval($_GET['movie_id']);

    require_once "../config/db_conn.php";

    $sql_movie = "select * from movie where MovieID = ?";
    $stmt = $db->prepare($sql_movie);
    $stmt->execute([$movie_id]);
    $movie = $stmt->fetch();
   
    if (!$movie) {
    die("找不到該電影");
    }

    // 查詢該電影所有場次
    $sql_screenings = "SELECT * FROM screening WHERE MovieID = ? ORDER BY StartTime ASC";
    $stmt2 = $db->prepare($sql_screenings);
    $stmt2->execute([$movie_id]);
    $screenings = $stmt2->fetchAll();  // 多筆

?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movie['Title'])?> 場次列表 - 電影院管理系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<?php include '../LoginView/navbar.php'; ?>

<div class="container">
    <div class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> 返回電影列表
        </a>
    </div>
    
    <h2 class="mb-4">
        <i class="bi bi-film"></i> <?= htmlspecialchars($movie['Title'])?> - 場次列表
    </h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th><i class="bi bi-hash"></i> 場次ID</th>
                        <th><i class="bi bi-clock"></i> 開始時間</th>
                        <th><i class="bi bi-door-open"></i> 場次廳別</th>
                        <th><i class="bi bi-cash"></i> 票價</th>
                        <th><i class="bi bi-people"></i> 剩餘座位</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($screenings as $row) { ?>
                    <tr>
                        <td><?= $row['ScreeningID'] ?></td>
                        <td><?= $row['StartTime'] ?></td>
                        <td><?= htmlspecialchars($row['Hall']) ?></td>
                        <td><strong>$<?= $row['Price'] ?></strong></td>
                        <td>
                            <?php if ($row['AvailableSeats'] > 20): ?>
                                <span class="badge bg-success"><?= $row['AvailableSeats'] ?></span>
                            <?php elseif ($row['AvailableSeats'] > 0): ?>
                                <span class="badge bg-warning text-dark"><?= $row['AvailableSeats'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">售罄</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['AvailableSeats'] > 0): ?>
                                <a class="btn btn-primary btn-sm"
                                   href="booking.php?screening_id=<?= $row['ScreeningID'] ?>">
                                    <i class="bi bi-ticket-perforated"></i> 訂票
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="bi bi-x-circle"></i> 已滿座
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php include '../LoginView/login_modal.php'; ?>
<?php include 'LoginView/register_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

