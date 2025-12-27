<?php
session_start();

/**
 * Flash message（一次性訊息）
 * login_process.php / register_process.php 只要設定：
 *   $_SESSION['flash_success'] = '...';
 *   $_SESSION['flash_error'] = '...';
 * 然後 redirect 到 login.php
 */

$flash_error = $_SESSION['flash_error'] ?? '';
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_info = $_SESSION['flash_info'] ?? '';

// 顯示一次就清掉（避免常駐）
unset($_SESSION['flash_error'], $_SESSION['flash_success'], $_SESSION['flash_info']);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入 - 電影院管理系統</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>🎬 電影院管理系統</h1>
            <h2>會員登入</h2>

            <!-- ✅ Flash messages -->
            <?php if (!empty($flash_error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($flash_error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($flash_success)): ?>
                <div class="success-message">
                    <?= htmlspecialchars($flash_success) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($flash_info)): ?>
                <div class="info-message">
                    <?= htmlspecialchars($flash_info) ?>
                </div>
            <?php endif; ?>

            <form action="login_process.php" method="POST">
                <div class="input-group">
                    <label for="username">使用者名稱</label>
                    <input type="text" id="username" name="username" required placeholder="請輸入使用者名稱">
                </div>

                <div class="input-group">
                    <label for="password">密碼</label>
                    <input type="password" id="password" name="password" required placeholder="請輸入密碼">
                </div>

                <button type="submit" class="btn-login">登入</button>
            </form>

            <div class="register-link">
                <p>還沒有帳號？ <a href="register.php">立即註冊</a></p>
            </div>
        </div>
    </div>
</body>
</html>
