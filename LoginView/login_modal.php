<!-- 登入模態框 -->
<?php
// 自動檢測當前頁面所在目錄，設置正確的相對路徑
$current_dir_modal = basename(dirname($_SERVER['SCRIPT_FILENAME']));
$in_public_modal = ($current_dir_modal === 'public');
$path_prefix_modal = $in_public_modal ? '../' : '';
?>
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">🎬 會員登入</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($flash_error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($flash_error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flash_success)): ?>
                    <div class="alert alert-success" role="alert">
                        <?= htmlspecialchars($flash_success) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= $path_prefix_modal ?>LoginView/login_process.php" method="POST">
                    <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">使用者名稱</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="請輸入使用者名稱">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">密碼</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="請輸入密碼">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">登入</button>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-0">還沒有帳號？ <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">立即註冊</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($flash_error) || !empty($flash_success)): ?>
<script>
    // 如果有錯誤或成功消息，自動打開模態框
    document.addEventListener('DOMContentLoaded', function() {
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    });
</script>
<?php endif; ?>
