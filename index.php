<?php
// index.php - 主畫面
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>Cinema 主畫面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            padding: 50px;
        }
        h1 {
            color: #333;
        }
        .btn {
            display: inline-block;
            margin: 20px;
            padding: 15px 30px;
            font-size: 18px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>歡迎來到 Cinema 電影院網站</h1>

    <a href="public/movie_list.php" class="btn">電影列表 / 訂票</a>
    <a href="public/booking.php" class="btn">訂票頁面</a>
    <a href="LoginView/login.html" class="btn">管理者登入</a>

</body>
</html>
