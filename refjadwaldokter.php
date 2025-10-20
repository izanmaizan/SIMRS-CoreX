<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
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
    <title>Referensi Jadwal Dokter - SIMRS CoreX | core Exchange</title>
    
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

            <!-- Content -->
            <div class="col-lg-10 col-md-12 content">
                <div id="contentReferensi">
                    <h4 class="mb-4 text-primary">Referensi Jadwal Dokter</h4>
                    <div id="formReferensi">
                        <div class="form-row d-none">
                            <div class="form-group col-md-4">
                                <label>Service</label>
                                <select class="form-control" id="selectAPI" readonly>
                                    <option value="antrean-rs" selected>Antrean RS</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Cons ID</label>
                                <input type="text" class="form-control" id="consid" placeholder="ConsID" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Secret Key</label>
                                <input type="text" class="form-control" id="secret" placeholder="Secret" readonly>
                            </div>
                        </div>
                        <div class="form-row d-none">
                            <div class="form-group col-md-3">
                                <label>User Key</label>
                                <input type="text" class="form-control" id="user_key" placeholder="User Key" readonly>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Timestamp</label>
                                <input type="text" class="form-control" id="x-timestamp" placeholder="X-Timestamp" readonly>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Signature</label>
                                <input type="text" class="form-control" id="x-signature" placeholder="X-Signature" readonly>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Dekripsi</label>
                                <select class="form-control" id="selectIsEncrypt" readonly>
                                    <option value="1" selected>Dekripsi</option>
                                    <option value="0">Non Dekripsi</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row d-none">
                            <div class="form-group col-md-4">
                                <label>Method</label>
                                <select class="form-control" id="method" readonly>
                                    <option value="GET" selected>GET</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Base URL</label>
                                <input type="text" class="form-control" id="url" value="https://apijkn.bpjs-kesehatan.go.id/antreanrs/" readonly>
                            </div>
                        </div>
