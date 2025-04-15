<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';

// Lấy thông tin người dùng
$user_name = '';
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $user_name = $user['name'];
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="fas fa-briefcase me-2"></i>
            <span class="fw-bold">Tìm Việc Sinh Viên</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="index.php">
                        <i class="fas fa-home me-1"></i>
                        <span>Trang chủ</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="jobs.php">
                        <i class="fas fa-search me-1"></i>
                        <span>Việc làm</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="companies.php">
                        <i class="fas fa-building me-1"></i>
                        <span>Công ty</span>
                    </a>
                </li>
                <?php if (isLoggedIn() && isAdmin()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cog me-1"></i>Quản trị
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="index_admin.php"><i class="fas fa-tachometer-alt me-2"></i>Tổng quan</a></li>
                        <li><a class="dropdown-item" href="companies_admin.php"><i class="fas fa-building me-2"></i>Quản lý công ty</a></li>
                        <li><a class="dropdown-item" href="jobs_admin.php"><i class="fas fa-briefcase me-2"></i>Quản lý việc làm</a></li>
                        <li><a class="dropdown-item" href="applications_admin.php"><i class="fas fa-file-alt me-2"></i>Quản lý đơn ứng tuyển</a></li>
                        <li><a class="dropdown-item" href="users_admin.php"><i class="fas fa-users me-2"></i>Quản lý người dùng</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if (isCompany()): ?>
                        <li class="nav-item me-2">
                            <a class="nav-link btn btn-outline-light btn-sm d-flex align-items-center" href="post_job.php">
                                <i class="fas fa-plus-circle me-1"></i>
                                <span>Đăng tin</span>
                            </a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="nav-link btn btn-outline-light btn-sm d-flex align-items-center" href="company_applications.php">
                                <i class="fas fa-file-alt me-1"></i>
                                <span>Hồ sơ</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="avatar me-2">
                                <i class="fas fa-user-circle fa-lg"></i>
                            </div>
                            <span class="fw-medium"><?php echo htmlspecialchars($user_name); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <?php if (isStudent()): ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="student_profile.php">
                                        <i class="fas fa-user-graduate me-2"></i>
                                        <span>Hồ sơ cá nhân</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="company_profile.php">
                                        <i class="fas fa-building me-2"></i>
                                        <span>Thông tin công ty</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    <span>Đăng xuất</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="nav-link btn btn-outline-light btn-sm" href="login.php">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light btn-sm text-primary" href="register.php">Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    .navbar {
        padding: 0.8rem 1rem;
    }
    
    .navbar-brand {
        font-size: 1.4rem;
    }
    
    .nav-link {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }
    
    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .dropdown-menu {
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem;
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .avatar {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .job-item {
        transition: transform 0.2s;
    }
    
    .job-item:hover {
        transform: translateY(-5px);
    }
</style> 