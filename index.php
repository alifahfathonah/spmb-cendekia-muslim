<?php
// api/index.php - Main API endpoint handler for SPMB system
session_start();
require_once '../config.php';

// Security headers
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Get the action from URL or POST data
    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    
    if (!$action) {
        throw new Exception('Action parameter is required');
    }

    // Rate limiting
    checkRateLimit($action);

    // Route to appropriate handler
    switch ($action) {
        // Student actions
        case 'get_student_data':
            requireAuth(['santri']);
            echo json_encode(getStudentData());
            break;
            
        case 'upload_payment_proof':
            requireAuth(['santri']);
            echo json_encode(uploadPaymentProof());
            break;
            
        case 'get_announcements':
            requireAuth(['santri', 'affiliate']);
            echo json_encode(getAnnouncements());
            break;

        // Panitia actions
        case 'verify_student':
            requireAuth(['panitia', 'admin']);
            echo json_encode(verifyStudent());
            break;
            
        case 'verify_payment':
            requireAuth(['panitia', 'admin']);
            echo json_encode(verifyPayment());
            break;
            
        case 'get_pending_verifications':
            requireAuth(['panitia', 'admin']);
            echo json_encode(getPendingVerifications());
            break;
            
        case 'send_announcement':
            requireAuth(['panitia', 'admin']);
            echo json_encode(sendAnnouncement());
            break;

        // Affiliate actions
        case 'get_affiliate_stats':
            requireAuth(['affiliate']);
            echo json_encode(getAffiliateStats());
            break;
            
        case 'get_referral_students':
            requireAuth(['affiliate']);
            echo json_encode(getReferralStudents());
            break;
            
        case 'request_withdrawal':
            requireAuth(['affiliate']);
            echo json_encode(requestWithdrawal());
            break;

        // Admin actions
        case 'get_system_stats':
            requireAuth(['admin']);
            echo json_encode(getSystemStats());
            break;
            
        case 'export_data':
            requireAuth(['admin', 'panitia']);
            echo json_encode(exportData());
            break;

        // Payment processing
        case 'process_payment':
            requireAuth(['santri']);
            echo json_encode(processPayment());
            break;
            
        case 'check_payment_status':
            requireAuth(['santri']);
            echo json_encode(checkPaymentStatus());
            break;

        // File operations
        case 'upload_file':
            requireAuth(['santri', 'panitia', 'admin']);
            echo json_encode(uploadFile());
            break;
            
        case 'delete_file':
            requireAuth(['santri', 'panitia', 'admin']);
            echo json_encode(deleteFile());
            break;

        // Communication
        case 'send_message':
            requireAuth(['santri', 'panitia', 'admin']);
            echo json_encode(sendMessage());
            break;
            
        case 'get_messages':
            requireAuth(['santri', 'panitia', 'admin']);
            echo json_encode(getMessages());
            break;

        // Reports
        case 'generate_report':
            requireAuth(['admin', 'panitia']);
            echo json_encode(generateReport());
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Check user authentication and authorization
 */
function requireAuth($allowed_roles = []) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
        exit();
    }
    
    if (!empty($allowed_roles) && !in_array($_SESSION['role'], $allowed_roles)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit();
    }
}

/**
 * Simple rate limiting
 */
function checkRateLimit($action) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "rate_limit_{$action}_{$ip}";
    
    // For demo purposes, just log the attempt
    error_log("API call: $action from $ip");
}

/**
 * Get student data
 */
function getStudentData() {
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT s.*, el.name as education_level_name, el.code as education_level_code
        FROM students s 
        JOIN education_levels el ON s.education_level_id = el.id 
        WHERE s.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        throw new Exception('Student data not found');
    }
    
    // Get payment information
    $stmt = $pdo->prepare("
        SELECT p.*, rf.amount as required_amount
        FROM payments p
        JOIN students s ON p.student_id = s.id
        JOIN registration_fees rf ON s.education_level_id = rf.education_level_id 
            AND rf.fee_type = 'registration' 
            AND rf.gender = ? 
            AND rf.year = YEAR(CURDATE())
        WHERE s.user_id = ? AND p.payment_type = 'registration'
        ORDER BY p.created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$student['gender'] === 'L' ? 'putra' : 'putri', $user_id]);
    $payment = $stmt->fetch();
    
    return [
        'success' => true,
        'data' => [
            'student' => $student,
            'payment' => $payment
        ]
    ];
}

