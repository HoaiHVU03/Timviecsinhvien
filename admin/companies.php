<?php
require_once '../config/config.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Xử lý thêm công ty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $address = $_POST['address'];
        $description = $_POST['description'];
        $logo = $_FILES['logo'];

        // Upload logo
        $logoPath = '';
        if ($logo['error'] === 0) {
            $uploadDir = '../uploads/companies/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $logoPath = $uploadDir . time() . '_' . $logo['name'];
            move_uploaded_file($logo['tmp_name'], $logoPath);
        }

        try {
            $pdo->beginTransaction();

            // Thêm vào bảng users
            $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'company')");
            $stmt->execute([$email, $password]);
            $userId = $pdo->lastInsertId();

            // Thêm vào bảng companies
            $stmt = $pdo->prepare("INSERT INTO companies (user_id, name, address, description, logo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $address, $description, $logoPath]);

            // Ghi log hoạt động
            $stmt = $pdo->prepare("INSERT INTO activities (user_id, description) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user']['id'], "Thêm công ty mới: $name"]);

            $pdo->commit();
            header('Location: companies.php?success=1');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// Xử lý xóa công ty
if (isset($_GET['delete'])) {
    $companyId = $_GET['delete'];
    try {
        $pdo->beginTransaction();

        // Lấy user_id của công ty
        $stmt = $pdo->prepare("SELECT user_id FROM companies WHERE id = ?");
        $stmt->execute([$companyId]);
        $userId = $stmt->fetchColumn();

        // Xóa công ty
        $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
        $stmt->execute([$companyId]);

        // Xóa user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        // Ghi log hoạt động
        $stmt = $pdo->prepare("INSERT INTO activities (user_id, description) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user']['id'], "Xóa công ty ID: $companyId"]);

        $pdo->commit();
        header('Location: companies.php?success=2');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Lấy danh sách công ty
$stmt = $pdo->query("SELECT * FROM companies ORDER BY created_at DESC");
$companies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý công ty - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .company-logo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý công ty</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                <i class="fas fa-plus me-2"></i>Thêm công ty
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
                        echo "Thêm công ty thành công!";
                        break;
                    case 2:
                        echo "Xóa công ty thành công!";
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
                                <th>Logo</th>
                                <th>Tên công ty</th>
                                <th>Địa chỉ</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($companies as $company): ?>
                                <tr>
                                    <td><?php echo $company['id']; ?></td>
                                    <td>
                                        <?php if ($company['logo']): ?>
                                            <img src="<?php echo $company['logo']; ?>" alt="Logo" class="company-logo">
                                        <?php else: ?>
                                            <i class="fas fa-building fa-2x text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($company['name']); ?></td>
                                    <td><?php echo htmlspecialchars($company['address']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($company['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_company.php?id=<?php echo $company['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="companies.php?delete=<?php echo $company['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa công ty này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm công ty -->
    <div class="modal fade" id="addCompanyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm công ty mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Tên công ty</label>
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
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
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