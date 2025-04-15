<?php
require_once 'config/config.php';

// Lấy danh sách công ty
$stmt = $pdo->prepare("SELECT c.*, u.name as contact_name, u.email as contact_email 
                      FROM companies c 
                      JOIN users u ON c.user_id = u.id 
                      ORDER BY c.name ASC");
$stmt->execute();
$companies = $stmt->fetchAll();

// Xử lý tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $pdo->prepare("SELECT c.*, u.name as contact_name, u.email as contact_email 
                          FROM companies c 
                          JOIN users u ON c.user_id = u.id 
                          WHERE c.name LIKE ? OR c.description LIKE ? OR c.industry LIKE ?
                          ORDER BY c.name ASC");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    $companies = $stmt->fetchAll();
}

// Lấy danh sách ngành nghề
$industries = $pdo->query("SELECT DISTINCT industry FROM companies")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách công ty - Website Tìm Việc Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Tìm kiếm</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="mb-3">
                                <label for="search" class="form-label">Từ khóa</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="industry" class="form-label">Ngành nghề</label>
                                <select class="form-select" id="industry" name="industry">
                                    <option value="">Tất cả</option>
                                    <?php foreach ($industries as $ind): ?>
                                        <option value="<?php echo htmlspecialchars($ind); ?>" <?php echo isset($_GET['industry']) && $_GET['industry'] == $ind ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ind); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <h2>Danh sách công ty</h2>
                
                <?php if (count($companies) > 0): ?>
                    <div class="row">
                        <?php foreach ($companies as $company): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($company['name']); ?></h5>
                                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($company['industry']); ?></h6>
                                        <p class="card-text">
                                            <strong>Địa chỉ:</strong> <?php echo htmlspecialchars($company['address']); ?><br>
                                            <strong>Email:</strong> <?php echo htmlspecialchars($company['contact_email']); ?><br>
                                            <strong>Điện thoại:</strong> <?php echo htmlspecialchars($company['phone']); ?>
                                        </p>
                                        <p class="card-text"><?php echo nl2br(htmlspecialchars($company['description'])); ?></p>
                                        <?php if ($company['website']): ?>
                                            <a href="<?php echo htmlspecialchars($company['website']); ?>" class="btn btn-primary" target="_blank">Website</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Không tìm thấy công ty phù hợp.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 