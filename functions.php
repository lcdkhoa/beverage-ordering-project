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
?>
