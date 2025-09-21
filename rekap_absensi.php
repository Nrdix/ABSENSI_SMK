<?php
include "config.php";
check_login('guru');

// Filter parameters
$filter_bulan = $_GET['bulan'] ?? date('Y-m');
$filter_kelas = $_GET['kelas'] ?? '';
$filter_jurusan = $_GET['jurusan'] ?? '';
$filter_room = $_GET['room'] ?? '';

// Build where clause
$where_conditions = ["a.tanggal LIKE '$filter_bulan%'"];
if (!empty($filter_kelas)) $where_conditions[] = "s.kelas = '$filter_kelas'";
if (!empty($filter_jurusan)) $where_conditions[] = "s.jurusan = '$filter_jurusan'";

$where_clause = implode(' AND ', $where_conditions);

// Get attendance summary
if ($filter_room) {
    $summary_query = "SELECT s.nis, s.nama, s.kelas, s.jurusan, 
                      COUNT(CASE WHEN ad.status = 'Hadir' THEN 1 END) as hadir,
                      COUNT(CASE WHEN ad.status = 'Izin' THEN 1 END) as izin,
                      COUNT(CASE WHEN ad.status = 'Sakit' THEN 1 END) as sakit,
                      COUNT(CASE WHEN ad.status = 'Cabut' THEN 1 END) as cabut,
                      COUNT(CASE WHEN ad.status = 'Alpha' OR ad.status IS NULL THEN 1 END) as alpha,
                      COUNT(ad.status) as total
                      FROM siswa s
                      LEFT JOIN absensi a ON s.nis = a.nis AND a.tanggal LIKE '$filter_bulan%'
                      LEFT JOIN absensi_detail ad ON a.id = ad.absensi_id AND ad.room_id = '$filter_room'
                      WHERE " . $where_clause . "
                      GROUP BY s.nis
                      ORDER BY s.kelas, s.nama";
} else {
    $summary_query = "SELECT s.nis, s.nama, s.kelas, s.jurusan, 
                      COUNT(CASE WHEN a.status = 'Hadir' THEN 1 END) as hadir,
                      COUNT(CASE WHEN a.status = 'Izin' THEN 1 END) as izin,
                      COUNT(CASE WHEN a.status = 'Sakit' THEN 1 END) as sakit,
                      COUNT(CASE WHEN a.status = 'Alpha' THEN 1 END) as alpha,
                      COUNT(a.status) as total
                      FROM siswa s
                      LEFT JOIN absensi a ON s.nis = a.nis AND a.tanggal LIKE '$filter_bulan%'
                      WHERE " . $where_clause . "
                      GROUP BY s.nis
                      ORDER BY s.kelas, s.nama";
}

$summary_result = $conn->query($summary_query);

// Get classes and majors for filter
$kelas_result = $conn->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas");
$jurusan_result = $conn->query("SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan");

// Get rooms for filter
$rooms_result = $conn->query("SELECT id, nama_room FROM rooms ORDER BY nama_room");

// Export to Excel
if (isset($_GET['export'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="rekap_absensi_'.$filter_bulan.'.xls"');
    header('Cache-Control: max-age=0');
    
    echo "<table border='1'>";
    echo "<tr><th colspan='9'>Rekap Absensi SMKN 1 Air Putih - " . date('F Y', strtotime($filter_bulan)) . "</th></tr>";
    echo "<tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Jurusan</th>
            <th>Hadir</th>
            <th>Izin</th>
            <th>Sakit</th>
            <th>Alpha</th>
            <th>Total</th>
            <th>Persentase</th>
          </tr>";
    
    $no = 1;
    $summary_result->data_seek(0);
    while ($row = $summary_result->fetch_assoc()) {
        $total = $row['hadir'] + $row['izin'] + $row['sakit'] + $row['alpha'];
        $percentage = $total > 0 ? round(($row['hadir'] / $total) * 100, 2) : 0;
        
        echo "<tr>
                <td>$no</td>
                <td>{$row['nis']}</td>
                <td>{$row['nama']}</td>
                <td>{$row['kelas']}</td>
                <td>{$row['jurusan']}</td>
                <td>{$row['hadir']}</td>
                <td>{$row['izin']}</td>
                <td>{$row['sakit']}</td>
                <td>{$row['alpha']}</td>
                <td>$total</td>
                <td>$percentage%</td>
              </tr>";
        $no++;
    }
    echo "</table>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi - SMKN 1 Air Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/LOGO SMKN.png" alt="SMKN 1 Air Putih" height="40" class="me-2">
                Rekap Absensi
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Halo, <?php echo $_SESSION['nama']; ?></span>
                <a href="dguru.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filter Rekap Absensi</h5>
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Bulan:</label>
                        <input type="month" name="bulan" class="form-control" value="<?php echo $filter_bulan; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kelas:</label>
                        <select name="kelas" class="form-control">
                            <option value="">Semua Kelas</option>
                            <?php while($row = $kelas_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['kelas']; ?>" <?php echo $filter_kelas == $row['kelas'] ? 'selected' : ''; ?>>
                                    <?php echo $row['kelas']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jurusan:</label>
                        <select name="jurusan" class="form-control">
                            <option value="">Semua Jurusan</option>
                            <?php 
                            $jurusan_result->data_seek(0);
                            while($row = $jurusan_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['jurusan']; ?>" <?php echo $filter_jurusan == $row['jurusan'] ? 'selected' : ''; ?>>
                                    <?php echo $row['jurusan']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Room:</label>
                        <select name="room" class="form-control">
                            <option value="">Semua Room</option>
                            <?php while($row = $rooms_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $filter_room == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo $row['nama_room']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            <a href="?export=1&bulan=<?php echo $filter_bulan; ?>&kelas=<?php echo $filter_kelas; ?>&jurusan=<?php echo $filter_jurusan; ?>&room=<?php echo $filter_room; ?>" class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i>Export
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Rekap Absensi Bulan: <?php echo date('F Y', strtotime($filter_bulan)); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Hadir</th>
                                <th>Izin</th>
                                <th>Sakit</th>
                                <?php if ($filter_room): ?>
                                <th>Cabut</th>
                                <?php endif; ?>
                                <th>Alpha</th>
                                <th>Total</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($summary_result->num_rows > 0): ?>
                                <?php 
                                $summary_result->data_seek(0);
                                while($row = $summary_result->fetch_assoc()): 
                                    $total = $row['hadir'] + $row['izin'] + $row['sakit'] + ($row['cabut'] ?? 0) + $row['alpha'];
                                    $percentage = $total > 0 ? round(($row['hadir'] / $total) * 100, 2) : 0;
                                ?>
                                <tr>
                                    <td><?php echo $row['nis']; ?></td>
                                    <td><?php echo $row['nama']; ?></td>
                                    <td><?php echo $row['kelas']; ?></td>
                                    <td><?php echo $row['jurusan']; ?></td>
                                    <td class="text-success"><?php echo $row['hadir']; ?></td>
                                    <td class="text-warning"><?php echo $row['izin']; ?></td>
                                    <td class="text-info"><?php echo $row['sakit']; ?></td>
                                    <?php if ($filter_room): ?>
                                    <td class="text-danger"><?php echo $row['cabut'] ?? 0; ?></td>
                                    <?php endif; ?>
                                    <td class="text-danger"><?php echo $row['alpha']; ?></td>
                                    <td><strong><?php echo $total; ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger'); ?>">
                                            <?php echo $percentage; ?>%
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo $filter_room ? '11' : '10'; ?>" class="text-center">Tidak ada data absensi untuk filter yang dipilih</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>