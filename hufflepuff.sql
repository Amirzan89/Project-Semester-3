-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2023 at 12:54 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hufflepuff_testing`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_events`
--

CREATE TABLE `detail_events` (
  `id_detail` int(11) NOT NULL,
  `nama_event` varchar(45) NOT NULL,
  `deskripsi` varchar(4000) DEFAULT NULL,
  `tempat_event` varchar(2000) DEFAULT NULL,
  `tanggal_awal` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `link_pendaftaran` varchar(2000) DEFAULT NULL,
  `poster_event` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_events`
--

INSERT INTO `detail_events` (`id_detail`, `nama_event`, `deskripsi`, `tempat_event`, `tanggal_awal`, `tanggal_akhir`, `link_pendaftaran`, `poster_event`) VALUES
(48, 'perkumpulan para sepuh', 'perkumpulan untuk semua kalangan dan semua tingkatan dari yg masih pemula, menengah, hebat, sepuh awal, sepuh dari segala sepuh, leluhur dan leluhur dari segala leluhur', 'jakarta amerika serikat', '2023-12-17', '2023-12-20', 'sepuh.com', '/1000018386 (1).jpg'),
(49, '\"penyelam handal\"', '\"perkumpulan dimana semua sepuh dari berbagai tingkat mulai dari pemula, menengah, kelas atas, sepuh, master sepuh, sepuh dari sepuh dan leluhur sampai ada legenda yg merendah sampai inti bumi\"', '\"idudhdhdidjdhfhf\"', '0000-00-00', '0000-00-00', '\"sijdjdjdhdhdhdhd\"', '/1000018065.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id_event` int(11) NOT NULL,
  `nama_pengirim` varchar(30) NOT NULL,
  `status` enum('diajukan','proses','diterima','ditolak') NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `id_detail` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id_event`, `nama_pengirim`, `status`, `catatan`, `created_at`, `updated_at`, `id_detail`, `id_user`) VALUES
(40, 'Amirzan', 'diterima', NULL, '2023-12-08 20:19:27', '2023-12-08 20:19:27', 48, 2),
(41, 'amirzan fikri ', 'diajukan', NULL, '2023-12-08 20:44:57', '2023-12-08 20:44:57', 48, 2);

-- --------------------------------------------------------

--
-- Table structure for table `histori_nis`
--

CREATE TABLE `histori_nis` (
  `id_histori` int(11) NOT NULL,
  `nis` varchar(45) NOT NULL,
  `tahun` varchar(5) NOT NULL,
  `id_seniman` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_seniman`
--

CREATE TABLE `kategori_seniman` (
  `id_kategori_seniman` int(2) NOT NULL,
  `nama_kategori` varchar(45) NOT NULL,
  `singkatan_kategori` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_seniman`
--

INSERT INTO `kategori_seniman` (`id_kategori_seniman`, `nama_kategori`, `singkatan_kategori`) VALUES
(1, 'campursari', 'CAMP'),
(2, 'dalang', 'DLG'),
(3, 'jaranan', 'JKP'),
(4, 'karawitan', 'KRW'),
(5, 'mc', 'MC'),
(6, 'ludruk', 'LDR'),
(7, 'organisasi kesenian musik', 'OKM'),
(8, 'organisasi', 'ORG'),
(9, 'pramugari tayup', 'PRAM'),
(10, 'sanggar', 'SGR'),
(11, 'sinden', 'SIND'),
(12, 'vocalis', 'VOC'),
(13, 'waranggono', 'WAR'),
(14, 'barongsai', 'BAR'),
(15, 'ketoprak', 'KTP'),
(16, 'pataji', 'PTJ'),
(17, 'reog', 'REOG'),
(18, 'taman hiburan rakyat', 'THR'),
(19, 'pelawak', 'PLWK');

-- --------------------------------------------------------

--
-- Table structure for table `list_tempat`
--

CREATE TABLE `list_tempat` (
  `id_tempat` int(5) NOT NULL,
  `nama_tempat` varchar(50) NOT NULL,
  `alamat_tempat` varchar(50) NOT NULL,
  `deskripsi_tempat` varchar(500) NOT NULL,
  `pengelola` varchar(50) NOT NULL,
  `contact_person` varchar(15) NOT NULL,
  `foto_tempat` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `list_tempat`
--

INSERT INTO `list_tempat` (`id_tempat`, `nama_tempat`, `alamat_tempat`, `deskripsi_tempat`, `pengelola`, `contact_person`, `foto_tempat`) VALUES
(1, 'Museum Anjuk Ladang ', 'Jl. Gatot Subroto Kec. Nganjuk Kab. Nganjuk ', 'Museum Anjuk Ladang Terletak di kota Nganjuk, tepatnya sebelah timur Terminal Bus Kota Nganjuk, di dalamnya tersimpan benda dan cagar budaya pada zaman Hindu, Doho dan Majapahit yang terdapat di daerah Kabupaten Nganjuk. Disamping itu di simpan Prasasti Anjuk Ladang yang merupakan cikal bakal berdirinya Kabupaten Nganjuk.', 'wong terserah', '08414141', '/1.png'),
(2, 'Balai Budaya ', 'Mangundikaran, Kec. Nganjuk, Kab. Nganjuk', 'Gedung Balai Budaya Nganjuk adalah salah satu legenda bangunan bersejarah di Kabupaten Nganjuk. Gedung ini bisa digunakan untuk berbagai acara.', 'random', '0888515151', '/2.png'),
(3, 'Monumen Dr. Soetomo ', 'Sono, Ngepeh, Kec. Loceret Kab. Nganjuk', 'Monumen Dr. Soetomo Nganjuk yang menempati tanah seluas 3,5 ha ini merupakan tempat kelahiran Dr. Soetomo Secara keseluruhan kompleks bangunan ini terdiri dari patung Dr. Soetomo, Pendopo induk, yang terletak di belakang patung, dan bangunan pringgitan jumlahnya 2 buah masing-masing 6 x 12 m.', 'gabut', '08881515', '/3.png'),
(4, 'Air Terjun Sedudo', 'Jl. Sedudo Kec. Sawahan Kab. Nganjuk', 'Air Terjun Sedudo adalah sebuah air terjun dan objek wisata yang terletak di Desa Ngliman Kecamatan Sawahan, Kabupaten Nganjuk, Jawa Timur. Jaraknya sekitar 30 km arah selatan ibu kota kabupaten Nganjuk. Berada pada ketinggian 1.438 meter dpl, ketinggian air terjun ini sekitar 105 meter. Tempat wisata ini memiliki fasilitas yang cukup baik, dan jalur transportasi yang mudah diakses.', 'nvonvonvoa', '08885151', '/4.png'),
(5, 'Goa Margo Tresno ', 'Ngluyu, Kec. Ngluyu Kab. Nganjuk ', 'Goa Margo Tresno adalah salah satu obyek wisata di Jawa Timur yang terletak di Dusun Cabean, Desa Sugih Waras, Kecamatan Ngluyu, Kabupaten Nganjuk. Wisata Goa Margo Tresno Nganjuk adalah destinasi wisata yang ramai dengan wisatawan baik dari dalam maupun luar kota pada hari biasa maupun hari liburan dan sudah terkenal di Nganjuk dan sekitarnya.', 'vaiai', '08885115', '/5.png'),
(6, 'Air Terjun Roro Kuning', 'Nglarangan, Bajulan, Kec. Loceret Kab. Nganjuk', 'Air Terjun Roro Kuning adalah sebuah air terjun yang berada sekitar 27–30 km selatan kota Nganjuk, di ketinggian 600 m dpl dan memiliki tinggi antara 10–15 m. Air terjun ini mengalir dari tiga sumber di sekitar Gunung Wilis yang mengalir merambat di sela-sela bebatuan padas di bawah pepohonan hutan pinus.', 'avavava', '08855155', '/6.png'),
(19, 'tanahh', 'jalan pegunungan', 'aaonna\r\n', 'admminnn', '088999151', ''),
(20, 'taavava', 'jalan pegunungan', 'avavavvavav', 'admminnn', '088418441441', '/20.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `perpanjangan`
--

CREATE TABLE `perpanjangan` (
  `id_perpanjangan` int(11) NOT NULL,
  `nik` varchar(500) NOT NULL,
  `ktp_seniman` text NOT NULL,
  `pass_foto` text NOT NULL,
  `surat_keterangan` text NOT NULL,
  `tgl_pembuatan` date NOT NULL,
  `kode_verifikasi` varchar(45) DEFAULT NULL,
  `status` enum('diajukan','proses','diterima','ditolak') NOT NULL,
  `catatan` text DEFAULT NULL,
  `id_seniman` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perpanjangan`
--

INSERT INTO `perpanjangan` (`id_perpanjangan`, `nik`, `ktp_seniman`, `pass_foto`, `surat_keterangan`, `tgl_pembuatan`, `kode_verifikasi`, `status`, `catatan`, `id_seniman`, `id_user`) VALUES
(8, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(1).jpg', '/download(1).jpeg', '/11. Recursive 1(1).pdf', '2023-12-15', '', 'diajukan', NULL, 90, 2),
(9, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(2).jpg', '/download(2).jpeg', '/11. Recursive 1(2).pdf', '2023-05-10', '', 'diajukan', NULL, 91, 2),
(10, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(3).jpg', '/download(3).jpeg', '/11. Recursive 1(3).pdf', '2023-10-10', '', 'diajukan', NULL, 92, 2),
(11, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(4).jpg', '/download(4).jpeg', '/11. Recursive 1(4).pdf', '2023-07-10', '', 'diajukan', NULL, 93, 2),
(12, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(5).jpg', '/download(5).jpeg', '/11. Recursive 1(5).pdf', '2023-04-10', '', 'diajukan', NULL, 94, 2),
(13, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(6).jpg', '/download(6).jpeg', '/11. Recursive 1(6).pdf', '2023-10-10', '', 'diajukan', NULL, 95, 2),
(15, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(8).jpg', '/download(8).jpeg', '/11. Recursive 1(8).pdf', '2023-01-12', '', 'diajukan', NULL, 97, 2),
(16, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(9).jpg', '/download(9).jpeg', '/11. Recursive 1(9).pdf', '2023-10-10', '', 'diajukan', NULL, 98, 2),
(17, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(10).jpg', '/download(10).jpeg', '/11. Recursive 1(10).pdf', '2023-10-10', '', 'diajukan', NULL, 99, 2),
(18, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(11).jpg', '/download(11).jpeg', '/11. Recursive 1(11).pdf', '2023-10-10', '', 'proses', NULL, 100, 2),
(19, '41414515', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6(12).jpg', '/download(12).jpeg', '/11. Recursive 1(12).pdf', '2023-10-10', '', 'proses', NULL, 101, 2);

-- --------------------------------------------------------

--
-- Table structure for table `refresh_token`
--

CREATE TABLE `refresh_token` (
  `id_token` int(10) NOT NULL,
  `email` varchar(45) NOT NULL,
  `token` longtext NOT NULL,
  `device` enum('website','mobile') NOT NULL,
  `number` int(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refresh_token`
--

INSERT INTO `refresh_token` (`id_token`, `email`, `token`, `device`, `number`, `created_at`, `updated_at`, `id_user`) VALUES
(337, 'SuperAdmin@gmail.com', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZF91c2VyIjoxLCJuYW1hX2xlbmdrYXAiOiJzdXBlciBhZG1pbiIsIm5vX3RlbHBvbiI6IjA4ODExMjIyMzMiLCJqZW5pc19rZWxhbWluIjoibGFraS1sYWtpIiwidGFuZ2dhbF9sYWhpciI6IjIwMjMtMTAtMDciLCJ0ZW1wYXRfbGFoaXIiOiJwbGFuZXQganVwaXRlciIsImVtYWlsIjoiU3VwZXJBZG1pbkBnbWFpbC5jb20iLCJyb2xlIjoic3VwZXIgYWRtaW4iLCJmb3RvIjoiXC8xLmpwZWciLCJudW1iZXIiOjMsImV4cCI6MTcwMjEyNzY3OX0.G23tXfMUwhcW_GyEcJHrvBkWHcoaT__9q8OYkAWyTag', 'website', 1, '2023-12-08 13:14:39', '2023-12-08 13:14:39', 1),
(338, 'SuperAdmin@gmail.com', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZF91c2VyIjoxLCJuYW1hX2xlbmdrYXAiOiJzdXBlciBhZG1pbiIsIm5vX3RlbHBvbiI6IjA4ODExMjIyMzMiLCJqZW5pc19rZWxhbWluIjoibGFraS1sYWtpIiwidGFuZ2dhbF9sYWhpciI6IjIwMjMtMTAtMDciLCJ0ZW1wYXRfbGFoaXIiOiJwbGFuZXQganVwaXRlciIsImVtYWlsIjoiU3VwZXJBZG1pbkBnbWFpbC5jb20iLCJyb2xlIjoic3VwZXIgYWRtaW4iLCJmb3RvIjoiXC8xLmpwZWciLCJudW1iZXIiOjMsImV4cCI6MTcwMjEzMTUwMX0.TUMfiU9nOJNMSRs9pb6hDpzHZyhQPFRhyskaNbVn5gQ', 'website', 2, '2023-12-08 14:18:21', '2023-12-08 14:18:21', 1),
(339, 'SuperAdmin@gmail.com', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZF91c2VyIjoxLCJuYW1hX2xlbmdrYXAiOiJzdXBlciBhZG1pbiIsIm5vX3RlbHBvbiI6IjA4ODExMjIyMzMiLCJqZW5pc19rZWxhbWluIjoibGFraS1sYWtpIiwidGFuZ2dhbF9sYWhpciI6IjIwMjMtMTAtMDciLCJ0ZW1wYXRfbGFoaXIiOiJwbGFuZXQganVwaXRlciIsImVtYWlsIjoiU3VwZXJBZG1pbkBnbWFpbC5jb20iLCJyb2xlIjoic3VwZXIgYWRtaW4iLCJmb3RvIjoiXC8xLmpwZWciLCJudW1iZXIiOjMsImV4cCI6MTcwMjE4MTg5MH0.a890t6OB-ZKazVx64oNepXAjrmVPGsbvdiNYS6QIMVs', 'website', 3, '2023-12-09 04:18:10', '2023-12-09 04:18:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `seniman`
--

CREATE TABLE `seniman` (
  `id_seniman` int(11) NOT NULL,
  `nik` varchar(500) NOT NULL,
  `nomor_induk` varchar(20) DEFAULT NULL,
  `nama_seniman` varchar(30) NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') NOT NULL,
  `kecamatan` enum('bagor','baron','berbek','gondang','jatikalen','kertosono','lengkong','loceret','nganjuk','ngetos','ngluyu','ngronggot','pace','patianrowo','prambon','rejoso','sawahan','sukomoro','tanjunganom','wilangan') NOT NULL,
  `tempat_lahir` varchar(30) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat_seniman` varchar(50) NOT NULL,
  `no_telpon` varchar(15) NOT NULL,
  `nama_organisasi` varchar(50) DEFAULT NULL,
  `jumlah_anggota` int(5) DEFAULT NULL,
  `ktp_seniman` text NOT NULL,
  `pass_foto` text NOT NULL,
  `surat_keterangan` text NOT NULL,
  `tgl_pembuatan` date NOT NULL,
  `tgl_berlaku` date NOT NULL,
  `kode_verifikasi` varchar(45) DEFAULT NULL,
  `status` enum('diajukan','proses','diterima','ditolak') NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `id_kategori_seniman` int(2) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seniman`
--

INSERT INTO `seniman` (`id_seniman`, `nik`, `nomor_induk`, `nama_seniman`, `jenis_kelamin`, `kecamatan`, `tempat_lahir`, `tanggal_lahir`, `alamat_seniman`, `no_telpon`, `nama_organisasi`, `jumlah_anggota`, `ktp_seniman`, `pass_foto`, `surat_keterangan`, `tgl_pembuatan`, `tgl_berlaku`, `kode_verifikasi`, `status`, `catatan`, `created_at`, `updated_at`, `id_kategori_seniman`, `id_user`) VALUES
(88, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890.jpeg', '/11. Recursive 2.pdf', '2023-11-16', '2023-12-31', NULL, 'ditolak', 'terserah', '2023-11-16 00:00:00', '2023-11-16 00:00:00', 2, 2),
(90, '41414515', 'DLG/001/411.302/2023', 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(2).jpeg', '/11. Recursive 2(2).pdf', '2023-11-16', '2023-12-31', NULL, 'diterima', NULL, '2023-11-16 00:00:00', '2023-11-16 00:00:00', 2, 2),
(91, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(3).jpeg', '/11. Recursive 2(3).pdf', '2023-11-24', '2023-12-31', '', 'proses', NULL, '2023-11-24 00:00:00', '2023-11-24 00:00:00', 2, 2),
(92, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(4).jpeg', '/11. Recursive 2(4).pdf', '2023-11-24', '2023-12-31', NULL, 'diajukan', NULL, '2023-11-24 00:00:00', '2023-11-24 00:00:00', 2, 2),
(93, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(5).jpeg', '/11. Recursive 2(5).pdf', '2023-11-24', '2023-12-31', '', 'proses', NULL, '2023-11-24 00:00:00', '2023-11-24 00:00:00', 2, 2),
(94, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(6).jpeg', '/11. Recursive 2(6).pdf', '2023-11-24', '2023-12-31', NULL, 'diajukan', NULL, '2023-11-24 00:00:00', '2023-11-24 00:00:00', 2, 2),
(95, '41414515', 'DLG/003/411.302/2023', 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(7).jpeg', '/11. Recursive 2(7).pdf', '2023-11-24', '2023-12-31', '656015b19dc', 'diterima', '', '2023-11-24 00:00:00', '2023-11-24 00:00:00', 2, 2),
(97, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(9).jpeg', '/11. Recursive 2(9).pdf', '2023-11-26', '2023-12-31', NULL, 'diajukan', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(98, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(10).jpeg', '/11. Recursive 2(10).pdf', '2023-11-26', '2023-12-31', NULL, 'diajukan', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(99, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(11).jpeg', '/11. Recursive 2(11).pdf', '2023-11-26', '2023-12-31', NULL, 'diajukan', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(100, '41414515', 'DLG/005/411.302/2023', 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(12).jpeg', '/11. Recursive 2(12).pdf', '2023-11-26', '2023-12-31', '2508336857', 'diterima', '', '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(101, '41414515', 'DLG/004/411.302/2023', 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(13).jpeg', '/11. Recursive 2(13).pdf', '2023-11-26', '2023-12-31', '799138142', 'diterima', '', '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(102, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(14).jpeg', '/11. Recursive 2(14).pdf', '2023-11-26', '2023-12-31', '', 'ditolak', 'akaokowokawaw', '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(103, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(15).jpeg', '/11. Recursive 2(15).pdf', '2023-11-26', '2023-12-31', '', 'proses', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(104, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(16).jpeg', '/11. Recursive 2(16).pdf', '2023-11-26', '2023-12-31', '', 'proses', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(105, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(17).jpeg', '/11. Recursive 2(17).pdf', '2023-11-26', '2023-12-31', NULL, 'diajukan', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(106, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(18).jpeg', '/11. Recursive 2(18).pdf', '2023-11-26', '2023-12-31', NULL, 'diajukan', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(107, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(19).jpeg', '/11. Recursive 2(19).pdf', '2023-11-26', '2023-12-31', NULL, 'diajukan', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(108, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(20).jpeg', '/11. Recursive 2(20).pdf', '2023-11-26', '2023-12-31', NULL, 'diajukan', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(109, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(21).jpeg', '/11. Recursive 2(21).pdf', '2023-11-26', '2023-12-31', '', 'proses', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(110, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(22).jpeg', '/11. Recursive 2(22).pdf', '2023-11-26', '2023-12-31', '', 'proses', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(111, '41414515', NULL, 'tserah', 'laki-laki', 'nganjuk', 'planet nganjuk', '2022-11-20', 'planet nganjuk', '08884141414', 'teersavah', 13144115, '/download (1).jpeg', '/Tesla_circa_1890(23).jpeg', '/11. Recursive 2(23).pdf', '2023-11-26', '2023-12-31', '', 'proses', NULL, '2023-11-26 00:00:00', '2023-11-26 00:00:00', 2, 2),
(112, '41414', NULL, 'ksdhhdjdidijdhdhhd', 'perempuan', 'ngronggot', 'Osidjfjdjd', '2023-12-02', 'jsjdhdhdudidud', '0845566464', '-', 1, 'uploads/seniman/ktp_seniman/1000018014.jpg', 'uploads/seniman/pass_foto/1000018016.jpg', 'uploads/seniman/surat_keterangan/15. Graph 2.pdf', '2023-12-04', '2024-12-31', '', 'proses', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 8, 2),
(116, '2147483647', 'JKP/001/411.302/2023', 'karepku lah', 'perempuan', 'baron', 'jupiter', '2023-11-30', 'faomodmodmavaovnondovav', '085151513253251', '', 1, '/Baluran(3).jpg', '/10-Pemandangan-Alam-Paling-Indah-yang-Wajib-Kalian-Datangi.jpg', '/11. Recursive 1.pdf', '2023-12-05', '2023-12-31', NULL, 'diterima', NULL, '2023-12-05 00:00:00', '2023-12-05 00:00:00', 3, 1),
(117, 'MDk1MTUxOTg1NTEyNTMyNTEyNTI1MQ==', 'MC/001/411.302/2023', 'karepku lah', 'perempuan', 'ngetos', 'makhluk isso', '2023-12-06', 'vvsvsvsdfvsfvsfv', '085151513253999', 'randnoom', 5, '/Hari-Penemu_-Mengenal-Nicola-Tesla-dan-Temuannya.womanindonesia.jpg', '/_106804932_b820b700-66c0-4874-84c6-783e271f76e6.jpg', '/11. Recursive 1(1).pdf', '2023-12-08', '2023-12-31', NULL, 'diterima', NULL, '2023-12-08 00:00:00', '2023-12-08 00:00:00', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sewa_tempat`
--

CREATE TABLE `sewa_tempat` (
  `id_sewa` int(5) NOT NULL,
  `nik_sewa` varchar(500) NOT NULL,
  `nama_peminjam` varchar(30) NOT NULL,
  `nama_tempat` varchar(50) DEFAULT NULL,
  `deskripsi_sewa_tempat` varchar(100) DEFAULT NULL,
  `nama_kegiatan_sewa` varchar(50) DEFAULT NULL,
  `jumlah_peserta` int(10) DEFAULT NULL,
  `instansi` varchar(50) DEFAULT NULL,
  `surat_ket_sewa` text DEFAULT NULL,
  `tgl_awal_peminjaman` datetime DEFAULT NULL,
  `tgl_akhir_peminjaman` datetime DEFAULT NULL,
  `kode_pinjam` varchar(45) DEFAULT NULL,
  `status` enum('diajukan','proses','diterima','ditolak') NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `id_tempat` int(5) NOT NULL,
  `id_user` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sewa_tempat`
--

INSERT INTO `sewa_tempat` (`id_sewa`, `nik_sewa`, `nama_peminjam`, `nama_tempat`, `deskripsi_sewa_tempat`, `nama_kegiatan_sewa`, `jumlah_peserta`, `instansi`, `surat_ket_sewa`, `tgl_awal_peminjaman`, `tgl_akhir_peminjaman`, `kode_pinjam`, `status`, `catatan`, `created_at`, `updated_at`, `id_tempat`, `id_user`) VALUES
(20, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1.pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-16 22:04:35', '2023-11-16 22:04:35', 2, 2),
(38, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-26 20:17:20', '2023-11-26 20:17:20', 2, 2),
(39, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-26 20:17:23', '2023-11-26 20:17:23', 2, 2),
(40, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-26 20:17:24', '2023-11-26 20:17:24', 2, 2),
(41, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-26 20:17:25', '2023-11-26 20:17:25', 2, 2),
(42, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-26 20:17:25', '2023-11-26 20:17:25', 2, 2),
(43, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-26 20:17:29', '2023-11-26 20:17:29', 2, 2),
(44, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-26 20:17:30', '2023-11-26 20:17:30', 2, 2),
(45, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-26 20:20:18', '2023-11-26 20:20:18', 2, 2),
(46, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', NULL, 'diajukan', NULL, '2023-11-26 20:20:19', '2023-11-26 20:20:19', 2, 2),
(47, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', '', 'ditolak', '0', '2023-11-26 20:20:20', '2023-11-26 20:20:20', 2, 2),
(48, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', '656ee60ea4', 'diterima', NULL, '2023-11-26 20:20:22', '2023-11-26 20:20:22', 2, 2),
(49, '2147483647', 'joshhh', 'amerika', 'menyewa tempat karepku lah', 'olahraga sepeda', 100, 'jerman', '/11. Recursive 1(1).pdf', '2023-12-14 06:01:01', '2023-12-30 12:10:11', '6566b6b8d8', 'diterima', NULL, '2023-11-26 20:20:23', '2023-11-26 20:20:23', 2, 2),
(50, '2147483647', 'randododomm', 'Museum Anjuk Ladang ', 'kfjgjgifjfjgjgjjfififjfiffifjfjcjcjcjididifjfjfjfiififhcchcfufufuufuchchchvhchchucucucuc', 'hdhfhdjdjhfbccnc', 4, 'ggffghgff', 'uploads/pinjam/1000018257.jpg', '2023-12-15 01:30:00', '2023-12-30 06:00:00', '', 'proses', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 32);

-- --------------------------------------------------------

--
-- Table structure for table `surat_advis`
--

CREATE TABLE `surat_advis` (
  `id_advis` int(5) NOT NULL,
  `nomor_induk` varchar(20) NOT NULL,
  `nama_advis` varchar(30) NOT NULL,
  `alamat_advis` varchar(100) NOT NULL,
  `deskripsi_advis` varchar(100) DEFAULT NULL,
  `tgl_advis` date NOT NULL,
  `tempat_advis` varchar(30) NOT NULL,
  `kode_verifikasi` varchar(45) DEFAULT NULL,
  `status` enum('diajukan','proses','diterima','ditolak') DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_seniman` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_advis`
--

INSERT INTO `surat_advis` (`id_advis`, `nomor_induk`, `nama_advis`, `alamat_advis`, `deskripsi_advis`, `tgl_advis`, `tempat_advis`, `kode_verifikasi`, `status`, `catatan`, `created_at`, `updated_at`, `id_user`, `id_seniman`) VALUES
(23, 'DLG/001/411.302/2023', 'aseppp', 'nganjukinonesioa', 'wayanggg', '2024-11-20', 'planet nganjuk', '656011f4d9', 'diterima', NULL, '2023-11-24 10:00:46', '2023-11-24 10:00:46', 2, 90),
(24, 'DLG/001/411.302/2023', 'aseppp', 'nganjukinonesioa', 'wayanggg', '2024-11-20', 'planet nganjuk', '', 'proses', NULL, '2023-11-24 10:00:47', '2023-11-24 10:00:47', 2, 90),
(25, 'DLG/001/411.302/2023', 'aseppp', 'nganjukinonesioa', 'wayanggg', '2024-11-20', 'planet nganjuk', NULL, 'diajukan', NULL, '2023-11-24 10:00:49', '2023-11-24 10:00:49', 2, 90),
(26, 'DLG/001/411.302/2023', 'aseppp', 'nganjukinonesioa', 'wayanggg', '2024-11-20', 'planet nganjuk', '656011ecba', 'diterima', NULL, '2023-11-24 10:00:49', '2023-11-24 10:00:49', 2, 90);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `no_telpon` varchar(15) NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `tempat_lahir` varchar(45) NOT NULL,
  `role` enum('super admin','admin event','admin seniman','admin tempat','masyarakat') NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(191) NOT NULL,
  `foto` varchar(45) DEFAULT NULL,
  `verifikasi` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `no_telpon`, `jenis_kelamin`, `tanggal_lahir`, `tempat_lahir`, `role`, `email`, `password`, `foto`, `verifikasi`) VALUES
(1, 'super admin', '0881122233', 'laki-laki', '2023-10-07', 'planet jupiter', 'super admin', 'SuperAdmin@gmail.com', '$2y$10$M1fEjUm7I3i7z8bMOSzYm.9WzkGl9rHV8Av5soEhKgXbkkvt8VbO2', '/1.jpeg', 1),
(2, 'amirzan fikri ', '0881122233', 'laki-laki', '2023-11-01', 'planet matahari', 'masyarakat', 'amirzanfikri5@gmail.com', '$2y$10$k0TaeSpsz6uGjWYDnoyi2OY9uv3qpmS8PITqgzr5nAQHfMu2z9dd.', '/45.jpeg', 1),
(46, 'admin seniman', '0888616161', 'laki-laki', '2023-11-04', 'jakarta bumi indonesia', 'admin seniman', 'AdminSeniman@gmail.com', '$2y$10$6HBxJHSsXi8BQU6BO5aWOOHMz4W900W/EYaTzX9dL486seaeSxo.6', '/46.jpeg', 1),
(47, 'admin tempat', '0888616161', 'perempuan', '2023-11-18', 'planet mars', 'admin tempat', 'AdminTempat@gmail.com', '$2y$10$euG7yuF809L7TkwJ2sSGeekfG1rI1WFu9O9c8ocwbgDUaR5jYMKqC', '/47.jpg', 1),
(48, 'admin event', '08881661', 'perempuan', '2023-11-04', 'planet jupiter', 'admin event', 'AdminEvent@gmail.com', '$2y$10$5Nc3eDjWLgcDLvGs68a88ummOXRpONff3n4hXPjt/QpbjL4D2CkGu', NULL, 1),
(52, 'AdminTesting', '088418441441', 'perempuan', '2023-10-31', 'jakarta pulau sumatra', 'super admin', 'AdminTest@gmail.com', '$2y$10$mqjaS23H8EgOfx1dwKmO4eJhVw3Fers7yJOvzHfOTJss5cDgUkVOy', '/52.jpeg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi`
--

CREATE TABLE `verifikasi` (
  `id_verifikasi` int(10) UNSIGNED NOT NULL,
  `email` varchar(45) NOT NULL,
  `kode_otp` int(6) NOT NULL,
  `link` varchar(50) NOT NULL,
  `deskripsi` enum('password','email') NOT NULL,
  `send` int(2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verifikasi`
--

INSERT INTO `verifikasi` (`id_verifikasi`, `email`, `kode_otp`, `link`, `deskripsi`, `send`, `created_at`, `updated_at`, `id_user`) VALUES
(18, 'amirzanfikri5@gmail.com', 268645, 'd210a056afd65edfcb25a21c8fb625b177c7e1f2d249730464', 'email', 0, '2023-11-16 22:24:11', '2023-11-16 22:24:11', 2),
(19, 'amirzanfikri5@gmail.com', 960690, '46b7e244fe40721e01f8cb3e9b5ae8fab04e257f5f4c841672', 'password', 0, '2023-11-16 22:24:52', '2023-11-16 22:24:52', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_events`
--
ALTER TABLE `detail_events`
  ADD PRIMARY KEY (`id_detail`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `eventFK` (`id_user`),
  ADD KEY `detailFK` (`id_detail`);

--
-- Indexes for table `histori_nis`
--
ALTER TABLE `histori_nis`
  ADD PRIMARY KEY (`id_histori`),
  ADD KEY `senimanHFK` (`id_seniman`);

--
-- Indexes for table `kategori_seniman`
--
ALTER TABLE `kategori_seniman`
  ADD PRIMARY KEY (`id_kategori_seniman`);

--
-- Indexes for table `list_tempat`
--
ALTER TABLE `list_tempat`
  ADD PRIMARY KEY (`id_tempat`);

--
-- Indexes for table `perpanjangan`
--
ALTER TABLE `perpanjangan`
  ADD PRIMARY KEY (`id_perpanjangan`),
  ADD KEY `senimanPFK` (`id_seniman`),
  ADD KEY `userPFK` (`id_user`);

--
-- Indexes for table `refresh_token`
--
ALTER TABLE `refresh_token`
  ADD PRIMARY KEY (`id_token`),
  ADD KEY `tokenFK` (`id_user`);

--
-- Indexes for table `seniman`
--
ALTER TABLE `seniman`
  ADD PRIMARY KEY (`id_seniman`),
  ADD KEY `senimanFK` (`id_user`),
  ADD KEY `kategoriSFK` (`id_kategori_seniman`);

--
-- Indexes for table `sewa_tempat`
--
ALTER TABLE `sewa_tempat`
  ADD PRIMARY KEY (`id_sewa`);

--
-- Indexes for table `surat_advis`
--
ALTER TABLE `surat_advis`
  ADD PRIMARY KEY (`id_advis`),
  ADD KEY `advisFK` (`id_user`),
  ADD KEY `senimanSAFK` (`id_seniman`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `verifikasi`
--
ALTER TABLE `verifikasi`
  ADD PRIMARY KEY (`id_verifikasi`),
  ADD KEY `verifyfk` (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_events`
--
ALTER TABLE `detail_events`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `histori_nis`
--
ALTER TABLE `histori_nis`
  MODIFY `id_histori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_seniman`
--
ALTER TABLE `kategori_seniman`
  MODIFY `id_kategori_seniman` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `list_tempat`
--
ALTER TABLE `list_tempat`
  MODIFY `id_tempat` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `perpanjangan`
--
ALTER TABLE `perpanjangan`
  MODIFY `id_perpanjangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `refresh_token`
--
ALTER TABLE `refresh_token`
  MODIFY `id_token` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=340;

--
-- AUTO_INCREMENT for table `seniman`
--
ALTER TABLE `seniman`
  MODIFY `id_seniman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `sewa_tempat`
--
ALTER TABLE `sewa_tempat`
  MODIFY `id_sewa` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `surat_advis`
--
ALTER TABLE `surat_advis`
  MODIFY `id_advis` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `verifikasi`
--
ALTER TABLE `verifikasi`
  MODIFY `id_verifikasi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `detailFK` FOREIGN KEY (`id_detail`) REFERENCES `detail_events` (`id_detail`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eventFK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `histori_nis`
--
ALTER TABLE `histori_nis`
  ADD CONSTRAINT `senimanHFK` FOREIGN KEY (`id_seniman`) REFERENCES `seniman` (`id_seniman`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `perpanjangan`
--
ALTER TABLE `perpanjangan`
  ADD CONSTRAINT `senimanPFK` FOREIGN KEY (`id_seniman`) REFERENCES `seniman` (`id_seniman`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userPFK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `refresh_token`
--
ALTER TABLE `refresh_token`
  ADD CONSTRAINT `tokenFK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seniman`
--
ALTER TABLE `seniman`
  ADD CONSTRAINT `kategoriFK` FOREIGN KEY (`id_kategori_seniman`) REFERENCES `kategori_seniman` (`id_kategori_seniman`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `senimanFK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `surat_advis`
--
ALTER TABLE `surat_advis`
  ADD CONSTRAINT `advisFK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `senimanSAFK` FOREIGN KEY (`id_seniman`) REFERENCES `seniman` (`id_seniman`);

--
-- Constraints for table `verifikasi`
--
ALTER TABLE `verifikasi`
  ADD CONSTRAINT `verifyfk` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
