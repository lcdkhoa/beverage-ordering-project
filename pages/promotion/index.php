<?php
/**
 * Promotion Management Page
 * Quản lý khuyến mãi - chỉ dành cho Admin
 */

require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}

// Check if user has Admin role
$userRole = $_SESSION['user_role_name'] ?? '';
if ($userRole !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

$basePath = '../../';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khuyến mãi - MeowTea Fresh</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/management.css">
    <link rel="stylesheet" href="../../assets/css/promotion.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <section class="management-section">
        <div class="container">
            <div class="management-header">
                <h1 class="page-title">Quản lý khuyến mãi</h1>
                <button type="button" class="btn btn-primary" id="btn-add-promotion">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Thêm khuyến mãi mới
                </button>
            </div>

            <div class="management-content">
                <!-- Promotions Table -->
                <div id="promotions-table-wrapper" class="promotions-table-wrapper">
                    <div class="loading-spinner">Đang tải...</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Promotion Modal -->
    <div id="add-promotion-modal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm khuyến mãi mới</h2>
                <button type="button" class="modal-close" id="close-add-modal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-promotion-form">
                    <div class="form-group">
                        <label for="promotion-code">Mã khuyến mãi <span class="required">*</span></label>
                        <input type="text" id="promotion-code" name="code" class="form-input" required placeholder="VD: SALE2024">
                    </div>
                    <div class="form-group">
                        <label for="promotion-loai-giam-gia">Loại giảm giá <span class="required">*</span></label>
                        <select id="promotion-loai-giam-gia" name="loai_giam_gia" class="form-input" required>
                            <option value="Percentage">Phần trăm (%)</option>
                            <option value="Fixed">Số tiền cố định (₫)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="promotion-gia-tri">Giá trị giảm giá <span class="required">*</span></label>
                        <input type="number" id="promotion-gia-tri" name="gia_tri" class="form-input" min="0" step="0.01" required>
                        <small class="form-help">Nhập phần trăm (0-100) hoặc số tiền (₫)</small>
                    </div>
                    <div class="form-group">
                        <label for="promotion-ngay-bat-dau">Ngày bắt đầu</label>
                        <input type="datetime-local" id="promotion-ngay-bat-dau" name="ngay_bat_dau" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="promotion-ngay-ket-thuc">Ngày kết thúc</label>
                        <input type="datetime-local" id="promotion-ngay-ket-thuc" name="ngay_ket_thuc" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="promotion-trang-thai">Trạng thái</label>
                        <select id="promotion-trang-thai" name="trang_thai" class="form-input">
                            <option value="1">Kích hoạt</option>
                            <option value="0">Vô hiệu hóa</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" id="cancel-add-promotion">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm khuyến mãi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Promotion Modal -->
    <div id="edit-promotion-modal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Cập nhật khuyến mãi</h2>
                <button type="button" class="modal-close" id="close-edit-modal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-promotion-form">
                    <input type="hidden" id="edit-promotion-id" name="promotion_id">
                    <div class="form-group">
                        <label for="edit-promotion-code">Mã khuyến mãi <span class="required">*</span></label>
                        <input type="text" id="edit-promotion-code" name="code" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-promotion-loai-giam-gia">Loại giảm giá <span class="required">*</span></label>
                        <select id="edit-promotion-loai-giam-gia" name="loai_giam_gia" class="form-input" required>
                            <option value="Percentage">Phần trăm (%)</option>
                            <option value="Fixed">Số tiền cố định (₫)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-promotion-gia-tri">Giá trị giảm giá <span class="required">*</span></label>
                        <input type="number" id="edit-promotion-gia-tri" name="gia_tri" class="form-input" min="0" step="0.01" required>
                        <small class="form-help">Nhập phần trăm (0-100) hoặc số tiền (₫)</small>
                    </div>
                    <div class="form-group">
                        <label for="edit-promotion-ngay-bat-dau">Ngày bắt đầu</label>
                        <input type="datetime-local" id="edit-promotion-ngay-bat-dau" name="ngay_bat_dau" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="edit-promotion-ngay-ket-thuc">Ngày kết thúc</label>
                        <input type="datetime-local" id="edit-promotion-ngay-ket-thuc" name="ngay_ket_thuc" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="edit-promotion-trang-thai">Trạng thái</label>
                        <select id="edit-promotion-trang-thai" name="trang_thai" class="form-input">
                            <option value="1">Kích hoạt</option>
                            <option value="0">Vô hiệu hóa</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" id="cancel-edit-promotion">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật khuyến mãi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../components/footer.php'; ?>

    <script src="../../assets/js/promotion.js"></script>
</body>
</html>
