<?php
require_once 'config/config.php';

// Xóa tất cả các biến session
session_unset();

// Hủy session
session_destroy();

// Chuyển hướng về trang chủ
header("Location: index.php");
exit();
?> 