<?php
/**
 * Management Page
 * Quản lý sản phẩm - chỉ dành cho Staff và Admin
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

// Check if user has Staff or Admin role
$userRole = $_SESSION['user_role_name'] ?? '';
if ($userRole !== 'Staff' && $userRole !== 'Admin') {
    header('Location: ../../index.php');
    exit;
}

$isAdmin = ($userRole === 'Admin');
$basePath = '../../';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - MeowTea Fresh</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/management.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <section class="management-section">
        <div class="container">
            <div class="management-header">
                <h1 class="page-title">Quản lý sản phẩm</h1>
                <?php if ($isAdmin): ?>
                    <button type="button" class="btn btn-primary" id="btn-add-product">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Thêm sản phẩm mới
                    </button>
                <?php endif; ?>
            </div>

            <div class="management-content">
                <!-- Products Accordion -->
                <div id="products-accordion" class="products-accordion">
                    <div class="loading-spinner">Đang tải...</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Product Modal (Admin only) -->
    <?php if ($isAdmin): ?>
    <div id="add-product-modal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm sản phẩm mới</h2>
                <button type="button" class="modal-close" id="close-add-modal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-product-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="product-name">Tên sản phẩm <span class="required">*</span></label>
                        <input type="text" id="product-name" name="ten_sp" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="product-category">Danh mục <span class="required">*</span></label>
                        <select id="product-category" name="ma_category" class="form-input" required>
                            <option value="">-- Chọn danh mục --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="product-price">Giá bán (₫) <span class="required">*</span></label>
                        <input type="number" id="product-price" name="gia_co_ban" class="form-input" min="0" step="1" required>
                    </div>
                    <div class="form-group">
                        <label for="product-image">Hình ảnh</label>
                        <input type="file" id="product-image" name="hinh_anh" class="form-input" accept="image/*">
                        <small class="form-help">Để trống để sử dụng hình ảnh mặc định. Chỉ chấp nhận file ảnh (JPG, PNG, GIF, etc.)</small>
                        <div id="image-preview" style="margin-top: 10px; display: none;">
                            <img id="preview-img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid var(--border-color);">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" id="cancel-add-product">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Edit Price Modal (Admin only) -->
    <?php if ($isAdmin): ?>
    <div id="edit-price-modal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Điều chỉnh giá bán</h2>
                <button type="button" class="modal-close" id="close-edit-modal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-price-form">
                    <input type="hidden" id="edit-product-id" name="product_id">
                    <div class="form-group">
                        <label for="edit-product-name">Tên sản phẩm</label>
                        <input type="text" id="edit-product-name" class="form-input" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit-product-price">Giá bán mới (₫) <span class="required">*</span></label>
                        <input type="number" id="edit-product-price" name="price" class="form-input" min="0" step="1" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" id="cancel-edit-price">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php include '../../components/footer.php'; ?>

    <script src="../../assets/js/management.js"></script>
</body>
</html>
