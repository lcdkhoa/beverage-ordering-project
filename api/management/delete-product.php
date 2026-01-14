<?php
/**
 * Management Delete Product API
 * Delete product (Admin only) - Soft delete by setting TrangThai = 0
 */

header('Content-Type: application/json');
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => ''];

try {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        throw new Exception('Bạn cần đăng nhập để thực hiện thao tác này');
    }

    // Check if user has Admin role
    $userRole = $_SESSION['user_role_name'] ?? '';
    if ($userRole !== 'Admin') {
        throw new Exception('Chỉ Admin mới có quyền xóa sản phẩm');
    }

    // Get POST data
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    // Validation
    if (!$productId || $productId <= 0) {
        throw new Exception('Mã sản phẩm không hợp lệ');
    }

    // Get database connection
    $pdo = getDBConnection();

    // Check if product exists
    $stmt = $pdo->prepare("SELECT MaSP, TenSP FROM SanPham WHERE MaSP = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        throw new Exception('Sản phẩm không tồn tại');
    }

    // Soft delete: Set TrangThai = 0
    $sql = "UPDATE SanPham SET TrangThai = 0 WHERE MaSP = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$productId]);

    $response = [
        'success' => true,
        'message' => 'Xóa sản phẩm "' . $product['TenSP'] . '" thành công'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in delete product: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
