<!-- 統一導航欄 -->
<?php
// 自動檢測當前頁面所在目錄，設置正確的相對路徑
$current_dir = basename(dirname($_SERVER['SCRIPT_FILENAME']));
$in_public = ($current_dir === 'public');
$path_prefix = $in_public ? '../' : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $path_prefix ?>index.php">
            🎬 電影院管理系統
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $path_prefix ?>public/admin_dashboard.php">
                            <i class="bi bi-speedometer2"></i> 管理後台
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $path_prefix ?>index.php">
                        <i class="bi bi-house"></i> 電影列表
                    </a>
                </li>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $path_prefix ?>public/mytickets.php">
                            <i class="bi bi-ticket-perforated"></i> 我的票券
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="bi bi-person-circle"></i> 歡迎，<?= htmlspecialchars($_SESSION['username']) ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm" href="<?= $path_prefix ?>LoginView/logout.php">
                            <i class="bi bi-box-arrow-right"></i> 登出
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-box-arrow-in-right"></i> 登入
                        </button>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
