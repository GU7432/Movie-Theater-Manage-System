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
            
            <?php if(isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php
                        if($_GET['error'] == 'invalid') {
                            echo '❌ 帳號或密碼錯誤！';
                        } elseif($_GET['error'] == 'empty') {
                            echo '❌ 請填寫所有欄位！';
                        }
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                <div class="success-message">
                    ✅ 註冊成功！請登入
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['logout'])): ?>
                <div class="success-message">
                    ✅ 已成功登出
                </div>
            <?php endif; ?>
            
            <div class="register-link">
                <p>還沒有帳號？ <a href="register.html">立即註冊</a></p>
            </div>
        </div>
    </div>
</body>
</html>
