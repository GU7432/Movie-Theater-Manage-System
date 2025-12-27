<?php
require_once "../config/db_conn.php";

// 查詢所有電影
$sql = "SELECT * FROM movie ORDER BY MovieID ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$movies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>電影列表</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="p-4">

<div class="container">

    <h2 class="mb-4">現正上映電影</h2>

    <div class="row">

        <?php foreach ($movies as $movie) { ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <?= htmlspecialchars($movie['Title']) ?>
                        </h5>

                        <p class="card-text">
                            類型：<?= htmlspecialchars($movie['Genre']) ?><br>
                            片長：<?= $movie['Duration'] ?> 分鐘
                        </p>

                        <div class="mt-auto">
                            <a href="screening_list.php?movie_id=<?= $movie['MovieID'] ?>"
                               class="btn btn-primary w-100">
                                查看場次
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        <?php } ?>

    </div>

</div>

</body>
</html>
