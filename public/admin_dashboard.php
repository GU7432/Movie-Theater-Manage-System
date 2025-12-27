<?php
require_once "../config/db_conn.php";
require_once "../LoginView/check_admin.php";

// 處理 AJAX 請求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    
    try {
        switch ($action) {
            // ========== 電影管理 ==========
            case 'add_movie':
                $title = trim($_POST['title']);
                $genre = trim($_POST['genre']);
                $duration = intval($_POST['duration']);
                $rating = trim($_POST['rating']);
                $description = trim($_POST['description']);
                
                $stmt = $db->prepare("INSERT INTO movie (Title, Genre, Duration, Rating, Description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $genre, $duration, $rating, $description]);
                echo json_encode(['success' => true, 'message' => '電影新增成功']);
                break;
                
            case 'edit_movie':
                $id = intval($_POST['movie_id']);
                $title = trim($_POST['title']);
                $genre = trim($_POST['genre']);
                $duration = intval($_POST['duration']);
                $rating = trim($_POST['rating']);
                $description = trim($_POST['description']);
                
                $stmt = $db->prepare("UPDATE movie SET Title=?, Genre=?, Duration=?, Rating=?, Description=? WHERE MovieID=?");
                $stmt->execute([$title, $genre, $duration, $rating, $description, $id]);
                echo json_encode(['success' => true, 'message' => '電影修改成功']);
                break;
                
            case 'delete_movie':
                $id = intval($_POST['movie_id']);
                // 先檢查是否有關聯的場次
                $stmt = $db->prepare("SELECT COUNT(*) FROM screening WHERE MovieID=?");
                $stmt->execute([$id]);
                if ($stmt->fetchColumn() > 0) {
                    echo json_encode(['success' => false, 'message' => '請先刪除該電影的所有場次']);
                    break;
                }
                $stmt = $db->prepare("DELETE FROM movie WHERE MovieID=?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'message' => '電影刪除成功']);
                break;
                
            case 'get_movie':
                $id = intval($_POST['movie_id']);
                $stmt = $db->prepare("SELECT * FROM movie WHERE MovieID=?");
                $stmt->execute([$id]);
                $movie = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $movie]);
                break;
                
            // ========== 場次管理 ==========
            case 'add_screening':
                $movie_id = intval($_POST['movie_id']);
                $hall = trim($_POST['hall']);
                $start_time = $_POST['start_time'];
                $price = floatval($_POST['price']);
                $seats = intval($_POST['available_seats']);
                
                $stmt = $db->prepare("INSERT INTO screening (MovieID, Hall, StartTime, Price, AvailableSeats) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$movie_id, $hall, $start_time, $price, $seats]);
                echo json_encode(['success' => true, 'message' => '場次新增成功']);
                break;
                
            case 'edit_screening':
                $id = intval($_POST['screening_id']);
                $movie_id = intval($_POST['movie_id']);
                $hall = trim($_POST['hall']);
                $start_time = $_POST['start_time'];
                $price = floatval($_POST['price']);
                $seats = intval($_POST['available_seats']);
                
                $stmt = $db->prepare("UPDATE screening SET MovieID=?, Hall=?, StartTime=?, Price=?, AvailableSeats=? WHERE ScreeningID=?");
                $stmt->execute([$movie_id, $hall, $start_time, $price, $seats, $id]);
                echo json_encode(['success' => true, 'message' => '場次修改成功']);
                break;
                
            case 'delete_screening':
                $id = intval($_POST['screening_id']);
                // 先檢查是否有訂票
                $stmt = $db->prepare("SELECT COUNT(*) FROM ticket WHERE ScreeningID=?");
                $stmt->execute([$id]);
                if ($stmt->fetchColumn() > 0) {
                    echo json_encode(['success' => false, 'message' => '該場次已有訂票，無法刪除']);
                    break;
                }
                $stmt = $db->prepare("DELETE FROM screening WHERE ScreeningID=?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'message' => '場次刪除成功']);
                break;
                
            case 'get_screening':
                $id = intval($_POST['screening_id']);
                $stmt = $db->prepare("SELECT * FROM screening WHERE ScreeningID=?");
                $stmt->execute([$id]);
                $screening = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $screening]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => '未知操作']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => '操作失敗: ' . $e->getMessage()]);
    }
    exit();
}

// 取得所有電影
$movies = $db->query("SELECT * FROM movie ORDER BY MovieID DESC")->fetchAll(PDO::FETCH_ASSOC);

