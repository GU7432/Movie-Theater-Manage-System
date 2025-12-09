<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>訂票頁面</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- 自己的 CSS（可選） -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="p-4">

<div class="container">

    <h2 class="header-title">訂票資訊</h2>

    <form action="booking_save.php" method="POST" class="card p-3">

        <div class="mb-3">
            <label>顧客姓名</label>
            <input type="text" name="customer" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>座位號碼</label>
            <input type="text" name="seat" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">送出訂票</button>
    </form>

</div>

</body>
</html>
