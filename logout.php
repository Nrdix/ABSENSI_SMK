<?php
include "config.php";

// Log activity sebelum logout
if (isset($_SESSION['user_id'])) {
    log_activity($_SESSION['user_id'], $_SESSION['role'], 'logout', 'Logout dari sistem');
} elseif (isset($_SESSION['nis'])) {
    log_activity($_SESSION['nis'], 'siswa', 'logout', 'Logout dari sistem');
}

session_destroy();
header("Location: index.php");
exit;
?>