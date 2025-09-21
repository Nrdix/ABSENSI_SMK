<?php
include "config.php";
check_login('guru');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nis = $_POST['nis'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];
    $keterangan = $_POST['keterangan'] ?? '';
    
    // Cek apakah data absensi sudah ada
    $check_query = "SELECT * FROM absensi WHERE nis = '$nis' AND tanggal = '$tanggal'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        // Update data absensi yang sudah ada
        $update_query = "UPDATE absensi SET status = '$status', keterangan = '$keterangan' 
                         WHERE nis = '$nis' AND tanggal = '$tanggal'";
    } else {
        // Insert data absensi baru
        $update_query = "INSERT INTO absensi (nis, tanggal, status, keterangan, waktu, lokasi) 
                         VALUES ('$nis', '$tanggal', '$status', '$keterangan', NOW(), 'SMKN 1 Air Putih')";
    }
    
    if ($conn->query($update_query)) {
        $_SESSION['success'] = "Status absensi berhasil diperbarui.";
        log_activity($_SESSION['user_id'], 'guru', 'update_absen', 'Memperbarui status absensi NIS: ' . $nis);
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
    
    header("Location: dguru.php?tanggal=$tanggal");
    exit;
} else {
    header("Location: dguru.php");
    exit;
}
?>