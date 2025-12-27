<?php
session_start();
require_once "config/db_conn.php";

// 获取 flash 消息
$flash_error = $_SESSION['flash_error'] ?? '';
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_register_error = $_SESSION['flash_register_error'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success'], $_SESSION['flash_register_error']);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>電影列表 - 電影院管理系統</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <?php include 'LoginView/navbar.php'; ?>

    <div class="container">

        <?php if (!empty($flash_success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($flash_success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

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

    <?php include 'LoginView/login_modal.php'; ?>
    <?php include 'LoginView/register_modal.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>