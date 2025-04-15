<?php
// Kiểm tra xem config.php đã được include chưa
if (!isset($pdo)) {
    require_once dirname(__DIR__) . '/config/config.php';
}

// Kiểm tra session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Kiểm tra kết nối database
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng kiểm tra cấu hình.");
}

// Lấy các tham số tìm kiếm
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Xây dựng câu truy vấn
$sql = "SELECT j.*, c.name as company_name, c.address as company_address 
        FROM jobs j 
        JOIN companies c ON j.company_id = c.id 
        WHERE 1=1";

$params = [];

if (!empty($keyword)) {
    $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR j.requirements LIKE ?)";
    $searchTerm = "%$keyword%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($location)) {
    $sql .= " AND j.location LIKE ?";
    $params[] = "%$location%";
}

if (!empty($category)) {
    $sql .= " AND j.category LIKE ?";
    $params[] = "%$category%";
}

$sql .= " ORDER BY j.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $jobs = [];
    // Log lỗi nếu cần
    error_log("Lỗi truy vấn database: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" name="keyword" placeholder="Tìm kiếm công việc theo tiêu đề, mô tả hoặc yêu cầu..." value="<?php echo htmlspecialchars($keyword); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Tìm
                            </button>
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
                                        <?php echo formatSalary($job['salary']); ?>
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