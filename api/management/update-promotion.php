<?php
/**
 * Management Update Promotion API
 * Update promotion (Admin only)
 */

header('Content-Type: application/json');
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => ''];

try {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        throw new Exception('Bạn cần đăng nhập để thực hiện thao tác này');
    }

    // Check if user has Admin role
    $userRole = $_SESSION['user_role_name'] ?? '';
    if ($userRole !== 'Admin') {
        throw new Exception('Chỉ Admin mới có quyền cập nhật khuyến mãi');
    }

    // Get POST data
    $promotionId = isset($_POST['promotion_id']) ? (int)$_POST['promotion_id'] : 0;
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    $loaiGiamGia = isset($_POST['loai_giam_gia']) ? trim($_POST['loai_giam_gia']) : 'Percentage';
    $giaTri = isset($_POST['gia_tri']) ? trim($_POST['gia_tri']) : '';
    $ngayBatDau = isset($_POST['ngay_bat_dau']) ? trim($_POST['ngay_bat_dau']) : null;
    $ngayKetThuc = isset($_POST['ngay_ket_thuc']) ? trim($_POST['ngay_ket_thuc']) : null;
    $trangThai = isset($_POST['trang_thai']) ? (int)$_POST['trang_thai'] : 1;

    // Validation
    if (!$promotionId) {
        throw new Exception('Mã khuyến mãi không hợp lệ');
    }

    if (empty($code)) {
        throw new Exception('Vui lòng nhập mã khuyến mãi');
    }

    if (empty($giaTri) || !is_numeric($giaTri) || $giaTri < 0) {
        throw new Exception('Giá trị giảm giá không hợp lệ');
    }

    if ($loaiGiamGia === 'Percentage' && ($giaTri > 100 || $giaTri < 0)) {
        throw new Exception('Phần trăm giảm giá phải từ 0 đến 100');
    }

    // Validate dates
    if (!empty($ngayBatDau) && !empty($ngayKetThuc)) {
        $startDate = strtotime($ngayBatDau);
        $endDate = strtotime($ngayKetThuc);
        if ($startDate === false || $endDate === false) {
            throw new Exception('Ngày tháng không hợp lệ');
        }
        if ($endDate < $startDate) {
            throw new Exception('Ngày kết thúc phải sau ngày bắt đầu');
        }
    }

    // Get database connection
    $pdo = getDBConnection();

    // Check if promotion exists
    $stmt = $pdo->prepare("SELECT MaPromotion FROM Promotion WHERE MaPromotion = ?");
    $stmt->execute([$promotionId]);
    if (!$stmt->fetch()) {
        throw new Exception('Không tìm thấy khuyến mãi');
    }

    // Check if code already exists (excluding current promotion)
    $stmt = $pdo->prepare("SELECT MaPromotion FROM Promotion WHERE Code = ? AND MaPromotion != ?");
    $stmt->execute([$code, $promotionId]);
    if ($stmt->fetch()) {
        throw new Exception('Mã khuyến mãi đã tồn tại');
    }

    // Convert dates to proper format
    $ngayBatDauFormatted = !empty($ngayBatDau) ? date('Y-m-d H:i:s', strtotime($ngayBatDau)) : null;
    $ngayKetThucFormatted = !empty($ngayKetThuc) ? date('Y-m-d H:i:s', strtotime($ngayKetThuc)) : null;

    // Update promotion
    $sql = "UPDATE Promotion 
            SET Code = ?, LoaiGiamGia = ?, GiaTri = ?, NgayBatDau = ?, NgayKetThuc = ?, TrangThai = ?
            WHERE MaPromotion = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$code, $loaiGiamGia, $giaTri, $ngayBatDauFormatted, $ngayKetThucFormatted, $trangThai, $promotionId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Không có thay đổi nào được cập nhật');
    }

    $response = [
        'success' => true,
        'message' => 'Cập nhật khuyến mãi thành công'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in update promotion: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
