<?php
include "config.php";
check_login('guru');

$guru_id = $_SESSION['user_id'];
$is_admin = ($_SESSION['role'] == 'admin');

// Ambil data room
$room_query = "SELECT r.*, g.nama as guru_nama, 
               (SELECT COUNT(*) FROM room_students rs WHERE rs.room_id = r.id) as jumlah_siswa
               FROM rooms r 
               JOIN guru g ON r.guru_id = g.id 
               WHERE r.guru_id = '$guru_id' OR '$is_admin' = 1
               ORDER BY r.nama_room";
$room_result = $conn->query($room_query);

// Filter
$filter_tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$filter_room = $_GET['room'] ?? '';

// Query data absensi
$absensi_query = "SELECT s.nis, s.nama, s.kelas, s.jurusan, 
                  a.status, a.keterangan, a.waktu_absen
                  FROM siswa s 
                  LEFT JOIN absensi a ON s.nis = a.nis AND a.tanggal = '$filter_tanggal'
                  ORDER BY s.kelas, s.nama";
$absensi_result = $conn->query($absensi_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - SMKN 1 Air Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/LOGO SMKN.png" alt="SMKN 1 Air Putih" height="40" class="me-2">
                Dashboard Guru
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Halo, <?php echo $_SESSION['nama']; ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Room Cards -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Pilih Room Mengajar</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php while($room = $room_result->fetch_assoc()): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card room-card <?php echo $filter_room == $room['id'] ? 'active' : ''; ?>" 
                                     onclick="window.location.href='?room=<?php echo $room['id']; ?>&tanggal=<?php echo $filter_tanggal; ?>'">
                                    <div class="card-body text-center">
                                        <h5><?php echo $room['nama_room']; ?></h5>
                                        <p class="text-muted"><?php echo $room['mata_pelajaran']; ?></p>
                                        <span class="badge bg-primary"><?php echo $room['jumlah_siswa']; ?> Siswa</span>
                                        <span class="badge bg-secondary"><?php echo $room['kelas']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($filter_room): ?>
        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filter Absensi</h5>
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">
                    <input type="hidden" name="room" value="<?php echo $filter_room; ?>">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal:</label>
                        <input type="date" name="tanggal" class="form-control" value="<?php echo $filter_tanggal; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <a href="scan_absen.php?room=<?php echo $filter_room; ?>" class="btn btn-success w-100">
                            <i class="fas fa-qrcode me-2"></i>Scan Absen
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Absensi -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Data Absensi - <?php echo date('d/m/Y', strtotime($filter_tanggal)); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $absensi_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['nis']; ?></td>
                                <td><?php echo $row['nama']; ?></td>
                                <td><?php echo $row['kelas']; ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        switch($row['status'] ?? 'Alpha') {
                                            case 'Hadir': echo 'success'; break;
                                            case 'Izin': echo 'warning'; break;
                                            case 'Sakit': echo 'info'; break;
                                            case 'Cabut': echo 'danger'; break;
                                            default: echo 'secondary';
                                        }
                                    ?>">
                                        <?php echo $row['status'] ?? 'Alpha'; ?>
                                    </span>
                                </td>
                                <td><?php echo $row['waktu_absen'] ?? '-'; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editModal" 
                                            data-nis="<?php echo $row['nis']; ?>"
                                            data-tanggal="<?php echo $filter_tanggal; ?>"
                                            data-status="<?php echo $row['status'] ?? ''; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Status Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="update_absensi.php">
                    <div class="modal-body">
                        <input type="hidden" name="nis" id="modalNis">
                        <input type="hidden" name="tanggal" id="modalTanggal">
                        
                        <div class="mb-3">
                            <label class="form-label">Status:</label>
                            <select name="status" class="form-control" required>
                                <option value="Hadir">Hadir</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Cabut">Cabut</option>
                                <option value="Alpha">Alpha</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan:</label>
                            <textarea name="keterangan" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('modalNis').value = button.getAttribute('data-nis');
            document.getElementById('modalTanggal').value = button.getAttribute('data-tanggal');
            
            const status = button.getAttribute('data-status');
            if (status) {
                document.querySelector('[name="status"]').value = status;
            }
        });
    </script>
</body>
</html>