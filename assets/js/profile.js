/**
 * Profile Page JavaScript
 * Handles tab switching, password change, and order loading
 * Requires: common.js
 */

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

    // Update profile form submit
    $('#updateProfileForm').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#updateProfileBtn');
        const $btnText = $btn.find('.btn-text');
        const $btnLoading = $btn.find('.btn-loading');
        const $message = $('#updateProfileMessage');
        
        // Reset message
        $message.hide().removeClass('success error').text('');
        
        // Disable button and show loading
        $btn.prop('disabled', true);
        $btnText.hide();
        $btnLoading.show();
        
        // Get form data
        const formData = {
            gioi_tinh: $('#gioi_tinh').val() || null,
            email: $('#email').val().trim() || null,
            dien_thoai: $('#dien_thoai').val().trim() || null
        };
        
        // AJAX request
        $.ajax({
            url: '../../api/auth/update-profile.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $message.addClass('success').text(response.message || 'Cập nhật thông tin thành công!').show();
                    
                    // Reload page after 1.5 seconds to reflect changes
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    $message.addClass('error').text(response.message || 'Cập nhật thông tin thất bại. Vui lòng thử lại.').show();
                    
                    // Re-enable button
                    $btn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Update profile error:', error);
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

    // Use formatCurrency and escapeHtml from common.js
});
