# 📚 Panduan Instalasi Sistem SPMB Cendekia Muslim

## 🎯 Deskripsi Sistem

Sistem Penerimaan Mahasiswa Baru (SPMB) Cendekia Muslim adalah platform lengkap untuk mengelola pendaftaran santri dengan fitur:

- ✅ **Sistem Pendaftaran Online** - Formulir pendaftaran lengkap dengan upload dokumen
- 💰 **Sistem Pembayaran Terintegrasi** - Multiple payment gateway dan verifikasi otomatis
- 🤝 **Sistem Affiliate** - Program referral dengan komisi otomatis
- 📱 **Notifikasi WhatsApp** - Automated WhatsApp notifications
- 📊 **Dashboard Admin** - Monitoring dan manajemen lengkap
- 🔔 **Real-time Notifications** - Sistem notifikasi real-time via AJAX

---

## 🛠️ Persiapan Server

### Requirement Minimum:
- **PHP** 7.4+ (Recommended: PHP 8.0+)
- **MySQL** 5.7+ atau **MariaDB** 10.3+
- **Web Server** Apache/Nginx
- **SSL Certificate** (Recommended untuk production)
- **Storage** minimum 1GB untuk file uploads

### PHP Extensions Required:
```bash
php-mysql
php-pdo
php-json
php-curl
php-gd
php-fileinfo
php-zip
```

---

## 📦 File Structure

Pastikan struktur folder seperti ini:

```
spmb-cendekia-muslim/
├── 📁 assets/
│   ├── 📁 css/
│   │   ├── style.css
│   │   └── notifications.css
│   ├── 📁 js/
│   │   └── app.js
│   └── 📁 images/
├── 📁 uploads/
│   ├── 📁 documents/
│   ├── 📁 payments/
│   └── 📁 profile/
├── 📁 admin/
├── 📁 santri/
├── 📁 affiliate/
├── 📄 config.php
├── 📄 functions.php
├── 📄 api.php
├── 📄 whatsapp_handler.php
├── 📄 setup_database.php
├── 📄 index.php
├── 📄 login.php
└── 📄 register.php
```

---

## 🚀 Langkah Instalasi

### 1. **Upload Files ke Server**

Upload semua file ke direktori web server (public_html/www):

```bash
# Via FTP/SFTP
# Upload semua file ke folder public_html

# Via Git (jika menggunakan version control)
git clone [repository-url] /path/to/webroot
```

### 2. **Set Permissions**

Set permission yang benar untuk folder uploads:

```bash
chmod 755 uploads/
chmod 755 uploads/documents/
chmod 755 uploads/payments/
chmod 755 uploads/profile/
```

### 3. **Konfigurasi Database**

Edit file `config.php`:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');          // Host database
define('DB_NAME', 'spmb_cendekia');     // Nama database
define('DB_USER', 'your_db_username');   // Username database
define('DB_PASS', 'your_db_password');   // Password database

// Base URL Configuration
define('BASE_URL', 'https://yourdomain.com');

// Security Configuration
define('SECRET_KEY', 'your-secret-key-here');
?>
```

### 4. **Setup Database**

Jalankan script setup database:

```bash
# Via Browser
https://yourdomain.com/setup_database.php

# Via CLI (Recommended)
php setup_database.php
```

**Output yang diharapkan:**
```
🚀 Memulai setup database...

📋 Membuat tabel database...
✅ Tabel database berhasil dibuat

📋 Memasukkan data default...
✅ Data default berhasil dimasukkan

👤 Membuat akun admin default...
✅ Admin default berhasil dibuat

✅ Setup database berhasil!

📋 Informasi Login Admin:
Username: admin
Password: admin123
Role: admin

⚠️ Jangan lupa ganti password default setelah login!
```

### 5. **Konfigurasi SSL (Production)**

Untuk production, pastikan SSL aktif:

```apache
# .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## ⚙️ Konfigurasi Sistem

### 1. **Login Admin**

Akses halaman admin:
```
https://yourdomain.com/admin/
Username: admin
Password: admin123
```

### 2. **Ubah Password Default**

⚠️ **PENTING**: Segera ubah password default!

1. Login sebagai admin
2. Menu Settings → User Management
3. Edit user "admin"
4. Ubah password

### 3. **Konfigurasi Basic Settings**

Di Admin Panel → Settings:

```
✅ Nama Institusi: Yayasan Cendekia Muslim
✅ Nomor Kontak: +62812-3456-7890
✅ Email Kontak: info@cendekiamuslim.com
✅ Base URL: https://yourdomain.com
✅ Biaya Pendaftaran: 100000
✅ Biaya Masuk: 500000
```

### 4. **Setup Payment Methods**

Admin Panel → Payment Settings:

```
🏦 Bank BRI
   📱 Nomor Rekening: 1234-5678-90
   👤 Atas Nama: Yayasan Cendekia Muslim

🏦 Bank BCA  
   📱 Nomor Rekening: 0987-6543-21
   👤 Atas Nama: Yayasan Cendekia Muslim
```

---

## 📱 Konfigurasi WhatsApp

### 1. **Pilih Provider WhatsApp API**

Rekomendasi provider:
- **Fonnte.com** - Mudah setup, harga terjangkau
- **Wablas.com** - Fitur lengkap, support lokal
- **Twillio** - Enterprise grade, lebih mahal

