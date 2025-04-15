<?php
require_once 'config/config.php';

try {
    // Tạo tài khoản sinh viên
    $student_password = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Nguyễn Văn A', 'student@example.com', $student_password, 'student']);

    // Tạo tài khoản công ty
    $company_password = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Công Ty ABC', 'company@example.com', $company_password, 'company']);

    echo "Đã tạo tài khoản mẫu thành công!<br>";
    echo "Tài khoản sinh viên:<br>";
    echo "Email: student@example.com<br>";
    echo "Mật khẩu: 123456<br><br>";
    echo "Tài khoản công ty:<br>";
    echo "Email: company@example.com<br>";
    echo "Mật khẩu: 123456<br>";

} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?> 