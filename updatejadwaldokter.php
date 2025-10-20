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
    <title>Update Jadwal Dokter - SIMRS CoreX | core Exchange</title>
    
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
                <div id="contentUpdate">
                    <h4 class="mb-4 text-primary">Update Jadwal Dokter</h4>
                    
                    <!-- Step 1: Input Kode Poli, Subspesialis, Dokter -->
                    <div id="step1" class="modern-card">
                        <div class="card-header">
                            <h5><i class="fas fa-info-circle"></i> Informasi Dokter</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-row d-none">
                                <div class="form-group col-md-4">
                                    <label>Service</label>
                                    <select class="form-control" id="selectAPI" readonly>
                                        <option value="antrean-rs" selected>Antrean RS</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Cons ID</label>
                                    <input type="text" class="form-control" id="consid" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Secret Key</label>
                                    <input type="text" class="form-control" id="secret" readonly>
                                </div>
                            </div>
                            <div class="form-row d-none">
                                <div class="form-group col-md-3">
                                    <label>User Key</label>
                                    <input type="text" class="form-control" id="user_key">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Timestamp</label>
                                    <input type="text" class="form-control" id="x-timestamp" readonly>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Signature</label>
                                    <input type="text" class="form-control" id="x-signature" readonly>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Dekripsi</label>
                                    <select class="form-control" id="selectIsEncrypt">
                                        <option value="1" selected>Dekripsi</option>
                                        <option value="0">Non Dekripsi</option>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" id="username" value="">
                            <input type="hidden" id="password" value="">
                            <input type="hidden" id="url" value="https://apijkn.bpjs-kesehatan.go.id/antreanrs/">
                            <input type="hidden" id="endpoint" value="/jadwaldokter/updatejadwaldokter">

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Kode Poli <span class="text-danger">*</span></label>
                                    <select class="form-control" id="update_kodepoli" required>
                                        <option value="">Pilih Kode Poli</option>
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
                                    <label>Kode Subspesialis <span class="text-danger">*</span></label>
                                    <select class="form-control" id="update_kodesubspesialis" required>
                                        <option value="">Pilih Kode Subspesialis</option>
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
                                    <label>Kode Dokter <span class="text-danger">*</span></label>
                                    <select class="form-control" id="update_kodedokter" required>
                                        <option value="">Pilih Dokter</option>
                                        <option value="305115">ABDUL RAZIQ JAMIL</option>
                                        <option value="19372">ADE ARIADI</option>
                                        <option value="19375">ADEK</option>
                                        <option value="19373">ADJI MUSTIADJI</option>
                                        <option value="620708">AHMAD ZIA FADELZI</option>
                                        <option value="549702">ALDILO TALIMA</option>
                                        <option value="291603">ALI MUDIARNIS</option>
                                        <option value="493830">ANDHIKA RULYANTI SIDO</option>
                                        <option value="498475">ANDINI PRAMESTI</option>
                                        <option value="19317">ANGGRA PRAMANA</option>
                                        <option value="443663">APRI YOLA</option>
                                        <option value="389339">ARIEF HIDAYAT</option>
                                        <option value="19380">ASRIZAL ASRIL</option>
                                        <option value="462988">ATHIKA RAHMAWATI</option>
                                        <option value="412851">AULIA RAHMI</option>
                                        <option value="19336">BOY HUTAPERI</option>
                                        <option value="399872">BUDI PATRIA MEILUS</option>
                                        <option value="434647">BULFENDRI DONI</option>
                                        <option value="510770">CERY TARISE HAJALI</option>
                                        <option value="401414">CHINTIA CITRA</option>
                                        <option value="19383">DEDDY KURNIAWAN JAHJA</option>
                                        <option value="375221">DEKY HIDAYATUL AKBAR</option>
                                        <option value="510778">DELA HANGRI JALMAS</option>
                                        <option value="116208">EKA PUTRI</option>
                                        <option value="412998">ELSIS MARETA EDRIYENTI</option>
                                        <option value="19333">ELVI FITRANETI</option>
                                        <option value="285283">EMBUN DINI</option>
                                        <option value="603732">FAJRIA KHALIDA</option>
                                        <option value="457694">FATMAH SINDI</option>
                                        <option value="78906">FAUZANA NAZIFAH</option>
                                        <option value="33945">FERRY INDRATNO</option>
                                        <option value="304780">FETRIA FAISAL</option>
                                        <option value="493833">FIONA SEPTI MULYA</option>
                                        <option value="33523">FITRI SARI SUSANTY</option>
                                        <option value="469413">HANA PERTIWI</option>
                                        <option value="546375">HANAFI IDRIS</option>
                                        <option value="19351">HELWI NOFIRA</option>
                                        <option value="19377">HONDRIZAL</option>
                                        <option value="113750">ILZIA</option>
                                        <option value="22729">IRSAL MUNANDAR</option>
                                        <option value="366090">JENNY TRI YUSPITA SARI</option>
                                        <option value="305110">JON HADI</option>
                                        <option value="442211">KHAIRUNNISA SALSABILA</option>
                                        <option value="305087">LIDIA DEWI</option>
                                        <option value="566659">MIFTAHUL JANNAH GAFAR</option>
                                        <option value="19385">MUHAMMAD PRAMANA KHALILUL HARMIN</option>
                                        <option value="305186">MUNAWIRAH</option>
                                        <option value="474169">NADYA DWI KARSA</option>
                                        <option value="19384">NOVERIAL</option>
                                        <option value="357021">NOVIA MARSELINA</option>
                                        <option value="405466">NUR</option>
                                        <option value="571562">NURUL RAMADANI</option>
                                        <option value="421707">PEPPY FOURINA</option>
                                        <option value="437695">PRARA MIFTAH RAHMI</option>
                                        <option value="33911">PUTRI MAGITA THURISIA</option>
                                        <option value="580069">PUTRI NABILLAH MULYA</option>
                                        <option value="509689">RAHMAT HARIS PRIBADI</option>
                                        <option value="412450">RAMADANUS</option>
                                        <option value="479956">RARA BADRIYA AGUSTIN</option>
                                        <option value="19382">RENO SARI CANIAGO</option>
                                        <option value="394367">RIA OKTAVIA</option>
                                        <option value="401415">RIDHA MUSTIKA ZAIF</option>
                                        <option value="319027">RIKA YANDRIANI</option>
                                        <option value="421706">RIRIN TRIYANI</option>
                                        <option value="291549">RISA ARTELIA</option>
                                        <option value="35014">RIZQA SARI</option>
                                        <option value="260657">SANTY SABERKO</option>
                                        <option value="19386">SARI NIKMAWATI</option>
                                        <option value="19323">SEPTA RINALDY</option>
                                        <option value="392457">SILVIA ARGA</option>
                                        <option value="19376">SOUFNI MORAWATI</option>
                                        <option value="442210">SUCI RAMADHANI PERMANA</option>
                                        <option value="19389">SULISTIANA DEWI</option>
                                        <option value="442214">SYAUQI FAIDHUN NI</option>
                                        <option value="442207">TIYA TASLISIA</option>
                                        <option value="251399">VANDRA BINA RIYANDA</option>
                                        <option value="541328">WAHYU ISKANDAR</option>
                                        <option value="302286">WIDYA ISRA</option>
                                        <option value="309477">YOSTILA DEROSA</option>
                                        <option value="401417">YULIA RAHMI</option>
                                        <option value="19381">YULSON</option>
                                        <option value="310095">ZULISMALIATUL FAJRIAH</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" id="btnTampilkanJadwal" class="btn btn-primary btn-lg">
                                    <i class="fas fa-calendar-alt"></i> Tampilkan Jadwal
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Edit Jadwal (Hidden by default) -->
                    <div id="step2" class="modern-card mt-4" style="display:none;">
                        <div class="card-header">
                            <h5><i class="fas fa-clock"></i> Edit Jadwal Praktik</h5>
                        </div>
                        <div class="card-body">
                            <div id="loadingJadwal" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                                <p class="mt-3">Memuat jadwal dokter...</p>
                            </div>
                            <div id="jadwalContainer" style="display:none;">
                                <!-- Jadwal akan dimuat di sini -->
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="withParam" value="1">
                    <textarea id="params" style="display:none;"></textarea>

                    <div id="jsonResponse" class="mt-4 d-none"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Konflik Jadwal (Code 201) -->
    <div class="modal fade" id="hfisAlertModal" tabindex="-1" role="dialog" aria-labelledby="hfisAlertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="hfisAlertModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> Perhatian - Konflik Jadwal
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="poli-info">
                        <strong><i class="fas fa-hospital"></i> Poli:</strong> <span id="poliDetail"></span><br>
                        <strong><i class="far fa-calendar"></i> Hari:</strong> <span id="hariDetail"></span><br>
                        <strong><i class="far fa-clock"></i> Jam Praktik:</strong> <span id="jamDetail"></span>
                    </div>
                    <p><strong>Jadwal hari <span id="hariDetail2"></span> sudah ada kode booking.</strong> Silakan lakukan pembatalan kode booking tersebut sebelum melakukan perubahan jadwal.</p>
                    <div id="bookingGroupContainer">
                        <!-- Group booking akan dimuat di sini -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Update -->
    <div class="modal fade" id="confirmUpdateModal" tabindex="-1" role="dialog" aria-labelledby="confirmUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUpdateModalLabel">
                        <i class="fas fa-question-circle"></i> Konfirmasi Update
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                    <h5>Apakah Anda yakin?</h5>
                    <p class="text-muted" id="confirmUpdateMessage">Apakah Anda yakin ingin melakukan perubahan untuk jadwal dokter ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmUpdateBtn">Ya, Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash fa-3x text-danger mb-3"></i>
                    <h5>Apakah Anda yakin ingin menghapus jadwal ini?</h5>
                    <p class="text-muted">Jadwal ini akan dihapus secara permanen.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Batal Kode Booking -->
    <div class="modal fade" id="batalKodeBookingModal" tabindex="-1" role="dialog" aria-labelledby="batalKodeBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="batalKodeBookingModalLabel">
                        <i class="fas fa-times-circle"></i> Konfirmasi Pembatalan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5>Apakah Anda yakin ingin membatalkan kode booking ini?</h5>
                        <p class="text-muted mb-0">Kode Booking: <strong id="displayKodeBooking"></strong></p>
                    </div>
                    <div class="form-group mt-3">
                        <label for="keteranganBatal">Keterangan Pembatalan <small class="text-muted">(opsional)</small></label>
                        <textarea class="form-control" id="keteranganBatal" rows="3" placeholder="Masukkan alasan pembatalan atau kosongkan jika tidak ada"></textarea>
                        <small class="form-text text-muted">Jika dikosongkan, akan diisi dengan "-"</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-danger" id="btnKonfirmasiBatal">Ya, Batalkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/json-viewer.js"></script>

    <script>
        var jsonViewer;
        var currentJadwalData = [];
        var currentDeleteIndex = null;
        var currentUpdateButton = null;
        var currentUpdateIndex = null;

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

        function getNamaHari(hariNum) {
            const hariNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Hari Libur Nasional'];
            return hariNames[parseInt(hariNum)] || 'Tidak diketahui';
        }

        function getNamaPoli(kodePoli) {
            const poliMap = {
                'ANA': 'ANAK', 'BED': 'BEDAH', '018': 'BEDAH DIGESTIF',
                'BDM': 'BEDAH MULUT', '017': 'BEDAH ONKOLOGI',
                '005': 'GASTROENTEROLOGY DAN HEPATOLOGY', '006': 'GERIATRI',
                'GIG': 'GIGI', 'JAN': 'JANTUNG DAN PEMBULUH DARAH',
                'JIW': 'KEDOKTERAN JIWA', 'KLT': 'KULIT KELAMIN',
                'MAT': 'MATA', 'OBG': 'OBSTETRI DAN GINEKOLOGI',
                'ORT': 'ORTHOPEDI DAN TRAUMATOLOGY', 'PAR': 'PARU',
                'INT': 'PENYAKIT DALAM (Interne)', 'IRM': 'REHAB MEDIK',
                'SAR': 'SARAF', 'THT': 'TELINGA HIDUNG DAN TENGGOROKAN'
            };
            return poliMap[kodePoli] || kodePoli;
        }

        function getNextDatesForDay(dayOfWeek, weeksCount) {
            var dates = [];
            var today = new Date();
            var currentDay = today.getDay();
            var targetDay = (dayOfWeek === 7) ? 0 : dayOfWeek;
            var daysUntilTarget = (targetDay - currentDay + 7) % 7;
            
            if (daysUntilTarget === 0 && today.getHours() >= 17) {
                daysUntilTarget = 7;
            }
            
            for (var i = 0; i < weeksCount; i++) {
                var nextDate = new Date(today);
                nextDate.setDate(today.getDate() + daysUntilTarget + (i * 7));
                var dateStr = nextDate.getFullYear() + '-' +
                    String(nextDate.getMonth() + 1).padStart(2, '0') + '-' +
                    String(nextDate.getDate()).padStart(2, '0');
                dates.push(dateStr);
            }
            
            return dates;
        }

        function fetchBookingFor5Weeks(hariNum, jamPraktek, kodepoli, kodedokter) {
            var tbody = $("#bookingTableBody");
            
            var dates = getNextDatesForDay(hariNum, 5);
            var allBookings = [];
            var completedRequests = 0;
            var totalRequests = dates.length;
            
            dates.forEach(function(tanggal) {
                var postData = {
                    jenisAPI: $("#selectAPI").val(),
                    consid: $("#consid").val(),
                    secret: $("#secret").val(),
                    user_key: $("#user_key").val(),
                    is_encrypt: 1,
                    username: $("#username").val(),
                    password: $("#password").val(),
                    method: "GET",
                    url: $("#url").val().replace(/\/$/, '') + "/antrean/pendaftaran/tanggal/" + tanggal,
                    withParam: 0,
                    params: ""
                };
                
                $.ajax({
                    url: "getResponse.php",
                    method: "POST",
                    data: postData,
                    success: function(response) {
                        try {
                            var parsed = JSON.parse(response);
                            var list = [];
                            
                            if (Array.isArray(parsed.response)) {
                                list = parsed.response;
                            } else if (parsed.response && Array.isArray(parsed.response.list)) {
                                list = parsed.response.list;
                            }
                            
                            var filtered = list.filter(function(item) {
                                return item.kodedokter === kodedokter;
                            });
                            
                            allBookings = allBookings.concat(filtered);
                        } catch (error) {
                            console.error("Error parsing response:", error);
                        }
                        
                        completedRequests++;
                        if (completedRequests === totalRequests) {
                            displayAllBookings(allBookings, jamPraktek, kodedokter);
                        }
                    },
                    error: function() {
                        completedRequests++;
                        if (completedRequests === totalRequests) {
                            displayAllBookings(allBookings, jamPraktek, kodedokter);
                        }
                    }
                });
            });
        }

        function displayAllBookings(bookings, jamPraktek, kodedokter) {
    var container = $("#bookingGroupContainer");
    container.empty();
    
    if (bookings.length > 0) {
        // Sort by date
        bookings.sort(function(a, b) {
            return new Date(a.tanggal) - new Date(b.tanggal);
        });
        
        // Group by tanggal
        var groupedBookings = {};
        bookings.forEach(function(item) {
            var tanggal = item.tanggal || 'Unknown';
            if (!groupedBookings[tanggal]) {
                groupedBookings[tanggal] = [];
            }
            groupedBookings[tanggal].push(item);
        });
        
        // Create collapsible groups
        Object.keys(groupedBookings).forEach(function(tanggal, groupIndex) {
            var items = groupedBookings[tanggal];
            var groupId = 'bookingGroup' + groupIndex;
            
            var groupHtml = `
                <div class="booking-group">
                    <div class="booking-group-header" data-target="#${groupId}">
                        <div>
                            <span class="booking-group-title">
                                <i class="far fa-calendar-alt"></i> ${tanggal}
                            </span>
                        </div>
                        <div>
                            <span class="booking-group-badge">${items.length} kode booking</span>
                            <i class="fas fa-chevron-down booking-group-icon"></i>
                        </div>
                    </div>
                    <div class="booking-group-content" id="${groupId}">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Booking</th>
                                    <th>Jam Praktik</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            items.forEach(function(item) {
                var rowClass = '';
                var highlightIcon = '';
                var btnDisabled = '';
                var btnClass = 'btn-danger';
                var btnText = '<i class="fas fa-times"></i> Batal';
                
                if (item.kodedokter == kodedokter && item.jampraktek == jamPraktek) {
                    rowClass = 'table-warning';
                    highlightIcon = '<i class="fas fa-exclamation-triangle text-warning"></i> ';
                }
                
                // Check jika status sudah batal
                if (item.status && item.status.toLowerCase() === 'batal') {
                    btnDisabled = 'disabled';
                    btnClass = 'btn-secondary';
                    btnText = '<i class="fas fa-ban"></i> Dibatalkan';
                }
                
                groupHtml += `
                    <tr class="${rowClass}">
                        <td>${highlightIcon}${item.kodebooking || '-'}</td>
                        <td>${item.jampraktek || '-'}</td>
                        <td>${item.status || '-'}</td>
                        <td>
                            <button type="button" 
                                class="btn ${btnClass} btn-sm btn-block btn-batal-booking" 
                                data-kodebooking="${item.kodebooking}"
                                data-jampraktek="${item.jampraktek}"
                                data-tanggal="${tanggal}"
                                ${btnDisabled}>
                                ${btnText}
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            groupHtml += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            
            container.append(groupHtml);
        });
        
        // Add click event for expand/collapse
        $('.booking-group-header').on('click', function() {
            var target = $(this).data('target');
            $(this).toggleClass('active');
            $(target).toggleClass('show');
        });
        
        // Add click event for batal button
        $('.btn-batal-booking').on('click', function() {
            if (!$(this).prop('disabled')) {
                var kodebooking = $(this).data('kodebooking');
                var jamPraktek = $(this).data('jampraktek');
                var tanggal = $(this).data('tanggal');
                batalKodeBooking(kodebooking, jamPraktek, tanggal, $(this));
            }
        });
        
    } else {
        var updateKodepoli = $("#update_kodepoli").val();
        var namaPoli = getNamaPoli(updateKodepoli);
        container.html('<div class="alert alert-info">Tidak ada booking untuk poli <strong>' + namaPoli + ' (' + updateKodepoli + ')</strong> dalam 5 minggu ke depan.</div>');
    }
}

