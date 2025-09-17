<?php
/**
 * Database Setup Script
 * Setup database lengkap untuk Sistem SPMB Cendekia Muslim
 */

require_once 'config.php';

try {
    echo "🚀 Memulai setup database...\n\n";
    
    // Create database tables
    createTables();
    
    // Insert default data
    insertDefaultData();
    
    // Create default admin account
    createDefaultAdmin();
    
    echo "✅ Setup database berhasil!\n\n";
    echo "📋 Informasi Login Admin:\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "Role: admin\n\n";
    echo "⚠️  Jangan lupa ganti password default setelah login!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

function createTables() {
    global $pdo;
    
    echo "📋 Membuat tabel database...\n";
    
    // Users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'operator', 'santri', 'affiliate') NOT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100),
            phone VARCHAR(20),
            is_active BOOLEAN DEFAULT 1,
            last_login DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_username (username),
            INDEX idx_role (role),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB
    ");
    
    // Students table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            registration_number VARCHAR(20) UNIQUE NOT NULL,
            nama_lengkap VARCHAR(100) NOT NULL,
            tempat_lahir VARCHAR(50),
            tanggal_lahir DATE,
            jenis_kelamin ENUM('L', 'P') NOT NULL,
            alamat TEXT,
            nama_ayah VARCHAR(100),
            nama_ibu VARCHAR(100),
            no_hp_ortu VARCHAR(20),
            email VARCHAR(100),
            asal_sekolah VARCHAR(100),
            status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
            affiliate_code VARCHAR(20),
            verification_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_registration_number (registration_number),
            INDEX idx_status (status),
            INDEX idx_affiliate_code (affiliate_code)
        ) ENGINE=InnoDB
    ");
    
    // Student Documents table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS student_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            document_type ENUM('foto', 'ktp', 'ijazah', 'rapor', 'kk', 'akte_lahir', 'lainnya') NOT NULL,
            document_name VARCHAR(100) NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            file_size INT,
            is_verified BOOLEAN DEFAULT 0,
            verification_notes TEXT,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            INDEX idx_student_id (student_id),
            INDEX idx_document_type (document_type)
        ) ENGINE=InnoDB
    ");
    
    // Payments table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            payment_code VARCHAR(50) UNIQUE NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_type ENUM('registration', 'admission', 'monthly') NOT NULL,
            payment_method ENUM('bank_transfer', 'va', 'ewallet', 'cash') NOT NULL,
            payment_status ENUM('pending', 'success', 'failed', 'expired') DEFAULT 'pending',
            payment_proof VARCHAR(255),
            payment_date DATETIME,
            verified_by INT,
            verification_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_payment_code (payment_code),
            INDEX idx_payment_status (payment_status),
            INDEX idx_student_id (student_id)
        ) ENGINE=InnoDB
    ");
    
    // Payment Methods table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payment_methods (
            id INT AUTO_INCREMENT PRIMARY KEY,
            method_type ENUM('bank_transfer', 'va', 'ewallet') NOT NULL,
            bank_name VARCHAR(50),
            account_number VARCHAR(50),
            account_name VARCHAR(100),
            description TEXT,
            is_active BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    
    // Affiliates table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS affiliates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            affiliate_code VARCHAR(20) UNIQUE NOT NULL,
            commission_rate DECIMAL(5,2) DEFAULT 10.00,
            total_referrals INT DEFAULT 0,
            total_commission DECIMAL(10,2) DEFAULT 0.00,
            bank_name VARCHAR(50),
            account_number VARCHAR(50),
            account_name VARCHAR(100),
            is_active BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_affiliate_code (affiliate_code)
        ) ENGINE=InnoDB
    ");
    
    // Commissions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS commissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            affiliate_id INT NOT NULL,
            student_id INT NOT NULL,
            payment_id INT NOT NULL,
            commission_amount DECIMAL(10,2) NOT NULL,
            commission_rate DECIMAL(5,2) NOT NULL,
            status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
            paid_at DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (affiliate_id) REFERENCES affiliates(id) ON DELETE CASCADE,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
            INDEX idx_affiliate_id (affiliate_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB
    ");
    
    // Notifications table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(200),
            message TEXT NOT NULL,
            data JSON,
            is_read BOOLEAN DEFAULT 0,
            read_at DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_is_read (is_read),
            INDEX idx_type (type)
        ) ENGINE=InnoDB
    ");
    
    // Messages table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            message TEXT NOT NULL,
            is_read BOOLEAN DEFAULT 0,
            read_at DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_sender_id (sender_id),
            INDEX idx_receiver_id (receiver_id),
            INDEX idx_is_read (is_read)
        ) ENGINE=InnoDB
    ");
    
    // Activity Logs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(100) NOT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB
    ");
    
    // Settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_setting_key (setting_key)
        ) ENGINE=InnoDB
    ");
    
    // WhatsApp Templates table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS whatsapp_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            template_name VARCHAR(100) UNIQUE NOT NULL,
            template_content TEXT NOT NULL,
            parameters JSON,
            is_active BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    
    // WhatsApp Logs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS whatsapp_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            phone VARCHAR(20) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('sent', 'failed', 'pending') NOT NULL,
            response TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_phone (phone),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB
    ");
    
    // Announcements table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS announcements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            content TEXT NOT NULL,
            target_audience ENUM('all', 'students', 'affiliates', 'admins') DEFAULT 'all',
            is_active BOOLEAN DEFAULT 1,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_target_audience (target_audience),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB
    ");
    
    echo "✅ Tabel database berhasil dibuat\n\n";
}

