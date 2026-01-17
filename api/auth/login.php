<?php
/**
 * Login API
 * Xử lý đăng nhập người dùng
 */

header('Content-Type: application/json');
require_once '../../database/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => ''];

try {
    // Get POST data
    $usernameOrEmail = isset($_POST['username_or_email']) ? trim($_POST['username_or_email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validation
    if (empty($usernameOrEmail)) {
        throw new Exception('Vui lòng nhập tên đăng nhập hoặc email');
    }

    if (empty($password)) {
        throw new Exception('Vui lòng nhập mật khẩu');
    }

    // Get database connection
    $pdo = getDBConnection();

    // Find user by username or email
    $sql = "SELECT u.*, r.TenRole 
            FROM User u 
            INNER JOIN Role r ON u.MaRole = r.MaRole 
            WHERE (u.Username = ? OR u.Email = ?) AND u.TrangThai = 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Tên đăng nhập/email hoặc mật khẩu không đúng');
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
        throw new Exception('Tên đăng nhập/email hoặc mật khẩu không đúng');
    }

    // Login successful - set session
    $fullName = trim($user['Ho'] . ' ' . $user['Ten']);
    $_SESSION['user_id'] = $user['MaUser'];
    $_SESSION['username'] = $user['Username'];
    $_SESSION['user_ho'] = $user['Ho'];
    $_SESSION['user_ten'] = $user['Ten'];
    $_SESSION['user_name'] = $fullName;
    $_SESSION['user_gioi_tinh'] = $user['GioiTinh'] ?? null;
    $_SESSION['user_email'] = $user['Email'];
    $_SESSION['user_phone'] = $user['DienThoai'];
    $_SESSION['user_dia_chi'] = $user['DiaChi'] ?? '';
    $_SESSION['user_role'] = $user['MaRole'];
    $_SESSION['user_role_name'] = $user['TenRole'];
    $_SESSION['logged_in'] = true;

    $response = [
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'user' => [
            'id' => $user['MaUser'],
            'username' => $user['Username'],
            'ho' => $user['Ho'],
            'ten' => $user['Ten'],
            'name' => $fullName,
            'gioi_tinh' => $user['GioiTinh'] ?? null,
            'email' => $user['Email'],
            'phone' => $user['DienThoai'],
            'dia_chi' => $user['DiaChi'] ?? '',
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