// 取得所有場次
$screenings = $db->query("
    SELECT s.*, m.Title as MovieTitle 
    FROM screening s 
    JOIN movie m ON s.MovieID = m.MovieID 
    ORDER BY s.StartTime DESC
")->fetchAll(PDO::FETCH_ASSOC);

// 統計數據
$stats = [
    'total_movies' => $db->query("SELECT COUNT(*) FROM movie")->fetchColumn(),
    'total_screenings' => $db->query("SELECT COUNT(*) FROM screening")->fetchColumn(),
    'total_tickets' => $db->query("SELECT COUNT(*) FROM ticket")->fetchColumn(),
    'total_revenue' => $db->query("SELECT COALESCE(SUM(s.Price), 0) FROM ticket t JOIN screening s ON t.ScreeningID=s.ScreeningID")->fetchColumn()
];
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者儀表板</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .table-actions button {
            margin: 0 2px;
        }
    </style>
</head>
<body>

<!-- 導覽列 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">🎬 電影院管理系統</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link">歡迎, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../LoginView/logout.php">登出</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        
        <!-- 側邊欄 -->
        <nav class="col-md-2 d-md-block bg-light sidebar py-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#dashboard" data-bs-toggle="tab">
                        <i class="bi bi-speedometer2"></i> 儀表板
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#movies" data-bs-toggle="tab">
                        <i class="bi bi-film"></i> 電影管理
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#screenings" data-bs-toggle="tab">
                        <i class="bi bi-calendar-event"></i> 場次管理
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link text-secondary" href="../index.php">
                        <i class="bi bi-house"></i> 回首頁
                    </a>
                </li>
            </ul>
        </nav>

        <!-- 主要內容 -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <div class="tab-content">
                
                <!-- 儀表板 -->
                <div class="tab-pane fade show active" id="dashboard">
                    <h2 class="mb-4">儀表板總覽</h2>
                    
                    <!-- 統計卡片 -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card text-white bg-primary">
                                <div class="card-body">
                                    <h5><i class="bi bi-film"></i> 電影總數</h5>
                                    <h2><?= $stats['total_movies'] ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card text-white bg-success">
                                <div class="card-body">
                                    <h5><i class="bi bi-calendar-event"></i> 場次總數</h5>
                                    <h2><?= $stats['total_screenings'] ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card text-white bg-info">
                                <div class="card-body">
                                    <h5><i class="bi bi-ticket"></i> 訂票總數</h5>
                                    <h2><?= $stats['total_tickets'] ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card text-white bg-warning">
                                <div class="card-body">
                                    <h5><i class="bi bi-cash"></i> 總收入</h5>
                                    <h2>$<?= number_format($stats['total_revenue']) ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 使用左側選單管理電影和場次
                    </div>
                </div>
                
                <!-- 電影管理 -->
                <div class="tab-pane fade" id="movies">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>電影管理</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#movieModal" onclick="openAddMovieModal()">
                            <i class="bi bi-plus-lg"></i> 新增電影
                        </button>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>電影名稱</th>
                                        <th>類型</th>
                                        <th>片長</th>
                                        <th>分級</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($movies as $m): ?>
                                    <tr>
                                        <td><?= $m['MovieID'] ?></td>
                                        <td><?= htmlspecialchars($m['Title']) ?></td>
                                        <td><?= htmlspecialchars($m['Genre'] ?? '-') ?></td>
                                        <td><?= $m['Duration'] ?? '-' ?> 分鐘</td>
                                        <td><?= htmlspecialchars($m['Rating'] ?? '-') ?></td>
                                        <td class="table-actions">
                                            <button class="btn btn-sm btn-warning" onclick="editMovie(<?= $m['MovieID'] ?>)">
                                                <i class="bi bi-pencil"></i> 編輯
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteMovie(<?= $m['MovieID'] ?>, '<?= htmlspecialchars($m['Title'], ENT_QUOTES) ?>')">
                                                <i class="bi bi-trash"></i> 刪除
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- 場次管理 -->
                <div class="tab-pane fade" id="screenings">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>場次管理</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#screeningModal" onclick="openAddScreeningModal()">
                            <i class="bi bi-plus-lg"></i> 新增場次
                        </button>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>電影</th>
                                        <th>影廳</th>
                                        <th>放映時間</th>
                                        <th>票價</th>
                                        <th>剩餘座位</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($screenings as $s): ?>
                                    <tr>
                                        <td><?= $s['ScreeningID'] ?></td>
                                        <td><?= htmlspecialchars($s['MovieTitle']) ?></td>
                                        <td><?= htmlspecialchars($s['Hall']) ?></td>
                                        <td><?= $s['StartTime'] ?></td>
                                        <td>$<?= $s['Price'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $s['AvailableSeats'] > 20 ? 'success' : ($s['AvailableSeats'] > 0 ? 'warning' : 'danger') ?>">
                                                <?= $s['AvailableSeats'] ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <button class="btn btn-sm btn-warning" onclick="editScreening(<?= $s['ScreeningID'] ?>)">
                                                <i class="bi bi-pencil"></i> 編輯
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteScreening(<?= $s['ScreeningID'] ?>)">
                                                <i class="bi bi-trash"></i> 刪除
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </main>
    </div>
</div>

<!-- 電影 Modal -->
<div class="modal fade" id="movieModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="movieModalTitle">新增電影</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="movieForm">
                <div class="modal-body">
                    <input type="hidden" id="movie_id" name="movie_id">
                    <input type="hidden" id="movie_action" name="action" value="add_movie">
                    
                    <div class="mb-3">
                        <label class="form-label">電影名稱 *</label>
                        <input type="text" class="form-control" id="movie_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">類型</label>
                        <input type="text" class="form-control" id="movie_genre" name="genre" placeholder="動作、喜劇、科幻...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">片長 (分鐘)</label>
                        <input type="number" class="form-control" id="movie_duration" name="duration" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">分級</label>
                        <select class="form-select" id="movie_rating" name="rating">
                            <option value="">請選擇</option>
                            <option value="G">G - 普遍級</option>
                            <option value="PG">PG - 保護級</option>
                            <option value="PG-13">PG-13 - 輔導級</option>
                            <option value="R">R - 限制級</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">簡介</label>
                        <textarea class="form-control" id="movie_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">儲存</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 場次 Modal -->
<div class="modal fade" id="screeningModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="screeningModalTitle">新增場次</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="screeningForm">
                <div class="modal-body">
                    <input type="hidden" id="screening_id" name="screening_id">
                    <input type="hidden" id="screening_action" name="action" value="add_screening">
                    
                    <div class="mb-3">
                        <label class="form-label">電影 *</label>
                        <select class="form-select" id="screening_movie_id" name="movie_id" required>
                            <option value="">請選擇電影</option>
                            <?php foreach ($movies as $m): ?>
                            <option value="<?= $m['MovieID'] ?>"><?= htmlspecialchars($m['Title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">影廳 *</label>
                        <select class="form-select" id="screening_hall" name="hall" required>
                            <option value="">請選擇影廳</option>
                            <option value="A廳">A廳</option>
                            <option value="B廳">B廳</option>
                            <option value="C廳">C廳</option>
                            <option value="IMAX廳">IMAX廳</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">放映時間 *</label>
                        <input type="datetime-local" class="form-control" id="screening_start_time" name="start_time" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">票價 *</label>
                        <input type="number" class="form-control" id="screening_price" name="price" min="0" step="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">可用座位數 *</label>
                        <input type="number" class="form-control" id="screening_seats" name="available_seats" min="0" value="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">儲存</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ========== 電影管理 ==========
function openAddMovieModal() {
    document.getElementById('movieModalTitle').textContent = '新增電影';
    document.getElementById('movie_action').value = 'add_movie';
    document.getElementById('movieForm').reset();
    document.getElementById('movie_id').value = '';
}

function editMovie(id) {
    fetch('admin_dashboard.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=get_movie&movie_id=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const m = data.data;
            document.getElementById('movieModalTitle').textContent = '編輯電影';
            document.getElementById('movie_action').value = 'edit_movie';
            document.getElementById('movie_id').value = m.MovieID;
            document.getElementById('movie_title').value = m.Title;
            document.getElementById('movie_genre').value = m.Genre || '';
            document.getElementById('movie_duration').value = m.Duration || '';
            document.getElementById('movie_rating').value = m.Rating || '';
            document.getElementById('movie_description').value = m.Description || '';
            new bootstrap.Modal(document.getElementById('movieModal')).show();
        }
    });
}

function deleteMovie(id, title) {
    if (confirm(`確定要刪除電影「${title}」嗎？`)) {
        fetch('admin_dashboard.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=delete_movie&movie_id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    }
}

document.getElementById('movieForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('admin_dashboard.php', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    });
});

// ========== 場次管理 ==========
function openAddScreeningModal() {
    document.getElementById('screeningModalTitle').textContent = '新增場次';
    document.getElementById('screening_action').value = 'add_screening';
    document.getElementById('screeningForm').reset();
    document.getElementById('screening_id').value = '';
    document.getElementById('screening_seats').value = 100;
}

function editScreening(id) {
    fetch('admin_dashboard.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=get_screening&screening_id=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const s = data.data;
            document.getElementById('screeningModalTitle').textContent = '編輯場次';
            document.getElementById('screening_action').value = 'edit_screening';
            document.getElementById('screening_id').value = s.ScreeningID;
            document.getElementById('screening_movie_id').value = s.MovieID;
            document.getElementById('screening_hall').value = s.Hall;
            document.getElementById('screening_start_time').value = s.StartTime.replace(' ', 'T');
            document.getElementById('screening_price').value = s.Price;
            document.getElementById('screening_seats').value = s.AvailableSeats;
            new bootstrap.Modal(document.getElementById('screeningModal')).show();
        }
    });
}

function deleteScreening(id) {
    if (confirm('確定要刪除此場次嗎？')) {
        fetch('admin_dashboard.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=delete_screening&screening_id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    }
}

document.getElementById('screeningForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('admin_dashboard.php', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    });
});
</script>
</body>
</html>
