<?php
require_once 'config/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $university = $_POST['university'];
    $major = $_POST['major'];
    $gpa = $_POST['gpa'];
    $graduation_year = $_POST['graduation_year'];
    $work_time_preference = $_POST['work_time_preference'];
    $desired_position = $_POST['desired_position'];
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];
    $achievements = $_POST['achievements'];

    try {
        // Kiểm tra xem profile đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT id FROM student_profiles WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $profile = $stmt->fetch();

        if ($profile) {
            // Cập nhật profile
            $stmt = $pdo->prepare("UPDATE student_profiles SET 
                university = ?, 
                major = ?, 
                gpa = ?, 
                graduation_year = ?, 
                work_time_preference = ?, 
                desired_position = ?, 
                skills = ?, 
                experience = ?, 
                achievements = ? 
                WHERE user_id = ?");
            $stmt->execute([
                $university, $major, $gpa, $graduation_year, 
                $work_time_preference, $desired_position, 
                $skills, $experience, $achievements, $user_id
            ]);
        } else {
            // Tạo profile mới
            $stmt = $pdo->prepare("INSERT INTO student_profiles 
                (user_id, university, major, gpa, graduation_year, work_time_preference, 
                desired_position, skills, experience, achievements) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $user_id, $university, $major, $gpa, $graduation_year, 
                $work_time_preference, $desired_position, 
                $skills, $experience, $achievements
            ]);
        }

        $message = '<div class="alert alert-success">Cập nhật hồ sơ thành công!</div>';
    } catch(PDOException $e) {
        $message = '<div class="alert alert-danger">Lỗi: ' . $e->getMessage() . '</div>';
    }
}

// Lấy thông tin profile hiện tại
try {
    $stmt = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch();
} catch(PDOException $e) {
    $message = '<div class="alert alert-danger">Lỗi: ' . $e->getMessage() . '</div>';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ sinh viên - Job Portal</title>
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
                        <h3 class="card-title">Hồ sơ sinh viên</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="university" class="form-label">Trường đại học</label>
                                    <input type="text" class="form-control" id="university" name="university" 
                                           value="<?php echo $profile['university'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="major" class="form-label">Chuyên ngành</label>
                                    <input type="text" class="form-control" id="major" name="major" 
                                           value="<?php echo $profile['major'] ?? ''; ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gpa" class="form-label">Điểm trung bình (GPA)</label>
                                    <input type="number" step="0.01" class="form-control" id="gpa" name="gpa" 
                                           value="<?php echo $profile['gpa'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="graduation_year" class="form-label">Năm tốt nghiệp</label>
                                    <input type="number" class="form-control" id="graduation_year" name="graduation_year" 
                                           value="<?php echo $profile['graduation_year'] ?? ''; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="work_time_preference" class="form-label">Thời gian làm việc mong muốn</label>
                                <select class="form-select" id="work_time_preference" name="work_time_preference" required>
                                    <option value="">Chọn thời gian làm việc</option>
                                    <option value="full_time" <?php echo ($profile['work_time_preference'] ?? '') == 'full_time' ? 'selected' : ''; ?>>
                                        Full-time
                                    </option>
                                    <option value="part_time_morning" <?php echo ($profile['work_time_preference'] ?? '') == 'part_time_morning' ? 'selected' : ''; ?>>
                                        Part-time (Buổi sáng)
                                    </option>
                                    <option value="part_time_afternoon" <?php echo ($profile['work_time_preference'] ?? '') == 'part_time_afternoon' ? 'selected' : ''; ?>>
                                        Part-time (Buổi chiều)
                                    </option>
                                    <option value="flexible" <?php echo ($profile['work_time_preference'] ?? '') == 'flexible' ? 'selected' : ''; ?>>
                                        Linh hoạt
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="desired_position" class="form-label">Vị trí mong muốn</label>
                                <input type="text" class="form-control" id="desired_position" name="desired_position" 
                                       value="<?php echo $profile['desired_position'] ?? ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="skills" class="form-label">Kỹ năng</label>
                                <textarea class="form-control" id="skills" name="skills" rows="3" required><?php echo $profile['skills'] ?? ''; ?></textarea>
                                <small class="text-muted">Mỗi kỹ năng cách nhau bằng dấu phẩy</small>
                            </div>

                            <div class="mb-3">
                                <label for="experience" class="form-label">Kinh nghiệm</label>
                                <textarea class="form-control" id="experience" name="experience" rows="3"><?php echo $profile['experience'] ?? ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="achievements" class="form-label">Thành tích</label>
                                <textarea class="form-control" id="achievements" name="achievements" rows="3"><?php echo $profile['achievements'] ?? ''; ?></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Lưu hồ sơ</button>
                            </div>
                        </form>
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