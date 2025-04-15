<?php
require_once 'config/config.php';

// Hàm xử lý mức lương
function formatSalary($salary) {
    // Nếu là số, định dạng với dấu phân cách hàng nghìn
    if (is_numeric($salary)) {
        return number_format($salary) . ' VNĐ';
    }
    // Nếu là chuỗi, trả về nguyên bản
    return $salary;
}

if (!isset($_GET['id'])) {
    header("Location: jobs.php");
    exit();
}

$job_id = $_GET['id'];

// Lấy thông tin việc làm
$stmt = $pdo->prepare("SELECT j.*, c.name as company_name, c.description as company_description, c.address as company_address 
                       FROM jobs j 
                       JOIN companies c ON j.company_id = c.id 
                       WHERE j.id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    header("Location: jobs.php");
    exit();
}

// Xử lý nộp đơn
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isStudent()) {
    $student_id = $_SESSION['user_id'];
    
    // Kiểm tra xem đã nộp đơn chưa
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND student_id = ?");
    $stmt->execute([$job_id, $student_id]);
    if ($stmt->rowCount() > 0) {
        $message = '<div class="alert alert-warning">Bạn đã nộp đơn cho vị trí này rồi!</div>';
    } else {
        // Thêm đơn ứng tuyển
        $stmt = $pdo->prepare("INSERT INTO applications (job_id, student_id, status, created_at) VALUES (?, ?, 'pending', NOW())");
        if ($stmt->execute([$job_id, $student_id])) {
            $message = '<div class="alert alert-success">Nộp đơn thành công!</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job['title']); ?> - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title"><?php echo htmlspecialchars($job['title']); ?></h2>
                        <h4 class="card-subtitle mb-3 text-muted"><?php echo htmlspecialchars($job['company_name']); ?></h4>
                        
                        <div class="mb-4">
                            <h5>Mô tả công việc</h5>
                            <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Yêu cầu</h5>
                            <p><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Thông tin khác</h5>
                            <p>
                                <strong>Mức lương:</strong> <?php echo formatSalary($job['salary']); ?><br>
                                <strong>Địa điểm:</strong> <?php echo $job['location'] ? htmlspecialchars($job['location']) : 'Chưa cập nhật'; ?><br>
                                <strong>Ngành nghề:</strong> <?php echo $job['category'] ? htmlspecialchars($job['category']) : 'Chưa cập nhật'; ?><br>
                                <strong>Ngày đăng:</strong> <?php echo date('d/m/Y', strtotime($job['created_at'])); ?>
                            </p>
                        </div>
                        
                        <?php if (isStudent()): ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <button type="submit" class="btn btn-primary">Nộp đơn ứng tuyển</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin công ty</h5>
                        <p class="card-text">
                            <strong>Địa chỉ:</strong> <?php echo htmlspecialchars($job['company_address']); ?><br>
                            <strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($job['company_description'])); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 