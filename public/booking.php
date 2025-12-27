<?php
require_once "../config/db_conn.php";

// 取得所有電影
$movies = $db->query("SELECT * FROM movie")->fetchAll(PDO::FETCH_ASSOC);

// 選擇電影
$selected_movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : ($movies[0]['MovieID'] ?? 0);

// 取得電影場次
$stmt = $db->prepare("SELECT * FROM screening WHERE MovieID=? ORDER BY StartTime ASC");
$stmt->execute([$selected_movie_id]);
$screenings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 選擇場次
$selected_screening_id = isset($_GET['screening_id']) ? intval($_GET['screening_id']) : ($screenings[0]['ScreeningID'] ?? 0);

// 取得選中場次資訊
$stmt = $db->prepare("SELECT * FROM screening WHERE ScreeningID=?");
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
    <title>訂票頁面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .seat { display:inline-block; width:40px; height:40px; margin:3px; text-align:center; line-height:40px; border:1px solid #333; cursor:pointer; border-radius:5px;}
        .seat.selected{background:#007bff;color:#fff;}
        .seat.taken{background:#ccc; cursor:not-allowed;}
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
<div class="container">

<!-- 導覽列 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">Cinema</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="booking.php">訂票</a></li>
                <li class="nav-item"><a class="nav-link" href="mytickets.php">我的票券</a></li>
                <li class="nav-item"><a class="nav-link" href="../LoginView/login.php">登入</a></li>
            </ul>
        </div>
    </div>
</nav>

<h2>訂票頁面</h2>

<!-- 選電影 -->
<form method="GET" class="mb-3">
    <label>選擇電影:</label>
    <select name="movie_id" class="form-select" onchange="this.form.submit()">
        <?php foreach ($movies as $movie): ?>
            <option value="<?= $movie['MovieID'] ?>" <?= $movie['MovieID']==$selected_movie_id?'selected':'' ?>>
                <?= htmlspecialchars($movie['Title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<!-- 選場次 -->
<form method="GET" class="mb-3">
    <input type="hidden" name="movie_id" value="<?= $selected_movie_id ?>">
    <label>選擇場次:</label>
    <select name="screening_id" class="form-select" onchange="this.form.submit()">
        <?php foreach ($screenings as $s): ?>
            <option value="<?= $s['ScreeningID'] ?>" <?= $s['ScreeningID']==$selected_screening_id?'selected':'' ?>>
                <?= $s['StartTime'] ?> - <?= $s['Hall'] ?> - <?= $s['Price'] ?>元 (剩餘 <?= $s['AvailableSeats'] ?>)
            </option>
        <?php endforeach; ?>
    </select>
</form>

<!-- 座位選擇 -->
<h4>選擇座位</h4>
<div id="seats" class="mb-3">
<?php
$rows = range('A','J');
$cols = range(1,10);
foreach ($rows as $r){
    foreach($cols as $c){
        $seat = $r . str_pad($c,2,'0',STR_PAD_LEFT);
        $class = in_array($seat,$taken_seats)?'seat taken':'seat';
        echo "<div class='$class' data-seat='$seat'>$seat</div>";
    }
    echo "<br>";
}
?>
</div>

<!-- 訂票表單 -->
<form action="booking_save.php" method="POST">
    <div class="mb-3">
        <label>座位號碼</label>
        <input type="text" id="seatInput" name="seat" class="form-control" required readonly>
    </div>
    <input type="hidden" name="screening_id" value="<?= $screening['ScreeningID'] ?>">
    <button type="submit" class="btn btn-primary">提交訂票</button>
</form>

</div>

<script>
$(document).ready(function(){
    $(".seat").not(".taken").click(function(){
        $(".seat").removeClass("selected");
        $(this).addClass("selected");
        $("#seatInput").val($(this).data("seat"));
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
