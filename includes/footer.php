<footer class="bg-dark text-light">
    <div class="container-fluid px-5 py-5">
        <div class="row g-4">
            <!-- Cột thông tin chính -->
            <div class="col-lg-4">
                <div class="footer-brand mb-4">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-briefcase me-2"></i>
                        Tìm Việc Sinh Viên
                    </h4>
                    <p class="text-muted mb-4">
                        Nền tảng kết nối sinh viên với các cơ hội việc làm phù hợp. 
                        Giúp sinh viên tìm được công việc lý tưởng và doanh nghiệp tìm được nhân tài.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cột liên kết nhanh -->
            <div class="col-lg-2 col-md-4">
                <h6 class="text-primary mb-4">Liên kết nhanh</h6>
                <ul class="footer-links">
                    <li>
                        <a href="index.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Trang chủ
                        </a>
                    </li>
                    <li>
                        <a href="jobs.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Việc làm
                        </a>
                    </li>
                    <li>
                        <a href="companies.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Công ty
                        </a>
                    </li>
                    <li>
                        <a href="about.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Giới thiệu
                        </a>
                    </li>
                    <li>
                        <a href="contact.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Liên hệ
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Cột dành cho sinh viên -->
            <div class="col-lg-3 col-md-4">
                <h6 class="text-primary mb-4">Dành cho sinh viên</h6>
                <ul class="footer-links">
                    <li>
                        <a href="student_profile.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Hồ sơ cá nhân
                        </a>
                    </li>
                    <li>
                        <a href="student_applications.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Đơn ứng tuyển
                        </a>
                    </li>
                    <li>
                        <a href="tips.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Mẹo tìm việc
                        </a>
                    </li>
                    <li>
                        <a href="resources.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Tài liệu hữu ích
                        </a>
                    </li>
                    <li>
                        <a href="career_advice.php">
                            <i class="fas fa-chevron-right me-2"></i>
                            Tư vấn nghề nghiệp
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Cột liên hệ -->
            <div class="col-lg-3 col-md-4">
                <h6 class="text-primary mb-4">Liên hệ</h6>
                <ul class="footer-contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Đại Học Hùng VươngVương</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>(84) 3865748333</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>contact@timviecsinhvien.com</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Thứ 2 - Thứ 6: 8:00 - 17:00</span>
                    </li>
                </ul>
                
                <div class="newsletter mt-4">
                    <h6 class="text-primary mb-3">Đăng ký nhận tin</h6>
                    <form class="d-flex">
                        <input type="email" class="form-control me-2" placeholder="Email của bạn">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted">
                    &copy; <?php echo date('Y'); ?> Xuân Hoài.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="privacy.php" class="text-muted text-decoration-none me-3">Chính sách bảo mật</a>
                <a href="terms.php" class="text-muted text-decoration-none me-3">Điều khoản sử dụng</a>
                <a href="sitemap.php" class="text-muted text-decoration-none">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer-brand h4 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #0d6efd;
    }

    .social-links {
        display: flex;
        gap: 1rem;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
        color: #0d6efd;
        transition: all 0.3s ease;
    }

    .social-icon:hover {
        background: #0d6efd;
        transform: translateY(-3px);
        color: #fff;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.75rem;
    }

    .footer-links a {
        color: #adb5bd;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .footer-links a:hover {
        color: #0d6efd;
        padding-left: 5px;
    }

    .footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-contact li {
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
    }

    .footer-contact i {
        color: #0d6efd;
        margin-right: 1rem;
        margin-top: 0.25rem;
    }

    .footer-contact span {
        color: #adb5bd;
    }

    .newsletter .form-control {
        background: rgba(13, 110, 253, 0.1);
        border: 1px solid rgba(13, 110, 253, 0.2);
        color: #fff;
    }

    .newsletter .form-control:focus {
        background: rgba(13, 110, 253, 0.15);
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .newsletter .form-control::placeholder {
        color: #adb5bd;
    }

    .newsletter .btn {
        padding: 0.5rem 1rem;
        background: #0d6efd;
        border: none;
    }

    .newsletter .btn:hover {
        background: #0b5ed7;
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .border-secondary {
        border-color: rgba(13, 110, 253, 0.1) !important;
    }

    @media (max-width: 768px) {
        .footer-brand {
            text-align: center;
        }

        .social-links {
            justify-content: center;
        }

        .footer-links,
        .footer-contact {
            text-align: center;
        }

        .footer-links a,
        .footer-contact li {
            justify-content: center;
        }
    }
</style> 