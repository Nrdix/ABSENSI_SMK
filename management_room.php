<?php
include "config.php";
check_login('guru');

$guru_id = $_SESSION['user_id'];
$is_admin = ($_SESSION['role'] == 'admin');

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_room'])) {
        $nama_room = $_POST['nama_room'];
        $kelas = $_POST['kelas'];
        $mata_pelajaran = $_POST['mata_pelajaran'];
        $guru_id_room = $is_admin ? $_POST['guru_id'] : $guru_id;

        $stmt = $conn->prepare("INSERT INTO rooms (nama_room, guru_id, kelas, mata_pelajaran) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $nama_room, $guru_id_room, $kelas, $mata_pelajaran);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Room berhasil ditambahkan";
            log_activity($_SESSION['user_id'], 'guru', 'tambah_room', 'Menambah room: ' . $nama_room);
        } else {
            $_SESSION['error'] = "Gagal menambahkan room";
        }
        $stmt->close();
    }
    elseif (isset($_POST['edit_room'])) {
        $room_id = $_POST['room_id'];
        $nama_room = $_POST['nama_room'];
        $kelas = $_POST['kelas'];
        $mata_pelajaran = $_POST['mata_pelajaran'];

        $stmt = $conn->prepare("UPDATE rooms SET nama_room=?, kelas=?, mata_pelajaran=? WHERE id=? AND (guru_id=? OR ?)");
        $stmt->bind_param("sssiis", $nama_room, $kelas, $mata_pelajaran, $room_id, $guru_id, $is_admin);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Room berhasil diupdate";
            log_activity($_SESSION['user_id'], 'guru', 'edit_room', 'Mengedit room ID: ' . $room_id);
        } else {
            $_SESSION['error'] = "Gagal mengupdate room";
        }
        $stmt->close();
    }
    elseif (isset($_POST['delete_room'])) {
        $room_id = $_POST['room_id'];

        $stmt = $conn->prepare("DELETE FROM rooms WHERE id=? AND (guru_id=? OR ?)");
        $stmt->bind_param("iis", $room_id, $guru_id, $is_admin);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Room berhasil dihapus";
            log_activity($_SESSION['user_id'], 'guru', 'hapus_room', 'Menghapus room ID: ' . $room_id);
        } else {
            $_SESSION['error'] = "Gagal menghapus room";
        }
        $stmt->close();
    }
    // Tambahkan siswa ke room
    elseif (isset($_POST['add_students_to_room'])) {
        $room_id = $_POST['room_id'];
        $selected_students = $_POST['students'] ?? [];
        
        // Hapus siswa lama dari room ini
        $conn->query("DELETE FROM room_students WHERE room_id = $room_id");
        
        // Tambahkan siswa baru
        if (!empty($selected_students)) {
            $values = [];
            foreach ($selected_students as $student_nis) {
                $values[] = "($room_id, '$student_nis')";
            }
            $insert_query = "INSERT INTO room_students (room_id, nis) VALUES " . implode(',', $values);
            if ($conn->query($insert_query)) {
                $_SESSION['success'] = "Siswa berhasil ditambahkan ke room";
            } else {
                $_SESSION['error'] = "Gagal menambahkan siswa ke room";
            }
        }
    }
    header("Location: management_room.php");
    exit;
}

// Get rooms
if ($is_admin) {
    $rooms_query = "SELECT r.*, g.nama as guru_nama FROM rooms r JOIN guru g ON r.guru_id = g.id ORDER BY r.nama_room";
} else {
    $rooms_query = "SELECT r.*, g.nama as guru_nama FROM rooms r JOIN guru g ON r.guru_id = g.id WHERE r.guru_id = '$guru_id' ORDER BY r.nama_room";
}
$rooms_result = $conn->query($rooms_query);

// Get teachers for admin
$teachers_result = $conn->query("SELECT id, nama FROM guru ORDER BY nama");

