<?php
// setup.php - Setup Database SPMB Cendekia Muslim Islamic School

// Security check - prevent re-run if setup is complete
$lock_file = '.setup_complete';
if (file_exists($lock_file)) {
    die('<!DOCTYPE html>
    <html>
    <head>
        <title>Setup Sudah Selesai</title>
        <style>
            body { font-family: Arial; background: #28a745; color: white; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
            .container { text-align: center; padding: 40px; background: rgba(0,0,0,0.2); border-radius: 10px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>⚠️ Setup Sudah Selesai</h1>
            <p>Database SPMB Cendekia Muslim sudah dikonfigurasi.</p>
            <p>Untuk reset, hapus file <code>.setup_complete</code></p>
            <p><a href="index.html" style="color: white;">Buka Aplikasi</a></p>
        </div>
    </body>
    </html>');
}

header('Content-Type: text/html; charset=utf-8');
$setup_success = false;
$messages = [];

// Check if accessed via POST
$run_setup = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_setup']);

if (!$run_setup) {
    // Show confirmation form
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Setup SPMB Cendekia Muslim</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #28a745, #20c997);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
                padding: 20px;
            }
            .container {
                background: white;
                border-radius: 15px;
                padding: 40px;
                box-shadow: 0 15px 50px rgba(0,0,0,0.3);
                max-width: 600px;
                width: 100%;
            }
            .logo {
                text-align: center;
                margin-bottom: 30px;
            }
            .logo img {
                max-width: 200px;
                height: auto;
            }
            h1 {
                color: #28a745;
                margin-bottom: 30px;
                text-align: center;
                font-size: 24px;
            }
            .warning {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                color: #856404;
                padding: 15px;
                border-radius: 5px;
                margin: 20px 0;
            }
            .form-group {
                margin: 20px 0;
            }
            label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #333;
            }
            input[type="password"], input[type="text"], input[type="email"] {
                width: 100%;
                padding: 12px;
                border: 2px solid #ddd;
                border-radius: 8px;
                font-size: 14px;
                transition: border-color 0.3s;
                box-sizing: border-box;
            }
            input[type="password"]:focus, input[type="text"]:focus, input[type="email"]:focus {
                outline: none;
                border-color: #28a745;
            }
            .btn {
                display: inline-block;
                padding: 12px 30px;
                margin: 10px 5px;
                background: #28a745;
                color: white;
                text-decoration: none;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                transition: all 0.3s;
            }
            .btn:hover {
                background: #218838;
                transform: translateY(-2px);
            }
            .btn-danger {
                background: #dc3545;
            }
            .btn-danger:hover {
                background: #c82333;
            }
            .checkbox-group {
                margin: 20px 0;
            }
            .checkbox-group input {
                margin-right: 10px;
            }
            .info-box {
                background: #e8f5e8;
                border-left: 4px solid #28a745;
                padding: 15px;
                border-radius: 5px;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">
                <h1>🏫 Setup SPMB Cendekia Muslim</h1>
                <p style="color: #666; margin: 0;">Sistem Penerimaan Murid Baru - Islamic School</p>
            </div>
            
            <div class="warning">
                <strong>⚠️ Perhatian!</strong><br>
                Setup ini akan membuat database lengkap untuk sistem pendaftaran santri baru dengan semua fitur yang diperlukan.
            </div>

            <div class="info-box">
                <strong>📋 Yang akan dibuat:</strong>
                <ul>
                    <li>Database dan tabel sistem SPMB</li>
                    <li>Data master jenjang pendidikan (TK, SD, SMP, PKBM, LPQ)</li>
                    <li>Data master biaya pendaftaran</li>
                    <li>Sistem user dengan role (Admin, Panitia, Santri, Affiliate)</li>
                    <li>Folder untuk file uploads</li>
                    <li>Konfigurasi notifikasi WhatsApp dan Email</li>
                    <li>Sistem pembayaran dan komisi affiliate</li>
                </ul>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="db_host">Database Host:</label>
                    <input type="text" name="db_host" id="db_host" required value="localhost">
                </div>

                <div class="form-group">
                    <label for="db_name">Database Name:</label>
                    <input type="text" name="db_name" id="db_name" required value="spmb_cendekia_muslim">
                </div>

                <div class="form-group">
                    <label for="db_user">Database Username:</label>
                    <input type="text" name="db_user" id="db_user" required value="root">
                </div>

                <div class="form-group">
                    <label for="db_pass">Database Password:</label>
                    <input type="password" name="db_pass" id="db_pass" placeholder="Kosongkan jika tidak ada password">
                </div>

                <div class="form-group">
                    <label for="admin_password">Password Admin:</label>
                    <input type="password" name="admin_password" id="admin_password" required 
                           placeholder="Minimal 8 karakter" minlength="8">
                </div>

                <div class="form-group">
                    <label for="admin_email">Email Admin:</label>
                    <input type="email" name="admin_email" id="admin_email" required 
                           placeholder="admin@cendekiamuslim.or.id" value="admin@cendekiamuslim.or.id">
                </div>

                <div class="form-group">
                    <label for="panitia_password">Password Panitia SPMB:</label>
                    <input type="password" name="panitia_password" id="panitia_password" required 
                           placeholder="Minimal 8 karakter" minlength="8">
                </div>

                <div class="form-group">
                    <label for="panitia_email">Email Panitia SPMB:</label>
                    <input type="email" name="panitia_email" id="panitia_email" required 
                           placeholder="spmb@cendekiamuslim.or.id" value="spmb@cendekiamuslim.or.id">
                </div>

                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="create_sample_data" value="1">
                        Buat data contoh untuk testing
                    </label>
                </div>

                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="confirm_setup" value="1" required>
                        <strong>Saya yakin ingin menjalankan setup SPMB Cendekia Muslim</strong>
                    </label>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn">🚀 Jalankan Setup</button>
                    <a href="index.html" class="btn btn-danger">❌ Batal</a>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Process setup
try {
    $messages[] = "🚀 Memulai setup sistem SPMB Cendekia Muslim...";

    // Database configuration
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];

    // Create database connection
    $dsn = "mysql:host=" . $db_host . ";charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . $db_name . "`");
    $messages[] = "✅ Database '" . $db_name . "' siap";

    // Enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Create tables
    $tables = [
        // Users table
        "CREATE TABLE users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            role ENUM('admin', 'panitia', 'santri', 'affiliate') NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            must_change_password BOOLEAN DEFAULT FALSE,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        )",

        // Education Levels table
        "CREATE TABLE education_levels (
            id INT PRIMARY KEY AUTO_INCREMENT,
            code VARCHAR(10) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // Registration Fees table
        "CREATE TABLE registration_fees (
            id INT PRIMARY KEY AUTO_INCREMENT,
            education_level_id INT NOT NULL,
            fee_type ENUM('registration', 'admission') NOT NULL,
            gender ENUM('putra', 'putri') NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            year INT NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (education_level_id) REFERENCES education_levels(id) ON DELETE CASCADE
        )",

        // Students table
        "CREATE TABLE students (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            registration_code VARCHAR(20) UNIQUE NOT NULL,
            education_level_id INT NOT NULL,
            package_type ENUM('A', 'B', 'C') NULL, -- For PKBM only
            
            -- Personal Data
            full_name VARCHAR(150) NOT NULL,
            nickname VARCHAR(50),
            gender ENUM('L', 'P') NOT NULL,
            birth_place VARCHAR(100) NOT NULL,
            birth_date DATE NOT NULL,
            religion VARCHAR(50) DEFAULT 'Islam',
            citizenship VARCHAR(50) DEFAULT 'WNI',
            nik VARCHAR(16),
            
            -- Address
            address TEXT NOT NULL,
            rt VARCHAR(5),
            rw VARCHAR(5),
            village VARCHAR(100),
            district VARCHAR(100),
            regency VARCHAR(100),
            province VARCHAR(100),
            postal_code VARCHAR(10),
            
            -- Family Data
            father_name VARCHAR(150),
            father_education VARCHAR(50),
            father_job VARCHAR(100),
            father_income DECIMAL(15,2),
            mother_name VARCHAR(150),
            mother_education VARCHAR(50),
            mother_job VARCHAR(100),
            mother_income DECIMAL(15,2),
            guardian_name VARCHAR(150),
            guardian_relation VARCHAR(50),
            guardian_phone VARCHAR(20),
            
            -- Education Background
            previous_school VARCHAR(200),
            ijazah_number VARCHAR(100),
            ijazah_file VARCHAR(500),
            
            -- Registration Data
            affiliate_id INT NULL,
            status ENUM('pending', 'verified', 'payment_pending', 'paid', 'admitted', 'rejected') DEFAULT 'pending',
            verified_by INT NULL,
            verified_at TIMESTAMP NULL,
            notes TEXT,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (education_level_id) REFERENCES education_levels(id),
            FOREIGN KEY (affiliate_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
        )",

        // Payments table
        "CREATE TABLE payments (
            id INT PRIMARY KEY AUTO_INCREMENT,
            student_id INT NOT NULL,
            payment_type ENUM('registration', 'admission') NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            payment_method ENUM('auto', 'manual') NOT NULL,
            
            -- Auto payment data
            transaction_id VARCHAR(100),
            payment_status ENUM('pending', 'success', 'failed', 'expired') DEFAULT 'pending',
            payment_url TEXT,
            
            -- Manual payment data
            manual_proof VARCHAR(500),
            bank_account VARCHAR(100),
            transfer_date DATE,
            
            verified_by INT NULL,
            verified_at TIMESTAMP NULL,
            verification_notes TEXT,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
        )",

        // Affiliate Commissions table
        "CREATE TABLE affiliate_commissions (
            id INT PRIMARY KEY AUTO_INCREMENT,
            affiliate_id INT NOT NULL,
            student_id INT NOT NULL,
            payment_id INT NOT NULL,
            commission_amount DECIMAL(12,2) NOT NULL,
            status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
            paid_at TIMESTAMP NULL,
            paid_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (affiliate_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
            FOREIGN KEY (paid_by) REFERENCES users(id) ON DELETE SET NULL
        )",

        // Affiliate Withdrawals table
        "CREATE TABLE affiliate_withdrawals (
            id INT PRIMARY KEY AUTO_INCREMENT,
            affiliate_id INT NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            bank_name VARCHAR(100) NOT NULL,
            bank_account VARCHAR(50) NOT NULL,
            bank_holder VARCHAR(150) NOT NULL,
            status ENUM('pending', 'processed', 'completed', 'rejected') DEFAULT 'pending',
            processed_by INT NULL,
            processed_at TIMESTAMP NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (affiliate_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
        )",

        // Announcements table
        "CREATE TABLE announcements (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(200) NOT NULL,
            content TEXT NOT NULL,
            type ENUM('general', 'registration', 'payment', 'matsalim') NOT NULL,
            target_audience ENUM('all', 'students', 'affiliates') DEFAULT 'all',
            is_active BOOLEAN DEFAULT TRUE,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
        )",

        // Chat Messages table
        "CREATE TABLE chat_messages (
            id INT PRIMARY KEY AUTO_INCREMENT,
            student_id INT NOT NULL,
            sender_id INT NOT NULL,
            message TEXT NOT NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
        )",

        // System Settings table
        "CREATE TABLE settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            description TEXT,
            is_encrypted BOOLEAN DEFAULT FALSE,
            updated_by INT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
        )",

        // Audit Logs table
        "CREATE TABLE audit_logs (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT,
            action VARCHAR(50) NOT NULL,
            table_name VARCHAR(50) NOT NULL,
            record_id INT,
            old_data JSON,
            new_data JSON,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )"
    ];

    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    $messages[] = "✅ Semua tabel berhasil dibuat";

    // Create indexes for performance
    $indexes = [
        "CREATE INDEX idx_students_registration_code ON students(registration_code)",
        "CREATE INDEX idx_students_status ON students(status)",
        "CREATE INDEX idx_students_created_at ON students(created_at)",
        "CREATE INDEX idx_payments_student_id ON payments(student_id)",
        "CREATE INDEX idx_payments_status ON payments(payment_status)",
        "CREATE INDEX idx_affiliate_commissions_affiliate_id ON affiliate_commissions(affiliate_id)",
        "CREATE INDEX idx_chat_messages_student_id ON chat_messages(student_id)",
        "CREATE INDEX idx_audit_logs_user_id ON audit_logs(user_id)"
    ];

    foreach ($indexes as $sql) {
        $pdo->exec($sql);
    }
    $messages[] = "✅ Index database dibuat";

    // Insert master data education levels
    $education_levels = [
        ['001', 'TK Akhlak Cendekia Muslim', 'Taman Kanak-kanak'],
        ['002', 'SD Akhlak Cendekia Muslim', 'Sekolah Dasar'],
        ['003', 'SMP Akhlak Cendekia Muslim', 'Sekolah Menengah Pertama'],
        ['004', 'PKBM Cendekia Muslim', 'Pusat Kegiatan Belajar Masyarakat'],
        ['005', 'LPQ Cendekia Muslim', 'Lembaga Pendidikan Al-Quran']
    ];

    foreach ($education_levels as $data) {
        $stmt = $pdo->prepare("INSERT INTO education_levels (code, name, description) VALUES (?, ?, ?)");
        $stmt->execute($data);
    }
    $messages[] = "✅ Data master jenjang pendidikan berhasil ditambahkan";

    // Insert registration fees based on PDF data
    $registration_fees = [
        // TK
        [1, 'registration', 'putra', 100000, 2026],
        [1, 'registration', 'putri', 100000, 2026],
        [1, 'admission', 'putra', 2300000, 2026],
        [1, 'admission', 'putri', 2400000, 2026],
        
        // SD
        [2, 'registration', 'putra', 150000, 2026],
        [2, 'registration', 'putri', 150000, 2026],
        [2, 'admission', 'putra', 4875000, 2026],
        [2, 'admission', 'putri', 5025000, 2026],
        
        // SMP
        [3, 'registration', 'putra', 200000, 2026],
        [3, 'registration', 'putri', 200000, 2026],
        [3, 'admission', 'putra', 5725000, 2026],
        [3, 'admission', 'putri', 5875000, 2026],
        
        // PKBM
        [4, 'registration', 'putra', 50000, 2026],
        [4, 'registration', 'putri', 50000, 2026],
        [4, 'admission', 'putra', 100000, 2026], // Regular
        [4, 'admission', 'putri', 100000, 2026], // Regular
        
        // LPQ
        [5, 'registration', 'putra', 0, 2026], // Free registration
        [5, 'registration', 'putri', 0, 2026],
        [5, 'admission', 'putra', 350000, 2026], // Per semester
        [5, 'admission', 'putri', 350000, 2026]
    ];

    foreach ($registration_fees as $data) {
        $stmt = $pdo->prepare("INSERT INTO registration_fees (education_level_id, fee_type, gender, amount, year) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($data);
    }
    $messages[] = "✅ Data master biaya pendaftaran berhasil ditambahkan";

    // Create folders
    $folders = [
        'uploads',
        'uploads/ijazah', 
        'uploads/payment_proof',
        'uploads/profile_pictures',
        'assets',
        'assets/brochures',
        'assets/logos'
    ];
    
    foreach ($folders as $folder) {
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
            // Create .htaccess for upload security
            if (strpos($folder, 'uploads') !== false) {
                file_put_contents($folder . '/.htaccess', "Options -Indexes\n<Files *.php>\n    Deny from all\n</Files>");
            }
        }
    }
    $messages[] = "✅ Folder uploads dan assets dibuat";

    // Insert system settings
    $settings = [
        ['school_name', 'Cendekia Muslim Islamic School', 'Nama sekolah'],
        ['school_address', 'Nagari Pematang Panjang, Kab. Sijunjung, Sumatera Barat', 'Alamat sekolah'],
        ['school_phone', '0821-7772-9934', 'Telepon sekolah'],
        ['school_email', 'info@cendekiamuslim.or.id', 'Email sekolah'],
        ['school_website', 'https://cendekiamuslim.or.id', 'Website sekolah'],
        ['bank_name', 'Bank Syariah Indonesia (BSI)', 'Nama bank'],
        ['bank_account', '7229105625', 'Nomor rekening'],
        ['bank_holder', 'YYS Pendidikan Cendekia Muslim', 'Nama pemegang rekening'],
        ['whatsapp_api_token', '', 'Token API WhatsApp'],
        ['whatsapp_number', '6282177729934', 'Nomor WhatsApp'],
        ['email_smtp_host', 'smtp.gmail.com', 'SMTP host email'],
        ['email_smtp_port', '587', 'SMTP port email'],
        ['email_smtp_user', '', 'SMTP username email'],
        ['email_smtp_pass', '', 'SMTP password email', 1],
        ['commission_rate', '50', 'Persentase komisi affiliate (%)'],
        ['registration_open', '1', 'Status pendaftaran (1=buka, 0=tutup)'],
        ['admission_open', '0', 'Status daftar ulang (1=buka, 0=tutup)'],
        ['academic_year', '2026/2027', 'Tahun ajaran'],
        ['max_file_size', '5242880', 'Maksimal ukuran file upload (5MB)']
    ];

    foreach ($settings as $data) {
        $is_encrypted = isset($data[3]) ? $data[3] : 0;
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, description, is_encrypted) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data[0], $data[1], $data[2], $is_encrypted]);
    }
    $messages[] = "✅ Pengaturan sistem berhasil disimpan";

    // Create admin user
    $admin_password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, name, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin', $admin_password, $_POST['admin_email'], 'Administrator SPMB', 'admin']);
    $admin_id = $pdo->lastInsertId();
    $messages[] = "✅ User admin berhasil dibuat (username: admin)";

    // Create panitia SPMB user
    $panitia_password = password_hash($_POST['panitia_password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, name, role, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(['panitia', $panitia_password, $_POST['panitia_email'], 'Panitia SPMB', 'panitia', $admin_id]);
    $messages[] = "✅ User panitia SPMB berhasil dibuat (username: panitia)";

    // Create sample data if requested
    if (isset($_POST['create_sample_data'])) {
        // Sample affiliate
        $affiliate_password = password_hash('affiliate123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, name, phone, role, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['affiliate001', $affiliate_password, 'affiliate@example.com', 'Affiliate Contoh', '08123456789', 'affiliate', $admin_id]);
        $affiliate_id = $pdo->lastInsertId();

        // Sample student
        $student_password = password_hash('student123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, name, role, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['2026001001', $student_password, 'student@example.com', 'Santri Contoh', 'santri', $admin_id]);
        $student_user_id = $pdo->lastInsertId();

        // Sample student data
        $stmt = $pdo->prepare("INSERT INTO students (user_id, registration_code, education_level_id, full_name, nickname, gender, birth_place, birth_date, address, father_name, mother_name, affiliate_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $student_user_id, '2026001001', 1, 'Ahmad Santri Contoh', 'Ahmad', 'L', 
            'Sijunjung', '2020-01-01', 'Jl. Contoh No. 123', 'Ayah Santri', 'Ibu Santri', $affiliate_id
        ]);

        $messages[] = "✅ Data contoh berhasil dibuat";
    }

    // Create config file
    $config_content = "<?php
// Database Configuration
define('DB_HOST', '{$db_host}');
define('DB_NAME', '{$db_name}');
define('DB_USER', '{$db_user}');
define('DB_PASS', '{$db_pass}');

// Database Connection
try {
    \$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException \$e) {
    die('Connection failed: ' . \$e->getMessage());
}

// Application Settings
define('APP_NAME', 'SPMB Cendekia Muslim');
define('APP_URL', 'https://spmb.cendekiamuslim.or.id');
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
session_start();

// Helper Functions
function getSetting(\$key, \$default = '') {
    global \$pdo;
    \$stmt = \$pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
    \$stmt->execute([\$key]);
    \$result = \$stmt->fetch();
    return \$result ? \$result['setting_value'] : \$default;
}

function isLoggedIn() {
    return isset(\$_SESSION['user_id']);
}

function getUserRole() {
    return \$_SESSION['role'] ?? null;
}

function redirectTo(\$url) {
    header('Location: ' . \$url);
    exit;
}

function generateRegistrationCode(\$education_level_code, \$year) {
    global \$pdo;
    
    // Get the last registration number for this education level and year
    \$stmt = \$pdo->prepare('SELECT COUNT(*) + 1 as next_number FROM students s 
                           JOIN education_levels el ON s.education_level_id = el.id 
                           WHERE el.code = ? AND YEAR(s.created_at) = ?');
    \$stmt->execute([\$education_level_code, \$year]);
    \$result = \$stmt->fetch();
    \$next_number = \$result['next_number'];
    
    return \$year . \$education_level_code . str_pad(\$next_number, 3, '0', STR_PAD_LEFT);
}
?>";

    file_put_contents('config.php', $config_content);
    $messages[] = "✅ File konfigurasi dibuat";

    // Create .env file
    $env_content = "# Database Configuration
DB_HOST={$db_host}
DB_NAME={$db_name}
DB_USER={$db_user}
DB_PASS={$db_pass}

# Application Settings
APP_NAME=\"SPMB Cendekia Muslim\"
APP_URL=https://spmb.cendekiamuslim.or.id
DEBUG_MODE=false

# WhatsApp Configuration
WHATSAPP_API_TOKEN=
WHATSAPP_NUMBER=6282177729934

# Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=
SMTP_PASS=
SMTP_FROM_EMAIL=noreply@cendekiamuslim.or.id
SMTP_FROM_NAME=\"SPMB Cendekia Muslim\"

# Payment Gateway (Configure based on your provider)
PAYMENT_GATEWAY_API_KEY=
PAYMENT_GATEWAY_SECRET=
PAYMENT_GATEWAY_MERCHANT_ID=

# File Upload
MAX_FILE_SIZE=5242880
UPLOAD_PATH=uploads/

# Security
SESSION_LIFETIME=7200
RATE_LIMIT_PER_HOUR=100
";

    file_put_contents('.env', $env_content);
    $messages[] = "✅ File environment configuration dibuat";

    // Create .htaccess for clean URLs
    $htaccess_content = "RewriteEngine On

# Redirect to HTTPS (uncomment for production)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Clean URLs
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([^/]+)/?$ $1.php [L,QSA]

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection \"1; mode=block\"
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"
</IfModule>

# Prevent access to sensitive files
<Files \".env\">
    Order allow,deny
    Deny from all
</Files>

<Files \"config.php\">
    Order allow,deny
    Deny from all
</Files>
";

    file_put_contents('.htaccess', $htaccess_content);
    $messages[] = "✅ File .htaccess dibuat";

    // Create lock file
    file_put_contents($lock_file, date('Y-m-d H:i:s') . "\nSetup completed successfully for SPMB Cendekia Muslim");
    $messages[] = "✅ Setup dikunci untuk keamanan";

    $setup_success = true;

} catch (Exception $e) {
    $messages[] = "❌ Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup Complete - SPMB Cendekia Muslim</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #28a745, #20c997);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
        }
        h1 {
            color: #28a745;
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
        }
        .message {
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 14px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .credentials {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .credentials h3 {
            margin-top: 0;
            color: #856404;
        }
        .security-note {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px 5px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }
        .next-steps {
            background: #e8f5e8;
            border: 1px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .next-steps h3 {
            margin-top: 0;
            color: #155724;
        }
        .next-steps ol {
            margin: 10px 0;
        }
        .next-steps li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Setup SPMB Cendekia Muslim Berhasil!</h1>

        <?php foreach ($messages as $msg): ?>
            <div class="message <?php echo strpos($msg, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endforeach; ?>

        <?php if ($setup_success): ?>
            <div class="credentials">
                <h3>🔐 Kredensial Login:</h3>
                <p><strong>Admin:</strong> username: <code>admin</code></p>
                <p><strong>Panitia SPMB:</strong> username: <code>panitia</code></p>
                <?php if (isset($_POST['create_sample_data'])): ?>
                <p><strong>Data Contoh:</strong></p>
                <ul>
                    <li>Affiliate: username: <code>affiliate001</code> / password: <code>affiliate123</code></li>
                    <li>Santri: username: <code>2026001001</code> / password: <code>student123</code></li>
                </ul>
                <?php endif; ?>
            </div>

            <div class="next-steps">
                <h3>📋 Langkah Selanjutnya:</h3>
                <ol>
                    <li>Konfigurasi file <code>.env</code> untuk WhatsApp, Email, dan Payment Gateway</li>
                    <li>Pastikan folder <code>uploads/</code> memiliki permission write (755)</li>
                    <li>Upload logo sekolah ke <code>assets/logos/logo.png</code></li>
                    <li>Upload brosur pendaftaran ke <code>assets/brochures/</code></li>
                    <li>Konfigurasi web server untuk clean URLs</li>
                    <li>Test login dengan kredensial di atas</li>
                    <li>Sesuaikan pengaturan sistem melalui panel admin</li>
                </ol>
            </div>

            <div class="security-note">
                <strong>⚠️ KEAMANAN PENTING:</strong>
                <ul>
                    <li>Setup telah dikunci dan tidak bisa dijalankan lagi</li>
                    <li>Hapus file <code>setup.php</code> dari server produksi</li>
                    <li>Gunakan HTTPS untuk produksi</li>
                    <li>Ganti password default secepatnya</li>
                    <li>Backup database secara berkala</li>
                    <li>Lindungi file <code>.env</code> dan <code>config.php</code> dari akses publik</li>
                </ul>
            </div>

            <div class="actions">
                <a href="index.html" class="btn">🏠 Buka Landing Page</a>
                <a href="login.php" class="btn">🔑 Login Admin</a>
                <a href="register.php" class="btn">📝 Pendaftaran Santri</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>