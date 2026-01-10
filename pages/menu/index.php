<?php
/**
 * Menu Page - Danh sách sản phẩm
 * Hiển thị products từ database với filter theo category và search
 */

require_once '../../functions.php';

// Get parameters
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;

// Get data from database
$categories = getCategories();
$products = searchProducts($keyword, $categoryId, $page, $perPage);
$totalProducts = countProducts($keyword, $categoryId);
$totalPages = ceil($totalProducts / $perPage);

// Get selected category name
$selectedCategoryName = 'Tất cả';
if ($categoryId) {
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
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/menu.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <!-- Hero Banner Section -->
    <section class="menu-hero">
        <div class="container">
            <div class="menu-hero-content">
                <div class="menu-hero-text">
                    <h1 class="menu-hero-title">Menu</h1>
                    <p class="menu-hero-subtitle">Hôm nay bạn muốn uống gì?</p>
                </div>
                <div class="menu-hero-image">
                    <img src="../../assets/img/carousel/one.png" alt="Fresh Juice">
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Content -->
    <section class="menu-content-section">
        <div class="container">
            <div class="menu-layout">
                <!-- Sidebar - Categories -->
                <aside class="menu-sidebar">
                    <div class="best-seller-badge">BEST SELLER</div>
                    <h3 class="sidebar-title">Danh mục</h3>
                    <ul class="category-list">
                        <li class="category-item <?php echo !$categoryId ? 'active' : ''; ?>">
                            <a href="?search=<?php echo urlencode($keyword); ?>" class="category-link">
                                <span class="category-icon">☕</span>
                                <span class="category-name">Tất cả</span>
                            </a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li class="category-item <?php echo $categoryId == $category['MaCategory'] ? 'active' : ''; ?>">
                                <a href="?category=<?php echo $category['MaCategory']; ?>&search=<?php echo urlencode($keyword); ?>" class="category-link">
                                    <span class="category-icon">
                                        <?php
                                        $icon = getCategoryIcon($category['TenCategory']);
                                        $icons = [
                                            'coffee' => '☕',
                                            'milk-tea' => '🧋',
                                            'fruit-tea' => '🍹',
                                            'blended' => '🥤',
                                            'yogurt' => '🥛',
                                            'topping' => '🍮'
                                        ];
                                        echo $icons[$icon] ?? '☕';
                                        ?>
                                    </span>
                                    <span class="category-name"><?php echo e($category['TenCategory']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>

                <!-- Main Content - Products -->
                <main class="menu-main">
                    <!-- Search Bar -->
                    <div class="menu-search">
                        <form method="GET" action="" class="search-form">
                            <?php if ($categoryId): ?>
                                <input type="hidden" name="category" value="<?php echo $categoryId; ?>">
                            <?php endif; ?>
                            <div class="search-input-wrapper">
                                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                                <input 
                                    type="text" 
                                    name="search" 
                                    class="search-input" 
                                    placeholder="Hôm nay bạn muốn uống gì?" 
                                    value="<?php echo e($keyword); ?>"
                                >
                            </div>
                            <button type="submit" class="search-btn">Tìm kiếm</button>
                        </form>
                    </div>

                    <!-- Products Grid -->
                    <div class="menu-products-section">
                        <h2 class="section-heading">
                            <?php echo e($selectedCategoryName); ?>
                            <?php if ($totalProducts > 0): ?>
                                <span class="product-count">(<?php echo $totalProducts; ?> sản phẩm)</span>
                            <?php endif; ?>
                        </h2>
                        
                        <?php if (!empty($products)): ?>
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
                        <?php if ($totalPages > 1): ?>
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

    <div class="back-to-top">
        <a href="#top" class="back-to-top-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 15l-6-6-6 6"/>
            </svg>
            <span>Lên đầu trang</span>
        </a>
    </div>

    <?php include '../../components/footer.php'; ?>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
