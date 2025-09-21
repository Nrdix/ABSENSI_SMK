<?php
session_start();
include "config.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'guru' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php");
    exit;
}

$permission_id = $_GET['id'] ?? 0;

// Get permission details
$permission_query = "SELECT p.*, s.nama as nama_siswa, s.kelas, s.jurusan 
                     FROM permissions p 
                     JOIN siswa s ON p.nis_siswa = s.nis 
                     WHERE p.id = '$permission_id'";
$permission_result = $conn->query($permission_query);
$permission = $permission_result->fetch_assoc();

if (!$permission) {
    $_SESSION['error'] = "Permohonan izin tidak ditemukan";
    header("Location: dguru.php");
    exit;
}

// Process approval
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $catatan = $_POST['catatan'] ?? '';
    
    if ($action == 'approve') {
        $status = 'approved';
        $message = "Izin disetujui";
    } else {
        $status = 'rejected';
        $message = "Izin ditolak";
    }
    
    $update_query = "UPDATE permissions SET status = '$status', guru_approval = '{$_SESSION['user_id']}', updated_at = NOW() WHERE id = '$permission_id'";
    
    if ($conn->query($update_query)) {
        // Update attendance record if approved
        if ($action == 'approve') {
            $absensi_query = "INSERT INTO absensi (nis, tanggal, status, keterangan, waktu, lokasi, waktu_absen) 
                             VALUES ('{$permission['nis_siswa']}', '{$permission['tanggal']}', 'Izin', '$catatan', NOW(), 'SMKN 1 Air Putih', NOW()) 
                             ON DUPLICATE KEY UPDATE status = 'Izin', keterangan = '$catatan'";
            $conn->query($absensi_query);
        }
        
        $_SESSION['success'] = $message;
        log_activity($_SESSION['user_id'], 'guru', 'approve_izin', $message . ' untuk NIS: ' . $permission['nis_siswa']);
    } else {
        $_SESSION['error'] = "Gagal memproses permohonan";
    }
    
    header("Location: dguru.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Review Izin - SMKN 1 Air Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/LOGO SMKN.png" alt="SMKN 1 Air Putih" height="40" class="d-inline-block align-text-top me-2">
                Review Permohonan Izin
            </a>
            <div class="navbar-nav ms-auto">
                <a href="dguru.php" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h5>Detail Permohonan Izin</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>NIS:</strong></div>
                    <div class="col-md-9"><?php echo $permission['nis_siswa']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Nama Siswa:</strong></div>
                    <div class="col-md-9"><?php echo $permission['nama_siswa']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Kelas:</strong></div>
                    <div class="col-md-9"><?php echo $permission['kelas']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Tanggal Izin:</strong></div>
                    <div class="col-md-9"><?php echo date('d/m/Y', strtotime($permission['tanggal'])); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Alasan:</strong></div>
                    <div class="col-md-9"><?php echo nl2br($permission['alasan']); ?></div>
                </div>
                
                <?php if ($permission['surat']): ?>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Surat Izin:</strong></div>
                    <div class="col-md-9">
                        <a href="<?php echo $permission['surat']; ?>" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-download"></i> Download Surat
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($permission['status'] == 'pending'): ?>
                <hr>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional):</label>
                        <textarea name="catatan" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="approve" class="btn btn-success">
                            <i class="fas fa-check"></i> Setujui Izin
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger">
                            <i class="fas fa-times"></i> Tolak Izin
                        </button>
                        <a href="dguru.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
                <?php else: ?>
                <div class="alert alert-info">
                    Status: <strong><?php echo ucfirst($permission['status']); ?></strong><br>
                    Diproses pada: <?php echo date('d/m/Y H:i', strtotime($permission['updated_at'])); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>