<?php
require_once 'config/config.php';

if (!isset($_GET['id'])) {
    header("Location: companies.php");
    exit();
}

$company_id = $_GET['id'];

// Lấy thông tin công ty
$stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$company = $stmt->fetch();

if (!$company) {
    header("Location: companies.php");
    exit();
}

// Lấy danh sách việc làm của công ty
$stmt = $conn->prepare("SELECT * FROM jobs WHERE company_id = ? ORDER BY created_at DESC");
$stmt->execute([$company_id]);
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($company['name']); ?> - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title"><?php echo htmlspecialchars($company['name']); ?></h2>
                        
                        <div class="mb-4">
                            <h5>Thông tin công ty</h5>
                            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($company['address']); ?></p>
                            <p><strong>Ngành nghề:</strong> <?php echo htmlspecialchars($company['industry']); ?></p>
                            <p><strong>Mô tả:</strong></p>
                            <div class="ms-3">
                                <?php echo nl2br(htmlspecialchars($company['description'])); ?>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Việc làm đang tuyển</h5>
                            <?php if (count($jobs) > 0): ?>
                                <div class="list-group">
                                    <?php foreach ($jobs as $job): ?>
                                        <a href="job_detail.php?id=<?php echo $job['id']; ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($job['title']); ?></h6>
                                                <small><?php echo date('d/m/Y', strtotime($job['created_at'])); ?></small>
                                            </div>
                                            <p class="mb-1">
                                                <strong>Địa điểm:</strong> <?php echo htmlspecialchars($job['location']); ?><br>
                                                <strong>Mức lương:</strong> <?php echo htmlspecialchars($job['salary']); ?>
                                            </p>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    Hiện tại công ty chưa có tin tuyển dụng nào.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin liên hệ</h5>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($company['email']); ?></p>
                        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($company['phone']); ?></p>
                        <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank"><?php echo htmlspecialchars($company['website']); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 