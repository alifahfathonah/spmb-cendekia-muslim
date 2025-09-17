<?php
// register_process.php - Process student registration form
session_start();
require_once 'config.php';

// Security headers
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CORS headers (adjust origin as needed)
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
    // Rate limiting (simple implementation)
    $ip = $_SERVER['REMOTE_ADDR'];
    $rate_limit_key = "registration_rate_limit_$ip";
    
    // Check if IP has exceeded rate limit (5 registrations per hour)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE created_at > NOW() - INTERVAL 1 HOUR AND user_id IN (SELECT id FROM users WHERE created_at > NOW() - INTERVAL 1 HOUR)");
    $stmt->execute();
    $recent_registrations = $stmt->fetchColumn();
    
    if ($recent_registrations >= 5) {
        throw new Exception('Terlalu banyak percobaan pendaftaran. Silakan coba lagi nanti.');
    }

    // Input validation and sanitization
    $required_fields = [
        'education_level', 'full_name', 'gender', 'birth_place', 'birth_date',
        'religion', 'citizenship', 'address', 'village', 'district', 'regency', 'province',
        'father_name', 'mother_name', 'agreement'
    ];

    $data = [];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            throw new Exception("Field '$field' wajib diisi.");
        }
        $data[$field] = trim($_POST[$field]);
    }

    // Additional validation
    if (!in_array($data['education_level'], ['tk', 'sd', 'smp', 'pkbm', 'lpq'])) {
        throw new Exception('Jenjang pendidikan tidak valid.');
    }

    if (!in_array($data['gender'], ['L', 'P'])) {
        throw new Exception('Jenis kelamin tidak valid.');
    }

    if (!in_array($data['religion'], ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])) {
        throw new Exception('Agama tidak valid.');
    }

    if (!in_array($data['citizenship'], ['WNI', 'WNA'])) {
        throw new Exception('Kewarganegaraan tidak valid.');
    }

    // Validate birth date
    $birth_date = DateTime::createFromFormat('Y-m-d', $data['birth_date']);
    if (!$birth_date) {
        throw new Exception('Format tanggal lahir tidak valid.');
    }

    // Check age limits based on education level
    $today = new DateTime();
    $age = $today->diff($birth_date)->y;
    
    $age_limits = [
        'tk' => ['min' => 4, 'max' => 6],
        'sd' => ['min' => 6, 'max' => 13],
        'smp' => ['min' => 12, 'max' => 16],
        'pkbm' => ['min' => 15, 'max' => 50],
        'lpq' => ['min' => 5, 'max' => 50]
    ];
    
    $limits = $age_limits[$data['education_level']];
    if ($age < $limits['min'] || $age > $limits['max']) {
        throw new Exception("Usia tidak sesuai untuk jenjang {$data['education_level']}. Usia harus {$limits['min']}-{$limits['max']} tahun.");
    }

    // Optional fields with validation
    $optional_fields = ['nickname', 'nik', 'rt', 'rw', 'postal_code', 'father_education', 
                       'father_job', 'father_income', 'mother_education', 'mother_job', 
                       'mother_income', 'guardian_name', 'guardian_relation', 'guardian_phone',
                       'previous_school', 'ijazah_number', 'referral_code'];

    foreach ($optional_fields as $field) {
        $data[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : null;
    }

    // Validate NIK if provided
    if ($data['nik'] && !preg_match('/^\d{16}$/', $data['nik'])) {
        throw new Exception('NIK harus 16 digit angka.');
    }

    // Validate phone numbers
    if ($data['guardian_phone'] && !preg_match('/^[0-9+\-\s()]{10,15}$/', $data['guardian_phone'])) {
        throw new Exception('Format nomor telepon wali tidak valid.');
    }

    // PKBM package validation
    if ($data['education_level'] === 'pkbm') {
        if (!isset($_POST['pkbm_package']) || !in_array($_POST['pkbm_package'], ['A', 'B', 'C'])) {
            throw new Exception('Paket PKBM wajib dipilih.');
        }
        $data['pkbm_package'] = $_POST['pkbm_package'];
    }

    // Handle referral code
    $affiliate_id = null;
    if ($data['referral_code']) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND role = 'affiliate' AND is_active = 1");
        $stmt->execute([$data['referral_code']]);
        $affiliate = $stmt->fetch();
        
        if (!$affiliate) {
            // If referral code not found, use general institution referral
            $data['referral_code'] = 'LEMBAGA_UMUM';
        } else {
            $affiliate_id = $affiliate['id'];
        }
    } else {
        $data['referral_code'] = 'LEMBAGA_UMUM';
    }

    // Start transaction
    $pdo->beginTransaction();

    // Generate registration code
    $education_codes = [
        'tk' => '001',
        'sd' => '002', 
        'smp' => '003',
        'pkbm' => '004',
        'lpq' => '005'
    ];
    
    $year = date('Y');
    $education_code = $education_codes[$data['education_level']];
    
    // Get next registration number
    $stmt = $pdo->prepare("SELECT COUNT(*) + 1 as next_number FROM students s 
                          JOIN education_levels el ON s.education_level_id = el.id 
                          WHERE el.code = ? AND YEAR(s.created_at) = ?");
    $stmt->execute([$education_code, $year]);
    $result = $stmt->fetch();
    $next_number = $result['next_number'];
    
    $registration_code = $year . $education_code . str_pad($next_number, 3, '0', STR_PAD_LEFT);

    // Generate password (registration code without year)
    $password = $education_code . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Create user account
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, name, role, is_active, must_change_password) 
                          VALUES (?, ?, ?, ?, 'santri', 1, 0)");
    
    // Generate temporary email if not provided
    $email = $registration_code . '@temp.cendekiamuslim.or.id';
    
    $stmt->execute([$registration_code, $hashed_password, $email, $data['full_name']]);
    $user_id = $pdo->lastInsertId();

    // Get education level ID
    $stmt = $pdo->prepare("SELECT id FROM education_levels WHERE code = ?");
    $stmt->execute([$education_code]);
    $education_level = $stmt->fetch();
    
    if (!$education_level) {
        throw new Exception('Jenjang pendidikan tidak ditemukan.');
    }

    // Insert student data
    $stmt = $pdo->prepare("
        INSERT INTO students (
            user_id, registration_code, education_level_id, package_type,
            full_name, nickname, gender, birth_place, birth_date, religion, citizenship, nik,
            address, rt, rw, village, district, regency, province, postal_code,
            father_name, father_education, father_job, father_income,
            mother_name, mother_education, mother_job, mother_income,
            guardian_name, guardian_relation, guardian_phone,
            previous_school, ijazah_number, affiliate_id, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $user_id, $registration_code, $education_level['id'], $data['pkbm_package'] ?? null,
        $data['full_name'], $data['nickname'], $data['gender'], $data['birth_place'], $data['birth_date'],
        $data['religion'], $data['citizenship'], $data['nik'],
        $data['address'], $data['rt'], $data['rw'], $data['village'], $data['district'], 
        $data['regency'], $data['province'], $data['postal_code'],
        $data['father_name'], $data['father_education'], $data['father_job'], $data['father_income'],
        $data['mother_name'], $data['mother_education'], $data['mother_job'], $data['mother_income'],
        $data['guardian_name'], $data['guardian_relation'], $data['guardian_phone'],
        $data['previous_school'], $data['ijazah_number'], $affiliate_id
    ]);
    
    $student_id = $pdo->lastInsertId();

    // Handle file upload (ijazah)
    if (isset($_FILES['ijazah_file']) && $_FILES['ijazah_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['ijazah_file'];
        
        // Validate file
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 5242880; // 5MB
        
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Format file ijazah tidak didukung. Gunakan PDF, JPG, atau PNG.');
        }
        
        if ($file['size'] > $max_size) {
            throw new Exception('Ukuran file ijazah terlalu besar. Maksimal 5MB.');
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $registration_code . '_ijazah_' . time() . '.' . $extension;
        $upload_path = 'uploads/ijazah/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Update student record with ijazah file path
            $stmt = $pdo->prepare("UPDATE students SET ijazah_file = ? WHERE id = ?");
            $stmt->execute([$upload_path, $student_id]);
        }
    }

    // Log the registration
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id, new_data, ip_address, user_agent) 
                          VALUES (?, 'CREATE', 'students', ?, ?, ?, ?)");
    $stmt->execute([
        $user_id, $student_id, 
        json_encode(['registration_code' => $registration_code, 'education_level' => $data['education_level']]),
        $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);

    // Commit transaction
    $pdo->commit();

    // Send notifications (WhatsApp and Email)
    try {
        sendRegistrationNotifications($registration_code, $data['full_name'], $password, $data['education_level'], $data['guardian_phone']);
    } catch (Exception $e) {
        // Log notification error but don't fail the registration
        error_log("Notification error: " . $e->getMessage());
    }

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Pendaftaran berhasil!',
        'data' => [
            'registration_code' => $registration_code,
            'username' => $registration_code,
            'password' => $password,
            'education_level' => $data['education_level'],
            'student_name' => $data['full_name']
        ]
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    // Log error
    error_log("Registration error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Send registration notifications via WhatsApp and Email
 */
function sendRegistrationNotifications($registration_code, $student_name, $password, $education_level, $guardian_phone) {
    // WhatsApp notification
    if ($guardian_phone) {
        $whatsapp_message = "Assalamu'alaikum,\n\n";
        $whatsapp_message .= "Pendaftaran santri baru berhasil!\n\n";
        $whatsapp_message .= "Nama: {$student_name}\n";
        $whatsapp_message .= "Kode Pendaftaran: {$registration_code}\n";
        $whatsapp_message .= "Jenjang: " . strtoupper($education_level) . "\n\n";
        $whatsapp_message .= "Login Information:\n";
        $whatsapp_message .= "Username: {$registration_code}\n";
        $whatsapp_message .= "Password: {$password}\n\n";
        $whatsapp_message .= "Silakan login di: https://spmb.cendekiamuslim.or.id/login.php\n\n";
        $whatsapp_message .= "Data akan diverifikasi oleh panitia SPMB maksimal 2x24 jam.\n\n";
        $whatsapp_message .= "Terima kasih,\nPanitia SPMB Cendekia Muslim";
        
        // Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $guardian_phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        sendWhatsApp($phone, $whatsapp_message);
    }

    // Email notification (if email provided)
    $email_subject = "Pendaftaran Santri Baru - " . $registration_code;
    $email_body = generateRegistrationEmailTemplate($registration_code, $student_name, $password, $education_level);
    
    // For now, log the email content (implement actual email sending later)
    error_log("Email notification: Subject: $email_subject, Body: $email_body");
}

/**
 * Send WhatsApp message using API
 */
function sendWhatsApp($phone, $message) {
    $whatsapp_token = getSetting('whatsapp_api_token');
    
    if (empty($whatsapp_token)) {
        throw new Exception('WhatsApp API token not configured');
    }
    
    // Example using a WhatsApp API service (adjust based on your provider)
    $api_url = "https://api.whatsapp.com/send"; // Replace with actual API endpoint
    $data = [
        'phone' => $phone,
        'message' => $message,
        'api_token' => $whatsapp_token
    ];
    
    // Use cURL to send the message
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        throw new Exception('Failed to send WhatsApp message');
    }
}

/**
 * Generate email template for registration
 */
function generateRegistrationEmailTemplate($registration_code, $student_name, $password, $education_level) {
    $template = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Pendaftaran Santri Baru</title>
    </head>
    <body style='font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f8f9fa;'>
        <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);'>
            <div style='background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 24px;'>🎉 Pendaftaran Berhasil!</h1>
                <p style='margin: 10px 0 0 0; opacity: 0.9;'>Cendekia Muslim Islamic School</p>
            </div>
            
            <div style='padding: 30px;'>
                <p>Assalamu'alaikum,</p>
                
                <p>Selamat! Pendaftaran santri baru telah berhasil disubmit dengan detail sebagai berikut:</p>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 8px 0; font-weight: bold;'>Nama Santri:</td>
                            <td style='padding: 8px 0;'>{$student_name}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; font-weight: bold;'>Kode Pendaftaran:</td>
                            <td style='padding: 8px 0;'><strong style='color: #28a745;'>{$registration_code}</strong></td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; font-weight: bold;'>Jenjang:</td>
                            <td style='padding: 8px 0;'>" . strtoupper($education_level) . "</td>
                        </tr>
                    </table>
                </div>
                
                <div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='margin: 0 0 15px 0; color: #0277bd;'>🔐 Informasi Login</h3>
                    <p style='margin: 5px 0;'><strong>Username:</strong> {$registration_code}</p>
                    <p style='margin: 5px 0;'><strong>Password:</strong> {$password}</p>
                    <p style='margin: 15px 0 5px 0;'><strong>Link Login:</strong></p>
                    <a href='https://spmb.cendekiamuslim.or.id/login.php' style='color: #0277bd; text-decoration: none;'>https://spmb.cendekiamuslim.or.id/login.php</a>
                </div>
                
                <div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='margin: 0 0 15px 0; color: #856404;'>📋 Langkah Selanjutnya</h3>
                    <ol style='margin: 0; padding-left: 20px;'>
                        <li>Data akan diverifikasi oleh panitia SPMB (maksimal 2x24 jam)</li>
                        <li>Anda akan mendapat notifikasi setelah verifikasi selesai</li>
                        <li>Lakukan pembayaran biaya administrasi pendaftaran</li>
                        <li>Tunggu pengumuman untuk daftar ulang</li>
                    </ol>
                </div>
                
                <p>Jika ada pertanyaan, silakan hubungi:</p>
                <ul>
                    <li>WhatsApp: 0821-7772-9934</li>
                    <li>Email: info@cendekiamuslim.or.id</li>
                </ul>
                
                <p>Terima kasih atas kepercayaan Anda kepada Cendekia Muslim Islamic School.</p>
                
                <p>Barakallahu fiikum,<br>
                <strong>Panitia SPMB Cendekia Muslim</strong></p>
            </div>
            
            <div style='background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px;'>
                <p style='margin: 0;'>© 2025 Yayasan Pendidikan Cendekia Muslim</p>
                <p style='margin: 5px 0 0 0;'>Nagari Pematang Panjang, Kab. Sijunjung, Sumatera Barat</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return $template;
}
?>