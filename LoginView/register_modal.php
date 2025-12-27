<!-- 註冊模態框 -->
<?php
// 自動檢測當前頁面所在目錄，設置正確的相對路徑
$current_dir_reg = basename(dirname($_SERVER['SCRIPT_FILENAME']));
$in_public_reg = ($current_dir_reg === 'public');
$path_prefix_reg = $in_public_reg ? '../' : '';
?>
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">🎬 會員註冊</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($flash_register_error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($flash_register_error) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= $path_prefix_reg ?>LoginView/register_process.php" method="POST" id="registerForm">
                    <div class="mb-3">
                        <label for="register_username" class="form-label">使用者名稱</label>
                        <input type="text" class="form-control" id="register_username" name="username" required placeholder="請輸入使用者名稱">
                    </div>

                    <div class="mb-3">
                        <label for="register_password" class="form-label">密碼</label>
                        <input type="password" class="form-control" id="register_password" name="password" required placeholder="至少6個字元" minlength="6">
                        <div class="form-text">密碼長度至少6個字元</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">確認密碼</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="請再次輸入密碼">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">註冊</button>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-0">已有帳號？ <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">返回登入</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    const password = document.getElementById('register_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('兩次密碼輸入不一致！');
        return false;
    }
});
</script>

<?php if (!empty($flash_register_error)): ?>
<script>
    // 如果有註冊錯誤消息，自動打開模態框
    document.addEventListener('DOMContentLoaded', function() {
        var registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
        registerModal.show();
    });
</script>
<?php endif; ?>
