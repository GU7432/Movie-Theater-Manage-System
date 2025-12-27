<?php
session_start();
require_once '../config/db_conn.php';

// 檢查是否為 POST 請求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit();
}

// 獲取並驗證輸入
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// 檢查是否有空值
if (empty($username) || empty($password)) {
    header('Location: login.html?error=empty');
    exit();
}

try {
    // 查詢使用者
    $stmt = $db->prepare("SELECT UserName, password_hash, IsAdmin FROM users WHERE UserName = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 驗證密碼
    if ($user && password_verify($password, $user['password_hash'])) {
        // 登入成功，設置 session
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $user['UserName'];
        $_SESSION['is_admin'] = (bool)$user['IsAdmin'];
        
        // 更新最後登入時間（如果需要的話）
        // $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE UserName = :username");
        // $updateStmt->bindParam(':username', $username);
        // $updateStmt->execute();
        
        // 根據使用者身份跳轉
        if ($user['IsAdmin']) {
            header('Location: ../public/admin_dashboard.php');
        } else {
            header('Location: ../public/movie_list.php');
        }
        exit();
    } else {
        // 登入失敗
        header('Location: login.html?error=invalid');
        exit();
    }
    
} catch (PDOException $e) {
    error_log("登入錯誤: " . $e->getMessage());
    header('Location: login.html?error=system');
    exit();
}
?>
