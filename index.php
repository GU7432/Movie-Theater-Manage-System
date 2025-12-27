<?php
require_once "config/db_conn.php";

// 查詢所有電影
$sql = "SELECT * FROM movie ORDER BY MovieID ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>電影列表</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">

    <div class="container">

        <!-- 導航按鈕 -->
        <div class="mb-4 d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">Home</a>
            <a href="LoginView/login_process.php" class="btn btn-warning">登入</a>
        </div>

        <h2 class="mb-4">現正上映電影</h2>

        <div class="row">

            <?php foreach ($movies as $movie): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">

                        <?php if (!empty($movie['img'])): ?>
                            <img src="<?= htmlspecialchars($movie['img']) ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($movie['Title']) ?>">
                        <?php endif; ?>

                        



                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <?= htmlspecialchars($movie['Title']) ?>
                            </h5>

                            <p class="card-text">
                                類型：<?= htmlspecialchars($movie['Genre']) ?><br>
                                片長：<?= (int) $movie['Duration'] ?> 分鐘
                            </p>

                            <div class="mt-auto">
                                <a href="public/screening_list.php?movie_id=<?= $movie['MovieID'] ?>"
                                    class="btn btn-primary w-100">
                                    查看場次
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    </div>

</body>

</html>