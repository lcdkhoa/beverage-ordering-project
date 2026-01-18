/**
 * Profile Page JavaScript
 * Handles tab switching, password change, and order loading
 * Requires: common.js
 */

$(document).ready(function() {
    // Collapsible sections
    $('.collapsible-header').on('click', function() {
        const $header = $(this);
        const targetId = $header.data('target');
        const $content = $('#' + targetId);
        
        // Toggle collapsed class
        $header.toggleClass('collapsed');
        $content.toggleClass('collapsed');
        
        // Update aria-expanded for accessibility
        const isExpanded = !$header.hasClass('collapsed');
        $header.attr('aria-expanded', isExpanded);
    });

    // Tab switching (only for items with data-tab; allow default for links like logout)
    $('.profile-nav-item').on('click', function(e) {
        const tab = $(this).data('tab');
        if (!tab) return; // e.g. logout link - let browser navigate

        e.preventDefault();
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
            dien_thoai: $('#dien_thoai').val().trim() || null,
            dia_chi: $('#dia_chi').val().trim() || null
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

    // Orders filters: reload on change (reset to page 1)
    $('#orderStatusFilter, #orderDaysFilter').on('change', function() {
        loadOrders(1);
    });

    // Order detail modal: close
    $('#orderDetailModal .order-detail-overlay, #orderDetailModal .order-detail-close').on('click', function() {
        $('#orderDetailModal').hide();
    });
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#orderDetailModal').is(':visible')) {
            $('#orderDetailModal').hide();
        }
    });

    // Load orders with pagination and filters
    function loadOrders(page) {
        page = page || 1;
        var $loading = $('#ordersLoading');
        var $empty = $('#ordersEmpty');
        var $list = $('#ordersList');
        var $pagination = $('#ordersPagination');

        $loading.show();
        $empty.hide();
        $list.hide();
        $pagination.hide();

        var params = {
            page: page,
            per_page: 10,
            status: $('#orderStatusFilter').val() || '',
            days: $('#orderDaysFilter').val() || 30
        };

        $.ajax({
            url: '../../api/order/get.php',
            method: 'GET',
            data: { page: params.page, per_page: params.per_page, status: params.status, days: params.days },
            dataType: 'json',
            success: function(res) {
                $loading.hide();
                if (res.success && res.orders && res.orders.length > 0) {
                    renderOrders(res.orders);
                    renderOrdersPagination(res);
                    $list.show();
                    if (res.total_pages > 1) {
                        $pagination.show();
                    }
                } else {
                    $empty.show();
                }
            },
            error: function(xhr, status, err) {
                console.error('Load orders error:', err);
                $loading.hide();
                $empty.show();
            }
        });
    }

    // Render order list cards (design: compact with Xem chi tiết)
    function renderOrders(orders) {
        var $list = $('#ordersList');
        $list.empty();
        orders.forEach(function(o) {
            var dateTime = (o.NgayTaoFormatted || '') + ' | ' + (o.NgayTaoTime || '');
            var statusClass = getStatusClass(o.TrangThai);
            var statusText = getStatusText(o.TrangThai);
            var card = $('<div class="order-card order-card-compact">')
                .append(
                    '<div class="order-card-header">' +
                    '<span class="order-status-badge status-' + statusClass + '">' + escapeHtml(statusText) + '</span>' +
                    '</div>' +
                    '<div class="order-card-body">' +
                    '<h3 class="order-card-code">Mã đơn ' + escapeHtml(o.OrderCode) + '</h3>' +
                    '<p class="order-card-date">' + escapeHtml(dateTime) + '</p>' +
                    '<p class="order-card-store">Cửa hàng: ' + escapeHtml(o.TenStore) + '</p>' +
                    '<p class="order-card-qty">Số lượng: ' + (o.ItemCount || 0) + ' Sản phẩm</p>' +
                    '<div class="order-card-footer">' +
                    '<a href="#" class="order-card-detail-link" data-order-id="' + o.MaOrder + '">Xem chi tiết</a>' +
                    '<span class="order-card-total">Tổng tiền: ' + formatCurrency(o.TongTien) + '</span>' +
                    '</div>' +
                    '</div>'
                );
            $list.append(card);
        });
        $list.find('.order-card-detail-link').on('click', function(e) {
            e.preventDefault();
            openOrderDetail(parseInt($(this).data('order-id'), 10));
        });
    }

    // Pagination: "Trang X trên Y" and prev/next, numbers
    function renderOrdersPagination(res) {
        var total = res.total || 0;
        var totalPages = res.total_pages || 1;
        var page = res.page || 1;
        var $p = $('#ordersPagination');
        $p.empty();
        if (totalPages <= 1) return;

        var start = Math.max(1, page - 2);
        var end = Math.min(totalPages, page + 2);
        var nums = '';
        for (var i = start; i <= end; i++) {
            nums += '<button type="button" class="pagination-number' + (i === page ? ' active' : '') + '" data-page="' + i + '">' + i + '</button>';
        }
        var html = '<div class="orders-pagination-inner">' +
            '<p class="pagination-info">Trang ' + page + ' trên ' + totalPages + '</p>' +
            '<div class="pagination-controls">' +
            '<button type="button" class="pagination-arrow" data-page="' + (page - 1) + '"' + (page <= 1 ? ' disabled' : '') + '>&lt;</button>' +
            '<div class="pagination-numbers">' + nums + '</div>' +
            '<button type="button" class="pagination-arrow" data-page="' + (page + 1) + '"' + (page >= totalPages ? ' disabled' : '') + '>&gt;</button>' +
            '</div>' +
            '</div>';
        $p.html(html);
        $p.find('.pagination-number, .pagination-arrow').on('click', function() {
            var p = parseInt($(this).data('page'), 10);
            if (p >= 1 && p <= totalPages) loadOrders(p);
        });
    }

    // Open order detail modal: fetch get_one and render 4 sections
    function openOrderDetail(orderId) {
        var $modal = $('#orderDetailModal');
        var $body = $('#orderDetailBody');
        $body.html('<div class="order-detail-loading">Đang tải...</div>');
        $modal.show();

        $.ajax({
            url: '../../api/order/get_one.php',
            method: 'GET',
            data: { id: orderId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.order) {
                    $body.html(renderOrderDetail(res.order));
                } else {
                    $body.html('<p class="order-detail-error">Không tải được đơn hàng.</p>');
                }
            },
            error: function() {
                $body.html('<p class="order-detail-error">Có lỗi xảy ra. Vui lòng thử lại.</p>');
            }
        });
    }

    // Render order detail (4 sections as in design)
    function renderOrderDetail(o) {
        var statusClass = getStatusClass(o.TrangThai);
        var statusText = getStatusText(o.TrangThai);
        var basePath = '../../';
        var sect1 = '<div class="order-detail-section">' +
            '<h3 class="order-detail-section-title">Thông tin đơn hàng</h3>' +
            '<div class="order-detail-info-grid">' +
            '<div class="info-item"><span class="info-label">Mã đơn hàng:</span> <span class="info-value">' + escapeHtml(o.OrderCode) + '</span></div>' +
            '<div class="info-item"><span class="info-label">Thời gian đặt hàng:</span> <span class="info-value">' + escapeHtml(o.NgayTaoFormatted) + '</span></div>' +
            '<div class="info-item"><span class="info-label">Trạng thái:</span> <span class="order-detail-status status-' + statusClass + '">' + escapeHtml(statusText) + '</span></div>' +
            '<div class="info-item"><span class="info-label">Hình thức thanh toán:</span> <span class="info-value">' + escapeHtml(o.PaymentMethod) + '</span></div>' +
            '</div></div>';

        var sect2 = '<div class="order-detail-section">' +
            '<h3 class="order-detail-section-title">Thông tin nhận hàng</h3>' +
            '<div class="order-detail-info-grid">' +
            '<div class="info-item"><span class="info-label">Họ và tên:</span> <span class="info-value">' + escapeHtml(o.NguoiNhan || '') + '</span></div>' +
            '<div class="info-item"><span class="info-label">Số điện thoại:</span> <span class="info-value">' + escapeHtml(o.DienThoaiGiao || '') + '</span></div>' +
            '<div class="info-item full"><span class="info-label">Địa chỉ nhận hàng:</span> <span class="info-value">' + escapeHtml(o.DiaChiGiao || '') + '</span></div>' +
            '</div></div>';

        var productsHtml = '';
        if (o.items && o.items.length > 0) {
            o.items.forEach(function(it) {
                var img = (it.HinhAnh && it.HinhAnh.indexOf('http') !== 0) ? (basePath + (it.HinhAnh || 'assets/img/products/product_one.png')) : (it.HinhAnh || (basePath + 'assets/img/products/product_one.png'));
                var opts = [];
                if (it.options && it.options.length) {
                    it.options.forEach(function(opt) {
                        var t = (parseFloat(opt.GiaThem) || 0) > 0 ? '+ ' + (opt.TenGiaTri || '') : (opt.TenGiaTri || '');
                        opts.push(escapeHtml(t));
                    });
                }
                var optsStr = opts.length ? '<div class="order-detail-item-options">' + opts.join(', ') + '</div>' : '';
                
                // Calculate total price including options
                var giaHienTai = parseFloat(it.GiaCoBan);
                if (it.options && it.options.length) {
                    it.options.forEach(function(opt) {
                        giaHienTai += parseFloat(opt.GiaThem || 0);
                    });
                }
                
                productsHtml += '<div class="order-detail-product">' +
                    '<div class="order-detail-product-img"><img src="' + escapeHtml(img) + '" alt=""></div>' +
                    '<div class="order-detail-product-info">' +
                    '<p class="order-detail-product-name">x' + (it.SoLuong || 1) + ' ' + escapeHtml(it.TenSP || '') + '</p>' +
                    optsStr +
                    '<div class="order-detail-product-price">' +
                    '<span class="order-detail-item-current-price">' + formatCurrency(giaHienTai) + '</span>' +
                    '</div></div></div>';
            });
        }
        var sect3 = '<div class="order-detail-section">' +
            '<h3 class="order-detail-section-title">Sản phẩm (' + (o.items ? o.items.length : 0) + ')</h3>' +
            '<div class="order-detail-products">' + (productsHtml || '<p>Không có sản phẩm</p>') + '</div></div>';

        var sect4 = '<div class="order-detail-section">' +
            '<h3 class="order-detail-section-title">Số tiền thanh toán</h3>' +
            '<div class="order-detail-summary">' +
            '<div class="order-detail-summary-row"><span class="info-label">Tạm tính:</span> <span class="info-value">' + formatCurrency(o.Subtotal || 0) + '</span></div>' +
            '<div class="order-detail-summary-row"><span class="info-label">Phí vận chuyển:</span> <span class="info-value">' + formatCurrency(o.PhiVanChuyen || 0) + '</span></div>' +
            '<div class="order-detail-summary-row"><span class="info-label">Khuyến mãi:</span> <span class="info-value">' + ((o.GiamGia || 0) > 0 ? '-' : '') + formatCurrency(o.GiamGia || 0) + '</span></div>' +
            '<div class="order-detail-summary-row total"><span class="info-label">Số tiền thanh toán:</span> <span class="info-value">' + formatCurrency(o.TongTien || 0) + '</span></div>' +
            '</div></div>';

        return sect1 + sect2 + sect3 + sect4;
    }

    function getStatusClass(status) {
        var s = (status || '').toLowerCase();
        if (s === 'completed') return 'completed';
        if (s === 'cancelled' || s === 'store_cancelled') return 'cancelled';
        if (s === 'delivering' || s === 'processing') return 'delivering';
        if (s === 'payment_received' || s === 'pending') return 'received';
        return 'received';
    }

    function getStatusText(status) {
        var s = (status || '').toLowerCase();
        if (s === 'completed') return 'Hoàn thành';
        if (s === 'cancelled' || s === 'store_cancelled') return 'Cửa hàng hủy';
        if (s === 'delivering' || s === 'processing') return 'Đang giao hàng';
        if (s === 'payment_received' || s === 'pending') return 'Đã nhận đơn';
        return 'Đã nhận đơn';
    }
});
