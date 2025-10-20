<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Tambahan: Pengecekan role untuk admin (tidak boleh akses setting)
if ($_SESSION['role'] !== 'superadmin') {
    header('Location: index.php?message=unauthorized');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Credentials - SIMRS CoreX | core Exchange</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link rel="apple-touch-icon" href="./logo.webp">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/json-viewer.css" />
    <link rel="stylesheet" href="style.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"></script>
</head>

<body>

    <div id="overlay"></div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <button class="btn btn-outline-primary d-lg-none" id="toggleSidebar">☰</button>
    <div class="d-flex align-items-center ml-2">
        <img src="./logo.webp" alt="RSUD Logo" style="height: 40px; width: auto; margin-right: 12px; object-fit: contain;">
        <div>
            <div class="navbar-brand-main" style="margin: 0; font-weight: 700; font-size: 1.1rem; color: #0066cc;">SIMRS CoreX</div>
            <div class="navbar-brand-sub" style="margin: 0; font-size: 0.7rem; color: #999; font-weight: 500;">core Exchange</div>
        </div>
    </div>
    <div class="ml-auto d-flex align-items-center">
        <div class="d-none d-md-block navbar-powered-by">
            <small style="color: #999; font-size: 0.75rem;">Powered by SIMRS 2025</small>
        </div>
        <a href="?logout=true" class="btn btn-outline-danger btn-sm ml-3">Logout</a>
    </div>
</nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 sidebar p-0" id="sidebar">
                <div class="logo">🏥 RSUD M. NATSIR</div>
                <a href="index.php">Dashboard</a>
                <hr>
                <a href="refjadwaldokter.php">Referensi Jadwal Dokter</a>
                <a href="updatejadwaldokter.php">Update Jadwal Dokter</a>
                <hr>
                <a href="setting.php" class="active">Setting Credentials</a>
                <a href="?logout=true" onclick="return confirm('Apakah Anda yakin ingin logout?')">Logout</a>
            </div>

            <!-- Content -->
            <div class="col-lg-10 col-md-12 content">
                <div id="contentSetting">
                    <h4 class="mb-4 text-primary">Setting Credentials</h4>
                    
                    <div class="modern-card">
                        <div class="card-header">
                            <h5><i class="fas fa-key"></i> Kredensial API</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Service</label>
                                    <select class="form-control" id="selectAPI" readonly>
                                        <option value="antrean-rs" selected>Antrean RS</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Cons ID</label>
                                    <input type="password" class="form-control" id="consid" placeholder="Masukkan Cons ID">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Secret Key</label>
                                    <input type="password" class="form-control" id="secret" placeholder="Masukkan Secret Key">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>User Key</label>
                                    <input type="password" class="form-control" id="user_key" placeholder="Masukkan User Key">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Timestamp</label>
                                    <input type="text" class="form-control" id="x-timestamp" placeholder="Auto Generated" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Signature</label>
                                    <input type="text" class="form-control" id="x-signature" placeholder="Auto Generated" readonly>
                                </div>
                            </div>

                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-success mr-2" id="btnSaveCredential">
                                    <i class="fas fa-save"></i> Simpan Data Lokal
                                </button>
                                <button type="button" class="btn btn-danger" id="btnDeleteCredential">
                                    <i class="fas fa-trash"></i> Hapus Data Lokal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/json-viewer.js"></script>
    <script src="js/main.js"></script>

    <script>
        // Toast Notification Function
        function showToast(title, message, type = 'info') {
            const toastContainer = $('#toastContainer');
            const iconMap = {
                success: 'fa-check-circle',
                error: 'fa-times-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            const toast = $(`
                <div class="toast-notification ${type}">
                    <div class="toast-icon">
                        <i class="fas ${iconMap[type]}"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">${title}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                    <button class="toast-close">×</button>
                </div>
            `);

            toastContainer.append(toast);

            toast.find('.toast-close').on('click', function() {
                toast.addClass('hiding');
                setTimeout(() => toast.remove(), 300);
            });

            setTimeout(() => {
                toast.addClass('hiding');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        $(document).ready(function() {
            // Load saved credentials
            if (window.localStorage.getItem("consid")) {
                $("#consid").val(window.localStorage.getItem("consid"));
            }

            if (window.localStorage.getItem("secret")) {
                $("#secret").val(window.localStorage.getItem("secret"));
            }

            if (window.localStorage.getItem("user_key")) {
                $("#user_key").val(window.localStorage.getItem("user_key"));
            }

            // Generate signature on load
            generateTimestampAndSignature();

            // Auto-generate on credential change
            $("#consid, #secret").on("input", generateTimestampAndSignature);
        });

        function generateTimestampAndSignature() {
            var timestamp = Date.now() / 1000 | 0;
            $("#x-timestamp").val(timestamp);

            var data = $("#consid").val() + "&" + timestamp;
            var secret = $("#secret").val();
            
            if (secret) {
                var signature = CryptoJS.HmacSHA256(data, secret).toString(CryptoJS.enc.Base64);
                $("#x-signature").val(signature);
            }
        }

        // Toggle show/hide password
        $("#btn_show_hide").click(function() {
            var type = $("#consid").attr("type");
            if (type === "password") {
                $("#consid").attr("type", "text");
                $("#secret").attr("type", "text");
                $("#user_key").attr("type", "text");
                $("#toggleText").text("Sembunyikan");
                $(this).html('<i class="fas fa-eye-slash"></i> <span id="toggleText">Sembunyikan</span>');
            } else {
                $("#consid").attr("type", "password");
                $("#secret").attr("type", "password");
                $("#user_key").attr("type", "password");
                $("#toggleText").text("Tampilkan");
                $(this).html('<i class="fas fa-eye"></i> <span id="toggleText">Tampilkan</span>');
            }
        });

        // Save credentials
        $("#btnSaveCredential").click(function() {
            if (!$("#consid").val() || !$("#secret").val() || !$("#user_key").val()) {
                showToast('Validasi Error', 'Semua field wajib diisi sebelum menyimpan!', 'error');
                return;
            }

            window.localStorage.setItem("consid", $("#consid").val());
            window.localStorage.setItem("secret", $("#secret").val());
            window.localStorage.setItem("user_key", $("#user_key").val());
            window.localStorage.setItem("selectAPI", $("#selectAPI").val());

            showToast('Berhasil', 'Data kredensial berhasil disimpan di browser!', 'success');
        });

        // Delete credentials
        $("#btnDeleteCredential").click(function() {
            if (confirm("Apakah Anda yakin ingin menghapus semua data kredensial dari browser?")) {
                window.localStorage.removeItem("consid");
                window.localStorage.removeItem("secret");
                window.localStorage.removeItem("user_key");
                window.localStorage.removeItem("selectAPI");
                window.localStorage.removeItem("show_hide");

                $("#consid").val("");
                $("#secret").val("");
                $("#user_key").val("");
                $("#x-timestamp").val("");
                $("#x-signature").val("");

                showToast('Berhasil', 'Data kredensial berhasil dihapus dari browser!', 'success');
            }
        });

        // Sidebar toggle
        $('#toggleSidebar').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#overlay').toggleClass('active');
        });
        
        $('#overlay').on('click', function() {
            $('#sidebar').removeClass('active');
            $('#overlay').removeClass('active');
        });
    </script>

</body>

</html>