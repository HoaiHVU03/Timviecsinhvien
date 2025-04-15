<?php
require_once 'config/config.php';

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Xử lý cập nhật trạng thái hồ sơ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id']) && isset($_POST['status'])) {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ? AND job_id IN (SELECT id FROM jobs WHERE company_id IN (SELECT id FROM companies WHERE user_id = ?))");
        $stmt->execute([$status, $application_id, $user_id]);
        $message = '<div class="alert alert-success">Cập nhật trạng thái thành công!</div>';
        
        // Chuyển hướng để tránh vòng lặp
        header('Location: company_applications.php');
        exit();
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Lỗi: ' . $e->getMessage() . '</div>';
    }
}

// Lấy danh sách hồ sơ ứng tuyển
try {
    $stmt = $pdo->prepare("
        SELECT a.*, j.title as job_title, u.name as student_name, u.email as student_email,
               sp.university, sp.major, sp.gpa, sp.graduation_year, sp.skills, sp.experience, sp.achievements
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        JOIN users u ON a.student_id = u.id
        LEFT JOIN student_profiles sp ON a.student_id = sp.user_id
        WHERE j.company_id IN (SELECT id FROM companies WHERE user_id = ?)
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $applications = $stmt->fetchAll();
} catch(PDOException $e) {
    $message = '<div class="alert alert-danger">Lỗi: ' . $e->getMessage() . '</div>';
    $applications = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý hồ sơ ứng tuyển - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quản lý hồ sơ ứng tuyển</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <?php if (empty($applications)): ?>
                            <div class="alert alert-info">Chưa có hồ sơ ứng tuyển nào.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-nowrap">Vị trí</th>
                                            <th class="text-nowrap">Ứng viên</th>
                                            <th class="text-nowrap">Email</th>
                                            <th class="text-nowrap">Trường</th>
                                            <th class="text-nowrap">Chuyên ngành</th>
                                            <th class="text-nowrap">GPA</th>
                                            <th>Kỹ năng</th>
                                            <th>Kinh nghiệm</th>
                                            <th>Thành tích</th>
                                            <th class="text-nowrap">Trạng thái</th>
                                            <th class="text-nowrap">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $application): ?>
                                            <tr>
                                                <td class="text-nowrap"><?php echo htmlspecialchars($application['job_title']); ?></td>
                                                <td class="text-nowrap"><?php echo htmlspecialchars($application['student_name']); ?></td>
                                                <td class="text-nowrap"><?php echo htmlspecialchars($application['student_email']); ?></td>
                                                <td class="text-nowrap"><?php echo htmlspecialchars($application['university'] ?? 'Chưa cập nhật'); ?></td>
                                                <td class="text-nowrap"><?php echo htmlspecialchars($application['major'] ?? 'Chưa cập nhật'); ?></td>
                                                <td class="text-nowrap"><?php echo htmlspecialchars($application['gpa'] ?? 'Chưa cập nhật'); ?></td>
                                                <td class="text-break" style="max-width: 200px;"><?php echo nl2br(htmlspecialchars($application['skills'] ?? 'Chưa cập nhật')); ?></td>
                                                <td class="text-break" style="max-width: 200px;"><?php echo nl2br(htmlspecialchars($application['experience'] ?? 'Chưa cập nhật')); ?></td>
                                                <td class="text-break" style="max-width: 200px;"><?php echo nl2br(htmlspecialchars($application['achievements'] ?? 'Chưa cập nhật')); ?></td>
                                                <td class="text-nowrap">
                                                    <span class="badge bg-<?php 
                                                        echo $application['status'] === 'pending' ? 'warning' : 
                                                            ($application['status'] === 'accepted' ? 'success' : 'danger'); 
                                                    ?>">
                                                        <?php 
                                                        echo $application['status'] === 'pending' ? 'Đang chờ' : 
                                                            ($application['status'] === 'accepted' ? 'Đã chấp nhận' : 'Đã từ chối'); 
                                                        ?>
                                                    </span>
                                                </td>
                                                <td class="text-nowrap">
                                                    <form method="POST" action="" class="d-inline-flex align-items-center gap-2" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái?')">
                                                        <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                        <select name="status" class="form-select form-select-sm" style="min-width: 120px;"
                                                                onchange="this.form.submit()">
                                                            <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>
                                                                Đang chờ
                                                            </option>
                                                            <option value="accepted" <?php echo $application['status'] === 'accepted' ? 'selected' : ''; ?>>
                                                                Chấp nhận
                                                            </option>
                                                            <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>
                                                                Từ chối
                                                            </option>
                                                        </select>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <style>
                                @media (max-width: 768px) {
                                    .table-responsive {
                                        font-size: 14px;
                                    }
                                    .form-select-sm {
                                        font-size: 12px;
                                        padding: 0.25rem 0.5rem;
                                    }
                                }
                            </style>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Xử lý form submit
        document.querySelectorAll('select[name="status"]').forEach(select => {
            select.addEventListener('change', function(e) {
                if (!confirm('Bạn có chắc chắn muốn thay đổi trạng thái?')) {
                    e.preventDefault();
                    // Reset về giá trị cũ nếu người dùng hủy
                    this.value = this.getAttribute('data-original-value');
                    return false;
                }
                // Lưu giá trị mới
                this.setAttribute('data-original-value', this.value);
                this.form.submit();
            });

            // Lưu giá trị ban đầu
            select.setAttribute('data-original-value', select.value);
        });
    </script>
</body>
</html> 