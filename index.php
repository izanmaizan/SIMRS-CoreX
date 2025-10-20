<?php
session_start();

// Auto logout kalau belum login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Handle logout langsung di file ini
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
    <title>Dashboard - SIMRS CoreX | core Exchange</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link rel="apple-touch-icon" href="./logo.webp">

    <!-- Bootstrap 4.6.1 (disamakan dengan refjadwaldokter.php) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Style CSS Eksternal (disamakan) -->
    <link rel="stylesheet" href="style.css">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div id="overlay"></div>

    <!-- Toast Container (disamakan untuk notifikasi) -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Navbar (disamakan) -->
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
    <a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''); ?>>Dashboard</a>
    <hr>
    <a href="refjadwaldokter.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'refjadwaldokter.php' ? 'class="active"' : ''); ?>>Referensi Jadwal Dokter</a>
    <a href="updatejadwaldokter.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'updatejadwaldokter.php' ? 'class="active"' : ''); ?>>Update Jadwal Dokter</a>
    <hr>
    <?php if ($_SESSION['role'] === 'superadmin'): ?>
    <a href="setting.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'setting.php' ? 'class="active"' : ''); ?>>Setting Credentials</a>
    <?php endif; ?>
    <a href="?logout=true" onclick="return confirm('Apakah Anda yakin ingin logout?')">Logout</a>
</div>

            <!-- Content (disamakan struktur) -->
            <div class="col-lg-10 col-md-12 content">
                <div id="contentDashboard">
                    <h4 class="mb-4 text-primary">Dashboard</h4>

                    <!-- Welcome Section (disamakan) -->
                    <div class="modern-card">
                        <div class="card-header">
                            <i class="fas fa-home"></i> Selamat Datang
                        </div>
                        <div class="card-body">
                            <p class="info-value">Halo! Selamat datang di Dashboard SIMRS core Exchange RSUD M. Natsir.</p>
                        </div>
                    </div>

                    <hr>

                    <div id="hasilDashboard" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script (disamakan dengan tambahan toast dan toggle sidebar) -->
    <script>
        // Toast Notification Function (disalin dari refjadwaldokter.php)
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

            // Close button
            toast.find('.toast-close').on('click', function() {
                toast.addClass('hiding');
                setTimeout(() => toast.remove(), 300);
            });

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.addClass('hiding');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    </script>

</body>

</html>