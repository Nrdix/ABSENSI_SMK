<?php
include "config.php";
check_login('guru');

// Fungsi haversine formula untuk menghitung jarak
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // meters
    
    $latFrom = deg2rad($lat1);
    $lonFrom = deg2rad($lon1);
    $latTo = deg2rad($lat2);
    $lonTo = deg2rad($lon2);
    
    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;
    
    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    
    return $angle * $earthRadius;
}

$room_id = $_GET['room'] ?? 0;
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nis = trim($_POST['nis']);
    $status = $_POST['status'] ?? 'Hadir';
    $keterangan = $_POST['keterangan'] ?? '';
    $tanggal = date('Y-m-d');
    $waktu = date('H:i:s');
    $lokasi = "SMKN 1 Air Putih";
    
    // Validasi NIS
    if (empty($nis)) {
        $error = "NIS tidak boleh kosong.";
    } else {
        // Cek apakah siswa dengan NIS tersebut ada
        $check_siswa_query = "SELECT * FROM siswa WHERE nis = '$nis'";
        $check_siswa_result = $conn->query($check_siswa_query);
        
        if ($check_siswa_result->num_rows == 0) {
            $error = "Siswa dengan NIS $nis tidak ditemukan.";
        } else {
            // Validasi lokasi
            $user_lat = $_POST['latitude'] ?? 0;
            $user_lng = $_POST['longitude'] ?? 0;
            
            $distance = calculateDistance($user_lat, $user_lng, SCHOOL_LAT, SCHOOL_LNG);
            
            if ($distance > SCHOOL_RADIUS) {
                $error = "Anda berada di luar radius sekolah. Jarak: " . round($distance, 2) . " meter";
            } else {
                // Cek apakah siswa sudah absen hari ini
                $check_absen_query = "SELECT * FROM absensi WHERE nis = '$nis' AND tanggal = '$tanggal'";
                $check_absen_result = $conn->query($check_absen_query);
                
                if ($check_absen_result->num_rows > 0) {
                    // Update data absensi yang sudah ada
                    $update_query = "UPDATE absensi SET status = '$status', keterangan = '$keterangan', 
                                    waktu_absen = '$waktu' WHERE nis = '$nis' AND tanggal = '$tanggal'";
                } else {
                    // Insert data absensi baru
                    $update_query = "INSERT INTO absensi (nis, tanggal, status, keterangan, waktu, lokasi, waktu_absen) 
                                     VALUES ('$nis', '$tanggal', '$status', '$keterangan', NOW(), '$lokasi', '$waktu')";
                }
                
                if ($conn->query($update_query)) {
                    $siswa_data = $check_siswa_result->fetch_assoc();
                    $success = "Absensi berhasil dicatat untuk $nis (" . $siswa_data['nama'] . ")";
                    log_activity($_SESSION['user_id'], 'guru', 'absen', 'Melakukan absensi untuk NIS: ' . $nis);
                } else {
                    $error = "Error: " . $conn->error;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Absen - SMKN 1 Air Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/LOGO SMKN.png" alt="SMKN 1 Air Putih" height="40" class="me-2">
                Scan Absensi
            </a>
            <div class="navbar-nav ms-auto">
                <a href="dguru.php" class="btn btn-outline-light btn-sm">Kembali</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0"><i class="fas fa-qrcode me-2"></i>Scan QR Code Absensi</h4>
            </div>
            
            <div class="card-body">
                <div class="alert alert-info text-center">
                    <i class="fas fa-map-marker-alt me-2"></i> Pastikan berada di wilayah SMKN 1 Air Putih
                    <div id="location-status" class="mt-2 small"></div>
                </div>
                
                <div class="manual-section">
                    <h5 class="text-center mb-3">Input Manual Absensi</h5>
                    <form method="post" id="absenForm">
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">NIS Siswa:</label>
                                <input type="text" name="nis" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select name="status" class="form-control">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Sakit">Sakit</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Keterangan:</label>
                                <textarea name="keterangan" class="form-control" rows="2" 
                                          placeholder="Opsional"></textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check-circle me-2"></i>Simpan Absensi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <?php if ($success): ?>
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get user location
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    showPosition,
                    showError,
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                document.getElementById('location-status').innerHTML = 
                    'Geolocation tidak didukung oleh browser ini.';
            }
        }
        
        function showPosition(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            document.getElementById('location-status').innerHTML = 
                `Lokasi terdeteksi: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
        
        function showError(error) {
            let message = '';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = 'User menolak permintaan geolocation';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = 'Informasi lokasi tidak tersedia';
                    break;
                case error.TIMEOUT:
                    message = 'Permintaan lokasi timeout';
                    break;
                case error.UNKNOWN_ERROR:
                    message = 'Error tidak diketahui';
                    break;
            }
            document.getElementById('location-status').innerHTML = message;
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            getLocation();
        });
    </script>
</body>
</html>