<!-- 
                        <input type="hidden" id="username" value="">
                        <input type="hidden" id="password" value=""> -->

                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label>Pilih Poli</label>
                                <select class="form-control" id="kodepoli" required>
                                    <option value="">Pilih Poli</option>
                                    <option value="ANA">ANAK</option>
                                    <option value="BED">BEDAH</option>
                                    <option value="018">BEDAH DIGESTIF</option>
                                    <option value="BDM">BEDAH MULUT</option>
                                    <option value="017">BEDAH ONKOLOGI</option>
                                    <option value="005">GASTROENTEROLOGY DAN HEPATOLOGY</option>
                                    <option value="006">GERIATRI</option>
                                    <option value="GIG">GIGI</option>
                                    <option value="JAN">JANTUNG DAN PEMBULUH DARAH</option>
                                    <option value="JIW">KEDOKTERAN JIWA</option>
                                    <option value="KLT">KULIT KELAMIN</option>
                                    <option value="MAT">MATA</option>
                                    <option value="OBG">OBSTETRI DAN GINEKOLOGI</option>
                                    <option value="ORT">ORTHOPEDI DAN TRAUMATOLOGY</option>
                                    <option value="PAR">PARU</option>
                                    <option value="INT">PENYAKIT DALAM (Interne)</option>
                                    <option value="IRM">REHAB MEDIK</option>
                                    <option value="SAR">SARAF</option>
                                    <option value="THT">TELINGA HIDUNG DAN TENGGOROKAN</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" required>
                            </div>
                            <div class="form-group col-md-12 d-none">
                                <label>Endpoint</label>
                                <input type="text" class="form-control" id="endpoint" placeholder="Endpoint">
                            </div>
                        </div>

                        <input type="hidden" id="withParam" value="0">
                        <textarea id="params" style="display:none;"></textarea>
                    </div>
                    <center>
                        <button type="button" id="send" class="btn btn-primary btn-block col-md-5">Tampilkan</button>
                    </center>

                    <div id="hasilReferensi" class="mt-4"></div>
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

        $(document).ready(function() {
            function updateEndpoint() {
                var kodepoli = $("#kodepoli").val().trim();
                var tanggal = $("#tanggal").val().trim();
                if (kodepoli && tanggal) {
                    $("#endpoint").val("jadwaldokter/kodepoli/" + kodepoli + "/tanggal/" + tanggal);
                } else {
                    $("#endpoint").val("");
                }
            }

            $("#kodepoli, #tanggal").on("change", updateEndpoint);

            $("#send").off('click').on('click', function() {
                var kodepoli = $("#kodepoli").val().trim();
                var tanggal = $("#tanggal").val().trim();
                
                if (!kodepoli || !tanggal) {
                    showToast('Validasi Error', 'Kode Poli dan Tanggal wajib diisi!', 'error');
                    return;
                }

                $.ajax({
                    url: "getResponse.php",
                    method: "GET",
                    data: {
                        jenisAPI: $("#selectAPI").val(),
                        consid: $("#consid").val(),
                        secret: $("#secret").val(),
                        user_key: $("#user_key").val(),
                        is_encrypt: $("#selectIsEncrypt").val(),
                        username: "",
                        password: "",
                        method: $("#method").val(),
                        url: $("#url").val() + $("#endpoint").val(),
                        withParam: 0,
                        params: "",
                    },
                    success: function(e) {
                        try {
                            var parsed = JSON.parse(e);

                            // Check untuk code 201 (No Content)
                            if (parsed.metadata && parsed.metadata.code === 201) {
                                showToast('Tidak Ada Jadwal', 'Dokter tidak memiliki jadwal di tanggal tersebut', 'warning');
                                
                                var noContentHtml = `
                                    <div class="no-content-card">
                                        <div class="no-content-icon">📅</div>
                                        <h5 class="no-content-title">
                                            <i class="fas fa-exclamation-triangle"></i> Tidak Ada Jadwal
                                        </h5>
                                        <p class="no-content-message">
                                            Dokter pada poli yang dipilih tidak memiliki jadwal di tanggal yang Anda tentukan. 
                                            Silakan pilih tanggal lain atau poli lain.
                                        </p>
                                    </div>
                                `;
                                
                                $("#hasilReferensi").html(noContentHtml);
                                
                                // Smooth scroll ke hasil
                                $('html, body').animate({
                                    scrollTop: $("#hasilReferensi").offset().top - 100
                                }, 500);
                                
                                return;
                            }

                            if (parsed.metadata && parsed.response && Array.isArray(parsed.response)) {
                                showToast('Berhasil', 'Data jadwal dokter berhasil dimuat!', 'success');
                                
                                var html = '';

                                if (parsed.response.length > 0) {
                                    html += '<div class="result-grid">';

                                    parsed.response.forEach(function(item) {
                                        html += `
                                        <div class="modern-card">
                                            <div class="card-header">
                                                <i class="fas fa-user-md"></i> ${item.namadokter || '-'}
                                            </div>
                                            <div class="card-body">
                                                <div class="poli-name">
                                                    <i class="fas fa-hospital"></i> ${item.namapoli || '-'}
                                                </div>
                                                
                                                <div class="info-row">
                                                    <span class="info-label"><i class="far fa-calendar"></i> Hari</span>
                                                    <span class="badge-custom badge-hari">${item.namahari || '-'}</span>
                                                </div>
                                                
                                                <div class="info-row">
                                                    <span class="info-label"><i class="far fa-clock"></i> Jam Praktik</span>
                                                    <span class="badge-custom badge-jadwal">${item.jadwal || '-'}</span>
                                                </div>
                                                
                                                <div class="info-row">
                                                    <span class="info-label"><i class="fas fa-users"></i> Kapasitas</span>
                                                    <span class="badge-custom badge-kapasitas">${item.kapasitaspasien || '-'} Pasien</span>
                                                </div>
                                                
                                                <div class="info-row">
                                                    <span class="info-label"><i class="fas fa-stethoscope"></i> Subspesialis</span>
                                                    <span class="info-value">${item.namasubspesialis || '-'}</span>
                                                </div>
                                                
                                                <div class="info-row">
                                                    <span class="info-label"><i class="fas fa-id-card"></i> Kode Subspesialis</span>
                                                    <span class="info-value">${item.kodesubspesialis || '-'}</span>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <p class="footer-text">
                                                    <i class="fas fa-info-circle"></i> Kode Dokter: <strong>${item.kodedokter || '-'}</strong> | 
                                                    Kode Poli: <strong>${item.kodepoli || '-'}</strong>
                                                </p>
                                            </div>
                                        </div>
                                        `;
                                    });

                                    html += '</div>';
                                } else {
                                    html += `
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📅</div>
                                        <div class="empty-state-text">Tidak ada jadwal dokter yang tersedia</div>
                                    </div>
                                    `;
                                }

                                $("#hasilReferensi").html(html);
                                
                                // Smooth scroll ke hasil
                                $('html, body').animate({
                                    scrollTop: $("#hasilReferensi").offset().top - 100
                                }, 500);
                            }
                        } catch (error) {
                            console.error("Error parsing response:", error);
                            showToast('Error', 'Terjadi kesalahan saat memproses data', 'error');
                        }

                        $("#send").blur();
                    },
                    beforeSend: function() {
                        $("#send").html('<i class="fas fa-spinner fa-spin"></i> Memuat..');
                        $("#send").prop('disabled', true);
                        $("#hasilReferensi").html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Memuat data jadwal dokter...</p></div>');
                    },
                    complete: function() {
                        $("#send").html('Tampilkan');
                        $("#send").prop('disabled', false);
                    },
                    error: function() {
                        showToast('Error', 'Terjadi kesalahan saat menghubungi server', 'error');
                        $("#hasilReferensi").html('');
                    }
                });
            });
        });

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