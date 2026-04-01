-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2026 at 05:53 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_aspirasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `input_aspirasi`
--

CREATE TABLE `input_aspirasi` (
  `id_pelaporan` int(5) NOT NULL,
  `nis` int(10) NOT NULL,
  `id_kategori` int(5) NOT NULL,
  `lokasi` varchar(50) NOT NULL,
  `ket` varchar(50) NOT NULL,
  `tanggal_input` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `input_aspirasi`
--

INSERT INTO `input_aspirasi` (`id_pelaporan`, `nis`, `id_kategori`, `lokasi`, `ket`, `tanggal_input`) VALUES
(5, 2023001005, 6, 'Perpustakaan', 'Koleksi buku pelajaran sangat terbatas', '2026-02-28 10:40:22'),
(7, 2023001007, 4, 'Lapangan Olahraga', 'Kegiatan pramuka perlu ditingkatkan', '2026-02-28 10:40:22'),
(8, 2023001008, 8, 'Pintu Gerbang', 'Penjagaan keamanan di gerbang kurang ketat', '2026-02-28 10:40:22'),
(9, 2023001009, 10, 'Ruang Guru', 'Guru sering terlambat masuk kelas', '2026-02-28 10:40:22'),
(10, 2023001010, 11, 'Ruang Kelas XII-A', 'Jadwal pelajaran terlalu padat', '2026-02-28 10:40:22'),
(11, 2023001011, 9, 'Halte Depan Sekolah', 'Tidak ada antar jemput khusus siswa', '2026-02-28 10:40:22'),
(12, 1111111, 4, 'dasdwa', 'dasdasd', '2026-04-01 22:20:53');

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id_admin` int(15) NOT NULL,
  `username` text NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`id_admin`, `username`, `password`) VALUES
(0, 'adit', 'e10adc3949ba59abbe56e057f20f883e'),
(2, 'admin', '21232f297a57a5a743894a0e4a801fc3'),
(10101, 'adityo', 'e10adc3949ba59abbe56e057f20f883e');

-- --------------------------------------------------------

--
-- Table structure for table `tb_aspirasi`
--

CREATE TABLE `tb_aspirasi` (
  `id_aspirasi` int(5) NOT NULL,
  `status` enum('menunggu','proses','selesai') NOT NULL,
  `id_pelaporan` int(5) NOT NULL,
  `feedback` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_aspirasi`
--

INSERT INTO `tb_aspirasi` (`id_aspirasi`, `status`, `id_pelaporan`, `feedback`) VALUES
(0, 'proses', 12, 'dasdawdsdaw'),
(5, 'selesai', 5, 'sadwadwadadawdas\'\''),
(7, 'selesai', 7, 'Jadwal pramuka telah diperbarui dan pembina baru telah ditunjuk'),
(8, 'proses', 8, 'Koordinasi dengan satpam sedang dilakukan'),
(9, 'menunggu', 9, ''),
(10, 'selesai', 10, 'Jadwal pelajaran telah direvisi oleh bagian kurikulum'),
(11, 'menunggu', 11, '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `id_kategori` int(5) NOT NULL,
  `ket_kategori` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kategori`
--

INSERT INTO `tb_kategori` (`id_kategori`, `ket_kategori`) VALUES
(4, 'ezkaliguler'),
(5, 'Kantin'),
(6, 'Perpustakaan'),
(7, 'Toilet'),
(8, 'Keamanan'),
(9, 'Transportasi'),
(10, 'Guru dan Staf'),
(11, 'Kurikulum'),
(12, 'Lainnya'),
(13, 'test'),
(14, 'y'),
(15, 'trtrtr'),
(17, 'test test'),
(18, 'dasdawdasdaw');

-- --------------------------------------------------------

--
-- Table structure for table `tb_siswa`
--

CREATE TABLE `tb_siswa` (
  `nis` int(10) NOT NULL,
  `kelas` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_siswa`
--

INSERT INTO `tb_siswa` (`nis`, `kelas`) VALUES
(3333, '333'),
(1111111, '111'),
(2023001001, 'X-A'),
(2023001002, 'X-A'),
(2023001003, 'X-B'),
(2023001004, 'X-B'),
(2023001005, 'XI-A'),
(2023001006, 'XI-A'),
(2023001007, 'XI-B'),
(2023001008, 'XI-B'),
(2023001009, 'XII-A'),
(2023001010, 'XII-A'),
(2023001011, 'XII-B'),
(2023001012, 'XII-B'),
(2147483647, '11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  ADD PRIMARY KEY (`id_pelaporan`),
  ADD KEY `nis` (`nis`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `tb_aspirasi`
--
ALTER TABLE `tb_aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`),
  ADD KEY `id_pelaporan` (`id_pelaporan`);

--
-- Indexes for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `tb_siswa`
--
ALTER TABLE `tb_siswa`
  ADD PRIMARY KEY (`nis`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  ADD CONSTRAINT `input_aspirasi_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `tb_kategori` (`id_kategori`),
  ADD CONSTRAINT `input_aspirasi_ibfk_2` FOREIGN KEY (`nis`) REFERENCES `tb_siswa` (`nis`);

--
-- Constraints for table `tb_aspirasi`
--
ALTER TABLE `tb_aspirasi`
  ADD CONSTRAINT `tb_aspirasi_ibfk_1` FOREIGN KEY (`id_pelaporan`) REFERENCES `input_aspirasi` (`id_pelaporan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
