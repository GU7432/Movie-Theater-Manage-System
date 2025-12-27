<?php
// === 只允許在命令列執行 ===


require_once '../config/db_conn.php';

// 互動式設定管理員密碼
echo "請設定管理員密碼：";
$password = trim(fgets(STDIN));

if (strlen($password) < 6) {
    die("❌ 密碼長度至少要 6 個字元\n");
}

echo "請再次確認密碼：";
$confirm = trim(fgets(STDIN));

if ($password !== $confirm) {
    die("❌ 兩次密碼不一致\n");
}

// 建立管理員帳號
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $db->prepare("INSERT INTO users (UserName, password_hash, IsAdmin) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hash, 1]);
    
    echo "\n✅ 管理員帳號建立成功！\n";
    echo "帳號：admin\n";
    echo "密碼：（你剛才設定的）\n\n";
    echo "啟動伺服器：php -S localhost:8000\n";
    echo "登入網址：http://localhost:8000/LoginView/login.html\n";
    
} catch (PDOException $e) {
    echo "❌ 錯誤：" . $e->getMessage() . "\n";
}
?>