function insertDefaultData() {
    global $pdo;
    
    echo "📋 Memasukkan data default...\n";
    
    // Default settings
    $default_settings = [
        ['institution_name', 'Yayasan Cendekia Muslim', 'text', 'Nama Institusi'],
        ['contact_phone', '081234567890', 'text', 'Nomor Telepon Kontak'],
        ['contact_email', 'info@cendekiamuslim.com', 'text', 'Email Kontak'],
        ['base_url', 'https://spmb.cendekiamuslim.com', 'text', 'URL Dasar Sistem'],
        ['registration_fee', '100000', 'number', 'Biaya Pendaftaran'],
        ['admission_fee', '500000', 'number', 'Biaya Masuk'],
        ['affiliate_commission_rate', '10', 'number', 'Persentase Komisi Affiliate'],
        ['whatsapp_api_url', '', 'text', 'URL API WhatsApp'],
        ['whatsapp_api_key', '', 'text', 'API Key WhatsApp'],
        ['whatsapp_sender_number', '', 'text', 'Nomor Pengirim WhatsApp'],
        ['test_phone_number', '', 'text', 'Nomor Test WhatsApp']
    ];
    
    foreach ($default_settings as $setting) {
        $pdo->prepare("
            INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, description)
            VALUES (?, ?, ?, ?)
        ")->execute($setting);
    }
    
    // Default payment methods
    $payment_methods = [
        ['bank_transfer', 'BRI', '1234567890', 'Yayasan Cendekia Muslim', 'Transfer Bank BRI'],
        ['bank_transfer', 'BCA', '0987654321', 'Yayasan Cendekia Muslim', 'Transfer Bank BCA'],
        ['bank_transfer', 'Mandiri', '1122334455', 'Yayasan Cendekia Muslim', 'Transfer Bank Mandiri']
    ];
    
    foreach ($payment_methods as $method) {
        $pdo->prepare("
            INSERT IGNORE INTO payment_methods (method_type, bank_name, account_number, account_name, description)
            VALUES (?, ?, ?, ?, ?)
        ")->execute($method);
    }
    
    // Default WhatsApp templates
    $wa_templates = [
        [
            'registration_success',
            "🎉 *PENDAFTARAN BERHASIL* 🎉\n\nAssalamu'alaikum Wr. Wb.\n\nPendaftaran atas nama {nama_lengkap} telah berhasil.\n\n📝 *No. Pendaftaran:* {registration_number}\n👤 *Username:* {username}\n🔐 *Password:* {password}\n\nSilakan login ke sistem dan lengkapi data pendaftaran.\n\nJazakallahu khair 🤲",
            '["nama_lengkap", "registration_number", "username", "password"]'
        ],
        [
            'payment_success',
            "💰 *PEMBAYARAN DIKONFIRMASI* 💰\n\nAssalamu'alaikum Wr. Wb.\n\nAlhamdulillah, pembayaran {payment_type} sebesar Rp {amount} telah dikonfirmasi.\n\nTerima kasih atas kepercayaan Anda.\n\nJazakallahu khair 🤲",
            '["payment_type", "amount"]'
        ]
    ];
    
    foreach ($wa_templates as $template) {
        $pdo->prepare("
            INSERT IGNORE INTO whatsapp_templates (template_name, template_content, parameters)
            VALUES (?, ?, ?)
        ")->execute($template);
    }
    
    echo "✅ Data default berhasil dimasukkan\n\n";
}

function createDefaultAdmin() {
    global $pdo;
    
    echo "👤 Membuat akun admin default...\n";
    
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $name = 'Super Administrator';
    $email = 'admin@cendekiamuslim.com';
    
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("
            INSERT INTO users (username, password, role, name, email, is_active)
            VALUES (?, ?, 'admin', ?, ?, 1)
        ");
        $stmt->execute([$username, $password, $name, $email]);
        
        $admin_id = $pdo->lastInsertId();
        
        // Log admin creation
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, action, description, ip_address)
            VALUES (?, 'admin_created', 'Default admin account created during setup', ?)
        ");
        $stmt->execute([$admin_id, $_SERVER['SERVER_ADDR'] ?? 'localhost']);
        
        echo "✅ Admin default berhasil dibuat\n";
    } else {
        echo "ℹ️  Admin sudah ada, melewati pembuatan admin\n";
    }
    
    echo "\n";
}

/**
 * Additional utility functions for database maintenance
 */

function resetDatabase() {
    global $pdo;
    
    echo "⚠️  RESET DATABASE - MENGHAPUS SEMUA DATA!\n";
    echo "Apakah Anda yakin? Ketik 'YES' untuk melanjutkan: ";
    
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim($line) !== 'YES') {
        echo "Reset dibatalkan.\n";
        return;
    }
    
    $tables = [
        'whatsapp_logs', 'whatsapp_templates', 'announcements', 'activity_logs',
        'messages', 'notifications', 'commissions', 'affiliates', 'payment_methods',
        'payments', 'student_documents', 'students', 'users', 'settings'
    ];
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS $table");
        echo "🗑️  Tabel $table dihapus\n";
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "✅ Database berhasil direset\n\n";
    
    // Recreate tables and data
    createTables();
    insertDefaultData();
    createDefaultAdmin();
}

function updateDatabase() {
    echo "🔄 Update database belum tersedia\n";
    echo "Fitur ini akan ditambahkan untuk update schema di versi mendatang\n";
}

// CLI interface
if (php_sapi_name() === 'cli') {
    $action = $argv[1] ?? 'setup';
    
    switch ($action) {
        case 'setup':
            // Already handled above
            break;
            
        case 'reset':
            resetDatabase();
            break;
            
        case 'update':
            updateDatabase();
            break;
            
        default:
            echo "Usage: php setup_database.php [setup|reset|update]\n";
            echo "  setup  - Setup database (default)\n";
            echo "  reset  - Reset dan recreate database\n";
            echo "  update - Update database schema\n";
            break;
    }
}
?>