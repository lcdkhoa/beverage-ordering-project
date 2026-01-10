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

// Normalize basePath - đảm bảo không có trailing slash
$basePath = rtrim($basePath, '/\\');
if ($basePath) {
    $basePath .= '/';
}

// Normalize imagePath - loại bỏ leading slash
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
