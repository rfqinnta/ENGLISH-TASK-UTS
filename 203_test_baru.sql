-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2026 at 12:54 PM
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
-- Database: `203_test_baru`
--

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_daftar` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_ukm` int(11) DEFAULT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_daftar`, `id_user`, `id_ukm`, `no_hp`, `alasan`, `status`) VALUES
(21, 3, 9, '', '', 'ditolak'),
(22, 2, 8, '', '', ''),
(23, 2, 10, '', '', ''),
(24, 2, 1, '083189087838', 'SAY ANGBAND', ''),
(25, 2, 6, '081284921', 'SDKJSAVJISS', '');

-- --------------------------------------------------------

--
-- Table structure for table `ukm`
--

CREATE TABLE `ukm` (
  `id_ukm` int(11) NOT NULL,
  `nama_ukm` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `kuota` int(11) NOT NULL,
  `jadwal_open` date DEFAULT NULL,
  `jadwal_close` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ukm`
--

INSERT INTO `ukm` (`id_ukm`, `nama_ukm`, `kategori`, `kuota`, `jadwal_open`, `jadwal_close`) VALUES
(1, 'UKM Seni Musik UAD', 'Seni', 15, NULL, NULL),
(2, 'UKM Bola Basket UAD', 'Olahraga', 25, NULL, NULL),
(3, 'UKM Seni Tari UAD', 'Seni', 20, NULL, NULL),
(4, 'UKM Kopma UAD', 'Minat Bakat', 50, NULL, NULL),
(5, 'UKM Taekwondo UAD', 'Bela Diri', 30, NULL, NULL),
(6, 'UKM Karate UAD', 'Bela Diri', 30, NULL, NULL),
(7, 'UKM Lensa UAD', 'Fotografi', 20, NULL, NULL),
(8, 'UKM Poros UAD', 'Jurnalistik', 15, NULL, NULL),
(9, 'UKM Badminton UAD', 'Olahraga', 25, NULL, NULL),
(10, 'UKM Sepakbola UAD', 'Olahraga', 40, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `prodi` varchar(50) DEFAULT NULL,
  `fakultas` varchar(50) DEFAULT NULL,
  `angkatan` varchar(10) DEFAULT NULL,
  `role` enum('mahasiswa','admin') DEFAULT 'mahasiswa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nim`, `nama`, `email`, `password`, `prodi`, `fakultas`, `angkatan`, `role`) VALUES
(1, '202100111', 'Dito', 'rifqi@mail.com', '123', 'Infor', 'FTI', '2099', 'mahasiswa'),
(2, '2300018203', 'SHAKBKASJB', 'admin@mail.com', 'admin123', 'Infor', 'FTI', '2021', 'admin'),
(3, '20210014', 'Alangkah', 'jo@mail.com', '123', 'Infor', 'FTI', '2222', 'mahasiswa'),
(5, '1111111111', 'Dito', 'dito@mail.com', '123', 'Infor', 'FTI', '2023', 'mahasiswa'),
(6, '2300018001', 'Muhammad Rifqi Ananta', 'rfkynntaa@gmail.com', '123', NULL, NULL, NULL, 'mahasiswa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_daftar`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_ukm` (`id_ukm`);

--
-- Indexes for table `ukm`
--
ALTER TABLE `ukm`
  ADD PRIMARY KEY (`id_ukm`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_daftar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `ukm`
--
ALTER TABLE `ukm`
  MODIFY `id_ukm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `pendaftaran_ibfk_2` FOREIGN KEY (`id_ukm`) REFERENCES `ukm` (`id_ukm`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
