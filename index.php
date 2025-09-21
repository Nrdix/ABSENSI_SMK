<?php
include "config.php";
cleanup_old_logs();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role == 'siswa') {
        $sql = "SELECT * FROM siswa WHERE nis='$username' AND password='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['nis'] = $row['nis'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['role'] = 'siswa';
            $_SESSION['kelas'] = $row['kelas'];
            $_SESSION['jurusan'] = $row['jurusan'];
            
            log_activity($row['id'], 'siswa', 'login', 'Siswa login ke sistem');
            header("Location: siswa.php");
            exit;
        } else {
            $error = "Login gagal! NIS atau password salah.";
        }
    } else {
        $sql = "SELECT * FROM guru WHERE username='$username' AND password='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['role'] = $row['level'];
            
            log_activity($row['id'], 'guru', 'login', 'Guru/login ke sistem');
            header("Location: dguru.php");
            exit;
        } else {
            $error = "Login gagal! Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absensi SMKN 1 Air Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card fade-in">
            <div class="login-header">
                <img src="img/LOGO SMKN.png" alt="SMKN 1 Air Putih" height="80" class="mb-3">
                <h4>SMKN 1 AIR PUTIH</h4>
                <p class="mb-0">Sistem Absensi Digital</p>
            </div>
            
            <div class="login-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Login Sebagai:</label>
                        <select name="role" class="form-control" required>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru/Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" id="username-label">NIS:</label>
                        <input type="text" name="username" class="form-control" required 
                               placeholder="Masukkan NIS">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password:</label>
                        <input type="password" name="password" class="form-control" required 
                               placeholder="Masukkan Password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('[name="role"]').addEventListener('change', function() {
            const label = document.getElementById('username-label');
            const input = document.querySelector('[name="username"]');
            
            if (this.value === 'siswa') {
                label.textContent = 'NIS:';
                input.placeholder = 'Masukkan NIS';
            } else {
                label.textContent = 'Username:';
                input.placeholder = 'Masukkan Username';
            }
        });
    </script>
</body>
</html>