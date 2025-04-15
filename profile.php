<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Lấy thông tin người dùng
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Kiểm tra mật khẩu hiện tại
    if (!empty($current_password) && !password_verify($current_password, $user['password'])) {
        $error = "Mật khẩu hiện tại không đúng!";
    } else {
        // Cập nhật thông tin
        $update_fields = [];
        $params = [];
        
        if ($name != $user['name']) {
            $update_fields[] = "name = ?";
            $params[] = $name;
        }
        
        if ($email != $user['email']) {
            // Kiểm tra email đã tồn tại chưa
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->rowCount() > 0) {
                $error = "Email đã được sử dụng!";
            } else {
                $update_fields[] = "email = ?";
                $params[] = $email;
            }
        }

        if (isset($user['phone']) && $phone != $user['phone']) {
            $update_fields[] = "phone = ?";
            $params[] = $phone;
        }

        if (isset($user['address']) && $address != $user['address']) {
            $update_fields[] = "address = ?";
            $params[] = $address;
        }

        if (isset($user['birthday']) && $birthday != $user['birthday']) {
            $update_fields[] = "birthday = ?";
            $params[] = $birthday;
        }

        if (isset($user['gender']) && $gender != $user['gender']) {
            $update_fields[] = "gender = ?";
            $params[] = $gender;
        }
        
        if (!empty($new_password)) {
            if ($new_password != $confirm_password) {
                $error = "Mật khẩu mới không khớp!";
            } else {
                $update_fields[] = "password = ?";
                $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            }
        }
        
        if (empty($error) && !empty($update_fields)) {
            $params[] = $user_id;
            $query = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($query);
            
            if ($stmt->execute($params)) {
                $success = "Cập nhật thông tin thành công!";
                // Cập nhật session
                $_SESSION['name'] = $name;
                // Lấy lại thông tin người dùng
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3>Hồ sơ cá nhân</h3>
                        <?php if ($_SESSION['role'] === 'student'): ?>
                            <a href="student_profile.php" class="btn btn-outline-primary">Xem hồ sơ sinh viên</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="birthday" class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($user['birthday'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Giới tính</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="Nam" <?php echo ($user['gender'] ?? '') == 'Nam' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="male">Nam</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="Nữ" <?php echo ($user['gender'] ?? '') == 'Nữ' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="female">Nữ</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="other" value="Khác" <?php echo ($user['gender'] ?? '') == 'Khác' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="other">Khác</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                <small class="text-muted">Chỉ điền nếu muốn thay đổi mật khẩu</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 