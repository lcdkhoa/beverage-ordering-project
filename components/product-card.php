<?php
/**
 * Product Card Component
 * Reusable product card với hình ảnh, tên, giá, rating
 * 
 * @param array $product - Product data from database
 * @param string $basePath - Base path prefix (optional, auto-detect if not provided)
 */
if (!isset($product)) return;

$productName = e($product['TenSP']);
$productPrice = formatCurrency($product['GiaCoBan']);
$productId = $product['MaSP'];

// Get rating data from database
$rating = isset($product['Rating']) && $product['Rating'] !== null ? (float)$product['Rating'] : 0;
$ratingCount = isset($product['SoLuotRating']) ? (int)$product['SoLuotRating'] : 0;

// Format rating value (1 decimal place)
$ratingValue = $rating > 0 ? number_format($rating, 1, ',', '.') : '0,0';

// Generate stars based on rating (0-5 scale)
$starsDisplay = renderStars($rating);

// Xử lý đường dẫn hình ảnh
$imagePath = !empty($product['HinhAnh']) ? $product['HinhAnh'] : 'assets/img/products/product_one.png';

// Auto-detect base path nếu không được truyền vào (fallback)
if (!isset($basePath)) {
    // Lấy đường dẫn file đang gọi component này
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $callerFile = isset($backtrace[1]['file']) ? $backtrace[1]['file'] : __FILE__;
    $callerDir = dirname($callerFile);
    $rootDir = dirname(__DIR__); // Root của project (parent của components/)
    
    // Normalize paths để so sánh
    $callerDir = realpath($callerDir);
    $rootDir = realpath($rootDir);
    
    if ($callerDir && $rootDir && strpos($callerDir, $rootDir) === 0) {
        // Tính số level cần lùi lại từ caller về root
        $relativePath = str_replace($rootDir, '', $callerDir);
        $relativePath = trim($relativePath, DIRECTORY_SEPARATOR);
        $levels = $relativePath ? substr_count($relativePath, DIRECTORY_SEPARATOR) + 1 : 0;
        
        // Tạo prefix path
        $basePath = $levels > 0 ? str_repeat('../', $levels) : '';
    } else {
        // Fallback: giả sử đang ở root
        $basePath = '';
    }
}

$basePath = rtrim($basePath, '/\\');
if ($basePath) {
    $basePath .= '/';
}

$imagePath = ltrim($imagePath, '/\\');

// Tạo đường dẫn đầy đủ
$productImage = $basePath . $imagePath;
$fallbackImage = $basePath . 'assets/img/products/product_one.png';
?>
<div class="product-card" data-product-id="<?php echo $productId; ?>">
    <div class="product-image-wrapper">
        <img src="<?php echo e($productImage); ?>" 
             alt="<?php echo $productName; ?>" 
             class="product-image" 
             onerror="this.onerror=null; if(this.src !== '<?php echo e($fallbackImage); ?>') { this.src='<?php echo e($fallbackImage); ?>'; } else { this.style.display='none'; }">
        <button class="add-to-cart-btn" data-product-id="<?php echo $productId; ?>" title="Thêm vào giỏ hàng">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"/>
            </svg>
        </button>
    </div>
    <div class="product-info">
        <h3 class="product-name"><?php echo $productName; ?></h3>
        <div class="product-rating">
            <span class="stars"><?php echo $starsDisplay; ?></span>
            <span class="rating-value"><?php echo $ratingValue; ?></span>
            <?php if ($ratingCount > 0): ?>
                <span class="rating-count">(<?php echo number_format($ratingCount, 0, ',', '.'); ?> đánh giá)</span>
            <?php else: ?>
                <span class="rating-count">(Chưa có đánh giá)</span>
            <?php endif; ?>
        </div>
        <div class="product-price">
            <span class="current-price"><?php echo $productPrice; ?></span>
            <span class="old-price"><?php echo formatCurrency($product['GiaCoBan'] * 1.3); ?></span>
        </div>
    </div>
</div>
