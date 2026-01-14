<?php
/**
 * Management Promotions API
 * List all promotions for management page
 */

header('Content-Type: application/json');
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        throw new Exception('Bạn cần đăng nhập để truy cập');
    }

    // Check if user has Admin role
    $userRole = $_SESSION['user_role_name'] ?? '';
    if ($userRole !== 'Admin') {
        throw new Exception('Bạn không có quyền truy cập trang này');
    }

    // Get database connection
    $pdo = getDBConnection();

    // Get all promotions
    $sql = "SELECT * FROM Promotion ORDER BY MaPromotion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $promotions = $stmt->fetchAll();

    $response = [
        'success' => true,
        'data' => $promotions,
        'message' => 'Lấy danh sách khuyến mãi thành công'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in promotions list: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
