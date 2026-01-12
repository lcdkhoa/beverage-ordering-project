<?php
/**
 * Helper Functions
 */

require_once __DIR__ . '/database/config.php';

/**
 * Sanitize output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . '₫';
}

/**
 * Get products by category
 */
function getProductsByCategory($categoryId = null, $limit = null) {
    $pdo = getDBConnection();
    $sql = "SELECT sp.*, c.TenCategory 
            FROM SanPham sp 
            INNER JOIN Category c ON sp.MaCategory = c.MaCategory 
            WHERE sp.TrangThai = 1";
    
    $params = [];
    if ($categoryId) {
        $sql .= " AND sp.MaCategory = ?";
        $params[] = $categoryId;
    }
    
    $sql .= " ORDER BY sp.MaSP DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get all categories
 */
function getCategories() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM Category WHERE TrangThai = 1 ORDER BY TenCategory");
    return $stmt->fetchAll();
}

/**
 * Get stores
 */
function getStores($limit = null) {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM Store WHERE TrangThai = 1 ORDER BY TenStore";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get payment methods
 */
function getPaymentMethods() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM Payment_Method ORDER BY MaPayment");
    return $stmt->fetchAll();
}

/**
 * Get news/articles
 */
function getNews($limit = null) {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM News WHERE TrangThai = 1 ORDER BY NgayTao DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get product by ID
 */
function getProductById($productId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT sp.*, c.TenCategory 
                          FROM SanPham sp 
                          INNER JOIN Category c ON sp.MaCategory = c.MaCategory 
                          WHERE sp.MaSP = ? AND sp.TrangThai = 1");
    $stmt->execute([$productId]);
    return $stmt->fetch();
}

/**
 * Get product options
 */
function getProductOptions($productId) {
    $pdo = getDBConnection();
    $sql = "SELECT og.MaOptionGroup, og.TenNhom, og.IsMultiple,
                   ov.MaOptionValue, ov.TenGiaTri, ov.GiaThem
            FROM Product_Option_Group pog
            INNER JOIN Option_Group og ON pog.MaOptionGroup = og.MaOptionGroup
            INNER JOIN Option_Value ov ON og.MaOptionGroup = ov.MaOptionGroup
            WHERE pog.MaSP = ?
            ORDER BY og.MaOptionGroup, ov.MaOptionValue";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

/**
 * Search products by name
 */
function searchProducts($keyword, $categoryId = null, $page = 1, $perPage = 12) {
    $pdo = getDBConnection();
    $offset = ($page - 1) * $perPage;
    
    $sql = "SELECT sp.*, c.TenCategory 
            FROM SanPham sp 
            INNER JOIN Category c ON sp.MaCategory = c.MaCategory 
            WHERE sp.TrangThai = 1";
    
    $params = [];
    
    if (!empty($keyword)) {
        $sql .= " AND sp.TenSP LIKE ?";
        $params[] = "%{$keyword}%";
    }
    
    if ($categoryId) {
        $sql .= " AND sp.MaCategory = ?";
        $params[] = $categoryId;
    }
    
    $sql .= " ORDER BY sp.MaSP DESC LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Count total products for pagination
 */
function countProducts($keyword = null, $categoryId = null) {
    $pdo = getDBConnection();
    $sql = "SELECT COUNT(*) as total 
            FROM SanPham sp 
            WHERE sp.TrangThai = 1";
    
    $params = [];
    
    if (!empty($keyword)) {
        $sql .= " AND sp.TenSP LIKE ?";
        $params[] = "%{$keyword}%";
    }
    
    if ($categoryId) {
        $sql .= " AND sp.MaCategory = ?";
        $params[] = $categoryId;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

/**
 * Get category icon name (for display)
 */
function getCategoryIcon($categoryName) {
    $icons = [
        'Cà phê truyền thống' => 'coffee',
        'Trà sữa' => 'milk-tea',
        'Trà trái cây' => 'fruit-tea',
        'Đá xay' => 'blended',
        'Sữa chua' => 'yogurt',
        'Topping' => 'topping'
    ];
    return $icons[$categoryName] ?? 'default';
}

/**
 * Normalize image path - đảm bảo đường dẫn hình ảnh luôn đúng từ root
 * @param string $imagePath - Đường dẫn từ database
 * @param string $currentDir - Thư mục hiện tại (__DIR__ hoặc __FILE__)
 * @return string - Đường dẫn đã được normalize
 */
function normalizeImagePath($imagePath, $currentDir = null) {
    if (empty($imagePath)) {
        return 'assets/img/products/product_one.png';
    }
    
    // Nếu đã là absolute path (bắt đầu bằng /), giữ nguyên
    if (strpos($imagePath, '/') === 0) {
        return $imagePath;
    }
    
    // Nếu đã có http/https, giữ nguyên
    if (strpos($imagePath, 'http://') === 0 || strpos($imagePath, 'https://') === 0) {
        return $imagePath;
    }
    
    // Xác định base path dựa trên vị trí file gọi function
    if ($currentDir === null) {
        // Mặc định từ root
        return $imagePath;
    }
    
    // Tính toán relative path từ currentDir về root
    $rootPath = realpath(__DIR__);
    $currentPath = realpath($currentDir);
    
    if ($currentPath && strpos($currentPath, $rootPath) === 0) {
        // Tính số level cần lùi lại
        $relativePath = str_replace($rootPath, '', $currentPath);
        $levels = substr_count($relativePath, DIRECTORY_SEPARATOR);
        
        if ($levels > 0) {
            $prefix = str_repeat('../', $levels);
            return $prefix . $imagePath;
        }
    }
    
    return $imagePath;
}

/**
 * Get image path from root - đơn giản hóa, luôn trả về đường dẫn từ root
 * @param string $imagePath - Đường dẫn từ database
 * @return string - Đường dẫn từ root
 */
function getImagePath($imagePath) {
    if (empty($imagePath)) {
        return 'assets/img/products/product_one.png';
    }
    
    // Nếu đã là absolute path, giữ nguyên
    if (strpos($imagePath, '/') === 0) {
        return $imagePath;
    }
    
    // Nếu đã có http/https, giữ nguyên
    if (strpos($imagePath, 'http://') === 0 || strpos($imagePath, 'https://') === 0) {
        return $imagePath;
    }
    
    // Đảm bảo bắt đầu từ root (không có ../)
    // Loại bỏ các ../ ở đầu nếu có
    $imagePath = ltrim($imagePath, './');
    
    return $imagePath;
}

/**
 * Hash password using bcrypt
 * @param string $password - Plain text password
 * @return string - Hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 * @param string $password - Plain text password
 * @param string $hash - Hashed password
 * @return bool - True if password matches
 */
function verifyPassword($password, $hash) {
    // If hash starts with $2y$, use password_verify
    if (strpos($hash, '$2y$') === 0) {
        return password_verify($password, $hash);
    }
    // Otherwise, compare directly (for demo/legacy passwords)
    return ($password === $hash);
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get current user data from session
 * @return array|null - User data or null if not logged in
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'ho' => $_SESSION['user_ho'] ?? null,
        'ten' => $_SESSION['user_ten'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'gioi_tinh' => $_SESSION['user_gioi_tinh'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'phone' => $_SESSION['user_phone'] ?? null,
        'role' => $_SESSION['user_role'] ?? null,
        'role_name' => $_SESSION['user_role_name'] ?? null
    ];
}

/**
 * Get full name from Ho and Ten
 * @param string $ho - Last name
 * @param string $ten - First name
 * @return string - Full name
 */
function getFullName($ho, $ten) {
    return trim($ho . ' ' . $ten);
}

/**
 * Logout user
 */
function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_destroy();
}

/**
 * Get first character of name for avatar
 * @param string $name - Full name or first name (Ten)
 * @return string - First character (uppercase)
 */
function getAvatarInitial($name) {
    if (empty($name)) {
        return 'U';
    }
    
    // Remove extra spaces and get first character
    $name = trim($name);
    $firstChar = mb_substr($name, 0, 1, 'UTF-8');
    
    // Convert to uppercase
    return mb_strtoupper($firstChar, 'UTF-8');
}

/**
 * Get avatar initial from Ho and Ten (prefer Ten)
 * @param string $ho - Last name
 * @param string $ten - First name
 * @return string - First character (uppercase)
 */
function getAvatarInitialFromName($ho, $ten) {
    // Prefer Ten (first name) for avatar initial
    if (!empty($ten)) {
        return getAvatarInitial($ten);
    }
    if (!empty($ho)) {
        return getAvatarInitial($ho);
    }
    return 'U';
}

/**
 * Get avatar image path based on gender
 * @param string|null $gioiTinh - Gender: 'M', 'F', 'O', or null
 * @param string $basePath - Base path for assets
 * @return string - Avatar image path
 */
function getAvatarImagePath($gioiTinh, $basePath = '') {
    $avatarFile = 'o.png'; // Default for Other or null
    
    if ($gioiTinh === 'M') {
        $avatarFile = 'm.jpg';
    } elseif ($gioiTinh === 'F') {
        $avatarFile = 'f.jpg';
    } elseif ($gioiTinh === 'O') {
        $avatarFile = 'o.png';
    }
    
    return $basePath . 'assets/img/avatar/' . $avatarFile;
}

/**
 * Get best seller products based on rating and number of ratings
 * @param int $limit - Number of products to return
 * @return array - Array of best seller products
 */
function getBestSellerProducts($limit = 8) {
    $db = getDBConnection(); 
    $sql = "
        SELECT sp.*, c.TenCategory
        FROM SanPham sp
        INNER JOIN Category c ON sp.MaCategory = c.MaCategory
        WHERE sp.TrangThai = 1 
          AND sp.Rating IS NOT NULL
          AND sp.SoLuotRating > 0
        ORDER BY sp.Rating DESC, sp.SoLuotRating DESC
        LIMIT :limit
    ";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Render stars based on rating value (0-5 scale)
 * @param float $rating - Rating value from 0 to 5
 * @return string - HTML string with stars (★ for full, ☆ for empty/half)
 */
function renderStars($rating) {
    // Ensure rating is between 0 and 5
    $rating = max(0, min(5, (float)$rating));
    
    $fullStars = floor($rating); // Số sao đầy
    $hasHalfStar = ($rating - $fullStars) >= 0.5; // Có nửa sao không (>= 0.5)
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0); // Số sao rỗng
    
    $stars = str_repeat('★', $fullStars); // Sao đầy
    if ($hasHalfStar) {
        $stars .= '☆'; // Nửa sao (hiển thị như sao rỗng, có thể dùng CSS để style)
    }
    $stars .= str_repeat('☆', $emptyStars); // Sao rỗng
    
    return $stars;
}

?>
