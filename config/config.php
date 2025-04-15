<?php
// Báo cáo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cấu hình database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Hoai20102003');
define('DB_NAME', 'job_portal');

// Khởi tạo session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm kiểm tra quyền admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Hàm kiểm tra quyền doanh nghiệp
function isCompany() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'company';
}

// Hàm kiểm tra quyền sinh viên
function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'student';
}
?> 