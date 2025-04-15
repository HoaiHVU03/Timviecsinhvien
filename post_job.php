<?php
require_once 'config/config.php';

// Kiểm tra quyền truy cập
if (!isCompany()) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST data: ' . print_r($_POST, true));
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $salary = trim($_POST['salary']);
    $location = trim($_POST['location']);
    $type = trim($_POST['type']);
    $category = trim($_POST['category']);
    $user_id = $_SESSION['user_id'];

    // Debug: In ra giá trị type
    error_log("Type value: " . $type);

    // Lấy company_id từ bảng companies dựa vào user_id
    $stmt = $pdo->prepare("SELECT id FROM companies WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $company = $stmt->fetch();

    if (!$company) {
        $message = '<div class="alert alert-danger">Không tìm thấy thông tin công ty. Vui lòng cập nhật thông tin công ty trước khi đăng việc.</div>';
    } else {
        try {
            // Kiểm tra giá trị type có hợp lệ không
            $valid_types = ['full_time', 'part_time_morning', 'part_time_afternoon', 'flexible'];
            if (!in_array($type, $valid_types)) {
                throw new Exception('Loại công việc không hợp lệ');
            }

            $stmt = $pdo->prepare("INSERT INTO jobs (company_id, title, description, requirements, salary, location, type, category, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$company['id'], $title, $description, $requirements, $salary, $location, $type, $category]);
            $message = '<div class="alert alert-success">Đăng tin tuyển dụng thành công!</div>';
        } catch(Exception $e) {
            $message = '<div class="alert alert-danger">Lỗi: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng tin tuyển dụng - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Đăng tin tuyển dụng</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($message)): ?>
                            <?php echo $message; ?>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả công việc</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="requirements" class="form-label">Yêu cầu</label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="4" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="salary" class="form-label">Mức lương</label>
                                <input type="text" class="form-control" id="salary" name="salary" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="location" class="form-label">Địa điểm</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="type" class="form-label">Loại công việc</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Chọn loại công việc</option>
                                    <option value="full_time" <?php echo isset($_POST['type']) && $_POST['type'] == 'full_time' ? 'selected' : ''; ?>>Toàn thời gian</option>
                                    <option value="part_time_morning" <?php echo isset($_POST['type']) && $_POST['type'] == 'part_time_morning' ? 'selected' : ''; ?>>Bán thời gian (Sáng)</option>
                                    <option value="part_time_afternoon" <?php echo isset($_POST['type']) && $_POST['type'] == 'part_time_afternoon' ? 'selected' : ''; ?>>Bán thời gian (Chiều)</option>
                                    <option value="flexible" <?php echo isset($_POST['type']) && $_POST['type'] == 'flexible' ? 'selected' : ''; ?>>Linh hoạt</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Ngành nghề</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Chọn ngành nghề</option>
                                    <option value="IT" <?php echo isset($_POST['category']) && $_POST['category'] == 'IT' ? 'selected' : ''; ?>>Công nghệ thông tin</option>
                                    <option value="Marketing" <?php echo isset($_POST['category']) && $_POST['category'] == 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                                    <option value="Design" <?php echo isset($_POST['category']) && $_POST['category'] == 'Design' ? 'selected' : ''; ?>>Thiết kế</option>
                                    <option value="Education" <?php echo isset($_POST['category']) && $_POST['category'] == 'Education' ? 'selected' : ''; ?>>Giáo dục</option>
                                    <option value="Finance" <?php echo isset($_POST['category']) && $_POST['category'] == 'Finance' ? 'selected' : ''; ?>>Tài chính</option>
                                    <option value="Sales" <?php echo isset($_POST['category']) && $_POST['category'] == 'Sales' ? 'selected' : ''; ?>>Bán hàng</option>
                                    <option value="Customer Service" <?php echo isset($_POST['category']) && $_POST['category'] == 'Customer Service' ? 'selected' : ''; ?>>Dịch vụ khách hàng</option>
                                    <option value="Other" <?php echo isset($_POST['category']) && $_POST['category'] == 'Other' ? 'selected' : ''; ?>>Khác</option>
                                </select>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Đăng tin</button>
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