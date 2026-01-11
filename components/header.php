<?php
/**
 * Header Component
 * Reusable header với logo, navigation, cart, login
 */

require_once __DIR__ . '/../functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tính đường dẫn base từ vị trí file gọi component này
$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
$callerFile = isset($backtrace[0]['file']) ? $backtrace[0]['file'] : __FILE__;
$callerDir = dirname($callerFile);
$rootDir = dirname(__DIR__); // Root của project

// Normalize paths
$callerDir = realpath($callerDir);
$rootDir = realpath($rootDir);

// Tính số level cần lùi lại
if ($callerDir && $rootDir && strpos($callerDir, $rootDir) === 0) {
    $relativePath = str_replace($rootDir, '', $callerDir);
    $relativePath = trim($relativePath, DIRECTORY_SEPARATOR);
    $levels = $relativePath ? substr_count($relativePath, DIRECTORY_SEPARATOR) + 1 : 0;
    $basePath = $levels > 0 ? str_repeat('../', $levels) : '';
} else {
    $basePath = '';
}

// Tính đường dẫn index.php
$indexPath = $basePath . 'index.php';

// Xác định trang hiện tại để đánh dấu active
$currentScript = basename($_SERVER['PHP_SELF']);
$currentPath = $_SERVER['REQUEST_URI'];

// Xác định active link dựa trên path
$isHome = ($currentScript == 'index.php' && strpos($currentPath, '/pages/') === false);
$isMenu = strpos($currentPath, '/pages/menu/') !== false;
$isStores = strpos($currentPath, '/pages/stores/') !== false;
$isNews = strpos($currentPath, '/pages/news/') !== false;
$isCareer = strpos($currentPath, '/pages/career/') !== false;
$isAbout = strpos($currentPath, '/pages/about/') !== false;

// Check if user is logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $isLoggedIn ? ($_SESSION['user_name'] ?? '') : '';
$avatarInitial = $isLoggedIn ? getAvatarInitial($userName) : '';
?>
<header class="main-header">
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <div class="logo">
                <a href="<?php echo $indexPath; ?>">
                    <img src="<?php echo $basePath; ?>assets/img/logo.png" alt="MeowTea Fresh" class="logo-img">
                    <span class="logo-text">MeowTea Fresh</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-list">
                    <li>
                        <?php if ($isHome): ?>
                            <span class="nav-link active">Trang chủ</span>
                        <?php else: ?>
                            <a href="<?php echo $indexPath; ?>" class="nav-link">Trang chủ</a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if ($isMenu): ?>
                            <span class="nav-link active">Menu</span>
                        <?php else: ?>
                            <a href="<?php echo $basePath; ?>pages/menu/index.php" class="nav-link">Menu</a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if ($isStores): ?>
                            <span class="nav-link active">Cửa hàng</span>
                        <?php else: ?>
                            <a href="<?php echo $basePath; ?>pages/stores/index.php" class="nav-link">Cửa hàng</a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if ($isNews): ?>
                            <span class="nav-link active">Tin tức</span>
                        <?php else: ?>
                            <a href="<?php echo $basePath; ?>pages/news/index.php" class="nav-link">Tin tức</a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if ($isCareer): ?>
                            <span class="nav-link active">Tuyển dụng</span>
                        <?php else: ?>
                            <a href="<?php echo $basePath; ?>pages/career/index.php" class="nav-link">Tuyển dụng</a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if ($isAbout): ?>
                            <span class="nav-link active">Về MeowTea Fresh</span>
                        <?php else: ?>
                            <a href="<?php echo $basePath; ?>pages/about/index.php" class="nav-link">Về MeowTea Fresh</a>
                        <?php endif; ?>
                    </li>
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
                    <a href="<?php echo $basePath; ?>pages/cart/index.php" class="cart-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 2L7 6H2v2h1l1 10h12l1-10h1V6h-5L15 2H9z"/>
                        </svg>
                        <span class="cart-text">Giỏ hàng</span>
                        <span class="cart-count">0</span>
                    </a>
                </div>
                <div class="separator">|</div>
                <?php if ($isLoggedIn): ?>
                    <!-- User Info (when logged in) -->
                    <div class="user-info-wrapper">
                        <div class="user-avatar" title="<?php echo e($userName); ?>">
                            <span class="avatar-initial"><?php echo e($avatarInitial); ?></span>
                        </div>
                        <span class="user-name"><?php echo e($userName); ?></span>
                        <div class="user-dropdown">
                            <button class="user-dropdown-toggle" aria-label="Menu người dùng">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </button>
                            <div class="user-dropdown-menu">
                                <a href="<?php echo $basePath; ?>pages/profile/index.php" class="dropdown-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span>Thông tin tài khoản</span>
                                </a>
                                <a href="<?php echo $basePath; ?>api/auth/logout.php" class="dropdown-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    <span>Đăng xuất</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Login Link (when not logged in) -->
                    <div class="login-icon">
                        <a href="<?php echo $basePath; ?>pages/auth/login.php" class="login-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span class="login-text">Đăng nhập</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
