<?php
// 此檔案用於保護需要登入才能訪問的頁面
// 在其他頁面開頭加入: require_once '../LoginView/check_auth.php';

session_start();

// 檢查是否已登入
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../LoginView/login.php');
    exit();
}

// 可選：檢查 session 是否過期（例如：30分鐘無活動）
$timeout_duration = 1800; // 30 分鐘（秒）

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session 過期
    session_unset();
    session_destroy();
    header('Location: ../LoginView/login.php?error=timeout');
    exit();
}

// 更新最後活動時間
$_SESSION['last_activity'] = time();

// 設置一些有用的變數供頁面使用
$current_user = $_SESSION['username'];
$is_admin = $_SESSION['is_admin'] ?? false;
?>
