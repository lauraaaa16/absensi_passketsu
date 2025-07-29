-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2025 at 06:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `paskibra`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpa') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id_absensi`, `id_user`, `nama`, `tanggal`, `status`, `keterangan`, `bukti`, `created_at`) VALUES
(6, 1, 'LAURA', '2025-07-28', 'Hadir', 'Hadir', '', '2025-07-28 19:00:34'),
(7, 2, 'andreansyah indriawan', '2025-07-28', 'Hadir', 'Hadir', '', '2025-07-28 19:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id_kelas` int(11) NOT NULL,
  `tingkat_kelas` enum('X','XI','XII') NOT NULL,
  `nama_kelas` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id_kelas`, `tingkat_kelas`, `nama_kelas`) VALUES
(1, 'XII', 'XII RPL 1'),
(2, 'XII', 'XII RPL 2'),
(3, 'XII', 'XII TEI 1'),
(4, 'XII', 'XII TEI 2'),
(5, 'XII', 'XII TEI 3'),
(6, 'XII', 'XII TAV'),
(7, 'XII', 'XII TKI 1'),
(8, 'XII', 'XII TKI 2'),
(9, 'XII', 'XII TKI 3'),
(10, 'XII', 'XII TKR 1'),
(11, 'XII', 'XII TKR 2'),
(12, 'XII', 'XII TKR 3'),
(13, 'XII', 'XII TKR 4'),
(14, 'XI', 'XI RPL 1'),
(15, 'XI', 'XI RPL 2'),
(16, 'XI', 'XI TEI 1'),
(17, 'XI', 'XI TEI 2'),
(18, 'XI', 'XI TEI 3'),
(19, 'XI', 'XI TAV'),
(20, 'XI', 'XI TKI 1'),
(21, 'XI', 'XI TKI 2'),
(22, 'XI', 'XI TKI 3'),
(23, 'XI', 'XI TKR 1'),
(24, 'XI', 'XI TKR 2'),
(25, 'XI', 'XI TKR 3'),
(26, 'XI', 'XI TKR 4'),
(27, 'X', 'X RPL 1'),
(28, 'X', 'X RPL 2'),
(29, 'X', 'X TEI 1'),
(30, 'X', 'X TEI 2'),
(31, 'X', 'X TEI 3'),
(32, 'X', 'X TAV'),
(33, 'X', 'X TKI 1'),
(34, 'X', 'X TKI 2'),
(35, 'X', 'X TKI 3'),
(36, 'X', 'X TKR 1'),
(37, 'X', 'X TKR 2'),
(38, 'X', 'X TKR 3'),
(39, 'X', 'X TKR 4');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `nohp` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `level` enum('admin','anggota') DEFAULT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_photo` varchar(255) NOT NULL,
  `status` enum('aktif','keluar') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_user`, `nama`, `email`, `alamat`, `nohp`, `username`, `password`, `level`, `id_kelas`, `created_at`, `profile_photo`, `status`) VALUES
(1, 'LAURA', 'lala112@gmail.com', 'pakukerto kemiri sukorejo', '081927839182', 'laura', '25d55ad283aa400af464c76d713c07ad', 'anggota', 1, '2025-07-20 04:49:42', '', 'aktif'),
(2, 'andreansyah indriawan', 'andreansyah@gmail.com', 'bulukandang, rt.04/rw.01', '085748662362', 'andre', '25d55ad283aa400af464c76d713c07ad', 'anggota', 18, '2025-07-28 10:54:18', '', 'aktif'),
(4, 'Admin Passketsu', 'passketsu@gmail.com', 'JL. SUMBER GARENG - SUKOREJO, SUKOREJO, Kec. Sukorejo, Kab. Pasuruan', '08123456789', 'passketsu', '52849afc799acb5361fce3d8cca9e50d', 'admin', 1, '2025-07-29 04:31:09', 'uploads/1753763469_logopas.png', 'aktif');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `fk_absensi_user` (`id_user`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id_kelas`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nama` (`nama`),
  ADD UNIQUE KEY `nama_2` (`nama`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_user` FOREIGN KEY (`id_user`) REFERENCES `pengguna` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `pengguna_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