/**
 * Upload payment proof
 */
function uploadPaymentProof() {
    global $pdo;
    
    if (!isset($_FILES['payment_proof'])) {
        throw new Exception('File payment proof is required');
    }
    
    $file = $_FILES['payment_proof'];
    $user_id = $_SESSION['user_id'];
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    $max_size = 5242880; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
    }
    
    // Get student info
    $stmt = $pdo->prepare("SELECT id, registration_code FROM students WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        throw new Exception('Student not found');
    }
    
    // Generate filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $student['registration_code'] . '_payment_' . time() . '.' . $extension;
    $upload_path = '../uploads/payment_proof/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload file');
    }
    
    // Save payment record
    $amount = floatval($_POST['amount']);
    $bank_account = $_POST['bank_account'];
    $transfer_date = $_POST['transfer_date'];
    
    $stmt = $pdo->prepare("
        INSERT INTO payments (student_id, payment_type, amount, payment_method, manual_proof, bank_account, transfer_date)
        VALUES (?, 'registration', ?, 'manual', ?, ?, ?)
    ");
    $stmt->execute([$student['id'], $amount, $upload_path, $bank_account, $transfer_date]);
    
    // Send notification to panitia
    sendNotificationToPanitia('payment_uploaded', [
        'student_name' => $_SESSION['name'],
        'registration_code' => $student['registration_code'],
        'amount' => $amount
    ]);
    
    return [
        'success' => true,
        'message' => 'Bukti pembayaran berhasil diupload'
    ];
}

/**
 * Verify student data
 */