### 2. **Konfigurasi WhatsApp API**

Di Admin Panel → WhatsApp Settings:

```php
// Contoh konfigurasi Fonnte
WhatsApp API URL: https://api.fonnte.com/send
API Key: your-fonnte-token
Sender Number: 6281234567890
```

### 3. **Test WhatsApp Connection**

```bash
# Test via admin panel
Admin Panel → WhatsApp → Test Connection

# Atau via file test
php -r "
require 'whatsapp_handler.php';
$wa = new WhatsAppHandler();
var_dump($wa->testConnection());
"
```

---

## 🔄 Testing Sistem

### 1. **Test Pendaftaran Santri**

1. Buka `https://yourdomain.com/register.php`
2. Isi form pendaftaran lengkap
3. Upload dokumen (JPG/PNG/PDF, max 5MB)
4. Submit form
5. Cek notifikasi WhatsApp masuk

### 2. **Test Login Santri**

1. Login dengan username/password dari notifikasi
2. Cek dashboard santri
3. Test upload dokumen tambahan
4. Test sistem pembayaran

### 3. **Test Admin Panel**

1. Login admin
2. Cek daftar pendaftar
3. Approve/reject pendaftar
4. Verifikasi pembayaran
5. Cek notifikasi real-time

### 4. **Test Sistem Affiliate**

1. Daftar sebagai affiliate
2. Dapatkan kode referral
3. Test pendaftaran dengan kode referral
4. Cek komisi di dashboard affiliate

---

## 🛡️ Security Checklist

### 1. **File Permissions**
```bash
# Set permission yang aman
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 755 uploads/
```

### 2. **Database Security**
- ✅ Gunakan password database yang kuat
- ✅ Buat user database khusus (jangan root)
- ✅ Batasi privileges database user

### 3. **File Upload Security**
- ✅ Validasi file type (sudah implemented)
- ✅ Limit file size (sudah implemented)
- ✅ Scan malware (recommended)

### 4. **SSL/HTTPS**
- ✅ Install SSL Certificate
- ✅ Force HTTPS redirect
- ✅ HSTS headers

---

## 📊 Monitoring & Maintenance

### 1. **Database Backup**

Setup automatic backup:

```bash
#!/bin/bash
# backup-db.sh
DATE=$(date +"%Y%m%d_%H%M%S")
mysqldump -u username -p password spmb_cendekia > backup_$DATE.sql
```

### 2. **Log Monitoring**

Monitor file logs:
```bash
# PHP Error Logs
tail -f /var/log/apache2/error.log

# WhatsApp Logs
tail -f uploads/logs/whatsapp.log

# Activity Logs (via admin panel)
Admin Panel → Logs → Activity Logs
```

### 3. **Performance Monitoring**

```sql
-- Monitor database performance
SHOW PROCESSLIST;
SHOW STATUS LIKE 'Slow_queries';

-- Monitor storage usage
SELECT table_name, 
       ROUND(data_length/1024/1024) AS data_mb,
       ROUND(index_length/1024/1024) AS index_mb
FROM information_schema.tables 
WHERE table_schema = 'spmb_cendekia';
```

---

## 🆘 Troubleshooting

### 1. **Database Connection Error**

```
Error: Connection failed: Access denied for user
```

**Solusi:**
- Cek username/password database di `config.php`
- Pastikan user database memiliki privileges
- Test koneksi database secara manual

### 2. **File Upload Error**

```
Error: Failed to upload file
```

**Solusi:**
- Cek permission folder `uploads/`
- Cek setting `upload_max_filesize` di `php.ini`
- Cek setting `post_max_size` di `php.ini`

### 3. **WhatsApp Not Working**

```
Error: WhatsApp API connection failed
```

**Solusi:**
- Cek API URL dan token di settings
- Test API via Postman/curl
- Cek saldo/quota provider WhatsApp

### 4. **Session Expired Frequently**

**Solusi:**
```php
// Tambah di config.php
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.cookie_lifetime', 3600);
```

### 5. **Slow Performance**

**Solusi:**
- Enable PHP OPcache
- Optimize database queries
- Add database indexes
- Enable gzip compression

---

## 📞 Support & Contact

### 🛠️ Technical Support
- **Documentation**: [Link to docs]
- **Issues**: [Link to GitHub issues]
- **Updates**: [Link to update channel]

### 🤝 Community
- **Telegram Group**: [Link]
- **WhatsApp Group**: [Link]
- **Forum**: [Link]

---

## 📋 Update System

### Checking Updates
```bash
# Check current version
cat VERSION

# Download updates
wget https://updates.cendekiamuslim.com/latest.zip

# Backup current system
cp -r /current/system /backup/$(date +%Y%m%d)

# Apply updates
unzip latest.zip
php update_database.php
```

### Version History
- **v1.0.0** - Initial release
- **v1.1.0** - Added WhatsApp integration
- **v1.2.0** - Enhanced security features
- **v1.3.0** - Added affiliate system

---

**🎉 Selamat! Sistem SPMB Cendekia Muslim sudah siap digunakan!**

> 💡 **Tips**: Backup sistem secara berkala dan monitor log aktivitas untuk keamanan optimal.

---

*Dokumentasi ini dibuat dengan ❤️ untuk kemudahan penggunaan sistem SPMB Cendekia Muslim*