<?php
require_once 'config/config.php';

// Hàm định dạng mức lương
function formatSalary($salary) {
    if ($salary === null) {
        return 'Thương lượng';
    }
    if (is_numeric($salary)) {
        return number_format($salary, 0, ',', '.') . ' VNĐ';
    }
    return $salary;
}

// Lấy các tham số tìm kiếm
$search = isset($_GET['search']) ? $_GET['search'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$salary = isset($_GET['salary']) ? $_GET['salary'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Xây dựng câu truy vấn
$sql = "SELECT j.*, c.name as company_name, c.address as company_address 
        FROM jobs j 
        JOIN companies c ON j.company_id = c.id 
        WHERE 1=1";

$params = [];

// Tìm kiếm theo từ khóa
if (!empty($search)) {
    $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR j.requirements LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Tìm kiếm theo địa điểm
if (!empty($location)) {
    $sql .= " AND j.location = ?";
    $params[] = $location;
}

// Tìm kiếm theo mức lương
if (!empty($salary)) {
    switch ($salary) {
        case '0-5':
            $sql .= " AND (j.salary IS NULL OR 
                    (j.salary >= 0 AND j.salary < 5000000) OR
                    (j.salary LIKE '%-%' AND 
                     CAST(SUBSTRING_INDEX(j.salary, '-', 1) AS UNSIGNED) < 5000000))";
            break;
        case '5-10':
            $sql .= " AND (j.salary >= 5000000 AND j.salary < 10000000 OR
                    (j.salary LIKE '%-%' AND 
                     CAST(SUBSTRING_INDEX(j.salary, '-', 1) AS UNSIGNED) < 10000000 AND
                     CAST(SUBSTRING_INDEX(j.salary, '-', -1) AS UNSIGNED) > 5000000))";
            break;
        case '10-15':
            $sql .= " AND (j.salary >= 10000000 AND j.salary < 15000000 OR
                    (j.salary LIKE '%-%' AND 
                     CAST(SUBSTRING_INDEX(j.salary, '-', 1) AS UNSIGNED) < 15000000 AND
                     CAST(SUBSTRING_INDEX(j.salary, '-', -1) AS UNSIGNED) > 10000000))";
            break;
        case '15-20':
            $sql .= " AND (j.salary >= 15000000 AND j.salary < 20000000 OR
                    (j.salary LIKE '%-%' AND 
                     CAST(SUBSTRING_INDEX(j.salary, '-', 1) AS UNSIGNED) < 20000000 AND
                     CAST(SUBSTRING_INDEX(j.salary, '-', -1) AS UNSIGNED) > 15000000))";
            break;
        case '20+':
            $sql .= " AND (j.salary >= 20000000 OR
                    (j.salary LIKE '%-%' AND 
                     CAST(SUBSTRING_INDEX(j.salary, '-', -1) AS UNSIGNED) >= 20000000))";
            break;
    }
}

// Tìm kiếm theo loại công việc
if (!empty($type)) {
    $sql .= " AND j.type = ?";
    $params[] = $type;
}

