<?php
// login_process.php - Handle user authentication
session_start();
require_once 'config.php';

// Security headers
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Rate limiting for login attempts
    $ip = $_SERVER['REMOTE_ADDR'];
    $rate_limit_key = "login_attempts_$ip";
    
    // Check failed login attempts in the last hour
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM audit_logs 
                          WHERE action = 'LOGIN_FAILED' 
                          AND ip_address = ? 
                          AND created_at > NOW() - INTERVAL 1 HOUR");
    $stmt->execute([$ip]);
    $failed_attempts = $stmt->fetchColumn();
    
    if ($failed_attempts >= 5) {
        logAuditAction(null, 'LOGIN_BLOCKED', 'users', null, ['reason' => 'Too many failed attempts'], $ip);
        throw new Exception('Terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam 1 jam.');
    }

    // Validate input
    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['role'])) {
        throw new Exception('Username, password, dan role wajib diisi.');
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $remember_me = isset($_POST['remember']) && $_POST['remember'] === 'on';

    // Validate role
    $allowed_roles = ['admin', 'panitia', 'santri', 'affiliate'];
    if (!in_array($role, $allowed_roles)) {
        throw new Exception('Role tidak valid.');
    }

    // Basic input validation
    if (empty($username) || empty($password)) {
        throw new Exception('Username dan password tidak boleh kosong.');
    }

    if (strlen($username) > 50 || strlen($password) > 255) {
        throw new Exception('Username atau password terlalu panjang.');
    }

    // Find user based on role and username
    $user = findUserByCredentials($username, $role);
    
    if (!$user) {
        logAuditAction(null, 'LOGIN_FAILED', 'users', null, [
            'username' => $username, 
            'role' => $role, 
            'reason' => 'User not found'
        ], $ip);
        throw new Exception('Username, password, atau role tidak valid.');
    }

    // Check if user is active
    if (!$user['is_active']) {
        logAuditAction($user['id'], 'LOGIN_FAILED', 'users', $user['id'], [
            'reason' => 'Account inactive'
        ], $ip);
        throw new Exception('Akun Anda telah dinonaktifkan. Hubungi administrator.');
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        logAuditAction($user['id'], 'LOGIN_FAILED', 'users', $user['id'], [
            'username' => $username,
            'reason' => 'Invalid password'
        ], $ip);
        throw new Exception('Username, password, atau role tidak valid.');
    }

    // Check for password change requirement
    if ($user['must_change_password']) {
        // For users who must change password, create a temporary session
        $_SESSION['temp_user_id'] = $user['id'];
        $_SESSION['must_change_password'] = true;
        
        echo json_encode([
            'success' => true,
            'must_change_password' => true,
            'message' => 'Anda harus mengganti password terlebih dahulu.',
            'redirect' => 'change-password.php'
        ]);
        exit();
    }

    // Successful login - create session
    createUserSession($user, $remember_me);

    // Update last login time
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);

    // Log successful login
    logAuditAction($user['id'], 'LOGIN_SUCCESS', 'users', $user['id'], [
        'username' => $username,
        'role' => $role
    ], $ip);

    // Determine redirect URL based on role
    $redirect_urls = [
        'admin' => 'admin/dashboard.php',
        'panitia' => 'panitia/dashboard.php',
        'santri' => 'santri/dashboard.php',
        'affiliate' => 'affiliate/dashboard.php'
    ];

    // Get additional user info based on role
    $additional_info = getUserAdditionalInfo($user['id'], $role);

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Login berhasil!',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'last_login' => $user['last_login']
        ],
        'additional_info' => $additional_info,
        'redirect' => $redirect_urls[$role]
    ]);

} catch (Exception $e) {
    // Log error
    error_log("Login error: " . $e->getMessage());
    
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Find user by credentials and role
 */
function findUserByCredentials($username, $role) {
    global $pdo;
    
    // For santri role, also check if username is a registration code
    if ($role === 'santri') {
        $stmt = $pdo->prepare("
            SELECT u.*, s.registration_code, s.full_name as student_name 
            FROM users u 
            LEFT JOIN students s ON u.id = s.user_id 
            WHERE (u.username = ? OR s.registration_code = ?) 
            AND u.role = 'santri'
        ");
        $stmt->execute([$username, $username]);
    } else {
        // For other roles
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
        $stmt->execute([$username, $role]);
    }
    
    return $stmt->fetch();
}

/**
 * Create user session
 */
function createUserSession($user, $remember_me = false) {
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    
    // Set session timeout (2 hours by default)
    $session_timeout = getSetting('session_lifetime', 7200);
    $_SESSION['session_timeout'] = $session_timeout;
    
    // For remember me functionality
    if ($remember_me) {
        $remember_token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Store remember token in database
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO user_remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $remember_token, date('Y-m-d H:i:s', $expires)]);
        
        // Set cookie
        setcookie('remember_token', $remember_token, $expires, '/', '', false, true);
    }
    
    // Additional session data based on role
    if ($user['role'] === 'santri') {
        // Get student-specific data
        $stmt = $pdo->prepare("SELECT registration_code, education_level_id, status FROM students WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $student_data = $stmt->fetch();
        
        if ($student_data) {
            $_SESSION['registration_code'] = $student_data['registration_code'];
            $_SESSION['education_level_id'] = $student_data['education_level_id'];
            $_SESSION['student_status'] = $student_data['status'];
        }
    }
}

/**
 * Get additional user information based on role
 */
function getUserAdditionalInfo($user_id, $role) {
    global $pdo;
    
    $info = [];
    
    switch ($role) {
        case 'santri':
            $stmt = $pdo->prepare("
                SELECT s.*, el.name as education_level_name 
                FROM students s 
                JOIN education_levels el ON s.education_level_id = el.id 
                WHERE s.user_id = ?
            ");
            $stmt->execute([$user_id]);
            $student_data = $stmt->fetch();
            
            if ($student_data) {
                $info = [
                    'registration_code' => $student_data['registration_code'],
                    'education_level' => $student_data['education_level_name'],
                    'status' => $student_data['status'],
                    'full_name' => $student_data['full_name']
                ];
            }
            break;
            
        case 'affiliate':
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(s.id) as total_referrals,
                    COUNT(CASE WHEN s.status IN ('paid', 'admitted') THEN 1 END) as active_referrals,
                    COALESCE(SUM(CASE WHEN ac.status = 'pending' THEN ac.commission_amount END), 0) as pending_commission,
                    COALESCE(SUM(CASE WHEN ac.status = 'paid' THEN ac.commission_amount END), 0) as total_earnings
                FROM students s 
                LEFT JOIN affiliate_commissions ac ON s.id = ac.student_id 
                WHERE s.affiliate_id = ?
            ");
            $stmt->execute([$user_id]);
            $affiliate_stats = $stmt->fetch();
            
            $info = [
                'total_referrals' => $affiliate_stats['total_referrals'] ?? 0,
                'active_referrals' => $affiliate_stats['active_referrals'] ?? 0,
                'pending_commission' => $affiliate_stats['pending_commission'] ?? 0,
                'total_earnings' => $affiliate_stats['total_earnings'] ?? 0
            ];
            break;
            
        case 'panitia':
        case 'admin':
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(CASE WHEN s.status = 'pending' THEN 1 END) as pending_verification,
                    COUNT(CASE WHEN s.status = 'verified' THEN 1 END) as verified_students,
                    COUNT(CASE WHEN p.payment_status = 'pending' THEN 1 END) as pending_payments
                FROM students s 
                LEFT JOIN payments p ON s.id = p.student_id AND p.payment_type = 'registration'
            ");
            $stmt->execute();
            $stats = $stmt->fetch();
            
            $info = [
                'pending_verification' => $stats['pending_verification'] ?? 0,
                'verified_students' => $stats['verified_students'] ?? 0,
                'pending_payments' => $stats['pending_payments'] ?? 0
            ];
            break;
    }
    
    return $info;
}

/**
 * Log audit action
 */
function logAuditAction($user_id, $action, $table_name, $record_id, $data = [], $ip = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, action, table_name, record_id, new_data, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $user_id,
            $action,
            $table_name,
            $record_id,
            json_encode($data),
            $ip ?: $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (Exception $e) {
        error_log("Failed to log audit action: " . $e->getMessage());
    }
}

/**
 * Check remember me token and auto-login
 */
function checkRememberMeToken() {
    global $pdo;
    
    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }
    
    $token = $_COOKIE['remember_token'];
    
    // Find valid token
    $stmt = $pdo->prepare("
        SELECT rt.user_id, u.* 
        FROM user_remember_tokens rt 
        JOIN users u ON rt.user_id = u.id 
        WHERE rt.token = ? AND rt.expires_at > NOW() AND u.is_active = 1
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Create session
        createUserSession($user, false);
        
        // Clean up old tokens
        $stmt = $pdo->prepare("DELETE FROM user_remember_tokens WHERE expires_at <= NOW()");
        $stmt->execute();
        
        return true;
    } else {
        // Invalid token, remove cookie
        setcookie('remember_token', '', time() - 3600, '/');
        return false;
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        return checkRememberMeToken();
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity'])) {
        $session_timeout = $_SESSION['session_timeout'] ?? 7200; // 2 hours default
        
        if (time() - $_SESSION['last_activity'] > $session_timeout) {
            // Session expired
            session_destroy();
            return false;
        }
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
    
    return true;
}

/**
 * Logout user
 */
function logoutUser() {
    global $pdo;
    
    // Log logout action
    if (isset($_SESSION['user_id'])) {
        logAuditAction($_SESSION['user_id'], 'LOGOUT', 'users', $_SESSION['user_id']);
        
        // Remove remember me token if exists
        if (isset($_COOKIE['remember_token'])) {
            $stmt = $pdo->prepare("DELETE FROM user_remember_tokens WHERE token = ?");
            $stmt->execute([$_COOKIE['remember_token']]);
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
    
    // Clear session
    session_destroy();
    
    // Clear all cookies
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time() - 3600, '/');
        }
    }
}

// Create remember_tokens table if it doesn't exist
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_remember_tokens (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            token VARCHAR(64) NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_token (token),
            INDEX idx_expires (expires_at)
        )
    ");
} catch (Exception $e) {
    error_log("Failed to create remember_tokens table: " . $e->getMessage());
}
?>