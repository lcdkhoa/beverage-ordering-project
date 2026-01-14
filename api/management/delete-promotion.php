<?php
/**
 * Management Delete Promotion API
 * Delete promotion (Admin only)
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
        throw new Exception('Chỉ Admin mới có quyền xóa khuyến mãi');
    }

    // Get POST data
    $promotionId = isset($_POST['promotion_id']) ? (int)$_POST['promotion_id'] : 0;

    // Validation
    if (!$promotionId) {
        throw new Exception('Mã khuyến mãi không hợp lệ');
    }

    // Get database connection
    $pdo = getDBConnection();

    // Check if promotion exists
    $stmt = $pdo->prepare("SELECT Code FROM Promotion WHERE MaPromotion = ?");
    $stmt->execute([$promotionId]);
    $promotion = $stmt->fetch();
    
    if (!$promotion) {
        throw new Exception('Không tìm thấy khuyến mãi');
    }

    // Delete promotion
    $sql = "DELETE FROM Promotion WHERE MaPromotion = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$promotionId]);

    $response = [
        'success' => true,
        'message' => 'Xóa khuyến mãi thành công'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in delete promotion: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
