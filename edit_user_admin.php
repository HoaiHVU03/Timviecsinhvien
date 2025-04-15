<?php
require_once 'config/config.php';
require_once 'config/functions.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

// Kiểm tra ID người dùng
if (!isset($_GET['id'])) {
    header('Location: users_admin.php');
    exit;
}

$userId = $_GET['id'];

// Lấy thông tin người dùng
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users_admin.php');
    exit;
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    try {
        // Cập nhật thông tin người dùng
        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = ?, email = ?, role = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $email, $role, $userId]);

        header('Location: users_admin.php?success=5');
        exit;
    } catch (Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa thông tin người dùng - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Sửa thông tin người dùng</h2>
            <a href="users_admin.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Tên</label>
                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò</label>
                        <select class="form-select" name="role" required>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                            <option value="company" <?php echo $user['role'] === 'company' ? 'selected' : ''; ?>>Doanh nghiệp</option>
                            <option value="student" <?php echo $user['role'] === 'student' ? 'selected' : ''; ?>>Sinh viên</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 