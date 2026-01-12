<?php
/**
 * Get Product Data API
 * Lấy thông tin sản phẩm và options để hiển thị trong modal
 */

header('Content-Type: application/json');
require_once '../../functions.php';

$response = ['success' => false, 'data' => null, 'message' => ''];

try {
    $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$productId) {
        throw new Exception('Product ID is required');
    }
    
    // Get product data
    $product = getProductById($productId);
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Get product options
    $optionsData = getProductOptions($productId);
    
    // Group options by option group
    $optionGroups = [];
    foreach ($optionsData as $option) {
        $groupId = $option['MaOptionGroup'];
        if (!isset($optionGroups[$groupId])) {
            $optionGroups[$groupId] = [
                'MaOptionGroup' => $option['MaOptionGroup'],
                'TenNhom' => $option['TenNhom'],
                'IsMultiple' => (bool)$option['IsMultiple'],
                'options' => []
            ];
        }
        $optionGroups[$groupId]['options'][] = [
            'MaOptionValue' => $option['MaOptionValue'],
            'TenGiaTri' => $option['TenGiaTri'],
            'GiaThem' => (float)$option['GiaThem']
        ];
    }
    
    // Format response
    $response = [
        'success' => true,
        'data' => [
            'product' => [
                'MaSP' => $product['MaSP'],
                'TenSP' => $product['TenSP'],
                'GiaCoBan' => (float)$product['GiaCoBan'],
                'HinhAnh' => $product['HinhAnh'] ?? 'assets/img/products/product_one.png'
            ],
            'optionGroups' => array_values($optionGroups)
        ]
    ];
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
