<?php
require_once 'config/config.php';
require_once 'config/functions.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

// Xử lý cập nhật trạng thái đơn ứng tuyển
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $applicationId = $_POST['application_id'];
        $status = $_POST['status'];

        try {
            // Cập nhật trạng thái đơn ứng tuyển
            $stmt = $pdo->prepare("
                UPDATE applications 
                SET status = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$status, $applicationId]);

            header('Location: applications_admin.php?success=1');
            exit;
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// Lấy danh sách đơn ứng tuyển
$stmt = $pdo->query("
    SELECT a.*, 
           j.title as job_title,
           c.name as company_name,
           u.name as student_name,
           u.email as student_email
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN companies c ON j.company_id = c.id
    JOIN users u ON a.student_id = u.id
    ORDER BY a.created_at DESC
");
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn ứng tuyển - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-approved {
            background-color: #198754;
            color: #fff;
        }
        .status-rejected {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý đơn ứng tuyển</h2>
            <a href="index_admin.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Quay về Dashboard
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                switch ($_GET['success']) {
                    case 1:
                        echo "Cập nhật trạng thái đơn ứng tuyển thành công!";
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
                                <th>Việc làm</th>
                                <th>Công ty</th>
                                <th>Sinh viên</th>
                                <th>Email</th>
                                <th>Ngày nộp</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td><?php echo $application['id']; ?></td>
                                    <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($application['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($application['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($application['student_email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($application['created_at'])); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ($application['status']) {
                                            case 'pending':
                                                $statusClass = 'status-pending';
                                                $statusText = 'Chờ duyệt';
                                                break;
                                            case 'accepted':
                                                $statusClass = 'status-approved';
                                                $statusText = 'Đã duyệt';
                                                break;
                                            case 'rejected':
                                                $statusClass = 'status-rejected';
                                                $statusText = 'Từ chối';
                                                break;
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatusModal<?php echo $application['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal cập nhật trạng thái -->
                                <div class="modal fade" id="updateStatusModal<?php echo $application['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Cập nhật trạng thái đơn ứng tuyển</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Trạng thái</label>
                                                        <select class="form-select" name="status" required>
                                                            <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                                                            <option value="accepted" <?php echo $application['status'] === 'accepted' ? 'selected' : ''; ?>>Đã duyệt</option>
                                                            <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>Từ chối</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 