<?php
require_once '../config/db_conn.php';

// 檢查是否為 POST 請求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

// 獲取並驗證輸入
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// 檢查是否有空值
if (empty($username) || empty($password) || empty($confirm_password)) {
    header('Location: register.php?error=empty');
    exit();
}

// 檢查密碼是否一致
if ($password !== $confirm_password) {
    header('Location: register.php?error=password_mismatch');
    exit();
}

// 檢查密碼長度
if (strlen($password) < 6) {
    header('Location: register.php?error=password_short');
    exit();
}

try {
    // 檢查使用者名稱是否已存在
    $checkStmt = $db->prepare("SELECT UserName FROM users WHERE UserName = :username");
    $checkStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $checkStmt->execute();
    
    if ($checkStmt->fetch()) {
        header('Location: register.php?error=username_exists');
        exit();
    }
    
    // 加密密碼
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // 插入新使用者
    $insertStmt = $db->prepare("INSERT INTO users (UserName, password_hash, IsAdmin) VALUES (:username, :password_hash, 0)");
    $insertStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $insertStmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
    
    if ($insertStmt->execute()) {
        // 註冊成功
        header('Location: login.php?success=registered');
        exit();
    } else {
        // 註冊失敗
        header('Location: register.php?error=registration_failed');
        exit();
    }
    
} catch (PDOException $e) {
    error_log("註冊錯誤: " . $e->getMessage());
    header('Location: register.php?error=system');
    exit();
}
?>
