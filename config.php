<?php
session_start();

// Set timezone ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_smk";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Koordinat sekolah (pusat) - SMKN 1 Air Putih
define('SCHOOL_LAT', 3.2639431154468643);
define('SCHOOL_LNG', 99.38830819052349);
define('SCHOOL_RADIUS', 100); // dalam meter

// Fungsi log activity
function log_activity($user_id, $user_type, $action, $description) {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, user_type, action, description, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $user_type, $action, $description, $ip);
    $stmt->execute();
    $stmt->close();
}

// Fungsi hapus log lama (lebih dari 3 bulan)
function cleanup_old_logs() {
    global $conn;
    $three_months_ago = date('Y-m-d H:i:s', strtotime('-3 months'));
    
    $conn->query("DELETE FROM activity_logs WHERE created_at < '$three_months_ago'");
}

// Redirect jika tidak login
function check_login($required_role = null) {
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['nis'])) {
        header("Location: index.php");
        exit;
    }
    
    if ($required_role && $_SESSION['role'] != $required_role) {
        header("Location: unauthorized.php");
        exit;
    }
}
?>