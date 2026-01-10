<?php
/**
 * Header Component
 * Reusable header với logo, navigation, cart, login
 */
?>
<header class="main-header">
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <div class="logo">
                <a href="index.php">
                    <img src="assets/img/logo.png" alt="MeowTea Fresh" class="logo-img">
                    <span class="logo-text">MeowTea Fresh</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-list">
                    <li><a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Trang chủ</a></li>
                    <li><a href="pages/menu/index.php" class="nav-link">Menu</a></li>
                    <li><a href="pages/stores/index.php" class="nav-link">Cửa hàng</a></li>
                    <li><a href="pages/news/index.php" class="nav-link">Tin tức</a></li>
                    <li><a href="pages/career/index.php" class="nav-link">Tuyển dụng</a></li>
                    <li><a href="pages/about/index.php" class="nav-link">Về MeowTea Fresh</a></li>
                </ul>
            </nav>

            <!-- User Actions -->
            <div class="header-actions">
                <div class="currency-selector">
                    <span>VND</span>
                    <span class="flag-icon">🇻🇳</span>
                </div>
                <div class="separator">|</div>
                <div class="cart-icon">
                    <a href="pages/cart/index.php" class="cart-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 2L7 6H2v2h1l1 10h12l1-10h1V6h-5L15 2H9z"/>
                        </svg>
                        <span class="cart-text">Giỏ hàng</span>
                        <span class="cart-count">0</span>
                    </a>
                </div>
                <div class="separator">|</div>
                <div class="login-icon">
                    <a href="pages/auth/login.php" class="login-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span class="login-text">Đăng nhập</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
