<?php
/**
 * News Page - Danh sách tin tức
 * Hiển thị news từ database với pagination
 */

require_once '../../functions.php';

// Get parameters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 6; // Hiển thị 6 bài viết mỗi trang

// Get total news count
$pdo = getDBConnection();
$countStmt = $pdo->query("SELECT COUNT(*) as total FROM News WHERE TrangThai = 1");
$totalNews = $countStmt->fetch()['total'] ?? 0;
$totalPages = ceil($totalNews / $perPage);

// Get news for current page
$offset = ($page - 1) * $perPage;
$sql = "SELECT * FROM News WHERE TrangThai = 1 ORDER BY NgayTao DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin Tức - MeowTea Fresh</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <!-- News Banner Section -->
    <section class="news-banner-section" style="background-image: url('../../assets/img/news/news_banner.jpg'); background-size: cover; background-position: center; padding: 80px 0; margin-bottom: 40px;">
        <div class="container">
            <h1 class="section-title" style="color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">Tin Tức & Sự Kiện</h1>
            <p class="section-subtitle" style="color: white; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);">Cập nhật những thông tin mới nhất từ MeowTea Fresh</p>
        </div>
    </section>

    <!-- News Content Section -->
    <section class="news-content-section">
        <div class="container">
            <?php if (!empty($news)): ?>
                <div class="news-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-bottom: 40px;">
                    <?php foreach ($news as $newsItem): ?>
                        <?php 
                            $news = $newsItem;
                            include '../../components/news-card.php'; 
                        ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 0): ?>
                    <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin: 40px 0;">
                        <?php if ($page > 0): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="pagination-btn" style="display: flex; align-items: center; gap: 5px; padding: 10px 20px; background-color: var(--primary-green); color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15 18l-6-6 6-6"/>
                                </svg>
                                Trước
                            </a>
                        <?php endif; ?>

                        <div class="pagination-numbers" style="display: flex; gap: 5px;">
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            if ($startPage > 1): ?>
                                <a href="?page=1" class="pagination-number" style="padding: 10px 15px; background-color: #f0f0f0; color: var(--text-dark); text-decoration: none; border-radius: 5px; transition: background-color 0.3s;">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span class="pagination-dots" style="padding: 10px 5px;">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" 
                                   class="pagination-number <?php echo $i == $page ? 'active' : ''; ?>"
                                   style="padding: 10px 15px; background-color: <?php echo $i == $page ? 'var(--primary-green)' : '#f0f0f0'; ?>; color: <?php echo $i == $page ? 'white' : 'var(--text-dark)'; ?>; text-decoration: none; border-radius: 5px; transition: background-color 0.3s;">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="pagination-dots" style="padding: 10px 5px;">...</span>
                                <?php endif; ?>
                                <a href="?page=<?php echo $totalPages; ?>" class="pagination-number" style="padding: 10px 15px; background-color: #f0f0f0; color: var(--text-dark); text-decoration: none; border-radius: 5px; transition: background-color 0.3s;"><?php echo $totalPages; ?></a>
                            <?php endif; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="pagination-btn" style="display: flex; align-items: center; gap: 5px; padding: 10px 20px; background-color: var(--primary-green); color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s;">
                                Sau
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 18l6-6-6-6"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="pagination-info" style="text-align: center; color: var(--text-light); margin-bottom: 40px;">
                        Trang <?php echo $page; ?> trên <?php echo $totalPages; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-news" style="text-align: center; padding: 60px 20px;">
                    <p style="font-size: 18px; color: var(--text-light); margin-bottom: 20px;">Chưa có tin tức nào.</p>
                </div>
            <?php endif; ?>
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