// Get unique classes
$classes_result = $conn->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Room - SMKN 1 Air Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/LOGO SMKN.png" alt="SMKN 1 Air Putih" height="40" class="me-2">
                Management Room
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

        <!-- Add Room Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Tambah Room Baru</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="row g-3">
                        <?php if ($is_admin): ?>
                        <div class="col-md-3">
                            <label class="form-label">Guru:</label>
                            <select name="guru_id" class="form-control" required>
                                <?php while($teacher = $teachers_result->fetch_assoc()): ?>
                                    <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['nama']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-3">
                            <label class="form-label">Nama Room:</label>
                            <input type="text" name="nama_room" class="form-control" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Kelas:</label>
                            <select name="kelas" class="form-control" required>
                                <option value="">Pilih Kelas</option>
                                <?php while($class = $classes_result->fetch_assoc()): ?>
                                    <option value="<?php echo $class['kelas']; ?>"><?php echo $class['kelas']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Mata Pelajaran:</label>
                            <input type="text" name="mata_pelajaran" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" name="add_room" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Room
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rooms List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Room</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Room</th>
                                <th>Guru</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($rooms_result->num_rows > 0): ?>
                                <?php while($room = $rooms_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $room['nama_room']; ?></td>
                                    <td><?php echo $room['guru_nama']; ?></td>
                                    <td><?php echo $room['kelas']; ?></td>
                                    <td><?php echo $room['mata_pelajaran']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($room['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                data-bs-target="#editModal" 
                                                data-room='<?php echo json_encode($room); ?>'>
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                            <button type="submit" name="delete_room" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Yakin ingin menghapus room ini?')">
                                                <i class="fas fa-trash me-1"></i>Hapus
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                                data-bs-target="#manageStudentsModal" 
                                                data-room-id="<?php echo $room['id']; ?>">
                                            <i class="fas fa-users me-1"></i>Kelola Siswa
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada room yang dibuat</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="room_id" id="editRoomId">
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Room:</label>
                            <input type="text" name="nama_room" id="editNamaRoom" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kelas:</label>
                            <select name="kelas" id="editKelas" class="form-control" required>
                                <option value="">Pilih Kelas</option>
                                <?php 
                                $classes_result->data_seek(0);
                                while($class = $classes_result->fetch_assoc()): ?>
                                    <option value="<?php echo $class['kelas']; ?>"><?php echo $class['kelas']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mata Pelajaran:</label>
                            <input type="text" name="mata_pelajaran" id="editMataPelajaran" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_room" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Manage Students -->
    <div class="modal fade" id="manageStudentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kelola Siswa dalam Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="room_id" id="manageRoomId">
                        
                        <div class="mb-3">
                            <label class="form-label">Pilih Siswa:</label>
                            <select name="students[]" class="form-control" multiple size="10">
                                <?php
                                $students_query = "SELECT nis, nama, kelas, jurusan FROM siswa ORDER BY kelas, nama";
                                $students_result = $conn->query($students_query);
                                while($student = $students_result->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $student['nis']; ?>">
                                        <?php echo $student['nis'] . ' - ' . $student['nama'] . ' (' . $student['kelas'] . ' ' . $student['jurusan'] . ')'; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <small class="form-text text-muted">Gunakan Ctrl+Click untuk memilih multiple siswa</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_students_to_room" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit modal functionality
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const room = JSON.parse(button.getAttribute('data-room'));
            
            document.getElementById('editRoomId').value = room.id;
            document.getElementById('editNamaRoom').value = room.nama_room;
            document.getElementById('editKelas').value = room.kelas;
            document.getElementById('editMataPelajaran').value = room.mata_pelajaran;
        });

        // Script untuk modal manage students
        const manageStudentsModal = document.getElementById('manageStudentsModal');
        manageStudentsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const roomId = button.getAttribute('data-room-id');
            document.getElementById('manageRoomId').value = roomId;
            
            // Load siswa yang sudah dipilih untuk room ini
            fetch('get_room_students.php?room_id=' + roomId)
                .then(response => response.json())
                .then(students => {
                    const select = document.querySelector('select[name="students[]"]');
                    Array.from(select.options).forEach(option => {
                        option.selected = students.includes(option.value);
                    });
                });
        });
    </script>
</body>
</html>