<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Lấy danh sách đơn ứng tuyển
if (isStudent()) {
    // Sinh viên xem đơn ứng tuyển của mình
    $stmt = $pdo->prepare("
        SELECT a.*, j.title as job_title, c.name as company_name, j.location, j.salary
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        JOIN companies c ON j.company_id = c.id
        WHERE a.student_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$user_id]);
} else {
    // Doanh nghiệp xem đơn ứng tuyển cho công việc của mình
    $stmt = $pdo->prepare("
        SELECT a.*, j.title as job_title, u.name as student_name, c.name as company_name
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        JOIN users u ON a.student_id = u.id
        JOIN companies c ON j.company_id = c.id
        WHERE j.company_id IN (SELECT id FROM companies WHERE user_id = ?)
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$user_id]);
}

$applications = $stmt->fetchAll();

// Xử lý cập nhật trạng thái đơn ứng tuyển
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isCompany()) {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ? AND job_id IN (SELECT id FROM jobs WHERE company_id = ?)");
    if ($stmt->execute([$status, $application_id, $user_id])) {
        $success = "Cập nhật trạng thái thành công!";
    } else {
        $error = "Có lỗi xảy ra, vui lòng thử lại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn ứng tuyển - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2>Quản lý đơn ứng tuyển</h2>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (count($applications) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <?php if (isCompany()): ?>
                                        <th>Sinh viên</th>
                                    <?php else: ?>
                                        <th>Công ty</th>
                                    <?php endif; ?>
                                    <th>Vị trí</th>
                                    <th>Ngày nộp</th>
                                    <th>Trạng thái</th>
                                    <?php if (isCompany()): ?>
                                        <th>Thao tác</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $index => $app): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <?php if (isCompany()): ?>
                                            <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                                        <?php else: ?>
                                            <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                                        <?php endif; ?>
                                        <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($app['created_at'])); ?></td>
                                        <td>
                                            <?php
                                            $status_class = '';
                                            switch ($app['status']) {
                                                case 'pending':
                                                    $status_class = 'text-warning';
                                                    $status_text = 'Đang chờ';
                                                    break;
                                                case 'accepted':
                                                    $status_class = 'text-success';
                                                    $status_text = 'Đã chấp nhận';
                                                    break;
                                                case 'rejected':
                                                    $status_class = 'text-danger';
                                                    $status_text = 'Đã từ chối';
                                                    break;
                                            }
                                            ?>
                                            <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                        <?php if (isCompany()): ?>
                                            <td>
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $app['status'] == 'pending' ? 'selected' : ''; ?>>Đang chờ</option>
                                                        <option value="accepted" <?php echo $app['status'] == 'accepted' ? 'selected' : ''; ?>>Chấp nhận</option>
                                                        <option value="rejected" <?php echo $app['status'] == 'rejected' ? 'selected' : ''; ?>>Từ chối</option>
                                                    </select>
                                                </form>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php echo isStudent() ? 'Bạn chưa nộp đơn ứng tuyển nào.' : 'Chưa có đơn ứng tuyển nào.'; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 