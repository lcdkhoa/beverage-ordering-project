<?php
/**
 * Management Create Promotion API
 * Create new promotion (Admin only)
 */

header('Content-Type: application/json');
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => '', 'promotion_id' => null];

try {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        throw new Exception('Bạn cần đăng nhập để thực hiện thao tác này');
    }

    // Check if user has Admin role
    $userRole = $_SESSION['user_role_name'] ?? '';
    if ($userRole !== 'Admin') {
        throw new Exception('Chỉ Admin mới có quyền thêm khuyến mãi mới');
    }

    // Get POST data
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    $loaiGiamGia = isset($_POST['loai_giam_gia']) ? trim($_POST['loai_giam_gia']) : 'Percentage';
    $giaTri = isset($_POST['gia_tri']) ? trim($_POST['gia_tri']) : '';
    $ngayBatDau = isset($_POST['ngay_bat_dau']) ? trim($_POST['ngay_bat_dau']) : null;
    $ngayKetThuc = isset($_POST['ngay_ket_thuc']) ? trim($_POST['ngay_ket_thuc']) : null;
    $trangThai = isset($_POST['trang_thai']) ? (int)$_POST['trang_thai'] : 1;

    // Validation
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

    // Check if code already exists
    $stmt = $pdo->prepare("SELECT MaPromotion FROM Promotion WHERE Code = ?");
    $stmt->execute([$code]);
    if ($stmt->fetch()) {
        throw new Exception('Mã khuyến mãi đã tồn tại');
    }

    // Convert dates to proper format
    $ngayBatDauFormatted = !empty($ngayBatDau) ? date('Y-m-d H:i:s', strtotime($ngayBatDau)) : null;
    $ngayKetThucFormatted = !empty($ngayKetThuc) ? date('Y-m-d H:i:s', strtotime($ngayKetThuc)) : null;

    // Insert new promotion
    $sql = "INSERT INTO Promotion (Code, LoaiGiamGia, GiaTri, NgayBatDau, NgayKetThuc, TrangThai) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$code, $loaiGiamGia, $giaTri, $ngayBatDauFormatted, $ngayKetThucFormatted, $trangThai]);

    $promotionId = $pdo->lastInsertId();

    $response = [
        'success' => true,
        'message' => 'Thêm khuyến mãi mới thành công',
        'promotion_id' => $promotionId
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in create promotion: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
