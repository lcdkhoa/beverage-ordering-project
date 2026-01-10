/**
 * Main JavaScript for MeowTea Fresh
 * jQuery và AJAX functions
 */

$(document).ready(function() {
    // Back to top button
    $('.back-to-top-link').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 600);
    });

    // Show/hide back to top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn();
        } else {
            $('.back-to-top').fadeOut();
        }
    });

    // Add to cart functionality
    $('.add-to-cart-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const productId = $(this).data('product-id');
        
        // Show product selection modal or redirect
        window.location.href = `pages/menu/product.php?id=${productId}`;
    });

    // Product card click
    $('.product-card').on('click', function() {
        const productId = $(this).data('product-id');
        window.location.href = `pages/menu/product.php?id=${productId}`;
    });

    // Update cart count
    function updateCartCount() {
        // Tính đường dẫn API dựa trên vị trí hiện tại
        const currentPath = window.location.pathname;
        let apiPath = 'api/cart/count.php';
        
        // Nếu đang ở trong thư mục con (pages/...), cần thêm ../../
        // Ví dụ: /projects_web_php/pages/menu/index.php -> cần ../../api/...
        if (currentPath.includes('/pages/')) {
            // Đếm số level từ pages/ về root
            const pathParts = currentPath.split('/').filter(p => p);
            const pagesIndex = pathParts.indexOf('pages');
            if (pagesIndex >= 0) {
                // Số level = số phần tử sau 'pages' + 1 (cho pages)
                const levels = pathParts.length - pagesIndex - 1;
                apiPath = '../'.repeat(levels) + apiPath;
            }
        }
        
        $.ajax({
            url: apiPath,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('.cart-count').text(response.count || 0);
                }
            },
            error: function() {
                // Silent fail
            }
        });
    }

    // Load cart count on page load
    updateCartCount();
});
