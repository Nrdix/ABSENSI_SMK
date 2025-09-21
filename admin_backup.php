<?php
session_start();
include "config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Backup directory
$backup_dir = 'backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

// Manual backup
if (isset($_POST['backup_now'])) {
    $result = backupDatabase();
    if ($result['status']) {
        $_SESSION['success'] = "Backup berhasil dibuat: " . $result['filename'];
    } else {
        $_SESSION['error'] = "Backup gagal: " . $result['message'];
    }
    header("Location: admin_backup.php");
    exit;
}

// Delete backup
if (isset($_GET['delete'])) {
    $filename = basename($_GET['delete']);
    $filepath = $backup_dir . $filename;
    
    if (file_exists($filepath) && unlink($filepath)) {
        $_SESSION['success'] = "Backup berhasil dihapus";
        
        // Log deletion
        $conn->query("INSERT INTO backup_logs (filename, size, status, message) 
                     VALUES ('$filename', '0', 'success', 'Backup dihapus manual')");
    } else {
        $_SESSION['error'] = "Gagal menghapus backup";
    }
    header("Location: admin_backup.php");
    exit;
}

// Get backup files
$backup_files = [];
if (is_dir($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
            $filepath = $backup_dir . $file;
            $backup_files[] = [
                'filename' => $file,
                'size' => filesize($filepath),
                'modified' => filemtime($filepath)
            ];
        }
    }
    
    // Sort by modified time (newest first)
    usort($backup_files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
}

// Get backup logs
$logs_query = "SELECT * FROM backup_logs ORDER BY created_at DESC LIMIT 50";
$logs_result = $conn->query($logs_query);

// Backup function
function backupDatabase() {
    global $conn, $backup_dir;
    
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $filepath = $backup_dir . $filename;
    
    try {
        // Get all tables
        $tables = [];
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
        
        $sql = "-- MySQL Backup\n";
        $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($tables as $table) {
            // Drop table
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            
            // Create table
            $create_result = $conn->query("SHOW CREATE TABLE `$table`");
            $create_row = $create_result->fetch_row();
            $sql .= $create_row[1] . ";\n\n";
            
            // Insert data
            $data_result = $conn->query("SELECT * FROM `$table`");
            if ($data_result->num_rows > 0) {
                $sql .= "INSERT INTO `$table` VALUES\n";
                $rows = [];
                while ($row = $data_result->fetch_row()) {
                    $values = array_map(function($value) use ($conn) {
                        return $value === null ? 'NULL' : "'" . $conn->real_escape_string($value) . "'";
                    }, $row);
                    $rows[] = "(" . implode(', ', $values) . ")";
                }
                $sql .= implode(",\n", $rows) . ";\n\n";
            }
        }
        
        // Write to file
        if (file_put_contents($filepath, $sql)) {
            $size = filesize($filepath);
            
            // Log success
            $conn->query("INSERT INTO backup_logs (filename, size, status, message) 
                         VALUES ('$filename', '$size', 'success', 'Backup manual berhasil')");
            
            return [
                'status' => true,
                'filename' => $filename,
                'size' => $size
            ];
        } else {
            throw new Exception("Gagal menulis file backup");
        }
    } catch (Exception $e) {
        // Log error
        $conn->query("INSERT INTO backup_logs (filename, size, status, message) 
                     VALUES ('$filename', '0', 'failed', '" . $conn->real_escape_string($e->getMessage()) . "')");
        
        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Backup System - SMKN 1 Air Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/LOGO SMKN.png" alt="SMKN 1 Air Putih" height="40" class="d-inline-block align-text-top me-2">
                Backup System
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Halo, <?php echo $_SESSION['nama']; ?></span>
                <a href="dguru.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Backup Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Backup Database</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form method="post">
                            <button type="submit" name="backup_now" class="btn btn-primary">
                                <i class="fas fa-database"></i> Backup Sekarang
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            Backup otomatis berjalan setiap tanggal 1 pukul 00:00 WIB
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup Files -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>File Backup</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama File</th>
                                <th>Ukuran</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($backup_files)): ?>
                                <?php foreach ($backup_files as $file): ?>
                                <tr>
                                    <td><?php echo $file['filename']; ?></td>
                                    <td><?php echo formatSize($file['size']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', $file['modified']); ?></td>
                                    <td>
                                        <a href="<?php echo $backup_dir . $file['filename']; ?>" download class="btn btn-sm btn-success">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <a href="?delete=<?php echo $file['filename']; ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Yakin ingin menghapus backup ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada file backup</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Backup Logs -->
        <div class="card">
            <div class="card-header">
                <h5>Backup Logs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>File</th>
                                <th>Ukuran</th>
                                <th>Status</th>
                                <th>Pesan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($logs_result->num_rows > 0): ?>
                                <?php while($log = $logs_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></td>
                                    <td><?php echo $log['filename']; ?></td>
                                    <td><?php echo formatSize($log['size']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $log['status'] == 'success' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($log['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $log['message']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada log backup</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    function formatSize($size) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>