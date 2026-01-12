<?php
/**
 * Create Order API
 * Tạo đơn hàng mới từ giỏ hàng
 */

header('Content-Type: application/json');
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => ''];

try {
    // Check if user is logged in
    if (!isLoggedIn()) {
        throw new Exception('User not logged in');
    }

    $user = getCurrentUser();
    $userId = $user['id'];

    // Get POST data
    $storeId = isset($_POST['store_id']) ? (int)$_POST['store_id'] : 0;
    $paymentMethod = isset($_POST['payment_method']) ? (int)$_POST['payment_method'] : 0;
    $orderNote = isset($_POST['order_note']) ? trim($_POST['order_note']) : '';
    $vatInvoice = isset($_POST['vat_invoice']) ? (int)$_POST['vat_invoice'] : 0;
    $vatEmail = isset($_POST['vat_email']) ? trim($_POST['vat_email']) : '';
    $vatTaxId = isset($_POST['vat_tax_id']) ? trim($_POST['vat_tax_id']) : '';
    $vatCompany = isset($_POST['vat_company']) ? trim($_POST['vat_company']) : '';
    $vatAddress = isset($_POST['vat_address']) ? trim($_POST['vat_address']) : '';

    // Validate required fields
    if (!$storeId) {
        throw new Exception('Store is required');
    }

    if (!$paymentMethod) {
        throw new Exception('Payment method is required');
    }

    // Get cart items
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
        throw new Exception('Cart is empty');
    }

    $cartItems = $_SESSION['cart'];

    // Calculate totals
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += isset($item['total_price']) ? (float)$item['total_price'] : 0;
    }

    $shippingFee = 30000; // Default shipping fee
    $totalAmount = $subtotal + $shippingFee;

    // Get delivery address (for now, use default)
    $deliveryAddress = "54/31 Đ. Phổ Quang, Phường 2, Quận Tân Bình, Hồ Chí Minh";

    // Get database connection
    $pdo = getDBConnection();

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Create order (Note: MaPayment is not in schema, so we store it in session)
        $sql = "INSERT INTO Orders (MaUser, MaStore, DiaChiGiao, PhiVanChuyen, TongTien, TrangThai) 
                VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $storeId, $deliveryAddress, $shippingFee, $totalAmount]);
        $orderId = $pdo->lastInsertId();
        
        // Store payment method in session for this order
        $_SESSION['order_payment_' . $orderId] = $paymentMethod;

        // Create order items
        foreach ($cartItems as $item) {
            $productId = isset($item['product_id']) ? (int)$item['product_id'] : 0;
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            $basePrice = isset($item['base_price']) ? (float)$item['base_price'] : 0;

            if (!$productId) {
                continue;
            }

            // Insert order item
            $sql = "INSERT INTO Order_Item (MaOrder, MaSP, SoLuong, GiaCoBan) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$orderId, $productId, $quantity, $basePrice]);
            $orderItemId = $pdo->lastInsertId();

            // Insert order item options if any
            if (isset($item['options']) && is_array($item['options'])) {
                foreach ($item['options'] as $option) {
                    if (isset($option['value_id'])) {
                        $optionValueId = (int)$option['value_id'];
                        $optionPrice = isset($option['price']) ? (float)$option['price'] : 0;

                        $sql = "INSERT INTO Order_Item_Option (MaOrderItem, MaOptionValue, GiaThem) 
                                VALUES (?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$orderItemId, $optionValueId, $optionPrice]);
                    }
                }
            }
        }

        // Commit transaction
        $pdo->commit();

        // Clear cart after successful order
        $_SESSION['cart'] = [];

        // Generate order code
        $orderCode = 'AHDW' . str_pad($orderId, 3, '0', STR_PAD_LEFT);

        $response = [
            'success' => true,
            'message' => 'Đặt hàng thành công',
            'order_id' => $orderId,
            'order_code' => $orderCode,
            'total_amount' => $totalAmount
        ];

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
