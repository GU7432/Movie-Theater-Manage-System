<?php
// 此檔案用於保護需要管理員權限才能訪問的頁面
// 在管理頁面開頭加入: require_once '../LoginView/check_admin.php';

session_start();

// 檢查是否已登入
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../LoginView/login.html');
    exit();
}

// 檢查是否為管理員
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../public/movie_list.php?error=no_permission');
    exit();
}

// 更新最後活動時間
$_SESSION['last_activity'] = time();

// 設置變數
$current_user = $_SESSION['username'];
$is_admin = true;
?>
