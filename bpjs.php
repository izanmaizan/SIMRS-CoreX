<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 28800)) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/json-viewer.css" />
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"></script>
    <!-- <title>VClaim & PCare Rest API Servis</title> -->
    <title>Referensi dan Update Jadwal Dokter</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 position-relative">
                <!-- <h1>VClaim & PCare Rest API Servis</h1> -->
                <h1>Referensi dan Update Jadwal Dokter</h1>
                <a href="logout.php" class="btn btn-outline-danger logout-btn">Logout</a>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-md-7">
                <div class="row form-row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="selectAPI" value="antrean-rs" placeholder="Antrean RS" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="consid" placeholder="ConsID">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="secret" placeholder="Secret">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <button id="btn_show_hide" class="btn btn-primary btn-block">Sembunyikan</button>
                        </div>
                    </div>
                </div>
                <div class="row form-row" id="inputPcare">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="username" placeholder="Username">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="password" placeholder="Password">
                        </div>
                    </div>
                </div>
                <div class="row form-row" id="inputUserKey">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="user_key" placeholder="User Key">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="x-timestamp" placeholder="X-Timestamp" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="x-signature" placeholder="X-Signature" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" id="selectIsEncrypt">
                                <option value="1">Dekripsi</option>
                                <option value="0">Non Dekripsi</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row form-row" id="methodRow" style="display: none;">
                    <div class="col-md-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <select class="custom-select" style="border-top-right-radius: 0; border-bottom-right-radius: 0; " id="method">
                                    <option value="GET" selected>GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                            </div>
                            <input type="text" class="form-control" id="url" value="https://new-api.bpjs-kesehatan.go.id:8080/new-vclaim-rest/" style="background-color: white;">
                        </div>
                    </div>
                </div>
                <div class="row form-row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select class="form-control" id="selectAction">
                                <option value="1">Lihat Jadwal Dokter</option>
                                <option value="0">Update Jadwal Dokter</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Form untuk Lihat Jadwal Dokter -->
                <div class="row form-row" id="lihatParamsRow">
                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="form-control" id="kodepoli" required>
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
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="date" class="form-control" id="tanggal" required>
                        </div>
                    </div>
                </div>

                <!-- Form untuk Update Jadwal Dokter -->
                <div id="updateJadwalForm" style="display: none;">
                    <div class="row form-row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="update_kodepoli">Kode Poli:</label>
                                <select class="form-control" id="update_kodepoli">
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
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="update_kodesubspesialis">Kode Subspesialis:</label>
                                <input type="text" class="form-control" id="update_kodesubspesialis" placeholder="Contoh: ANA">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="update_kodedokter">Kode Dokter:</label>
                                <input type="number" class="form-control" id="update_kodedokter" placeholder="Contoh: 12346">
                            </div>
                        </div>
                    </div>

                    <div class="row form-row">
                        <div class="col-md-12">
                            <label>Jadwal Praktik:</label>
                            <div id="jadwalContainer"></div>
                            <button type="button" class="btn btn-sm btn-success" id="btnTambahJadwal">+ Tambah Jadwal</button>
                        </div>
                    </div>
                </div>

                <div class="row form-row" id="endpointRow">
                    <div class="col-md-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-primary text-white">
                                    Endpoint API
                                </div>
                            </div>
                            <input type="text" class="form-control" id="endpoint" placeholder="Endpoint">
                        </div>
                    </div>
                </div>
                <div class="row form-row">
                    <div class="col-md-12">
                        <button type="button" id="send" class="btn btn-success btn-block">Kirim</button>
                    </div>
                </div>
                <div class="row form-row" id="paramsRow">
                    <div class="col-md-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-primary text-white">
                                    <input type="checkbox" id="withParam" class="mr-2" value="1"> Parameter
                                </div>
                            </div>
                            <textarea class="form-control" id="params" placeholder="Format JSON" style="resize: vertical;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row form-row">
                    <div class="col-md-6">
                        <button class="btn btn-success btn-block" id="btnSaveCredential">Simpan Data Lokal</button>
                        <button class="btn btn-danger btn-block" id="btnDeleteCredential">Hapus Data Lokal</button>
                    </div>
                    <div class="col-md-6">
                        <a href="https://dvlp.bpjs-kesehatan.go.id:8888/trust-mark/portal.html" target="_blank" class="btn btn-outline-primary btn-block">Dokumentasi VClaim</a>
                        <a href="https://new-api.bpjs-kesehatan.go.id/pcare-rest-v3.0" target="_blank" class="btn btn-outline-dark btn-block">Dokumentasi PCare</a>
                        <a href="change_log.json" target="_blank" class="btn btn-outline-info btn-block">Catatan Perubahan</a>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="row form-row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-success text-white">
                                    Response :
                                </div>
                            </div>
                            <select class="custom-select" id="collapse">
                                <option class="collapseResponse" value="collapse_default" selected>Tutup Semua Data</option>
                                <option class="collapseResponse" value="collapse_all">Tampilkan Semua Data</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-secondary btn-block" id="btnJSON">Raw JSON</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="responseContainer">
                            <div id="jsonResponse"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row fixed-bottom">
        <div class="col-md-12 ml-3 mb-2">
            <a href="https://github.com/morizbebenk" target="_blank" class="text-dark" style="text-decoration: none;">Dibuat Oleh Moriz</a>
        </div>
    </div>

    <div class="modal fade" id="modalJSON" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title">Raw JSON</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <pre id="dataJSON"></pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/json-viewer.js"></script>
    <script src="js/main.js"></script>
    <script>
        var jadwalCounter = 0;

        $(document).ready(function() {
            // Fungsi untuk menambah jadwal baru
            $("#btnTambahJadwal").click(function() {
                tambahJadwal();
            });

            // Fungsi untuk menghapus jadwal
            $(document).on("click", ".btn-remove-jadwal", function() {
                $(this).closest(".jadwal-item").remove();
            });

            // Handler untuk perubahan pilihan aksi
            $("#selectAction").change(function() {
                var action = $(this).val();
                if (action == "1") { // Lihat Jadwal Dokter
                    $("#method").val("GET");
                    $("#lihatParamsRow").show();
                    $("#updateJadwalForm").hide();
                    $("#kodepoli").prop("required", true);
                    $("#tanggal").prop("required", true);
                    $("#endpointRow").hide();
                    $("#paramsRow").hide();
                    $("#withParam").prop("checked", false);
                    $("#selectIsEncrypt").val("1"); // Dekripsi
                } else { // Update Jadwal Dokter
                    $("#method").val("POST");
                    $("#lihatParamsRow").hide();
                    $("#updateJadwalForm").show();
                    $("#kodepoli").prop("required", false);
                    $("#tanggal").prop("required", false);
                    $("#endpointRow").show();
                    $("#paramsRow").hide();
                    $("#withParam").prop("checked", true);
                    $("#selectIsEncrypt").val("0"); // Non Dekripsi
                    $("#endpoint").val("jadwaldokter/updatejadwaldokter");
                    
                    // Tambahkan jadwal default jika belum ada
                    if ($("#jadwalContainer").children().length === 0) {
                        tambahJadwal();
                    }
                }
                updateEndpoint();
            });

            // Fungsi untuk update endpoint berdasarkan input
            function updateEndpoint() {
                var action = $("#selectAction").val();
                if (action == "1") {
                    var kodepoli = $("#kodepoli").val().trim();
                    var tanggal = $("#tanggal").val().trim();
                    if (kodepoli && tanggal) {
                        $("#endpoint").val("jadwaldokter/kodepoli/" + kodepoli + "/tanggal/" + tanggal);
                    } else {
                        $("#endpoint").val("");
                    }
                }
            }

            // Event listener untuk input parameter lihat
            $("#kodepoli, #tanggal").on("change", updateEndpoint);

            // Override getResponse
            $("#send").click(function() {
                var action = $("#selectAction").val();
                
                if (action == "1") {
                    // Validasi untuk Lihat Jadwal Dokter
                    var kodepoli = $("#kodepoli").val().trim();
                    var tanggal = $("#tanggal").val().trim();
                    if (!kodepoli || !tanggal) {
                        alert("Kode Poli dan Tanggal wajib diisi untuk Lihat Jadwal Dokter!");
                        return;
                    }
                } else {
                    // Validasi dan buat JSON untuk Update Jadwal Dokter
                    var update_kodepoli = $("#update_kodepoli").val().trim();
                    var update_kodesubspesialis = $("#update_kodesubspesialis").val().trim();
                    var update_kodedokter = $("#update_kodedokter").val().trim();
                    
                    if (!update_kodepoli || !update_kodesubspesialis || !update_kodedokter) {
                        alert("Kode Poli, Kode Subspesialis, dan Kode Dokter wajib diisi!");
                        return;
                    }
                    
                    // Ambil semua jadwal
                    var jadwalArray = [];
                    $(".jadwal-item").each(function() {
                        var hari = $(this).find(".jadwal-hari").val();
                        var buka = $(this).find(".jadwal-buka").val();
                        var tutup = $(this).find(".jadwal-tutup").val();
                        
                        if (hari && buka && tutup) {
                            jadwalArray.push({
                                hari: hari,
                                buka: buka,
                                tutup: tutup
                            });
                        }
                    });
                    
                    if (jadwalArray.length === 0) {
                        alert("Minimal harus ada satu jadwal praktik yang lengkap!");
                        return;
                    }
                    
                    // Buat JSON parameter
                    var jsonParam = {
                        kodepoli: update_kodepoli,
                        kodesubspesialis: update_kodesubspesialis,
                        kodedokter: parseInt(update_kodedokter),
                        jadwal: jadwalArray
                    };
                    
                    // Set ke textarea params
                    $("#params").val(JSON.stringify(jsonParam));
                    $("#withParam").prop("checked", true);
                }

                // Kirim request
                var withParam = $("#withParam").is(":checked") ? 1 : 0;

                $.ajax({
                    url: "getResponse.php",
                    method: "GET",
                    data: {
                        jenisAPI: $("#selectAPI").val(),
                        consid: $("#consid").val(),
                        secret: $("#secret").val(),
                        user_key: $("#user_key").val(),
                        is_encrypt: $("#selectIsEncrypt").val(),
                        username: $("#username").val(),
                        password: $("#password").val(),
                        method: $("#method").val(),
                        url: $("#url").val() + $("#endpoint").val(),
                        withParam: withParam,
                        params: $("#params").val(),
                    },
                    success: function (e) {
                        global_json = e;
                        
                        try {
                            var parsed = JSON.parse(e);
                            
                            // Cek jika hanya ada metadata tanpa response (untuk update, delete, atau no content)
                            if (parsed.metadata && !parsed.response) {
                                var alertClass = parsed.metadata.code >= 200 && parsed.metadata.code < 300 ? 'alert-success' : 'alert-danger';
                                var html = '<div class="alert ' + alertClass + '">' +
                                          '<h5>Response Status</h5>' +
                                          '<dl class="row mb-0">' +
                                          '<dt class="col-sm-3">Code:</dt><dd class="col-sm-9">' + parsed.metadata.code + '</dd>' +
                                          '<dt class="col-sm-3">Message:</dt><dd class="col-sm-9">' + parsed.metadata.message + '</dd>' +
                                          '</dl></div>';
                                $("#jsonResponse").html(html);
                                return;
                            }
                            
                            // Cek apakah ini response lihat jadwal dokter
                            if (action == "1" && parsed.metadata && parsed.response && Array.isArray(parsed.response)) {
                                // Langsung tampilkan sebagai cards untuk Lihat Jadwal Dokter
                                var htmlParts = ['<div class="alert alert-success">Metadata: Code ' + parsed.metadata.code + ' - ' + parsed.metadata.message + '</div>'];
                                
                                if (parsed.response.length > 0) {
                                    htmlParts.push('<div id="accordion">');
                                    var hariNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                    
                                    for (var i = 0; i < parsed.response.length; i++) {
                                        var item = parsed.response[i];
                                        var hariDisplay = hariNames[item.hari] || item.hari;
                                        var jadwalDisplay = item.jadwal.replace('-', ' - ');
                                        
                                        htmlParts.push(
                                            '<div class="card"><div class="card-header" id="heading', i, '">',
                                            '<h5 class="mb-0"><button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse', i, '">',
                                            item.namadokter, ' - ', item.namapoli,
                                            '</button></h5></div>',
                                            '<div id="collapse', i, '" class="collapse"><div class="card-body">',
                                            '<dl class="row">',
                                            '<dt class="col-sm-4">Kode Subspesialis</dt><dd class="col-sm-8">', item.kodesubspesialis, '</dd>',
                                            '<dt class="col-sm-4">Hari</dt><dd class="col-sm-8">', hariDisplay, '</dd>',
                                            '<dt class="col-sm-4">Kapasitas Pasien</dt><dd class="col-sm-8">', item.kapasitaspasien, '</dd>',
                                            '<dt class="col-sm-4">Libur</dt><dd class="col-sm-8">', item.libur, '</dd>',
                                            '<dt class="col-sm-4">Nama Hari</dt><dd class="col-sm-8">', item.namahari, '</dd>',
                                            '<dt class="col-sm-4">Jadwal</dt><dd class="col-sm-8">', jadwalDisplay, '</dd>',
                                            '<dt class="col-sm-4">Nama Subspesialis</dt><dd class="col-sm-8">', item.namasubspesialis, '</dd>',
                                            '<dt class="col-sm-4">Kode Poli</dt><dd class="col-sm-8">', item.kodepoli, '</dd>',
                                            '<dt class="col-sm-4">Kode Dokter</dt><dd class="col-sm-8">', item.kodedokter, '</dd>',
                                            '</dl></div></div></div>'
                                        );
                                    }
                                    htmlParts.push('</div>');
                                } else {
                                    htmlParts.push('<p class="alert alert-info">Tidak ada data jadwal dokter.</p>');
                                }
                                
                                $("#jsonResponse").html(htmlParts.join(''));
                            } else {
                                // Untuk response lainnya, gunakan JSON viewer
                                jsonViewer.showJSON(parsed, null, 2);
                            }
                        } catch (error) {
                            // Jika parsing gagal, tampilkan pesan error
                            global_json = '{"status":"gagal", "pesan":"Response tidak valid atau URL tidak ditemukan"}';
                            jsonViewer.showJSON(JSON.parse(global_json));
                        }

                        $("#send").blur();
                        $("#url").blur();
                        $("#endpoint").blur();
                    },
                    beforeSend: function () {
                        $("#send").html("Memuat..");
                    },
                    complete: function () {
                        $("#send").html("Kirim");
                    },
                });
            });

            // Inisialisasi default
            $("#selectAction").change();
        });

        // Fungsi untuk menambah form jadwal
        function tambahJadwal() {
            jadwalCounter++;
            var html = `
                <div class="jadwal-item">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Hari:</label>
                            <select class="form-control form-control-sm jadwal-hari">
                                <option value="">Pilih Hari</option>
                                <option value="1">Senin</option>
                                <option value="2">Selasa</option>
                                <option value="3">Rabu</option>
                                <option value="4">Kamis</option>
                                <option value="5">Jumat</option>
                                <option value="6">Sabtu</option>
                                <option value="7">Minggu</option>
                                <option value="8">Hari Libur Nasional</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Jam Buka:</label>
                            <input type="time" class="form-control form-control-sm jadwal-buka" placeholder="08:00">
                        </div>
                        <div class="col-md-3">
                            <label>Jam Tutup:</label>
                            <input type="time" class="form-control form-control-sm jadwal-tutup" placeholder="17:00">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-danger btn-remove-jadwal btn-block">Hapus</button>
                        </div>
                    </div>
                </div>
            `;
            $("#jadwalContainer").append(html);
        }
    </script>
</body>
</html>