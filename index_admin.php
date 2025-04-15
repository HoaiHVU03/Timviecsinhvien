<?php
require_once 'config/config.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

// Lấy thông tin admin
$adminId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

// Lấy thống kê
$stats = [
    'companies' => $pdo->query("SELECT COUNT(*) FROM companies")->fetchColumn(),
    'jobs' => $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn(),
    'students' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn(),
    'applications' => $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'")->fetchColumn()
];

// Lấy hoạt động gần đây từ bảng users thay vì activities
$stmt = $pdo->query("
    SELECT u.*, u.name as user_name, u.created_at as activity_date,
           CONCAT('Tài khoản mới: ', u.name) as description
    FROM users u 
    ORDER BY u.created_at DESC 
    LIMIT 5
");
$activities = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang quản trị - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            margin: 0.25rem 0;
        }
        .sidebar .nav-link:hover {
            background: #e9ecef;
        }
        .sidebar .nav-link.active {
            background: #0d6efd;
            color: white;
        }
        .main-content {
            padding: 2rem;
        }
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .activity-item {
            border-left: 3px solid #0d6efd;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h5 class="mb-4">Quản trị viên</h5>
                    <div class="nav flex-column">
                        <a class="nav-link active" href="index_admin.php">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Tổng quan
                        </a>
                        <a class="nav-link" href="companies_admin.php">
                            <i class="fas fa-building me-2"></i>
                            Quản lý công ty
                        </a>
                        <a class="nav-link" href="jobs_admin.php">
                            <i class="fas fa-briefcase me-2"></i>
                            Quản lý việc làm
                        </a>
                        <a class="nav-link" href="applications_admin.php">
                            <i class="fas fa-file-alt me-2"></i>
                            Quản lý đơn ứng tuyển
                        </a>
                        <a class="nav-link" href="users_admin.php">
                            <i class="fas fa-users me-2"></i>
                            Quản lý người dùng
                        </a>
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Đăng xuất
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Xin chào, <?php echo htmlspecialchars($admin['name']); ?></h4>
                    <div class="text-muted">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <?php echo date('d/m/Y H:i'); ?>
                    </div>
                </div>

                <!-- Thống kê -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Công ty</h5>
                                <h2 class="mb-0"><?php echo $stats['companies']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Việc làm</h5>
                                <h2 class="mb-0"><?php echo $stats['jobs']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Sinh viên</h5>
                                <h2 class="mb-0"><?php echo $stats['students']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Đơn chờ duyệt</h5>
                                <h2 class="mb-0"><?php echo $stats['applications']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hoạt động gần đây -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Hoạt động gần đây</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($activities as $activity): ?>
                            <div class="activity-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong><?php echo htmlspecialchars($activity['user_name']); ?></strong>
                                        <?php echo htmlspecialchars($activity['description']); ?>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($activity['activity_date'])); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 