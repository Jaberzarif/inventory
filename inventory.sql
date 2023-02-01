-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2023 at 07:50 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `category_ims`
--

CREATE TABLE `category_ims` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `category_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `category_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `category_ims`
--

INSERT INTO `category_ims` (`category_id`, `category_name`, `category_status`, `category_datetime`) VALUES
(7, 'Lego', 'Enable', '2023-02-01 06:26:24'),
(8, 'Puericultures', 'Enable', '2023-02-01 06:26:55'),
(9, 'Jeux d\'exterieur et de jardin', 'Enable', '2023-02-01 06:27:23'),
(10, 'Fille', 'Enable', '2023-02-01 06:28:06'),
(11, 'Garcon', 'Enable', '2023-02-01 06:28:24'),
(12, 'Animaux', 'Enable', '2023-02-01 06:28:43'),
(13, 'Jouet scientifique', 'Enable', '2023-02-01 06:30:56'),
(14, 'Jeux d\'imitation', 'Enable', '2023-02-01 06:31:20'),
(15, 'Dinette', 'Enable', '2023-02-01 06:32:30'),
(16, 'Jeux musical', 'Enable', '2023-02-01 06:32:50'),
(17, 'Maisons de poupee', 'Enable', '2023-02-01 06:33:01'),
(18, 'Robots', 'Enable', '2023-02-01 06:33:16'),
(19, 'Puzzle', 'Enable', '2023-02-01 06:33:29'),
(20, 'Jeux educatifs', 'Enable', '2023-02-01 06:33:58');

-- --------------------------------------------------------

--
-- Table structure for table `item_ims`
--

CREATE TABLE `item_ims` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `item_manufactured_by` int(11) NOT NULL,
  `item_category` int(11) NOT NULL,
  `item_available_quantity` int(11) NOT NULL,
  `item_location_rack` int(11) NOT NULL,
  `item_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `item_add_datetime` datetime NOT NULL,
  `item_update_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `item_ims`
--

