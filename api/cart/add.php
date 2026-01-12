<?php
/**
 * Add to Cart API
 * Thêm sản phẩm vào giỏ hàng
 * Tạm thời sử dụng session, sau sẽ tích hợp với database
 */

header('Content-Type: application/json');
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart in session if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['success' => false, 'message' => ''];

try {
    // Get POST data
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
    $optionsJson = isset($_POST['options']) ? $_POST['options'] : '[]';
    $options = json_decode($optionsJson, true) ?: [];
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
    $basePrice = isset($_POST['base_price']) ? (float)$_POST['base_price'] : 0;
    $totalPrice = isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0;

    if (!$productId) {
        throw new Exception('Product ID is required');
    }

    // Get product from database to validate
    $product = getProductById($productId);
    if (!$product) {
        throw new Exception('Product not found');
    }

    // Create cart item
    $cartItem = [
        'product_id' => $productId,
        'product_name' => $product['TenSP'],
        'product_image' => $product['HinhAnh'] ?? 'assets/img/products/product_one.png',
        'quantity' => $quantity,
        'base_price' => $basePrice,
        'total_price' => $totalPrice,
        'options' => $options,
        'note' => $note,
        'added_at' => date('Y-m-d H:i:s')
    ];

    // Add to cart (for now, just append - later can merge same items)
    $_SESSION['cart'][] = $cartItem;

    // Count total items
    $cartCount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }

    $response = [
        'success' => true,
        'message' => 'Đã thêm vào giỏ hàng',
        'cart_count' => $cartCount,
        'item' => $cartItem
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
