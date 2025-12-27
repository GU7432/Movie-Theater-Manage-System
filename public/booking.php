<?php
session_start();
require_once "../config/db_conn.php";

// 获取 flash 消息
$flash_error = $_SESSION['flash_error'] ?? '';
$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

// 取得所有電影
$movies = $db->query("SELECT * FROM movie")->fetchAll(PDO::FETCH_ASSOC);

// 檢查是否從 screening_list 直接進入（有 screening_id 參數）
$from_screening_list = isset($_GET['screening_id']);

// 選擇場次
$selected_screening_id = isset($_GET['screening_id']) ? intval($_GET['screening_id']) : 0;

if ($selected_screening_id) {
    // 如果有 screening_id，從場次取得電影ID
    $stmt = $db->prepare("SELECT MovieID FROM screening WHERE ScreeningID=?");
    $stmt->execute([$selected_screening_id]);
    $selected_movie_id = $stmt->fetchColumn() ?: 0;
} else {
    // 否則從 movie_id 或第一部電影開始
    $selected_movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : ($movies[0]['MovieID'] ?? 0);
}

// 取得電影場次
$stmt = $db->prepare("SELECT * FROM screening WHERE MovieID=? ORDER BY StartTime ASC");
$stmt->execute([$selected_movie_id]);
$screenings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 如果沒有指定場次，選第一個
if (!$selected_screening_id && !empty($screenings)) {
    $selected_screening_id = $screenings[0]['ScreeningID'];
}

// 取得選中場次資訊
$stmt = $db->prepare("SELECT s.*, m.Title as MovieTitle FROM screening s JOIN movie m ON s.MovieID = m.MovieID WHERE s.ScreeningID=?");
$stmt->execute([$selected_screening_id]);
$screening = $stmt->fetch(PDO::FETCH_ASSOC);

// 取得已訂座位
$stmt = $db->prepare("SELECT SeatNumber FROM ticket WHERE ScreeningID=?");
$stmt->execute([$selected_screening_id]);
$taken_seats = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>訂票頁面 - 電影院管理系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .seat { 
            display:inline-block; 
            width:40px; 
            height:40px; 
            margin:3px; 
            text-align:center; 
            line-height:40px; 
            border:2px solid #dee2e6; 
            cursor:pointer; 
            border-radius:5px;
            background:#fff;
            transition: all 0.2s;
        }
        .seat:hover:not(.taken) {
            border-color:#007bff;
            transform: scale(1.1);
        }
        .seat.selected{
            background:#007bff;
            color:#fff;
            border-color:#007bff;
        }
        .seat.taken{
            background:#6c757d;
            color:#fff;
            cursor:not-allowed;
            opacity: 0.6;
        }
        .screen {
            background: linear-gradient(to bottom, #333, #666);
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 50% 50% 0 0;
            margin-bottom: 30px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">

<?php include '../LoginView/navbar.php'; ?>

<div class="container">
    <div class="mb-4">
        <a href="../index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> 返回電影列表
        </a>
    </div>

    <h2 class="mb-4">
        <i class="bi bi-ticket-perforated"></i> 訂票頁面
    </h2>

<?php if ($from_screening_list && $screening): ?>
    <!-- 從場次列表進入，顯示當前場次資訊 -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="bi bi-film"></i> 電影資訊</h5>
                    <p class="mb-1"><strong>電影名稱：</strong><?= htmlspecialchars($screening['MovieTitle']) ?></p>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="bi bi-calendar-event"></i> 場次資訊</h5>
                    <p class="mb-1"><strong>時間：</strong><?= $screening['StartTime'] ?></p>
                    <p class="mb-1"><strong>廳別：</strong><?= htmlspecialchars($screening['Hall']) ?></p>
                    <p class="mb-1"><strong>票價：</strong>$<?= $screening['Price'] ?></p>
                    <p class="mb-0"><strong>剩餘座位：</strong><span class="badge bg-success"><?= $screening['AvailableSeats'] ?></span></p>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- 一般進入，顯示選擇表單 -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-film"></i> 選擇電影</h5>
                    <form method="GET">
                        <select name="movie_id" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($movies as $movie): ?>
                                <option value="<?= $movie['MovieID'] ?>" <?= $movie['MovieID']==$selected_movie_id?'selected':'' ?>>
                                    <?= htmlspecialchars($movie['Title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-calendar-event"></i> 選擇場次</h5>
                    <?php if (empty($screenings)): ?>
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> 該電影目前沒有場次
                        </div>
                    <?php else: ?>
                    <form method="GET">
                        <input type="hidden" name="movie_id" value="<?= $selected_movie_id ?>">
                        <select name="screening_id" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($screenings as $s): ?>
                                <option value="<?= $s['ScreeningID'] ?>" <?= $s['ScreeningID']==$selected_screening_id?'selected':'' ?>>
                                    <?= $s['StartTime'] ?> - <?= $s['Hall'] ?> - $<?= $s['Price'] ?> (剩餘 <?= $s['AvailableSeats'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($screenings) && $screening): ?>
<!-- 座位選擇 -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-grid-3x3"></i> 選擇座位</h5>
        <div class="screen mb-3">
            <i class="bi bi-display"></i> 螢幕
        </div>
        <div id="seats" class="text-center">
        <?php
        $rows = range('A','J');
        $cols = range(1,10);
        foreach ($rows as $r){
            echo "<div class='mb-2'>";
            echo "<span class='me-2'><strong>$r</strong></span>";
            foreach($cols as $c){
                $seat = $r . str_pad($c,2,'0',STR_PAD_LEFT);
                $class = in_array($seat,$taken_seats)?'seat taken':'seat';
                echo "<div class='$class' data-seat='$seat'>$seat</div>";
            }
            echo "</div>";
        }
        ?>
        </div>
        <div class="mt-3 d-flex justify-content-center gap-4">
            <div><span class="seat seat-legend" style="pointer-events:none;"></span> 可選</div>
            <div><span class="seat selected seat-legend" style="pointer-events:none;"></span> 已選</div>
            <div><span class="seat taken seat-legend" style="pointer-events:none;"></span> 已售</div>
        </div>
    </div>
</div>

<!-- 訂票表單 -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-check-circle"></i> 確認訂票</h5>
        <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i> 請先登入才能訂票
            </div>
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="bi bi-box-arrow-in-right"></i> 登入
            </button>
        <?php else: ?>
            <form action="booking_save.php" method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-pin-map"></i> 座位號碼</label>
                    <input type="text" id="seatInput" name="seat" class="form-control" required readonly placeholder="請點擊上方座位選擇">
                </div>
                <input type="hidden" name="screening_id" value="<?= $screening['ScreeningID'] ?>">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-cart-check"></i> 確認訂票
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

</div>

<?php include '../LoginView/login_modal.php'; ?>

<script>
$(document).ready(function(){
    // 只选择 seats 区域内的座位，排除已被订和标识图
    $("#seats .seat").not(".taken").click(function(){
        // 只清除 seats 区域内的 selected 状态
        $("#seats .seat").removeClass("selected");
        $(this).addClass("selected");
        $("#seatInput").val($(this).data("seat"));
    });
});
</script>
<?php include '../LoginView/login_modal.php'; ?>
<?php include '../LoginView/register_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
