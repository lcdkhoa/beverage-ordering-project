<?php
/**
 * Update Order Status API (Admin/Staff Only)
 * Update order status (accept or cancel)
 * POST: order_id, action (accept|cancel)
 */

header('Content-Type: application/json');
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isLoggedIn()) {
        throw new Exception('User not logged in');
    }

    $currentUser = getCurrentUser();
    $userRole = $currentUser['role_name'] ?? '';
    
    // Check if user is admin or staff
    if (strtolower($userRole) !== 'admin' && strtolower($userRole) !== 'staff') {
        throw new Exception('Access denied. Admin or Staff role required.');
    }

    $orderId = (int)($_POST['order_id'] ?? 0);
    $action = trim($_POST['action'] ?? '');

    if ($orderId <= 0) {
        throw new Exception('Invalid order ID');
    }

    if (!in_array($action, ['accept', 'cancel'], true)) {
        throw new Exception('Invalid action. Must be "accept" or "cancel"');
    }

    $pdo = getDBConnection();

    // Get current order status
    $stmt = $pdo->prepare("SELECT TrangThai FROM Orders WHERE MaOrder = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Order not found');
    }

    $currentStatus = strtolower($order['TrangThai']);

    // Only allow status update if current status is Payment_Received or Pending
    if ($currentStatus !== 'payment_received' && $currentStatus !== 'pending') {
        throw new Exception('Chỉ có thể cập nhật trạng thái đơn hàng đang ở trạng thái "Đã nhận thanh toán"');
    }

    // Determine new status based on action
    $newStatus = '';
    if ($action === 'accept') {
        $newStatus = 'Processing'; // Đã nhận đơn
    } else if ($action === 'cancel') {
        $newStatus = 'Store_Cancelled'; // Cửa hàng hủy
    }

    // Update order status
    $stmt = $pdo->prepare("UPDATE Orders SET TrangThai = ? WHERE MaOrder = ?");
    $stmt->execute([$newStatus, $orderId]);

    $statusText = $action === 'accept' ? 'Đã nhận đơn' : 'Đã hủy đơn';
    $response = [
        'success' => true,
        'message' => "Cập nhật trạng thái đơn hàng thành công: $statusText",
        'new_status' => $newStatus
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
