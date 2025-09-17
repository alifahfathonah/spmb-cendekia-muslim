<?php
/**
 * API Handler untuk Sistem SPMB Cendekia Muslim
 * Menangani berbagai permintaan AJAX dan sistem notifikasi
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';
require_once 'functions.php';

// Start session
session_start();

try {
    // Get action from request
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Action tidak ditemukan');
    }

    switch ($action) {
        
        // ===== AUTHENTICATION =====
        case 'login':
            handleLogin();
            break;
            
        case 'logout':
            handleLogout();
            break;
            
        case 'check_session':
            checkSession();
            break;
            
        // ===== STUDENT REGISTRATION =====
        case 'register_student':
            registerStudent();
            break;
            
        case 'get_student_data':
            getStudentData();
            break;
            
        case 'update_student_data':
            updateStudentData();
            break;
            
        case 'upload_document':
            uploadDocument();
            break;
            
        case 'get_student_status':
            getStudentStatus();
            break;
            
        // ===== AFFILIATE SYSTEM =====
        case 'register_affiliate':
            registerAffiliate();
            break;
            
        case 'get_affiliate_data':
            getAffiliateData();
            break;
            
        case 'get_referrals':
            getReferrals();
            break;
            
        case 'get_commissions':
            getCommissions();
            break;
            
        // ===== PAYMENT SYSTEM =====
        case 'process_payment':
            processPayment();
            break;
            
        case 'verify_payment':
            verifyPayment();
            break;
            
        case 'get_payment_history':
            getPaymentHistory();
            break;
            
        case 'get_payment_methods':
            getPaymentMethods();
            break;
            
        // ===== ADMIN FUNCTIONS =====
        case 'get_dashboard_stats':
            getDashboardStats();
            break;
            
        case 'get_students_list':
            getStudentsList();
            break;
            
        case 'approve_student':
            approveStudent();
            break;
            
        case 'reject_student':
            rejectStudent();
            break;
            
        case 'get_payments_list':
            getPaymentsList();
            break;
            
        case 'approve_payment':
            approvePayment();
            break;
            
        case 'reject_payment':
            rejectPayment();
            break;
            
        // ===== NOTIFICATION SYSTEM =====
        case 'get_notifications':
            getNotifications();
            break;
            
        case 'mark_notification_read':
            markNotificationRead();
            break;
            
        case 'mark_all_notifications_read':
            markAllNotificationsRead();
            break;
            
        case 'send_notification':
            sendNotification();
            break;
            
        case 'get_notification_count':
            getNotificationCount();
            break;
            
        // ===== MESSAGE SYSTEM =====
        case 'send_message':
            sendMessage();
            break;
            
        case 'get_messages':
            getMessages();
            break;
            
        case 'get_conversations':
            getConversations();
            break;
            
        case 'mark_message_read':
            markMessageRead();
            break;
            
        // ===== SYSTEM SETTINGS =====
        case 'get_settings':
            getSettings();
            break;
            
        case 'update_settings':
            updateSettings();
            break;
            
        case 'backup_database':
            backupDatabase();
            break;
            
        // ===== REPORTING =====
        case 'generate_report':
            generateReport();
            break;
            
        case 'export_data':
            exportData();
            break;
            
        // ===== GENERAL =====
        case 'search':
            handleSearch();
            break;
            
        case 'get_announcements':
            getAnnouncements();
            break;
            
        default:
            throw new Exception('Action tidak valid: ' . $action);
    }
    
} catch (Exception $e) {
    respondWithError($e->getMessage());
}

// ===== AUTHENTICATION FUNCTIONS =====
function handleLogin() {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if (empty($username) || empty($password)) {
        throw new Exception('Username dan password harus diisi');
    }
    
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->execute([$username, $role]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception('Username atau password salah');
    }
    
    if (!$user['is_active']) {
        throw new Exception('Akun Anda tidak aktif');
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['login_time'] = time();
    
    // Update last login
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    // Log activity
    logActivity($user['id'], 'login', 'User login berhasil');
    
    respondWithSuccess([
        'message' => 'Login berhasil',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name'],
            'role' => $user['role']
        ],
        'redirect' => getDashboardUrl($user['role'])
    ]);
}

function handleLogout() {
    if (isset($_SESSION['user_id'])) {
        logActivity($_SESSION['user_id'], 'logout', 'User logout');
    }
    
    session_destroy();
    respondWithSuccess(['message' => 'Logout berhasil']);
}

function checkSession() {
    if (!isLoggedIn()) {
        respondWithError('Session expired', 401);
    }
    
    respondWithSuccess([
        'valid' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'name' => $_SESSION['name'],
            'role' => $_SESSION['role']
        ]
    ]);
}

// ===== STUDENT REGISTRATION FUNCTIONS =====
function registerStudent() {
    global $pdo;
    
    // Validate required fields
    $required_fields = ['nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 
                       'alamat', 'nama_ayah', 'nama_ibu', 'no_hp_ortu', 'email'];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field {$field} harus diisi");
        }
    }
    
    $pdo->beginTransaction();
    
    try {
        // Create user account
        $username = generateUsername($_POST['nama_lengkap']);
        $password = generateRandomPassword();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (username, password, role, name, email, is_active, created_at)
            VALUES (?, ?, 'santri', ?, ?, 1, NOW())
        ");
        $stmt->execute([$username, $hashed_password, $_POST['nama_lengkap'], $_POST['email']]);
        $user_id = $pdo->lastInsertId();
        
        // Insert student data
        $stmt = $pdo->prepare("
            INSERT INTO students (
                user_id, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin,
                alamat, nama_ayah, nama_ibu, no_hp_ortu, email, status, 
                registration_number, affiliate_code, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, NOW())
        ");
        
        $registration_number = generateRegistrationNumber();
        $affiliate_code = $_POST['affiliate_code'] ?? null;
        
        $stmt->execute([
            $user_id, $_POST['nama_lengkap'], $_POST['tempat_lahir'], 
            $_POST['tanggal_lahir'], $_POST['jenis_kelamin'], $_POST['alamat'],
            $_POST['nama_ayah'], $_POST['nama_ibu'], $_POST['no_hp_ortu'],
            $_POST['email'], $registration_number, $affiliate_code
        ]);
        
        $student_id = $pdo->lastInsertId();
        
        // Handle affiliate commission
        if ($affiliate_code) {
            handleAffiliateCommission($affiliate_code, $student_id);
        }
        
        // Send notification to admin
        createNotification('admin', 'new_registration', 
            "Pendaftar baru: {$_POST['nama_lengkap']} ({$registration_number})");
        
        // Send welcome notification to student
        createNotification($user_id, 'registration_success', 
            "Pendaftaran berhasil! Nomor pendaftaran: {$registration_number}");
        
        $pdo->commit();
        
        // Send WhatsApp notification (if configured)
        sendWhatsAppNotification($_POST['no_hp_ortu'], 
            "Pendaftaran berhasil! Username: {$username}, Password: {$password}, Nomor Pendaftaran: {$registration_number}");
        
        respondWithSuccess([
            'message' => 'Pendaftaran berhasil',
            'data' => [
                'registration_number' => $registration_number,
                'username' => $username,
                'password' => $password,
                'student_id' => $student_id
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function getStudentData() {
    requireLogin();
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT s.*, u.username, u.email as user_email
        FROM students s
        JOIN users u ON s.user_id = u.id
        WHERE s.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        throw new Exception('Data santri tidak ditemukan');
    }
    
    // Get documents
    $stmt = $pdo->prepare("
        SELECT * FROM student_documents 
        WHERE student_id = ? 
        ORDER BY document_type
    ");
    $stmt->execute([$student['id']]);
    $documents = $stmt->fetchAll();
    
    respondWithSuccess([
        'student' => $student,
        'documents' => $documents
    ]);
}

// ===== NOTIFICATION FUNCTIONS =====
function getNotifications() {
    requireLogin();
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    $limit = intval($_GET['limit'] ?? 50);
    $unread_only = $_GET['unread_only'] ?? false;
    
    $sql = "
        SELECT * FROM notifications 
        WHERE user_id = ?
    ";
    
    if ($unread_only) {
        $sql .= " AND is_read = 0";
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $limit]);
    $notifications = $stmt->fetchAll();
    
    respondWithSuccess($notifications);
}

function markNotificationRead() {
    requireLogin();
    global $pdo;
    
    $notification_id = intval($_POST['notification_id']);
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        UPDATE notifications 
        SET is_read = 1, read_at = NOW() 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$notification_id, $user_id]);
    
    respondWithSuccess(['message' => 'Notifikasi ditandai sudah dibaca']);
}

function markAllNotificationsRead() {
    requireLogin();
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        UPDATE notifications 
        SET is_read = 1, read_at = NOW() 
        WHERE user_id = ? AND is_read = 0
    ");
    $stmt->execute([$user_id]);
    
    respondWithSuccess(['message' => 'Semua notifikasi ditandai sudah dibaca']);
}

function getNotificationCount() {
    requireLogin();
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total,
               SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread
        FROM notifications 
        WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);
    $count = $stmt->fetch();
    
    respondWithSuccess($count);
}

// ===== PAYMENT FUNCTIONS =====
function processPayment() {
    requireLogin();
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']);
    $payment_type = $_POST['payment_type']; // 'registration' or 'admission'
    $payment_method = $_POST['payment_method']; // 'bank_transfer', 'va', 'ewallet'
    
    if ($amount <= 0) {
        throw new Exception('Jumlah pembayaran tidak valid');
    }
    
    // Get student data
    $stmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        throw new Exception('Data santri tidak ditemukan');
    }
    
    $payment_code = generatePaymentCode();
    
    $stmt = $pdo->prepare("
        INSERT INTO payments (
            student_id, amount, payment_type, payment_method, payment_code,
            payment_status, created_at
        ) VALUES (?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->execute([
        $student['id'], $amount, $payment_type, $payment_method, $payment_code
    ]);
    
    $payment_id = $pdo->lastInsertId();
    
    // Get payment instructions
    $instructions = getPaymentInstructions($payment_method, $amount, $payment_code);
    
    // Create notification
    createNotification($user_id, 'payment_created', 
        "Pembayaran {$payment_type} sebesar Rp " . number_format($amount, 0, ',', '.') . " telah dibuat");
    
    respondWithSuccess([
        'message' => 'Pembayaran berhasil dibuat',
        'payment_id' => $payment_id,
        'payment_code' => $payment_code,
        'instructions' => $instructions
    ]);
}

// ===== ADMIN FUNCTIONS =====
function getDashboardStats() {
    requireRole(['admin', 'operator']);
    global $pdo;
    
    $stats = [];
    
    // Total students
    $stmt = $pdo->query("SELECT COUNT(*) FROM students");
    $stats['total_students'] = $stmt->fetchColumn();
    
    // Pending approvals
    $stmt = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'pending'");
    $stats['pending_students'] = $stmt->fetchColumn();
    
    // Total payments
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'success'");
    $stats['total_revenue'] = $stmt->fetchColumn();
    
    // Pending payments
    $stmt = $pdo->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'pending'");
    $stats['pending_payments'] = $stmt->fetchColumn();
    
    // Active affiliates
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'affiliate' AND is_active = 1");
    $stats['active_affiliates'] = $stmt->fetchColumn();
    
    // Monthly registration trend (last 6 months)
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count
        FROM students 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month DESC
    ");
    $stats['monthly_trend'] = $stmt->fetchAll();
    
    respondWithSuccess($stats);
}

function getStudentsList() {
    requireRole(['admin', 'operator']);
    global $pdo;
    
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 50);
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    
    $offset = ($page - 1) * $limit;
    
    $sql = "
        SELECT s.*, u.username 
        FROM students s
        JOIN users u ON s.user_id = u.id
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($status) {
        $sql .= " AND s.status = ?";
        $params[] = $status;
    }
    
    if ($search) {
        $sql .= " AND (s.nama_lengkap LIKE ? OR s.registration_number LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }
    
    $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();
    
    // Get total count
    $count_sql = str_replace("SELECT s.*, u.username", "SELECT COUNT(*)", $sql);
    $count_sql = str_replace("LIMIT ? OFFSET ?", "", $count_sql);
    array_pop($params); // Remove offset
    array_pop($params); // Remove limit
    
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    
    respondWithSuccess([
        'students' => $students,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

// ===== UTILITY FUNCTIONS =====
function requireLogin() {
    if (!isLoggedIn()) {
        throw new Exception('Anda harus login terlebih dahulu', 401);
    }
}

function requireRole($roles) {
    requireLogin();
    
    if (!in_array($_SESSION['role'], $roles)) {
        throw new Exception('Anda tidak memiliki akses', 403);
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function respondWithSuccess($data = []) {
    echo json_encode([
        'success' => true,
        'data' => $data,
        'timestamp' => time()
    ]);
    exit;
}

function respondWithError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $message,
        'timestamp' => time()
    ]);
    exit;
}

function logActivity($user_id, $action, $description) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $user_id, $action, $description, 
        $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']
    ]);
}

function createNotification($user_id, $type, $message, $data = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, type, message, data, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([$user_id, $type, $message, json_encode($data)]);
}

function generateRegistrationNumber() {
    return 'SPMB' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function generatePaymentCode() {
    return 'PAY' . date('YmdHis') . rand(100, 999);
}

function generateUsername($nama) {
    $username = strtolower(str_replace(' ', '', $nama));
    $username = preg_replace('/[^a-z0-9]/', '', $username);
    return substr($username, 0, 20) . rand(100, 999);
}

function generateRandomPassword($length = 8) {
    return substr(str_shuffle('abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789'), 0, $length);
}

function getDashboardUrl($role) {
    switch ($role) {
        case 'admin':
        case 'operator':
            return 'admin/dashboard.php';
        case 'santri':
            return 'santri/dashboard.php';
        case 'affiliate':
            return 'affiliate/dashboard.php';
        default:
            return 'login.php';
    }
}
?>