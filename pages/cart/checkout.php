<?php
/**
 * Checkout Page - Trang xác nhận đơn hàng
 * Hiển thị form xác nhận đơn hàng và thông tin thanh toán
 */

require_once '../../functions.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../auth/login.php?redirect=cart/checkout.php');
    exit;
}

// Get cart items from session
$cartItems = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cartItems)) {
    header('Location: index.php');
    exit;
}

// Get user info
$user = getCurrentUser();
$userFullName = getFullName($user['ho'] ?? '', $user['ten'] ?? '');
$userPhone = $user['phone'] ?? '';
$userEmail = $user['email'] ?? '';

// Get stores and payment methods
$stores = getStores();
$paymentMethods = getPaymentMethods();

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += isset($item['total_price']) ? (float)$item['total_price'] : 0;
}
$shippingFee = 30000; // Default shipping fee
$promotionDiscount = 0;
$totalAmount = $subtotal + $shippingFee - $promotionDiscount;

// Base path for assets
$basePath = '../../';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng - MeowTea Fresh</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/main.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/cart.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <main class="checkout-page">
        <div class="container">
            <h1 class="checkout-title">Xác nhận đơn hàng</h1>

            <div class="checkout-layout">
                <!-- Left Column: Order Details -->
                <div class="checkout-left">
                    <!-- Delivery Information -->
                    <section class="checkout-section">
                        <h2 class="section-title">Thông tin nhận hàng</h2>
                        <div class="delivery-info">
                            <div class="info-row">
                                <span class="info-label">Họ tên:</span>
                                <span class="info-value"><?php echo e($userFullName); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Số điện thoại:</span>
                                <span class="info-value"><?php echo e($userPhone); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Địa chỉ:</span>
                                <span class="info-value">Văn phòng</span>
                                <a href="#" class="change-address-link">Đổi địa chỉ</a>
                            </div>
                            <div class="info-row address-row">
                                <span class="info-value">54/31 Đ. Phổ Quang, Phường 2, Quận Tân Bình, Hồ Chí Minh</span>
                            </div>
                        </div>
                    </section>

                    <!-- Store Selection -->
                    <section class="checkout-section">
                        <h2 class="section-title">Giao từ cửa hàng</h2>
                        <select class="store-select" id="store-select" name="store_id" required>
                            <option value="">Chọn cửa hàng</option>
                            <?php foreach ($stores as $store): ?>
                                <option value="<?php echo $store['MaStore']; ?>" 
                                        data-phone="<?php echo e($store['DienThoai'] ?? ''); ?>"
                                        data-address="<?php echo e($store['DiaChi']); ?>">
                                    <?php echo e($store['TenStore']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="store-info" id="store-info" style="display: none;">
                            <p class="store-phone" id="store-phone"></p>
                            <p class="store-address" id="store-address"></p>
                        </div>
                    </section>

                    <!-- Order Notes -->
                    <section class="checkout-section">
                        <h2 class="section-title">Ghi chú đơn hàng</h2>
                        <textarea class="order-note-input" 
                                  id="order-note" 
                                  name="order_note" 
                                  placeholder="Nhập nội dung ghi chú cho đơn hàng (nếu có)" 
                                  maxlength="52"></textarea>
                        <span class="note-counter">0/52 ký tự</span>
                    </section>

                    <!-- VAT Invoice -->
                    <section class="checkout-section">
                        <label class="vat-checkbox-label">
                            <input type="checkbox" id="vat-invoice" name="vat_invoice">
                            <span>Tôi muốn xuất hóa đơn VAT</span>
                        </label>
                        <div class="vat-fields" id="vat-fields" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Email nhận hóa đơn *</label>
                                <input type="email" class="form-input" name="vat_email" placeholder="Nhập Email">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Mã số thuế *</label>
                                <div class="form-group-inline">
                                    <input type="text" class="form-input" name="vat_tax_id" placeholder="Nhập Mã số thuế">
                                    <button type="button" class="btn-lookup">Tra cứu</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tên công ty *</label>
                                <input type="text" class="form-input" name="vat_company" placeholder="Nhập tên công ty">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Địa chỉ công ty *</label>
                                <input type="text" class="form-input" name="vat_address" placeholder="Nhập địa chỉ công ty">
                            </div>
                        </div>
                    </section>

                    <!-- Payment Methods -->
                    <section class="checkout-section">
                        <h2 class="section-title">Phương thức thanh toán</h2>
                        <div class="payment-methods">
                            <?php 
                            $paymentIcons = [
                                'Ví MoMo' => 'momo',
                                'Ví ZaloPay' => 'zalopay',
                                'Thẻ tín dụng' => 'card',
                                'Chuyển khoản ngân hàng' => 'bank',
                                'Thanh toán qua ATM' => 'atm',
                                'Ví điện tử VNPAY' => 'vnpay'
                            ];
                            foreach ($paymentMethods as $index => $method): 
                                $methodName = $method['TenPayment'];
                                $isChecked = $index === 0;
                            ?>
                                <label class="payment-method-option">
                                    <input type="radio" 
                                           name="payment_method" 
                                           value="<?php echo $method['MaPayment']; ?>" 
                                           <?php echo $isChecked ? 'checked' : ''; ?>>
                                    <span class="payment-method-name"><?php echo e($methodName); ?></span>
                                    <?php if (isset($paymentIcons[$methodName])): ?>
                                        <span class="payment-icon payment-icon-<?php echo $paymentIcons[$methodName]; ?>"></span>
                                    <?php endif; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="checkout-right">
                    <div class="order-summary-card">
                        <div class="summary-header">
                            <h2 class="summary-title">Các món đã chọn</h2>
                            <a href="../menu/index.php" class="add-item-link">Thêm món</a>
                        </div>

                        <div class="summary-items">
                            <?php foreach ($cartItems as $index => $item): 
                                $productImage = !empty($item['product_image']) ? $item['product_image'] : 'assets/img/products/product_one.png';
                                $productImage = getImagePath($productImage);
                                $basePrice = isset($item['base_price']) ? (float)$item['base_price'] : 0;
                                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                                $itemTotal = isset($item['total_price']) ? (float)$item['total_price'] : $basePrice * $quantity;
                                $options = isset($item['options']) ? $item['options'] : [];
                            ?>
                                <div class="summary-item">
                                    <div class="summary-item-image">
                                        <img src="<?php echo e($basePath . $productImage); ?>" alt="<?php echo e($item['product_name']); ?>">
                                    </div>
                                    <div class="summary-item-info">
                                        <h3 class="summary-item-name"><?php echo e($item['product_name']); ?></h3>
                                        <?php if (!empty($options)): ?>
                                            <div class="summary-item-options">
                                                <?php foreach ($options as $option): ?>
                                                    <span class="option-tag-small">
                                                        <?php 
                                                        if (isset($option['group_name'])) {
                                                            echo e($option['group_name']) . ': ';
                                                        }
                                                        echo e($option['value_name'] ?? '');
                                                        ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="summary-item-price">
                                            <span class="current-price"><?php echo formatCurrency($basePrice); ?></span>
                                            <?php if ($basePrice < 45000): ?>
                                                <span class="old-price">45.000₫</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Promotion Code -->
                        <div class="promotion-section">
                            <label class="promotion-label">Khuyến mãi</label>
                            <input type="text" 
                                   class="promotion-input" 
                                   id="promotion-code" 
                                   placeholder="Nhập hoặc chọn mã khuyến mãi">
                        </div>

                        <!-- Payment Summary -->
                        <div class="payment-summary">
                            <div class="summary-row">
                                <span class="summary-label">Tạm tính</span>
                                <span class="summary-value" id="subtotal"><?php echo formatCurrency($subtotal); ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Phí vận chuyển</span>
                                <span class="summary-value" id="shipping-fee"><?php echo formatCurrency($shippingFee); ?></span>
                            </div>
                            <div class="summary-row promotion-row" id="promotion-row" style="display: none;">
                                <span class="summary-label">Khuyến mãi</span>
                                <span class="summary-value promotion-value" id="promotion-discount">-0₫</span>
                            </div>
                            <div class="summary-row total-row">
                                <span class="summary-label">Số tiền thanh toán</span>
                                <span class="summary-value total-value" id="total-amount"><?php echo formatCurrency($totalAmount); ?></span>
                            </div>
                        </div>

                        <!-- Terms Checkbox -->
                        <label class="terms-checkbox-label">
                            <input type="checkbox" id="agree-terms" required>
                            <span>Tôi đồng ý với những <a href="#" class="terms-link">điều khoản mua hàng</a> của MeowTea Fresh.</span>
                        </label>

                        <!-- Checkout Button -->
                        <button type="button" class="btn-pay-now" id="pay-now-btn">Thanh toán ngay</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="back-to-top">
        <a href="#" class="back-to-top-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 15l-6-6-6 6"/>
            </svg>
        </a>
        <span>Lên đầu trang</span>
    </div>

    <?php include '../../components/footer.php'; ?>

    <script src="<?php echo $basePath; ?>assets/js/main.js"></script>
    <script src="<?php echo $basePath; ?>assets/js/checkout.js"></script>
</body>
</html>
