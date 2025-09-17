<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPMB Cendekia Muslim</title>
    
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
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }

        .login-left {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23pattern)"/></svg>');
            opacity: 0.3;
        }

        .login-left-content {
            position: relative;
            z-index: 2;
        }

        .logo {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            font-size: 3rem;
            color: var(--primary-green);
        }

        .login-right {
            padding: 3rem;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #6c757d;
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

        .form-control {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
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
            border-color: var(--primary-green);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .btn-login:disabled {
            opacity: 0.6;
            transform: none;
            box-shadow: none;
        }

        .role-selector {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .role-option {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .role-option:hover {
            border-color: var(--primary-green);
            background: var(--light-green);
        }

        .role-option.active {
            border-color: var(--primary-green);
            background: var(--light-green);
            color: var(--primary-green);
            font-weight: 600;
        }

        .role-option input[type="radio"] {
            display: none;
        }

        .forgot-password {
            color: var(--primary-green);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-password:hover {
            color: var(--primary-green);
            text-decoration: underline;
        }

        .register-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
        }

        .register-link a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            color: var(--primary-green);
            text-decoration: underline;
        }

        .alert-custom {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-danger-custom {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-success-custom {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .quick-login {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
        }

        .quick-login h6 {
            color: var(--dark);
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .quick-login-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quick-login-item:hover {
            background: var(--light-green);
        }

        .quick-login-item:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .login-container {
                flex-direction: column;
            }
            
            .login-left {
                padding: 2rem;
            }
            
            .login-right {
                padding: 2rem;
            }
            
            .logo {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="row g-0 h-100">
            <!-- Left Side -->
            <div class="col-lg-6">
                <div class="login-left h-100">
                    <div class="login-left-content">
                        <div class="logo">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h2 class="mb-3">Selamat Datang!</h2>
                        <p class="mb-4">
                            Sistem Penerimaan Murid Baru<br>
                            <strong>Cendekia Muslim Islamic School</strong>
                        </p>
                        <p class="opacity-75">
                            "The Excellence Quranic Leader and Entrepreneur School"
                        </p>
                        <div class="mt-4">
                            <small>Tahun Ajaran 2026/2027</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side -->
            <div class="col-lg-6">
                <div class="login-right">
                    <div class="login-header">
                        <h2>Login ke Akun Anda</h2>
                        <p>Masukkan kredensial untuk mengakses sistem SPMB</p>
                    </div>

                    <!-- Alert Messages -->
                    <div id="alertMessage" style="display: none;"></div>

                    <!-- Login Form -->
                    <form id="loginForm">
                        <!-- Role Selection -->
                        <div class="form-group">
                            <label class="form-label">Pilih Role Login:</label>
                            <div class="role-selector">
                                <div class="role-option active" onclick="selectRole('santri')">
                                    <input type="radio" name="role" value="santri" id="role_santri" checked>
                                    <div>
                                        <i class="fas fa-user-graduate d-block mb-1"></i>
                                        <small>Santri</small>
                                    </div>
                                </div>
                                <div class="role-option" onclick="selectRole('panitia')">
                                    <input type="radio" name="role" value="panitia" id="role_panitia">
                                    <div>
                                        <i class="fas fa-users-cog d-block mb-1"></i>
                                        <small>Panitia</small>
                                    </div>
                                </div>
                                <div class="role-option" onclick="selectRole('affiliate')">
                                    <input type="radio" name="role" value="affiliate" id="role_affiliate">
                                    <div>
                                        <i class="fas fa-handshake d-block mb-1"></i>
                                        <small>Affiliate</small>
                                    </div>
                                </div>
                                <div class="role-option" onclick="selectRole('admin')">
                                    <input type="radio" name="role" value="admin" id="role_admin">
                                    <div>
                                        <i class="fas fa-user-shield d-block mb-1"></i>
                                        <small>Admin</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Masukkan username" required>
                            </div>
                            <small class="text-muted" id="usernameHint">
                                Untuk santri: gunakan kode pendaftaran (contoh: 2026001001)
                            </small>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Masukkan password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                            <a href="#" class="forgot-password" onclick="forgotPassword()">
                                Lupa password?
                            </a>
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="btn btn-login" id="loginBtn">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Login
                        </button>
                    </form>

                    <!-- Quick Login for Demo -->
                    <div class="quick-login">
                        <h6><i class="fas fa-bolt me-1"></i>Quick Login (Demo):</h6>
                        <div class="quick-login-item" onclick="quickLogin('admin', 'admin123')">
                            <span><strong>Admin</strong></span>
                            <small class="text-muted">admin / admin123</small>
                        </div>
                        <div class="quick-login-item" onclick="quickLogin('panitia', 'panitia123')">
                            <span><strong>Panitia SPMB</strong></span>
                            <small class="text-muted">panitia / panitia123</small>
                        </div>
                        <div class="quick-login-item" onclick="quickLogin('2026001001', 'student123')">
                            <span><strong>Santri Demo</strong></span>
                            <small class="text-muted">2026001001 / student123</small>
                        </div>
                        <div class="quick-login-item" onclick="quickLogin('affiliate001', 'affiliate123')">
                            <span><strong>Affiliate Demo</strong></span>
                            <small class="text-muted">affiliate001 / affiliate123</small>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <div class="register-link">
                        <p class="mb-0">
                            Belum punya akun? 
                            <a href="register.php">Daftar sebagai santri baru</a>
                        </p>
                        <p class="mt-2 mb-0">
                            <a href="affiliate-register.php">Daftar sebagai affiliate</a>
                        </p>
                        <p class="mt-3 mb-0">
                            <a href="index.html" class="text-muted">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke beranda
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Role selection
        function selectRole(role) {
            // Remove active class from all options
            document.querySelectorAll('.role-option').forEach(option => {
                option.classList.remove('active');
            });
            
            // Add active class to selected option
            document.querySelector(`#role_${role}`).closest('.role-option').classList.add('active');
            document.querySelector(`#role_${role}`).checked = true;
            
            // Update username hint
            const usernameHint = document.getElementById('usernameHint');
            switch(role) {
                case 'santri':
                    usernameHint.textContent = 'Untuk santri: gunakan kode pendaftaran (contoh: 2026001001)';
                    break;
                case 'panitia':
                    usernameHint.textContent = 'Username untuk panitia SPMB';
                    break;
                case 'affiliate':
                    usernameHint.textContent = 'Username affiliate atau email terdaftar';
                    break;
                case 'admin':
                    usernameHint.textContent = 'Username administrator sistem';
                    break;
            }
        }

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Quick login for demo
        function quickLogin(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            
            // Select appropriate role
            if (username === 'admin') {
                selectRole('admin');
            } else if (username === 'panitia') {
                selectRole('panitia');
            } else if (username.startsWith('affiliate')) {
                selectRole('affiliate');
            } else {
                selectRole('santri');
            }
        }

        // Forgot password
        function forgotPassword() {
            const role = document.querySelector('input[name="role"]:checked').value;
            const username = document.getElementById('username').value;
            
            if (!username) {
                showAlert('Masukkan username terlebih dahulu', 'danger');
                return;
            }
            
            // Show loading
            showAlert('Mengirim link reset password...', 'info');
            
            // Simulate forgot password process
            setTimeout(() => {
                showAlert(`Link reset password telah dikirim ke email yang terdaftar untuk ${role}: ${username}`, 'success');
            }, 2000);
        }

        // Show alert message
        function showAlert(message, type) {
            const alertDiv = document.getElementById('alertMessage');
            let alertClass = '';
            let icon = '';
            
            switch(type) {
                case 'success':
                    alertClass = 'alert-success-custom';
                    icon = 'fas fa-check-circle';
                    break;
                case 'danger':
                    alertClass = 'alert-danger-custom';
                    icon = 'fas fa-exclamation-circle';
                    break;
                case 'info':
                    alertClass = 'alert-info-custom';
                    icon = 'fas fa-info-circle';
                    break;
            }
            
            alertDiv.innerHTML = `
                <div class="alert-custom ${alertClass}">
                    <i class="${icon} me-2"></i>${message}
                </div>
            `;
            alertDiv.style.display = 'block';
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                alertDiv.style.display = 'none';
            }, 5000);
        }

        // Form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const loginBtn = document.getElementById('loginBtn');
            const originalText = loginBtn.innerHTML;
            
            // Show loading state
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
            loginBtn.disabled = true;
            
            // Get form values
            const username = formData.get('username');
            const password = formData.get('password');
            const role = formData.get('role');
            
            // Basic validation
            if (!username || !password) {
                showAlert('Username dan password harus diisi', 'danger');
                loginBtn.innerHTML = originalText;
                loginBtn.disabled = false;
                return;
            }
            
            // Simulate login process
            setTimeout(() => {
                // Demo login logic (replace with actual authentication)
                const validCredentials = [
                    { username: 'admin', password: 'admin123', role: 'admin', redirect: 'admin/dashboard.php' },
                    { username: 'panitia', password: 'panitia123', role: 'panitia', redirect: 'panitia/dashboard.php' },
                    { username: '2026001001', password: 'student123', role: 'santri', redirect: 'santri/dashboard.php' },
                    { username: 'affiliate001', password: 'affiliate123', role: 'affiliate', redirect: 'affiliate/dashboard.php' }
                ];
                
                const credential = validCredentials.find(cred => 
                    cred.username === username && 
                    cred.password === password && 
                    cred.role === role
                );
                
                if (credential) {
                    showAlert(`Login berhasil! Mengarahkan ke dashboard ${role}...`, 'success');
                    
                    // Store session info (in real app, this would be server-side)
                    localStorage.setItem('userSession', JSON.stringify({
                        username: username,
                        role: role,
                        loginTime: new Date().toISOString()
                    }));
                    
                    // Redirect to appropriate dashboard
                    setTimeout(() => {
                        window.location.href = credential.redirect;
                    }, 2000);
                } else {
                    showAlert('Username, password, atau role tidak valid', 'danger');
                    loginBtn.innerHTML = originalText;
                    loginBtn.disabled = false;
                }
            }, 1500);
        });

        // Check if user is already logged in
        document.addEventListener('DOMContentLoaded', function() {
            const userSession = localStorage.getItem('userSession');
            if (userSession) {
                const session = JSON.parse(userSession);
                const loginTime = new Date(session.loginTime);
                const now = new Date();
                const timeDiff = (now - loginTime) / (1000 * 60 * 60); // hours
                
                // If session is less than 8 hours old, redirect to dashboard
                if (timeDiff < 8) {
                    showAlert(`Anda sudah login sebagai ${session.role}. Mengarahkan ke dashboard...`, 'info');
                    setTimeout(() => {
                        window.location.href = `${session.role}/dashboard.php`;
                    }, 2000);
                } else {
                    // Clear expired session
                    localStorage.removeItem('userSession');
                }
            }
        });

        // Handle Enter key on form fields
        document.querySelectorAll('#loginForm input').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('loginForm').dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>
</body>
</html>