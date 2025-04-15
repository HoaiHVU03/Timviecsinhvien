<?php
// Cấu hình database
$host = 'localhost';
$dbname = 'job_portal';
$username = 'root';
$password = 'Hoai20102003';

try {
    // Tạo kết nối PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Thiết lập chế độ lỗi
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Thiết lập chế độ fetch mặc định
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Hiển thị thông báo lỗi
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Khởi tạo session
session_start();

// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh'); 