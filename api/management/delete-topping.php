<?php
/**
 * Management Delete Topping API
 * Delete topping (Admin only) - Hard delete from Option_Value
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
        throw new Exception('Chỉ Admin mới có quyền xóa topping');
    }

    // Get POST data
    $toppingId = isset($_POST['topping_id']) ? (int)$_POST['topping_id'] : 0;

    // Validation
    if (!$toppingId || $toppingId <= 0) {
        throw new Exception('Mã topping không hợp lệ');
    }

    // Get database connection
    $pdo = getDBConnection();

    // Check if topping exists and belongs to Topping group (MaOptionGroup = 3)
    $stmt = $pdo->prepare("SELECT ov.MaOptionValue, ov.TenGiaTri 
                           FROM Option_Value ov 
                           WHERE ov.MaOptionValue = ? AND ov.MaOptionGroup = 3");
    $stmt->execute([$toppingId]);
    $topping = $stmt->fetch();

    if (!$topping) {
        throw new Exception('Topping không tồn tại');
    }

    // Check if topping is being used in any cart items or order items
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Cart_Item_Option WHERE MaOptionValue = ?");
    $stmt->execute([$toppingId]);
    $cartUsage = $stmt->fetch()['count'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Order_Item_Option WHERE MaOptionValue = ?");
    $stmt->execute([$toppingId]);
    $orderUsage = $stmt->fetch()['count'];

    if ($cartUsage > 0 || $orderUsage > 0) {
        throw new Exception('Không thể xóa topping này vì đang được sử dụng trong giỏ hàng hoặc đơn hàng');
    }

    // Delete topping
    $sql = "DELETE FROM Option_Value WHERE MaOptionValue = ? AND MaOptionGroup = 3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$toppingId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Không thể xóa topping');
    }

    $response = [
        'success' => true,
        'message' => 'Xóa topping "' . $topping['TenGiaTri'] . '" thành công'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in delete topping: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
