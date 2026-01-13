/**
 * Login Page JavaScript
 * Handles password toggle, form submission, and social login
 */

$(document).ready(function() {
    // Password toggle visibility
    $('#passwordToggle').on('click', function() {
        const passwordInput = $('#password');
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

    // Login form submit
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#loginBtn');
        const $btnText = $btn.find('.btn-text');
        const $btnLoading = $btn.find('.btn-loading');
        const $message = $('#loginMessage');
        
        // Reset message
        $message.hide().removeClass('success error').text('');
        
        // Disable button and show loading
        $btn.prop('disabled', true);
        $btnText.hide();
        $btnLoading.show();
        
        // Get form data
        const formData = {
            email_or_phone: $('#email_or_phone').val().trim(),
            password: $('#password').val()
        };
        
        // AJAX request
        $.ajax({
            url: '../../api/auth/login.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $message.addClass('success').text(response.message || 'Đăng nhập thành công!').show();
                    
                    // Redirect after 1 second
                    setTimeout(function() {
                        window.location.href = '../../index.php';
                    }, 1000);
                } else {
                    // Show error message
                    $message.addClass('error').text(response.message || 'Đăng nhập thất bại. Vui lòng thử lại.').show();
                    
                    // Re-enable button
                    $btn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Login error:', error);
                $message.addClass('error').text('Có lỗi xảy ra. Vui lòng thử lại sau.').show();
                
                // Re-enable button
                $btn.prop('disabled', false);
                $btnText.show();
                $btnLoading.hide();
            }
        });
    });

    // Social login buttons (placeholder)
    $('#facebookLogin').on('click', function() {
        alert('Tính năng đăng nhập bằng Facebook sẽ được triển khai sau.');
    });

    $('#googleLogin').on('click', function() {
        alert('Tính năng đăng nhập bằng Google sẽ được triển khai sau.');
    });
});
