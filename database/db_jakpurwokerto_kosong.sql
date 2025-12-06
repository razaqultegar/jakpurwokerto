-- -------------------------------------------------------------
-- TablePlus 6.7.4(642)
--
-- https://tableplus.com/
--
-- Database: jakpurwokerto
-- Generation Time: 2025-12-06 02:54:51.9270
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `default_token`;
CREATE TABLE `default_token` (
  `tokenId` int NOT NULL AUTO_INCREMENT,
  `token` varchar(10) NOT NULL,
  PRIMARY KEY (`tokenId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `default_user`;
CREATE TABLE `default_user` (
  `userId` int NOT NULL AUTO_INCREMENT,
  `realname` varchar(100) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `jakpwt_anggota`;
CREATE TABLE `jakpwt_anggota` (
  `agtId` int NOT NULL AUTO_INCREMENT,
  `agtIdWilayah` bigint unsigned DEFAULT NULL,
  `agtFoto` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `agtFotoKTA` varchar(100) DEFAULT NULL,
  `agtNik` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtNoKTA` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `agtNama` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtJnsKelamin` enum('L','P') DEFAULT 'L',
  `agtTmptLahir` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtTglLahir` date NOT NULL,
  `agtUmur` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtProvinsi` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtKabupaten` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtKecamatan` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtKelurahan` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtAlamatJalan` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `agtEmail` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtNoTelp` varchar(13) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agtUkuranKaos` enum('S','M','L','XL','XXL') DEFAULT 'S',
  `agtMetodePembayaran` enum('BCA','MANDIRI') NOT NULL DEFAULT 'BCA',
  `agtTglInsert` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `agtLastUpdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agtId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `jakpwt_kas`;
CREATE TABLE `jakpwt_kas` (
  `kasId` int NOT NULL AUTO_INCREMENT,
  `kasWilId` int DEFAULT NULL,
  `kasTanggal` date DEFAULT NULL,
  `kasMasukUraian` text,
  `kasMasuk` varchar(100) DEFAULT NULL,
  `kasKeluarUraian` text,
  `kasKeluar` varchar(100) DEFAULT NULL,
  `kasSaldo` varchar(100) DEFAULT NULL,
  `kasLastUpdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`kasId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `jakpwt_ref_wilayah`;
CREATE TABLE `jakpwt_ref_wilayah` (
  `wilIdWilayah` int NOT NULL AUTO_INCREMENT,
  `wilNama` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `wilLastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`wilIdWilayah`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

INSERT INTO `default_token` (`tokenId`, `token`) VALUES
(1, '122025');

INSERT INTO `default_user` (`userId`, `realname`, `username`, `password`, `foto`) VALUES
(1, 'Super Admin', 'superadmin', '5f4dcc3b5aa765d61d8327deb882cf99', NULL);

INSERT INTO `jakpwt_ref_wilayah` (`wilIdWilayah`, `wilNama`, `wilLastUpdate`) VALUES
(1, 'Ajibarang', '2020-02-27 18:06:39'),
(2, 'Banyumas', '2020-02-27 18:06:54'),
(3, 'Purwokerto', '2020-02-27 20:20:52'),
(4, 'Rantau', '2020-02-27 20:20:55'),
(5, 'Wangon', '2020-02-27 20:21:03');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;