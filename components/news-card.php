<?php
/**
 * News Card Component
 * Reusable news card với hình ảnh, ngày, tiêu đề, mô tả
 * 
 * @param array $news - News data from database
 */
if (!isset($news)) return;

$newsTitle = e($news['TieuDe']);
$newsImage = !empty($news['HinhAnh']) ? e($news['HinhAnh']) : 'assets/img/news/news_one.jpg';
$newsDate = !empty($news['NgayTao']) ? date('d', strtotime($news['NgayTao'])) : '24';
$newsMonth = !empty($news['NgayTao']) ? date('M', strtotime($news['NgayTao'])) : 'THG 12';
$newsId = $news['MaNews'];
$newsExcerpt = !empty($news['NoiDung']) ? substr(strip_tags($news['NoiDung']), 0, 100) . '...' : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...';
?>
<div class="news-card">
    <div class="news-image-wrapper">
        <img src="<?php echo $newsImage; ?>" alt="<?php echo $newsTitle; ?>" class="news-image">
        <div class="news-date-badge">
            <span class="date-day"><?php echo $newsDate; ?></span>
            <span class="date-month"><?php echo $newsMonth; ?></span>
        </div>
    </div>
    <div class="news-content">
        <h3 class="news-title">
            <a href="pages/news/details.php?id=<?php echo $newsId; ?>"><?php echo $newsTitle; ?></a>
        </h3>
        <p class="news-excerpt"><?php echo e($newsExcerpt); ?></p>
        <a href="pages/news/details.php?id=<?php echo $newsId; ?>" class="news-read-more">
            Đọc tiếp →
        </a>
    </div>
</div>