INSERT INTO `item_ims` (`item_id`, `item_name`, `item_manufactured_by`, `item_category`, `item_available_quantity`, `item_location_rack`, `item_status`, `item_add_datetime`, `item_update_datetime`) VALUES
(2, 'Anchor Fastener SS', 1, 1, 1850, 1, 'Enable', '2022-05-10 17:24:39', '2022-05-10 17:41:41'),
(3, 'Stainless Steel Kicker Bolt 4 Inch', 1, 1, 2490, 1, 'Enable', '2022-05-13 16:10:22', '2022-05-13 16:10:22'),
(4, 'Self Drilling Screws', 9, 1, 1000, 1, 'Enable', '2022-05-14 15:47:19', '2022-05-14 15:47:19'),
(5, 'Machine Screws', 7, 1, 1000, 1, 'Enable', '2022-05-14 15:47:41', '2022-05-14 15:47:41'),
(6, 'Self Tapping Screws', 10, 1, 1000, 1, 'Enable', '2022-05-14 15:48:35', '2022-05-14 15:48:35'),
(7, 'Tapping Screw', 3, 1, 1000, 1, 'Enable', '2022-05-14 15:49:09', '2022-05-14 15:49:09'),
(8, 'Headless Screws', 4, 1, 1000, 1, 'Enable', '2022-05-14 15:49:30', '2022-05-14 15:49:30'),
(9, 'Wire Nails', 4, 2, 1000, 2, 'Enable', '2022-05-14 15:50:21', '2022-05-14 15:50:21'),
(10, 'Steel Nail', 8, 2, 1000, 2, 'Enable', '2022-05-14 15:50:43', '2022-05-14 15:50:43'),
(11, 'Stainless Steel Plain Wire Nails', 8, 2, 1000, 2, 'Enable', '2022-05-14 15:51:07', '2022-05-14 15:51:07'),
(12, 'Industrial Steel Wire Nails', 6, 2, 1000, 2, 'Enable', '2022-05-14 15:51:42', '2022-05-14 15:51:42'),
(13, 'S S U Nails', 5, 2, 1000, 2, 'Enable', '2022-05-14 15:52:26', '2022-05-14 15:52:26'),
(14, 'Hexagon Fit Bolts', 2, 3, 1000, 3, 'Enable', '2022-05-14 15:53:17', '2022-05-14 15:53:17'),
(15, 'Hex Flange Bolts', 9, 3, 990, 3, 'Enable', '2022-05-14 15:53:40', '2022-05-14 15:53:40'),
(16, 'High Tensile Bolt', 7, 3, 980, 3, 'Enable', '2022-05-14 15:54:00', '2022-05-14 15:54:00'),
(17, 'Metal Nuts', 10, 3, 1000, 3, 'Enable', '2022-05-14 15:54:43', '2022-05-14 15:54:43'),
(18, 'Pal Nuts', 3, 3, 1000, 3, 'Enable', '2022-05-14 15:55:03', '2022-05-14 15:55:03'),
(19, 'Indented Hex Washer Head', 3, 4, 1000, 4, 'Enable', '2022-05-14 15:56:02', '2022-05-14 15:56:02'),
(20, 'Medium Split Lock Washers', 4, 4, 1000, 4, 'Enable', '2022-05-14 15:56:26', '2022-05-14 15:56:26'),
(21, 'Plain Washer', 8, 4, 1000, 4, 'Enable', '2022-05-14 15:56:46', '2022-05-14 15:56:46'),
(22, 'Backup Washers', 8, 4, 1000, 4, 'Enable', '2022-05-14 15:57:08', '2022-05-14 15:57:08'),
(23, 'Mild Steel Washers', 6, 4, 1000, 4, 'Enable', '2022-05-14 15:57:28', '2022-05-14 15:57:28'),
(24, 'Anchor Bolts', 6, 5, 1000, 5, 'Enable', '2022-05-14 15:58:58', '2022-05-14 15:58:58'),
(25, 'Anchor Bolt Sleeve', 5, 5, 1000, 5, 'Enable', '2022-05-14 15:59:18', '2022-05-14 15:59:18'),
(26, 'Concrete Anchors', 2, 5, 1000, 5, 'Enable', '2022-05-14 15:59:39', '2022-05-14 15:59:39'),
(27, 'Sleeve Anchors', 9, 5, 1000, 5, 'Enable', '2022-05-14 16:00:02', '2022-05-14 16:00:02'),
(28, 'Anchor Nuts', 7, 5, 1000, 5, 'Enable', '2022-05-14 16:00:24', '2022-05-14 16:00:24'),
(29, 'Pop Rivets Fasteners', 7, 6, 1000, 6, 'Enable', '2022-05-14 16:05:31', '2022-05-14 16:05:31'),
(30, 'Avlock Interlock Rivet Fastener', 10, 6, 1000, 6, 'Enable', '2022-05-14 16:06:06', '2022-05-14 16:06:06'),
(31, 'Gi Pop Rivets Fasteners', 4, 6, 1000, 6, 'Enable', '2022-05-14 16:08:03', '2022-05-14 16:08:03'),
(32, 'Irrigation Rivet Fastener', 1, 6, 1000, 6, 'Enable', '2022-05-14 16:08:36', '2022-05-14 16:08:36'),
(33, 'Diana Rivet Fasteners', 6, 6, 1000, 6, 'Enable', '2022-05-14 16:09:02', '2022-05-14 16:09:02'),
(34, 'ACCESSOIRE CUISINE', 1, 15, 17, 6, 'Enable', '2023-02-01 06:41:15', '2023-02-01 06:41:24'),
(35, 'SUPER SAIYAN GOKU', 16, 11, 0, 6, 'Enable', '2023-02-01 06:52:37', '2023-02-01 06:52:37'),
(36, 'FORTNITE SCRATCH', 16, 11, 0, 6, 'Enable', '2023-02-01 06:53:02', '2023-02-01 06:53:02'),
(37, 'ACCESSOIRES BEAUTE POUR FILLE', 1, 10, 0, 5, 'Enable', '2023-02-01 06:53:22', '2023-02-01 06:53:22'),
(38, 'ACCESSOIRES POUR FILLE', 1, 10, 0, 5, 'Enable', '2023-02-01 06:53:50', '2023-02-01 06:53:50'),
(39, 'Art Creations MOBILE EN QUILLING - PAPILLONS', 1, 10, 0, 6, 'Enable', '2023-02-01 06:54:26', '2023-02-01 06:54:26'),
(40, 'ATELIER MANUCURE Corallienne', 1, 10, 0, 5, 'Enable', '2023-02-01 06:54:43', '2023-02-01 06:54:43'),
(41, 'BOMBES DE BAIN', 1, 10, 0, 6, 'Enable', '2023-02-01 06:55:09', '2023-02-01 06:55:09'),
(42, 'BIJOUX EN FOLIE NOUVEAU PACK', 1, 10, 0, 6, 'Enable', '2023-02-01 06:55:25', '2023-02-01 06:55:25'),
(43, 'BOUGIES PARFUMS NATURE', 1, 10, 0, 6, 'Enable', '2023-02-01 06:55:46', '2023-02-01 06:55:46'),
(44, 'CALICE AVEC POUPEE', 1, 10, 0, 6, 'Enable', '2023-02-01 06:56:05', '2023-02-01 06:56:05');

