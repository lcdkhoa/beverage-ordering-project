<?php
/**
 * Home Page - MeowTea Fresh
 * Trang chủ với hero section, categories, best sellers, news
 */

require_once 'functions.php';

// Get data from database
$categories = getCategories();
$bestSellerProducts = getProductsByCategory(null, 4); // Get 4 products
$news = getNews(3); // Get 3 latest news
$stores = getStores(1); // Get 1 store for display
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeowTea Fresh - Trang Chủ</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'components/header.php'; ?>

    <!-- Hero Section - Summer Delight Promotion -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="hero-promo">Summer Delight</div>
                    <h1 class="hero-title">
                        BUY 1<br>
                        <span style="font-size: 120px;">GET 1</span>
                    </h1>
                    <p class="hero-subtitle">Fresh and Healthy</p>
                </div>
                <div class="hero-image">
                    <img src="assets/img/carousel/one.png" alt="Summer Delight Drinks">
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">MeowTea Fresh</h2>
            <p class="section-subtitle">Lorem ipsum dolor sit amet</p>
            <p style="text-align: center; max-width: 800px; margin: 0 auto; color: var(--text-light); line-height: 1.8;">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
        </div>
    </section>

    <!-- Product Categories Section -->
    <section class="categories-section section">
        <div class="container">
            <div class="categories-grid">
                <div class="category-card" onclick="window.location.href='pages/menu/index.php?category=1'">
                    <img src="assets/img/product_catalogue/coffee.png" alt="Cà Phê" class="category-image">
                    <h3 class="category-name">Cà Phê</h3>
                </div>
                <div class="category-card" onclick="window.location.href='pages/menu/index.php?category=2'">
                    <img src="assets/img/product_catalogue/milk-tea.jpg" alt="Trà Sữa" class="category-image">
                    <h3 class="category-name">Trà Sữa</h3>
                </div>
                <div class="category-card" onclick="window.location.href='pages/menu/index.php?category=3'">
                    <img src="assets/img/product_catalogue/fruit-tea.png" alt="Trà Trái Cây" class="category-image">
                    <h3 class="category-name">Trà Trái Cây</h3>
                </div>
            </div>
            <div class="btn-center">
                <?php 
                    $text = 'ĐẶT NGAY';
                    $type = 'primary';
                    $href = 'pages/menu/index.php';
                    include 'components/button.php';
                ?>
            </div>
        </div>
    </section>

    <!-- Store System Section -->
    <section class="store-system-section section">
        <div class="container">
            <div class="store-system-content">
                <div>
                    <img src="assets/img/stores/home-page.png" alt="Cửa hàng MeowTea Fresh" class="store-image">
                </div>
                <div class="store-info">
                    <h3>Hệ Thống Cửa Hàng</h3>
                    <p class="store-count">12 cửa hàng trên toàn quốc</p>
                    <p class="store-description">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.
                    </p>
                    <a href="pages/stores/index.php" class="btn btn-primary">Xem thêm</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Best Seller Section -->
    <section class="best-seller-section">
        <div class="container">
            <div class="best-seller-header">
                <h2 class="best-seller-title">Best Seller</h2>
                <a href="pages/menu/index.php" class="view-all-link">Xem tất cả >></a>
            </div>
            <div class="products-grid">
                <?php if (!empty($bestSellerProducts)): ?>
                    <?php foreach ($bestSellerProducts as $product): ?>
                        <?php 
                            $product = $product;
                            include 'components/product-card.php'; 
                        ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; color: var(--text-light);">
                        Chưa có sản phẩm nào. Vui lòng thêm sản phẩm vào database.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- News & Events Section -->
    <section class="news-section">
        <div class="container">
            <div class="news-header">
                <h2 class="news-title">Tin Tức & Sự Kiện</h2>
                <a href="pages/news/index.php" class="view-all-link">Xem tất cả >></a>
            </div>
            <div class="news-grid">
                <?php if (!empty($news)): ?>
                    <?php foreach ($news as $newsItem): ?>
                        <?php 
                            $news = $newsItem;
                            include 'components/news-card.php'; 
                        ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback news items if database is empty -->
                    <div class="news-card">
                        <div class="news-image-wrapper">
                            <img src="assets/img/news/news_one.jpg" alt="News" class="news-image">
                            <div class="news-date-badge">
                                <span class="date-day">24</span>
                                <span class="date-month">THG 12</span>
                            </div>
                        </div>
                        <div class="news-content">
                            <h3 class="news-title">
                                <a href="pages/news/index.php">Những lợi ích tuyệt vời của nước ép trái cây đối với sức khỏe</a>
                            </h3>
                            <p class="news-excerpt">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore...</p>
                            <a href="pages/news/index.php" class="news-read-more">Đọc tiếp →</a>
                        </div>
                    </div>
                    <div class="news-card">
                        <div class="news-image-wrapper">
                            <img src="assets/img/news/news_two.jpg" alt="News" class="news-image">
                            <div class="news-date-badge">
                                <span class="date-day">05</span>
                                <span class="date-month">THG 12</span>
                            </div>
                        </div>
                        <div class="news-content">
                            <h3 class="news-title">
                                <a href="pages/news/index.php">Cà Phê Cappuccino Dừa lần đầu tiên có mặt tại MeowTea Fresh</a>
                            </h3>
                            <p class="news-excerpt">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore...</p>
                            <a href="pages/news/index.php" class="news-read-more">Đọc tiếp →</a>
                        </div>
                    </div>
                    <div class="news-card">
                        <div class="news-image-wrapper">
                            <img src="assets/img/news/news_three.png" alt="News" class="news-image">
                            <div class="news-date-badge">
                                <span class="date-day">09</span>
                                <span class="date-month">THG 12</span>
                            </div>
                        </div>
                        <div class="news-content">
                            <h3 class="news-title">
                                <a href="pages/news/index.php">MeowTea Fresh ra mắt dòng sản phẩm Matcha - dấu ấn độc đáo</a>
                            </h3>
                            <p class="news-excerpt">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore...</p>
                            <a href="pages/news/index.php" class="news-read-more">Đọc tiếp →</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
