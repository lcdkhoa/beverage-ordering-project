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

                        <div class="profile-info-card">
                            <div class="info-row">
                                <div class="info-label">Tên đăng nhập</div>
                                <div class="info-value"><?php echo e($username); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Họ</div>
                                <div class="info-value"><?php echo e($userHo); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Tên</div>
                                <div class="info-value"><?php echo e($userTen); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Giới tính</div>
                                <div class="info-value">
                                    <?php
                                    $gioiTinhText = '';
                                    if ($userGioiTinh === 'M') {
                                        $gioiTinhText = 'Nam';
                                    } elseif ($userGioiTinh === 'F') {
                                        $gioiTinhText = 'Nữ';
                                    } elseif ($userGioiTinh === 'O') {
                                        $gioiTinhText = 'Khác';
                                    } else {
                                        $gioiTinhText = 'Chưa cập nhật';
                                    }
                                    echo e($gioiTinhText);
                                    ?>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?php echo e($userEmail ?: 'Chưa cập nhật'); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Số điện thoại</div>
                                <div class="info-value"><?php echo e($userPhone ?: 'Chưa cập nhật'); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Vai trò</div>
                                <div class="info-value"><?php echo e($userRole); ?></div>
                            </div>
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
                                <a href="<?php echo $basePath; ?>pages/menu/index.php" class="btn-primary">Đặt hàng ngay</a>
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

    <?php include '../../components/footer.php'; ?>

    <script>
    $(document).ready(function() {
        // Tab switching
        $('.profile-nav-item').on('click', function(e) {
            e.preventDefault();
            const tab = $(this).data('tab');
            
            // Update nav active state
            $('.profile-nav-item').removeClass('active');
            $(this).addClass('active');
            
            // Update tab content
            $('.profile-tab').removeClass('active');
            if (tab === 'info') {
                $('#infoTab').addClass('active');
            } else if (tab === 'orders') {
                $('#ordersTab').addClass('active');
                loadOrders();
            } else if (tab === 'password') {
                $('#passwordTab').addClass('active');
            }
        });

        // Password toggle visibility
        function setupPasswordToggle(toggleId, inputId) {
            $(toggleId).on('click', function() {
                const passwordInput = $(inputId);
                const hiddenIcon = $(this).find('.eye-icon-hidden');
                const visibleIcon = $(this).find('.eye-icon-visible');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    hiddenIcon.hide();
                    visibleIcon.show();
                } else {
                    passwordInput.attr('type', 'password');
                    hiddenIcon.show();
                    visibleIcon.hide();
                }
            });
        }

        setupPasswordToggle('#currentPasswordToggle', '#current_password');
        setupPasswordToggle('#newPasswordToggle', '#new_password');
        setupPasswordToggle('#confirmPasswordToggle', '#confirm_password');

        // Real-time password match validation
        $('#confirm_password').on('input', function() {
            const newPassword = $('#new_password').val();
            const confirmPassword = $(this).val();
            const $input = $(this);
            
            if (confirmPassword.length > 0) {
                if (newPassword !== confirmPassword) {
                    $input[0].setCustomValidity('Mật khẩu xác nhận không khớp');
                } else {
                    $input[0].setCustomValidity('');
                }
            } else {
                $input[0].setCustomValidity('');
            }
        });

        // Change password form submit
        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $btn = $('#changePasswordBtn');
            const $btnText = $btn.find('.btn-text');
            const $btnLoading = $btn.find('.btn-loading');
            const $message = $('#changePasswordMessage');
            
            // Reset message
            $message.hide().removeClass('success error').text('');
            
            // Validate password match
            const newPassword = $('#new_password').val();
            const confirmPassword = $('#confirm_password').val();
            
            if (newPassword !== confirmPassword) {
                $message.addClass('error').text('Mật khẩu xác nhận không khớp').show();
                return;
            }
            
            // Disable button and show loading
            $btn.prop('disabled', true);
            $btnText.hide();
            $btnLoading.show();
            
            // Get form data
            const formData = {
                current_password: $('#current_password').val(),
                new_password: newPassword,
                confirm_password: confirmPassword
            };
            
            // AJAX request
            $.ajax({
                url: '../../api/auth/change-password.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $message.addClass('success').text(response.message || 'Đổi mật khẩu thành công!').show();
                        
                        // Clear form
                        $form[0].reset();
                        
                        // Re-enable button after 2 seconds
                        setTimeout(function() {
                            $btn.prop('disabled', false);
                            $btnText.show();
                            $btnLoading.hide();
                        }, 2000);
                    } else {
                        // Show error message
                        $message.addClass('error').text(response.message || 'Đổi mật khẩu thất bại. Vui lòng thử lại.').show();
                        
                        // Re-enable button
                        $btn.prop('disabled', false);
                        $btnText.show();
                        $btnLoading.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Change password error:', error);
                    let errorMessage = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
                    
                    // Try to parse error response
                    if (xhr.responseText) {
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                        } catch (e) {
                            // Use default error message
                        }
                    }
                    
                    $message.addClass('error').text(errorMessage).show();
                    
                    // Re-enable button
                    $btn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                }
            });
        });

        // Load orders function
        function loadOrders() {
            const $loading = $('#ordersLoading');
            const $empty = $('#ordersEmpty');
            const $list = $('#ordersList');
            
            $loading.show();
            $empty.hide();
            $list.hide();
            
            $.ajax({
                url: '../../api/order/get.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    $loading.hide();
                    
                    if (response.success && response.orders && response.orders.length > 0) {
                        renderOrders(response.orders);
                        $list.show();
                    } else {
                        $empty.show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Load orders error:', error);
                    $loading.hide();
                    $empty.show();
                }
            });
        }

        // Render orders list
        function renderOrders(orders) {
            const $list = $('#ordersList');
            $list.empty();
            
            orders.forEach(function(order) {
                const orderHtml = `
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-header-left">
                                <h3 class="order-code">Mã đơn: #${order.OrderCode}</h3>
                                <p class="order-date">Ngày đặt: ${order.NgayTaoFormatted}</p>
                            </div>
                            <div class="order-header-right">
                                <span class="order-status status-${getStatusClass(order.TrangThai)}">${getStatusText(order.TrangThai)}</span>
                            </div>
                        </div>
                        <div class="order-body">
                            <div class="order-store">
                                <strong>Cửa hàng:</strong> ${escapeHtml(order.TenStore)}
                            </div>
                            <div class="order-items">
                                ${renderOrderItems(order.items)}
                            </div>
                            <div class="order-summary">
                                <div class="order-summary-row">
                                    <span>Tạm tính:</span>
                                    <span>${formatCurrency(calculateSubtotal(order.items))}</span>
                                </div>
                                <div class="order-summary-row">
                                    <span>Phí vận chuyển:</span>
                                    <span>${formatCurrency(order.PhiVanChuyen || 0)}</span>
                                </div>
                                <div class="order-summary-row order-total">
                                    <span>Tổng tiền:</span>
                                    <span>${formatCurrency(order.TongTien)}</span>
                                </div>
                            </div>
                            <div class="order-footer">
                                <div class="order-payment">
                                    <strong>Phương thức thanh toán:</strong> ${escapeHtml(order.PaymentMethod)}
                                </div>
                                <div class="order-address">
                                    <strong>Địa chỉ giao hàng:</strong> ${escapeHtml(order.DiaChiGiao)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $list.append(orderHtml);
            });
        }

        // Render order items
        function renderOrderItems(items) {
            if (!items || items.length === 0) {
                return '<p class="no-items">Không có sản phẩm</p>';
            }
            
            let html = '<div class="order-items-list">';
            items.forEach(function(item) {
                const optionsHtml = item.options && item.options.length > 0 
                    ? '<div class="item-options">' + item.options.map(function(opt) {
                        return `<span class="item-option">${escapeHtml(opt.TenNhom)}: ${escapeHtml(opt.TenGiaTri)}</span>`;
                    }).join(', ') + '</div>'
                    : '';
                
                html += `
                    <div class="order-item">
                        <div class="item-info">
                            <span class="item-name">${escapeHtml(item.TenSP)}</span>
                            ${optionsHtml}
                            <span class="item-quantity">x${item.SoLuong}</span>
                        </div>
                        <div class="item-price">${formatCurrency(item.ItemTotal || (item.GiaCoBan * item.SoLuong))}</div>
                    </div>
                `;
            });
            html += '</div>';
            return html;
        }

        // Calculate subtotal from items
        function calculateSubtotal(items) {
            if (!items || items.length === 0) return 0;
            let subtotal = 0;
            items.forEach(function(item) {
                subtotal += (item.ItemTotal || (item.GiaCoBan * item.SoLuong));
            });
            return subtotal;
        }

        // Get status class for styling
        function getStatusClass(status) {
            const statusLower = (status || '').toLowerCase();
            if (statusLower === 'completed' || statusLower === 'hoàn thành') {
                return 'completed';
            } else if (statusLower === 'pending' || statusLower === 'chờ xử lý') {
                return 'pending';
            } else if (statusLower === 'processing' || statusLower === 'đang xử lý') {
                return 'processing';
            } else if (statusLower === 'cancelled' || statusLower === 'đã hủy') {
                return 'cancelled';
            }
            return 'default';
        }

        // Get status text in Vietnamese
        function getStatusText(status) {
            const statusLower = (status || '').toLowerCase();
            const statusMap = {
                'pending': 'Chờ xử lý',
                'processing': 'Đang xử lý',
                'completed': 'Hoàn thành',
                'cancelled': 'Đã hủy',
                'delivering': 'Đang giao hàng'
            };
            return statusMap[statusLower] || status || 'Chờ xử lý';
        }

        // Format currency (consistent with PHP formatCurrency)
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
        }

        // Escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return (text || '').replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    });
    </script>
</body>
</html>
