<?php
/**
 * Delete Cart Item API
 * Xóa sản phẩm khỏi giỏ hàng
 */

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => ''];

try {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        throw new Exception('Cart is empty');
    }

    $itemIndex = isset($_POST['item_index']) ? (int)$_POST['item_index'] : -1;
    
    if ($itemIndex < 0 || $itemIndex >= count($_SESSION['cart'])) {
        throw new Exception('Invalid item index');
    }

    // Remove item from cart
    array_splice($_SESSION['cart'], $itemIndex, 1);

    // Calculate total cart count
    $cartCount = 0;
    $totalAmount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
        $totalAmount += $item['total_price'] ?? 0;
    }

    $response = [
        'success' => true,
        'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
        'cart_count' => $cartCount,
        'total_amount' => $totalAmount
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
