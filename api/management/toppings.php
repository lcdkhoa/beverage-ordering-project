<?php
/**
 * Management Toppings API
 * List all toppings for management page
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

    // Get database connection
    $pdo = getDBConnection();

    // Get all toppings from Option_Value where MaOptionGroup = 3
    $sql = "SELECT ov.*, og.TenNhom 
            FROM Option_Value ov 
            INNER JOIN Option_Group og ON ov.MaOptionGroup = og.MaOptionGroup 
            WHERE og.MaOptionGroup = 3
            ORDER BY ov.MaOptionValue DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $toppings = $stmt->fetchAll();

    $response = [
        'success' => true,
        'data' => $toppings,
        'message' => 'Lấy danh sách topping thành công'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in toppings list: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
