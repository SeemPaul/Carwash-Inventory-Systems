-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2026 at 07:03 PM
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
-- Database: `carwash_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`audit_id`, `user_id`, `action`, `module`, `description`, `created_at`) VALUES
(1, 1, 'Adjusted Inventory', 'Inventory Adjustment', 'Manual adjustment added: New', '2026-05-25 20:44:36'),
(2, 1, 'Restocked Product', 'Stock In', 'Added 100 stock to product ID: 5', '2026-05-25 20:50:23'),
(3, 1, 'Added Product', 'Products', 'Added new product: Special Soap', '2026-05-25 20:56:55'),
(4, 1, 'Added Product', 'Products', 'Added new product: ', '2026-05-25 20:57:42'),
(5, 1, 'Added Product', 'Products', 'Added new product: Special Fiber', '2026-05-25 20:57:55'),
(6, 1, 'Added Product', 'Products', 'Added new product: Special Fiber', '2026-05-25 20:58:21'),
(7, 1, 'Restocked Product', 'Stock In', 'Added 100 stock to product ID: 3', '2026-05-26 01:10:24'),
(8, 1, 'Restocked Product', 'Stock In', 'Added 100 stock to product ID: 10', '2026-05-26 01:11:00'),
(9, 1, 'Restocked Product', 'Stock In', 'Added 100 stock to product ID: 2', '2026-05-26 01:11:11'),
(10, 1, 'Deactivated Product', 'Products', 'Set product as Inactive: Car Shampoo', '2026-05-26 02:11:14'),
(11, 1, 'Deactivated Product', 'Products', 'Set product as Inactive: Car Shampoo', '2026-05-26 02:26:05'),
(12, 1, 'Reactivated Product', 'Products', 'Set product as Active: Car Shampoo', '2026-05-26 02:44:29'),
(13, 1, 'Deactivated Service', 'Services', 'Set service as Inactive: Basic Wash', '2026-05-26 02:44:48'),
(14, 1, 'Reactivated Service', 'Services', 'Set service as Active: Basic Wash', '2026-05-26 02:44:57'),
(15, 1, 'Deactivated Product', 'Products', 'Set product as Inactive: Car Shampoo', '2026-05-26 03:09:35'),
(16, 1, 'Deactivated Product', 'Products', 'Set product as Inactive: Ceramic Coating', '2026-05-26 03:09:38'),
(17, 1, 'Deactivated Product', 'Products', 'Set product as Inactive: Special Fiber', '2026-05-26 03:09:39'),
(18, 1, 'Deactivated Product', 'Products', 'Set product as Inactive: Wax Polish', '2026-05-26 03:09:43'),
(19, 1, 'Deactivated Product', 'Products', 'Set product as Inactive: Tire Cleaner', '2026-05-26 03:09:45'),
(20, 1, 'Deactivated Product', 'Products', 'Set product as Inactive: Special Soap', '2026-05-26 03:09:47'),
(21, 1, 'Deactivated Service', 'Services', 'Set service as Inactive: Basic Wash', '2026-05-26 03:10:13'),
(22, 1, 'Deactivated Service', 'Services', 'Set service as Inactive: Full Detailing (Motorcyle)', '2026-05-26 03:10:14'),
(23, 1, 'Deactivated Service', 'Services', 'Set service as Inactive: Full Detailing (PickUp Truck)', '2026-05-26 03:10:15'),
(24, 1, 'Deactivated Service', 'Services', 'Set service as Inactive: Full Detailing (Sedan Car)', '2026-05-26 03:10:16'),
(25, 1, 'Deactivated Service', 'Services', 'Set service as Inactive: Full Detailing Van', '2026-05-26 03:10:17'),
(26, 1, 'Deactivated Service', 'Services', 'Set service as Inactive: Medium Wash', '2026-05-26 03:10:19'),
(27, 1, 'Deactivated Service', 'Services', 'Set service as Inactive: Tire Cleaning', '2026-05-26 03:10:20'),
(28, 1, 'Deactivated Category', 'Categories', 'Set category as Inactive: Brush', '2026-05-26 03:24:37'),
(29, 1, 'Deactivated Supplier', 'Suppliers', 'Set supplier as Inactive: ABC Supplier', '2026-05-26 03:24:41'),
(30, 1, 'Reactivated Supplier', 'Suppliers', 'Set supplier as Active: ABC Supplier', '2026-05-26 03:24:50'),
(31, 1, 'Reactivated Category', 'Categories', 'Set category as Active: Brush', '2026-05-26 03:24:53'),
(32, 1, 'Reactivated Product', 'Products', 'Set product as Active: Car Shampoo', '2026-05-26 03:24:55'),
(33, 1, 'Reactivated Product', 'Products', 'Set product as Active: Ceramic Coating', '2026-05-26 03:24:56'),
(34, 1, 'Reactivated Product', 'Products', 'Set product as Active: Special Fiber', '2026-05-26 03:24:56'),
(35, 1, 'Reactivated Product', 'Products', 'Set product as Active: Special Soap', '2026-05-26 03:24:57'),
(36, 1, 'Reactivated Product', 'Products', 'Set product as Active: Tire Cleaner', '2026-05-26 03:24:58'),
(37, 1, 'Reactivated Product', 'Products', 'Set product as Active: Wax Polish', '2026-05-26 03:24:58'),
(38, 1, 'Reactivated Service', 'Services', 'Set service as Active: Basic Wash', '2026-05-26 03:24:59'),
(39, 1, 'Reactivated Service', 'Services', 'Set service as Active: Full Detailing (Motorcyle)', '2026-05-26 03:25:00'),
(40, 1, 'Reactivated Service', 'Services', 'Set service as Active: Full Detailing (PickUp Truck)', '2026-05-26 03:25:01'),
(41, 1, 'Reactivated Service', 'Services', 'Set service as Active: Full Detailing (Sedan Car)', '2026-05-26 03:25:02'),
(42, 1, 'Reactivated Service', 'Services', 'Set service as Active: Full Detailing Van', '2026-05-26 03:25:02'),
(43, 1, 'Reactivated Service', 'Services', 'Set service as Active: Medium Wash', '2026-05-26 03:25:03'),
(44, 1, 'Reactivated Service', 'Services', 'Set service as Active: Tire Cleaning', '2026-05-26 03:25:04'),
(45, 1, 'Edited Product', 'Products', 'Updated product: Car Shampoo', '2026-05-26 03:33:32'),
(46, 1, 'Edited Product', 'Products', 'Updated product: Ceramic Coating', '2026-05-26 03:33:38'),
(47, 1, 'Edited Product', 'Products', 'Updated product: Foam Shampoo For Motorcyle', '2026-05-26 03:33:46'),
(48, 1, 'Deleted Product', 'Products', 'Moved product to deleted history: Special Fiber', '2026-05-27 22:03:03'),
(49, 1, 'Deleted Category', 'Categories', 'Moved category to deleted history: Brush', '2026-05-27 22:05:46'),
(50, 1, 'Deleted Supplier', 'Suppliers', 'Moved supplier to deleted history: Putagina Mo', '2026-05-27 22:10:02'),
(51, 1, 'Deactivated Service', 'Services', 'Set service as Inactive: Serbisyo Para sa Tawo', '2026-05-27 22:23:08'),
(52, 1, 'Restored Deleted Product', 'Products', 'Restored deleted product: Special Fiber', '2026-05-27 22:26:29'),
(53, 1, 'Deleted Product', 'Products', 'Moved product to deleted history: Special Fiber', '2026-05-27 22:27:09'),
(54, 1, 'Deleted Service', 'Services', 'Moved service to deleted history: Serbisyo Para sa Tawo', '2026-05-27 22:32:17'),
(55, 1, 'Restored Deleted Service', 'Services', 'Restored deleted service: Serbisyo Para sa Tawo', '2026-05-27 22:32:23'),
(56, 1, 'Restored Deleted Category', 'Categories', 'Restored deleted category: Brush', '2026-05-27 22:36:06'),
(57, 1, 'Deleted Category', 'Categories', 'Moved category to deleted history: Brushes', '2026-05-27 22:36:19'),
(58, 1, 'Restored Deleted Supplier', 'Suppliers', 'Restored deleted supplier: Putagina Mo', '2026-05-27 22:39:39'),
(59, 1, 'Deleted Supplier', 'Suppliers', 'Moved supplier to deleted history: Putagina Mo', '2026-05-27 22:39:52'),
(60, 1, 'Deleted Service', 'Services', 'Moved service to deleted history: Basic Wash', '2026-05-28 10:45:15'),
(61, 1, 'Restored Deleted Service', 'Services', 'Restored deleted service: Basic Wash', '2026-05-28 10:45:23'),
(62, 1, 'Restocked Product', 'Stock In', 'Added 50 stock to product ID: 2', '2026-05-28 10:46:30'),
(63, 1, 'Restocked Product', 'Stock In', 'Added 21 stock to product ID: 3', '2026-05-28 10:47:40'),
(64, 1, 'Restocked Product', 'Stock In', 'Added 10 stock to product ID: 10', '2026-05-29 19:02:11'),
(65, 1, 'Adjusted Inventory', 'Inventory Adjustment', 'Manual adjustment deducted: Damaged', '2026-05-29 19:02:43'),
(66, 1, 'Restored Deleted Product', 'Products', 'Restored deleted product: Special Fiber', '2026-05-29 19:10:33'),
(67, 1, 'Restocked Product', 'Stock In', 'Added 120 stock to product ID: 10', '2026-05-29 22:49:36'),
(68, 1, 'Adjusted Inventory', 'Inventory Adjustment', 'Manual adjustment deducted: Damaged', '2026-05-29 22:52:17'),
(69, 1, 'Edited Product', 'Products', 'Updated product: Car Shampoo', '2026-05-29 22:52:43'),
(70, 1, 'Adjusted Inventory', 'Inventory Adjustment', 'Manual adjustment deducted: Damaged', '2026-05-29 22:53:27'),
(71, 1, 'Edited Product', 'Products', 'Updated product: Ceramic Coating', '2026-05-29 22:54:10'),
(72, 1, 'Edited Product', 'Products', 'Updated product: Foam Shampoo For Motorcyle', '2026-05-29 22:54:17'),
(73, 1, 'Deleted Service', 'Services', 'Moved service to deleted history: Serbisyo Para sa Tawo', '2026-05-30 00:53:11');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `deleted_status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `status`, `deleted_status`) VALUES
(1, 'Cleaning Supplies', NULL, 'Active', 'Active'),
(3, 'Chemicals', NULL, 'Active', 'Active'),
(4, 'Brushes', 'Brush for cars and Motorcycles', 'Active', 'Deleted'),
(5, 'Detailing Products', NULL, 'Active', 'Active'),
(6, 'Microfiber', NULL, 'Active', 'Active'),
(7, 'Brush', 'sbbrua2123123 3123', 'Active', 'Active'),
(8, 'Sabon', NULL, 'Active', 'Active'),
(9, 'Equipments', NULL, 'Active', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `log_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `log_type` enum('IN','OUT') NOT NULL,
  `quantity` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `date_logged` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`log_id`, `product_id`, `log_type`, `quantity`, `remarks`, `date_logged`) VALUES
(1, 10, 'OUT', 2, 'Used for service ID: 4', '2026-04-15 02:28:12'),
(2, 3, 'OUT', 3, 'Used for service ID: 4', '2026-04-15 02:28:12'),
(3, 6, 'OUT', 6, 'Used for service ID: 4', '2026-04-15 02:28:12'),
(4, 4, 'OUT', 2, 'Used for service ID: 4', '2026-04-15 02:28:12'),
(5, 5, 'OUT', 3, 'Used for service ID: 4', '2026-04-15 02:28:12'),
(6, 10, 'OUT', 2, 'Used for service ID: 4', '2026-04-15 03:46:05'),
(7, 3, 'OUT', 3, 'Used for service ID: 4', '2026-04-15 03:46:05'),
(8, 6, 'OUT', 6, 'Used for service ID: 4', '2026-04-15 03:46:05'),
(9, 4, 'OUT', 2, 'Used for service ID: 4', '2026-04-15 03:46:05'),
(10, 5, 'OUT', 3, 'Used for service ID: 4', '2026-04-15 03:46:05'),
(11, 10, 'OUT', 2, 'Used for service ID: 4', '2026-04-15 03:46:47'),
(12, 3, 'OUT', 3, 'Used for service ID: 4', '2026-04-15 03:46:47'),
(13, 6, 'OUT', 6, 'Used for service ID: 4', '2026-04-15 03:46:47'),
(14, 4, 'OUT', 2, 'Used for service ID: 4', '2026-04-15 03:46:47'),
(15, 5, 'OUT', 3, 'Used for service ID: 4', '2026-04-15 03:46:47'),
(16, 2, 'OUT', 6, 'Used for service ID: 1', '2026-04-15 03:46:51'),
(17, 6, 'OUT', 1, 'Used for service ID: 1', '2026-04-15 03:46:51'),
(18, 2, 'OUT', 4, 'Used for service ID: 5', '2026-04-15 03:47:09'),
(19, 10, 'OUT', 4, 'Used for service ID: 5', '2026-04-15 03:47:09'),
(20, 6, 'OUT', 20, 'Used for service ID: 5', '2026-04-15 03:47:09'),
(21, 4, 'OUT', 4, 'Used for service ID: 5', '2026-04-15 03:47:09'),
(22, 5, 'OUT', 4, 'Used for service ID: 5', '2026-04-15 03:47:09'),
(23, 4, 'OUT', 3, 'Used for service ID: 3', '2026-04-15 03:47:13'),
(24, 2, 'OUT', 6, 'Used for service ID: 1', '2026-04-15 07:08:30'),
(25, 6, 'OUT', 1, 'Used for service ID: 1', '2026-04-15 07:08:30'),
(26, 2, 'IN', 60, 'Restocked', '2026-04-15 09:58:05'),
(27, 10, 'OUT', 2, 'Used for service ID: 4', '2026-04-15 10:03:33'),
(28, 3, 'OUT', 3, 'Used for service ID: 4', '2026-04-15 10:03:33'),
(29, 6, 'OUT', 6, 'Used for service ID: 4', '2026-04-15 10:03:33'),
(30, 4, 'OUT', 2, 'Used for service ID: 4', '2026-04-15 10:03:33'),
(31, 5, 'OUT', 3, 'Used for service ID: 4', '2026-04-15 10:03:33'),
(32, 2, 'OUT', 10, 'Used for service ID: 6', '2026-04-15 10:09:47'),
(33, 4, 'OUT', 8, 'Used for service ID: 6', '2026-04-15 10:09:47'),
(34, 5, 'OUT', 8, 'Used for service ID: 6', '2026-04-15 10:09:47'),
(35, 6, 'OUT', 25, 'Used for service ID: 6', '2026-04-15 10:09:47'),
(36, 10, 'OUT', 10, 'Used for service ID: 6', '2026-04-15 10:09:47'),
(37, 3, 'OUT', 3, 'Used for service ID: 4', '2026-04-16 09:09:02'),
(38, 4, 'OUT', 2, 'Used for service ID: 4', '2026-04-16 09:09:02'),
(39, 5, 'OUT', 3, 'Used for service ID: 4', '2026-04-16 09:09:02'),
(40, 6, 'OUT', 6, 'Used for service ID: 4', '2026-04-16 09:09:02'),
(41, 10, 'OUT', 2, 'Used for service ID: 4', '2026-04-16 09:09:02'),
(42, 4, 'IN', 200, 'Newly Stocked by Cangs', '2026-05-25 19:34:34'),
(43, 3, 'IN', 100, '100 Liters from Uymatioa', '2026-05-25 19:35:12'),
(44, 3, 'OUT', 3, 'Used for service ID: 4', '2026-05-25 19:35:56'),
(45, 4, 'OUT', 2, 'Used for service ID: 4', '2026-05-25 19:35:56'),
(46, 5, 'OUT', 3, 'Used for service ID: 4', '2026-05-25 19:35:56'),
(47, 6, 'OUT', 6, 'Used for service ID: 4', '2026-05-25 19:35:56'),
(48, 10, 'OUT', 2, 'Used for service ID: 4', '2026-05-25 19:35:56'),
(49, 4, 'OUT', 3, 'Used for service ID: 3', '2026-05-25 19:50:18'),
(50, 3, 'IN', 12, 'Manual adjustment added: Missing Stock', '2026-05-25 20:30:51'),
(51, 2, 'OUT', 5, 'Manual adjustment deducted: Damaged', '2026-05-25 20:40:23'),
(52, 10, 'OUT', 5, 'Manual adjustment deducted: Damaged', '2026-05-25 20:40:34'),
(53, 3, 'IN', 12, 'Manual adjustment added: New', '2026-05-25 20:41:11'),
(54, 3, 'IN', 12, 'Manual adjustment added: New', '2026-05-25 20:41:21'),
(55, 3, 'IN', 12, 'Manual adjustment added: New', '2026-05-25 20:42:15'),
(56, 3, 'IN', 12, 'Manual adjustment added: New', '2026-05-25 20:44:36'),
(57, 5, 'IN', 100, 'Restocked Supplier Cangs Inc.', '2026-05-25 20:50:23'),
(58, 2, 'OUT', 10, 'Used for service ID: 6', '2026-05-25 23:09:17'),
(59, 4, 'OUT', 8, 'Used for service ID: 6', '2026-05-25 23:09:17'),
(60, 5, 'OUT', 8, 'Used for service ID: 6', '2026-05-25 23:09:17'),
(61, 6, 'OUT', 25, 'Used for service ID: 6', '2026-05-25 23:09:17'),
(62, 10, 'OUT', 10, 'Used for service ID: 6', '2026-05-25 23:09:17'),
(63, 3, 'IN', 100, 'Restocked by Cangs', '2026-05-26 01:10:24'),
(64, 10, 'IN', 100, '', '2026-05-26 01:11:00'),
(65, 2, 'IN', 100, '', '2026-05-26 01:11:11'),
(66, 2, 'OUT', 2, 'Used for service ID: 10', '2026-05-26 01:13:33'),
(67, 6, 'OUT', 3, 'Used for service ID: 10', '2026-05-26 01:13:33'),
(68, 10, 'OUT', 2, 'Used for service ID: 10', '2026-05-26 01:13:33'),
(69, 2, 'IN', 50, 'Shoppe', '2026-05-28 10:46:30'),
(70, 3, 'IN', 21, '', '2026-05-28 10:47:40'),
(71, 10, 'IN', 10, '', '2026-05-29 19:02:11'),
(72, 10, 'OUT', 8, 'Manual adjustment deducted: Damaged', '2026-05-29 19:02:43'),
(73, 10, 'IN', 120, '', '2026-05-29 22:49:36'),
(74, 2, 'OUT', 30, 'Manual adjustment deducted: Damaged', '2026-05-29 22:52:17'),
(75, 2, 'OUT', 4, 'Manual adjustment deducted: Damaged', '2026-05-29 22:53:27');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `quantity_in_stock` int(11) DEFAULT NULL,
  `reorder_level` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `deleted_status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `supplier_id`, `product_name`, `unit`, `quantity_in_stock`, `reorder_level`, `status`, `deleted_status`) VALUES