$sql .= " ORDER BY j.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $jobs = [];
    error_log("Lỗi truy vấn database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách việc làm - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="jobs.php" class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm việc làm..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <select class="form-select" name="location">
                                        <option value="">Tất cả địa điểm</option>
                                        <?php
                                        $locations = ['Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 'Khác'];
                                        foreach ($locations as $loc) {
                                            $selected = (isset($_GET['location']) && $_GET['location'] == $loc) ? 'selected' : '';
                                            echo "<option value='$loc' $selected>$loc</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </span>
                                    <select class="form-select" name="salary">
                                        <option value="">Tất cả mức lương</option>
                                        <option value="0-5" <?php echo (isset($_GET['salary']) && $_GET['salary'] == '0-5') ? 'selected' : ''; ?>>Dưới 5 triệu</option>
                                        <option value="5-10" <?php echo (isset($_GET['salary']) && $_GET['salary'] == '5-10') ? 'selected' : ''; ?>>5 - 10 triệu</option>
                                        <option value="10-15" <?php echo (isset($_GET['salary']) && $_GET['salary'] == '10-15') ? 'selected' : ''; ?>>10 - 15 triệu</option>
                                        <option value="15-20" <?php echo (isset($_GET['salary']) && $_GET['salary'] == '15-20') ? 'selected' : ''; ?>>15 - 20 triệu</option>
                                        <option value="20+" <?php echo (isset($_GET['salary']) && $_GET['salary'] == '20+') ? 'selected' : ''; ?>>Trên 20 triệu</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-briefcase"></i>
                                    </span>
                                    <select class="form-select" name="type">
                                        <option value="">Tất cả loại</option>
                                        <option value="full_time" <?php echo (isset($_GET['type']) && $_GET['type'] == 'full_time') ? 'selected' : ''; ?>>Toàn thời gian</option>
                                        <option value="part_time_morning" <?php echo (isset($_GET['type']) && $_GET['type'] == 'part_time_morning') ? 'selected' : ''; ?>>Bán thời gian sáng</option>
                                        <option value="part_time_afternoon" <?php echo (isset($_GET['type']) && $_GET['type'] == 'part_time_afternoon') ? 'selected' : ''; ?>>Bán thời gian chiều</option>
                                        <option value="flexible" <?php echo (isset($_GET['type']) && $_GET['type'] == 'flexible') ? 'selected' : ''; ?>>Linh hoạt</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Tìm kiếm
                                </button>
                                <a href="jobs.php" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-redo me-2"></i>Đặt lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <?php if (empty($jobs)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Không tìm thấy việc làm phù hợp với tiêu chí tìm kiếm của bạn.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm job-item">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">
                                        <a href="job_detail.php?id=<?php echo $job['id']; ?>" class="text-decoration-none text-dark">
                                            <?php echo $job['title'] ? htmlspecialchars($job['title']) : 'Chưa cập nhật'; ?>
                                        </a>
                                    </h5>
                                    <span class="badge bg-primary">
                                        <?php 
                                        switch($job['type']) {
                                            case 'full_time':
                                                echo 'Toàn thời gian';
                                                break;
                                            case 'part_time_morning':
                                                echo 'Bán thời gian (Sáng)';
                                                break;
                                            case 'part_time_afternoon':
                                                echo 'Bán thời gian (Chiều)';
                                                break;
                                            case 'flexible':
                                                echo 'Linh hoạt';
                                                break;
                                            default:
                                                echo 'Chưa cập nhật';
                                        }
                                        ?>
                                    </span>
                                </div>
                                
                                <h6 class="card-subtitle mb-3 text-muted">
                                    <i class="fas fa-building me-2"></i>
                                    <?php echo $job['company_name'] ? htmlspecialchars($job['company_name']) : 'Chưa cập nhật'; ?>
                                </h6>

                                <div class="job-info mb-3">
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo $job['location'] ? htmlspecialchars($job['location']) : 'Chưa cập nhật'; ?>
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-briefcase me-1"></i>
                                            <?php echo $job['category'] ? htmlspecialchars($job['category']) : 'Chưa cập nhật'; ?>
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-money-bill-wave me-1"></i>
                                            <?php 
                                            if ($job['salary'] === null) {
                                                echo 'Thương lượng';
                                            } else {
                                                // Kiểm tra nếu là khoảng lương
                                                if (strpos($job['salary'], '-') !== false) {
                                                    $salaryRange = explode('-', $job['salary']);
                                                    $minSalary = trim($salaryRange[0]);
                                                    $maxSalary = trim($salaryRange[1]);
                                                    echo number_format((int)$minSalary, 0, ',', '.') . ' - ' . number_format((int)$maxSalary, 0, ',', '.') . ' VNĐ';
                                                } else {
                                                    echo number_format((int)$job['salary'], 0, ',', '.') . ' VNĐ';
                                                }
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <p class="card-text text-muted small mb-3">
                                    <i class="fas fa-clock me-1"></i>
                                    Đăng ngày: <?php echo date('d/m/Y', strtotime($job['created_at'])); ?>
                                </p>

                                <div class="job-description mb-3">
                                    <?php 
                                    $description = $job['description'] ? strip_tags($job['description']) : 'Chưa cập nhật';
                                    echo mb_substr($description, 0, 150) . (mb_strlen($description) > 150 ? '...' : '');
                                    ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="job_detail.php?id=<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> Xem chi tiết
                                    </a>
                                    <?php if (isStudent()): ?>
                                        <a href="apply_job.php?id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-paper-plane me-1"></i> Ứng tuyển ngay
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .input-group-text {
            border: none;
        }
        
        .form-control, .form-select {
            border-left: none;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }
        
        .card {
            border: none;
            border-radius: 10px;
        }
    </style>
</body>
</html> 