<?php
/**
 * Register API
 * Xử lý đăng ký người dùng mới
 */

header('Content-Type: application/json');
require_once '../../database/config.php';
require_once '../../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => ''];

try {
    // Get POST data
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $ho = isset($_POST['ho']) ? trim($_POST['ho']) : '';
    $ten = isset($_POST['ten']) ? trim($_POST['ten']) : '';
    $gioiTinh = null; // Gender field removed from form
    $dienThoai = isset($_POST['dien_thoai']) ? trim($_POST['dien_thoai']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;

    // Validation
    if (empty($username)) {
        throw new Exception('Vui lòng nhập tên đăng nhập');
    }

    if (strlen($username) < 3 || strlen($username) > 100) {
        throw new Exception('Tên đăng nhập phải có từ 3 đến 100 ký tự');
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới');
    }

    if (empty($password)) {
        throw new Exception('Vui lòng nhập mật khẩu');
    }

    if (strlen($password) < 6) {
        throw new Exception('Mật khẩu phải có ít nhất 6 ký tự');
    }

    if (empty($ho)) {
        throw new Exception('Vui lòng nhập họ');
    }

    if (strlen($ho) > 50) {
        throw new Exception('Họ không được vượt quá 50 ký tự');
    }

    if (empty($ten)) {
        throw new Exception('Vui lòng nhập tên');
    }

    if (strlen($ten) > 50) {
        throw new Exception('Tên không được vượt quá 50 ký tự');
    }

    // Validate phone if provided
    if ($dienThoai !== null && !empty($dienThoai)) {
        if (strlen($dienThoai) > 20) {
            throw new Exception('Số điện thoại không được vượt quá 20 ký tự');
        }
        // Basic phone validation (numbers and +)
        if (!preg_match('/^[0-9+\-\s()]+$/', $dienThoai)) {
            throw new Exception('Số điện thoại không hợp lệ');
        }
    }

    // Validate email if provided
    if ($email !== null && !empty($email)) {
        if (strlen($email) > 100) {
            throw new Exception('Email không được vượt quá 100 ký tự');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email không hợp lệ');
        }
    }

    // Get database connection
    $pdo = getDBConnection();

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT MaUser FROM [User] WHERE Username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        throw new Exception('Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác');
    }

    // Check if email already exists (if provided)
    if ($email !== null && !empty($email)) {
        $stmt = $pdo->prepare("SELECT MaUser FROM [User] WHERE Email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception('Email đã được sử dụng. Vui lòng sử dụng email khác');
        }
    }

    // Check if phone already exists (if provided)
    if ($dienThoai !== null && !empty($dienThoai)) {
        $stmt = $pdo->prepare("SELECT MaUser FROM [User] WHERE DienThoai = ?");
        $stmt->execute([$dienThoai]);
        if ($stmt->fetch()) {
            throw new Exception('Số điện thoại đã được sử dụng. Vui lòng sử dụng số khác');
        }
    }

    // Get Customer role ID (MaRole = 3)
    $stmt = $pdo->prepare("SELECT TOP 1 MaRole FROM Role WHERE TenRole = 'Customer'");
    $stmt->execute();
    $role = $stmt->fetch();

    if (!$role) {
        throw new Exception('Không tìm thấy role Customer. Vui lòng liên hệ quản trị viên');
    }

    $maRole = $role['MaRole'];

    // Hash password
    $hashedPassword = hashPassword($password);

    // Insert new user
    $sql = "INSERT INTO [User] (Username, Password, Ho, Ten, GioiTinh, DienThoai, Email, TrangThai, MaRole) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $username,
        $hashedPassword,
        $ho,
        $ten,
        $gioiTinh,
        $dienThoai,
        $email,
        $maRole
    ]);

    $newUserId = $pdo->lastInsertId();

    // Auto login after registration
    $fullName = trim($ho . ' ' . $ten);
    $_SESSION['user_id'] = $newUserId;
    $_SESSION['username'] = $username;
    $_SESSION['user_ho'] = $ho;
    $_SESSION['user_ten'] = $ten;
    $_SESSION['user_name'] = $fullName;
    $_SESSION['user_gioi_tinh'] = $gioiTinh;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_phone'] = $dienThoai;
    $_SESSION['user_role'] = $maRole;
    $_SESSION['user_role_name'] = 'Customer';
    $_SESSION['logged_in'] = true;

    $response = [
        'success' => true,
        'message' => 'Đăng ký thành công! Bạn đã được đăng nhập tự động.',
        'user' => [
            'id' => $newUserId,
            'username' => $username,
            'ho' => $ho,
            'ten' => $ten,
            'name' => $fullName,
            'gioi_tinh' => $gioiTinh,
            'email' => $email,
            'phone' => $dienThoai,
            'role' => 'Customer'
        ]
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in register: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
