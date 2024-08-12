-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 23, 2024 at 11:03 PM
-- Server version: 10.3.39-MariaDB-cll-lve
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aris5618_digipayid`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_journal_finance`
--

CREATE TABLE `admin_journal_finance` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `id_user` int(11) DEFAULT 0,
  `id_user_parent` int(11) DEFAULT 0,
  `amount_credit` int(11) NOT NULL DEFAULT 0,
  `amount_debet` int(11) NOT NULL DEFAULT 0,
  `accounting_type` mediumint(9) NOT NULL DEFAULT 0,
  `id_payment_method` smallint(6) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_journal_finance`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_journal_finance_40`
--

CREATE TABLE `app_journal_finance_40` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `amount_credit` int(11) NOT NULL DEFAULT 0,
  `amount_debet` int(11) NOT NULL DEFAULT 0,
  `accounting_type` smallint(4) NOT NULL DEFAULT 0,
  `id_payment_method` smallint(6) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_journal_finance_40`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_journal_finance_93`
--

CREATE TABLE `app_journal_finance_93` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `amount_credit` int(11) NOT NULL DEFAULT 0,
  `amount_debet` int(11) NOT NULL DEFAULT 0,
  `accounting_type` smallint(4) NOT NULL DEFAULT 0,
  `id_payment_method` smallint(6) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_payment_method_40`
--

CREATE TABLE `app_payment_method_40` (
  `id_payment_method` int(11) NOT NULL,
  `fee_on_merchant` tinyint(4) NOT NULL DEFAULT 0,
  `fee_app` int(11) NOT NULL DEFAULT 0,
  `fee_app_percent` float NOT NULL DEFAULT 0,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_payment_method_40`
--