-- --------------------------------------------------------

--
-- Table structure for table `item_manufacuter_company_ims`
--

CREATE TABLE `item_manufacuter_company_ims` (
  `item_manufacuter_company_id` int(11) NOT NULL,
  `company_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `company_short_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `company_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `company_added_datetime` datetime NOT NULL,
  `company_updated_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `item_manufacuter_company_ims`
--

INSERT INTO `item_manufacuter_company_ims` (`item_manufacuter_company_id`, `company_name`, `company_short_name`, `company_status`, `company_added_datetime`, `company_updated_datetime`) VALUES
(1, 'Tunisie Jouets', 'TNJ', 'Enable', '2022-05-10 15:32:12', '2023-02-01 06:49:28'),
(11, 'Mytek', 'MYT', 'Enable', '2023-02-01 06:50:02', '2023-02-01 06:50:02'),
(12, 'Jumia', 'JUM', 'Enable', '2023-02-01 06:50:12', '2023-02-01 06:50:12'),
(13, 'Burago', 'BUR', 'Enable', '2023-02-01 06:51:01', '2023-02-01 06:51:01'),
(14, 'Nintendo', 'NIN', 'Enable', '2023-02-01 06:51:12', '2023-02-01 06:51:12'),
(15, 'Sony', 'SON', 'Enable', '2023-02-01 06:51:27', '2023-02-01 06:51:27'),
(16, 'Funko', 'FUN', 'Enable', '2023-02-01 06:51:40', '2023-02-01 06:51:40');

-- --------------------------------------------------------

--
-- Table structure for table `item_purchase_ims`
--

CREATE TABLE `item_purchase_ims` (
  `item_purchase_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `item_batch_no` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `item_purchase_qty` int(11) NOT NULL,
  `available_quantity` int(11) NOT NULL,
  `item_purchase_price_per_unit` decimal(12,2) NOT NULL,
  `item_purchase_total_cost` decimal(12,2) NOT NULL,
  `item_manufacture_month` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `item_manufacture_year` int(5) NOT NULL,
  `item_expired_month` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `item_expired_year` int(5) NOT NULL,
  `item_sale_price_per_unit` decimal(12,2) NOT NULL,
  `item_purchase_datetime` datetime NOT NULL,
  `item_purchase_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `item_purchase_enter_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `item_purchase_ims`
--

INSERT INTO `item_purchase_ims` (`item_purchase_id`, `item_id`, `supplier_id`, `item_batch_no`, `item_purchase_qty`, `available_quantity`, `item_purchase_price_per_unit`, `item_purchase_total_cost`, `item_manufacture_month`, `item_manufacture_year`, `item_expired_month`, `item_expired_year`, `item_sale_price_per_unit`, `item_purchase_datetime`, `item_purchase_status`, `item_purchase_enter_by`) VALUES
(1, 2, 1, 'AFSS88', 1000, 850, '1.50', '1500.00', '04', 2022, '12', 2025, '2.30', '2022-05-12 16:24:01', 'Enable', 1),
(2, 3, 1, 'SSKB4I', 2500, 2490, '10.25', '25625.00', '04', 2022, '03', 2026, '12.50', '2022-05-13 16:12:02', 'Enable', 1),
(3, 25, 1, 'ABSJAN2022', 1000, 1000, '20.00', '20000.00', '01', 2022, '12', 2025, '25.00', '2022-05-14 16:26:26', 'Enable', 1),
(4, 24, 5, 'ABFEB2022', 1000, 1000, '19.00', '19000.00', '02', 2022, '01', 2025, '24.00', '2022-05-14 16:27:42', 'Enable', 1),
(5, 2, 3, 'AFSMAR2022', 1000, 1000, '35.00', '35000.00', '03', 2022, '02', 2025, '40.00', '2022-05-14 16:28:42', 'Enable', 1),
(6, 28, 4, 'ANAP2022', 1000, 1000, '34.00', '34000.00', '04', 2022, '03', 2025, '39.00', '2022-05-14 16:29:29', 'Enable', 1),
(7, 30, 2, 'AIRFMY2022', 1000, 1000, '10.00', '10000.00', '05', 2022, '04', 2024, '13.00', '2022-05-14 16:30:35', 'Enable', 1),
(8, 22, 1, 'BWJAN2022', 1000, 1000, '0.50', '500.00', '01', 2022, '12', 2024, '0.75', '2022-05-14 16:31:51', 'Enable', 1),
(9, 26, 5, 'CAFAB2022', 1000, 1000, '25.00', '25000.00', '02', 2022, '01', 2025, '29.00', '2022-05-14 16:32:59', 'Enable', 1),
(10, 33, 3, 'DRFMAR2022', 1000, 1000, '5.00', '5000.00', '03', 2022, '02', 2025, '8.00', '2022-05-14 16:34:35', 'Enable', 1),
(11, 32, 4, 'IRFAPR2022', 1000, 1000, '5.00', '5000.00', '04', 2022, '03', 2025, '8.50', '2022-05-14 16:35:45', 'Enable', 1),
(12, 31, 2, 'GPRFMY2022', 1000, 1000, '8.00', '8000.00', '05', 2025, '04', 2025, '11.50', '2022-05-14 16:36:59', 'Enable', 1),
(13, 29, 1, 'PRFJAN2022', 1000, 1000, '1.30', '1300.00', '01', 2022, '12', 2024, '1.50', '2022-05-14 16:38:08', 'Enable', 1),
(14, 27, 5, 'SAMY2022', 1000, 1000, '13.00', '13000.00', '05', 2022, '04', 2025, '17.00', '2022-05-14 16:39:34', 'Enable', 1),
(15, 23, 5, 'MSWJAN2022', 1000, 1000, '5.00', '5000.00', '01', 2022, '12', 2024, '7.50', '2022-05-14 16:40:59', 'Enable', 1),
(16, 21, 4, 'PWFEB2022', 1000, 1000, '1.00', '1000.00', '02', 2022, '01', 2025, '1.35', '2022-05-14 16:42:10', 'Enable', 1),
(17, 20, 4, 'MSLWMAR2022', 1000, 1000, '1.75', '1750.00', '03', 2022, '02', 2025, '2.15', '2022-05-14 16:43:26', 'Enable', 1),
(18, 19, 2, 'IHWHMY2022', 1000, 1000, '12.00', '12000.00', '05', 2025, '04', 2025, '15.75', '2022-05-14 16:44:52', 'Enable', 1),
(19, 18, 1, 'PNJAN2022', 1000, 1000, '2.10', '2100.00', '01', 2022, '12', 2024, '2.65', '2022-05-14 16:45:48', 'Enable', 1),
(20, 17, 5, 'MNMAR2022', 1000, 1000, '5.00', '5000.00', '03', 2022, '02', 2025, '6.15', '2022-05-14 16:47:15', 'Enable', 1),
(21, 16, 4, 'HTBJAN2022', 1000, 980, '12.00', '12000.00', '01', 2022, '12', 2024, '15.65', '2022-05-14 16:53:21', 'Enable', 1),
(22, 15, 2, 'HFBFEB2022', 1000, 990, '5.00', '5000.00', '02', 2022, '01', 2025, '6.35', '2022-05-14 16:55:25', 'Enable', 1),
(23, 14, 1, 'HFBMAR2022', 1000, 1000, '15.00', '15000.00', '03', 2022, '02', 2024, '18.00', '2022-05-14 16:56:25', 'Enable', 1),
(24, 13, 5, 'SSUNAPR2022', 1000, 1000, '2.75', '2750.00', '04', 2022, '03', 2025, '3.35', '2022-05-14 16:58:11', 'Enable', 1),
(25, 12, 3, 'ISWNMY2022', 1000, 1000, '0.50', '500.00', '05', 2022, '04', 2025, '0.65', '2022-05-14 16:59:14', 'Enable', 1),
(26, 11, 4, 'SSPWNJAN2022', 1000, 1000, '0.60', '600.00', '01', 2022, '12', 2025, '0.75', '2022-05-14 17:00:08', 'Enable', 1),
(27, 10, 2, 'SNFEB2022', 1000, 1000, '0.80', '800.00', '02', 2022, '01', 2025, '1.15', '2022-05-14 17:01:38', 'Enable', 1),
(28, 9, 1, 'WNAPR2022', 1000, 1000, '0.25', '250.00', '04', 2022, '03', 2025, '0.40', '2022-05-14 17:02:41', 'Enable', 1),
(29, 8, 5, 'HSMY2022', 1000, 1000, '1.30', '1300.00', '05', 2022, '04', 2025, '1.55', '2022-05-14 17:04:01', 'Enable', 1),
(30, 7, 4, 'PSJAN2022', 1000, 1000, '5.50', '5500.00', '01', 2022, '12', 2024, '6.25', '2022-05-14 17:05:00', 'Enable', 1),
(31, 6, 2, 'STSFEB2022', 1000, 1000, '2.50', '2500.00', '02', 2022, '01', 2025, '3.10', '2022-05-14 17:06:30', 'Enable', 1),
(32, 5, 1, 'MSMAR2022', 1000, 1000, '50.00', '50000.00', '03', 2022, '02', 2025, '58.00', '2022-05-14 17:07:52', 'Enable', 1),
(33, 4, 4, 'SDSMY2022', 1000, 1000, '2.10', '2100.00', '05', 2022, '04', 2025, '2.35', '2022-05-14 17:10:24', 'Enable', 1),
(34, 34, 1, '12345', 22, 17, '45.00', '990.00', '01', 2023, '01', 2023, '50.00', '2023-02-01 06:57:23', 'Enable', 1);

-- --------------------------------------------------------

--
-- Table structure for table `location_rack_ims`
--

CREATE TABLE `location_rack_ims` (
  `location_rack_id` int(11) NOT NULL,
  `location_rack_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `location_rack_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `location_rack_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `location_rack_ims`
--

INSERT INTO `location_rack_ims` (`location_rack_id`, `location_rack_name`, `location_rack_status`, `location_rack_datetime`) VALUES
(1, 'Tozeur - Tunisie', 'Enable', '2022-05-10 15:20:37'),
(2, 'Gafsa - Tunisie', 'Enable', '2022-05-14 15:23:02'),
(3, 'Sfax - Tunisie', 'Enable', '2022-05-14 15:23:22'),
(4, 'Sousse - Tunis', 'Enable', '2022-05-14 15:23:33'),
(5, 'Nabeul - Tunisie', 'Enable', '2022-05-14 15:29:52'),
(6, 'Tunis - Tunisie', 'Enable', '2022-05-14 15:30:18');

-- --------------------------------------------------------

--
-- Table structure for table `order_ims`
--

CREATE TABLE `order_ims` (
  `order_id` int(11) NOT NULL,
  `buyer_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_total_amount` decimal(12,2) NOT NULL,
  `order_created_by` int(11) NOT NULL,
  `order_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `order_added_on` datetime NOT NULL,
  `order_updated_on` datetime NOT NULL,
  `order_tax_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `order_tax_percentage` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `order_ims`
--

INSERT INTO `order_ims` (`order_id`, `buyer_name`, `order_total_amount`, `order_created_by`, `order_status`, `order_added_on`, `order_updated_on`, `order_tax_name`, `order_tax_percentage`) VALUES
(3, 'Sami Dridi', '407.10', 1, 'Enable', '2022-05-13 15:52:26', '2023-02-01 19:43:59', 'CGST, SGST', '9.00, 9.00'),
(5, 'Mohamed Ali', '149.57', 1, 'Enable', '2022-05-13 16:23:40', '2023-02-01 19:43:44', 'CGST, SGST', '9.00, 9.00'),
(6, 'Sami Bougtef', '444.27', 1, 'Enable', '2022-05-14 17:12:22', '2023-02-01 19:43:32', 'CGST, SGST', '9.00, 9.00'),
(7, 'Moez Chebbi', '59.00', 1, 'Enable', '2023-02-01 06:58:13', '2023-02-01 06:58:13', 'TVA', '18.00');

-- --------------------------------------------------------

--
-- Table structure for table `order_item_ims`
--

CREATE TABLE `order_item_ims` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_purchase_id` int(11) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `item_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `order_item_ims`
--

INSERT INTO `order_item_ims` (`order_item_id`, `order_id`, `item_id`, `item_purchase_id`, `item_quantity`, `item_price`) VALUES
(4, 0, 2, 1, 100, '2.30'),
(6, 3, 2, 1, 150, '2.30'),
(10, 5, 3, 2, 10, '12.50'),
(11, 6, 16, 21, 20, '15.65'),
(12, 6, 15, 22, 10, '6.35'),
(13, 7, 34, 34, 5, '50.00');

-- --------------------------------------------------------

--
-- Table structure for table `store_ims`
--

CREATE TABLE `store_ims` (
  `store_id` int(11) NOT NULL,
  `store_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `store_address` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `store_contact_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `store_email_address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `store_timezone` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `store_currency` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `store_added_on` datetime NOT NULL,
  `store_updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `store_ims`
--

INSERT INTO `store_ims` (`store_id`, `store_name`, `store_address`, `store_contact_no`, `store_email_address`, `store_timezone`, `store_currency`, `store_added_on`, `store_updated_on`) VALUES
(1, 'coin de jouets', 'Tunisie', '12345678900', 'jaber.zarif@gmail.com', 'Africa/Algiers', 'EUR', '2023-02-01 06:23:46', '2023-02-01 06:23:46');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_ims`
--

CREATE TABLE `supplier_ims` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_address` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `supplier_contact_no` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `supplier_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `supplier_ims`
--

INSERT INTO `supplier_ims` (`supplier_id`, `supplier_name`, `supplier_address`, `supplier_contact_no`, `supplier_email`, `supplier_status`, `supplier_datetime`) VALUES
(1, 'Jouet Gold', 'Tunis', '9632574531', 'jouet.gold@gmail.com', 'Enable', '2022-05-10 16:10:26'),
(2, 'Accessoire Tunisie', 'Tunis', '8521479630', 'at@gmail.com', 'Enable', '2022-05-14 15:37:53'),
(3, 'Lego House', 'Tunis', '8539517520', 'lego@gmail.com', 'Enable', '2022-05-14 15:38:42'),
(4, 'Mytek', 'Tunis', '7539518520', 'Mytek@gmail.com', 'Enable', '2022-05-14 15:39:36'),
(5, 'Jumia', 'Tunis', '9517538630', 'jumia@gmail.com', 'Enable', '2022-05-14 15:40:29');

-- --------------------------------------------------------

--
-- Table structure for table `tax_ims`
--

CREATE TABLE `tax_ims` (
  `tax_id` int(11) NOT NULL,
  `tax_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `tax_percentage` decimal(4,2) NOT NULL,
  `tax_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `tax_added_on` datetime NOT NULL,
  `tax_updated_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tax_ims`
--

INSERT INTO `tax_ims` (`tax_id`, `tax_name`, `tax_percentage`, `tax_status`, `tax_added_on`, `tax_updated_on`) VALUES
(2, 'TVA', '18.00', 'Enable', '2022-05-10 18:29:44', '2023-02-01 06:41:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_ims`
--

CREATE TABLE `user_ims` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_type` enum('Master','User') COLLATE utf8_unicode_ci NOT NULL,
  `user_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `user_created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_ims`
--

INSERT INTO `user_ims` (`user_id`, `user_name`, `user_email`, `user_password`, `user_type`, `user_status`, `user_created_on`) VALUES
(1, 'Sihem Hadj Bougtef', 'sihemhadjbougtefgmail.com', 'admin', 'Master', 'Enable', '2023-02-01 06:19:15'),
(2, 'Test Account', 'test@gmail.com', '12345678900', 'User', 'Enable', '2023-02-01 07:07:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category_ims`
--
ALTER TABLE `category_ims`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `item_ims`
--
ALTER TABLE `item_ims`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `item_manufacuter_company_ims`
--
ALTER TABLE `item_manufacuter_company_ims`
  ADD PRIMARY KEY (`item_manufacuter_company_id`);

--
-- Indexes for table `item_purchase_ims`
--
ALTER TABLE `item_purchase_ims`
  ADD PRIMARY KEY (`item_purchase_id`);

--
-- Indexes for table `location_rack_ims`
--
ALTER TABLE `location_rack_ims`
  ADD PRIMARY KEY (`location_rack_id`);

--
-- Indexes for table `order_ims`
--
ALTER TABLE `order_ims`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_item_ims`
--
ALTER TABLE `order_item_ims`
  ADD PRIMARY KEY (`order_item_id`);

--
-- Indexes for table `store_ims`
--
ALTER TABLE `store_ims`
  ADD PRIMARY KEY (`store_id`);

--
-- Indexes for table `supplier_ims`
--
ALTER TABLE `supplier_ims`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `tax_ims`
--
ALTER TABLE `tax_ims`
  ADD PRIMARY KEY (`tax_id`);

--
-- Indexes for table `user_ims`
--
ALTER TABLE `user_ims`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category_ims`
--
ALTER TABLE `category_ims`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `item_ims`
--
ALTER TABLE `item_ims`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `item_manufacuter_company_ims`
--
ALTER TABLE `item_manufacuter_company_ims`
  MODIFY `item_manufacuter_company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `item_purchase_ims`
--
ALTER TABLE `item_purchase_ims`
  MODIFY `item_purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `location_rack_ims`
--
ALTER TABLE `location_rack_ims`
  MODIFY `location_rack_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_ims`
--
ALTER TABLE `order_ims`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_item_ims`
--
ALTER TABLE `order_item_ims`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `store_ims`
--
ALTER TABLE `store_ims`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier_ims`
--
ALTER TABLE `supplier_ims`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tax_ims`
--
ALTER TABLE `tax_ims`
  MODIFY `tax_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_ims`
--
ALTER TABLE `user_ims`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
