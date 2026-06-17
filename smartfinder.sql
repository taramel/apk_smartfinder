SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `smartfinder` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `smartfinder`;

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT 'box',
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `kategori` VALUES (1,'Elektronik','smartphone'),(2,'Dokumen','file-text'),(3,'Dompet/Tas','briefcase'),(4,'Kunci','key'),(5,'Lainnya','box');

CREATE TABLE `lokasi` (
  `id_lokasi` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lokasi` varchar(100) NOT NULL,
  `gedung` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_lokasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `lokasi` VALUES (1,'Gedung A','Gedung A'),(2,'Kantin','Area Umum'),(3,'Perpustakaan','Gedung B'),(4,'Parkiran','Area Umum'),(5,'Lobby Utama','Gedung A');

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_petugas` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('admin','petugas') DEFAULT 'petugas',
  `foto_profil` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- password: admin123
INSERT INTO `users` (`id_user`,`username`,`password`,`nama_petugas`,`email`,`role`) VALUES
(1,'admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Tara Amelia','admin@smartfinder.ac.id','admin');

CREATE TABLE `barang_temuan` (
  `id_barang` int(11) NOT NULL AUTO_INCREMENT,
  `id_kategori` int(11) DEFAULT NULL,
  `id_lokasi` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `warna` varchar(50) DEFAULT NULL,
  `merek` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tgl_temu` datetime DEFAULT current_timestamp(),
  `tgl_expired` datetime DEFAULT NULL,
  `status` enum('Unclaimed','Claimed','Expired') DEFAULT 'Unclaimed',
  `bukti_rahasia` varchar(255) DEFAULT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_barang`),
  KEY `id_kategori` (`id_kategori`),
  KEY `id_lokasi` (`id_lokasi`),
  CONSTRAINT `bt_kat` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`),
  CONSTRAINT `bt_lok` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id_lokasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pencari` (
  `id_pencari` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pencari` varchar(100) DEFAULT NULL,
  `identitas_pencari` varchar(50) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_pencari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `klaim` (
  `id_klaim` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) DEFAULT NULL,
  `id_pencari` int(11) DEFAULT NULL,
  `bukti_input` varchar(255) DEFAULT NULL,
  `status_klaim` enum('Pending','Valid','Invalid') DEFAULT 'Pending',
  `tgl_klaim` datetime DEFAULT current_timestamp(),
  `catatan` text DEFAULT NULL,
  PRIMARY KEY (`id_klaim`),
  KEY `id_barang` (`id_barang`),
  KEY `id_pencari` (`id_pencari`),
  CONSTRAINT `kl_bar` FOREIGN KEY (`id_barang`) REFERENCES `barang_temuan` (`id_barang`),
  CONSTRAINT `kl_pen` FOREIGN KEY (`id_pencari`) REFERENCES `pencari` (`id_pencari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `verifikasi` (
  `id_verifikasi` int(11) NOT NULL AUTO_INCREMENT,
  `id_klaim` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `status_verifikasi` enum('Valid','Invalid') DEFAULT NULL,
  `catatan_petugas` text DEFAULT NULL,
  `tgl_verifikasi` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_verifikasi`),
  KEY `id_klaim` (`id_klaim`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `ver_klm` FOREIGN KEY (`id_klaim`) REFERENCES `klaim` (`id_klaim`),
  CONSTRAINT `ver_usr` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `activity_log` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `aksi` varchar(100) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
