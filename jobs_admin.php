<?php
require_once 'config/config.php';
require_once 'config/functions.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

// Xử lý thêm việc làm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $title = $_POST['title'];
        $companyId = $_POST['company_id'];
        $category = $_POST['category'];
        $location = $_POST['location'];
        $salary = $_POST['salary'];
        $description = $_POST['description'];
        $requirements = $_POST['requirements'];

        try {
            $pdo->beginTransaction();

            // Thêm vào bảng jobs
            $stmt = $pdo->prepare("
                INSERT INTO jobs (company_id, title, category, location, salary, description, requirements) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$companyId, $title, $category, $location, $salary, $description, $requirements]);

            // Ghi log hoạt động
            $stmt = $pdo->prepare("INSERT INTO activities (user_id, description) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user']['id'], "Thêm việc làm mới: $title"]);

            $pdo->commit();
            header('Location: jobs_admin.php?success=1');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// Xử lý xóa việc làm
if (isset($_GET['delete'])) {
    $jobId = $_GET['delete'];
    try {
        $pdo->beginTransaction();

        // Xóa việc làm
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$jobId]);

        // Ghi log hoạt động
        $stmt = $pdo->prepare("INSERT INTO activities (user_id, description) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user']['id'], "Xóa việc làm ID: $jobId"]);

        $pdo->commit();
        header('Location: jobs_admin.php?success=2');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Lấy danh sách công ty cho select
$stmt = $pdo->query("SELECT id, name FROM companies ORDER BY name");
$companies = $stmt->fetchAll();

// Lấy danh sách việc làm
$stmt = $pdo->query("
    SELECT j.*, c.name as company_name 
    FROM jobs j 
    JOIN companies c ON j.company_id = c.id 
    ORDER BY j.created_at DESC
");
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý việc làm - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý việc làm</h2>
            <a href="index_admin.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Quay về Dashboard
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJobModal">
                <i class="fas fa-plus me-2"></i>Thêm việc làm
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
                        echo "Thêm việc làm thành công!";
                        break;
                    case 2:
                        echo "Xóa việc làm thành công!";
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
                                <th>Tiêu đề</th>
                                <th>Công ty</th>
                                <th>Danh mục</th>
                                <th>Địa điểm</th>
                                <th>Lương</th>
                                <th>Ngày đăng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><?php echo $job['id']; ?></td>
                                    <td><?php echo htmlspecialchars($job['title'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($job['company_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($job['category'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($job['location'] ?? ''); ?></td>
                                    <td><?php echo formatSalary($job['salary'] ?? ''); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($job['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_job_admin.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="jobs_admin.php?delete=<?php echo $job['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa việc làm này?')">
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

    <!-- Modal thêm việc làm -->
    <div class="modal fade" id="addJobModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm việc làm mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Công ty</label>
                            <select class="form-select" name="company_id" required>
                                <option value="">Chọn công ty</option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?php echo $company['id']; ?>">
                                        <?php echo htmlspecialchars($company['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <input type="text" class="form-control" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa điểm</label>
                            <input type="text" class="form-control" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lương</label>
                            <input type="text" class="form-control" name="salary" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yêu cầu</label>
                            <textarea class="form-control" name="requirements" rows="3" required></textarea>
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