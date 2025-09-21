<?php
// File untuk mengupdate timezone database
include "config.php";

// Set timezone untuk MySQL
$conn->query("SET time_zone = '+07:00'");

// Update semua timestamp columns jika perlu
$conn->query("ALTER TABLE absensi MODIFY waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
$conn->query("ALTER TABLE activity_logs MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
$conn->query("ALTER TABLE backup_logs MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
$conn->query("ALTER TABLE permissions MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
$conn->query("ALTER TABLE permissions MODIFY updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
$conn->query("ALTER TABLE rooms MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

echo "Timezone updated successfully!";
?>