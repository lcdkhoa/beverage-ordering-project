<?php
/**
 * Pagination Component
 * Reusable pagination component với button component và số trang hình tròn
 * 
 * @param int $page - Current page number
 * @param int $totalPages - Total number of pages
 * @param string $baseUrl - Base URL for pagination links (default: current URL without page param)
 * @param array $queryParams - Additional query parameters to preserve (e.g., ['category' => '1', 'search' => 'keyword'])
 */
if (!isset($page)) $page = 1;
if (!isset($totalPages)) $totalPages = 1;
if (!isset($queryParams)) $queryParams = [];

// Build base URL
if (!isset($baseUrl)) {
    $baseUrl = $_SERVER['PHP_SELF'];
}

// Helper function to build pagination URL (scoped to avoid conflicts)
if (!function_exists('buildPaginationUrl')) {
    function buildPaginationUrl($pageNum, $baseUrl, $queryParams) {
        $params = array_merge($queryParams, ['page' => $pageNum]);
        $queryString = http_build_query($params);
        return $baseUrl . '?' . $queryString;
    }
}

// Calculate page range
$startPage = max(1, $page - 2);
$endPage = min($totalPages, $page + 2);
?>

<div class="pagination">
    <?php if ($page > 1): ?>
        <?php 
            $text = 'Trước';
            $type = 'outline';
            $href = buildPaginationUrl($page - 1, $baseUrl, $queryParams);
            $class = 'pagination-btn';
            $icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>';
            $iconPosition = 'left';
            $width = 'auto';
            include __DIR__ . '/button.php';
        ?>
    <?php endif; ?>

    <div class="pagination-numbers">
        <?php if ($startPage > 1): ?>
            <a href="<?php echo buildPaginationUrl(1, $baseUrl, $queryParams); ?>" class="pagination-number">
                <?php echo 1; ?>
            </a>
            <?php if ($startPage > 2): ?>
                <span class="pagination-dots">...</span>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="<?php echo buildPaginationUrl($i, $baseUrl, $queryParams); ?>" 
               class="pagination-number <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?>
                <span class="pagination-dots">...</span>
            <?php endif; ?>
            <a href="<?php echo buildPaginationUrl($totalPages, $baseUrl, $queryParams); ?>" class="pagination-number">
                <?php echo $totalPages; ?>
            </a>
        <?php endif; ?>
    </div>

    <?php if ($page < $totalPages): ?>
        <?php 
            $text = 'Sau';
            $type = 'outline';
            $href = buildPaginationUrl($page + 1, $baseUrl, $queryParams);
            $class = 'pagination-btn';
            $icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>';
            $iconPosition = 'right';
            $width = 'auto';
            include __DIR__ . '/button.php';
        ?>
    <?php endif; ?>
</div>