// Fungsi untuk membatalkan kode booking (dimodifikasi untuk menerima button element)
function batalKodeBooking(kodebooking, jamPraktek, tanggal, buttonElement) {
    // Tampilkan kode booking di modal
    $('#displayKodeBooking').text(kodebooking);
    
    // Simpan data ke global variable
    window.currentBatalData = {
        kodebooking: kodebooking,
        jamPraktek: jamPraktek,
        tanggal: tanggal,
        buttonElement: buttonElement
    };
    
    // Tampilkan modal
    $('#batalKodeBookingModal').modal('show');
}

// Handler untuk konfirmasi pembatalan (dimodifikasi)
$("#btnKonfirmasiBatal").on("click", function() {
    var keterangan = $("#keteranganBatal").val().trim();
    
    if (!keterangan) {
        keterangan = "-";
    }
    
    if (!window.currentBatalData) {
        showToast('Error', 'Data pembatalan tidak ditemukan', 'error');
        return;
    }
    
    var batalData = {
        kodebooking: window.currentBatalData.kodebooking,
        keterangan: keterangan
    };
    
    var targetButton = window.currentBatalData.buttonElement;
    
    generateTimestampAndSignature();
    
    $.ajax({
        url: "getResponse.php",
        method: "POST",
        data: {
            jenisAPI: $("#selectAPI").val(),
            consid: $("#consid").val(),
            secret: $("#secret").val(),
            user_key: $("#user_key").val(),
            is_encrypt: 1,
            username: "",
            password: "",
            method: "POST",
            url: $("#url").val().replace(/\/$/, '') + "/antrean/batal",
            withParam: 1,
            params: JSON.stringify(batalData)
        },
        success: function(response) {
            try {
                var parsed = JSON.parse(response);
                
                if (parsed.metadata || parsed.metaData) {
                    var meta = parsed.metadata || parsed.metaData;
                    
                    if (meta.code === 200) {
                        // Tampilkan icon centang dan ubah ke success
                        if (targetButton) {
                            targetButton.removeClass('btn-danger').addClass('btn-success');
                            targetButton.html('<i class="fas fa-check"></i> Berhasil Dibatalkan');
                            
                            // Setelah 2 detik, disable button
                            setTimeout(function() {
                                targetButton.removeClass('btn-success').addClass('btn-secondary');
                                targetButton.html('<i class="fas fa-ban"></i> Dibatalkan');
                                targetButton.prop('disabled', true);
                            }, 2000);
                        }
                        
                        showToast('Berhasil', 'Kode booking berhasil dibatalkan!', 'success');
                        $('#batalKodeBookingModal').modal('hide');
                        
                        // Refresh data booking setelah 2.5 detik
                        setTimeout(function() {
                            var hariMatch = $("#hariDetail").text();
                            var jamMatch = $("#jamDetail").text();
                            var kodepoli = $("#update_kodepoli").val();
                            var kodedokter = $("#update_kodedokter").val();
                            
                            // Cari hari number dari nama hari
                            var hariNum = 1;
                            var hariNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Hari Libur Nasional'];
                            for (var i = 1; i < hariNames.length; i++) {
                                if (hariNames[i] === hariMatch) {
                                    hariNum = i;
                                    break;
                                }
                            }
                            
                            fetchBookingFor5Weeks(hariNum, jamMatch, kodepoli, kodedokter);
                        }, 2500);
                    } else {
                        showToast('Gagal', meta.message || 'Gagal membatalkan kode booking', 'error');
                        
                        // Kembalikan button ke normal
                        if (targetButton) {
                            targetButton.html('<i class="fas fa-times"></i> Batal');
                            targetButton.prop('disabled', false);
                        }
                    }
                } else {
                    showToast('Error', 'Format response tidak sesuai', 'error');
                    
                    // Kembalikan button ke normal
                    if (targetButton) {
                        targetButton.html('<i class="fas fa-times"></i> Batal');
                        targetButton.prop('disabled', false);
                    }
                }
            } catch (error) {
                showToast('Error', 'Terjadi kesalahan: ' + error.message, 'error');
                
                // Kembalikan button ke normal
                if (targetButton) {
                    targetButton.html('<i class="fas fa-times"></i> Batal');
                    targetButton.prop('disabled', false);
                }
            }
        },
        beforeSend: function() {
            // Tampilkan loading di tombol yang diklik
            if (targetButton) {
                targetButton.html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
                targetButton.prop('disabled', true);
            }
            
            // Tampilkan loading di modal
            $("#btnKonfirmasiBatal").html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);
        },
        complete: function() {
            $("#btnKonfirmasiBatal").html('Ya, Batalkan').prop('disabled', false);
        },
        error: function(xhr, status, error) {
            showToast('Error', 'Terjadi kesalahan: ' + error, 'error');
            
            // Kembalikan button ke normal
            if (targetButton) {
                targetButton.html('<i class="fas fa-times"></i> Batal');
                targetButton.prop('disabled', false);
            }
        }
    });
});

        // Handler untuk konfirmasi pembatalan
        $("#btnKonfirmasiBatal").on("click", function() {
            var keterangan = $("#keteranganBatal").val().trim();
            
            if (!keterangan) {
                keterangan = "-";
            }
            
            if (!window.currentBatalData) {
                showToast('Error', 'Data pembatalan tidak ditemukan', 'error');
                return;
            }
            
            var batalData = {
                kodebooking: window.currentBatalData.kodebooking,
                keterangan: keterangan
            };
            
            generateTimestampAndSignature();
            
            $.ajax({
                url: "getResponse.php",
                method: "POST",
                data: {
                    jenisAPI: $("#selectAPI").val(),
                    consid: $("#consid").val(),
                    secret: $("#secret").val(),
                    user_key: $("#user_key").val(),
                    is_encrypt: 1,
                    username: "",
                    password: "",
                    method: "POST",
                    url: $("#url").val().replace(/\/$/, '') + "/antrean/batal",
                    withParam: 1,
                    params: JSON.stringify(batalData)
                },
                success: function(response) {
                    try {
                        var parsed = JSON.parse(response);
                        
                        if (parsed.metadata || parsed.metaData) {
                            var meta = parsed.metadata || parsed.metaData;
                            
                            if (meta.code === 200) {
                                showToast('Berhasil', 'Kode booking berhasil dibatalkan!', 'success');
                                $('#batalKodeBookingModal').modal('hide');
                                
                                // Refresh data booking
                                var hariMatch = $("#hariDetail").text();
                                var jamMatch = $("#jamDetail").text();
                                var kodepoli = $("#update_kodepoli").val();
                                var kodedokter = $("#update_kodedokter").val();
                                
                                // Cari hari number dari nama hari
                                var hariNum = 1;
                                var hariNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Hari Libur Nasional'];
                                for (var i = 1; i < hariNames.length; i++) {
                                    if (hariNames[i] === hariMatch) {
                                        hariNum = i;
                                        break;
                                    }
                                }
                                
                                fetchBookingFor5Weeks(hariNum, jamMatch, kodepoli, kodedokter);
                            } else {
                                showToast('Gagal', meta.message || 'Gagal membatalkan kode booking', 'error');
                            }
                        } else {
                            showToast('Error', 'Format response tidak sesuai', 'error');
                        }
                    } catch (error) {
                        showToast('Error', 'Terjadi kesalahan: ' + error.message, 'error');
                    }
                },
                beforeSend: function() {
                    $("#btnKonfirmasiBatal").html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);
                },
                complete: function() {
                    $("#btnKonfirmasiBatal").html('Ya, Batalkan').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    showToast('Error', 'Terjadi kesalahan: ' + error, 'error');
                }
            });
        });

        // Reset modal saat ditutup
        $('#batalKodeBookingModal').on('hidden.bs.modal', function() {
            $("#keteranganBatal").val('');
            $("#displayKodeBooking").text('');
            window.currentBatalData = null;
        });

        $(document).ready(function() {
            $("#consid").val(localStorage.getItem('consid') || '');
            $("#secret").val(localStorage.getItem('secret') || '');
            $("#user_key").val(localStorage.getItem('user_key') || '');

            try {
                jsonViewer = new JSONViewer();
                $("#jsonResponse").append(jsonViewer.getContainer());
            } catch (e) {
                console.error("Failed to initialize JSONViewer:", e);
            }

            generateTimestampAndSignature();
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

        $("#consid, #secret").on("input", generateTimestampAndSignature);

        function getWeekdayDates() {
            var today = new Date();
            var dayOfWeek = today.getDay();
            var dates = {};

            var dayMapping = { 1: 1, 2: 2, 3: 3, 4: 4, 5: 5 };
            var mondayOffset = (dayOfWeek === 0 ? -6 : 1 - dayOfWeek);
            var monday = new Date(today);
            monday.setDate(today.getDate() + mondayOffset);

            for (var i = 0; i < 5; i++) {
                var date = new Date(monday);
                date.setDate(monday.getDate() + i);
                var dateStr = date.getFullYear() + '-' +
                    String(date.getMonth() + 1).padStart(2, '0') + '-' +
                    String(date.getDate()).padStart(2, '0');
                dates[dayMapping[i + 1]] = dateStr;
            }

            return dates;
        }

        $("#btnTampilkanJadwal").on("click", function() {
            var kodepoli = $("#update_kodepoli").val();
            var kodesubspesialis = $("#update_kodesubspesialis").val();
            var kodedokter = $("#update_kodedokter").val();

            if (!kodepoli || !kodesubspesialis || !kodedokter) {
                showToast('Validasi Error', 'Kode Poli, Kode Subspesialis, dan Kode Dokter wajib diisi!', 'error');
                return;
            }

            generateTimestampAndSignature();

            $("#step2").show();
            $("#loadingJadwal").show();
            $("#jadwalContainer").hide();

            var weekdayDates = getWeekdayDates();
            var allJadwal = [];
            var completedRequests = 0;
            var totalRequests = 5;

            $.each(weekdayDates, function(hariNum, tanggal) {
                var endpoint = "jadwaldokter/kodepoli/" + kodepoli + "/tanggal/" + tanggal;

                $.ajax({
                    url: "getResponse.php",
                    method: "GET",
                    data: {
                        jenisAPI: $("#selectAPI").val(),
                        consid: $("#consid").val(),
                        secret: $("#secret").val(),
                        user_key: $("#user_key").val(),
                        is_encrypt: 1,
                        username: "",
                        password: "",
                        method: "GET",
                        url: $("#url").val() + endpoint,
                        withParam: 0,
                        params: ""
                    },
                    success: function(response) {
                        try {
                            var parsed = JSON.parse(response);
                            if (parsed.response && Array.isArray(parsed.response)) {
                                var filtered = parsed.response.filter(function(item) {
                                    return item.kodedokter == kodedokter;
                                });

                                if (filtered.length > 0) {
                                    allJadwal.push(filtered[0]);
                                }
                            }
                        } catch (error) {
                            console.error("Error parsing response:", error);
                        }

                        completedRequests++;
                        if (completedRequests === totalRequests) {
                            displayJadwal(allJadwal);
                        }
                    },
                    error: function() {
                        completedRequests++;
                        if (completedRequests === totalRequests) {
                            displayJadwal(allJadwal);
                        }
                    }
                });
            });
        });

        var hariOptions = [
            {val: "1", text: "Senin"}, {val: "2", text: "Selasa"},
            {val: "3", text: "Rabu"}, {val: "4", text: "Kamis"},
            {val: "5", text: "Jumat"}, {val: "6", text: "Sabtu"},
            {val: "7", text: "Minggu"}, {val: "8", text: "Hari Libur Nasional"}
        ];

        function displayJadwal(jadwalData) {
            $("#loadingJadwal").hide();
            currentJadwalData = jadwalData;

            var html = '';
            if (jadwalData.length === 0) {
                html += `
                    <div class="alert alert-info modern-alert">
                        <i class="fas fa-info-circle"></i> Tidak ada jadwal ditemukan untuk dokter ini pada minggu ini (Senin-Jumat).
                    </div>
                `;
            } else {
                jadwalData.forEach(function(item, index) {
                    var jamParts = (item.jadwal || '').split('-');
                    var jamBuka = jamParts[0] || '';
                    var jamTutup = jamParts[1] || '';
                    var namaHari = getNamaHari(item.hari);

                    var selectOptions = hariOptions.map(function(opt) {
                        var selected = item.hari == opt.val ? 'selected' : '';
                        return `<option value="${opt.val}" ${selected}>${opt.text}</option>`;
                    }).join('');

                    html += `
                        <div class="jadwal-item" data-index="${index}">
                            <div class="row align-items-end">
                                <div class="col-md-2">
                                    <label class="info-label"><i class="fas fa-calendar-alt"></i> Hari</label>
                                    <select class="form-control jadwal-hari" data-index="${index}">
                                        <option value="">Pilih Hari</option>
                                        ${selectOptions}
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="info-label"><i class="fas fa-clock"></i> Jam Buka</label>
                                    <input type="time" class="form-control jadwal-buka" data-index="${index}" value="${jamBuka}">
                                </div>
                                <div class="col-md-2">
                                    <label class="info-label"><i class="fas fa-clock"></i> Jam Tutup</label>
                                    <input type="time" class="form-control jadwal-tutup" data-index="${index}" value="${jamTutup}">
                                </div>
                                <div class="col-md-3">
                                    <label class="info-label"><i class="fas fa-calendar-day"></i> Hari Praktik</label>
                                    <div class="badge badge-hari">${namaHari}</div>
                                </div>
                                <div class="col-md-3 d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary btn-sm mr-2 btn-update-jadwal" data-index="${index}">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-jadwal" data-index="${index}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            html += `
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-success btn-lg" id="btnTambahJadwalBaru">
                        <i class="fas fa-plus"></i> Tambah Jadwal
                    </button>
                </div>
            `;

            $("#jadwalContainer").html(html);
            $("#jadwalContainer").show();
        }

        $(document).on("change", ".jadwal-hari, .jadwal-buka, .jadwal-tutup", function() {
            var index = $(this).data("index");
            var row = $(`.jadwal-item[data-index="${index}"]`);
            
            currentJadwalData[index].hari = row.find('.jadwal-hari').val();
            var jamBuka = row.find('.jadwal-buka').val();
            var jamTutup = row.find('.jadwal-tutup').val();
            currentJadwalData[index].jadwal = jamBuka + '-' + jamTutup;
        });

        $(document).on("click", ".btn-delete-jadwal", function() {
            var index = $(this).data("index");
            currentDeleteIndex = index;
            $('#confirmDeleteModal').modal('show');
        });

        $("#confirmDeleteBtn").on("click", function() {
            $('#confirmDeleteModal').modal('hide');
            if (currentDeleteIndex !== null) {
                currentJadwalData.splice(currentDeleteIndex, 1);
                displayJadwal(currentJadwalData);
                showToast('Berhasil', 'Jadwal berhasil dihapus!', 'success');
                currentDeleteIndex = null;
            }
        });

        $(document).on("click", "#btnTambahJadwalBaru", function() {
            currentJadwalData.push({
                hari: "",
                jadwal: "",
                kodedokter: $("#update_kodedokter").val()
            });
            displayJadwal(currentJadwalData);
        });

        $(document).on("click", ".btn-update-jadwal", function() {
            currentUpdateButton = $(this);
            currentUpdateIndex = $(this).data('index');
            var item = currentJadwalData[currentUpdateIndex];
            
            if (!item.hari || !item.jadwal) {
                showToast('Validasi Error', 'Jadwal harus lengkap (hari dan jam)!', 'error');
                currentUpdateButton = null;
                currentUpdateIndex = null;
                return;
            }

            var namaHariUpdate = getNamaHari(item.hari);
            $('#confirmUpdateMessage').text(`Apakah Anda yakin ingin mengupdate jadwal ${namaHariUpdate}?`);
            $('#confirmUpdateModal').modal('show');
        });

        $("#confirmUpdateBtn").on("click", function() {
            if (currentUpdateIndex === null) {
                return;
            }
            
            $('#confirmUpdateModal').modal('hide');
            
            var kodepoli = $("#update_kodepoli").val();
            var kodesubspesialis = $("#update_kodesubspesialis").val();
            var kodedokter = parseInt($("#update_kodedokter").val());

            var item = currentJadwalData[currentUpdateIndex];
            var jamParts = item.jadwal.split('-');
            var jadwalArray = [{
                hari: item.hari,
                buka: jamParts[0] || "00:00",
                tutup: jamParts[1] || "00:00"
            }];

            var jsonParam = {
                kodepoli: kodepoli,
                kodesubspesialis: kodesubspesialis,
                kodedokter: kodedokter,
                jadwal: jadwalArray
            };

            $("#params").val(JSON.stringify(jsonParam, null, 2));
            generateTimestampAndSignature();

            var postData = {
                jenisAPI: $("#selectAPI").val(),
                consid: $("#consid").val(),
                secret: $("#secret").val(),
                user_key: $("#user_key").val(),
                is_encrypt: $("#selectIsEncrypt").val(),
                username: $("#username").val(),
                password: $("#password").val(),
                method: "POST",
                url: $("#url").val().replace(/\/$/, '') + $("#endpoint").val(),
                withParam: 1,
                params: $("#params").val()
            };

            $.ajax({
                url: "getResponse.php",
                method: "POST",
                data: postData,
                success: function(e) {
                    try {
                        var parsed = JSON.parse(e);

                        if (parsed.metadata || parsed.metaData) {
                            var meta = parsed.metadata || parsed.metaData;
                            
                            if (meta.code === 200) {
                                showToast('Berhasil', 'Jadwal dokter berhasil diupdate!', 'success');
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else if (meta.code === 201) {
                                var message = meta.message;
                                var hariMatch = message.match(/hari : (\d+)/);
                                var jamMatch = message.match(/jam praktek : ([\d:]+-[\d:]+)/);
                                var hariNum = hariMatch ? hariMatch[1] : '1';
                                var jam = jamMatch ? jamMatch[1] : '08:00-15:30';
                                var namaHari = getNamaHari(hariNum);
                                var namaPoli = getNamaPoli(kodepoli);

                                $("#poliDetail").text(namaPoli + ' (' + kodepoli + ')');
                                $("#hariDetail").text(namaHari);
                                $("#hariDetail2").text(namaHari);
                                $("#jamDetail").text(jam);

                                $('#hfisAlertModal').modal('show');
                                fetchBookingFor5Weeks(parseInt(hariNum), jam, kodepoli, kodedokter);
                                
                                showToast('Konflik Jadwal', 'Jadwal bentrok dengan booking yang ada', 'warning');
                            } else {
                                showToast('Error', meta.message, 'error');
                            }
                        }
                    } catch (error) {
                        showToast('Error', 'Terjadi kesalahan: ' + error.message, 'error');
                    }
                },
                beforeSend: function() {
                    if (currentUpdateButton) {
                        currentUpdateButton.html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);
                    }
                },
                complete: function() {
                    if (currentUpdateButton) {
                        currentUpdateButton.html('<i class="fas fa-save"></i> Update').prop('disabled', false);
                        currentUpdateButton = null;
                    }
                    currentUpdateIndex = null;
                },
                error: function(xhr, status, error) {
                    showToast('Error', 'Terjadi kesalahan: ' + error, 'error');
                    if (currentUpdateButton) {
                        currentUpdateButton.html('<i class="fas fa-save"></i> Update').prop('disabled', false);
                        currentUpdateButton = null;
                    }
                    currentUpdateIndex = null;
                }
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