(2, 1, 1, 'Car Shampoo', '25 Bottles', 1, 5, 'Active', 'Active'),
(3, 1, 3, 'Foam Shampoo For Motorcyle', '25 Liters', 0, 5, 'Active', 'Active'),
(4, 1, 2, 'Tire Cleaner', '25 Bottles', 187, 5, 'Active', 'Active'),
(5, 5, 3, 'Wax Polish', '25 Bottles', 93, 5, 'Active', 'Active'),
(6, 6, 3, 'Microfiber Cloth', '50 Cloths', 29, 5, 'Active', 'Active'),
(10, 3, 5, 'Ceramic Coating', '125 Bottles', 49, 50, 'Active', 'Active'),
(13, 8, 2, 'Special Soap', '100 Liters', 100, 10, 'Active', 'Active'),
(14, 6, 1, 'Special Fiber', '100', 100, 10, 'Active', 'Active'),
(16, 6, 7, 'Special Fiber', '100', 100, 10, 'Active', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `estimated_duration` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deleted_status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `estimated_duration`, `status`, `price`, `deleted_status`) VALUES
(1, 'Basic Wash', 'Exterior cleaning using foam shampoo', '20 minutes', 'Active', 0.00, 'Active'),
(2, 'Waxing', 'Apply wax polish to vehicle surface', '30 Minutes', 'Active', 0.00, 'Active'),
(3, 'Tire Cleaning', 'Cleans tire to make it look new', '5 Minutes', 'Active', 0.00, 'Active'),
(4, 'Full Detailing (Motorcyle)', 'This includes wax, full body wash, and ceramic coating.\r\n\r\nEstimated Duration: 1 to 2 hours\r\n\r\nIncluded Products\r\nCeramic Coating - 2 (125 Bottles)\r\nFoam Shampoo For Motorcyle - 3 (25 Liters)\r\nMicrofiber Cloth - 6 (50 Cloths)\r\nTire Cleaner - 2 (25 Bottles)\r\nWax Polish - 3 (25 Bottles)', '1 to 2 hours', 'Active', 0.00, 'Active'),
(5, 'Full Detailing (Sedan Car)', 'This includes full carwash, wax, tire cleaning, ceramic coating.\r\n\r\nEstimated Duration: 1 to 2 hours\r\n\r\nIncluded Products\r\nCar Shampoo - 4 (25 Bottles)\r\nCeramic Coating - 4 (125 Bottles)\r\nMicrofiber Cloth - 20 (50 Cloths)\r\nTire Cleaner - 4 (25 Bottles)\r\nWax Polish - 4 (25 Bottles)', '1 to 2 hours', 'Active', 0.00, 'Active'),
(6, 'Full Detailing (PickUp Truck)', 'This includes full carwash, wax, tire cleaning, ceramic coating.', '2 to 3 hours', 'Active', 0.00, 'Active'),
(9, 'Full Detailing Van', '', '1 to 2 hours', 'Active', 0.00, 'Active'),
(10, 'Medium Wash', '', '20 Minutes', 'Active', 0.00, 'Active'),
(11, 'Serbisyo Para sa Tawo', 'Hugas LUBOT', '1 Sekonds', 'Inactive', 0.00, 'Deleted');

