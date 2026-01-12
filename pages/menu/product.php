<?php
/**
 * Product Detail Page - Customize sản phẩm
 * Hiển thị chi tiết sản phẩm và cho phép chọn đá, đường, topping
 */

require_once '../../functions.php';

// Get product ID
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$productId) {
    header('Location: index.php');
    exit;
}

// Get product data from database
$product = getProductById($productId);

if (!$product) {
    header('Location: index.php');
    exit;
}

// Get product options from database
$optionsData = getProductOptions($productId);

// Group options by option group
$optionGroups = [];
foreach ($optionsData as $option) {
    $groupId = $option['MaOptionGroup'];
    if (!isset($optionGroups[$groupId])) {
        $optionGroups[$groupId] = [
            'MaOptionGroup' => $option['MaOptionGroup'],
            'TenNhom' => $option['TenNhom'],
            'IsMultiple' => $option['IsMultiple'],
            'options' => []
        ];
    }
    $optionGroups[$groupId]['options'][] = [
        'MaOptionValue' => $option['MaOptionValue'],
        'TenGiaTri' => $option['TenGiaTri'],
        'GiaThem' => $option['GiaThem']
    ];
}

// Xử lý đường dẫn hình ảnh - từ pages/menu/ cần ../../ để về root
$imagePath = !empty($product['HinhAnh']) ? $product['HinhAnh'] : 'assets/img/products/product_one.png';
// Đảm bảo đường dẫn luôn từ root
if (strpos($imagePath, '/') !== 0 && strpos($imagePath, 'http') !== 0) {
    // Nếu chưa có prefix, thêm ../../ để về root từ pages/menu/
    $productImage = '../../' . ltrim($imagePath, './');
} else {
    $productImage = $imagePath;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($product['TenSP']); ?> - MeowTea Fresh</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/menu.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <section class="product-detail-section">
        <div class="container">
            <div class="product-detail-layout">
                <!-- Product Image -->
                <div class="product-detail-image-wrapper">
                    <img src="<?php echo e($productImage); ?>" alt="<?php echo e($product['TenSP']); ?>" class="product-detail-image" onerror="this.src='../../assets/img/products/product_one.png'">
                </div>

                <!-- Product Info & Customization -->
                <div class="product-detail-info">
                    <h1><?php echo e($product['TenSP']); ?></h1>
                    
                    <div class="product-detail-price">
                        <span class="current-price-large"><?php echo formatCurrency($product['GiaCoBan']); ?></span>
                        <span class="old-price-large"><?php echo formatCurrency($product['GiaCoBan'] * 1.3); ?></span>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="quantity-selector">
                        <button type="button" class="quantity-btn" id="decrease-qty">-</button>
                        <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="10">
                        <button type="button" class="quantity-btn" id="increase-qty">+</button>
                    </div>

                    <!-- Options Form -->
                    <form id="product-customize-form">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                        <input type="hidden" name="base_price" value="<?php echo $product['GiaCoBan']; ?>">

                        <?php foreach ($optionGroups as $group): ?>
                            <div class="option-group">
                                <h3 class="option-group-title">Chọn <?php echo e($group['TenNhom']); ?></h3>
                                <div class="option-list">
                                    <?php foreach ($group['options'] as $option): ?>
                                        <div class="option-item">
                                            <?php if ($group['IsMultiple']): ?>
                                                <!-- Checkbox for multiple selection (Topping) -->
                                                <input 
                                                    type="checkbox" 
                                                    name="options[]" 
                                                    id="option_<?php echo $option['MaOptionValue']; ?>"
                                                    value="<?php echo $option['MaOptionValue']; ?>"
                                                    data-price="<?php echo $option['GiaThem']; ?>"
                                                    class="option-checkbox"
                                                >
                                            <?php else: ?>
                                                <!-- Radio for single selection (Đá, Đường) -->
                                                <input 
                                                    type="radio" 
                                                    name="option_group_<?php echo $group['MaOptionGroup']; ?>" 
                                                    id="option_<?php echo $option['MaOptionValue']; ?>"
                                                    value="<?php echo $option['MaOptionValue']; ?>"
                                                    data-price="<?php echo $option['GiaThem']; ?>"
                                                    class="option-radio"
                                                    <?php echo $option === reset($group['options']) ? 'checked' : ''; ?>
                                                >
                                            <?php endif; ?>
                                            <label for="option_<?php echo $option['MaOptionValue']; ?>">
                                                <?php echo e($option['TenGiaTri']); ?>
                                            </label>
                                            <?php if ($option['GiaThem'] > 0): ?>
                                                <span class="option-price">+<?php echo formatCurrency($option['GiaThem']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Note Section -->
                        <div class="note-section">
                            <label for="product-note" class="note-label">Thêm ghi chú</label>
                            <textarea 
                                id="product-note" 
                                name="note" 
                                class="note-textarea" 
                                placeholder="Nhập nội dung ghi chú cho quán (nếu có)"
                                maxlength="52"
                            ></textarea>
                            <div class="char-count"><span id="char-count">0</span>/52 ký tự</div>
                        </div>

                        <!-- Total Price Display -->
                        <div class="total-price-display" style="margin: 30px 0; padding: 20px; background-color: var(--light-green); border-radius: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 20px; font-weight: bold; color: var(--primary-green);">Tổng tiền:</span>
                                <span id="total-price" style="font-size: 28px; font-weight: bold; color: var(--primary-green);">
                                    <?php echo formatCurrency($product['GiaCoBan']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="product-actions">
                            <button type="button" id="add-to-cart-btn" class="btn-add-cart">
                                Thêm vào giỏ
                            </button>
                            <a href="../../pages/cart/index.php" class="btn-view-cart">
                                Xem giỏ hàng
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include '../../components/footer.php'; ?>

    <script>
        $(document).ready(function() {
            const basePrice = parseFloat($('input[name="base_price"]').val());
            let quantity = parseInt($('#quantity').val());

            // Update total price
            function updateTotalPrice() {
                let total = basePrice;
                
                // Add selected options prices
                $('.option-checkbox:checked, .option-radio:checked').each(function() {
                    total += parseFloat($(this).data('price') || 0);
                });
                
                // Multiply by quantity
                total *= quantity;
                
                // Update display
                $('#total-price').text(formatCurrency(total));
            }

            // Quantity controls
            $('#increase-qty').on('click', function() {
                if (quantity < 10) {
                    quantity++;
                    $('#quantity').val(quantity);
                    updateTotalPrice();
                }
            });

            $('#decrease-qty').on('click', function() {
                if (quantity > 1) {
                    quantity--;
                    $('#quantity').val(quantity);
                    updateTotalPrice();
                }
            });

            $('#quantity').on('change', function() {
                quantity = Math.max(1, Math.min(10, parseInt($(this).val()) || 1));
                $(this).val(quantity);
                updateTotalPrice();
            });

            // Option change handlers
            $('.option-checkbox, .option-radio').on('change', function() {
                $(this).closest('.option-item').toggleClass('selected', $(this).is(':checked'));
                updateTotalPrice();
            });

            // Initialize selected state
            $('.option-radio:checked, .option-checkbox:checked').each(function() {
                $(this).closest('.option-item').addClass('selected');
            });

            // Note character counter
            $('#product-note').on('input', function() {
                const length = $(this).val().length;
                $('#char-count').text(length);
            });

            // Add to cart
            $('#add-to-cart-btn').on('click', function() {
                const options = [];
                
                // Collect selected options
                $('.option-checkbox:checked, .option-radio:checked').each(function() {
                    options.push({
                        option_value_id: $(this).val(),
                        price: parseFloat($(this).data('price') || 0)
                    });
                });

                // Calculate total
                let total = basePrice;
                options.forEach(function(opt) {
                    total += opt.price;
                });
                total *= quantity;

                const formData = {
                    product_id: $('input[name="product_id"]').val(),
                    quantity: quantity,
                    options: JSON.stringify(options), // Send as JSON string
                    note: $('#product-note').val(),
                    base_price: basePrice,
                    total_price: total
                };

                // Send to cart API
                $.ajax({
                    url: '../../api/cart/add.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Update cart count
                            if (response.cart_count !== undefined) {
                                $('.cart-count').text(response.cart_count);
                            }
                        } else {
                            alert('Có lỗi xảy ra: ' + (response.message || 'Vui lòng thử lại'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                });
            });

            // Format currency helper
            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
            }
        });
    </script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>
