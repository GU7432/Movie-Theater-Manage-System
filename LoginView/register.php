<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊 - 電影院管理系統</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>🎬 電影院管理系統</h1>
            <h2>會員註冊</h2>
            
            <form action="register_process.php" method="POST" onsubmit="return validateForm()">
                <div class="input-group">
                    <label for="username">使用者名稱</label>
                    <input type="text" id="username" name="username" required placeholder="請輸入使用者名稱">
                </div>
                
                <div class="input-group">
                    <label for="password">密碼</label>
                    <input type="password" id="password" name="password" required placeholder="至少6個字元" minlength="6">
                </div>
                
                <div class="input-group">
                    <label for="confirm_password">確認密碼</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="請再次輸入密碼">
                </div>
                
                <button type="submit" class="btn-login">註冊</button>
            </form>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php
                        if($_GET['error'] == 'password_mismatch') {
                            echo '❌ 兩次密碼輸入不一致！';
                        } elseif($_GET['error'] == 'username_exists') {
                            echo '❌ 使用者名稱已存在！';
                        } elseif($_GET['error'] == 'empty') {
                            echo '❌ 請填寫所有欄位！';
                        }
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="register-link">
                <p>已有帳號？ <a href="login.php">返回登入</a></p>
            </div>
        </div>
    </div>
    
    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                alert('兩次密碼輸入不一致！');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
