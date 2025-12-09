<?php
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
    <title><?= htmlspecialchars($movie['Title'])?>場次列表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
<div class="container">
    <h2 class="header-title">
        <?= htmlspecialchars($movie['Title'])?> - 場次列表
    </h2>

    <a href="movie_list.php" class="btn btn-secondary mb-3">← 返回電影列表</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>場次ID</th>
                <th>開始時間</th>
                <th>場次廳別</th>
                <th>票價</th>
                <th>剩餘座位</th>
                <th>訂票</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($screenings as $row) { ?>
            <tr>
                <td><?= $row['ScreeningID'] ?></td>
                <td><?= $row['StartTime'] ?></td>
                <td><?= htmlspecialchars($row['Hall']) ?></td>
                <td>$<?= $row['Price'] ?></td>
                <td><?= $row['AvailableSeats'] ?></td>

                <td>
                    <a class="btn btn-primary"
                       href="booking.php?screening_id=<?= $row['ScreeningID'] ?>">
                        訂票
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

</body>
</html>