INSERT INTO `app_payment_method_40` (`id_payment_method`, `fee_on_merchant`, `fee_app`, `fee_app_percent`, `is_active`, `created_at`, `updated_at`, `updated_by`) VALUES
(0, 0, 500, 0, 1, '2024-07-07 15:23:56', '2024-07-07 15:23:56', 0),
(1, 0, 800, 0, 1, '2024-03-18 13:45:35', '2024-03-18 13:45:35', 0),
(2, 0, 500, 0, 1, '2024-03-18 13:47:07', '2024-03-18 13:47:07', 0),
(3, 0, 500, 0, 1, '2024-03-18 13:48:12', '2024-03-18 13:48:12', 0),
(4, 0, 500, 0, 1, '2024-03-18 13:48:52', '2024-03-18 13:48:52', 0),
(5, 0, 500, 0, 1, '2024-03-18 13:49:18', '2024-03-18 13:49:18', 0),
(6, 0, 500, 0, 0, '2024-03-18 13:50:07', '2024-03-18 13:50:07', 0),
(7, 0, 500, 0, 1, '2024-03-18 13:50:52', '2024-03-18 13:50:52', 0),
(8, 0, 500, 0, 1, '2024-03-18 13:52:23', '2024-03-18 13:52:23', 0),
(9, 0, 500, 0, 1, '2024-03-18 13:52:57', '2024-03-18 13:52:57', 0),
(10, 1, 500, 0, 1, '2024-03-18 13:53:54', '2024-03-18 13:53:54', 0),
(11, 0, 500, 0.5, 1, '2024-03-18 13:58:35', '2024-03-18 13:58:35', 0),
(12, 0, 500, 0.5, 1, '2024-03-18 14:00:08', '2024-03-18 14:00:08', 0),
(13, 0, 500, 0.5, 1, '2024-03-18 14:00:43', '2024-03-18 14:00:43', 0),
(14, 0, 500, 0.5, 1, '2024-03-18 14:01:18', '2024-03-18 14:01:18', 0),
(15, 0, 500, 0.5, 1, '2024-03-18 14:02:05', '2024-03-18 14:02:05', 0),
(16, 0, 500, 0.5, 1, '2024-03-18 14:02:25', '2024-03-18 14:02:25', 0),
(17, 0, 500, 1, 1, '2024-03-18 14:03:01', '2024-03-18 14:03:01', 0),
(18, 0, 500, 1.3, 1, '2024-03-18 14:04:03', '2024-03-18 14:04:03', 0),
(19, 0, 400, 0.3, 1, '2024-03-18 14:04:36', '2024-03-18 14:04:36', 0),
(20, 0, 500, 3, 1, '2024-03-18 14:05:27', '2024-03-18 14:05:27', 0),
(21, 0, 500, 5, 1, '2024-03-18 14:06:01', '2024-03-18 14:06:01', 0),
(22, 0, 500, 5, 1, '2024-03-18 14:06:19', '2024-03-18 14:06:19', 0),
(23, 0, 500, 5, 1, '2024-03-18 14:06:41', '2024-03-18 14:06:41', 0),
(24, 0, 500, 0, 1, '2024-03-18 14:07:18', '2024-03-18 14:07:18', 0),
(25, 0, 500, 0, 1, '2024-03-18 14:07:34', '2024-03-18 14:07:34', 0),
(26, 0, 500, 0, 1, '2024-04-04 15:38:21', '2024-04-04 15:38:21', 0),
(27, 0, 500, 0, 1, '2024-04-04 15:39:36', '2024-04-04 15:39:36', 0),
(28, 0, 500, 0, 1, '2024-04-04 15:40:08', '2024-04-04 15:40:08', 0),
(29, 0, 500, 0, 1, '2024-04-04 15:41:36', '2024-04-04 15:41:36', 0),
(30, 0, 500, 0, 1, '2024-04-04 15:45:18', '2024-04-04 15:45:18', 0),
(31, 0, 500, 0, 1, '2024-04-04 15:47:38', '2024-04-04 15:47:38', 0),
(32, 0, 500, 0, 1, '2024-04-04 15:50:47', '2024-04-04 15:50:47', 0),
(33, 0, 500, 0, 1, '2024-04-04 15:51:53', '2024-04-04 15:51:53', 0),
(34, 0, 500, 0, 1, '2024-04-04 16:02:37', '2024-04-04 16:02:37', 0),
(35, 0, 500, 1, 1, '2024-05-12 22:59:22', '2024-05-12 22:59:22', 0),
(36, 0, 500, 1.8, 1, '2024-05-12 22:59:22', '2024-05-12 22:59:22', 0),
(37, 0, 500, 5, 1, '2024-07-02 22:55:51', '2024-07-02 22:55:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_payment_method_93`
--

CREATE TABLE `app_payment_method_93` (
  `id_payment_method` int(11) NOT NULL DEFAULT 0,
  `fee_on_merchant` tinyint(4) NOT NULL DEFAULT 0,
  `fee_app` int(11) NOT NULL DEFAULT 0,
  `fee_app_percent` float NOT NULL DEFAULT 0,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_payment_method_93`
--

INSERT INTO `app_payment_method_93` (`id_payment_method`, `fee_on_merchant`, `fee_app`, `fee_app_percent`, `is_active`, `created_at`, `updated_at`, `updated_by`) VALUES
(0, 0, 500, 0, 1, '2024-07-10 14:19:31', '2024-07-10 14:19:31', 0),
(1, 0, 800, 0, 1, '2024-03-18 13:45:35', '2024-03-18 13:45:35', 0),
(2, 0, 500, 0, 1, '2024-03-18 13:47:07', '2024-03-18 13:47:07', 0),
(3, 0, 500, 0, 1, '2024-03-18 13:48:12', '2024-03-18 13:48:12', 0),
(4, 0, 500, 0, 1, '2024-03-18 13:48:52', '2024-03-18 13:48:52', 0),
(5, 0, 500, 0, 1, '2024-03-18 13:49:18', '2024-03-18 13:49:18', 0),
(6, 0, 500, 0, 0, '2024-03-18 13:50:07', '2024-03-18 13:50:07', 0),
(7, 0, 500, 0, 1, '2024-03-18 13:50:52', '2024-03-18 13:50:52', 0),
(8, 0, 500, 0, 1, '2024-03-18 13:52:23', '2024-03-18 13:52:23', 0),
(9, 0, 500, 0, 1, '2024-03-18 13:52:57', '2024-03-18 13:52:57', 0),
(10, 0, 500, 0, 1, '2024-03-18 13:53:54', '2024-03-18 13:53:54', 0),
(11, 0, 500, 0.5, 1, '2024-03-18 13:58:35', '2024-03-18 13:58:35', 0),
(12, 0, 500, 0.5, 1, '2024-03-18 14:00:08', '2024-03-18 14:00:08', 0),
(13, 0, 500, 0.5, 1, '2024-03-18 14:00:43', '2024-03-18 14:00:43', 0),
(14, 0, 500, 0.5, 1, '2024-03-18 14:01:18', '2024-03-18 14:01:18', 0),
(15, 0, 500, 0.5, 1, '2024-03-18 14:02:05', '2024-03-18 14:02:05', 0),
(16, 0, 500, 0.5, 1, '2024-03-18 14:02:25', '2024-03-18 14:02:25', 0),
(17, 0, 500, 1, 1, '2024-03-18 14:03:01', '2024-03-18 14:03:01', 0),
(18, 0, 500, 1.3, 1, '2024-03-18 14:04:03', '2024-03-18 14:04:03', 0),
(19, 1, 400, 0.3, 1, '2024-03-18 14:04:36', '2024-03-18 14:04:36', 0),
(20, 0, 500, 3, 1, '2024-03-18 14:05:27', '2024-03-18 14:05:27', 0),
(21, 0, 500, 5, 1, '2024-03-18 14:06:01', '2024-03-18 14:06:01', 0),
(22, 0, 500, 5, 1, '2024-03-18 14:06:19', '2024-03-18 14:06:19', 0),
(23, 0, 500, 5, 1, '2024-03-18 14:06:41', '2024-03-18 14:06:41', 0),
(24, 0, 500, 0, 1, '2024-03-18 14:07:18', '2024-03-18 14:07:18', 0),
(25, 0, 500, 0, 1, '2024-03-18 14:07:34', '2024-03-18 14:07:34', 0),
(26, 0, 500, 0, 1, '2024-04-04 15:38:21', '2024-04-04 15:38:21', 0),
(27, 0, 500, 0, 1, '2024-04-04 15:39:36', '2024-04-04 15:39:36', 0),
(28, 0, 500, 0, 1, '2024-04-04 15:40:08', '2024-04-04 15:40:08', 0),
(29, 0, 500, 0, 1, '2024-04-04 15:41:36', '2024-04-04 15:41:36', 0),
(30, 0, 500, 0, 1, '2024-04-04 15:45:18', '2024-04-04 15:45:18', 0),
(31, 0, 500, 0, 1, '2024-04-04 15:47:38', '2024-04-04 15:47:38', 0),
(32, 0, 500, 0, 1, '2024-04-04 15:50:47', '2024-04-04 15:50:47', 0),
(33, 0, 500, 0, 1, '2024-04-04 15:51:53', '2024-04-04 15:51:53', 0),
(34, 0, 500, 0, 1, '2024-04-04 16:02:37', '2024-04-04 16:02:37', 0),
(35, 0, 500, 1, 1, '2024-05-12 22:59:22', '2024-05-12 22:59:22', 0),
(36, 0, 500, 1.8, 1, '2024-05-12 22:59:22', '2024-05-12 22:59:22', 0),
(37, 0, 500, 5, 1, '2024-07-02 22:55:51', '2024-07-02 22:55:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_product_40`
--

CREATE TABLE `app_product_40` (
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_product_40`
--

INSERT INTO `app_product_40` (`id_product`, `product_type`, `product_code`, `product_barcode`, `product_category_id`, `product_image_url`, `product_name`, `product_qty`, `product_price`, `product_discount`, `product_status`, `product_created_at`, `product_updated_at`) VALUES
(1, 0, 'DIGITAL001', 'X1', 1, '/images/1719916257_bf65b087913f64420190.jpg', 'Produk Digital Sample', 9999, 100, 0, 1, '2024-03-21 16:32:47', '0000-00-00 00:00:00'),
(2, 1, 'UTAMA001', 'X2', 1, '/images/1719916272_ec78e51e107ab1563afa.jpg', 'Produk Fisik Sample', 9999, 4000, 0, 1, '2024-03-21 16:32:47', '0000-00-00 00:00:00'),
(10, 0, 'DIGITAL002', 'X3', 1, '/images/1719916358_4976eaa54779180a87cd.jpg', 'OO', 9999, 2500, 0, 1, '2024-03-21 16:32:47', '0000-00-00 00:00:00'),
(15, 0, 'xxx', 'xxx', 0, '/images/1719916126_c89942f6c8d9728d1fb8.jpg', 'XXX', 9999, 1000, 0, 1, '2024-07-02 16:58:11', '0000-00-00 00:00:00'),
(16, 0, 'x1', 'x1', 0, '/images/1719916378_dc76d813f856083d09fa.jpg', 'x1', 9999, 100, 0, 1, '2024-07-02 17:32:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `app_product_93`
--

CREATE TABLE `app_product_93` (
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_product_category_40`
--

CREATE TABLE `app_product_category_40` (
  `id_product_category` int(11) NOT NULL,
  `product_category` varchar(150) DEFAULT NULL,
  `product_category_updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_product_category_40`
--

INSERT INTO `app_product_category_40` (`id_product_category`, `product_category`, `product_category_updated_at`) VALUES
(1, 'Utama', '2024-03-21 16:31:04'),
(2, 'Tambahan', '2024-03-21 16:31:04'),
(3, 'Cadangan', '2024-04-05 17:15:23'),
(8, '', '2024-05-31 13:08:17');

-- --------------------------------------------------------

--
-- Table structure for table `app_product_category_93`
--

CREATE TABLE `app_product_category_93` (
  `id_product_category` int(11) NOT NULL,
  `product_category` varchar(150) DEFAULT NULL,
  `product_category_updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_transactions_40`
--

CREATE TABLE `app_transactions_40` (
  `id_transaction` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `id_user` int(11) NOT NULL DEFAULT 0,
  `id_customer` varchar(255) DEFAULT NULL,
  `email_customer` varchar(255) DEFAULT NULL,
  `wa_customer` varchar(20) DEFAULT '62',
  `total_product` int(11) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL DEFAULT 0,
  `amount_to_pay` int(11) NOT NULL DEFAULT 0,
  `amount_to_back` int(11) NOT NULL DEFAULT 0,
  `amount_to_receive` int(11) DEFAULT 0,
  `amount_tax` int(11) NOT NULL DEFAULT 0,
  `tax_percentage` float NOT NULL DEFAULT 0,
  `pg_fee` int(11) NOT NULL DEFAULT 0,
  `app_fee` int(11) NOT NULL DEFAULT 0,
  `fee` int(11) NOT NULL DEFAULT 0,
  `fee_on_merchant` tinyint(4) NOT NULL DEFAULT 0,
  `time_transaction` datetime NOT NULL DEFAULT current_timestamp(),
  `time_transaction_success` datetime DEFAULT NULL,
  `time_transaction_failed` datetime DEFAULT NULL,
  `note_transaction` varchar(100) DEFAULT NULL,
  `status_transaction` int(11) NOT NULL DEFAULT 0,
  `id_payment_method` tinyint(4) NOT NULL DEFAULT 0,
  `payment_method_code` varchar(20) DEFAULT 'CASH',
  `payment_method_name` varchar(50) DEFAULT NULL,
  `external_id` varchar(55) DEFAULT NULL,
  `url_file_billing` text DEFAULT NULL,
  `url_file_receipt` text DEFAULT NULL,
  `payment_response` text DEFAULT NULL,
  `status_payment` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_transactions_40`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_transactions_93`
--

CREATE TABLE `app_transactions_93` (
  `id_transaction` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `id_user` int(11) NOT NULL DEFAULT 0,
  `id_customer` varchar(255) DEFAULT NULL,
  `email_customer` varchar(255) DEFAULT NULL,
  `wa_customer` varchar(20) DEFAULT '62',
  `total_product` int(11) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL DEFAULT 0,
  `amount_to_pay` int(11) NOT NULL DEFAULT 0,
  `amount_to_back` int(11) NOT NULL DEFAULT 0,
  `amount_to_receive` int(11) DEFAULT 0,
  `amount_tax` int(11) NOT NULL DEFAULT 0,
  `tax_percentage` float NOT NULL DEFAULT 0,
  `pg_fee` int(11) NOT NULL DEFAULT 0,
  `app_fee` int(11) NOT NULL DEFAULT 0,
  `fee` int(11) NOT NULL DEFAULT 0,
  `fee_on_merchant` tinyint(4) NOT NULL DEFAULT 0,
  `time_transaction` datetime NOT NULL DEFAULT current_timestamp(),
  `time_transaction_success` datetime DEFAULT NULL,
  `time_transaction_failed` datetime DEFAULT NULL,
  `note_transaction` varchar(100) DEFAULT NULL,
  `status_transaction` int(11) NOT NULL DEFAULT 0,
  `id_payment_method` tinyint(4) NOT NULL DEFAULT 0,
  `payment_method_code` varchar(20) DEFAULT 'CASH',
  `payment_method_name` varchar(50) DEFAULT NULL,
  `external_id` varchar(55) DEFAULT NULL,
  `url_file_billing` text DEFAULT NULL,
  `url_file_receipt` text DEFAULT NULL,
  `payment_response` text DEFAULT NULL,
  `status_payment` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_transaction_products_40`
--

CREATE TABLE `app_transaction_products_40` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `total_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_transaction_products_40`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_transaction_products_93`
--

CREATE TABLE `app_transaction_products_93` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `total_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_transaction_products_temp_40`
--

CREATE TABLE `app_transaction_products_temp_40` (
  `id` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_transaction_products_temp_93`
--

CREATE TABLE `app_transaction_products_temp_93` (
  `id` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_users`
--

CREATE TABLE `app_users` (
  `id_user` int(11) NOT NULL,
  `id_user_parent` int(11) NOT NULL DEFAULT 0,
  `merchant_name` varchar(50) DEFAULT NULL,
  `merchant_address` varchar(255) DEFAULT NULL,
  `merchant_wa` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `telp_country_code` varchar(5) NOT NULL DEFAULT '+62',
  `telp` varchar(17) NOT NULL,
  `bank_short_name` varchar(15) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_account` varchar(35) DEFAULT NULL,
  `bank_account_name` varchar(50) DEFAULT NULL,
  `tax_percentage` float NOT NULL DEFAULT 0,
  `token_login` varchar(255) DEFAULT NULL,
  `token_api` varchar(255) DEFAULT NULL,
  `webhook_url` text DEFAULT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp(),
  `last_ip_address` varchar(25) DEFAULT NULL,
  `last_ip_location` varchar(255) DEFAULT NULL,
  `user_role` int(11) NOT NULL DEFAULT 2,
  `user_privilege` tinyint(4) NOT NULL DEFAULT 5,
  `user_status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `otp_email` int(8) DEFAULT NULL,
  `otp_wa` int(8) DEFAULT NULL,
  `is_verified` tinyint(4) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_users`
--

INSERT INTO `app_users` (`id_user`, `id_user_parent`, `merchant_name`, `merchant_address`, `merchant_wa`, `username`, `email`, `password`, `telp_country_code`, `telp`, `bank_short_name`, `bank_name`, `bank_account`, `bank_account_name`, `tax_percentage`, `token_login`, `token_api`, `webhook_url`, `last_login`, `last_ip_address`, `last_ip_location`, `user_role`, `user_privilege`, `user_status`, `created_at`, `updated_at`, `otp_email`, `otp_wa`, `is_verified`, `is_active`) VALUES
(1, 0, NULL, NULL, '081290383389', 'Admin X', 'admin@digipayid.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '81290383389', NULL, NULL, NULL, NULL, 0, '842715455686be795724623346039a82c8a48756c06314065ac7cbf54ed41be9', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, '2024-07-20 20:11:30', '103.108.156.66', NULL, 1, 1, 'ACTIVE', '2023-06-19 13:51:05', NULL, 277038, 676643, 1, 1),
(40, 0, 'DIGIPAYID', 'Jl Raya Bekasi, no. 46', '081290383389', 'Dewa Danu Brata', 'tesakun29@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '81290383389', 'SHOPEE PAY', 'SHOPEE PAY', '123123123', 'Dewa', 11, '786677fde24b8ca0215f2c7595509a54e23c4f820e9122242e919b6e73188d70', 'b4e6988b5fde95cbebc03b1e9c514dd5a47697aa7cd6aae4b51f8aaa4a8d6e5f', 'https://domain.com/webhook.php', '2024-07-21 08:33:32', '103.108.156.68', NULL, 2, 5, 'ACTIVE', '2023-12-27 12:56:06', NULL, NULL, NULL, 1, 1),
(68, 40, 'DIGIPAYID', 'Jl Raya Bekasi, no. 46', '081290383389', 'Operator Dewa', 'dewadanubrata@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '81290383389', 'SHOPEE PAY', 'SHOPEE PAY', '123123123', 'Dewa', 11, 'fe89a4b380c14ff2ce0db4dcceda1bc658b123095e42c06eb542f6c276d84526', NULL, NULL, '2024-07-09 15:06:20', '::1', NULL, 2, 7, 'ACTIVE', '2024-01-06 22:00:01', NULL, NULL, NULL, 1, 1),
(85, 40, 'DIGIPAYID', 'Jl Raya Bekasi, no. 46', '081290383389', 'Danu Brata', 'tesakun17@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '8194100592', 'SHOPEE PAY', 'SHOPEE PAY', '123123123', 'Dewa', 11, NULL, NULL, NULL, '2024-05-13 14:42:46', NULL, NULL, 2, 6, 'ACTIVE', '2024-05-13 14:42:46', NULL, NULL, NULL, 0, 1),
(93, 0, 'Toko Cantik', 'Bekasi Kota', '081617613766', 'Ciaa', 'tesakun35@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '62', '81617613766', NULL, NULL, NULL, NULL, 0, 'a7182092ad9373b0d13b0697176c746cebe94b218c4bd4e02ec834d63e1c3c12', NULL, NULL, '2024-07-15 03:11:43', '::1', NULL, 2, 5, 'ACTIVE', '2024-07-10 11:13:33', NULL, NULL, NULL, 1, 1),
(97, 1, NULL, NULL, '085881628286', 'XXX', 'tessdfds@dsfvsfs.df', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '85881628286', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2024-07-10 17:34:08', NULL, NULL, 1, 2, 'ACTIVE', '2024-07-10 17:34:08', NULL, NULL, NULL, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_users_2`
--

CREATE TABLE `app_users_2` (
  `id_user` int(11) NOT NULL DEFAULT 0,
  `id_user_parent` int(11) NOT NULL DEFAULT 0,
  `merchant_name` varchar(50) DEFAULT NULL,
  `merchant_address` varchar(255) DEFAULT NULL,
  `merchant_wa` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `telp_country_code` varchar(5) NOT NULL DEFAULT '+62',
  `telp` varchar(17) NOT NULL,
  `bank_short_name` varchar(15) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_account` varchar(35) DEFAULT NULL,
  `bank_account_name` varchar(50) DEFAULT NULL,
  `token_login` varchar(255) DEFAULT NULL,
  `token_api` varchar(255) DEFAULT NULL,
  `webhook_url` text DEFAULT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp(),
  `last_ip_address` varchar(25) DEFAULT NULL,
  `last_ip_location` varchar(255) DEFAULT NULL,
  `user_role` int(11) NOT NULL DEFAULT 2,
  `user_privilege` tinyint(4) NOT NULL DEFAULT 5,
  `user_status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `otp_email` int(8) DEFAULT NULL,
  `otp_wa` int(8) DEFAULT NULL,
  `is_verified` tinyint(4) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_users_2`
--

INSERT INTO `app_users_2` (`id_user`, `id_user_parent`, `merchant_name`, `merchant_address`, `merchant_wa`, `username`, `email`, `password`, `telp_country_code`, `telp`, `bank_short_name`, `bank_name`, `bank_account`, `bank_account_name`, `token_login`, `token_api`, `webhook_url`, `last_login`, `last_ip_address`, `last_ip_location`, `user_role`, `user_privilege`, `user_status`, `created_at`, `updated_at`, `otp_email`, `otp_wa`, `is_verified`, `is_active`) VALUES
(1, 0, NULL, NULL, NULL, 'Admin', 'admin@digipayid.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '81290383389', NULL, NULL, NULL, NULL, '1d03c14192ce03f66f19737d0449afeb641619ae93dbe1bbf400e8424821e1df', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, '2024-07-09 11:09:32', '::1', NULL, 1, 2, 'ACTIVE', '2023-06-19 13:51:05', NULL, 277038, 676643, 1, 1),
(40, 0, 'DIGIPAYIDX', 'Jl Raya Bekasi, no. 46', '081290383389', 'Dewa Danu', 'tesakun29@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '812334254353', 'GOPAY', 'GOPAY', '123123123', 'Dewa', '0d75bd9ca623dc72fbf470e8566ab90d666e8aa5c2d7eefe3c99551425954df2', 'b4e6988b5fde95cbebc03b1e9c514dd5a47697aa7cd6aae4b51f8aaa4a8d6e5f', 'https://domain.com/webhook.php', '2024-07-09 05:10:00', '::1', NULL, 2, 5, 'ACTIVE', '2023-12-27 12:56:06', NULL, NULL, NULL, 1, 1),
(68, 40, 'DIGIPAYID', 'Jl Raya Bekasi, no. 46', '08194100592', 'Operator Dewa', 'dewadanubrata@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '81290383389', 'GOPAY', 'GOPAY', '123123123', 'Dewa', '5602bf09f45a6cab725997c85f85573f99bad776fc9713df4e58029367ddf6f9', NULL, NULL, '2024-07-02 14:56:39', '::1', NULL, 2, 7, 'ACTIVE', '2024-01-06 22:00:01', NULL, NULL, NULL, 0, 1),
(85, 40, 'DIGIPAYID', 'Jl Raya Bekasi, no. 46', '081290383466', 'Danu Brata', 'tesakun17@gmail.com', 'bfd1dd7d57b8ae61db2de2053397dbe980473269a88c66e251a4b3e8912cce67', '+62', '8194100592', 'GOPAY', 'GOPAY', '123123123', 'Dewa', NULL, NULL, NULL, '2024-05-13 14:42:46', NULL, NULL, 2, 6, 'ACTIVE', '2024-05-13 14:42:46', NULL, NULL, NULL, 0, 1),
(91, 0, 'Toko Baju Faisal', 'Depok', '085881628286', 'A Faisal', 'tesakun35@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '62', '85881628286', NULL, NULL, NULL, NULL, '8b66734879ce9dfb7adc72fec2cc8389d7e387ac7e9c2495f012e7226dc63946', NULL, NULL, '2024-07-09 03:12:54', '::1', NULL, 2, 5, 'ACTIVE', '2024-07-09 02:46:19', NULL, NULL, NULL, 1, 1),
(1, 0, NULL, NULL, NULL, 'Admin', 'admin@digipayid.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '81290383389', NULL, NULL, NULL, NULL, '1d03c14192ce03f66f19737d0449afeb641619ae93dbe1bbf400e8424821e1df', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, '2024-07-09 11:09:32', '::1', NULL, 1, 2, 'ACTIVE', '2023-06-19 13:51:05', NULL, 277038, 676643, 1, 1),
(40, 0, 'DIGIPAYIDX', 'Jl Raya Bekasi, no. 46', '081290383389', 'Dewa Danu', 'tesakun29@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '812334254353', 'GOPAY', 'GOPAY', '123123123', 'Dewa', '0d75bd9ca623dc72fbf470e8566ab90d666e8aa5c2d7eefe3c99551425954df2', 'b4e6988b5fde95cbebc03b1e9c514dd5a47697aa7cd6aae4b51f8aaa4a8d6e5f', 'https://domain.com/webhook.php', '2024-07-09 05:10:00', '::1', NULL, 2, 5, 'ACTIVE', '2023-12-27 12:56:06', NULL, NULL, NULL, 1, 1),
(68, 40, 'DIGIPAYID', 'Jl Raya Bekasi, no. 46', '08194100592', 'Operator Dewa', 'dewadanubrata@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '+62', '81290383389', 'GOPAY', 'GOPAY', '123123123', 'Dewa', '5602bf09f45a6cab725997c85f85573f99bad776fc9713df4e58029367ddf6f9', NULL, NULL, '2024-07-02 14:56:39', '::1', NULL, 2, 7, 'ACTIVE', '2024-01-06 22:00:01', NULL, NULL, NULL, 0, 1),
(85, 40, 'DIGIPAYID', 'Jl Raya Bekasi, no. 46', '081290383466', 'Danu Brata', 'tesakun17@gmail.com', 'bfd1dd7d57b8ae61db2de2053397dbe980473269a88c66e251a4b3e8912cce67', '+62', '8194100592', 'GOPAY', 'GOPAY', '123123123', 'Dewa', NULL, NULL, NULL, '2024-05-13 14:42:46', NULL, NULL, 2, 6, 'ACTIVE', '2024-05-13 14:42:46', NULL, NULL, NULL, 0, 1),
(91, 0, 'Toko Baju Faisal', 'Depok', '085881628286', 'A Faisal', 'tesakun35@gmail.com', 'b3905b2f30a82c38edc1d972df2877695860319796c0df6c55fd3ac3cd7c9bfc', '62', '85881628286', NULL, NULL, NULL, NULL, '8b66734879ce9dfb7adc72fec2cc8389d7e387ac7e9c2495f012e7226dc63946', NULL, NULL, '2024-07-09 03:12:54', '::1', NULL, 2, 5, 'ACTIVE', '2024-07-09 02:46:19', NULL, NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_users_3`
--

CREATE TABLE `app_users_3` (
  `id_user` int(11) NOT NULL,
  `id_user_parent` int(11) NOT NULL DEFAULT 0,
  `merchant_name` varchar(50) DEFAULT NULL,
  `merchant_address` varchar(255) DEFAULT NULL,
  `merchant_wa` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `telp_country_code` varchar(5) NOT NULL DEFAULT '+62',
  `telp` varchar(17) NOT NULL,
  `bank_short_name` varchar(15) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_account` varchar(35) DEFAULT NULL,
  `bank_account_name` varchar(50) DEFAULT NULL,
  `token_login` varchar(255) DEFAULT NULL,
  `token_api` varchar(255) DEFAULT NULL,
  `webhook_url` text DEFAULT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp(),
  `last_ip_address` varchar(25) DEFAULT NULL,
  `last_ip_location` varchar(255) DEFAULT NULL,
  `user_role` int(11) NOT NULL DEFAULT 2,
  `user_privilege` tinyint(4) NOT NULL DEFAULT 5,
  `user_status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `otp_email` int(8) DEFAULT NULL,
  `otp_wa` int(8) DEFAULT NULL,
  `is_verified` tinyint(4) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_user_privilege`
--

CREATE TABLE `app_user_privilege` (
  `id_user_privilege` int(11) NOT NULL,
  `user_privilege_name` varchar(150) DEFAULT NULL,
  `user_privilege_type` tinyint(4) NOT NULL DEFAULT 2,
  `user_privilege_updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_user_privilege`
--

INSERT INTO `app_user_privilege` (`id_user_privilege`, `user_privilege_name`, `user_privilege_type`, `user_privilege_updated_at`) VALUES
(1, 'Super Admin', 1, '2024-03-21 16:39:12'),
(2, 'Administrator', 1, '2024-03-21 16:39:12'),
(3, 'Admin Finance', 1, '2024-03-21 16:39:12'),
(4, 'Admin Report', 1, '2024-03-21 16:39:12'),
(5, 'Merchant Owner', 2, '2024-03-21 16:39:12'),
(6, 'Merchant Finance', 2, '2024-03-21 17:02:43'),
(7, 'Merchant Operator', 2, '2024-03-21 17:02:43');

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ci_sessions`
--

-- --------------------------------------------------------

--
-- Table structure for table `log_hit_api`
--

CREATE TABLE `log_hit_api` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT 0,
  `token_api` varchar(255) DEFAULT NULL,
  `path` varchar(100) DEFAULT NULL,
  `query` text DEFAULT NULL,
  `ip_address` varchar(20) DEFAULT NULL,
  `executed_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_login`
--

CREATE TABLE `log_login` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL DEFAULT 0,
  `ip_address` varchar(25) DEFAULT NULL,
  `user_role` tinyint(1) NOT NULL DEFAULT 2,
  `token_login` varchar(255) DEFAULT NULL,
  `token_api` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_login`
--

-- --------------------------------------------------------

--
-- Table structure for table `master_accounting_type`
--

CREATE TABLE `master_accounting_type` (
  `id_master_accounting_type` int(11) NOT NULL,
  `accounting_type` int(11) NOT NULL DEFAULT 0,
  `is_credit` tinyint(4) NOT NULL DEFAULT 0,
  `is_debet` tinyint(4) NOT NULL DEFAULT 0,
  `description` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_accounting_type`
--

INSERT INTO `master_accounting_type` (`id_master_accounting_type`, `accounting_type`, `is_credit`, `is_debet`, `description`) VALUES
(1, 1, 1, 0, 'Pembayaran Belanja'),
(2, 101, 0, 1, 'Fee Pembayaran Belanja'),
(3, 1001, 1, 0, 'Fee Pembayaran Belanja App'),
(4, 1002, 0, 1, 'Fee Pembayaran Belanja PG'),
(5, 2, 1, 0, 'Deposit Merchant'),
(6, 201, 0, 1, 'Fee Deposit Merchant'),
(7, 2001, 1, 0, 'Fee App Deposit Merchant'),
(8, 2002, 0, 1, 'Fee PG Deposit Merchant'),
(9, 3, 0, 1, 'Withdraw Merchant'),
(10, 301, 0, 1, 'Fee Withdraw Merchant'),
(11, 3001, 1, 0, 'Fee App Withdraw Merchant'),
(12, 4, 0, 1, 'Withdraw Admin'),
(13, 5, 0, 1, 'Tax Merchant'),
(14, 6, 0, 1, 'Tax Admin');

-- --------------------------------------------------------

--
-- Table structure for table `master_cart`
--

CREATE TABLE `master_cart` (
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_cart`
--

INSERT INTO `master_cart` (`id_product`, `product_type`, `product_code`, `product_category_id`, `product_image_url`, `product_name`, `product_qty`, `product_price`, `product_discount`, `product_status`, `product_created_at`, `product_updated_at`) VALUES
(1, 0, 'DIGITAL001', 1, 'https://qph.cf2.quoracdn.net/main-qimg-fd40b1907215515f1a55ceabc36e259f-pjlq', 'Produk Digital Sample', 0, 150000, 2.5, 1, '2024-03-21 16:32:47', NULL),
(2, 1, 'UTAMA001', 1, 'https://wgmimedia.com/wp-content/uploads/2023/04/digital_products.jpg', 'Produk Fisik Sample', 100, 250000, 5, 1, '2024-03-21 16:32:47', NULL),
(10, 0, 'DIGITAL002', 1, 'https://qph.cf2.quoracdn.net/main-qimg-fd40b1907215515f1a55ceabc36e259f-pjlq', 'OO', 0, 150000, 2.5, 1, '2024-03-21 16:32:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_journal_finance`
--

CREATE TABLE `master_journal_finance` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `amount_credit` int(11) NOT NULL DEFAULT 0,
  `amount_debet` int(11) NOT NULL DEFAULT 0,
  `accounting_type` smallint(4) NOT NULL DEFAULT 0,
  `id_payment_method` smallint(6) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_payment_method`
--

CREATE TABLE `master_payment_method` (
  `id_payment_method` int(11) NOT NULL,
  `payment_method_id_pg` int(11) NOT NULL DEFAULT 1,
  `payment_method_code` varchar(25) DEFAULT NULL,
  `payment_method_name` varchar(100) DEFAULT NULL,
  `payment_method_type` tinyint(4) NOT NULL DEFAULT 1,
  `payment_method_image_url` varchar(255) DEFAULT NULL,
  `bank_short_name` varchar(15) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `can_withdraw` tinyint(4) DEFAULT 0,
  `payment_method_url` varchar(255) DEFAULT NULL,
  `payment_method_server` tinyint(4) NOT NULL DEFAULT 1,
  `fee_original` int(11) NOT NULL DEFAULT 0,
  `fee_original_percent` float NOT NULL DEFAULT 0,
  `settlement_day` int(11) NOT NULL DEFAULT 0,
  `settlement_on_weekend` tinyint(1) NOT NULL DEFAULT 0,
  `min_transaction` int(11) NOT NULL DEFAULT 0,
  `max_transaction` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `status_admin` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_payment_method`
--

INSERT INTO `master_payment_method` (`id_payment_method`, `payment_method_id_pg`, `payment_method_code`, `payment_method_name`, `payment_method_type`, `payment_method_image_url`, `bank_short_name`, `bank_name`, `can_withdraw`, `payment_method_url`, `payment_method_server`, `fee_original`, `fee_original_percent`, `settlement_day`, `settlement_on_weekend`, `min_transaction`, `max_transaction`, `status`, `status_admin`) VALUES
(0, 1, 'CASH', 'TUNAI (CASH)', 0, '/images/logo.png', 'TUNAI (CASH)', 'TUNAI (CASH)', 0, NULL, 0, 0, 0, 0, 1, 100, 1000000000, 1, 1),
(1, 1, 'BCAVA', 'BCA VA', 3, 'https://assets.tokovoucher.id/2022/11/f16b7a44e94da7632dfc672b6dbcf525.png', 'BCA', 'Bank Central Asia', 1, NULL, 1, 4200, 0, 2, 0, 15000, 50000000, 1, 1),
(2, 1, 'BNIVA', 'BNI VA', 3, 'https://assets.tokovoucher.id/2022/11/ce2ecb5af35f8ed39f3e3eced974a70c.png', 'BNI', 'Bank Negara Indonesia', 1, NULL, 1, 3500, 0, 0, 1, 10000, 50000000, 1, 1),
(3, 1, 'BRIVA', 'BRI VA', 3, 'https://assets.tokovoucher.id/2022/11/065303bb0d98a0e72292e93b90045d18.png', 'BRI', 'bank Rakyat Indonesia', 1, NULL, 1, 3000, 0, 0, 1, 10000, 10000000, 1, 1),
(4, 1, 'MANDIRIVA', 'MANDIRI VA', 3, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/2560px-Bank_Mandiri_logo_2016.svg.png', 'MANDIRI', 'Bank Mandiri', 1, NULL, 1, 3500, 0, 0, 1, 10000, 50000000, 1, 1),
(5, 1, 'PERMATAVA', 'PERMATA VA', 3, 'https://pay.biznetnetworks.com/image/bank/permata.png', 'PERMATA', 'Bank Permata', 1, NULL, 1, 2000, 0, 1, 0, 10000, 50000000, 1, 1),
(6, 1, 'PERMATAVAA', 'PERMATA VA II', 3, 'https://pay.biznetnetworks.com/image/bank/permata.png', NULL, NULL, 0, NULL, 1, 3000, 0, 1, 0, 10000, 1000000, 0, 1),
(7, 1, 'CIMBVA', 'CIMB VA', 3, 'https://assets.tokovoucher.id/2023/05/f7dd3b47f32b2ce56dec828255b4ba7a.png', 'CIMB', 'Bank CIMB Niaga', 1, NULL, 1, 2500, 0, 0, 1, 10000, 50000000, 1, 1),
(8, 1, 'DANAMONVA', 'DANAMON VA', 3, 'https://cdnaz.cekaja.com/media/2022/04/Danamon-Logo.png', 'DANAMON', 'Bank Danamonn', 1, NULL, 1, 2500, 0, 0, 1, 10000, 50000000, 1, 1),
(9, 1, 'BSIVA', 'BSI VA', 3, 'https://assets.pikiran-rakyat.com/crop/0x0:0x0/x/photo/2021/09/19/833815890.jpg', 'BSI', 'Bank Syariah Indonesia', 1, NULL, 1, 3500, 0, 1, 0, 10000, 2000000, 1, 1),
(10, 1, 'BNCVA', 'BNC VA (NEO)', 3, 'https://assets.tokovoucher.id/2023/05/ebdff869a4fdc5c694aaa31a4c7b2940.png', 'BNC', 'Bank Neo Commerce', 1, NULL, 1, 3000, 0, 1, 0, 10000, 50000000, 1, 1),
(11, 1, 'SHOPEEPAY', 'SHOPEE PAY', 2, 'https://assets.tokovoucher.id/2022/11/9a8849fb68683ccaed7483d827d07b39.png', 'SHOPEE PAY', 'SHOPEE PAY', 1, NULL, 1, 0, 2.5, 1, 0, 100, 2000000, 1, 1),
(12, 1, 'GOPAY', 'GOPAY', 2, 'https://assets.tokovoucher.id/2023/04/64fb349fefc6ce687700ea8724a37d19.png', 'GOPAY', 'GOPAY', 1, NULL, 1, 0, 3, 1, 0, 10, 2000000, 1, 1),
(13, 1, 'DANA', 'DANA', 2, 'https://assets.tokovoucher.id/2022/11/39dfa0a150297717e71239f0cd215f75.png', 'DANA', 'DANA', 1, NULL, 1, 0, 2.5, 1, 0, 10, 50000000, 1, 1),
(14, 1, 'LINKAJA', 'LINKAJA', 2, 'https://assets.tokovoucher.id/2022/11/b951de09eee40c57a3b570ecf396f119.png', 'LINKAJA', 'LINKAJA', 1, NULL, 1, 0, 3, 1, 0, 10, 2000000, 1, 1),
(15, 1, 'OVOPUSH', 'OVO', 2, 'https://js.durianpay.id/assets/img_ovo.svg', 'OVO', 'OVO', 1, NULL, 1, 0, 2.5, 1, 1, 100, 10000000, 1, 1),
(16, 1, 'ASTRAPAY', 'ASTRAPAY', 2, 'https://astrapay.com/static-assets/images/logos/logo-colorful.png', NULL, NULL, 0, NULL, 1, 0, 2.5, 0, 1, 100, 10000000, 0, 1),
(17, 1, 'VIRGO', 'VIRGO', 2, 'https://cdn.tokovoucher.id/2023/09/afc0069f7ab599da7a1f4980990d3211.png', NULL, NULL, 0, NULL, 1, 0, 2, 2, 0, 1000, 10000000, 0, 1),
(18, 1, 'QRISREALTIME', 'QRIS REALTIME', 1, 'https://assets.tokovoucher.id/2023/04/915e406841cd333f12e6cd2d29c59723.png', NULL, NULL, 0, NULL, 1, 0, 1.7, 0, 1, 100, 10000000, 1, 1),
(19, 1, 'QRIS', 'QRIS', 1, 'https://assets.tokovoucher.id/2023/04/915e406841cd333f12e6cd2d29c59723.png', NULL, NULL, 0, NULL, 1, 100, 0.7, 1, 0, 100, 15000000, 1, 1),
(20, 1, 'TELKOMSEL', 'TELKOMSEL', 4, 'https://qph.cf2.quoracdn.net/main-qimg-ee07e3e58d02303800aafb472929b28f', NULL, NULL, 0, NULL, 1, 0, 32, 1, 0, 5000, 1000000, 1, 1),
(21, 1, 'AXIS', 'AXIS', 4, 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Axis_logo_2015.svg/800px-Axis_logo_2015.svg.png', NULL, NULL, 0, NULL, 1, 0, 25, 1, 0, 2000, 2000000, 1, 1),
(22, 1, 'XL', 'XL', 4, 'https://upload.wikimedia.org/wikipedia/id/5/55/XL_logo_2016.svg', NULL, NULL, 0, NULL, 1, 0, 25, 1, 0, 2000, 2000000, 1, 1),
(23, 1, 'TRI', 'TRI', 4, 'https://static.insales-cdn.com/files/1/6331/25499835/original/tri_9c6da75f3c126b8a0cf8e391bc2dbb35.png', NULL, NULL, 0, NULL, 1, 0, 25, 1, 0, 1000, 200000, 1, 1),
(24, 1, 'ALFAMART', 'ALFAMART', 5, 'https://assets.tokovoucher.id/2022/11/0932396b5975cc0bd27a885539283b51.png', NULL, NULL, 0, NULL, 1, 3000, 0, 3, 0, 10000, 2000000, 0, 1),
(25, 1, 'INDOMARET', 'INDOMARET', 5, 'https://assets.tokovoucher.id/2022/12/5ad59de08cb178e08ff5a33449755e76.png', NULL, NULL, 0, NULL, 1, 3000, 0, 3, 0, 10000, 2000000, 0, 1),
(26, 2, 'MANDIRI', 'MANDIRI VA', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/2560px-Bank_Mandiri_logo_2016.svg.png', NULL, NULL, 0, 'gs/va/close', 1, 3885, 0, 1, 0, 10000, 500000000, 0, 1),
(27, 2, 'BRI', 'BRI VA', 1, 'https://assets.tokovoucher.id/2022/11/065303bb0d98a0e72292e93b90045d18.png', NULL, NULL, 0, 'gs/va/close', 1, 3885, 0, 1, 0, 10000, 500000000, 0, 1),
(28, 2, 'CIMB_NIAGA', 'CIMB VA', 1, 'https://assets.tokovoucher.id/2023/05/f7dd3b47f32b2ce56dec828255b4ba7a.png', NULL, NULL, 0, 'gs/va/close', 1, 3885, 0, 1, 0, 10000, 500000000, 0, 1),
(29, 2, 'PERMATA', 'PERMATA VA', 1, 'https://pay.biznetnetworks.com/image/bank/permata.png', NULL, NULL, 0, 'gs/va/open', 1, 3885, 0, 1, 0, 10000, 500000000, 0, 1),
(30, 2, 'DANAMON', 'DANAMON VA', 1, 'https://cdnaz.cekaja.com/media/2022/04/Danamon-Logo.png', NULL, NULL, 0, 'gs/va/close', 1, 4995, 0, 1, 0, 10000, 25000000, 0, 1),
(31, 2, 'BNI', 'BNI VA', 1, 'https://assets.tokovoucher.id/2022/11/ce2ecb5af35f8ed39f3e3eced974a70c.png', NULL, NULL, 0, 'gs/va/close', 1, 3885, 0, 1, 0, 10000, 500000000, 0, 1),
(32, 2, 'SAHABAT_SAMPOERNA', 'SAHABAT SAMPOERNA VA', 1, 'https://upload.wikimedia.org/wikipedia/id/1/14/Bank_Sahabat_Sampoerna_logo.png', NULL, NULL, 0, 'gs/va/open', 1, 3885, 0, 1, 0, 10000, 10000000, 0, 1),
(33, 2, 'BSI', 'BSI VA', 1, 'https://assets.pikiran-rakyat.com/crop/0x0:0x0/x/photo/2021/09/19/833815890.jpg', NULL, NULL, 0, 'gs/va/close', 1, 4995, 0, 1, 0, 10000, 500000000, 0, 1),
(34, 2, 'QRIS', 'QRIS', 3, 'https://assets.tokovoucher.id/2023/04/915e406841cd333f12e6cd2d29c59723.png', NULL, NULL, 0, 'gs/qris/dynamic', 1, 0, 0.7, 1, 0, 10000, 500000000, 0, 1),
(35, 1, 'SHOPEEPAY_REALTIME', 'SHOPEE PAY REALTIME', 2, 'https://assets.tokovoucher.id/2022/11/9a8849fb68683ccaed7483d827d07b39.png', NULL, NULL, 0, NULL, 1, 0, 3, 0, 1, 100, 2000000, 1, 1),
(36, 1, 'DANA_REALTIME', 'DANA REALTIME', 2, 'https://assets.tokovoucher.id/2022/11/39dfa0a150297717e71239f0cd215f75.png', NULL, NULL, 0, NULL, 1, 0, 3.2, 0, 1, 10, 50000000, 1, 1),
(37, 1, 'SMARTFREN', 'SMARTFREN', 4, 'https://assets2.bukakios.net/img2/uploads/2024/05/551-3543px-smartfren2015.svg.png', NULL, NULL, 0, NULL, 1, 0, 25, 1, 0, 5000, 1000000, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `master_payment_method2`
--

CREATE TABLE `master_payment_method2` (
  `id_master_payment_method` int(11) NOT NULL,
  `master_payment_method_code` varchar(25) DEFAULT NULL,
  `master_payment_method_name` varchar(100) DEFAULT NULL,
  `master_payment_method_type` tinyint(4) NOT NULL DEFAULT 1,
  `master_payment_method_image_url` varchar(255) DEFAULT NULL,
  `fee_original` int(11) NOT NULL DEFAULT 0,
  `fee_original_percent` float NOT NULL DEFAULT 0,
  `fee_merchant` int(11) NOT NULL DEFAULT 0,
  `settlement_day` int(11) NOT NULL DEFAULT 0,
  `settlement_on_weekend` tinyint(1) NOT NULL DEFAULT 0,
  `min_transaction` int(11) NOT NULL DEFAULT 0,
  `max_transaction` int(11) NOT NULL DEFAULT 0,
  `fee_on_merchant` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_payment_method2`
--

INSERT INTO `master_payment_method2` (`id_master_payment_method`, `master_payment_method_code`, `master_payment_method_name`, `master_payment_method_type`, `master_payment_method_image_url`, `fee_original`, `fee_original_percent`, `fee_merchant`, `settlement_day`, `settlement_on_weekend`, `min_transaction`, `max_transaction`, `fee_on_merchant`, `is_active`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 'BCAVA', 'BCA VA', 1, 'https://assets.tokovoucher.id/2022/11/f16b7a44e94da7632dfc672b6dbcf525.png', 4200, 0, 2000, 2, 0, 15000, 50000000, 0, 1, '2024-03-18 13:45:35', '2024-03-18 13:45:35', 0),
(2, 'BNIVA', 'BNI VA', 1, 'https://assets.tokovoucher.id/2022/11/ce2ecb5af35f8ed39f3e3eced974a70c.png', 3500, 0, 2000, 0, 1, 10000, 50000000, 0, 1, '2024-03-18 13:47:07', '2024-03-18 13:47:07', 0),
(3, 'BRIVA', 'BRI VA', 1, 'https://assets.tokovoucher.id/2022/11/065303bb0d98a0e72292e93b90045d18.png', 3000, 0, 2000, 0, 1, 10000, 10000000, 0, 1, '2024-03-18 13:48:12', '2024-03-18 13:48:12', 0),
(4, 'MANDIRIVA', 'MANDIRI VA', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/2560px-Bank_Mandiri_logo_2016.svg.png', 3000, 0, 2000, 1, 0, 10000, 50000000, 0, 1, '2024-03-18 13:48:52', '2024-03-18 13:48:52', 0),
(5, 'PERMATAVA', 'PERMATA VA', 1, 'https://pay.biznetnetworks.com/image/bank/permata.png', 2000, 0, 2000, 1, 0, 10000, 50000000, 0, 1, '2024-03-18 13:49:18', '2024-03-18 13:49:18', 0),
(6, 'PERMATAVAA', 'PERMATA VA II', 1, 'https://pay.biznetnetworks.com/image/bank/permata.png', 3000, 0, 2000, 1, 0, 10000, 1000000, 0, 0, '2024-03-18 13:50:07', '2024-03-18 13:50:07', 0),
(7, 'CIMBVA', 'CIMB VA', 1, 'https://assets.tokovoucher.id/2023/05/f7dd3b47f32b2ce56dec828255b4ba7a.png', 2500, 0, 2000, 0, 1, 10000, 50000000, 0, 1, '2024-03-18 13:50:52', '2024-03-18 13:50:52', 0),
(8, 'DANAMONVA', 'DANAMON VA', 1, 'https://cdnaz.cekaja.com/media/2022/04/Danamon-Logo.png', 2500, 0, 2000, 0, 1, 10000, 50000000, 0, 1, '2024-03-18 13:52:23', '2024-03-18 13:52:23', 0),
(9, 'BSIVA', 'BSI VA', 1, 'https://assets.pikiran-rakyat.com/crop/0x0:0x0/x/photo/2021/09/19/833815890.jpg', 4500, 0, 2000, 1, 0, 10000, 2000000, 0, 1, '2024-03-18 13:52:57', '2024-03-18 13:52:57', 0),
(10, 'BNCVA', 'BNC VA (NEO)', 1, 'https://assets.tokovoucher.id/2023/05/ebdff869a4fdc5c694aaa31a4c7b2940.png', 3000, 0, 2000, 1, 0, 10000, 50000000, 0, 1, '2024-03-18 13:53:54', '2024-03-18 13:53:54', 0),
(11, 'SHOPEEPAY', 'SHOPEE PAY', 2, 'https://assets.tokovoucher.id/2022/11/9a8849fb68683ccaed7483d827d07b39.png', 0, 2.5, 2000, 1, 0, 100, 2000000, 0, 1, '2024-03-18 13:58:35', '2024-03-18 13:58:35', 0),
(12, 'GOPAY', 'GOPAY', 2, 'https://assets.tokovoucher.id/2023/04/64fb349fefc6ce687700ea8724a37d19.png', 0, 3, 2000, 1, 0, 10, 2000000, 0, 1, '2024-03-18 14:00:08', '2024-03-18 14:00:08', 0),
(13, 'DANA', 'DANA', 2, 'https://assets.tokovoucher.id/2022/11/39dfa0a150297717e71239f0cd215f75.png', 0, 2.5, 2000, 1, 0, 10, 50000000, 0, 1, '2024-03-18 14:00:43', '2024-03-18 14:00:43', 0),
(14, 'LINKAJA', 'LINKAJA', 2, 'https://assets.tokovoucher.id/2022/11/b951de09eee40c57a3b570ecf396f119.png', 0, 3, 2000, 1, 0, 10, 2000000, 0, 1, '2024-03-18 14:01:18', '2024-03-18 14:01:18', 0),
(15, 'OVOPUSH', 'OVO', 2, 'https://js.durianpay.id/assets/img_ovo.svg', 0, 2.5, 2000, 1, 1, 100, 10000000, 0, 1, '2024-03-18 14:02:05', '2024-03-18 14:02:05', 0),
(16, 'ASTRAPAY', 'ASTRAPAY', 2, 'https://astrapay.com/static-assets/images/logos/logo-colorful.png', 0, 2.5, 2000, 0, 1, 100, 10000000, 0, 1, '2024-03-18 14:02:25', '2024-03-18 14:02:25', 0),
(17, 'VIRGO', 'VIRGO', 2, 'https://cdn.tokovoucher.id/2023/09/afc0069f7ab599da7a1f4980990d3211.png', 0, 2, 2000, 2, 0, 1000, 10000000, 0, 1, '2024-03-18 14:03:01', '2024-03-18 14:03:01', 0),
(18, 'QRISREALTIME', 'QRIS REALTIME', 3, 'https://assets.tokovoucher.id/2023/04/915e406841cd333f12e6cd2d29c59723.png', 0, 1.7, 2000, 0, 1, 100, 10000000, 0, 1, '2024-03-18 14:04:03', '2024-03-18 14:04:03', 0),
(19, 'QRIS', 'QRIS', 3, 'https://assets.tokovoucher.id/2023/04/915e406841cd333f12e6cd2d29c59723.png', 0, 0.7, 2000, 1, 0, 100, 15000000, 0, 1, '2024-03-18 14:04:36', '2024-03-18 14:04:36', 0),
(20, 'TELKOMSEL', 'TELKOMSEL', 4, 'https://i.ibb.co/TPzm8yx/tsel.png', 0, 32, 2000, 1, 0, 5000, 1000000, 0, 1, '2024-03-18 14:05:27', '2024-03-18 14:05:27', 0),
(21, 'AXIS', 'AXIS', 4, 'https://i.ibb.co/2qFqyQz/axis.png', 0, 25, 2000, 1, 0, 2000, 2000000, 0, 1, '2024-03-18 14:06:01', '2024-03-18 14:06:01', 0),
(22, 'XL', 'XL', 4, 'https://i.ibb.co/v42331B/xl.png', 0, 25, 2000, 1, 0, 2000, 2000000, 0, 1, '2024-03-18 14:06:19', '2024-03-18 14:06:19', 0),
(23, 'TRI', 'TRI', 4, 'https://i.ibb.co/JyRLqxY/tri.png', 0, 25, 2000, 1, 0, 1000, 200000, 0, 1, '2024-03-18 14:06:41', '2024-03-18 14:06:41', 0),
(24, 'ALFAMART', 'ALFAMART', 5, 'https://assets.tokovoucher.id/2022/11/0932396b5975cc0bd27a885539283b51.png', 3000, 0, 2000, 3, 0, 10000, 2000000, 0, 1, '2024-03-18 14:07:18', '2024-03-18 14:07:18', 0),
(25, 'INDOMARET', 'INDOMARET', 5, 'https://assets.tokovoucher.id/2022/12/5ad59de08cb178e08ff5a33449755e76.png', 3000, 0, 2000, 3, 0, 10000, 2000000, 0, 1, '2024-03-18 14:07:34', '2024-03-18 14:07:34', 0);

-- --------------------------------------------------------

--
-- Table structure for table `master_payment_method_user`
--

CREATE TABLE `master_payment_method_user` (
  `id_payment_method` int(11) NOT NULL DEFAULT 0,
  `fee_on_merchant` tinyint(4) NOT NULL DEFAULT 0,
  `fee_app` int(11) NOT NULL DEFAULT 0,
  `fee_app_percent` float NOT NULL DEFAULT 0,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_payment_method_user`
--

INSERT INTO `master_payment_method_user` (`id_payment_method`, `fee_on_merchant`, `fee_app`, `fee_app_percent`, `is_active`, `created_at`, `updated_at`, `updated_by`) VALUES
(0, 0, 500, 0, 1, '2024-07-07 15:23:56', '2024-07-07 15:23:56', 0),
(1, 0, 800, 0, 1, '2024-03-18 13:45:35', '2024-03-18 13:45:35', 0),
(2, 0, 500, 0, 1, '2024-03-18 13:47:07', '2024-03-18 13:47:07', 0),
(3, 0, 500, 0, 1, '2024-03-18 13:48:12', '2024-03-18 13:48:12', 0),
(4, 0, 500, 0, 1, '2024-03-18 13:48:52', '2024-03-18 13:48:52', 0),
(5, 0, 500, 0, 1, '2024-03-18 13:49:18', '2024-03-18 13:49:18', 0),
(6, 0, 500, 0, 0, '2024-03-18 13:50:07', '2024-03-18 13:50:07', 0),
(7, 0, 500, 0, 1, '2024-03-18 13:50:52', '2024-03-18 13:50:52', 0),
(8, 0, 500, 0, 1, '2024-03-18 13:52:23', '2024-03-18 13:52:23', 0),
(9, 0, 500, 0, 1, '2024-03-18 13:52:57', '2024-03-18 13:52:57', 0),
(10, 0, 500, 0, 1, '2024-03-18 13:53:54', '2024-03-18 13:53:54', 0),
(11, 0, 500, 0.5, 1, '2024-03-18 13:58:35', '2024-03-18 13:58:35', 0),
(12, 0, 500, 0.5, 1, '2024-03-18 14:00:08', '2024-03-18 14:00:08', 0),
(13, 0, 500, 0.5, 1, '2024-03-18 14:00:43', '2024-03-18 14:00:43', 0),
(14, 0, 500, 0.5, 1, '2024-03-18 14:01:18', '2024-03-18 14:01:18', 0),
(15, 0, 500, 0.5, 1, '2024-03-18 14:02:05', '2024-03-18 14:02:05', 0),
(16, 0, 500, 0.5, 1, '2024-03-18 14:02:25', '2024-03-18 14:02:25', 0),
(17, 0, 500, 1, 1, '2024-03-18 14:03:01', '2024-03-18 14:03:01', 0),
(18, 0, 500, 1.3, 1, '2024-03-18 14:04:03', '2024-03-18 14:04:03', 0),
(19, 1, 400, 0.3, 1, '2024-03-18 14:04:36', '2024-03-18 14:04:36', 0),
(20, 0, 500, 3, 1, '2024-03-18 14:05:27', '2024-03-18 14:05:27', 0),
(21, 0, 500, 5, 1, '2024-03-18 14:06:01', '2024-03-18 14:06:01', 0),
(22, 0, 500, 5, 1, '2024-03-18 14:06:19', '2024-03-18 14:06:19', 0),
(23, 0, 500, 5, 1, '2024-03-18 14:06:41', '2024-03-18 14:06:41', 0),
(24, 0, 500, 0, 1, '2024-03-18 14:07:18', '2024-03-18 14:07:18', 0),
(25, 0, 500, 0, 1, '2024-03-18 14:07:34', '2024-03-18 14:07:34', 0),
(26, 0, 500, 0, 1, '2024-04-04 15:38:21', '2024-04-04 15:38:21', 0),
(27, 0, 500, 0, 1, '2024-04-04 15:39:36', '2024-04-04 15:39:36', 0),
(28, 0, 500, 0, 1, '2024-04-04 15:40:08', '2024-04-04 15:40:08', 0),
(29, 0, 500, 0, 1, '2024-04-04 15:41:36', '2024-04-04 15:41:36', 0),
(30, 0, 500, 0, 1, '2024-04-04 15:45:18', '2024-04-04 15:45:18', 0),
(31, 0, 500, 0, 1, '2024-04-04 15:47:38', '2024-04-04 15:47:38', 0),
(32, 0, 500, 0, 1, '2024-04-04 15:50:47', '2024-04-04 15:50:47', 0),
(33, 0, 500, 0, 1, '2024-04-04 15:51:53', '2024-04-04 15:51:53', 0),
(34, 0, 500, 0, 1, '2024-04-04 16:02:37', '2024-04-04 16:02:37', 0),
(35, 0, 500, 1, 1, '2024-05-12 22:59:22', '2024-05-12 22:59:22', 0),
(36, 0, 500, 1.8, 1, '2024-05-12 22:59:22', '2024-05-12 22:59:22', 0),
(37, 0, 500, 5, 1, '2024-07-02 22:55:51', '2024-07-02 22:55:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `master_product`
--

CREATE TABLE `master_product` (
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_product`
--

INSERT INTO `master_product` (`id_product`, `product_type`, `product_code`, `product_barcode`, `product_category_id`, `product_image_url`, `product_name`, `product_qty`, `product_price`, `product_discount`, `product_status`, `product_created_at`, `product_updated_at`) VALUES
(1, 0, 'DIGITAL001', NULL, 1, 'https://qph.cf2.quoracdn.net/main-qimg-fd40b1907215515f1a55ceabc36e259f-pjlq', 'Produk Digital Sample', 0, 150000, 2.5, 1, '2024-03-21 16:32:47', NULL),
(2, 1, 'UTAMA001', NULL, 1, 'https://wgmimedia.com/wp-content/uploads/2023/04/digital_products.jpg', 'Produk Fisik Sample', 100, 250000, 5, 1, '2024-03-21 16:32:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_product_category`
--

CREATE TABLE `master_product_category` (
  `id_product_category` int(11) NOT NULL,
  `product_category` varchar(150) DEFAULT NULL,
  `product_category_updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_product_category`
--

INSERT INTO `master_product_category` (`id_product_category`, `product_category`, `product_category_updated_at`) VALUES
(1, 'Utama', '2024-03-21 16:31:04'),
(2, 'Tambahan', '2024-03-21 16:31:04');

-- --------------------------------------------------------

--
-- Table structure for table `master_transactions`
--

CREATE TABLE `master_transactions` (
  `id_transaction` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `id_user` int(11) NOT NULL DEFAULT 0,
  `id_customer` varchar(255) DEFAULT NULL,
  `email_customer` varchar(255) DEFAULT NULL,
  `wa_customer` varchar(20) DEFAULT '62',
  `total_product` int(11) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL DEFAULT 0,
  `amount_to_pay` int(11) NOT NULL DEFAULT 0,
  `amount_to_back` int(11) NOT NULL DEFAULT 0,
  `amount_to_receive` int(11) DEFAULT 0,
  `amount_tax` int(11) NOT NULL DEFAULT 0,
  `tax_percentage` float NOT NULL DEFAULT 0,
  `pg_fee` int(11) NOT NULL DEFAULT 0,
  `app_fee` int(11) NOT NULL DEFAULT 0,
  `fee` int(11) NOT NULL DEFAULT 0,
  `fee_on_merchant` tinyint(4) NOT NULL DEFAULT 0,
  `time_transaction` datetime NOT NULL DEFAULT current_timestamp(),
  `time_transaction_success` datetime DEFAULT NULL,
  `time_transaction_failed` datetime DEFAULT NULL,
  `note_transaction` varchar(100) DEFAULT NULL,
  `status_transaction` int(11) NOT NULL DEFAULT 0,
  `id_payment_method` tinyint(4) NOT NULL DEFAULT 0,
  `payment_method_code` varchar(20) DEFAULT 'CASH',
  `payment_method_name` varchar(50) DEFAULT NULL,
  `external_id` varchar(55) DEFAULT NULL,
  `url_file_billing` text DEFAULT NULL,
  `url_file_receipt` text DEFAULT NULL,
  `payment_response` text DEFAULT NULL,
  `status_payment` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_transactions`
--

-- --------------------------------------------------------

--
-- Table structure for table `master_transaction_products`
--

CREATE TABLE `master_transaction_products` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `total_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_transaction_products_temp`
--

CREATE TABLE `master_transaction_products_temp` (
  `id` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `product_code` varchar(15) DEFAULT NULL,
  `product_barcode` varchar(50) DEFAULT NULL,
  `product_category_id` smallint(4) NOT NULL DEFAULT 0,
  `product_image_url` varchar(255) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_qty` int(5) NOT NULL DEFAULT 0,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `product_discount` float NOT NULL DEFAULT 0,
  `product_status` tinyint(4) NOT NULL DEFAULT 0,
  `product_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pg_callback`
--

CREATE TABLE `pg_callback` (
  `id` int(11) NOT NULL,
  `json_text` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pg_settings`
--

CREATE TABLE `pg_settings` (
  `id` int(11) NOT NULL,
  `pg_name` varchar(100) DEFAULT NULL,
  `host_pg` varchar(255) DEFAULT NULL,
  `merchant_id` varchar(120) DEFAULT NULL,
  `secret_key` varchar(120) DEFAULT NULL,
  `callback_url` varchar(255) NOT NULL DEFAULT 'callbacks/brick',
  `is_used` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pg_settings`
--

INSERT INTO `pg_settings` (`id`, `pg_name`, `host_pg`, `merchant_id`, `secret_key`, `callback_url`, `is_used`) VALUES
(1, 'TOKOPAY', 'https://api.tokopay.id/', 'M240313QUHJM438', '813b05aae929dab66dffc60a5b9bc190cdd9b459ac480b803f4fb26443ff6c16', 'callbacks/tokopay', 1),
(2, 'BRICK', 'https://sandbox.onebrick.io/v2/payments/', '309fe148-bc35-4b82-b708-f0a73a8fd649', 'P1gLjH76d21jjggwcsvl72vJLFPUJX', 'callbacks/brick', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_journal_finance`
--
ALTER TABLE `admin_journal_finance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_journal_finance_40`
--
ALTER TABLE `app_journal_finance_40`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_journal_finance_93`
--
ALTER TABLE `app_journal_finance_93`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_payment_method_40`
--
ALTER TABLE `app_payment_method_40`
  ADD PRIMARY KEY (`id_payment_method`);

--
-- Indexes for table `app_payment_method_93`
--
ALTER TABLE `app_payment_method_93`
  ADD PRIMARY KEY (`id_payment_method`);

--
-- Indexes for table `app_product_40`
--
ALTER TABLE `app_product_40`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `product_barcode` (`product_barcode`);

--
-- Indexes for table `app_product_93`
--
ALTER TABLE `app_product_93`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `product_barcode` (`product_barcode`);

--
-- Indexes for table `app_product_category_40`
--
ALTER TABLE `app_product_category_40`
  ADD PRIMARY KEY (`id_product_category`);

--
-- Indexes for table `app_product_category_93`
--
ALTER TABLE `app_product_category_93`
  ADD PRIMARY KEY (`id_product_category`);

--
-- Indexes for table `app_transactions_40`
--
ALTER TABLE `app_transactions_40`
  ADD PRIMARY KEY (`id_transaction`);

--
-- Indexes for table `app_transactions_93`
--
ALTER TABLE `app_transactions_93`
  ADD PRIMARY KEY (`id_transaction`);

--
-- Indexes for table `app_transaction_products_40`
--
ALTER TABLE `app_transaction_products_40`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_barcode` (`product_barcode`);

--
-- Indexes for table `app_transaction_products_93`
--
ALTER TABLE `app_transaction_products_93`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_barcode` (`product_barcode`);

--
-- Indexes for table `app_transaction_products_temp_40`
--
ALTER TABLE `app_transaction_products_temp_40`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_barcode` (`product_barcode`);

--
-- Indexes for table `app_transaction_products_temp_93`
--
ALTER TABLE `app_transaction_products_temp_93`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_barcode` (`product_barcode`);

--
-- Indexes for table `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`,`telp`),
  ADD UNIQUE KEY `telp` (`telp`,`token_login`,`created_at`),
  ADD UNIQUE KEY `token_login` (`token_login`,`token_api`),
  ADD KEY `password` (`password`,`telp_country_code`,`user_role`,`user_status`,`is_active`),
  ADD KEY `token_api` (`token_api`),
  ADD KEY `last_ip_address` (`last_ip_address`),
  ADD KEY `last_ip_location` (`last_ip_location`);

--
-- Indexes for table `app_users_3`
--
ALTER TABLE `app_users_3`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`,`telp`),
  ADD UNIQUE KEY `telp` (`telp`,`token_login`,`created_at`),
  ADD UNIQUE KEY `token_login` (`token_login`,`token_api`),
  ADD KEY `password` (`password`,`telp_country_code`,`user_role`,`user_status`,`is_active`),
  ADD KEY `token_api` (`token_api`),
  ADD KEY `last_ip_address` (`last_ip_address`),
  ADD KEY `last_ip_location` (`last_ip_location`);

--
-- Indexes for table `app_user_privilege`
--
ALTER TABLE `app_user_privilege`
  ADD PRIMARY KEY (`id_user_privilege`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ci_sessions_timestamp` (`timestamp`),
  ADD KEY `ip_address` (`ip_address`) USING BTREE;

--
-- Indexes for table `log_hit_api`
--
ALTER TABLE `log_hit_api`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`,`token_api`,`ip_address`,`path`,`executed_date`),
  ADD KEY `query` (`query`(768));

--
-- Indexes for table `log_login`
--
ALTER TABLE `log_login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token_login`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `is_admin` (`user_role`),
  ADD KEY `token_api_2` (`token_api`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `master_accounting_type`
--
ALTER TABLE `master_accounting_type`
  ADD PRIMARY KEY (`id_master_accounting_type`);

--
-- Indexes for table `master_cart`
--
ALTER TABLE `master_cart`
  ADD PRIMARY KEY (`id_product`);

--
-- Indexes for table `master_journal_finance`
--
ALTER TABLE `master_journal_finance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_payment_method`
--
ALTER TABLE `master_payment_method`
  ADD PRIMARY KEY (`id_payment_method`);

--
-- Indexes for table `master_payment_method2`
--
ALTER TABLE `master_payment_method2`
  ADD PRIMARY KEY (`id_master_payment_method`);

--
-- Indexes for table `master_payment_method_user`
--
ALTER TABLE `master_payment_method_user`
  ADD PRIMARY KEY (`id_payment_method`);

--
-- Indexes for table `master_product`
--
ALTER TABLE `master_product`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `product_barcode` (`product_barcode`);

--
-- Indexes for table `master_product_category`
--
ALTER TABLE `master_product_category`
  ADD PRIMARY KEY (`id_product_category`);

--
-- Indexes for table `master_transactions`
--
ALTER TABLE `master_transactions`
  ADD PRIMARY KEY (`id_transaction`);

--
-- Indexes for table `master_transaction_products`
--
ALTER TABLE `master_transaction_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_barcode` (`product_barcode`);

--
-- Indexes for table `master_transaction_products_temp`
--
ALTER TABLE `master_transaction_products_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_barcode` (`product_barcode`);

--
-- Indexes for table `pg_callback`
--
ALTER TABLE `pg_callback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pg_settings`
--
ALTER TABLE `pg_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_journal_finance`
--
ALTER TABLE `admin_journal_finance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=730;

--
-- AUTO_INCREMENT for table `app_journal_finance_40`
--
ALTER TABLE `app_journal_finance_40`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=502;

--
-- AUTO_INCREMENT for table `app_journal_finance_93`
--
ALTER TABLE `app_journal_finance_93`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_payment_method_40`
--
ALTER TABLE `app_payment_method_40`
  MODIFY `id_payment_method` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `app_product_40`
--
ALTER TABLE `app_product_40`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `app_product_93`
--
ALTER TABLE `app_product_93`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_product_category_40`
--
ALTER TABLE `app_product_category_40`
  MODIFY `id_product_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `app_product_category_93`
--
ALTER TABLE `app_product_category_93`
  MODIFY `id_product_category` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_transactions_40`
--
ALTER TABLE `app_transactions_40`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `app_transactions_93`
--
ALTER TABLE `app_transactions_93`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_transaction_products_40`
--
ALTER TABLE `app_transaction_products_40`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=796;

--
-- AUTO_INCREMENT for table `app_transaction_products_93`
--
ALTER TABLE `app_transaction_products_93`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_transaction_products_temp_40`
--
ALTER TABLE `app_transaction_products_temp_40`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_transaction_products_temp_93`
--
ALTER TABLE `app_transaction_products_temp_93`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `app_users_3`
--
ALTER TABLE `app_users_3`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_user_privilege`
--
ALTER TABLE `app_user_privilege`
  MODIFY `id_user_privilege` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `log_hit_api`
--
ALTER TABLE `log_hit_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_login`
--
ALTER TABLE `log_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `master_accounting_type`
--
ALTER TABLE `master_accounting_type`
  MODIFY `id_master_accounting_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `master_cart`
--
ALTER TABLE `master_cart`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `master_journal_finance`
--
ALTER TABLE `master_journal_finance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_payment_method`
--
ALTER TABLE `master_payment_method`
  MODIFY `id_payment_method` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `master_payment_method2`
--
ALTER TABLE `master_payment_method2`
  MODIFY `id_master_payment_method` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `master_product`
--
ALTER TABLE `master_product`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `master_product_category`
--
ALTER TABLE `master_product_category`
  MODIFY `id_product_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `master_transactions`
--
ALTER TABLE `master_transactions`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `master_transaction_products`
--
ALTER TABLE `master_transaction_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_transaction_products_temp`
--
ALTER TABLE `master_transaction_products_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pg_callback`
--
ALTER TABLE `pg_callback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pg_settings`
--
ALTER TABLE `pg_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
