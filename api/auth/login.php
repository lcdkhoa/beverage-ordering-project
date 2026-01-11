<?php
/**
 * Login API
 * Xử lý đăng nhập người dùng
 */

header('Content-Type: application/json');
require_once '../../database/config.php';

session_start();

$response = ['success' => false, 'message' => ''];

try {
    // Get POST data
    $emailOrPhone = isset($_POST['email_or_phone']) ? trim($_POST['email_or_phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validation
    if (empty($emailOrPhone)) {
        throw new Exception('Vui lòng nhập email hoặc số điện thoại');
    }

    if (empty($password)) {
        throw new Exception('Vui lòng nhập mật khẩu');
    }

    // Get database connection
    $pdo = getDBConnection();

    // Find user by email or phone
    $sql = "SELECT u.*, r.TenRole 
            FROM User u 
            INNER JOIN Role r ON u.MaRole = r.MaRole 
            WHERE (u.Email = ? OR u.DienThoai = ?) AND u.TrangThai = 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$emailOrPhone, $emailOrPhone]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Email/số điện thoại hoặc mật khẩu không đúng');
    }

    // Verify password
    // Note: Trong seed data, password là plain text, nên tạm thời so sánh trực tiếp
    // Trong production, cần hash password bằng password_hash() và verify bằng password_verify()
    $passwordMatch = false;
    
    // Check if password is hashed (starts with $2y$ for bcrypt)
    if (strpos($user['Password'], '$2y$') === 0) {
        // Password is hashed, use password_verify
        $passwordMatch = password_verify($password, $user['Password']);
    } else {
        // Password is plain text (for demo), compare directly
        $passwordMatch = ($user['Password'] === $password);
    }

    if (!$passwordMatch) {
        throw new Exception('Email/số điện thoại hoặc mật khẩu không đúng');
    }

    // Login successful - set session
    $_SESSION['user_id'] = $user['MaUser'];
    $_SESSION['username'] = $user['Username'];
    $_SESSION['user_name'] = $user['HoTen'];
    $_SESSION['user_email'] = $user['Email'];
    $_SESSION['user_phone'] = $user['DienThoai'];
    $_SESSION['user_role'] = $user['MaRole'];
    $_SESSION['user_role_name'] = $user['TenRole'];
    $_SESSION['logged_in'] = true;

    $response = [
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'user' => [
            'id' => $user['MaUser'],
            'username' => $user['Username'],
            'name' => $user['HoTen'],
            'email' => $user['Email'],
            'phone' => $user['DienThoai'],
            'role' => $user['TenRole']
        ]
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    error_log("Database error in login: " . $e->getMessage());
    $response['message'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;