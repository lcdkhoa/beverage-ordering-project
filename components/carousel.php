<?php
/**
 * Carousel Component
 * Reusable carousel với navigation và pagination dots
 * 
 * @param array $images - Mảng đường dẫn hình ảnh
 * @param string $carouselId - ID duy nhất cho carousel (mặc định: 'hero-carousel')
 * @param int $autoPlayInterval - Thời gian tự động chuyển slide (ms, mặc định: 500)
 */
if (!isset($images) || empty($images)) {
    return;
}

$carouselId = $carouselId ?? 'hero-carousel';
$autoPlayInterval = 3000;
?>
<div class="carousel-container" id="<?php echo htmlspecialchars($carouselId); ?>">
    <div class="carousel-wrapper">
        <div class="carousel-slides">
            <?php foreach ($images as $index => $image): ?>
                <div class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Carousel Image <?php echo $index + 1; ?>">
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Navigation Buttons -->
        <button class="carousel-btn carousel-prev" aria-label="Previous slide">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        <button class="carousel-btn carousel-next" aria-label="Next slide">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
    </div>
    
    <!-- Pagination Dots -->
    <div class="carousel-pagination">
        <?php foreach ($images as $index => $image): ?>
            <button class="carousel-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" aria-label="Go to slide <?php echo $index + 1; ?>"></button>
        <?php endforeach; ?>
    </div>
</div>

<script>
(function() {
    const carouselId = '<?php echo htmlspecialchars($carouselId); ?>';
    const autoPlayInterval = <?php echo (int)$autoPlayInterval; ?>;
    const $carousel = $('#' + carouselId);
    
    if ($carousel.length === 0) return;
    
    const $wrapper = $carousel.find('.carousel-wrapper');
    const $slidesContainer = $carousel.find('.carousel-slides');
    const $slides = $carousel.find('.carousel-slide');
    const $dots = $carousel.find('.carousel-dot');
    const $prevBtn = $carousel.find('.carousel-prev');
    const $nextBtn = $carousel.find('.carousel-next');
    const totalSlides = $slides.length;

    let fixedHeight = null;
    function setCarouselHeight() {
        // Calculate height only once on first load
        if (fixedHeight === null) {
            const header = $('.main-header');
            const headerHeight = header.length > 0 ? header.outerHeight() : 0;
            const viewportHeight = window.innerHeight;
            fixedHeight = viewportHeight - headerHeight;
            
            $('.hero-section').css('height', fixedHeight + 'px');
        }
        
        
        $wrapper.css('height', fixedHeight + 'px');
        $slidesContainer.css('height', fixedHeight + 'px');
    }
    
    setCarouselHeight();
    
    
    let resizeTimeout = null;
    $(window).on('resize', function() {
        if (resizeTimeout) {
            clearTimeout(resizeTimeout);
        }
        resizeTimeout = setTimeout(function() {
            fixedHeight = null;
            setCarouselHeight();
            resizeTimeout = null;
        }, 250);
    });
    
    if (totalSlides <= 1) return;
    
    let currentIndex = 0;
    let autoPlayTimer = null;
    
    function showSlide(index) {
        // Ensure index is within bounds
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;
        
        currentIndex = index;
        
        // Update slides
        $slides.removeClass('active');
        $slides.eq(index).addClass('active');
        
        // Update dots
        $dots.removeClass('active');
        $dots.eq(index).addClass('active');
    }
    
    function nextSlide() {
        showSlide(currentIndex + 1);
    }
    
    function prevSlide() {
        showSlide(currentIndex - 1);
    }
    
    function startAutoPlay() {
        stopAutoPlay();
        autoPlayTimer = setInterval(nextSlide, autoPlayInterval);
    }
    
    function stopAutoPlay() {
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer);
            autoPlayTimer = null;
        }
    }
    
    // Navigation buttons
    $nextBtn.on('click', function() {
        nextSlide();
        startAutoPlay();
    });
    
    $prevBtn.on('click', function() {
        prevSlide();
        startAutoPlay();
    });
    
    // Pagination dots
    $dots.on('click', function() {
        const index = parseInt($(this).data('index'));
        showSlide(index);
        startAutoPlay();
    });
    
    // Pause on hover
    $carousel.on('mouseenter', stopAutoPlay);
    $carousel.on('mouseleave', startAutoPlay);
    
    // Initialize
    showSlide(0);
    startAutoPlay();
})();
</script>
