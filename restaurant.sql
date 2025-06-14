-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 09:24 AM
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
-- Database: `restaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `nama`, `deskripsi`, `harga`, `kategori`, `gambar`, `status`) VALUES
(10, 'Tahu Campur', 'Makanan Khas Jawa Timur yang terdiri dari irisan lontong, tahu goreng, lendho (perkedel singkong, mie kuning, taoge segar dan selada, di siram dengan kuah dari rempah rempah yang di campur dengan bumbu petis khas warung tombo kangen, dan dilengkapi dengan kerupuk', 22000, 'makanan', 'Tahu Campur 1.jpg', 'tersedia'),
(11, 'Tahu Telur', 'Makanan Khas Jawa Timur yang terdiri dari irisan lontong yang di padukan dengan telur yang di dadar dengan tahu, touge, dan di siram dengan bumbu kacang yang telah diulek bersama petis khas warung tombo kangen dan dilengkapi dengan kerupuk\r\n', 18000, 'makanan', 'Tahu Telur 1.jpg', 'tersedia'),
(12, 'Tahu Thek Thek', 'Makanan Khas Jawa Timur yang terdiri dari irisan lontong yang di padukan dengan potongan tahu dan kentang goreng serta telur rebus, dan touge kemudian di siram dengan bumbu kacang yang telah diulek bersama petis khas warung tombo kangen dan dilengkapi dengan kerupuk\r\n', 18000, 'makanan', 'Tahu Thek Thek 1.jpg', 'tersedia'),
(13, 'Rujak Cingur', 'Makanan Khas Jawa Timur yang terdiri dari irisan lontong, tahu, tempe, timun serta sayuran kangkung dan touge, dilengkapi dengan irisan cingur sapi dan di siram dengan bumbu petis khas warung tombo kangen dan dilengkapi dengan kerupuk', 28000, 'makanan', 'vecteezy_a-serving-of-typical-indonesian-food-known-as-rujak-cingur_47195424.jpg', 'tersedia'),
(14, 'Soto Ayam Suroboyo', 'Soto ayam kuah bening yang di padukan dengan kol, soun, touge segar, serta irisan ayam dan koyah khas warung tombo kangen\r\n', 13000, 'makanan', 'soto-is-a-typical-indonesian-soup-dish-consisting-of-meat_45734789.jpeg', 'tidak_tersedia'),
(15, 'Rawon', 'Sop daging berkuah hitam dengan campuran bumbu khas yang menggunakan kluwek, dipadukan dengan taburan taoge dan bawang goreng', 22000, 'makanan', 'Rawon 1.jpg', 'tersedia'),
(16, 'Nasi Putih', 'Nasi Putih (Nasi Saja)', 5000, 'makanan', 'nasi.jpg', 'tersedia'),
(18, 'Es Teh Manis', 'Es teh manis', 7000, 'minuman', 'esteh.jpg', 'tersedia'),
(19, 'Es Jeruk', 'Es jeruk manis', 8000, 'minuman', 'es jeruk.jpg', 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `waktu` time NOT NULL DEFAULT current_timestamp(),
  `tanggal` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `metode_pembayaran`, `bukti_pembayaran`, `waktu`, `tanggal`, `status`) VALUES
(19, 7, 8, 'Bayar Di Tempat', NULL, '17:35:54', '2025-01-15', 'Selesai'),
(22, 9, 30, 'Bayar Di Tempat', NULL, '16:21:38', '2025-01-19', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `menu_id`, `jumlah`, `subtotal`) VALUES
(7, 19, 19, 1, 8000),
(10, 22, 15, 1, 22000),
(11, 22, 19, 1, 8000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `no_telp`, `role`) VALUES
(1, 'uqi', 'uqi@gmail.com', '$2y$10$zZyvKKg7xd1U25pkg7ZRFu62EUIO6AcIX8/Nr9P3OZUl0CHndJ4da', '', 'Admin'),
(7, 'Daven', 'davenwu12@gmail.com', '$2y$10$dsk7HXd7bpfGwbf8WlQq9.hOnkSKEdLo7gZlyvVoFe2pJDpNasynS', '081323234141', 'User'),
(9, 'Yoga ', 'yoga05@gmail.com', '$2y$10$NzYyDdgDnxmgH1VuZn1SW.zZTG3LcYTHib0Oh9AQfug5SnrmAXXCS', '081280854676', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
