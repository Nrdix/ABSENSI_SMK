<?php
include "config.php";
check_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = $_POST['nis'];
    $tanggal = $_POST['tanggal'];
    $alasan = $_POST['alasan'];
    
    // Handle file upload
    $surat_path = null;
    if (isset($_FILES['surat']) && $_FILES['surat']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 1 * 1024 * 1024; // 1MB
        
        if (in_array($_FILES['surat']['type'], $allowed_types) && $_FILES['surat']['size'] <= $max_size) {
            $upload_dir = 'uploads/permissions/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['surat']['name'], PATHINFO_EXTENSION);
            $filename = 'izin_' . $nis . '_' . date('YmdHis') . '.' . $file_extension;
            $surat_path = $upload_dir . $filename;
            
            move_uploaded_file($_FILES['surat']['tmp_name'], $surat_path);
        }
    }
    
    // Insert ke database
    $stmt = $conn->prepare("INSERT INTO permissions (nis_siswa, tanggal, alasan, surat) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nis, $tanggal, $alasan, $surat_path);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Izin berhasil diajukan. Menunggu persetujuan guru.";
        log_activity($_SESSION['nis'], 'siswa', 'ajukan_izin', 'Mengajukan izin untuk tanggal ' . $tanggal);
    } else {
        $_SESSION['error'] = "Gagal mengajukan izin. Silakan coba lagi.";
    }
    
    $stmt->close();
    header("Location: siswa.php");
    exit;
}
?>