-- --------------------------------------------------------

--
-- Table structure for table `service_logs`
--

CREATE TABLE `service_logs` (
  `service_log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `date_performed` datetime NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_logs`
--

INSERT INTO `service_logs` (`service_log_id`, `user_id`, `service_id`, `date_performed`, `remarks`) VALUES
(1, 1, 1, '2026-04-14 14:16:48', 'Done'),
(2, 1, 1, '2026-04-14 14:52:02', 'Done'),
(3, 1, 3, '2026-04-14 15:07:43', 'Done'),
(4, 1, 2, '2026-04-14 15:07:51', 'Done'),
(5, 1, 1, '2026-04-14 15:07:55', 'Done'),
(6, 1, 1, '2026-04-14 15:25:40', ''),
(7, 1, 1, '2026-04-14 17:39:50', ''),
(8, 1, 4, '2026-04-14 17:41:33', 'Done'),
(9, 1, 6, '2026-04-14 17:41:37', 'Done'),
(10, 1, 5, '2026-04-14 17:41:42', 'Done'),
(11, 1, 4, '2026-04-14 19:37:57', 'Done'),
(12, 1, 4, '2026-04-14 20:28:12', 'Done'),
(13, 1, 4, '2026-04-14 21:46:05', ''),
(14, 1, 4, '2026-04-14 21:46:47', ''),
(15, 1, 1, '2026-04-14 21:46:51', ''),
(16, 1, 5, '2026-04-14 21:47:09', 'Done'),
(17, 1, 3, '2026-04-14 21:47:13', ''),
(18, 1, 1, '2026-04-15 01:08:30', 'Done'),
(19, 1, 4, '2026-04-15 04:03:33', 'Done'),
(20, 1, 6, '2026-04-15 04:09:47', 'Done'),
(21, 1, 4, '2026-04-16 03:09:02', ''),
(22, 1, 4, '2026-05-25 13:35:56', 'Sir Peters Motorcycle Click 125 White'),
(23, 4, 3, '2026-05-25 13:50:18', ''),
(24, 1, 6, '2026-05-25 17:09:17', ''),
(25, 1, 10, '2026-05-25 19:13:33', '');

-- --------------------------------------------------------

--
-- Table structure for table `service_products`
--

CREATE TABLE `service_products` (
  `service_product_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_used` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_products`
--

INSERT INTO `service_products` (`service_product_id`, `service_id`, `product_id`, `quantity_used`) VALUES
(9, 1, 2, 6),
(10, 1, 6, 1),
(11, 3, 4, 3),
(12, 2, 5, 2),
(21, 4, 10, 2),
(22, 4, 3, 3),
(23, 4, 6, 6),
(24, 4, 4, 2),
(25, 4, 5, 3),
(26, 5, 2, 4),
(27, 5, 10, 4),
(28, 5, 6, 20),
(29, 5, 4, 4),
(30, 5, 5, 4),
(31, 6, 2, 10),
(32, 6, 10, 10),
(33, 6, 6, 25),
(34, 6, 4, 8),
(35, 6, 5, 8),
(36, 9, 2, 2),
(37, 9, 10, 3),
(38, 9, 6, 5),
(39, 9, 4, 4),
(40, 9, 5, 3),
(41, 10, 2, 2),
(42, 10, 10, 2),
(43, 10, 6, 3);

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `request_id` int(11) NOT NULL,
  `request_code` varchar(30) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `plate_number` varchar(50) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `status` enum('Pending','Forwarded to Cashier','Approved','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`request_id`, `request_code`, `customer_name`, `vehicle_type`, `plate_number`, `service_id`, `status`, `created_at`) VALUES
(1, 'SR-20260415-61E6C3', 'Peter Paul Juanica', 'Sedan', '', 5, 'Pending', '2026-04-15 07:05:46'),
(2, 'SR-20260415-1C9D4E', 'Peter Paul Juanica', 'Sedan', '', 4, 'Pending', '2026-04-15 07:06:49'),
(3, 'SR-20260415-D4E8CE', 'Peter Paul Juanica', 'Sedan', '', 4, 'Pending', '2026-04-15 08:35:59'),
(4, 'SR-20260415-2F649F', 'Peter Paul Juanica', 'PickUp', '', 6, 'Pending', '2026-04-15 10:04:43'),
(5, 'SR-20260416-CC583E', 'Peter Paul Juanica', 'Sedan', '', 4, 'Pending', '2026-04-16 09:08:48'),
(6, 'SR-20260525-A81E41', 'Peter Paul Juanica', 'Van', 'EEW', 9, 'Pending', '2026-05-25 23:29:34'),
(7, 'SR-20260525-A2AEE0', 'Peter Paul Juanica', 'Van', 'EEW', 9, 'Pending', '2026-05-25 23:29:44');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `deleted_status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_person`, `phone`, `status`, `deleted_status`) VALUES
(1, 'ABC Supplier', 'John Doe', '09123456788', 'Active', 'Active'),
(2, 'DEF Supplies', 'CleanPro Trading', 'ShineSource', 'Active', 'Active'),
(3, 'Cang\'s Incorporated', 'Roy T. Cang', '09759316462', 'Active', 'Active'),
(4, 'Lee Plaza', 'Manager', '09559329547', 'Active', 'Active'),
(5, 'Shoppe', 'LKC Manager', '09573846542', 'Active', 'Active'),
(7, 'Uymatiao', 'Manager', '097515415451', 'Active', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `password`, `role`, `email`) VALUES
(1, 'Admin', 'admin', 'admin123', 'Admin', 'admin@example.com'),
(4, 'Staff', 'staff', 'staff123', 'Staff', 'staff@example.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_logs`
--
ALTER TABLE `service_logs`
  ADD PRIMARY KEY (`service_log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `service_products`
--
ALTER TABLE `service_products`
  ADD PRIMARY KEY (`service_product_id`),
  ADD UNIQUE KEY `unique_service_product` (`service_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD UNIQUE KEY `request_code` (`request_code`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `service_logs`
--
ALTER TABLE `service_logs`
  MODIFY `service_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `service_products`
--
ALTER TABLE `service_products`
  MODIFY `service_product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `service_logs`
--
ALTER TABLE `service_logs`
  ADD CONSTRAINT `service_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_logs_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_products`
--
ALTER TABLE `service_products`
  ADD CONSTRAINT `service_products_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
