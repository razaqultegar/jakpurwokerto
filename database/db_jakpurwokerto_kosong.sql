-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Mar 2020 pada 07.29
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
(1, 'cobatoken');

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
(1, 'Super Admin', 'superadmin', 'd389d5247e4f47ecf0a33dead2428ec0', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jakpwt_anggota`
--

CREATE TABLE `jakpwt_anggota` (
  `agtId` int(11) NOT NULL,
  `agtNoKta` varchar(100) DEFAULT NULL,
  `agtIdWilayah` int(20) DEFAULT NULL,
  `agtNama` varchar(250) DEFAULT NULL,
  `agtNmPendek` varchar(20) DEFAULT NULL,
  `agtTmptLahir` varchar(500) DEFAULT NULL,
  `agtTglLahir` date DEFAULT NULL,
  `agtUmur` int(20) DEFAULT NULL,
  `agtJnsKelamin` enum('L','P','N') DEFAULT NULL,
  `agtIdPendidikan` int(20) DEFAULT NULL,
  `agtIdPekerjaan` int(20) DEFAULT NULL,
  `agtKelurahan` varchar(250) DEFAULT NULL,
  `agtKecamatan` varchar(250) DEFAULT NULL,
  `agtAlamatJalan` text,
  `agtKdPos` varchar(5) DEFAULT NULL,
  `agtNoTelp` varchar(13) DEFAULT NULL,
  `agtEmail` varchar(250) DEFAULT NULL,
  `agtUkrnKaos` varchar(20) DEFAULT NULL,
  `agtFoto` varchar(100) DEFAULT NULL,
  `agtStatusKta` enum('0','1') NOT NULL DEFAULT '0',
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
(1, 'Petani / Pekebun', '2020-02-17 07:07:14'),
(2, 'Tentara Nasional Indonesia', '2020-02-17 07:07:16'),
(3, 'Pegawai Negeri Sipil', '2020-02-17 07:07:18'),
(4, 'Karyawan Swasta', '2020-02-17 07:07:21'),
(5, 'Wirausaha', '2020-02-17 07:07:25'),
(6, 'Buruh Migran (TKI)', '2020-02-17 07:07:35'),
(7, 'Pedagang', '2020-02-17 07:07:38'),
(8, 'Buruh Harian Lepas', '2020-02-17 07:07:42'),
(9, 'Guru', '2020-02-17 07:11:46'),
(10, 'Kepala Desa', '2020-02-17 07:12:15'),
(11, 'Perangkat Desa', '2020-02-17 07:12:15'),
(12, 'Bupati', '2020-02-17 07:12:15'),
(13, 'Anggota DPRD Kabupaten / Kota', '2020-02-17 07:12:15'),
(14, 'Anggota DPR RI', '2020-02-17 07:12:16'),
(15, 'Anggota DPD', '2020-02-17 07:12:16'),
(16, 'Anggota DPRD Provinsi', '2020-02-17 07:12:16'),
(17, 'Mengurus Rumah Tangga', '2020-02-17 07:12:16'),
(18, 'Belum/ Tidak Bekerja', '2020-02-17 07:12:16'),
(19, 'Pelajar/ Mahasiswa', '2020-02-17 07:12:16'),
(20, 'Dokter', '2020-02-17 07:12:16'),
(21, 'Pengacara', '2020-02-17 07:12:16'),
(22, 'Menteri', '2020-02-17 07:12:16'),
(23, 'Presiden', '2020-02-17 07:12:16'),
(24, 'Kepolisian RI', '2020-02-17 07:12:16'),
(25, 'Pensiunan', '2020-02-17 07:12:16'),
(26, 'Perdagangan', '2020-02-17 07:12:16'),
(27, 'Peternak', '2020-02-17 07:12:16'),
(28, 'Nelayan/ Perikanan', '2020-02-17 07:12:16'),
(29, 'Industri', '2020-02-17 07:12:17'),
(30, 'Konstruksi', '2020-02-17 07:12:17'),
(31, 'Transportasi', '2020-02-17 07:12:17'),
(32, 'Karyawan BUMN', '2020-02-17 07:12:17'),
(33, 'Karyawan BUMD', '2020-02-17 07:12:17'),
(34, 'Buruh Tani / Perkebunan', '2020-02-17 07:12:17'),
(35, 'Buruh Nelayan / Perikanan', '2020-02-17 07:12:17'),
(36, 'Buruh Peternakan', '2020-02-17 07:12:17'),
(37, 'Pembantu Rumah Tangga', '2020-02-17 07:12:17'),
(38, 'Tukang Cukur', '2020-02-17 07:12:17'),
(39, 'Tukang Listrik', '2020-02-17 07:12:17'),
(40, 'Tukang Batu', '2020-02-17 07:12:17'),
(41, 'Tukang Kayu', '2020-02-17 07:12:17'),
(42, 'Tukang Sol Sepatu', '2020-02-17 07:12:18'),
(43, 'Tukang Las / Pandai Besi', '2020-02-17 07:12:18'),
(44, 'Tukang Jahit', '2020-02-17 07:12:18'),
(45, 'Tukang Gigi', '2020-02-17 07:12:18'),
(46, 'Penata Rias', '2020-02-17 07:12:18'),
(47, 'Penata Busana', '2020-02-17 07:12:18'),
(48, 'Penata Rambut', '2020-02-17 07:12:19'),
(49, 'Mekanik', '2020-02-17 07:12:19'),
(50, 'Seniman', '2020-02-17 07:12:19'),
(51, 'Tabib', '2020-02-17 07:16:38'),
(52, 'Paraji', '2020-02-17 07:16:38'),
(53, 'Perancang Busana', '2020-02-17 07:16:38'),
(54, 'Penterjemah', '2020-02-17 07:16:38'),
(55, 'Imam Masjid', '2020-02-17 07:16:38'),
(56, 'Pendeta', '2020-02-17 07:16:38'),
(57, 'Pastur', '2020-02-17 07:16:38'),
(58, 'Wartawan', '2020-02-17 07:16:38'),
(59, 'Ustadz / Mubaligh', '2020-02-17 07:16:39'),
(60, 'Juru Masak', '2020-02-17 07:16:39'),
(61, 'Promotor Acara', '2020-02-17 07:16:39'),
(62, 'Anggota BPK', '2020-02-17 07:16:39'),
(63, 'Anggota Mahkamah Konstitusi', '2020-02-17 07:16:39'),
(64, 'Dosen', '2020-02-17 07:16:39'),
(65, 'Pilot', '2020-02-17 07:16:39'),
(66, 'Notaris', '2020-02-17 07:16:39'),
(67, 'Arsitek', '2020-02-17 07:16:39'),
(68, 'Akuntan', '2020-02-17 07:16:40'),
(69, 'Konsultan', '2020-02-17 07:16:40'),
(70, 'Perawat', '2020-02-17 07:16:40'),
(71, 'Apoteker', '2020-02-17 07:16:40'),
(72, 'Psikiater / Psikolog', '2020-02-17 07:16:40'),
(73, 'Penyiar Televisi', '2020-02-17 07:16:40'),
(74, 'Penyiar Radio', '2020-02-17 07:16:40'),
(75, 'Pelaut', '2020-02-17 07:16:40'),
(76, 'Peneliti', '2020-02-17 07:16:40'),
(77, 'Sopir', '2020-02-17 07:16:40'),
(78, 'Pialang', '2020-02-17 07:16:40'),
(79, 'Paranormal', '2020-02-17 07:16:40'),
(80, 'Biarawati', '2020-02-17 07:16:41'),
(81, 'Wiraswasta', '2020-02-17 07:16:41'),
(82, 'Pekerjaan Lainnya', '2020-02-17 07:16:41'),
(83, 'Duta Besar', '2020-02-17 07:16:41'),
(84, 'Karyawan Honorer', '2020-02-17 07:16:41'),
(85, 'Bidan', '2020-02-17 07:16:41'),
(86, 'Anggota Kabinet / Kementerian', '2020-02-17 07:16:41'),
(87, 'Gubernur', '2020-02-17 07:16:41'),
(88, 'Wakil Bupati', '2020-02-17 07:16:42'),
(89, 'Wakil Gubernur', '2020-02-17 07:16:42'),
(90, 'Wakil Presiden', '2020-02-17 07:16:42'),
(91, 'Wakil Walikota', '2020-02-17 07:16:42'),
(92, 'Walikota', '2020-02-17 07:16:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jakpwt_ref_pendidikan`
--

CREATE TABLE `jakpwt_ref_pendidikan` (
  `dikIdPendidikan` int(11) NOT NULL,
  `dikPendidikan` varchar(500) DEFAULT NULL,
  `dikLastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `jakpwt_ref_pendidikan`
--

INSERT INTO `jakpwt_ref_pendidikan` (`dikIdPendidikan`, `dikPendidikan`, `dikLastUpdate`) VALUES
(1, 'Tidak / Belum Sekolah', '2020-02-17 07:06:28'),
(2, 'Tamat SD / Sederajat', '2020-02-17 07:06:32'),
(3, 'SLTP / Sederajat', '2020-02-17 07:06:36'),
(4, 'SLTA / Sederajat', '2020-02-17 07:06:40'),
(5, 'Diploma I / II', '2020-02-17 07:06:44'),
(6, 'Diploma IV / Strata I\r\n', '2020-02-17 07:06:47'),
(7, 'Belum Tamat SD / Sederajat\r\n', '2020-02-17 07:06:51'),
(8, 'Akademi / Diploma III / Sarjana Muda', '2020-02-17 07:06:55'),
(9, 'Strata II', '2020-02-17 07:06:58'),
(10, 'Strata III', '2020-02-17 07:07:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jakpwt_ref_wilayah`
--

CREATE TABLE `jakpwt_ref_wilayah` (
  `wilIdWilayah` int(11) NOT NULL,
  `wilNama` varchar(500) DEFAULT NULL,
  `wilLastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `jakpwt_ref_wilayah`
--

INSERT INTO `jakpwt_ref_wilayah` (`wilIdWilayah`, `wilNama`, `wilLastUpdate`) VALUES
(1, 'Ajibarang', '2020-02-27 11:06:39'),
(2, 'Banyumas', '2020-02-27 11:06:54'),
(3, 'Purwokerto', '2020-02-27 13:20:52'),
(4, 'Rantau', '2020-02-27 13:20:55'),
(5, 'Wangon', '2020-02-27 13:21:03');

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
  ADD PRIMARY KEY (`agtId`),
  ADD KEY `agtIdWilayah` (`agtIdWilayah`),
  ADD KEY `agtIdPendidikan` (`agtIdPendidikan`),
  ADD KEY `agtIdPekerjaan` (`agtIdPekerjaan`);

--
-- Indeks untuk tabel `jakpwt_kas`
--
ALTER TABLE `jakpwt_kas`
  ADD PRIMARY KEY (`kasId`);

--
-- Indeks untuk tabel `jakpwt_ref_pekerjaan`
--
ALTER TABLE `jakpwt_ref_pekerjaan`
  ADD PRIMARY KEY (`pkjIdPekerjaan`);

--
-- Indeks untuk tabel `jakpwt_ref_pendidikan`
--
ALTER TABLE `jakpwt_ref_pendidikan`
  ADD PRIMARY KEY (`dikIdPendidikan`) USING BTREE;

--
-- Indeks untuk tabel `jakpwt_ref_wilayah`
--
ALTER TABLE `jakpwt_ref_wilayah`
  ADD PRIMARY KEY (`wilIdWilayah`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `default_token`
--
ALTER TABLE `default_token`
  MODIFY `tokenId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `default_user`
--
ALTER TABLE `default_user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `jakpwt_anggota`
--
ALTER TABLE `jakpwt_anggota`
  MODIFY `agtId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jakpwt_kas`
--
ALTER TABLE `jakpwt_kas`
  MODIFY `kasId` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
