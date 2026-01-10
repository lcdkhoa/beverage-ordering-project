<?php
/**
 * Product Card Component
 * Reusable product card với hình ảnh, tên, giá, rating
 * 
 * @param array $product - Product data from database
 */
if (!isset($product)) return;

$productName = e($product['TenSP']);
$productPrice = formatCurrency($product['GiaCoBan']);
$productImage = !empty($product['HinhAnh']) ? e($product['HinhAnh']) : 'assets/img/products/product_one.png';
$productId = $product['MaSP'];
?>
<div class="product-card" data-product-id="<?php echo $productId; ?>">
    <div class="product-image-wrapper">
        <img src="<?php echo $productImage; ?>" alt="<?php echo $productName; ?>" class="product-image">
        <button class="add-to-cart-btn" data-product-id="<?php echo $productId; ?>" title="Thêm vào giỏ hàng">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"/>
            </svg>
        </button>
    </div>
    <div class="product-info">
        <h3 class="product-name"><?php echo $productName; ?></h3>
        <div class="product-rating">
            <span class="stars">★★★★★</span>
            <span class="rating-value">4.9</span>
            <span class="rating-count">(109 đánh giá)</span>
        </div>
        <div class="product-price">
            <span class="current-price"><?php echo $productPrice; ?></span>
            <span class="old-price"><?php echo formatCurrency($product['GiaCoBan'] * 1.3); ?></span>
        </div>
    </div>
</div>
