<?php
/**
 * Profile Page
 * Trang thông tin tài khoản và đổi mật khẩu
 */

require_once '../../functions.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../pages/auth/login.php');
    exit;
}

// Get user data from session
$userId = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? '';
$userHo = $_SESSION['user_ho'] ?? '';
$userTen = $_SESSION['user_ten'] ?? '';
$userName = $_SESSION['user_name'] ?? '';
$userGioiTinh = $_SESSION['user_gioi_tinh'] ?? null;
$userEmail = $_SESSION['user_email'] ?? '';
$userPhone = $_SESSION['user_phone'] ?? '';
$userRole = $_SESSION['user_role_name'] ?? '';

// Calculate base path
$basePath = '../../';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản - MeowTea Fresh</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/main.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/profile.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <!-- Profile Section -->
    <section class="profile-section">
        <div class="container">
            <div class="profile-layout">
                <!-- Profile Sidebar -->
                <div class="profile-sidebar">
                    <div class="profile-avatar-card">
                        <div class="profile-avatar">
                            <?php
                            $avatarImagePath = getAvatarImagePath($userGioiTinh, $basePath);
                            if (!empty($avatarImagePath)): ?>
                                <img src="<?php echo e($avatarImagePath); ?>" alt="<?php echo e($userName); ?>" class="avatar-image-large">
                            <?php else: ?>
                                <span class="avatar-initial-large"><?php echo e(getAvatarInitialFromName($userHo, $userTen)); ?></span>
                            <?php endif; ?>
                        </div>
                        <h2 class="profile-name"><?php echo e($userName); ?></h2>
                        <p class="profile-username">@<?php echo e($username); ?></p>
                        <p class="profile-role"><?php echo e($userRole); ?></p>
                    </div>

                    <nav class="profile-nav">
                        <a href="#info" class="profile-nav-item active" data-tab="info">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>Thông tin cá nhân</span>
                        </a>
                        <a href="#orders" class="profile-nav-item" data-tab="orders">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Đơn hàng</span>
                        </a>
                        <a href="#password" class="profile-nav-item" data-tab="password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span>Đổi mật khẩu</span>
                        </a>
                    </nav>
                </div>

                <!-- Profile Content -->
                <div class="profile-content">
                    <!-- Personal Info Tab -->
                    <div id="infoTab" class="profile-tab active">
                        <div class="profile-tab-header">
                            <h1 class="profile-tab-title">Thông tin cá nhân</h1>
                            <p class="profile-tab-subtitle">Xem và quản lý thông tin tài khoản của bạn</p>
                        </div>

                        <div class="profile-form-card">
                            <form id="updateProfileForm" class="profile-form" method="POST">
                                <!-- Username (Read-only) -->
                                <div class="form-group">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input 
                                        type="text" 
                                        id="username" 
                                        name="username" 
                                        class="form-input" 
                                        value="<?php echo e($username); ?>"
                                        readonly
                                        disabled
                                    >
                                </div>

                                <!-- Ho (Read-only) -->
                                <div class="form-group">
                                    <label for="ho" class="form-label">Họ</label>
                                    <input 
                                        type="text" 
                                        id="ho" 
                                        name="ho" 
                                        class="form-input" 
                                        value="<?php echo e($userHo); ?>"
                                        readonly
                                        disabled
                                    >
                                </div>

                                <!-- Ten (Read-only) -->
                                <div class="form-group">
                                    <label for="ten" class="form-label">Tên</label>
                                    <input 
                                        type="text" 
                                        id="ten" 
                                        name="ten" 
                                        class="form-input" 
                                        value="<?php echo e($userTen); ?>"
                                        readonly
                                        disabled
                                    >
                                </div>

                                <!-- GioiTinh (Editable) -->
                                <div class="form-group">
                                    <label for="gioi_tinh" class="form-label">Giới tính</label>
                                    <select 
                                        id="gioi_tinh" 
                                        name="gioi_tinh" 
                                        class="form-input dropdown-select"
                                    >
                                        <option value="">-- Chọn giới tính --</option>
                                        <option value="M" <?php echo ($userGioiTinh === 'M') ? 'selected' : ''; ?>>Nam</option>
                                        <option value="F" <?php echo ($userGioiTinh === 'F') ? 'selected' : ''; ?>>Nữ</option>
                                        <option value="O" <?php echo ($userGioiTinh === 'O') ? 'selected' : ''; ?>>Khác</option>
                                    </select>
                                </div>

                                <!-- Email (Editable) -->
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        class="form-input" 
                                        placeholder="Nhập email"
                                        value="<?php echo e($userEmail); ?>"
                                        maxlength="100"
                                        autocomplete="email"
                                    >
                                </div>

                                <!-- DienThoai (Editable) -->
                                <div class="form-group">
                                    <label for="dien_thoai" class="form-label">Số điện thoại</label>
                                    <input 
                                        type="tel" 
                                        id="dien_thoai" 
                                        name="dien_thoai" 
                                        class="form-input" 
                                        placeholder="Nhập số điện thoại"
                                        value="<?php echo e($userPhone); ?>"
                                        maxlength="20"
                                        autocomplete="tel"
                                    >
                                </div>

                                <!-- Role (Read-only) -->
                                <div class="form-group">
                                    <label for="role" class="form-label">Vai trò</label>
                                    <input 
                                        type="text" 
                                        id="role" 
                                        name="role" 
                                        class="form-input" 
                                        value="<?php echo e($userRole); ?>"
                                        readonly
                                        disabled
                                    >
                                </div>

                                <!-- Error/Success Message -->
                                <div id="updateProfileMessage" class="login-message" style="display: none;"></div>

                                <!-- Submit Button -->
                                <button type="submit" class="login-btn" id="updateProfileBtn">
                                    <span class="btn-text">Cập nhật thông tin</span>
                                    <span class="btn-loading" style="display: none;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                                            <path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="0.75"/>
                                        </svg>
                                        Đang xử lý...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div id="ordersTab" class="profile-tab">
                        <div class="profile-tab-header">
                            <h1 class="profile-tab-title">Đơn hàng của tôi</h1>
                            <p class="profile-tab-subtitle">Xem lịch sử đơn hàng và trạng thái đơn hàng của bạn</p>
                        </div>

                        <div class="orders-container">
                            <div id="ordersLoading" class="orders-loading" style="display: none;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                                    <path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="0.75"/>
                                </svg>
                                <p>Đang tải đơn hàng...</p>
                            </div>
                            <div id="ordersEmpty" class="orders-empty" style="display: none;">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                                </svg>
                                <p>Bạn chưa có đơn hàng nào</p>
                                <?php 
                                    $text = 'Đặt hàng ngay';
                                    $type = 'primary';
                                    $href = $basePath . 'pages/menu/index.php';
                                    $width = '200px';
                                    include '../../components/button.php';
                                ?>
                            </div>
                            <div id="ordersList" class="orders-list"></div>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div id="passwordTab" class="profile-tab">
                        <div class="profile-tab-header">
                            <h1 class="profile-tab-title">Đổi mật khẩu</h1>
                            <p class="profile-tab-subtitle">Thay đổi mật khẩu để bảo vệ tài khoản của bạn</p>
                        </div>

                        <div class="profile-form-card">
                            <form id="changePasswordForm" class="profile-form" method="POST">
                                <!-- Current Password Field -->
                                <div class="form-group">
                                    <label for="current_password" class="form-label">Mật khẩu hiện tại <span class="required">*</span></label>
                                    <div class="password-input-wrapper">
                                        <input 
                                            type="password" 
                                            id="current_password" 
                                            name="current_password" 
                                            class="form-input" 
                                            placeholder="Nhập mật khẩu hiện tại"
                                            required
                                            autocomplete="current-password"
                                        >
                                        <button 
                                            type="button" 
                                            class="password-toggle" 
                                            id="currentPasswordToggle"
                                            aria-label="Hiển thị mật khẩu"
                                        >
                                            <svg class="eye-icon eye-icon-hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <svg class="eye-icon eye-icon-visible" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                                <line x1="1" y1="1" x2="23" y2="23"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- New Password Field -->
                                <div class="form-group">
                                    <label for="new_password" class="form-label">Mật khẩu mới <span class="required">*</span></label>
                                    <div class="password-input-wrapper">
                                        <input 
                                            type="password" 
                                            id="new_password" 
                                            name="new_password" 
                                            class="form-input" 
                                            placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                                            required
                                            autocomplete="new-password"
                                            minlength="6"
                                        >
                                        <button 
                                            type="button" 
                                            class="password-toggle" 
                                            id="newPasswordToggle"
                                            aria-label="Hiển thị mật khẩu"
                                        >
                                            <svg class="eye-icon eye-icon-hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <svg class="eye-icon eye-icon-visible" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                                <line x1="1" y1="1" x2="23" y2="23"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <small class="form-hint">Mật khẩu mới phải có ít nhất 6 ký tự và khác mật khẩu hiện tại</small>
                                </div>

                                <!-- Confirm Password Field -->
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới <span class="required">*</span></label>
                                    <div class="password-input-wrapper">
                                        <input 
                                            type="password" 
                                            id="confirm_password" 
                                            name="confirm_password" 
                                            class="form-input" 
                                            placeholder="Nhập lại mật khẩu mới"
                                            required
                                            autocomplete="new-password"
                                            minlength="6"
                                        >
                                        <button 
                                            type="button" 
                                            class="password-toggle" 
                                            id="confirmPasswordToggle"
                                            aria-label="Hiển thị mật khẩu"
                                        >
                                            <svg class="eye-icon eye-icon-hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <svg class="eye-icon eye-icon-visible" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                                <line x1="1" y1="1" x2="23" y2="23"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Error/Success Message -->
                                <div id="changePasswordMessage" class="login-message" style="display: none;"></div>

                                <!-- Submit Button -->
                                <button type="submit" class="login-btn" id="changePasswordBtn">
                                    <span class="btn-text">Đổi mật khẩu</span>
                                    <span class="btn-loading" style="display: none;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                                            <path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="0.75"/>
                                        </svg>
                                        Đang xử lý...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php 
        $bgType = 'light-green';
        include '../../components/back-to-top.php';
    ?>

    <?php include '../../components/footer.php'; ?>

    <script src="<?php echo $basePath; ?>assets/js/common.js"></script>
    <script src="<?php echo $basePath; ?>assets/js/profile.js"></script>
</body>
</html>
