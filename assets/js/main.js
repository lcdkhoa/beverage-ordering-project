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

    // Update cart count (placeholder - sẽ implement sau)
    function updateCartCount() {
        $.ajax({
            url: 'api/cart/count.php',
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
