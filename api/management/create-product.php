<?php
/**
 * Management Create Product API
 * Create new product (Admin only)
 */

header('Content-Type: application/json');
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => '', 'product_id' => null];

try {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        throw new Exception('Bạn cần đăng nhập để thực hiện thao tác này');
    }

    // Check if user has Admin role
    $userRole = $_SESSION['user_role_name'] ?? '';
    if ($userRole !== 'Admin') {
        throw new Exception('Chỉ Admin mới có quyền thêm sản phẩm mới');
    }

    // Get POST data
    $tenSP = isset($_POST['ten_sp']) ? trim($_POST['ten_sp']) : '';
    $giaCoBan = isset($_POST['gia_co_ban']) ? trim($_POST['gia_co_ban']) : '';
    $maCategory = isset($_POST['ma_category']) ? (int)$_POST['ma_category'] : 0;
    $hinhAnh = isset($_POST['hinh_anh']) ? trim($_POST['hinh_anh']) : '';

    // Validation
    if (empty($tenSP)) {
        throw new Exception('Vui lòng nhập tên sản phẩm');
    }

    if (empty($giaCoBan) || !is_numeric($giaCoBan) || $giaCoBan < 0) {
        throw new Exception('Giá bán không hợp lệ');
    }

    if (!$maCategory) {
        throw new Exception('Vui lòng chọn danh mục');
    }

    // Get database connection
    $pdo = getDBConnection();

    // Check if category exists
    $stmt = $pdo->prepare("SELECT MaCategory FROM Category WHERE MaCategory = ? AND TrangThai = 1");
    $stmt->execute([$maCategory]);
    if (!$stmt->fetch()) {
        throw new Exception('Danh mục không tồn tại');
    }

    // Default image if not provided
    if (empty($hinhAnh)) {
        $hinhAnh = 'assets/img/products/product_one.png';
    }

    // Insert new product
    $sql = "INSERT INTO SanPham (TenSP, GiaCoBan, HinhAnh, MaCategory, TrangThai) 
            VALUES (?, ?, ?, ?, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tenSP, $giaCoBan, $hinhAnh, $maCategory]);

    $productId = $pdo->lastInsertId();

    $response = [
        'success' => true,
        'message' => 'Thêm sản phẩm mới thành công',
        'product_id' => $productId
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in create product: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
