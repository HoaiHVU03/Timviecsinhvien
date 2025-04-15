<?php
require_once 'config/config.php';
require_once 'config/functions.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

// Xử lý thêm người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        try {
            // Thêm vào bảng users
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password, role) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $password, $role]);

            header('Location: users_admin.php?success=1');
            exit;
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// Xử lý xóa người dùng
if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    try {
        // Xóa người dùng
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND id != ?");
        $stmt->execute([$userId, $_SESSION['user_id']]);

        header('Location: users_admin.php?success=2');
        exit;
    } catch (Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Xử lý khóa/mở khóa tài khoản
if (isset($_GET['toggle_status'])) {
    $userId = $_GET['toggle_status'];
    try {
        // Lấy trạng thái hiện tại
        $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        // Đảo ngược trạng thái
        $newStatus = $user['is_active'] ? 0 : 1;
        
        // Cập nhật trạng thái
        $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ? AND id != ?");
        $stmt->execute([$newStatus, $userId, $_SESSION['user_id']]);

        header('Location: users_admin.php?success=3');
        exit;
    } catch (Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Xử lý đặt lại mật khẩu
if (isset($_GET['reset_password'])) {
    $userId = $_GET['reset_password'];
    try {
        // Đặt mật khẩu mặc định là "123456"
        $hashedPassword = password_hash("123456", PASSWORD_DEFAULT);
        
        // Cập nhật mật khẩu
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);

        header('Location: users_admin.php?success=4');
        exit;
    } catch (Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Lấy danh sách người dùng
$stmt = $pdo->query("
    SELECT * FROM users 
    ORDER BY created_at DESC
");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý người dùng</h2>
            <a href="index_admin.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Quay về Dashboard
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-2"></i>Thêm người dùng
            </button>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                switch ($_GET['success']) {
                    case 1:
                        echo "Thêm người dùng thành công!";
                        break;
                    case 2:
                        echo "Xóa người dùng thành công!";
                        break;
                    case 3:
                        echo "Cập nhật trạng thái tài khoản thành công!";
                        break;
                    case 4:
                        echo "Đặt lại mật khẩu thành công! Mật khẩu mới là: 123456";
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <?php
                                        $roleClass = '';
                                        $roleText = '';
                                        switch ($user['role']) {
                                            case 'admin':
                                                $roleClass = 'bg-danger';
                                                $roleText = 'Quản trị viên';
                                                break;
                                            case 'company':
                                                $roleClass = 'bg-primary';
                                                $roleText = 'Doanh nghiệp';
                                                break;
                                            case 'student':
                                                $roleClass = 'bg-success';
                                                $roleText = 'Sinh viên';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $roleClass; ?>">
                                            <?php echo $roleText; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="edit_user_admin.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="user_detail_admin.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="users_admin.php?reset_password=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning" title="Đặt lại mật khẩu" onclick="return confirm('Bạn có chắc chắn muốn đặt lại mật khẩu cho người dùng này? Mật khẩu mới sẽ là: 123456')">
                                                <i class="fas fa-key"></i>
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="users_admin.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm người dùng -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm người dùng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Tên</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vai trò</label>
                            <select class="form-select" name="role" required>
                                <option value="admin">Quản trị viên</option>
                                <option value="company">Doanh nghiệp</option>
                                <option value="student">Sinh viên</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 