<?php
/**
 * Register Page
 * Trang đăng ký người dùng mới
 */

require_once '../../functions.php';

// Redirect if already logged in
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: ../../index.php');
    exit;
}

// Calculate base path
$basePath = '../../';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - MeowTea Fresh</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/main.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <!-- Register Section -->
    <section class="login-section">
        <div class="container">
            <div class="login-layout">
                <!-- Left: Image -->
                <div class="login-image-wrapper">
                    <img src="<?php echo $basePath; ?>assets/img/stores/home-page.png" alt="MeowTea Fresh Cafe" class="login-image">
                </div>

                <!-- Right: Register Form -->
                <div class="login-form-wrapper">
                    <div class="login-form-container">
                        <h1 class="login-title">Tạo tài khoản mới</h1>
                        <p class="login-subtitle">Đăng ký để nhận nhiều ưu đãi hấp dẫn từ MeowTea Fresh!</p>

                        <form id="registerForm" class="login-form" method="POST">
                            <!-- Username Field -->
                            <div class="form-group">
                                <label for="username" class="form-label">Tên đăng nhập <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    id="username" 
                                    name="username" 
                                    class="form-input" 
                                    placeholder="Nhập tên đăng nhập (3-100 ký tự, chỉ chữ, số và _)"
                                    required
                                    autocomplete="username"
                                    minlength="3"
                                    maxlength="100"
                                    pattern="[a-zA-Z0-9_]+"
                                >
                                <small class="form-hint">Chỉ được chứa chữ cái, số và dấu gạch dưới</small>
                            </div>

                            <!-- Password Field -->
                            <div class="form-group">
                                <label for="password" class="form-label">Mật khẩu <span class="required">*</span></label>
                                <div class="password-input-wrapper">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        class="form-input" 
                                        placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
                                        required
                                        autocomplete="new-password"
                                        minlength="6"
                                    >
                                    <button 
                                        type="button" 
                                        class="password-toggle" 
                                        id="passwordToggle"
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

                            <!-- Confirm Password Field -->
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="required">*</span></label>
                                <div class="password-input-wrapper">
                                    <input 
                                        type="password" 
                                        id="confirm_password" 
                                        name="confirm_password" 
                                        class="form-input" 
                                        placeholder="Nhập lại mật khẩu"
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

                            <!-- Ho (Last Name) Field -->
                            <div class="form-group">
                                <label for="ho" class="form-label">Họ <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    id="ho" 
                                    name="ho" 
                                    class="form-input" 
                                    placeholder="Nhập họ"
                                    required
                                    maxlength="50"
                                    autocomplete="family-name"
                                >
                            </div>

                            <!-- Ten (First Name) Field -->
                            <div class="form-group">
                                <label for="ten" class="form-label">Tên <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    id="ten" 
                                    name="ten" 
                                    class="form-input" 
                                    placeholder="Nhập tên"
                                    required
                                    maxlength="50"
                                    autocomplete="given-name"
                                >
                            </div>

                            <!-- GioiTinh (Gender) Field -->
                            <div class="form-group">
                                <label for="gioi_tinh" class="form-label">Giới tính</label>
                                <select 
                                    id="gioi_tinh" 
                                    name="gioi_tinh" 
                                    class="form-input"
                                >
                                    <option value="">-- Chọn giới tính --</option>
                                    <option value="M">Nam</option>
                                    <option value="F">Nữ</option>
                                    <option value="O">Khác</option>
                                </select>
                            </div>

                            <!-- DienThoai (Phone) Field -->
                            <div class="form-group">
                                <label for="dien_thoai" class="form-label">Số điện thoại</label>
                                <input 
                                    type="tel" 
                                    id="dien_thoai" 
                                    name="dien_thoai" 
                                    class="form-input" 
                                    placeholder="Nhập số điện thoại"
                                    maxlength="20"
                                    autocomplete="tel"
                                >
                            </div>

                            <!-- Email Field -->
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-input" 
                                    placeholder="Nhập email"
                                    maxlength="100"
                                    autocomplete="email"
                                >
                            </div>

                            <!-- Error/Success Message -->
                            <div id="registerMessage" class="login-message" style="display: none;"></div>

                            <!-- Register Button -->
                            <button type="submit" class="login-btn" id="registerBtn">
                                <span class="btn-text">Đăng ký</span>
                                <span class="btn-loading" style="display: none;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                                        <path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="0.75"/>
                                    </svg>
                                    Đang xử lý...
                                </span>
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="register-link-wrapper">
                            <span class="register-text">Bạn đã có tài khoản?</span>
                            <a href="login.php" class="register-link">Đăng nhập</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../../components/footer.php'; ?>

    <script>
    $(document).ready(function() {
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

        setupPasswordToggle('#passwordToggle', '#password');
        setupPasswordToggle('#confirmPasswordToggle', '#confirm_password');

        // Real-time password match validation
        $('#confirm_password').on('input', function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();
            const $input = $(this);
            
            if (confirmPassword.length > 0) {
                if (password !== confirmPassword) {
                    $input[0].setCustomValidity('Mật khẩu xác nhận không khớp');
                } else {
                    $input[0].setCustomValidity('');
                }
            } else {
                $input[0].setCustomValidity('');
            }
        });

        // Register form submit
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $btn = $('#registerBtn');
            const $btnText = $btn.find('.btn-text');
            const $btnLoading = $btn.find('.btn-loading');
            const $message = $('#registerMessage');
            
            // Reset message
            $message.hide().removeClass('success error').text('');
            
            // Validate password match
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            
            if (password !== confirmPassword) {
                $message.addClass('error').text('Mật khẩu xác nhận không khớp').show();
                return;
            }
            
            // Disable button and show loading
            $btn.prop('disabled', true);
            $btnText.hide();
            $btnLoading.show();
            
            // Get form data
            const formData = {
                username: $('#username').val().trim(),
                password: password,
                confirm_password: confirmPassword,
                ho: $('#ho').val().trim(),
                ten: $('#ten').val().trim(),
                gioi_tinh: $('#gioi_tinh').val() || null,
                dien_thoai: $('#dien_thoai').val().trim() || null,
                email: $('#email').val().trim() || null
            };
            
            // AJAX request
            $.ajax({
                url: '../../api/auth/register.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $message.addClass('success').text(response.message || 'Đăng ký thành công!').show();
                        
                        // Redirect after 1.5 seconds
                        setTimeout(function() {
                            window.location.href = '../../index.php';
                        }, 1500);
                    } else {
                        // Show error message
                        $message.addClass('error').text(response.message || 'Đăng ký thất bại. Vui lòng thử lại.').show();
                        
                        // Re-enable button
                        $btn.prop('disabled', false);
                        $btnText.show();
                        $btnLoading.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Register error:', error);
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
    });
    </script>
</body>
</html>
