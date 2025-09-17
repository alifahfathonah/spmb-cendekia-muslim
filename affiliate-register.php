<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Affiliate - SPMB Cendekia Muslim</title>
    
    <!-- CSS Framework -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #28a745;
            --secondary-green: #20c997;
            --light-green: #d4edda;
            --orange: #fd7e14;
            --blue: #007bff;
            --dark: #343a40;
            --purple: #6f42c1;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--purple), #563d7c);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .registration-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }

        .registration-header {
            background: linear-gradient(135deg, var(--purple), #563d7c);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .registration-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23pattern)"/></svg>');
            opacity: 0.3;
        }

        .registration-header-content {
            position: relative;
            z-index: 2;
        }

        .registration-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .registration-header p {
            opacity: 0.9;
            margin: 0;
        }

        .form-container {
            padding: 2rem;
        }

        .benefit-card {
            background: linear-gradient(135deg, var(--orange), #e07e00);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .benefit-list {
            list-style: none;
            padding: 0;
            margin: 1rem 0 0 0;
        }

        .benefit-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }

        .benefit-list li i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .commission-highlight {
            background: rgba(255,255,255,0.2);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin-top: 1rem;
        }

        .commission-amount {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .required {
            color: #dc3545;
        }

        .form-control {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--purple);
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #6c757d;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--purple);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--purple), #563d7c);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(111, 66, 193, 0.3);
            color: white;
        }

        .btn-register:disabled {
            opacity: 0.6;
            transform: none;
            box-shadow: none;
        }

        .login-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
        }

        .login-link a {
            color: var(--purple);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            color: var(--purple);
            text-decoration: underline;
        }

        .alert-custom {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-info-custom {
            background: #e3f2fd;
            color: #0c5460;
            border-left: 4px solid var(--blue);
        }

        .info-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-section h6 {
            color: var(--purple);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .referral-preview {
            background: #e8f5e8;
            border: 1px solid var(--primary-green);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .referral-preview code {
            background: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: var(--purple);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .registration-container {
                margin: 0;
            }
            
            .registration-header {
                padding: 1.5rem;
            }
            
            .form-container {
                padding: 1.5rem;
            }
            
            .registration-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <!-- Header -->
        <div class="registration-header">
            <div class="registration-header-content">
                <h1><i class="fas fa-handshake me-2"></i>Daftar Affiliate</h1>
                <p>Bergabunglah dengan program affiliate SPMB Cendekia Muslim</p>
                <p><small>Dapatkan komisi dari setiap referral yang berhasil</small></p>
            </div>
        </div>

        <div class="form-container">
            <!-- Benefits Section -->
            <div class="benefit-card">
                <h5 class="mb-3"><i class="fas fa-star me-2"></i>Keuntungan Menjadi Affiliate</h5>
                <ul class="benefit-list">
                    <li><i class="fas fa-money-bill-wave"></i>Komisi 50% dari biaya administrasi pendaftaran</li>
                    <li><i class="fas fa-chart-line"></i>Potensi penghasilan hingga jutaan rupiah</li>
                    <li><i class="fas fa-users"></i>Tidak ada batasan jumlah referral</li>
                    <li><i class="fas fa-clock"></i>Pencairan komisi cepat (1-3 hari kerja)</li>
                    <li><i class="fas fa-trophy"></i>Sistem ranking dan reward menarik</li>
                    <li><i class="fas fa-headset"></i>Dukungan marketing material lengkap</li>
                </ul>
                
                <div class="commission-highlight">
                    <p class="mb-1">Komisi Per Referral:</p>
                    <div class="commission-amount">50%</div>
                    <small>Dari biaya administrasi pendaftaran</small>
                </div>
            </div>

            <!-- Info Section -->
            <div class="info-section">
                <h6><i class="fas fa-info-circle me-2"></i>Cara Kerja Program Affiliate</h6>
                <ol class="mb-0">
                    <li>Daftar sebagai affiliate dengan mengisi form di bawah</li>
                    <li>Dapatkan link referral unik Anda</li>
                    <li>Bagikan link ke keluarga, teman, dan media sosial</li>
                    <li>Setiap orang yang mendaftar melalui link Anda akan memberikan komisi</li>
                    <li>Komisi akan masuk ke e-wallet Anda setelah pembayaran administrasi</li>
                    <li>Cairkan komisi kapan saja (minimum Rp 100.000)</li>
                </ol>
            </div>

            <!-- Registration Form -->
            <form id="affiliateForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="full_name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required
                                   placeholder="Nama sesuai KTP">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-label">Nomor WhatsApp <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="tel" class="form-control" id="phone" name="phone" required
                                       placeholder="81234567890">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email <span class="required">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required
                           placeholder="email@example.com">
                    <small class="text-muted">Email akan digunakan sebagai username login</small>
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Alamat Lengkap <span class="required">*</span></label>
                    <textarea class="form-control" id="address" name="address" rows="3" required
                              placeholder="Alamat lengkap untuk keperluan transfer komisi"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bank_name" class="form-label">Bank <span class="required">*</span></label>
                            <select class="form-control" id="bank_name" name="bank_name" required>
                                <option value="">Pilih Bank</option>
                                <option value="BSI">Bank Syariah Indonesia (BSI)</option>
                                <option value="BRI">Bank Rakyat Indonesia (BRI)</option>
                                <option value="BNI">Bank Negara Indonesia (BNI)</option>
                                <option value="BCA">Bank Central Asia (BCA)</option>
                                <option value="Mandiri">Bank Mandiri</option>
                                <option value="Other">Bank Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bank_account" class="form-label">Nomor Rekening <span class="required">*</span></label>
                            <input type="text" class="form-control" id="bank_account" name="bank_account" required
                                   placeholder="Nomor rekening untuk transfer komisi">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bank_holder" class="form-label">Nama Pemegang Rekening <span class="required">*</span></label>
                    <input type="text" class="form-control" id="bank_holder" name="bank_holder" required
                           placeholder="Nama sesuai rekening bank">
                </div>

                <div class="form-group">
                    <label for="referral_source" class="form-label">Bagaimana Anda mengetahui program ini?</label>
                    <select class="form-control" id="referral_source" name="referral_source">
                        <option value="">Pilih sumber informasi</option>
                        <option value="website">Website resmi</option>
                        <option value="social_media">Media sosial</option>
                        <option value="friend">Teman/Keluarga</option>
                        <option value="affiliate">Affiliate lain</option>
                        <option value="advertisement">Iklan</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="motivation" class="form-label">Motivasi bergabung (opsional)</label>
                    <textarea class="form-control" id="motivation" name="motivation" rows="3"
                              placeholder="Ceritakan mengapa Anda tertarik menjadi affiliate..."></textarea>
                </div>

                <!-- Custom Referral Code -->
                <div class="form-group">
                    <label for="custom_code" class="form-label">Kode Referral Kustom (opsional)</label>
                    <input type="text" class="form-control" id="custom_code" name="custom_code" 
                           placeholder="affiliate_nama" pattern="[a-zA-Z0-9_]+" maxlength="20">
                    <small class="text-muted">Hanya huruf, angka, dan underscore. Jika kosong akan di-generate otomatis.</small>
                    
                    <div class="referral-preview" id="referralPreview" style="display: none;">
                        <h6><i class="fas fa-link me-2"></i>Preview Link Referral Anda:</h6>
                        <p class="mb-0">
                            <code id="previewLink">https://spmb.cendekiamuslim.or.id/register.php?ref=</code>
                        </p>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            Saya menyetujui <a href="#" onclick="showTerms()" target="_blank">syarat dan ketentuan</a> program affiliate SPMB Cendekia Muslim
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                        <label class="form-check-label" for="privacy">
                            Saya setuju dengan <a href="#" onclick="showPrivacy()" target="_blank">kebijakan privasi</a> dan penggunaan data saya
                        </label>
                    </div>
                </div>

                <!-- Alert Messages -->
                <div id="alertMessage" style="display: none;"></div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-register" id="submitBtn">
                    <i class="fas fa-user-plus me-2"></i>
                    Daftar Sebagai Affiliate
                </button>
            </form>

            <!-- Login Link -->
            <div class="login-link">
                <p class="mb-0">
                    Sudah punya akun affiliate? 
                    <a href="login.php">Login di sini</a>
                </p>
                <p class="mt-2 mb-0">
                    <a href="index.html" class="text-muted">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke beranda
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Syarat dan Ketentuan Program Affiliate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Ketentuan Umum</h6>
                    <ul>
                        <li>Program affiliate terbuka untuk individu berusia minimal 17 tahun</li>
                        <li>Affiliate harus memiliki rekening bank atas nama sendiri</li>
                        <li>Satu orang hanya boleh memiliki satu akun affiliate</li>
                    </ul>

                    <h6>2. Komisi dan Pembayaran</h6>
                    <ul>
                        <li>Komisi sebesar 50% dari biaya administrasi pendaftaran</li>
                        <li>Komisi dibayarkan setelah santri melakukan pembayaran administrasi</li>
                        <li>Minimum pencairan komisi Rp 100.000</li>
                        <li>Pencairan diproses dalam 1-3 hari kerja</li>
                    </ul>

                    <h6>3. Kewajiban Affiliate</h6>
                    <ul>
                        <li>Menyampaikan informasi yang akurat tentang sekolah</li>
                        <li>Tidak melakukan spam atau promosi yang mengganggu</li>
                        <li>Tidak menggunakan cara yang menyesatkan atau tidak etis</li>
                        <li>Mematuhi semua peraturan yang berlaku</li>
                    </ul>

                    <h6>4. Larangan</h6>
                    <ul>
                        <li>Penggunaan iklan berbayar tanpa persetujuan</li>
                        <li>Manipulasi atau kecurangan dalam sistem</li>
                        <li>Menggunakan nama atau logo sekolah tanpa izin</li>
                        <li>Memberikan janji yang tidak dapat dipenuhi sekolah</li>
                    </ul>

                    <h6>5. Pemutusan Program</h6>
                    <p>Kami berhak memutuskan kerjasama affiliate jika ditemukan pelanggaran terhadap syarat dan ketentuan ini.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Custom referral code preview
        document.getElementById('custom_code').addEventListener('input', function() {
            const customCode = this.value.trim();
            const preview = document.getElementById('referralPreview');
            const previewLink = document.getElementById('previewLink');
            
            if (customCode) {
                const baseUrl = 'https://spmb.cendekiamuslim.or.id/register.php?ref=';
                previewLink.textContent = baseUrl + customCode;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });

        // Form validation
        function validateForm() {
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const customCode = document.getElementById('custom_code').value;

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('Format email tidak valid', 'danger');
                return false;
            }

            // Phone validation
            const phoneRegex = /^[0-9]{10,13}$/;
            if (!phoneRegex.test(phone)) {
                showAlert('Nomor WhatsApp harus 10-13 digit angka', 'danger');
                return false;
            }

            // Custom code validation
            if (customCode) {
                const codeRegex = /^[a-zA-Z0-9_]+$/;
                if (!codeRegex.test(customCode)) {
                    showAlert('Kode referral hanya boleh berisi huruf, angka, dan underscore', 'danger');
                    return false;
                }
                if (customCode.length < 3 || customCode.length > 20) {
                    showAlert('Kode referral harus 3-20 karakter', 'danger');
                    return false;
                }
            }

            return true;
        }

        // Form submission
        document.getElementById('affiliateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses pendaftaran...';
            submitBtn.disabled = true;
            
            // Get form data
            const formData = new FormData(this);
            
            // Simulate registration process
            setTimeout(() => {
                // Check if email already exists (simulation)
                const email = formData.get('email');
                if (email === 'existing@example.com') {
                    showAlert('Email sudah terdaftar. Gunakan email lain atau login jika sudah memiliki akun.', 'danger');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    return;
                }
                
                // Success simulation
                showAlert('Pendaftaran affiliate berhasil! Silakan login untuk mengakses dashboard Anda.', 'success');
                
                // Store affiliate data (simulation)
                const affiliateData = {
                    name: formData.get('full_name'),
                    email: formData.get('email'),
                    phone: formData.get('phone'),
                    customCode: formData.get('custom_code') || generateRandomCode(),
                    joinDate: new Date().toISOString()
                };
                
                localStorage.setItem('newAffiliateData', JSON.stringify(affiliateData));
                
                // Redirect to login with success message
                setTimeout(() => {
                    window.location.href = 'login.php?registered=affiliate';
                }, 2000);
                
            }, 2000);
        });

        // Generate random affiliate code
        function generateRandomCode() {
            const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
            let result = 'affiliate';
            for (let i = 0; i < 3; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return result;
        }

        // Show alert message
        function showAlert(message, type) {
            const alertDiv = document.getElementById('alertMessage');
            let alertClass = '';
            let icon = '';
            
            switch(type) {
                case 'success':
                    alertClass = 'alert-success';
                    icon = 'fas fa-check-circle';
                    break;
                case 'danger':
                    alertClass = 'alert-danger';
                    icon = 'fas fa-exclamation-circle';
                    break;
                case 'info':
                    alertClass = 'alert-info';
                    icon = 'fas fa-info-circle';
                    break;
            }
            
            alertDiv.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show">
                    <i class="${icon} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertDiv.style.display = 'block';
            
            // Auto hide after 5 seconds for success messages
            if (type === 'success') {
                setTimeout(() => {
                    alertDiv.style.display = 'none';
                }, 5000);
            }
        }

        // Show terms modal
        function showTerms() {
            const modal = new bootstrap.Modal(document.getElementById('termsModal'));
            modal.show();
        }

        // Show privacy policy (placeholder)
        function showPrivacy() {
            alert('Kebijakan Privasi:\n\nData pribadi Anda akan dijaga kerahasiaannya dan hanya digunakan untuk keperluan program affiliate. Kami tidak akan membagikan data Anda kepada pihak ketiga tanpa persetujuan Anda.');
        }

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, ''); // Remove non-digits
            
            // Remove leading zero if exists
            if (value.startsWith('0')) {
                value = value.substring(1);
            }
            
            // Limit to 13 digits
            if (value.length > 13) {
                value = value.substring(0, 13);
            }
            
            this.value = value;
        });

        // Auto-fill bank holder name from full name
        document.getElementById('full_name').addEventListener('input', function() {
            const bankHolderField = document.getElementById('bank_holder');
            if (!bankHolderField.value) {
                bankHolderField.value = this.value.toUpperCase();
            }
        });

        // Custom code formatting
        document.getElementById('custom_code').addEventListener('input', function() {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
        });
    </script>
</body>
</html>