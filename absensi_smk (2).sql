-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2025 at 03:19 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_smk`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpha') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `surat` varchar(255) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  `waktu_absen` time DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `mapel_pilihan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `nis`, `tanggal`, `status`, `keterangan`, `surat`, `waktu`, `waktu_absen`, `lokasi`, `mapel_pilihan`) VALUES
(1, '5085.23', '2025-09-17', 'Hadir', '', NULL, '0000-00-00 00:00:00', '00:00:00', 'SMKN 1 Air Putih', NULL),
(2, '4846.23', '2025-09-17', 'Izin', 'osis\r\n', NULL, '0000-00-00 00:00:00', '00:00:00', 'SMKN 1 Air Putih', NULL),
(3, '4864.23', '2025-09-17', 'Hadir', '', NULL, '0000-00-00 00:00:00', '00:00:00', 'SMKN 1 Air Putih', NULL),
(4, '5065.23', '2025-09-17', 'Hadir', '', NULL, '0000-00-00 00:00:00', '00:00:00', 'SMKN 1 Air Putih', NULL),
(5, '5035.23', '2025-09-17', 'Hadir', '', NULL, '0000-00-00 00:00:00', '00:00:00', 'SMKN 1 Air Putih', NULL),
(6, '5032.23', '2025-09-17', 'Hadir', '', NULL, '0000-00-00 00:00:00', '00:00:00', 'SMKN 1 Air Putih', NULL),
(7, '5085.23', '2025-09-19', 'Izin', '', NULL, '2025-09-18 17:23:43', '00:23:43', 'SMKN 1 Air Putih', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('guru','siswa','admin') DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_type`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 22, 'siswa', 'login', 'Siswa login ke sistem', '::1', '2025-09-18 17:22:31'),
(2, 1, 'guru', 'login', 'Guru/login ke sistem', '::1', '2025-09-18 17:23:30'),
(3, 1, 'guru', 'approve_izin', 'Izin disetujui untuk NIS: 5085.23', '::1', '2025-09-18 17:23:43'),
(4, 1, 'guru', 'tambah_room', 'Menambah room: Matematika-Yuyun', '::1', '2025-09-18 17:25:21'),
(5, 1, 'guru', 'edit_room', 'Mengedit room ID: 1', '::1', '2025-09-18 17:25:25'),
(6, 1, 'guru', 'edit_room', 'Mengedit room ID: 1', '::1', '2025-09-18 17:26:12'),
(7, 2, 'guru', 'login', 'Guru/login ke sistem', '::1', '2025-09-18 17:27:08'),
(8, 2, 'guru', 'tambah_room', 'Menambah room: Matematika-Yuyun', '::1', '2025-09-18 17:27:19'),
(9, 2, 'guru', 'login', 'Guru/login ke sistem', '::1', '2025-09-18 17:51:18'),
(10, 2, 'guru', 'login', 'Guru/login ke sistem', '::1', '2025-09-18 17:59:00'),
(11, 5, 'siswa', 'login', 'Siswa login ke sistem', '::1', '2025-09-18 23:32:34'),
(12, 5, 'siswa', 'login', 'Siswa login ke sistem', '::1', '2025-09-18 23:33:31'),
(13, 5, 'siswa', 'login', 'Siswa login ke sistem', '::1', '2025-09-18 23:33:55'),
(14, 2, 'guru', 'login', 'Guru/login ke sistem', '::1', '2025-09-18 23:34:12'),
(15, 3, 'siswa', 'login', 'Siswa login ke sistem', '::1', '2025-09-19 00:59:29'),
(16, 3, 'siswa', 'login', 'Siswa login ke sistem', '::1', '2025-09-19 00:59:35'),
(17, 3, 'siswa', 'login', 'Siswa login ke sistem', '::1', '2025-09-19 00:59:44'),
(18, 1, 'guru', 'login', 'Guru/login ke sistem', '::1', '2025-09-19 01:00:04'),
(19, 1, 'guru', 'login', 'Guru/login ke sistem', '::1', '2025-09-19 01:00:06'),
(20, 3, 'siswa', 'login', 'Siswa login ke sistem', '::1', '2025-09-19 01:01:51'),
(21, 3, 'siswa', 'login', 'Siswa login ke sistem', '::1', '2025-09-19 01:02:31'),
(22, 2, 'guru', 'login', 'Guru/login ke sistem', '::1', '2025-09-19 01:02:53');

-- --------------------------------------------------------

--
-- Table structure for table `backup_logs`
--

CREATE TABLE `backup_logs` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `size` varchar(20) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `level` enum('guru','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `username`, `password`, `nama`, `level`) VALUES
(1, 'admin', 'smkn1ap', 'Administrator', 'admin'),
(2, 'guru', 'smkn1ap', 'Guru Pengajar', 'guru'),
(3, 'pakguru', 'smkn1ap', 'Guru Wali Kelas', 'guru');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(10) NOT NULL,
  `jurusan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orang_tua`
--

CREATE TABLE `orang_tua` (
  `id` int(11) NOT NULL,
  `nis_siswa` varchar(20) NOT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `no_wa_ayah` varchar(20) DEFAULT NULL,
  `no_wa_ibu` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `nis_siswa` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `alasan` text NOT NULL,
  `surat` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `guru_approval` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `nis_siswa`, `tanggal`, `alasan`, `surat`, `status`, `guru_approval`, `created_at`, `updated_at`) VALUES
(1, '5085.23', '2025-09-19', 'Sakit bukk', 'uploads/permissions/izin_5085.23_20250919002310.pdf', 'approved', 1, '2025-09-18 17:23:10', '2025-09-18 17:23:43');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `nama_room` varchar(100) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `mata_pelajaran` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `nama_room`, `guru_id`, `kelas`, `mata_pelajaran`, `created_at`) VALUES
(1, 'Matematika-Yuyun', 3, 'XII RPL 3', 'Matematika', '2025-09-18 17:25:21'),
(2, 'Matematika-Yuyun', 2, 'XII RPL 3', 'Matematika', '2025-09-18 17:27:19');

-- --------------------------------------------------------

--
-- Table structure for table `room_students`
--

CREATE TABLE `room_students` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_students`
--

INSERT INTO `room_students` (`id`, `room_id`, `nis`, `created_at`) VALUES
(14, 2, '5099.23', '2025-09-19 01:00:38');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_boundaries`
--

CREATE TABLE `sekolah_boundaries` (
  `id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sekolah_boundaries`
--

INSERT INTO `sekolah_boundaries` (`id`, `latitude`, `longitude`, `created_at`) VALUES
(1, '-1.12345678', '100.12345678', '2025-09-17 11:32:52'),
(2, '-1.12345679', '100.12345679', '2025-09-17 11:32:52'),
(3, '-1.12345680', '100.12345680', '2025-09-17 11:32:52'),
(4, '-1.12345681', '100.12345681', '2025-09-17 11:32:52'),
(5, '3.31154391', '99.34271206', '2025-09-17 12:18:30'),
(6, '3.31114391', '99.34271206', '2025-09-17 12:18:30'),
(7, '3.31114391', '99.34311206', '2025-09-17 12:18:30'),
(8, '3.31154391', '99.34311206', '2025-09-17 12:18:30');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `jurusan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nis`, `nama`, `password`, `kelas`, `jurusan`) VALUES
(1, '5097.23', 'ADITYA RAMADHAN', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(2, '5062.23', 'AL-AZRI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(3, '5099.23', 'ALFIANDRA IRFANSYAH', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(4, '5027.23', 'ANNA SINAGA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(5, '5065.23', 'ANNUR SAFITRI DANI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(6, '5100.23', 'ARDIYANA FIRDA SARI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(7, '5066.23', 'ASTRI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(8, '5029.23', 'AYU ANGGRAINI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(9, '5101.23', 'CHAIRI BUNGA LESTARI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(10, '5102.23', 'CHAIRUNNISA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(11, '5032.23', 'CRISTIAN SIAGIAN', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(12, '5070.23', 'DILA SAFIRA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(13, '5071.23', 'DINDA AULIA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(14, '5073.23', 'DZAKI AMAR FIROS', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(15, '5035.23', 'EMIRSYAH FAHMI SIMAMORA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(16, '5107.23', 'FAZA RAFIF RAMADHAN', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(17, '5077.23', 'HANIFA SYAIBA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(18, '5080.23', 'KAYLA FELISHA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(19, '5113.23', 'LYLA ATHAYA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(20, '5082.23', 'MIFTAH SYIR ALIYYA SARAAN', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(21, '5116.23', 'MUHAMMAD FIKRI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(22, '5085.23', 'MUHAMMAD NURHADI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(23, '5086.23', 'MUSTIKA DWIANA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(24, '5044.23', 'NADEA SYAHFITRI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(25, '5124.23', 'NURSAHILA RIZKA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(26, '5048.23', 'RINIATI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(27, '5092.23', 'SIFA VIRLYA SITEPU', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(28, '5094.23', 'SYAH RENO DWI IHRAZUL PURBA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(29, '5095.23', 'SYAHKIRA ASMARA PUTRI', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(30, '5056.23', 'TIFANY INTAN SIAGIAN', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(31, '5057.23', 'VERLITA GUSTIARA', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(32, '5060.23', 'ZEVANY HIZKIA BR MANIK', 'smkn1ap', 'XII RPL 3', 'REKAYASA PERANGKAT LUNAK'),
(54, '4845.23', 'ADELYA WAHYUNINGTIAS', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(55, '4846.23', 'ADILLA AZZAHRA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(56, '4847.23', 'ANNAI LUSIA SITUMORANG', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(57, '4848.23', 'ARIL ALFAHRI PURBA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(58, '4849.23', 'BALQIS NUR JAHRA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(59, '4850.23', 'CINDY AULIA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(60, '4851.23', 'CLARA SIMBOLON', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(61, '4852.23', 'DITHA YULIANTI SIRAIT', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(62, '4853.23', 'EVAN FARRAS HAZIM', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(63, '4854.23', 'FADLY ARKHAN NASUTION', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(64, '4855.23', 'FAIZ AULIA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(65, '4856.23', 'FAREL IKHSAN RASHYA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(66, '4857.23', 'FATTAHUL UKHWA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(67, '4858.23', 'FIZA AL FITRA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(68, '4859.23', 'GLENN SITUMORANG', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(69, '4860.23', 'JELITA GULTOM', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(70, '4861.23', 'JESICA NURFADILA SUSENO', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(71, '4862.23', 'KELANA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(72, '4863.23', 'KHARINNA TASYA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(73, '4864.23', 'KRISMADAYANTI', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI'),
(74, '4865.23', 'KIMIA', 'smkn1ap', 'XII KI 1', 'KIMIA INDUSTRI');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nis` (`nis`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orang_tua`
--
ALTER TABLE `orang_tua`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nis_siswa` (`nis_siswa`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nis_siswa` (`nis_siswa`),
  ADD KEY `guru_approval` (`guru_approval`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `room_students`
--
ALTER TABLE `room_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `nis` (`nis`);

--
-- Indexes for table `sekolah_boundaries`
--
ALTER TABLE `sekolah_boundaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `backup_logs`
--
ALTER TABLE `backup_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orang_tua`
--
ALTER TABLE `orang_tua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `room_students`
--
ALTER TABLE `room_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sekolah_boundaries`
--
ALTER TABLE `sekolah_boundaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`);

--
-- Constraints for table `orang_tua`
--
ALTER TABLE `orang_tua`
  ADD CONSTRAINT `orang_tua_ibfk_1` FOREIGN KEY (`nis_siswa`) REFERENCES `siswa` (`nis`) ON DELETE CASCADE;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`nis_siswa`) REFERENCES `siswa` (`nis`) ON DELETE CASCADE,
  ADD CONSTRAINT `permissions_ibfk_2` FOREIGN KEY (`guru_approval`) REFERENCES `guru` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_students`
--
ALTER TABLE `room_students`
  ADD CONSTRAINT `room_students_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_students_ibfk_2` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
