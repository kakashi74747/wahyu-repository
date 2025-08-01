-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Jun 2025 pada 03.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafe_akmal`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bahan_baku`
--

CREATE TABLE `bahan_baku` (
  `id_bahan` int(11) NOT NULL,
  `nama_bahan` varchar(50) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `permintaan` int(11) DEFAULT NULL,
  `bahan_masuk` int(11) DEFAULT NULL,
  `bahan_terpakai` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bahan_baku`
--

INSERT INTO `bahan_baku` (`id_bahan`, `nama_bahan`, `satuan`, `permintaan`, `bahan_masuk`, `bahan_terpakai`) VALUES
(1, 'Beras', 'kg', 25, 25, 30),
(2, 'Daging', 'kg', 5, 10, 7),
(3, 'Ayam', 'kg', 9, 8, 9),
(4, 'Gula', 'kg', 10, 5, 7),
(5, 'Teh', 'kotak', 3, 5, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_karyawan`
--

CREATE TABLE `data_karyawan` (
  `idKaryawan` int(11) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `agama` int(11) DEFAULT NULL,
  `bagian` char(1) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tipe_karyawan` enum('full-time','part-time','freelance') DEFAULT 'full-time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_karyawan`
--

INSERT INTO `data_karyawan` (`idKaryawan`, `nama`, `alamat`, `gender`, `agama`, `bagian`, `avatar`, `tanggal_mulai`, `tipe_karyawan`) VALUES
(0, 'Maybell Gardenia Ermanno', 'Castello Imperiale di Ermanno', 'P', 3, 'D', 'karyawan_avatar_0_1748443042.jpg', '2024-02-15', 'part-time'),
(188, 'LeBron James', 'Jl. Sucipto Mewing', 'L', 1, 'C', NULL, '2022-07-15', 'part-time'),
(555, 'Dostoyevsky', 'South NordKai City', 'L', 5, 'D', NULL, '2018-11-08', 'full-time'),
(666, 'Chelsea', 'Jl.Wilis no 10', 'P', 2, 'B', 'karyawan_avatar_666_1748442654.jpg', '2025-01-20', 'part-time'),
(696, 'Vedrfolnir', 'Khaenri\'ah Residence', 'L', 4, 'C', NULL, '2019-08-29', 'full-time'),
(777, 'Columbina', '3/7 Oviria Garden', 'P', 3, 'A', 'karyawan_avatar_777_1748442011.gif', '2017-07-07', 'full-time'),
(778, 'Ragus Altar', ' S. Walnut St, Atlanta', 'L', 6, 'C', NULL, '2024-04-09', 'part-time'),
(782, 'MRBEAST', 'Somewhere in North Caroline', 'L', 2, 'D', 'karyawan_avatar_782_1749448799.jpg', '2022-04-30', 'full-time'),
(783, 'Andrian', 'S. Hoe St', 'L', 6, 'C', NULL, '2024-08-15', 'full-time');

-- --------------------------------------------------------

--
-- Struktur dari tabel `favorites`
--

CREATE TABLE `favorites` (
  `id_favorite` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `favorites`
--

INSERT INTO `favorites` (`id_favorite`, `id_user`, `id_menu`) VALUES
(15, 1, 16),
(5, 1, 25),
(3, 2, 1),
(17, 4, 8),
(16, 4, 24),
(2, 6, 2),
(18, 7, 23),
(8, 11, 15),
(11, 11, 16),
(12, 11, 19),
(14, 12, 2),
(13, 12, 24);

-- --------------------------------------------------------

--
-- Struktur dari tabel `masakan`
--

CREATE TABLE `masakan` (
  `id_menu` int(11) NOT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `nama_masakan` varchar(100) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `status_masakan` enum('tersedia','habis') DEFAULT NULL,
  `kategori` enum('makanan','minuman','dessert') NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `is_special` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `masakan`
--

INSERT INTO `masakan` (`id_menu`, `jenis`, `nama_masakan`, `harga`, `status_masakan`, `kategori`, `is_featured`, `deskripsi`, `gambar`, `is_special`) VALUES
(1, NULL, 'Cotton Candy Latte', 45000.00, 'tersedia', 'minuman', 1, 'Cotton Candy Latte :3', 'food_1749085795.jpg', 0),
(2, NULL, 'Ballerina Cappuccina', 45000.00, 'tersedia', 'minuman', 1, '', 'food_1749097437.jpg', 0),
(3, NULL, 'Iced Lavender Latte ', 55000.00, 'habis', 'minuman', 1, 'Iced lavender latte :D', 'food_1749267821.jpg', 0),
(4, NULL, 'Lemon Coffee Tonic', 25000.00, 'tersedia', 'minuman', 1, '', 'food_1749267880.jpg', 0),
(5, NULL, 'Strawberry Fields', 64999.99, 'tersedia', 'minuman', 1, '', 'food_1749268091.jpg', 0),
(6, NULL, 'Summer Shakerato', 50000.00, NULL, 'minuman', 1, '', 'food_1749268261.jpg', 0),
(8, NULL, 'Mocha Latte', 30000.00, NULL, 'minuman', 1, '', 'food_1749268358.jpg', 0),
(12, NULL, 'Lychee Rose Mocktail (Maybell-Themed)', 60000.00, NULL, 'minuman', 1, '', 'food_1749268376.jpg', 0),
(14, NULL, 'Strawberry Vanilla Latte', 69000.00, NULL, 'minuman', 1, '', 'food_1749268668.jpg', 0),
(15, NULL, 'Lava Cake', 40000.00, NULL, 'dessert', 1, '', 'food_1749269175.jpg', 0),
(16, NULL, 'Wagyu Rendang A5', 500000.00, NULL, 'makanan', 1, '', 'food_1749268697.jpg', 0),
(17, NULL, 'Double Ice Cream Sandwich', 30000.00, NULL, 'dessert', 1, '', 'food_1749269289.jpg', 0),
(18, NULL, 'Nasi Goreng Cassie', 20000.00, NULL, 'makanan', 1, '', 'food_1749268792.jpg', 0),
(19, NULL, 'French Fries ', 30000.00, NULL, 'makanan', 1, '', 'food_1749268838.jpg', 0),
(20, NULL, 'pot-au-feu', 50000.00, NULL, 'makanan', 1, '', 'food_1749268871.jpg', 0),
(21, NULL, 'Vanilla Ice Cream', 20000.00, NULL, 'dessert', 1, '', 'food_1749299378.jpg', 0),
(22, NULL, 'Mie Ayam with Premium Ingredients', 60000.00, NULL, 'makanan', 1, '', 'food_1749269088.jpg', 0),
(23, NULL, 'Double Cheese Burger', 30000.00, NULL, 'makanan', 1, '', 'food_1749269141.jpg', 0),
(24, NULL, 'Choco Chip Cookies', 20000.00, NULL, 'dessert', 1, 'Cookies klasik dengan choco chip yang lumer di mulut🤤', 'food_1749269573.jpg', 0),
(25, NULL, 'Strawberry Parfait', 50000.00, NULL, 'dessert', 1, 'Lapisan strawberry, granola, dan jelly, dihias dengan marshmallow & cotton candy, bak awan di langit fantasy.', 'food_1749269765.jpg', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu_bahan`
--

CREATE TABLE `menu_bahan` (
  `id_menu` int(11) NOT NULL,
  `id_bahan` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `menu_bahan`
--

INSERT INTO `menu_bahan` (`id_menu`, `id_bahan`, `jumlah`) VALUES
(16, 2, 1),
(18, 1, 2),
(20, 2, 1),
(22, 2, 1),
(23, 2, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `reviews`
--

CREATE TABLE `reviews` (
  `id_review` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reviews`
--

INSERT INTO `reviews` (`id_review`, `id_user`, `id_menu`, `rating`, `komentar`, `tanggal`) VALUES
(1, 6, 2, 5, 'Oh My Gyatt! Cappuccina mimimimi ini sangat enak! Ini adalah Cappuccina terbaik dalam hidup saya🥰❤️', '2025-05-31 07:12:02'),
(2, 10, 8, 5, 'Sangat enak. Proved by popi❤️🙏', '2025-05-31 12:52:02'),
(3, 7, 16, 5, 'Das ist vortrefflich. 1000/5.', '2025-06-05 08:26:29'),
(4, 13, 22, 5, 'Siehe, wie süß der Mund sich freut, ja, dies ist ein Lobgesang des Geschmacks! ❤️', '2025-06-05 08:30:13'),
(5, 11, 25, 5, 'strawberry parfaitnya the best! oh iya, dan juga, pelayan bernama Andrian memberikan pelayanan yang sangat menakjubkan!😍', '2025-06-08 17:17:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_level` int(11) DEFAULT 1,
  `tanggal` date DEFAULT NULL,
  `is_debug` tinyint(1) DEFAULT 0,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `original_total` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `id_level`, `tanggal`, `is_debug`, `total_amount`, `discount_percentage`, `original_total`) VALUES
(1, 2, 1, '2024-10-09', 0, 90000.00, 0.00, 0.00),
(2, 3, 1, '2024-10-09', 0, 90000.00, 0.00, 0.00),
(3, 2, 1, '2024-09-09', 0, 275000.00, 0.00, 0.00),
(4, 4, 1, '2024-10-09', 0, 129999.98, 0.00, 0.00),
(5, 5, 1, '2024-09-09', 0, 360000.00, 0.00, 0.00),
(6, 5, 1, '2025-05-15', 0, 300000.00, 0.00, 0.00),
(7, 6, 1, '2025-02-15', 0, 75000.00, 0.00, 0.00),
(8, 4, 1, '2025-05-08', 0, 180000.00, 0.00, 0.00),
(9, 1, 1, '2025-05-14', 0, 180000.00, 0.00, 0.00),
(11, 5, 1, '2025-05-21', 0, 2346000.00, 0.00, 0.00),
(13, 8, 1, '2025-05-20', 0, 100000.00, 0.00, 0.00),
(14, 2, 1, '2025-05-22', 0, 180000.00, 0.00, 0.00),
(15, 5, 1, '2025-05-28', 0, 1650000.00, 0.00, 0.00),
(16, 1, 1, '2025-05-27', 0, 2000000.00, 0.00, 0.00),
(17, 1, 1, '2025-05-29', 0, 1000000.00, 0.00, 0.00),
(18, 1, 1, '2025-05-29', 0, 40000.00, 0.00, 0.00),
(19, 11, 1, '2025-05-30', 0, 1000000.00, 0.00, 0.00),
(20, 11, 1, '2025-05-30', 0, 90000.00, 0.00, 0.00),
(21, 11, 1, '2025-05-30', 0, 160000.00, 0.00, 0.00),
(22, 11, 1, '2025-05-30', 0, 60000.00, 0.00, 0.00),
(23, 11, 1, '2025-05-30', 0, 1000000.00, 0.00, 0.00),
(24, 12, 1, '2025-05-30', 0, 159999.98, 0.00, 0.00),
(25, 1, 1, '2025-05-30', 0, 60000.00, 0.00, 0.00),
(26, 6, 1, '2025-05-31', 0, 75000.00, 0.00, 0.00),
(27, 10, 1, '2025-05-31', 0, 30000.00, 0.00, 0.00),
(28, 11, 1, '2025-06-03', 0, 75000.00, 0.00, 0.00),
(29, 13, 1, '2025-06-05', 0, 60000.00, 0.00, 0.00),
(30, 7, 1, '2025-06-05', 0, 135000.00, 0.00, 0.00),
(31, 2, 1, '2025-06-05', 0, 90000.00, 0.00, 0.00),
(32, 1, 1, '2025-06-07', 0, 160000.00, 0.00, 0.00),
(33, 11, 1, '2025-06-08', 0, 70000.00, 0.00, 0.00),
(34, 1, 1, '2025-06-08', 0, 540000.00, 0.00, 0.00),
(35, 11, 1, '2025-06-09', 0, 1000000.00, 0.00, 0.00),
(36, 1, 1, '2025-06-09', 0, 5000000.00, 0.00, 0.00),
(37, 1, 1, '2025-06-09', 0, 610000.00, 0.00, 0.00),
(38, 1, 1, '2025-06-09', 0, 500000.00, 0.00, 0.00),
(39, 2, 1, '2025-06-09', 0, 50000.00, 0.00, 0.00),
(40, 2, 1, '2025-06-09', 0, 500000.00, 0.00, 0.00),
(41, 11, 1, '2025-06-09', 0, 20000.00, 0.00, 0.00),
(42, 1, 1, '2025-06-09', 0, 6000000.00, 0.00, 0.00),
(43, 1, 1, '2025-06-09', 0, 4165000.00, 0.00, 0.00),
(44, 2, 1, '2025-06-09', 0, 50000.00, 0.00, 0.00),
(48, 4, 1, '2025-06-09', 0, 30000.00, 0.00, 0.00),
(49, 2, 4, '2025-06-09', 0, 50000.00, 0.00, 0.00),
(50, 6, 6, '2025-06-09', 0, 10600000.00, 0.00, 0.00),
(51, 6, 6, '2025-06-09', 0, 1395000.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`id_detail`, `id_transaksi`, `id_menu`, `jumlah`) VALUES
(4, 4, 5, 1),
(5, 5, 1, 4),
(6, 6, 6, 3),
(8, 8, 8, 3),
(9, 9, 1, 2),
(10, 11, 14, 17),
(11, 13, 4, 2),
(12, 14, 8, 3),
(14, 16, 16, 2),
(15, 17, 16, 1),
(16, 18, 18, 1),
(17, 19, 16, 1),
(18, 20, 1, 1),
(19, 21, 15, 2),
(20, 22, 17, 1),
(21, 23, 16, 1),
(35, 4, 5, 1),
(36, 5, 1, 4),
(37, 6, 6, 3),
(39, 8, 8, 3),
(40, 9, 1, 2),
(41, 11, 14, 17),
(42, 13, 4, 2),
(43, 14, 8, 3),
(45, 16, 16, 2),
(46, 17, 16, 1),
(47, 18, 18, 1),
(48, 19, 16, 1),
(49, 20, 1, 1),
(50, 21, 15, 2),
(51, 22, 17, 1),
(52, 23, 16, 1),
(63, 24, 19, 1),
(64, 24, 5, 2),
(65, 25, 22, 1),
(66, 26, 1, 1),
(67, 26, 19, 1),
(68, 27, 8, 1),
(69, 28, 1, 1),
(70, 28, 19, 1),
(71, 29, 22, 1),
(72, 30, 1, 3),
(73, 31, 2, 1),
(74, 31, 1, 1),
(75, 32, 25, 1),
(76, 32, 22, 1),
(77, 32, 20, 1),
(78, 1, 1, 2),
(79, 15, 3, 30),
(80, 7, 2, 1),
(81, 7, 19, 1),
(82, 3, 4, 3),
(83, 3, 18, 10),
(84, 33, 25, 1),
(85, 33, 21, 1),
(86, 34, 15, 1),
(87, 34, 16, 1),
(88, 35, 16, 2),
(89, 36, 16, 10),
(90, 37, 12, 6),
(91, 37, 25, 2),
(92, 37, 19, 1),
(93, 37, 17, 4),
(94, 38, 16, 1),
(95, 39, 25, 1),
(96, 40, 16, 1),
(97, 41, 18, 1),
(98, 42, 25, 20),
(99, 42, 16, 10),
(100, 43, 22, 10),
(101, 43, 3, 5),
(102, 43, 12, 10),
(103, 43, 14, 10),
(104, 43, 16, 4),
(105, 44, 20, 1),
(109, 48, 19, 1),
(110, 49, 25, 1),
(111, 2, 2, 1),
(112, 2, 14, 2),
(113, 50, 16, 21),
(114, 50, 20, 2),
(115, 51, 16, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `nama_user` varchar(100) DEFAULT NULL,
  `id_level` int(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `orders` int(11) DEFAULT 0,
  `favorites` int(11) DEFAULT 0,
  `reviews` int(11) DEFAULT 0,
  `member_tier` varchar(50) DEFAULT 'Bronze',
  `telepon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama_user`, `id_level`, `avatar`, `bio`, `orders`, `favorites`, `reviews`, `member_tier`, `telepon`) VALUES
(1, 'kaiserimpact10', 'soccersucks', 'Michael Kaiser Mulyono', 15, 'user_avatar_1_1749276784.jpg', 'Man, I Love Frappuccino', 0, 0, 0, 'Bronze', NULL),
(2, 'anitamaxwynn', 'gamble4ever', 'Aventurine Ambaturin', 4, 'user_avatar_2_1749277581.jpg', 'Gambling is Life. ', 0, 0, 0, 'Bronze', NULL),
(3, 'tylertheauthor', 'chromakopia', 'Tyler Okonma', 3, NULL, NULL, 0, 0, 0, 'Bronze', NULL),
(4, 'vedrfolnir91', 'kritjanzdottr', 'Veðrfölnir Kristjansdottir', 2, 'user_avatar_4_1749433701.jpg', 'Perceptive.', 0, 0, 0, 'Bronze', NULL),
(5, 'nekorinn99', 'rinchan', 'Rin', 6, 'user_avatar_5_1749434999.jpg', 'Goonmaxxing to c.ai 24/7.', 0, 0, 0, 'Bronze', NULL),
(6, 'puffydaddy999', 'babyoilgoat', 'Sean Combs', 10, 'user_avatar_6_1748650124.jpg', 'Hey there! I am using WhatsApp.', 0, 0, 0, 'Bronze', NULL),
(7, 'themasochist', 'steponme.com', 'Ruen Kleist', 1, 'user_avatar_7_1749086389.jpg', 'I love my wife.', 0, 0, 0, 'Bronze', NULL),
(8, 'williamalt144', 'arutaisen', 'William Alteisen', 1, 'user_avatar_8_1749404200.jpg', NULL, 0, 0, 0, 'Bronze', NULL),
(10, 'sigmapopexiv', 'callmepopi', 'Robert Prevost', 1, 'user_avatar_10_1748445618.jpg', '267th pope of the Roman Catholic Church (and a nazi)', 0, 0, 0, 'Bronze', NULL),
(11, 'adam', 'thefirsthuman', 'Adam El Tralalelo Tralala', 5, 'user_avatar_11_1749478762.jpg', 'The OG Adam is here and ready to steal ur wife from the rear. (Schyeah)', 0, 0, 0, 'Bronze', '80593842'),
(12, 'rajajawacina', 'jawacina2007', 'Kanae', 1, 'user_avatar_12_1749398615.jpg', 'Jenderal Intelijen Yugoslavia', 0, 0, 0, 'Bronze', NULL),
(13, 'zarathustra', 'ubermensch', 'Zarathustra', 1, 'user_avatar_13_1749086885.jpg', 'and thus spoke, Zarathustra.\r\n', 0, 0, 0, 'Bronze', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id_bahan`);

--
-- Indeks untuk tabel `data_karyawan`
--
ALTER TABLE `data_karyawan`
  ADD PRIMARY KEY (`idKaryawan`);

--
-- Indeks untuk tabel `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id_favorite`),
  ADD UNIQUE KEY `id_user` (`id_user`,`id_menu`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indeks untuk tabel `masakan`
--
ALTER TABLE `masakan`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indeks untuk tabel `menu_bahan`
--
ALTER TABLE `menu_bahan`
  ADD PRIMARY KEY (`id_menu`,`id_bahan`),
  ADD KEY `id_bahan` (`id_bahan`);

--
-- Indeks untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `data_karyawan`
--
ALTER TABLE `data_karyawan`
  MODIFY `idKaryawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=785;

--
-- AUTO_INCREMENT untuk tabel `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id_favorite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `masakan`
--
ALTER TABLE `masakan`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `masakan` (`id_menu`);

--
-- Ketidakleluasaan untuk tabel `menu_bahan`
--
ALTER TABLE `menu_bahan`
  ADD CONSTRAINT `menu_bahan_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `masakan` (`id_menu`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_bahan_ibfk_2` FOREIGN KEY (`id_bahan`) REFERENCES `bahan_baku` (`id_bahan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `masakan` (`id_menu`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_detail_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `masakan` (`id_menu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
