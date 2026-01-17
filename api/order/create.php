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
    $promotionCode = isset($_POST['promotion_code']) ? trim($_POST['promotion_code']) : '';
    $promotionId = isset($_POST['promotion_id']) ? (int)$_POST['promotion_id'] : 0;
    $promotionDiscount = isset($_POST['promotion_discount']) ? (float)$_POST['promotion_discount'] : 0;

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
    
    // Get delivery address (for now, use default)
    $deliveryAddress = "54/31 Đ. Phổ Quang, Phường 2, Quận Tân Bình, Hồ Chí Minh";

    // Get database connection
    $pdo = getDBConnection();
    
    // Validate promotion if provided
    if (!empty($promotionCode) && $promotionId > 0) {
        $sql = "SELECT * FROM Promotion 
                WHERE MaPromotion = ? AND Code = ? AND TrangThai = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$promotionId, $promotionCode]);
        $promotion = $stmt->fetch();
        
        if ($promotion) {
            // Check date validity
            $now = new DateTime();
            $isValid = true;
            
            if (!empty($promotion['NgayBatDau'])) {
                $startDate = new DateTime($promotion['NgayBatDau']);
                if ($now < $startDate) {
                    $isValid = false;
                }
            }
            
            if (!empty($promotion['NgayKetThuc'])) {
                $endDate = new DateTime($promotion['NgayKetThuc']);
                if ($now > $endDate) {
                    $isValid = false;
                }
            }
            
            if (!$isValid) {
                // Promotion is invalid, reset discount
                $promotionDiscount = 0;
                $promotionCode = '';
                $promotionId = 0;
            } else {
                // Recalculate discount to ensure it's correct
                $loaiGiamGia = $promotion['LoaiGiamGia'] ?? 'Percentage';
                $giaTri = (float)$promotion['GiaTri'];
                $giaTriToiDa = isset($promotion['GiaTriToiDa']) && $promotion['GiaTriToiDa'] !== null ? (float)$promotion['GiaTriToiDa'] : null;
                
                if ($loaiGiamGia === 'Percentage') {
                    $promotionDiscount = ($subtotal * $giaTri) / 100;
                    
                    // Apply maximum value limit if set
                    if ($giaTriToiDa !== null && $giaTriToiDa > 0) {
                        if ($promotionDiscount > $giaTriToiDa) {
                            $promotionDiscount = $giaTriToiDa;
                        }
                    }
                    
                    if ($promotionDiscount > $subtotal) {
                        $promotionDiscount = $subtotal;
                    }
                } else {
                    $promotionDiscount = $giaTri;
                    if ($promotionDiscount > $subtotal) {
                        $promotionDiscount = $subtotal;
                    }
                }
            }
        } else {
            // Promotion not found, reset discount
            $promotionDiscount = 0;
            $promotionCode = '';
            $promotionId = 0;
        }
    } else {
        // No promotion provided
        $promotionDiscount = 0;
    }
    
    $totalAmount = $subtotal + $shippingFee - $promotionDiscount;

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Create order (Note: MaPayment is not in schema, so we store it in session)
        // Set status to 'Payment_Received' when order is created (đã nhận thanh toán)
        $sql = "INSERT INTO Orders (MaUser, MaStore, DiaChiGiao, PhiVanChuyen, MaPromotion, GiamGia, TongTien, TrangThai) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Payment_Received')";
        $stmt = $pdo->prepare($sql);
        $promotionIdForDB = ($promotionId > 0 && !empty($promotionCode)) ? $promotionId : null;
        $stmt->execute([$userId, $storeId, $deliveryAddress, $shippingFee, $promotionIdForDB, $promotionDiscount, $totalAmount]);
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