function verifyStudent() {
    global $pdo;
    
    $student_id = intval($_POST['student_id']);
    $status = $_POST['status']; // 'approved' or 'rejected'
    $notes = $_POST['notes'] ?? '';
    
    if (!in_array($status, ['approved', 'rejected'])) {
        throw new Exception('Invalid status');
    }
    
    $new_status = $status === 'approved' ? 'verified' : 'rejected';
    
    $pdo->beginTransaction();
    
    try {
        // Update student status
        $stmt = $pdo->prepare("
            UPDATE students 
            SET status = ?, verified_by = ?, verified_at = NOW(), notes = ?
            WHERE id = ?
        ");
        $stmt->execute([$new_status, $_SESSION['user_id'], $notes, $student_id]);
        
        // Get student info for notification
        $stmt = $pdo->prepare("
            SELECT s.*, u.name, u.id as user_id 
            FROM students s 
            JOIN users u ON s.user_id = u.id 
            WHERE s.id = ?
        ");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();
        
        if ($status === 'approved') {
            // Send WhatsApp notification
            sendStudentNotification($student, 'verification_approved');
        } else {
            // Send rejection notification
            sendStudentNotification($student, 'verification_rejected', $notes);
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => $status === 'approved' ? 'Student approved successfully' : 'Student rejected'
        ];
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

/**
 * Verify payment
 */
function verifyPayment() {
    global $pdo;
    
    $payment_id = intval($_POST['payment_id']);
    $status = $_POST['status']; // 'approved' or 'rejected'
    $notes = $_POST['notes'] ?? '';
    
    if (!in_array($status, ['approved', 'rejected'])) {
        throw new Exception('Invalid status');
    }
    
    $pdo->beginTransaction();
    
    try {
        // Update payment status
        $payment_status = $status === 'approved' ? 'success' : 'failed';
        $stmt = $pdo->prepare("
            UPDATE payments 
            SET payment_status = ?, verified_by = ?, verified_at = NOW(), verification_notes = ?
            WHERE id = ?
        ");
        $stmt->execute([$payment_status, $_SESSION['user_id'], $notes, $payment_id]);
        
        // Get payment and student info
        $stmt = $pdo->prepare("
            SELECT p.*, s.*, u.name, u.id as user_id, s.affiliate_id
            FROM payments p
            JOIN students s ON p.student_id = s.id
            JOIN users u ON s.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$payment_id]);
        $payment_data = $stmt->fetch();
        
        if ($status === 'approved') {
            // Update student status to payment confirmed
            $stmt = $pdo->prepare("UPDATE students SET status = 'paid' WHERE id = ?");
            $stmt->execute([$payment_data['student_id']]);
            
            // Create affiliate commission if applicable
            if ($payment_data['affiliate_id']) {
                $commission_amount = $payment_data['amount'] * 0.5; // 50% commission
                
                $stmt = $pdo->prepare("
                    INSERT INTO affiliate_commissions (affiliate_id, student_id, payment_id, commission_amount)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $payment_data['affiliate_id'],
                    $payment_data['student_id'],
                    $payment_id,
                    $commission_amount
                ]);
            }
            
            // Send success notification
            sendStudentNotification($payment_data, 'payment_approved');
        } else {
            // Send rejection notification
            sendStudentNotification($payment_data, 'payment_rejected', $notes);
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => $status === 'approved' ? 'Payment approved successfully' : 'Payment rejected'
        ];
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

/**
 * Get pending verifications
 */
function getPendingVerifications() {
    global $pdo;
    
    $type = $_GET['type'] ?? 'students';
    
    if ($type === 'students') {
        $stmt = $pdo->prepare("
            SELECT s.*, el.name as education_level_name, u.name as student_name
            FROM students s
            JOIN education_levels el ON s.education_level_id = el.id
            JOIN users u ON s.user_id = u.id
            WHERE s.status = 'pending'
            ORDER BY s.created_at DESC
            LIMIT 20
        ");
        $stmt->execute();
        $data = $stmt->fetchAll();
    } else {
        $stmt = $pdo->prepare("
            SELECT p.*, s.registration_code, s.full_name, el.name as education_level_name
            FROM payments p
            JOIN students s ON p.student_id = s.id
            JOIN education_levels el ON s.education_level_id = el.id
            WHERE p.payment_status = 'pending' AND p.payment_method = 'manual'
            ORDER BY p.created_at DESC
            LIMIT 20
        ");
        $stmt->execute();
        $data = $stmt->fetchAll();
    }
    
    return [
        'success' => true,
        'data' => $data
    ];
}

/**
 * Get affiliate statistics
 */
function getAffiliateStats() {
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    
    // Get referral statistics
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
    $stats = $stmt->fetch();
    
    // Get recent referrals
    $stmt = $pdo->prepare("
        SELECT s.registration_code, s.full_name, s.status, s.created_at, el.name as education_level
        FROM students s
        JOIN education_levels el ON s.education_level_id = el.id
        WHERE s.affiliate_id = ?
        ORDER BY s.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $recent_referrals = $stmt->fetchAll();
    
    return [
        'success' => true,
        'data' => [
            'stats' => $stats,
            'recent_referrals' => $recent_referrals
        ]
    ];
}

/**
 * Request withdrawal
 */
function requestWithdrawal() {
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']);
    $bank_name = $_POST['bank_name'];
    $bank_account = $_POST['bank_account'];
    $bank_holder = $_POST['bank_holder'];
    
    if ($amount < 100000) {
        throw new Exception('Minimum withdrawal amount is Rp 100,000');
    }
    
    // Check available balance
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(commission_amount), 0) as available_balance
        FROM affiliate_commissions 
        WHERE affiliate_id = ? AND status = 'pending'
    ");
    $stmt->execute([$user_id]);
    $balance = $stmt->fetchColumn();
    
    if ($amount > $balance) {
        throw new Exception('Insufficient balance');
    }
    
    // Create withdrawal request
    $stmt = $pdo->prepare("
        INSERT INTO affiliate_withdrawals (affiliate_id, amount, bank_name, bank_account, bank_holder)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $amount, $bank_name, $bank_account, $bank_holder]);
    
    return [
        'success' => true,
        'message' => 'Withdrawal request submitted successfully'
    ];
}

/**
 * Send notification to panitia
 */
function sendNotificationToPanitia($type, $data) {
    global $pdo;
    
    // Get all panitia users
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE role IN ('panitia', 'admin') AND is_active = 1");
    $stmt->execute();
    $panitia_users = $stmt->fetchAll();
    
    // Create notification message based on type
    $messages = [
        'payment_uploaded' => "Bukti pembayaran baru dari {$data['student_name']} ({$data['registration_code']}) sebesar Rp " . number_format($data['amount']),
        'new_registration' => "Pendaftaran baru dari {$data['student_name']} untuk jenjang {$data['education_level']}"
    ];
    
    $message = $messages[$type] ?? 'New notification';
    
    // Send WhatsApp to panitia (implement based on your WhatsApp API)
    $whatsapp_number = getSetting('whatsapp_number');
    if ($whatsapp_number) {
        // sendWhatsApp($whatsapp_number, $message);
    }
}

/**
 * Send notification to student
 */
function sendStudentNotification($student_data, $type, $additional_info = '') {
    $messages = [
        'verification_approved' => "Selamat! Data pendaftaran Anda telah diverifikasi. Silakan lakukan pembayaran biaya administrasi.",
        'verification_rejected' => "Data pendaftaran Anda perlu diperbaiki. " . $additional_info,
        'payment_approved' => "Pembayaran Anda telah diverifikasi. Terima kasih!",
        'payment_rejected' => "Pembayaran Anda ditolak. " . $additional_info
    ];
    
    $message = $messages[$type] ?? 'Notification from SPMB Cendekia Muslim';
    
    // Send via WhatsApp if phone number available
    if (!empty($student_data['guardian_phone'])) {
        // sendWhatsApp($student_data['guardian_phone'], $message);
    }
}

/**
 * Get system statistics for admin
 */
function getSystemStats() {
    global $pdo;
    
    $stats = [];
    
    // Total registrations
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students");
    $stmt->execute();
    $stats['total_students'] = $stmt->fetchColumn();
    
    // Pending verifications
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE status = 'pending'");
    $stmt->execute();
    $stats['pending_verification'] = $stmt->fetchColumn();
    
    // Total payments
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'success'");
    $stmt->execute();
    $stats['total_revenue'] = $stmt->fetchColumn();
    
    // Active affiliates
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'affiliate' AND is_active = 1");
    $stmt->execute();
    $stats['active_affiliates'] = $stmt->fetchColumn();
    
    return [
        'success' => true,
        'data' => $stats
    ];
}

/**
 * Get announcements
 */
function getAnnouncements() {
    global $pdo;
    
    $role = $_SESSION['role'];
    $target_audience = $role === 'santri' ? 'students' : ($role === 'affiliate' ? 'affiliates' : 'all');
    
    $stmt = $pdo->prepare("
        SELECT * FROM announcements 
        WHERE is_active = 1 AND (target_audience = 'all' OR target_audience = ?)
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$target_audience]);
    $announcements = $stmt->fetchAll();
    
    return [
        'success' => true,
        'data' => $announcements
    ];
}

/**
 * Process automatic payment
 */
function processPayment() {
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']);
    $payment_type = $_POST['payment_type']; // 'registration' or 'admission'
    
    // Get student info
    $stmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        throw new Exception('Student not found');
    }
    
    // Generate transaction ID
    $transaction_id = 'TXN_' . time() . '_' . $student['id'];
    
    // Create payment record
    $stmt = $pdo->prepare("
        INSERT INTO payments (student_id, payment_type, amount, payment_method, transaction_id, payment_status)
        VALUES (?, ?, ?, 'auto', ?, 'pending')
    ");
    $stmt->execute([$student['id'], $payment_type, $amount, $transaction_id]);
    $payment_id = $pdo->lastInsertId();
    
    // Generate payment URL (implement based on your payment gateway)
    $payment_url = generatePaymentURL($transaction_id, $amount);
    
    // Update payment record with URL
    $stmt = $pdo->prepare("UPDATE payments SET payment_url = ? WHERE id = ?");
    $stmt->execute([$payment_url, $payment_id]);
    
    return [
        'success' => true,
        'data' => [
            'transaction_id' => $transaction_id,
            'payment_url' => $payment_url,
            'amount' => $amount
        ]
    ];
}

/**
 * Generate payment URL (placeholder - implement based on your payment gateway)
 */
function generatePaymentURL($transaction_id, $amount) {
    // This is a placeholder - implement based on your payment gateway
    // Example for Midtrans, Xendit, etc.
    return "https://payment-gateway.example.com/pay?txn=$transaction_id&amount=$amount";
}

/**
 * Export data to various formats
 */
function exportData() {
    $type = $_GET['type'] ?? 'students';
    $format = $_GET['format'] ?? 'excel';
    
    // This is a placeholder - implement actual export logic
    return [
        'success' => true,
        'message' => "Export $type as $format initiated",
        'download_url' => "/exports/{$type}_" . date('Y-m-d') . ".$format"
    ];
}
?>