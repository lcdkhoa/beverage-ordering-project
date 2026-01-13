<?php
/**
 * Menu Page - Danh sách sản phẩm
 * Hiển thị products từ database với filter theo category và search
 */

require_once '../../functions.php';

// Get parameters
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$showBestSeller = isset($_GET['bestseller']) && $_GET['bestseller'] == '1';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;


$categories = getCategories();
$bestSellers = getBestSellerProducts(2);
$products = searchProducts($keyword, $categoryId, $page, $perPage);
$totalProducts = countProducts($keyword, $categoryId);
$totalPages = ceil($totalProducts / $perPage);


$selectedCategoryName = 'Tất cả';
if ($showBestSeller) {
    $selectedCategoryName = 'Best Seller';
} elseif ($categoryId) {
    foreach ($categories as $cat) {
        if ($cat['MaCategory'] == $categoryId) {
            $selectedCategoryName = $cat['TenCategory'];
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - MeowTea Fresh</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/menu.css">
    <link rel="stylesheet" href="../../assets/css/menu-modal.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <!-- Hero Banner Section
    <!-- <section class="menu-hero" id="menu-hero-section">
        <div class="menu-hero-image">
            <img src="../../assets/img/products/product_banner.png" alt="Fresh Juice">
        </div>
    </section> -->

    <!-- Menu Content -->
    <section class="menu-content-section" id="menu-content-section">
        <div class="container">
            <!-- Menu Title and Search Bar -->
            <div class="menu-header">
                <h1 class="sidebar-title">Menu</h1>
                <div class="menu-search">
                    <form method="GET" action="" class="search-form">
                        <?php if ($categoryId): ?>
                            <input type="hidden" name="category" value="<?php echo $categoryId; ?>">
                        <?php endif; ?>
                        <?php if ($showBestSeller): ?>
                            <input type="hidden" name="bestseller" value="1">
                        <?php endif; ?>
                        <div class="search-input-wrapper">
                            <input 
                                type="text" 
                                name="search" 
                                class="search-input" 
                                placeholder="Hôm nay bạn muốn uống gì?" 
                                value="<?php echo e($keyword); ?>"
                            >
                            <svg class="search-mic-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                                <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                                <line x1="12" y1="19" x2="12" y2="23"/>
                                <line x1="8" y1="23" x2="16" y2="23"/>
                            </svg>
                        </div>
                        
                    </form>
                </div>
            </div>

            <div class="menu-layout">
                <!-- Sidebar - Categories -->
                <aside >
                    <ul class="category-list">
                        <li class="category-item <?php echo $showBestSeller ? 'active' : ''; ?>">
                            <a href="?bestseller=1&search=<?php echo urlencode($keyword); ?>" class="category-link">
                                <span class="category-icon">
                                    <img src="../../assets/img/products/menu/best_seller.svg" 
                                         alt="Best Seller" 
                                         class="category-icon-img">
                                </span>
                            </a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li class="category-item <?php echo $categoryId == $category['MaCategory'] ? 'active' : ''; ?>">
                                <a href="?category=<?php echo $category['MaCategory']; ?>&search=<?php echo urlencode($keyword); ?>" class="category-link">
                                    <span class="category-icon">
                                        <?php
                                        $icon = getCategoryIcon($category['TenCategory']);
                                        $iconMap = [
                                            'coffee' => 'coffee.svg',
                                            'milk-tea' => 'milk_tea.svg',
                                            'fruit-tea' => 'fruit_tea.svg',
                                            'blended' => 'grinded_ice.svg',
                                            'yogurt' => 'yoghurt.svg',
                                            'topping' => 'topping.svg',
                                            'default' => 'coffee.svg'
                                        ];
                                        $iconFile = $iconMap[$icon] ?? 'coffee.svg';
                                        ?>
                                        <img src="../../assets/img/products/menu/<?php echo $iconFile; ?>" 
                                             alt="<?php echo e($category['TenCategory']); ?>" 
                                             class="category-icon-img">
                                    </span>
                                    
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>

                <!-- Main Content - Products -->
                <main class="menu-main">

                    <!-- Products Grid -->
                    <div class="menu-products-section">
                        <h2 class="section-heading">
                            <?php echo e($selectedCategoryName); ?>
                            <?php if ($showBestSeller && !empty($bestSellers)): ?>
                                <span class="product-count">(<?php echo count($bestSellers); ?> sản phẩm)</span>
                            <?php elseif (!$showBestSeller && $totalProducts > 0): ?>
                                <span class="product-count">(<?php echo $totalProducts; ?> sản phẩm)</span>
                            <?php endif; ?>
                        </h2>
                        
                        <?php if ($showBestSeller && !empty($bestSellers)): ?>
                            <!-- Render Best Sellers -->
                            <div class="products-grid">
                                <?php foreach ($bestSellers as $product): ?>
                                    <?php 
                                        $product = $product;
                                        $basePath = '../../'; // Từ pages/menu/ về root
                                        include '../../components/product-card.php'; 
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (!$showBestSeller && !empty($products)): ?>
                            <!-- Render Regular Products -->
                            <div class="products-grid">
                                <?php foreach ($products as $product): ?>
                                    <?php 
                                        $product = $product;
                                        $basePath = '../../'; // Từ pages/menu/ về root
                                        include '../../components/product-card.php'; 
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-products">
                                <p>Không tìm thấy sản phẩm nào.</p>
                                <a href="index.php" class="btn btn-primary">Xem tất cả sản phẩm</a>
                            </div>
                        <?php endif; ?>

                        <!-- Pagination -->
                        <?php if (!$showBestSeller && $totalPages > 0): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&category=<?php echo $categoryId ?? ''; ?>&search=<?php echo urlencode($keyword); ?>" class="pagination-btn">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M15 18l-6-6 6-6"/>
                                        </svg>
                                        Trước
                                    </a>
                                <?php endif; ?>

                                <div class="pagination-numbers">
                                    <?php
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($totalPages, $page + 2);
                                    
                                    if ($startPage > 1): ?>
                                        <a href="?page=1&category=<?php echo $categoryId ?? ''; ?>&search=<?php echo urlencode($keyword); ?>" class="pagination-number">1</a>
                                        <?php if ($startPage > 2): ?>
                                            <span class="pagination-dots">...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>&category=<?php echo $categoryId ?? ''; ?>&search=<?php echo urlencode($keyword); ?>" 
                                           class="pagination-number <?php echo $i == $page ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($endPage < $totalPages): ?>
                                        <?php if ($endPage < $totalPages - 1): ?>
                                            <span class="pagination-dots">...</span>
                                        <?php endif; ?>
                                        <a href="?page=<?php echo $totalPages; ?>&category=<?php echo $categoryId ?? ''; ?>&search=<?php echo urlencode($keyword); ?>" class="pagination-number"><?php echo $totalPages; ?></a>
                                    <?php endif; ?>
                                </div>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&category=<?php echo $categoryId ?? ''; ?>&search=<?php echo urlencode($keyword); ?>" class="pagination-btn">
                                        Sau
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9 18l6-6-6-6"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="pagination-info">
                                Trang <?php echo $page; ?> trên <?php echo $totalPages; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </main>
            </div>
        </div>
    </section>

    <div style="background-color: var(--light-green);" class="back-to-top">
        <a href="#top" class="back-to-top-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 15l-6-6-6 6"/>
            </svg>
            <span>Lên đầu trang</span>
        </a>
    </div>

    <!-- Product Customization Side Menu -->
    <div id="product-customize-modal" class="product-customize-modal">
        <div class="modal-overlay"></div>
        <div class="modal-side-panel">
            <div class="modal-content">
                <div id="modal-loading" class="modal-loading">
                    <p>Đang tải...</p>
                </div>
                
                <div id="modal-product-content" style="display: none;">
                    <!-- Product Image -->
                    <div class="modal-product-image-wrapper">
                        <img id="modal-product-image" src="" alt="" class="modal-product-image">
                    </div>
                    
                    <!-- Product Info -->
                    <div class="modal-product-info">
                        <h2 id="modal-product-name"></h2>
                        <div class="modal-product-price">
                            <span id="modal-current-price" class="modal-current-price"></span>
                            <span id="modal-old-price" class="modal-old-price"></span>
                        </div>
                        
                        <!-- Quantity Selector -->
                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn" id="modal-decrease-qty">-</button>
                            <!-- Giữ input ẩn để logic JS dùng, hiển thị span để người dùng thấy -->
                            <input type="hidden" id="modal-quantity" value="1" min="1" max="10">
                            <span id="modal-quantity-display" class="quantity-input" aria-live="polite">1</span>
                            <button type="button" class="quantity-btn" id="modal-increase-qty">+</button>
                        </div>
                        
                        <!-- Options Form -->
                        <form id="modal-product-form">
                            <input type="hidden" id="modal-product-id" name="product_id">
                            <input type="hidden" id="modal-base-price" name="base_price">
                            
                            <div id="modal-option-groups" style="margin-top: 20px;"></div>
                            
                            <!-- Note Section -->
                            <div class="note-section">
                                <label for="modal-product-note" class="note-label">Thêm ghi chú</label>
                                <textarea 
                                    id="modal-product-note" 
                                    name="note" 
                                    class="note-textarea" 
                                    placeholder="Nhập nội dung ghi chú cho quán (nếu có)"
                                    maxlength="52"
                                ></textarea>
                                <div class="char-count"><span id="modal-char-count">0</span>/52 ký tự</div>
                            </div>
                            
                            <!-- Total Price Display -->
                            <div class="total-price-display">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 20px; font-weight: bold; color: var(--primary-green);">Tổng tiền:</span>
                                    <span id="modal-total-price" style="font-size: 28px; font-weight: bold; color: var(--primary-green);"></span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="product-actions" style="position: fixed; bottom: 0; background-color: var(--white); padding: 20px; border-top: 1px solid var(--border-color);">
                                <button type="button" id="modal-add-to-cart-btn" class="btn-add-cart">
                                    Thêm vào giỏ
                                </button>
                                <button type="button" id="modal-add-to-cart-btn" class="btn-view-cart">
                                    <a href="../../pages/cart/index.php">
                                        Xem giỏ hàng
                                    </a>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../components/footer.php'; ?>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
