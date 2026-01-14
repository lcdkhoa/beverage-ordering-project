<?php
/**
 * Management Categories API
 * Get all categories for dropdown
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

    // Check if user has Staff or Admin role
    $userRole = $_SESSION['user_role_name'] ?? '';
    if ($userRole !== 'Staff' && $userRole !== 'Admin') {
        throw new Exception('Bạn không có quyền truy cập trang này');
    }

    // Get categories
    $categories = getCategories();

    $response = [
        'success' => true,
        'data' => $categories,
        'message' => 'Lấy danh sách danh mục thành công'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in categories list: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
