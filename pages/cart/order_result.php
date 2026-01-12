<?php
/**
 * Order Result Page - Trang kết quả đơn hàng
 * Hiển thị thông tin đơn hàng sau khi đặt hàng thành công
 */

require_once '../../functions.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

// Get order ID from query string
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$orderId) {
    header('Location: index.php');
    exit;
}

// Get user info
$user = getCurrentUser();
$userId = $user['id'];

// Get order from database
$pdo = getDBConnection();
$sql = "SELECT o.*, s.TenStore, s.DiaChi as StoreAddress, s.DienThoai as StorePhone
        FROM Orders o
        INNER JOIN Store s ON o.MaStore = s.MaStore
        WHERE o.MaOrder = ? AND o.MaUser = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch();

// Get payment method from session or default
$paymentMethodId = $_SESSION['order_payment_' . $orderId] ?? null;
$paymentMethodName = 'Ví Zalo Pay'; // Default
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

if (!$order) {
    header('Location: index.php');
    exit;
}

// Get order items
$sql = "SELECT oi.*, sp.TenSP, sp.HinhAnh
        FROM Order_Item oi
        INNER JOIN SanPham sp ON oi.MaSP = sp.MaSP
        WHERE oi.MaOrder = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll();

// Get order item options
foreach ($orderItems as &$item) {
    $sql = "SELECT oio.*, ov.TenGiaTri, og.TenNhom
            FROM Order_Item_Option oio
            INNER JOIN Option_Value ov ON oio.MaOptionValue = ov.MaOptionValue
            INNER JOIN Option_Group og ON ov.MaOptionGroup = og.MaOptionGroup
            WHERE oio.MaOrderItem = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$item['MaOrderItem']]);
    $item['options'] = $stmt->fetchAll();
}

// Calculate totals
$subtotal = 0;
foreach ($orderItems as $item) {
    $subtotal += ($item['GiaCoBan'] * $item['SoLuong']);
    if (isset($item['options'])) {
        foreach ($item['options'] as $option) {
            $subtotal += ($option['GiaThem'] * $item['SoLuong']);
        }
    }
}

$shippingFee = $order['PhiVanChuyen'] ?? 0;
$totalAmount = $order['TongTien'] ?? 0;
$promotionDiscount = $totalAmount - $subtotal - $shippingFee;

// Generate order code
$orderCode = 'AHDW' . str_pad($orderId, 3, '0', STR_PAD_LEFT);

// Estimated delivery time (1 hour from now)
$estimatedDelivery = date('H:i d/m/Y', strtotime('+1 hour'));

// Base path for assets
$basePath = '../../';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - MeowTea Fresh</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/cart.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <main class="order-result-page">
        <div class="container">
            <!-- Success Message -->
            <div class="order-success-header">
                <h1 class="success-title">Đặt Hàng Thành Công</h1>
                <p class="order-code-label">Mã đơn hàng của bạn</p>
                <div class="order-code-badge">#<?php echo e($orderCode); ?></div>
                <p class="email-notice">
                    Vui lòng kiểm tra hộp thư đến trong email của bạn để xem thông tin chi tiết đơn hàng
                </p>
                <p class="email-address"><?php echo e($user['email'] ?? 'email@gmail.com'); ?></p>
            </div>

            <!-- Order Progress Tracker -->
            <div class="order-progress">
                <div class="progress-step completed">
                    <div class="step-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                    </div>
                    <div class="step-info">
                        <p class="step-title">Đã nhận thanh toán</p>
                        <p class="step-time"><?php echo date('H:i'); ?></p>
                    </div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="step-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="step-info">
                        <p class="step-title">Đã nhận đơn</p>
                        <p class="step-time">-</p>
                    </div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="step-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <div class="step-info">
                        <p class="step-title">Đang vận chuyển</p>
                        <p class="step-time">-</p>
                    </div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="step-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="step-info">
                        <p class="step-title">Hoàn thành</p>
                        <p class="step-time">-</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary-section">
                <h2 class="summary-section-title">Tóm tắt đơn hàng</h2>
                <div class="summary-details">
                    <div class="summary-detail-row">
                        <span class="detail-label">Thời gian giao hàng dự kiến</span>
                        <span class="detail-value"><?php echo e($estimatedDelivery); ?></span>
                    </div>
                    <div class="summary-detail-row">
                        <span class="detail-label">Phương thức thanh toán</span>
                        <span class="detail-value"><?php echo e($order['PaymentMethod'] ?? 'Ví Zalo Pay'); ?></span>
                    </div>
                    <div class="summary-detail-row">
                        <span class="detail-label">Thành tiền</span>
                        <span class="detail-value"><?php echo formatCurrency($subtotal); ?></span>
                    </div>
                    <div class="summary-detail-row">
                        <span class="detail-label">Phí vận chuyển</span>
                        <span class="detail-value"><?php echo formatCurrency($shippingFee); ?></span>
                    </div>
                    <?php if ($promotionDiscount > 0): ?>
                    <div class="summary-detail-row">
                        <span class="detail-label">Khuyến mãi</span>
                        <span class="detail-value promotion-value">-<?php echo formatCurrency($promotionDiscount); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="summary-detail-row total-row">
                        <span class="detail-label">Số tiền thanh toán</span>
                        <span class="detail-value total-value"><?php echo formatCurrency($totalAmount); ?></span>
                    </div>
                </div>
            </div>

            <!-- Support Contact -->
            <div class="support-section">
                <p class="support-text">
                    Bạn cần hỗ trợ về đơn hàng? Vui lòng liên hệ:
                </p>
                <p class="support-phone">(028) 6868 6868</p>
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
</body>
</html>
