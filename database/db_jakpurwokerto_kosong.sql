-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Feb 2020 pada 19.01
-- Versi server: 10.1.38-MariaDB
-- Versi PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_jakpurwokerto`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `default_token`
--

CREATE TABLE `default_token` (
  `tokenId` int(11) NOT NULL,
  `token` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `default_token`
--

INSERT INTO `default_token` (`tokenId`, `token`) VALUES
(1, 'cobatoken'),
(2, 'p8>)DU)VCX');

-- --------------------------------------------------------

--
-- Struktur dari tabel `default_user`
--

CREATE TABLE `default_user` (
  `userId` int(11) NOT NULL,
  `realname` varchar(100) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `default_user`
--

INSERT INTO `default_user` (`userId`, `realname`, `username`, `password`, `foto`) VALUES
(1, 'Super Admin', 'superadmin', '17c4520f6cfd1ab53d8745e84681eb49', 'default.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jakpwt_anggota`
--

CREATE TABLE `jakpwt_anggota` (
  `noKta` varchar(100) NOT NULL,
  `agtIdWilayah` int(20) DEFAULT NULL,
  `agtNama` varchar(250) DEFAULT NULL,
  `agtNmPendek` varchar(20) DEFAULT NULL,
  `agtTmptLahir` varchar(500) DEFAULT NULL,
  `agtTglLahir` date DEFAULT NULL,
  `agtJnsKelamin` enum('L','P','N') DEFAULT NULL,
  `agtUmur` int(20) DEFAULT NULL,
  `agtIdPendidikan` int(20) DEFAULT NULL,
  `agtIdPekerjaan` int(20) DEFAULT NULL,
  `agtKelurahan` varchar(250) DEFAULT NULL,
  `agtKecamatan` varchar(250) DEFAULT NULL,
  `agtAlamatJalan` text,
  `agtKdPos` varchar(5) DEFAULT NULL,
  `agtNoTelp` varchar(13) DEFAULT NULL,
  `agtEmail` varchar(250) DEFAULT NULL,
  `agtUkrnKaos` enum('S','M','L','XL','XXL','XXXL') DEFAULT NULL,
  `agtFoto` varchar(100) NOT NULL DEFAULT 'default.jpg',
  `agtBrlkDari` varchar(100) DEFAULT NULL,
  `agtBrlkSampai` varchar(100) DEFAULT NULL,
  `agtTglInsert` date DEFAULT NULL,
  `agtLastUpdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jakpwt_kas`
--

CREATE TABLE `jakpwt_kas` (
  `kasId` int(11) NOT NULL,
  `kasWilId` int(20) DEFAULT NULL,
  `kasTanggal` date DEFAULT NULL,
  `kasMasukUraian` text,
  `kasMasuk` varchar(100) DEFAULT NULL,
  `kasKeluarUraian` text,
  `kasKeluar` varchar(100) DEFAULT NULL,
  `kasSaldo` varchar(100) DEFAULT NULL,
  `kasLastUpdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jakpwt_ref_agama`
--

CREATE TABLE `jakpwt_ref_agama` (
  `agmIdAgama` int(20) NOT NULL,
  `agmNama` varchar(500) DEFAULT NULL,
  `agmLastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `jakpwt_ref_agama`
--

INSERT INTO `jakpwt_ref_agama` (`agmIdAgama`, `agmNama`, `agmLastUpdate`) VALUES
(1, 'Islam', '2020-02-14 17:46:29'),
(2, 'Kristen', '2020-02-14 17:47:08'),
(3, 'Katholik', '2020-02-14 17:47:46'),
(4, 'Hindu', '2020-02-14 17:48:01'),
(5, 'Budha', '2020-02-14 17:48:12'),
(6, 'Konghucu', '2020-02-14 17:48:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jakpwt_ref_pekerjaan`
--

CREATE TABLE `jakpwt_ref_pekerjaan` (
  `pkjIdPekerjaan` int(11) NOT NULL,
  `pkjNama` varchar(500) DEFAULT NULL,
  `pkjLastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `jakpwt_ref_pekerjaan`
--

INSERT INTO `jakpwt_ref_pekerjaan` (`pkjIdPekerjaan`, `pkjNama`, `pkjLastUpdate`) VALUES
(1, 'Petani / Pekebun', '2018-06-06 11:57:09'),
(2, 'Tentara Nasional Indonesia', '2018-06-02 05:37:27'),
(4, 'Pegawai Negeri Sipil', '2018-06-02 05:19:50'),
(5, 'Karyawan Swasta', '2017-09-12 07:49:49'),
(6, 'Wirausaha', '2017-09-12 07:50:00'),
(7, 'Buruh Migran (TKI)', '2017-09-12 07:50:15'),
(8, 'Pedagang', '2017-09-12 07:50:24'),
(9, 'Buruh Harian Lepas', '2017-09-18 14:41:59'),
(11, 'Guru', '2017-09-12 07:51:10'),
(12, 'Kepala Desa', '2017-09-12 07:51:36'),
(13, 'Perangkat Desa', '2017-09-12 07:51:44'),
(14, 'Bupati', '2017-09-12 07:51:53'),
(15, 'Anggota DPRD Kabupaten / Kota', '2018-06-06 11:43:16'),
(17, 'Anggota DPR RI', '2017-09-12 07:52:28'),
(18, 'Anggota DPD', '2018-06-06 11:42:44'),
(19, 'Anggota DPRD Provinsi', '2017-09-12 07:52:51'),
(20, 'Mengurus Rumah Tangga', '2017-09-12 07:53:16'),
(21, 'Belum/ Tidak Bekerja', '2017-09-18 14:23:51'),
(22, 'Pelajar/ Mahasiswa', '2017-09-18 14:24:15'),
(24, 'Dokter', '2017-09-12 07:54:00'),
(25, 'Pengacara', '2017-09-12 07:54:09'),
(26, 'Menteri', '2017-09-12 07:54:23'),
(27, 'Presiden', '2017-09-12 07:54:31'),
(29, 'Kepolisian RI', '2018-06-02 05:47:03'),
(30, 'Pensiunan', '2017-09-18 14:25:09'),
(31, 'Perdagangan', '2017-09-18 14:25:38'),
(32, 'Peternak', '2017-09-18 14:27:05'),
(33, 'Nelayan/ Perikanan', '2017-09-18 14:34:09'),
(35, 'Industri', '2017-09-18 14:39:09'),
(36, 'Konstruksi', '2017-09-18 14:39:35'),
(37, 'Transportasi', '2017-09-18 14:40:31'),
(38, 'Karyawan BUMN', '2017-09-18 14:40:58'),
(39, 'Karyawan BUMD', '2017-09-18 14:41:11'),
(41, 'Buruh Tani / Perkebunan', '2018-06-06 11:46:34'),
(42, 'Buruh Nelayan / Perikanan', '2018-06-06 11:46:14'),
(43, 'Buruh Peternakan', '2017-09-18 14:43:05'),
(44, 'Pembantu Rumah Tangga', '2017-09-18 14:43:25'),
(45, 'Tukang Cukur', '2017-09-18 14:43:40'),
(46, 'Tukang Listrik', '2017-09-18 14:43:57'),
(47, 'Tukang Batu', '2017-09-18 14:44:06'),
(48, 'Tukang Kayu', '2017-09-18 14:44:18'),
(49, 'Tukang Sol Sepatu', '2017-09-18 14:44:29'),
(50, 'Tukang Las / Pandai Besi', '2018-06-06 12:02:33'),
(51, 'Tukang Jahit', '2017-09-18 14:44:57'),
(52, 'Tukang Gigi', '2017-09-18 14:46:46'),
(53, 'Penata Rias', '2017-09-18 14:48:36'),
(54, 'Penata Busana', '2017-09-18 14:48:50'),
(55, 'Penata Rambut', '2017-09-18 14:48:57'),
(56, 'Mekanik', '2017-09-18 14:49:07'),
(57, 'Seniman', '2017-09-18 14:49:13'),
(58, 'Tabib', '2017-09-18 14:49:26'),
(59, 'Paraji', '2017-09-18 14:49:31'),
(60, 'Perancang Busana', '2017-09-18 14:49:48'),
(61, 'Penterjemah', '2018-06-06 11:55:35'),
(62, 'Imam Masjid', '2017-09-18 14:50:16'),
(63, 'Pendeta', '2017-09-18 14:50:28'),
(64, 'Pastur', '2018-06-06 11:53:11'),
(65, 'Wartawan', '2017-09-18 14:50:52'),
(66, 'Ustadz / Mubaligh', '2018-06-06 12:02:56'),
(67, 'Juru Masak', '2017-09-18 14:51:24'),
(68, 'Promotor Acara', '2017-09-18 14:51:43'),
(69, 'Anggota BPK', '2017-09-18 14:52:06'),
(70, 'Anggota Mahkamah Konstitusi', '2017-09-18 14:52:29'),
(71, 'Dosen', '2017-09-18 14:53:49'),
(72, 'Pilot', '2017-09-18 14:54:04'),
(73, 'Notaris', '2017-09-18 14:54:50'),
(74, 'Arsitek', '2017-09-18 14:55:18'),
(75, 'Akuntan', '2017-09-18 14:55:30'),
(76, 'Konsultan', '2017-09-18 14:55:52'),
(77, 'Perawat', '2017-09-18 14:56:17'),
(78, 'Apoteker', '2017-09-18 14:56:39'),
(79, 'Psikiater / Psikolog', '2018-06-06 12:01:21'),
(80, 'Penyiar Televisi', '2017-09-18 14:58:32'),
(81, 'Penyiar Radio', '2017-09-18 14:58:39'),
(82, 'Pelaut', '2017-09-18 14:58:57'),
(83, 'Peneliti', '2017-09-18 14:59:04'),
(84, 'Sopir', '2017-09-18 14:59:20'),
(85, 'Pialang', '2017-09-18 14:59:52'),
(86, 'Paranormal', '2017-09-18 15:00:07'),
(87, 'Biarawati', '2018-06-06 11:45:41'),
(88, 'Wiraswasta', '2017-09-18 15:01:07'),
(89, 'Pekerjaan Lainnya', '2017-09-18 15:01:21'),
(90, 'Duta Besar', '2017-09-18 15:01:40'),
(91, 'Karyawan Honorer', '2017-11-15 04:18:54'),
(92, 'Bidan', '2017-11-24 04:22:57'),
(93, 'Anggota Kabinet / Kementerian', '2018-06-06 11:45:08'),
(94, 'Gubernur', '2018-06-06 11:46:57'),
(95, 'Wakil Bupati', '2018-06-06 12:05:39'),
(96, 'Wakil Gubernur', '2018-06-06 12:05:56'),
(97, 'Wakil Presiden', '2018-06-06 12:06:15'),
(98, 'Wakil Walikota', '2018-06-06 12:06:39'),
(99, 'Walikota', '2018-06-06 12:06:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jakpwt_ref_pendidikan`
--

CREATE TABLE `jakpwt_ref_pendidikan` (
  `dikIdPendidikan` int(20) NOT NULL,
  `dikPendidikan` varchar(500) DEFAULT NULL,
  `dikLastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `jakpwt_ref_pendidikan`
--

INSERT INTO `jakpwt_ref_pendidikan` (`dikIdPendidikan`, `dikPendidikan`, `dikLastUpdate`) VALUES
(2, 'Tidak / Belum Sekolah', '2019-01-19 18:38:52'),
(3, 'Tamat SD / Sederajat', '2018-06-06 12:14:58'),
(4, 'SLTP / Sederajat', '2017-10-01 14:57:05'),
(5, 'SLTA / Sederajat', '2017-10-01 14:57:28'),
(6, 'Diploma I / II', '2017-10-01 14:57:59'),
(9, 'Diploma IV / Strata I\r\n', '2017-09-30 20:24:22'),
(10, 'Belum Tamat SD / Sederajat\r\n', '2017-09-30 20:24:44'),
(11, 'Akademi / Diploma III / Sarjana Muda', '2017-10-01 15:17:03'),
(12, 'Strata II', '2017-10-01 15:16:34'),
(13, 'Strata III', '2017-10-01 15:16:40');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `default_token`
--
ALTER TABLE `default_token`
  ADD PRIMARY KEY (`tokenId`);

--
-- Indeks untuk tabel `default_user`
--
ALTER TABLE `default_user`
  ADD PRIMARY KEY (`userId`);

--
-- Indeks untuk tabel `jakpwt_anggota`
--
ALTER TABLE `jakpwt_anggota`
  ADD PRIMARY KEY (`noKta`);

--
-- Indeks untuk tabel `jakpwt_kas`
--
ALTER TABLE `jakpwt_kas`
  ADD PRIMARY KEY (`kasId`);

--
-- Indeks untuk tabel `jakpwt_ref_agama`
--
ALTER TABLE `jakpwt_ref_agama`
  ADD PRIMARY KEY (`agmIdAgama`);

--
-- Indeks untuk tabel `jakpwt_ref_pekerjaan`
--
ALTER TABLE `jakpwt_ref_pekerjaan`
  ADD PRIMARY KEY (`pkjIdPekerjaan`);

--
-- Indeks untuk tabel `jakpwt_ref_pendidikan`
--
ALTER TABLE `jakpwt_ref_pendidikan`
  ADD PRIMARY KEY (`dikIdPendidikan`,`dikLastUpdate`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `default_token`
--
ALTER TABLE `default_token`
  MODIFY `tokenId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `default_user`
--
ALTER TABLE `default_user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
