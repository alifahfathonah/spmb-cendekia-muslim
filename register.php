<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran Santri Baru - Cendekia Muslim</title>
    
    <!-- CSS Framework -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet">
    
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
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
        }

        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 2rem 0;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
        }

        .step-item {
            display: flex;
            align-items: center;
            margin: 0 1rem;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dee2e6;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .step-item.completed .step-number {
            background: var(--primary-green);
            color: white;
        }

        .step-item.active .step-number {
            background: var(--orange);
            color: white;
        }

        .step-connector {
            width: 50px;
            height: 2px;
            background: #dee2e6;
            margin: 0 1rem;
        }

        .step-item.completed + .step-connector {
            background: var(--primary-green);
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

        .form-control, .form-select {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .education-level-card {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .education-level-card:hover {
            border-color: var(--primary-green);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .education-level-card.selected {
            border-color: var(--primary-green);
            background: var(--light-green);
        }

        .education-level-card input[type="radio"] {
            display: none;
        }

        .level-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            font-size: 1.5rem;
            color: white;
        }

        .package-option {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .package-option:hover {
            border-color: var(--primary-green);
        }

        .package-option.selected {
            border-color: var(--primary-green);
            background: var(--light-green);
        }

        .package-option input[type="radio"] {
            margin-right: 0.5rem;
        }

        .btn-custom {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            border: none;
            transition: all 0.3s ease;
            margin: 0.25rem;
        }

        .btn-primary-custom {
            background: var(--primary-green);
            color: white;
        }

        .btn-primary-custom:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-secondary-custom {
            background: #6c757d;
            color: white;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-orange-custom {
            background: var(--orange);
            color: white;
        }

        .btn-orange-custom:hover {
            background: #e8650e;
            transform: translateY(-2px);
        }

        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: var(--primary-green);
            background: #f8f9fa;
        }

        .file-upload-area.drag-over {
            border-color: var(--primary-green);
            background: var(--light-green);
        }

        .uploaded-file {
            background: var(--light-green);
            border: 1px solid var(--primary-green);
            border-radius: 8px;
            padding: 0.75rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: between;
        }

        .summary-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .summary-item:last-child {
            border-bottom: none;
            font-weight: 700;
            color: var(--primary-green);
        }

        .alert-info-custom {
            background: #e3f2fd;
            border-left: 4px solid var(--blue);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            color: var(--light-green) !important;
        }

        .referral-code-display {
            background: linear-gradient(135deg, var(--orange), #e07e00);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin: 1rem 0;
        }

        .referral-code-display h5 {
            margin: 0;
            font-size: 1.2rem;
        }

        .referral-code-display .code {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 2px;
        }

        @media (max-width: 768px) {
            .form-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .form-header h1 {
                font-size: 1.5rem;
            }
            
            .step-connector {
                width: 30px;
            }
            
            .step-item {
                margin: 0 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <i class="fas fa-graduation-cap me-2"></i>
                Cendekia Muslim
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">
                            <i class="fas fa-home me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="form-container">
            <!-- Form Header -->
            <div class="form-header">
                <h1><i class="fas fa-user-plus me-2"></i>Formulir Pendaftaran Santri Baru</h1>
                <p>Tahun Ajaran 2026/2027</p>
                <p><small>Lengkapi data dengan benar sesuai dokumen resmi</small></p>
            </div>

            <!-- Referral Code Display (if from referral link) -->
            <div id="referralCodeDisplay" style="display: none;">
                <div class="referral-code-display">
                    <h5><i class="fas fa-users me-2"></i>Kode Referral Aktif</h5>
                    <div class="code" id="displayedReferralCode"></div>
                    <small>Anda mendaftar melalui referral dan akan mendapat benefit khusus</small>
                </div>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step-item active" id="step1Indicator">
                    <div class="step-number">1</div>
                    <span>Jenjang</span>
                </div>
                <div class="step-connector"></div>
                <div class="step-item" id="step2Indicator">
                    <div class="step-number">2</div>
                    <span>Data Diri</span>
                </div>
                <div class="step-connector"></div>
                <div class="step-item" id="step3Indicator">
                    <div class="step-number">3</div>
                    <span>Keluarga</span>
                </div>
                <div class="step-connector"></div>
                <div class="step-item" id="step4Indicator">
                    <div class="step-number">4</div>
                    <span>Berkas</span>
                </div>
                <div class="step-connector"></div>
                <div class="step-item" id="step5Indicator">
                    <div class="step-number">5</div>
                    <span>Konfirmasi</span>
                </div>
            </div>

            <!-- Registration Form -->
            <form id="registrationForm" enctype="multipart/form-data">
                <!-- Step 1: Education Level Selection -->
                <div class="form-step active" id="step1">
                    <h3 class="mb-4"><i class="fas fa-school me-2"></i>Pilih Jenjang Pendidikan</h3>
                    
                    <div class="row">
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="education-level-card" onclick="selectEducationLevel('tk')">
                                <input type="radio" name="education_level" value="tk" id="level_tk">
                                <div class="level-icon">
                                    <i class="fas fa-child"></i>
                                </div>
                                <h5 class="text-center mb-2">TK Akhlak</h5>
                                <p class="text-center text-muted small mb-0">Taman Kanak-kanak</p>
                                <p class="text-center small mb-0"><strong>Biaya: Rp 100.000</strong></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="education-level-card" onclick="selectEducationLevel('sd')">
                                <input type="radio" name="education_level" value="sd" id="level_sd">
                                <div class="level-icon">
                                    <i class="fas fa-book-reader"></i>
                                </div>
                                <h5 class="text-center mb-2">SD Akhlak</h5>
                                <p class="text-center text-muted small mb-0">Sekolah Dasar</p>
                                <p class="text-center small mb-0"><strong>Biaya: Rp 150.000</strong></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="education-level-card" onclick="selectEducationLevel('smp')">
                                <input type="radio" name="education_level" value="smp" id="level_smp">
                                <div class="level-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <h5 class="text-center mb-2">SMP Akhlak</h5>
                                <p class="text-center text-muted small mb-0">Sekolah Menengah Pertama</p>
                                <p class="text-center small mb-0"><strong>Biaya: Rp 200.000</strong></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="education-level-card" onclick="selectEducationLevel('pkbm')">
                                <input type="radio" name="education_level" value="pkbm" id="level_pkbm">
                                <div class="level-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h5 class="text-center mb-2">PKBM</h5>
                                <p class="text-center text-muted small mb-0">Paket A, B, C</p>
                                <p class="text-center small mb-0"><strong>Biaya: Rp 50.000 - 100.000</strong></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="education-level-card" onclick="selectEducationLevel('lpq')">
                                <input type="radio" name="education_level" value="lpq" id="level_lpq">
                                <div class="level-icon">
                                    <i class="fas fa-quran"></i>
                                </div>
                                <h5 class="text-center mb-2">LPQ</h5>
                                <p class="text-center text-muted small mb-0">Lembaga Pendidikan Al-Quran</p>
                                <p class="text-center small mb-0"><strong>Biaya: GRATIS</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- PKBM Package Selection (Hidden by default) -->
                    <div id="pkbmPackages" style="display: none;">
                        <h5 class="mt-4 mb-3">Pilih Paket PKBM:</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="package-option" onclick="selectPackage('A')">
                                    <input type="radio" name="pkbm_package" value="A" id="package_a">
                                    <strong>Paket A</strong><br>
                                    <small class="text-muted">Setara SD</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="package-option" onclick="selectPackage('B')">
                                    <input type="radio" name="pkbm_package" value="B" id="package_b">
                                    <strong>Paket B</strong><br>
                                    <small class="text-muted">Setara SMP</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="package-option" onclick="selectPackage('C')">
                                    <input type="radio" name="pkbm_package" value="C" id="package_c">
                                    <strong>Paket C</strong><br>
                                    <small class="text-muted">Setara SMA</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-custom btn-primary-custom" onclick="nextStep(2)">
                            Lanjut ke Data Diri <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Personal Data -->
                <div class="form-step" id="step2">
                    <h3 class="mb-4"><i class="fas fa-user me-2"></i>Data Diri Calon Santri</h3>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="full_name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required
                                       placeholder="Sesuai akta kelahiran">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nickname" class="form-label">Nama Panggilan</label>
                                <input type="text" class="form-control" id="nickname" name="nickname"
                                       placeholder="Nama panggilan sehari-hari">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="gender" class="form-label">Jenis Kelamin <span class="required">*</span></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="birth_place" class="form-label">Tempat Lahir <span class="required">*</span></label>
                                <input type="text" class="form-control" id="birth_place" name="birth_place" required
                                       placeholder="Kota/Kabupaten kelahiran">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="birth_date" class="form-label">Tanggal Lahir <span class="required">*</span></label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="religion" class="form-label">Agama <span class="required">*</span></label>
                                <select class="form-select" id="religion" name="religion" required>
                                    <option value="Islam" selected>Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="citizenship" class="form-label">Kewarganegaraan <span class="required">*</span></label>
                                <select class="form-select" id="citizenship" name="citizenship" required>
                                    <option value="WNI" selected>WNI (Warga Negara Indonesia)</option>
                                    <option value="WNA">WNA (Warga Negara Asing)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nik" class="form-label">NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" class="form-control" id="nik" name="nik" maxlength="16"
                                       placeholder="16 digit NIK (opsional)">
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3"><i class="fas fa-map-marker-alt me-2"></i>Alamat Tinggal</h5>
                    
                    <div class="form-group">
                        <label for="address" class="form-label">Alamat Lengkap <span class="required">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" required
                                  placeholder="Jalan, Nomor Rumah, dan detail alamat lainnya"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="rt" class="form-label">RT</label>
                                <input type="text" class="form-control" id="rt" name="rt" placeholder="001">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="rw" class="form-label">RW</label>
                                <input type="text" class="form-control" id="rw" name="rw" placeholder="001">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="village" class="form-label">Desa/Kelurahan <span class="required">*</span></label>
                                <input type="text" class="form-control" id="village" name="village" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="district" class="form-label">Kecamatan <span class="required">*</span></label>
                                <input type="text" class="form-control" id="district" name="district" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="regency" class="form-label">Kabupaten/Kota <span class="required">*</span></label>
                                <input type="text" class="form-control" id="regency" name="regency" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="province" class="form-label">Provinsi <span class="required">*</span></label>
                                <input type="text" class="form-control" id="province" name="province" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="postal_code" class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="5"
                                       placeholder="27511">
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-custom btn-secondary-custom" onclick="prevStep(1)">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-custom btn-primary-custom" onclick="nextStep(3)">
                            Lanjut ke Data Keluarga <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Family Data -->
                <div class="form-step" id="step3">
                    <h3 class="mb-4"><i class="fas fa-users me-2"></i>Data Keluarga</h3>
                    
                    <h5 class="mb-3"><i class="fas fa-male me-2"></i>Data Ayah</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="father_name" class="form-label">Nama Ayah <span class="required">*</span></label>
                                <input type="text" class="form-control" id="father_name" name="father_name" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="father_education" class="form-label">Pendidikan Ayah</label>
                                <select class="form-select" id="father_education" name="father_education">
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="father_job" class="form-label">Pekerjaan Ayah</label>
                                <input type="text" class="form-control" id="father_job" name="father_job"
                                       placeholder="Pegawai, Wiraswasta, dll">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="father_income" class="form-label">Penghasilan Ayah (per bulan)</label>
                        <select class="form-select" id="father_income" name="father_income">
                            <option value="">Pilih Range Penghasilan</option>
                            <option value="< 1000000">< Rp 1.000.000</option>
                            <option value="1000000-2999999">Rp 1.000.000 - Rp 2.999.999</option>
                            <option value="3000000-4999999">Rp 3.000.000 - Rp 4.999.999</option>
                            <option value="5000000-9999999">Rp 5.000.000 - Rp 9.999.999</option>
                            <option value=">= 10000000">>= Rp 10.000.000</option>
                        </select>
                    </div>

                    <h5 class="mt-4 mb-3"><i class="fas fa-female me-2"></i>Data Ibu</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mother_name" class="form-label">Nama Ibu <span class="required">*</span></label>
                                <input type="text" class="form-control" id="mother_name" name="mother_name" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mother_education" class="form-label">Pendidikan Ibu</label>
                                <select class="form-select" id="mother_education" name="mother_education">
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mother_job" class="form-label">Pekerjaan Ibu</label>
                                <input type="text" class="form-control" id="mother_job" name="mother_job"
                                       placeholder="Ibu Rumah Tangga, Guru, dll">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mother_income" class="form-label">Penghasilan Ibu (per bulan)</label>
                        <select class="form-select" id="mother_income" name="mother_income">
                            <option value="">Pilih Range Penghasilan</option>
                            <option value="0">Tidak Bekerja</option>
                            <option value="< 1000000">< Rp 1.000.000</option>
                            <option value="1000000-2999999">Rp 1.000.000 - Rp 2.999.999</option>
                            <option value="3000000-4999999">Rp 3.000.000 - Rp 4.999.999</option>
                            <option value="5000000-9999999">Rp 5.000.000 - Rp 9.999.999</option>
                            <option value=">= 10000000">>= Rp 10.000.000</option>
                        </select>
                    </div>

                    <h5 class="mt-4 mb-3"><i class="fas fa-user-shield me-2"></i>Data Wali (Jika Ada)</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="guardian_name" class="form-label">Nama Wali</label>
                                <input type="text" class="form-control" id="guardian_name" name="guardian_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="guardian_relation" class="form-label">Hubungan dengan Santri</label>
                                <select class="form-select" id="guardian_relation" name="guardian_relation">
                                    <option value="">Pilih Hubungan</option>
                                    <option value="Kakek">Kakek</option>
                                    <option value="Nenek">Nenek</option>
                                    <option value="Paman">Paman</option>
                                    <option value="Bibi">Bibi</option>
                                    <option value="Kakak">Kakak</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="guardian_phone" class="form-label">No. HP Wali</label>
                                <input type="tel" class="form-control" id="guardian_phone" name="guardian_phone"
                                       placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-custom btn-secondary-custom" onclick="prevStep(2)">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-custom btn-primary-custom" onclick="nextStep(4)">
                            Lanjut ke Upload Berkas <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 4: File Upload -->
                <div class="form-step" id="step4">
                    <h3 class="mb-4"><i class="fas fa-upload me-2"></i>Upload Berkas Pendukung</h3>
                    
                    <h5 class="mb-3"><i class="fas fa-graduation-cap me-2"></i>Riwayat Pendidikan</h5>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="previous_school" class="form-label">Asal Sekolah Terakhir</label>
                                <input type="text" class="form-control" id="previous_school" name="previous_school"
                                       placeholder="Nama sekolah/TK sebelumnya">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ijazah_number" class="form-label">Nomor Ijazah/Sertifikat</label>
                                <input type="text" class="form-control" id="ijazah_number" name="ijazah_number"
                                       placeholder="Nomor ijazah (jika ada)">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ijazah_file" class="form-label">Upload Ijazah/Sertifikat <small class="text-muted">(Opsional)</small></label>
                        <div class="file-upload-area" onclick="document.getElementById('ijazah_file').click()">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-muted"></i>
                            <p class="mb-1">Klik untuk upload atau drag & drop file</p>
                            <small class="text-muted">Format: PDF, JPG, PNG (Max: 5MB)</small>
                            <input type="file" id="ijazah_file" name="ijazah_file" style="display: none;" 
                                   accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this, 'ijazahPreview')">
                        </div>
                        <div id="ijazahPreview"></div>
                    </div>

                    <div class="alert-info-custom">
                        <h6><i class="fas fa-info-circle me-2"></i>Informasi Penting:</h6>
                        <ul class="mb-0">
                            <li>Upload ijazah/sertifikat bersifat <strong>opsional</strong></li>
                            <li>File yang diupload akan diverifikasi oleh panitia SPMB</li>
                            <li>Pastikan file jelas dan dapat dibaca</li>
                            <li>Anda dapat mengupload file nanti setelah pendaftaran selesai</li>
                        </ul>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-custom btn-secondary-custom" onclick="prevStep(3)">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-custom btn-primary-custom" onclick="nextStep(5)">
                            Lanjut ke Konfirmasi <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 5: Confirmation -->
                <div class="form-step" id="step5">
                    <h3 class="mb-4"><i class="fas fa-check me-2"></i>Konfirmasi Data Pendaftaran</h3>
                    
                    <div class="summary-section">
                        <h5 class="mb-3"><i class="fas fa-user me-2"></i>Ringkasan Data</h5>
                        <div id="summaryContent">
                            <!-- Summary will be populated by JavaScript -->
                        </div>
                    </div>

                    <div class="alert-info-custom">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Perhatian:</h6>
                        <ul class="mb-0">
                            <li>Pastikan semua data yang diisi sudah benar</li>
                            <li>Setelah submit, Anda akan mendapat akun login otomatis</li>
                            <li>Username akan digenerate otomatis berdasarkan kode pendaftaran</li>
                            <li>Password akan dikirim via email dan WhatsApp</li>
                            <li>Data akan diverifikasi oleh panitia SPMB maksimal 2x24 jam</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agreement" name="agreement" required>
                            <label class="form-check-label" for="agreement">
                                Saya menyatakan bahwa data yang saya isi adalah benar dan dapat dipertanggungjawabkan. 
                                Saya bersedia mengikuti semua ketentuan yang berlaku di Cendekia Muslim Islamic School.
                            </label>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-custom btn-secondary-custom" onclick="prevStep(4)">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="submit" class="btn btn-custom btn-orange-custom" id="submitBtn">
                            <i class="fas fa-paper-plane me-1"></i> Submit Pendaftaran
                        </button>
                    </div>
                </div>

                <!-- Hidden Fields -->
                <input type="hidden" name="referral_code" id="referralCode" value="">
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>
    
    <script>
        let currentStep = 1;
        let totalSteps = 5;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Check for referral code in URL
            const urlParams = new URLSearchParams(window.location.search);
            const referralCode = urlParams.get('ref');
            
            if (referralCode) {
                document.getElementById('referralCode').value = referralCode;
                document.getElementById('displayedReferralCode').textContent = referralCode;
                document.getElementById('referralCodeDisplay').style.display = 'block';
            }

            // Initialize Select2 for better dropdowns
            $('.form-select').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });

        function selectEducationLevel(level) {
            // Remove previous selections
            document.querySelectorAll('.education-level-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selection to clicked card
            document.querySelector(`#level_${level}`).closest('.education-level-card').classList.add('selected');
            document.querySelector(`#level_${level}`).checked = true;
            
            // Show/hide PKBM packages
            const pkbmPackages = document.getElementById('pkbmPackages');
            if (level === 'pkbm') {
                pkbmPackages.style.display = 'block';
            } else {
                pkbmPackages.style.display = 'none';
                // Clear PKBM package selection
                document.querySelectorAll('input[name="pkbm_package"]').forEach(input => {
                    input.checked = false;
                });
            }
        }

        function selectPackage(packageType) {
            // Remove previous selections
            document.querySelectorAll('.package-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selection to clicked option
            document.querySelector(`#package_${packageType.toLowerCase()}`).closest('.package-option').classList.add('selected');
            document.querySelector(`#package_${packageType.toLowerCase()}`).checked = true;
        }

        function nextStep(step) {
            if (validateCurrentStep()) {
                // Hide current step
                document.getElementById(`step${currentStep}`).classList.remove('active');
                document.getElementById(`step${currentStep}Indicator`).classList.remove('active');
                document.getElementById(`step${currentStep}Indicator`).classList.add('completed');
                
                // Show next step
                currentStep = step;
                document.getElementById(`step${currentStep}`).classList.add('active');
                document.getElementById(`step${currentStep}Indicator`).classList.add('active');
                
                // Generate summary if on last step
                if (currentStep === 5) {
                    generateSummary();
                }
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function prevStep(step) {
            // Hide current step
            document.getElementById(`step${currentStep}`).classList.remove('active');
            document.getElementById(`step${currentStep}Indicator`).classList.remove('active');
            
            // Show previous step
            currentStep = step;
            document.getElementById(`step${currentStep}`).classList.add('active');
            document.getElementById(`step${currentStep}Indicator`).classList.add('active');
            document.getElementById(`step${currentStep}Indicator`).classList.remove('completed');
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateCurrentStep() {
            let isValid = true;
            let errorMessages = [];

            switch(currentStep) {
                case 1:
                    // Validate education level selection
                    const educationLevel = document.querySelector('input[name="education_level"]:checked');
                    if (!educationLevel) {
                        errorMessages.push('Pilih jenjang pendidikan terlebih dahulu');
                        isValid = false;
                    } else if (educationLevel.value === 'pkbm') {
                        const pkbmPackage = document.querySelector('input[name="pkbm_package"]:checked');
                        if (!pkbmPackage) {
                            errorMessages.push('Pilih paket PKBM terlebih dahulu');
                            isValid = false;
                        }
                    }
                    break;
                    
                case 2:
                    // Validate personal data
                    const requiredFields = ['full_name', 'gender', 'birth_place', 'birth_date', 'religion', 'citizenship', 'address', 'village', 'district', 'regency', 'province'];
                    requiredFields.forEach(field => {
                        const element = document.getElementById(field);
                        if (!element.value.trim()) {
                            element.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            element.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!isValid) {
                        errorMessages.push('Lengkapi semua field yang wajib diisi');
                    }
                    break;
                    
                case 3:
                    // Validate family data
                    const familyRequired = ['father_name', 'mother_name'];
                    familyRequired.forEach(field => {
                        const element = document.getElementById(field);
                        if (!element.value.trim()) {
                            element.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            element.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!isValid) {
                        errorMessages.push('Nama ayah dan ibu wajib diisi');
                    }
                    break;
                    
                case 4:
                    // File upload is optional, so always valid
                    break;
                    
                case 5:
                    // Validate agreement
                    const agreement = document.getElementById('agreement');
                    if (!agreement.checked) {
                        errorMessages.push('Anda harus menyetujui pernyataan untuk melanjutkan');
                        isValid = false;
                    }
                    break;
            }

            if (!isValid && errorMessages.length > 0) {
                alert(errorMessages.join('\n'));
            }

            return isValid;
        }

        function generateSummary() {
            const formData = new FormData(document.getElementById('registrationForm'));
            const educationLevel = formData.get('education_level');
            const pkbmPackage = formData.get('pkbm_package');
            
            let levelText = '';
            switch(educationLevel) {
                case 'tk': levelText = 'TK Akhlak Cendekia Muslim'; break;
                case 'sd': levelText = 'SD Akhlak Cendekia Muslim'; break;
                case 'smp': levelText = 'SMP Akhlak Cendekia Muslim'; break;
                case 'pkbm': levelText = `PKBM Cendekia Muslim - Paket ${pkbmPackage}`; break;
                case 'lpq': levelText = 'LPQ Cendekia Muslim'; break;
            }

            const summary = `
                <div class="summary-item">
                    <span><strong>Jenjang Pendidikan:</strong></span>
                    <span>${levelText}</span>
                </div>
                <div class="summary-item">
                    <span><strong>Nama Lengkap:</strong></span>
                    <span>${formData.get('full_name')}</span>
                </div>
                <div class="summary-item">
                    <span><strong>Jenis Kelamin:</strong></span>
                    <span>${formData.get('gender') === 'L' ? 'Laki-laki' : 'Perempuan'}</span>
                </div>
                <div class="summary-item">
                    <span><strong>Tempat, Tanggal Lahir:</strong></span>
                    <span>${formData.get('birth_place')}, ${formData.get('birth_date')}</span>
                </div>
                <div class="summary-item">
                    <span><strong>Alamat:</strong></span>
                    <span>${formData.get('address')}, ${formData.get('village')}, ${formData.get('district')}</span>
                </div>
                <div class="summary-item">
                    <span><strong>Nama Ayah:</strong></span>
                    <span>${formData.get('father_name')}</span>
                </div>
                <div class="summary-item">
                    <span><strong>Nama Ibu:</strong></span>
                    <span>${formData.get('mother_name')}</span>
                </div>
                ${formData.get('referral_code') ? `
                <div class="summary-item">
                    <span><strong>Kode Referral:</strong></span>
                    <span>${formData.get('referral_code')}</span>
                </div>
                ` : ''}
            `;

            document.getElementById('summaryContent').innerHTML = summary;
        }

        function handleFileSelect(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5242880) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    input.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan PDF, JPG, atau PNG.');
                    input.value = '';
                    return;
                }
                
                // Show file preview
                preview.innerHTML = `
                    <div class="uploaded-file">
                        <div>
                            <i class="fas fa-file-${file.type.includes('pdf') ? 'pdf' : 'image'} me-2"></i>
                            <strong>${file.name}</strong><br>
                            <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('${input.id}', '${previewId}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }
        }

        function removeFile(inputId, previewId) {
            document.getElementById(inputId).value = '';
            document.getElementById(previewId).innerHTML = '';
        }

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateCurrentStep()) {
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
            submitBtn.disabled = true;
            
            // Simulate form submission (replace with actual AJAX call)
            setTimeout(() => {
                // Get form data
                const formData = new FormData(this);
                
                // Show success message (replace with actual submission)
                alert('Pendaftaran berhasil dikirim!\n\nAnda akan diarahkan ke halaman login untuk mengakses akun Anda.');
                
                // Redirect to login or success page
                window.location.href = 'login.php';
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });

        // Drag and drop functionality
        document.querySelectorAll('.file-upload-area').forEach(area => {
            area.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });
            
            area.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });
            
            area.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const input = this.querySelector('input[type="file"]');
                    input.files = files;
                    input.dispatchEvent(new Event('change'));
                }
            });
        });
    </script>
</body>
</html>