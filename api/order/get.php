<?php
/**
 * Get Orders API
 * Lấy danh sách đơn hàng của user hiện tại
 */

header('Content-Type: application/json');
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => '', 'orders' => []];

try {
    // Check if user is logged in
    if (!isLoggedIn()) {
        throw new Exception('User not logged in');
    }

    $user = getCurrentUser();
    $userId = $user['id'];

    // Get database connection
    $pdo = getDBConnection();

    // Get orders with store information
    $sql = "SELECT o.*, s.TenStore, s.DiaChi as StoreAddress, s.DienThoai as StorePhone
            FROM Orders o
            INNER JOIN Store s ON o.MaStore = s.MaStore
            WHERE o.MaUser = ?
            ORDER BY o.NgayTao DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get order items and options for each order
    foreach ($orders as &$order) {
        $orderId = $order['MaOrder'];
        
        // Generate order code
        $order['OrderCode'] = 'AHDW' . str_pad($orderId, 3, '0', STR_PAD_LEFT);
        
        // Get payment method from session or default
        $paymentMethodId = $_SESSION['order_payment_' . $orderId] ?? null;
        $paymentMethodName = 'Chưa xác định';
        if ($paymentMethodId) {
            $sql = "SELECT TenPayment FROM Payment_Method WHERE MaPayment = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$paymentMethodId]);
            $pm = $stmt->fetch();
            if ($pm) {
                $paymentMethodName = $pm['TenPayment'];
            }
        }
        $order['PaymentMethod'] = $paymentMethodName;
        
        // Get order items
        $sql = "SELECT oi.*, sp.TenSP, sp.HinhAnh
                FROM Order_Item oi
                INNER JOIN SanPham sp ON oi.MaSP = sp.MaSP
                WHERE oi.MaOrder = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get order item options
        foreach ($orderItems as &$item) {
            $sql = "SELECT oio.*, ov.TenGiaTri, og.TenNhom
                    FROM Order_Item_Option oio
                    INNER JOIN Option_Value ov ON oio.MaOptionValue = ov.MaOptionValue
                    INNER JOIN Option_Group og ON ov.MaOptionGroup = og.MaOptionGroup
                    WHERE oio.MaOrderItem = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$item['MaOrderItem']]);
            $item['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate item total
            $itemTotal = ($item['GiaCoBan'] * $item['SoLuong']);
            foreach ($item['options'] as $option) {
                $itemTotal += ($option['GiaThem'] * $item['SoLuong']);
            }
            $item['ItemTotal'] = $itemTotal;
        }
        
        $order['items'] = $orderItems;
        
        // Format date
        $order['NgayTaoFormatted'] = date('d/m/Y H:i', strtotime($order['NgayTao']));
    }

    $response = [
        'success' => true,
        'message' => 'Lấy danh sách đơn hàng thành công',
        'orders' => $orders
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
