<?php
require_once 'config/config.php';

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['user_id'];
$job_id = $_GET['id'] ?? 0;
$message = '';

// Kiểm tra job_id
if (!$job_id) {
    header('Location: jobs.php');
    exit();
}

// Lấy thông tin công việc
try {
    $stmt = $pdo->prepare("SELECT j.*, c.name as company_name 
                          FROM jobs j 
                          JOIN companies c ON j.company_id = c.id 
                          WHERE j.id = ?");
    $stmt->execute([$job_id]);
    $job = $stmt->fetch();
    
    if (!$job) {
        header('Location: jobs.php');
        exit();
    }
} catch(PDOException $e) {
    $message = '<div class="alert alert-danger">Lỗi: ' . $e->getMessage() . '</div>';
}

// Xử lý nộp đơn
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Kiểm tra xem đã nộp đơn chưa
        $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND student_id = ?");
        $stmt->execute([$job_id, $student_id]);
        if ($stmt->rowCount() > 0) {
            $message = '<div class="alert alert-warning">Bạn đã nộp đơn cho vị trí này rồi!</div>';
        } else {
            // Thêm đơn ứng tuyển
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, student_id, status, created_at) VALUES (?, ?, 'pending', NOW())");
            $stmt->execute([$job_id, $student_id]);
            $message = '<div class="alert alert-success">Nộp đơn thành công!</div>';
        }
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Lỗi: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nộp đơn ứng tuyển - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Nộp đơn ứng tuyển</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <div class="mb-4">
                            <h4>Thông tin vị trí</h4>
                            <p><strong>Tiêu đề:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
                            <p><strong>Công ty:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                            <p><strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                            <p><strong>Yêu cầu:</strong> <?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
                            <p><strong>Mức lương:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
                            <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                        </div>

                        <div class="mb-4">
                            <h4>Thông tin hồ sơ của bạn</h4>
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
                            $stmt->execute([$student_id]);
                            $profile = $stmt->fetch();
                            
                            if ($profile):
                            ?>
                                <p><strong>Trường đại học:</strong> <?php echo htmlspecialchars($profile['university']); ?></p>
                                <p><strong>Chuyên ngành:</strong> <?php echo htmlspecialchars($profile['major']); ?></p>
                                <p><strong>GPA:</strong> <?php echo htmlspecialchars($profile['gpa']); ?></p>
                                <p><strong>Kỹ năng:</strong> <?php echo nl2br(htmlspecialchars($profile['skills'])); ?></p>
                                <p><strong>Kinh nghiệm:</strong> <?php echo nl2br(htmlspecialchars($profile['experience'])); ?></p>
                                <p><strong>Thành tích:</strong> <?php echo nl2br(htmlspecialchars($profile['achievements'])); ?></p>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Bạn chưa có hồ sơ sinh viên. Vui lòng cập nhật hồ sơ trước khi nộp đơn.
                                    <a href="student_profile.php" class="alert-link">Cập nhật hồ sơ</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($profile): ?>
                            <form method="POST" action="">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Nộp đơn ứng tuyển</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 