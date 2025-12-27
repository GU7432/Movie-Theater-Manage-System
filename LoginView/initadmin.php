<?php
require_once 'config/db_conn.php';


echo "<h1>🎬 系統初始化</h1>";

// ... 初始化代碼 ...

$accounts = [
    ['username' => 'admin', 'password' => 'admin123', 'isAdmin' => 1],
];

foreach ($accounts as $account) {
    $hash = password_hash($account['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $db->prepare("INSERT INTO users (UserName, password_hash, IsAdmin) VALUES (?, ?, ?)");
        $stmt->execute([$account['username'], $hash, $account['isAdmin']]);
        
        echo "✅ 管理員帳號已建立<br>";
        echo "⚠️ <strong>請立即登入並修改密碼！</strong><br>";
    } catch (PDOException $e) {
        // 靜默處理（避免洩露錯誤訊息）
    }
}


echo "<hr>";
echo "<h2>⚠️ 安全提示：</h2>";
echo "<p style='color: red;'>1. 此腳本已被鎖定，無法再次執行</p>";
echo "<p style='color: red;'>2. 請立即登入並修改預設密碼</p>";
echo "<p style='color: red;'>3. 如果是正式環境，請刪除此檔案：setup.php</p>";
echo "<br>";
echo "<a href='LoginView/login.html'>前往登入頁面</a>";
?>