<?php
/**
 * Định dạng hiển thị lương
 * @param string $salary Chuỗi lương cần định dạng
 * @return string Chuỗi lương đã được định dạng
 */
function formatSalary($salary) {
    if (empty($salary)) {
        return '<span class="text-muted">Thương lượng</span>';
    }
    
    // Kiểm tra nếu có dấu gạch ngang (khoảng lương)
    if (strpos($salary, '-') !== false) {
        $salaries = explode('-', $salary);
        $min = preg_replace('/[^0-9]/', '', $salaries[0]);
        $max = preg_replace('/[^0-9]/', '', $salaries[1]);
        
        if (empty($min) || empty($max)) {
            return '<span class="text-muted">Thương lượng</span>';
        }
        
        $formattedMin = number_format($min, 0, ',', '.');
        $formattedMax = number_format($max, 0, ',', '.');
        
        // Thêm đơn vị tiền tệ và style
        if (strpos($salary, 'USD') !== false || strpos($salary, '$') !== false) {
            return '<span class="text-success fw-bold">' . $formattedMin . ' - ' . $formattedMax . ' <small>USD</small></span>';
        }
        
        return '<span class="text-success fw-bold">' . $formattedMin . ' - ' . $formattedMax . ' <small>VNĐ</small></span>';
    }
    
    // Chuyển đổi chuỗi lương thành số
    $amount = preg_replace('/[^0-9]/', '', $salary);
    if (empty($amount)) {
        return '<span class="text-muted">Thương lượng</span>';
    }
    
    // Định dạng số với dấu phân cách hàng nghìn
    $formatted = number_format($amount, 0, ',', '.');
    
    // Thêm đơn vị tiền tệ và style
    if (strpos($salary, 'USD') !== false || strpos($salary, '$') !== false) {
        return '<span class="text-success fw-bold">' . $formatted . ' <small>USD</small></span>';
    }
    
    return '<span class="text-success fw-bold">' . $formatted . ' <small>VNĐ</small></span>';
} 