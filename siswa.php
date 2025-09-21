<?php
include "config.php";
check_login();

$nis = $_SESSION['nis'];
$nama = $_SESSION['nama'];
$kelas = $_SESSION['kelas'];
$jurusan = $_SESSION['jurusan'];

// Cek apakah sudah absen hari ini
$today = date('Y-m-d');
$absen_query = "SELECT * FROM absensi WHERE nis = '$nis' AND tanggal = '$today'";
$absen_result = $conn->query($absen_query);
$sudah_absen = $absen_result->num_rows > 0;
$status_absen = $sudah_absen ? $absen_result->fetch_assoc()['status'] : 'Belum Absen';

// Cek waktu absensi
$current_time = date('H:i');
$bisa_absen_pagi = ($current_time >= '05:00' && $current_time <= '15:00');
$bisa_absen_sore = ($current_time >= '16:00' && $current_time <= '17:00');
$bisa_absen = $bisa_absen_pagi || $bisa_absen_sore;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Absen - SMKN 1 Air Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/LOGO SMKN.png" alt="SMKN 1 Air Putih" height="40" class="me-2">
                Kartu Absen Digital
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Halo, <?php echo $nama; ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-2"><?php echo $nama; ?></h4>
                <p class="mb-0">Tunjukkan QR code ini untuk absen</p>
                <div class="status-info mt-2">
                    Status: <span class="badge bg-<?php 
                        echo $status_absen == 'Hadir' ? 'success' : 
                             ($status_absen == 'Izin' ? 'warning' : 
                             ($status_absen == 'Sakit' ? 'info' : 'secondary')); 
                    ?>"><?php echo $status_absen; ?></span>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="qr-container">
                            <div id="qrcode"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="student-info">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>NIS:</strong> <?php echo $nis; ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Kelas:</strong> <?php echo $kelas; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Jurusan:</strong> <?php echo $jurusan; ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Tanggal:</strong> <?php echo date('d/m/Y'); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="action-buttons mt-4">
                            <div class="d-grid gap-2 d-md-flex">
                                <?php if ($bisa_absen && !$sudah_absen): ?>
                                    <a href="scan_absen.php" class="btn btn-primary me-md-2">
                                        <i class="fas fa-qrcode me-2"></i>Absen Sekarang
                                    </a>
                                <?php elseif (!$bisa_absen && !$sudah_absen): ?>
                                    <button class="btn btn-secondary me-md-2" disabled>
                                        <i class="fas fa-clock me-2"></i>Waktu Absen: 07:00-15:00 atau 16:00-17:00
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn btn-warning me-md-2" data-bs-toggle="modal" data-bs-target="#izinModal">
                                    <i class="fas fa-envelope me-2"></i>Ajukan Izin
                                </button>
                                
                                <button onclick="window.print()" class="btn btn-info me-md-2">
                                    <i class="fas fa-print me-2"></i>Cetak Kartu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Izin -->
    <div class="modal fade" id="izinModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajukan Izin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="ajukan_izin.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="nis" value="<?php echo $nis; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Izin</label>
                            <input type="date" name="tanggal" class="form-control" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alasan Izin</label>
                            <textarea name="alasan" class="form-control" rows="3" required 
                                      placeholder="Masukkan alasan izin"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Upload Surat Izin (opsional)</label>
                            <input type="file" name="surat" class="form-control" 
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Maksimal 1MB (PDF, JPG, PNG)</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Ajukan Izin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new QRCode(document.getElementById("qrcode"), {
                text: "<?php echo $nis; ?>",
                width: 150,
                height: 150,
                colorDark: "#2c3e50",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>