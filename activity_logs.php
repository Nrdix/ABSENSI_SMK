<?php
session_start();
include "config.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'guru' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php");
    exit;
}

// Filter parameters
$filter_user_type = $_GET['user_type'] ?? '';
$filter_action = $_GET['action'] ?? '';
$filter_date = $_GET['date'] ?? '';

// Build where clause
$where_conditions = [];
if (!empty($filter_user_type)) $where_conditions[] = "user_type = '$filter_user_type'";
if (!empty($filter_action)) $where_conditions[] = "action = '$filter_action'";
if (!empty($filter_date)) $where_conditions[] = "DATE(created_at) = '$filter_date'";

$where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

// Get activity logs
$logs_query = "SELECT l.*, 
               CASE 
                   WHEN l.user_type = 'guru' THEN g.nama
                   WHEN l.user_type = 'siswa' THEN s.nama
                   ELSE 'System'
               END as user_name
               FROM activity_logs l
               LEFT JOIN guru g ON l.user_id = g.id AND l.user_type = 'guru'
               LEFT JOIN siswa s ON l.user_id = s.id AND l.user_type = 'siswa'
               $where_clause
               ORDER BY l.created_at DESC
               LIMIT 1000";
$logs_result = $conn->query($logs_query);

// Get unique actions for filter
$actions_result = $conn->query("SELECT DISTINCT action FROM activity_logs ORDER BY action");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activity Log - SMKN 1 Air Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/LOGO SMKN.png" alt="SMKN 1 Air Putih" height="40" class="d-inline-block align-text-top me-2">
                Activity Log
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
                <h5>Filter Log Activity</h5>
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">User Type:</label>
                        <select name="user_type" class="form-control">
                            <option value="">Semua Type</option>
                            <option value="guru" <?php echo $filter_user_type == 'guru' ? 'selected' : ''; ?>>Guru</option>
                            <option value="siswa" <?php echo $filter_user_type == 'siswa' ? 'selected' : ''; ?>>Siswa</option>
                            <option value="admin" <?php echo $filter_user_type == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Action:</label>
                        <select name="action" class="form-control">
                            <option value="">Semua Action</option>
                            <?php while($action = $actions_result->fetch_assoc()): ?>
                                <option value="<?php echo $action['action']; ?>" <?php echo $filter_action == $action['action'] ? 'selected' : ''; ?>>
                                    <?php echo $action['action']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Tanggal:</label>
                        <input type="date" name="date" class="form-control" value="<?php echo $filter_date; ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="activity_logs.php" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-header">
                <h5>Activity Logs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($logs_result->num_rows > 0): ?>
                                <?php while($log = $logs_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></td>
                                    <td><?php echo $log['user_name'] ?? 'System'; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            switch($log['user_type']) {
                                                case 'guru': echo 'primary'; break;
                                                case 'siswa': echo 'success'; break;
                                                case 'admin': echo 'danger'; break;
                                                default: echo 'secondary';
                                            }
                                        ?>">
                                            <?php echo ucfirst($log['user_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $log['action']; ?></td>
                                    <td><?php echo $log['description']; ?></td>
                                    <td><?php echo $log['ip_address']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data log</td>
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