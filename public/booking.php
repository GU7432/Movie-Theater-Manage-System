<?php
    if(!isset($_GET['screening_id'])) {
        die("screening_id not provided.");
    }

    $screening_id = intval($_GET['screening_id']);

    require_once "../config/db_conn.php";

    $sql_screening = "select * from screening where ScreeningID = ?";
    $stmt = $db->prepare($sql_screening);
    $stmt->execute([$screening_id]);
    $screening = $stmt->fetch();

    if (!$screening) {
        die("找不到該場次");
    }
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>訂票頁面</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="p-4">

<div class="container">

    <h2 class="header-title">訂票資訊</h2>

    <form action="booking_save.php" method="POST" class="card p-3">

        <div class="mb-3">
            <label>座位號碼</label>
            <input type="text" name="seat" class="form-control" required>
        </div>

        <input type="hidden" name="screening_id" value="<?= $screening['ScreeningID'] ?>">

        <button type="submit" class="btn btn-primary">提交訂票</button>
    </form>

</div>

</body>
</html>
