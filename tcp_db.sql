-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2026 at 09:22 AM
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
-- Database: `tcp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `docs_history`
--

CREATE TABLE `docs_history` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `date_release` date DEFAULT NULL,
  `particular` varchar(255) NOT NULL,
  `date_request` date DEFAULT NULL,
  `released_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `docs_history`
--

INSERT INTO `docs_history` (`id`, `student_id`, `date_release`, `particular`, `date_request`, `released_by`, `created_at`) VALUES
(1, '123', '2026-07-09', 'WATER', NULL, 'Christie Dianne S. Rulona', '2026-07-09 14:54:10'),
(2, '123', '2026-07-09', 'WATER', NULL, 'Christie Dianne S. Rulona', '2026-07-09 15:03:55'),
(3, '123', '2026-07-09', 'WATER', NULL, 'Christie Dianne S. Rulona', '2026-07-09 15:07:06'),
(4, '123', '2026-07-10', 'WATER', NULL, 'Christie Dianne S. Rulona', '2026-07-09 15:11:13'),
(5, '123', '2026-07-09', 'WATER', NULL, 'Christie Dianne S. Rulona', '2026-07-09 15:14:19'),
(6, '123', '2026-07-10', 'WATER', NULL, 'Christie Dianne S. Rulona', '2026-07-09 15:20:21'),
(7, '143', '2026-07-11', 'water', NULL, 'Christie Dianne S. Rulona', '2026-07-10 11:23:52');

-- --------------------------------------------------------

--
-- Table structure for table `document_fees`
--

CREATE TABLE `document_fees` (
  `id` int(11) NOT NULL,
  `fee_key` varchar(50) DEFAULT NULL,
  `document_name` varchar(150) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_fees`
--

INSERT INTO `document_fees` (`id`, `fee_key`, `document_name`, `amount`) VALUES
(1, 'tor_prc', 'TOR for PRC', 700.00),
(2, 'tor_employment', 'TOR for Employment', 700.00),
(3, 'honorable', 'Honorable Dismissal', 1.00),
(4, 'completed_units', 'Certificate of Completed Units', 150.00),
(5, 'gwa', 'Certificate of GWA', 100.00),
(6, 'gmc', 'Certificate of GMC', 100.00),
(7, 'auth_tor', 'Authenticated Copy of TOR', 200.00),
(8, 'auth_certificate', 'Authenticated Copy of Certificate', 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `document_request_monitoring`
--

CREATE TABLE `document_request_monitoring` (
  `id` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `request_type` text NOT NULL,
  `request_sets` text DEFAULT NULL,
  `delivery_mode` enum('Pick-up','Shipment') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `or_number` varchar(50) DEFAULT NULL,
  `gcash_ref` varchar(100) DEFAULT NULL,
  `courier_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','Verified Payment','Processing','For Signature','For Shipment','For Pick-up','Released') DEFAULT 'Pending',
  `tracking_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `released_date` datetime DEFAULT NULL,
  `gcash_verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_history`
--

CREATE TABLE `email_history` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `sent_by` varchar(100) DEFAULT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_signatures`
--

CREATE TABLE `email_signatures` (
  `id` int(11) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `signature_name` varchar(255) DEFAULT NULL,
  `signature_content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_signatures`
--

INSERT INTO `email_signatures` (`id`, `fullname`, `signature_name`, `signature_content`, `created_at`) VALUES
(1, 'Christie Dianne S. Rulona', 'TCP Coordinator', 'TCP Coordinator\nCaraga Institute of Technology \nNational Highway Songkoy Alegria-Boundary Kitcharao, Agusan del Norte\nMobile: 09461238620 | tcp@citkitcharao.edu.ph', '2026-03-28 03:31:54'),
(2, 'Christie Dianne S. Rulona', 'Chris James Monterola', 'sadada \nadas\nSd', '2026-03-28 03:42:52'),
(3, 'Christie Dianne S. Rulona', 'SPORTS Coordinator', 'SAMPLE LANG NI NA SIGNATURE', '2026-06-25 03:25:35'),
(4, NULL, 'DWIGHT KISHNA SEVILLA', 'SAMPLE LANG NI HA AYAW SABA', '2026-07-09 13:15:35'),
(5, NULL, 'SAMPLE AGAIN', '123', '2026-07-09 13:17:53'),
(6, NULL, 'PLEASE LANG', '123123', '2026-07-09 13:22:58'),
(7, NULL, 'FINAL', '123', '2026-07-09 13:24:15');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `type_expenses` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `let_year`
--

CREATE TABLE `let_year` (
  `id` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `let_year`
--

INSERT INTO `let_year` (`id`, `year`, `created_at`) VALUES
(1, '2025', '2026-08-04 12:22:00'),
(2, '2026', '2026-08-04 12:31:21'),
(3, '2024', '2026-08-15 15:01:25');

-- --------------------------------------------------------

--
-- Table structure for table `or_booklets`
--

CREATE TABLE `or_booklets` (
  `id` int(11) NOT NULL,
  `booklet_no` varchar(50) NOT NULL,
  `or_start` int(11) NOT NULL,
  `or_end` int(11) NOT NULL,
  `current_or` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bk_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `deposited_date` datetime DEFAULT NULL,
  `deposited_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `or_booklets`
--

INSERT INTO `or_booklets` (`id`, `booklet_no`, `or_start`, `or_end`, `current_or`, `created_at`, `total_amount`, `bk_status`, `deposited_date`, `deposited_by`) VALUES
(1, 'BK-100', 1, 5, 2, '2026-08-14 12:51:37', 1500.00, 'Pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` int(11) NOT NULL,
  `student_id` varchar(100) NOT NULL,
  `payment_date` date NOT NULL,
  `or_number` varchar(100) DEFAULT NULL,
  `gcash_ref` varchar(100) DEFAULT NULL,
  `particular` varchar(255) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `received_by` varchar(255) DEFAULT NULL,
  `gcash_status` varchar(20) DEFAULT 'pending',
  `verified_by` varchar(255) DEFAULT NULL,
  `booklet_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`id`, `student_id`, `payment_date`, `or_number`, `gcash_ref`, `particular`, `amount_paid`, `received_by`, `gcash_status`, `verified_by`, `booklet_no`) VALUES
(1, '143', '2026-08-14', '143', '143', 'Registration Fee', 1500.00, 'Christie Dianne S. Rulona', 'verified', 'Christie Dianne S. Rulona', 'BK-100');

-- --------------------------------------------------------

--
-- Table structure for table `registrar_directory`
--

CREATE TABLE `registrar_directory` (
  `id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `school_email` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrar_email_history`
--

CREATE TABLE `registrar_email_history` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` longtext DEFAULT NULL,
  `signature_id` int(11) DEFAULT NULL,
  `sent_by` varchar(150) DEFAULT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `semester_sections`
--

CREATE TABLE `semester_sections` (
  `id` int(11) NOT NULL,
  `semester_name` varchar(100) NOT NULL,
  `school_year` varchar(20) DEFAULT NULL,
  `section_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester_sections`
--

INSERT INTO `semester_sections` (`id`, `semester_name`, `school_year`, `section_name`, `created_at`) VALUES
(1, '1ST SEMESTER', NULL, '', '2026-06-24 12:19:54'),
(2, '2ND SEMESTER', NULL, '', '2026-06-24 12:51:55'),
(3, 'SUMMER', NULL, '', '2026-06-24 12:51:55'),
(4, '', '2024-2025', '', '2026-06-24 12:56:01'),
(5, '', '2025-2026', '', '2026-06-24 12:56:01'),
(6, '', '2026-2027', '', '2026-06-24 12:56:01'),
(7, '', '2027-2028', '', '2026-06-24 12:56:01'),
(8, '', NULL, 'A', '2026-06-24 13:01:10'),
(9, '', NULL, 'B', '2026-06-24 13:01:10'),
(10, '', NULL, 'C', '2026-06-24 13:01:10'),
(11, '', NULL, 'D', '2026-06-24 13:01:10'),
(12, '3RD SEMESTER', NULL, '', '2026-07-11 11:11:47'),
(13, '4TH SEMESTER', NULL, '', '2026-07-11 16:19:41'),
(14, '', '2029-2030', '', '2026-07-11 16:26:10'),
(15, '', NULL, 'E', '2026-07-11 23:41:08'),
(16, '', NULL, 'F', '2026-07-11 23:41:15');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `section` varchar(50) NOT NULL,
  `status_type` enum('Unenrolled','Enrolled') NOT NULL DEFAULT 'Unenrolled',
  `requirements_status` varchar(50) DEFAULT NULL,
  `status_fee` enum('Fully Paid','Partially Paid','Unpaid') DEFAULT NULL,
  `status_credentials` enum('Complete','Incomplete','Pending') DEFAULT NULL,
  `let_status` tinyint(1) NOT NULL DEFAULT 0,
  `let_month` varchar(20) DEFAULT NULL,
  `let_year` varchar(10) DEFAULT NULL,
  `let_exam` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `fullname`, `contact_no`, `email_address`, `semester`, `school_year`, `section`, `status_type`, `requirements_status`, `status_fee`, `status_credentials`, `let_status`, `let_month`, `let_year`, `let_exam`) VALUES
(1, '124', 'SAMPLE STUDENT', '9815968031', 'dwightkishna1997@gmail.com', '2nd SEMESTER', '2025-2027', 'A', 'Unenrolled', NULL, NULL, '', 1, 'March', '2026', 1),
(2, '143', 'student a', '09815968030', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2029-2030', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 1),
(3, '200-11', 'SAMPLE STUDENT', '9815968031', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 1),
(4, '200-12', 'SAMPLE STUDENT', '9815968032', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, 'March', '2026', 1),
(5, '200-13', 'SAMPLE STUDENT', '9815968033', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, 'March', '2026', 1),
(6, '200-14', 'SAMPLE STUDENT', '9815968034', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 1, 'March', '2025', 1),
(7, '200-15', 'SAMPLE STUDENT', '9815968035', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 1, 'March', '2024', 1),
(8, '200-16', 'SAMPLE STUDENT', '9815968036', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 1),
(9, '200-17', 'SAMPLE STUDENT', '9815968037', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(10, '200-18', 'SAMPLE STUDENT', '9815968038', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(11, '200-19', 'SAMPLE STUDENT', '9815968039', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(12, '200-20', 'SAMPLE STUDENT', '9815968040', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(13, '200-21', 'SAMPLE STUDENT', '9815968041', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(14, '200-22', 'SAMPLE STUDENT', '9815968042', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(15, '200-23', 'SAMPLE STUDENT', '9815968043', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(16, '200-24', 'SAMPLE STUDENT', '9815968044', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(17, '200-25', 'SAMPLE STUDENT', '9815968045', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(18, '200-26', 'SAMPLE STUDENT', '9815968046', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(19, '200-27', 'SAMPLE STUDENT', '9815968047', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(20, '200-28', 'SAMPLE STUDENT', '9815968048', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(21, '200-29', 'SAMPLE STUDENT', '9815968049', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(22, '200-30', 'SAMPLE STUDENT', '9815968050', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(23, '200-31', 'SAMPLE STUDENT', '9815968051', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(24, '200-32', 'SAMPLE STUDENT', '9815968052', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(25, '200-33', 'SAMPLE STUDENT', '9815968053', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(26, '200-34', 'SAMPLE STUDENT', '9815968054', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(27, '200-35', 'SAMPLE STUDENT', '9815968055', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(28, '200-36', 'SAMPLE STUDENT', '9815968056', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(29, '200-37', 'SAMPLE STUDENT', '9815968057', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(30, '200-38', 'SAMPLE STUDENT', '9815968058', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(31, '200-39', 'SAMPLE STUDENT', '9815968059', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(32, '200-40', 'SAMPLE STUDENT', '9815968060', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(33, '200-41', 'SAMPLE STUDENT', '9815968061', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(34, '200-42', 'SAMPLE STUDENT', '9815968062', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(35, '200-43', 'SAMPLE STUDENT', '9815968063', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(36, '200-44', 'SAMPLE STUDENT', '9815968064', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(37, '200-45', 'SAMPLE STUDENT', '9815968065', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(38, '200-46', 'SAMPLE STUDENT', '9815968066', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(39, '200-47', 'SAMPLE STUDENT', '9815968067', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(40, '200-48', 'SAMPLE STUDENT', '9815968068', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(41, '200-49', 'SAMPLE STUDENT', '9815968069', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(42, '200-50', 'SAMPLE STUDENT', '9815968070', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(43, '200-51', 'SAMPLE STUDENT', '9815968071', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(44, '200-52', 'SAMPLE STUDENT', '9815968072', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(45, '200-53', 'SAMPLE STUDENT', '9815968073', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(46, '200-54', 'SAMPLE STUDENT', '9815968074', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(47, '200-55', 'SAMPLE STUDENT', '9815968075', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(48, '200-56', 'SAMPLE STUDENT', '9815968076', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(49, '200-57', 'SAMPLE STUDENT', '9815968077', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(50, '200-58', 'SAMPLE STUDENT', '9815968078', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(51, '200-59', 'SAMPLE STUDENT', '9815968079', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(52, '200-60', 'SAMPLE STUDENT', '9815968080', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(53, '200-61', 'SAMPLE STUDENT', '9815968081', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(54, '200-62', 'SAMPLE STUDENT', '9815968082', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(55, '200-63', 'SAMPLE STUDENT', '9815968083', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(56, '200-64', 'SAMPLE STUDENT', '9815968084', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(57, '200-65', 'SAMPLE STUDENT', '9815968085', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(58, '200-66', 'SAMPLE STUDENT', '9815968086', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(59, '200-67', 'SAMPLE STUDENT', '9815968087', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(60, '200-68', 'SAMPLE STUDENT', '9815968088', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(61, '200-69', 'SAMPLE STUDENT', '9815968089', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(62, '200-70', 'SAMPLE STUDENT', '9815968090', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(63, '200-71', 'SAMPLE STUDENT', '9815968091', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(64, '200-72', 'SAMPLE STUDENT', '9815968092', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(65, '200-73', 'SAMPLE STUDENT', '9815968093', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(66, '200-74', 'SAMPLE STUDENT', '9815968094', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(67, '200-75', 'SAMPLE STUDENT', '9815968095', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(68, '200-76', 'SAMPLE STUDENT', '9815968096', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(69, '200-77', 'SAMPLE STUDENT', '9815968097', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(70, '200-78', 'SAMPLE STUDENT', '9815968098', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(71, '200-79', 'SAMPLE STUDENT', '9815968099', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(72, '200-80', 'SAMPLE STUDENT', '9815968100', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(73, '200-81', 'SAMPLE STUDENT', '9815968101', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(74, '200-82', 'SAMPLE STUDENT', '9815968102', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(75, '200-83', 'SAMPLE STUDENT', '9815968103', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(76, '200-84', 'SAMPLE STUDENT', '9815968104', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(77, '200-85', 'SAMPLE STUDENT', '9815968105', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(78, '200-86', 'SAMPLE STUDENT', '9815968106', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(79, '200-87', 'SAMPLE STUDENT', '9815968107', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(80, '200-88', 'SAMPLE STUDENT', '9815968108', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(81, '200-89', 'SAMPLE STUDENT', '9815968109', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(82, '200-90', 'SAMPLE STUDENT', '9815968110', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(83, '200-91', 'SAMPLE STUDENT', '9815968111', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(84, '200-92', 'SAMPLE STUDENT', '9815968112', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(85, '200-93', 'SAMPLE STUDENT', '9815968113', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(86, '200-94', 'SAMPLE STUDENT', '9815968114', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(87, '200-95', 'SAMPLE STUDENT', '9815968115', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(88, '200-96', 'SAMPLE STUDENT', '9815968116', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(89, '200-97', 'SAMPLE STUDENT', '9815968117', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(90, '200-98', 'SAMPLE STUDENT', '9815968118', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(91, '200-99', 'SAMPLE STUDENT', '9815968119', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(92, '200-100', 'SAMPLE STUDENT', '9815968120', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(93, '200-101', 'SAMPLE STUDENT', '9815968121', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(94, '200-102', 'SAMPLE STUDENT', '9815968122', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(95, '200-103', 'SAMPLE STUDENT', '9815968123', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(96, '200-104', 'SAMPLE STUDENT', '9815968124', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(97, '200-105', 'SAMPLE STUDENT', '9815968125', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(98, '200-106', 'SAMPLE STUDENT', '9815968126', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(99, '200-107', 'SAMPLE STUDENT', '9815968127', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(100, '200-108', 'SAMPLE STUDENT', '9815968128', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(101, '200-109', 'SAMPLE STUDENT', '9815968129', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(102, '200-110', 'SAMPLE STUDENT', '9815968130', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(103, '200-111', 'SAMPLE STUDENT', '9815968131', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(104, '200-112', 'SAMPLE STUDENT', '9815968132', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(105, '200-113', 'SAMPLE STUDENT', '9815968133', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(106, '200-114', 'SAMPLE STUDENT', '9815968134', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(107, '200-115', 'SAMPLE STUDENT', '9815968135', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(108, '200-116', 'SAMPLE STUDENT', '9815968136', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(109, '200-117', 'SAMPLE STUDENT', '9815968137', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(110, '200-118', 'SAMPLE STUDENT', '9815968138', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(111, '200-119', 'SAMPLE STUDENT', '9815968139', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(112, '200-120', 'SAMPLE STUDENT', '9815968140', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(113, '200-121', 'SAMPLE STUDENT', '9815968141', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(114, '200-122', 'SAMPLE STUDENT', '9815968142', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(115, '200-123', 'SAMPLE STUDENT', '9815968143', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(116, '200-124', 'SAMPLE STUDENT', '9815968144', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(117, '200-125', 'SAMPLE STUDENT', '9815968145', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(118, '200-126', 'SAMPLE STUDENT', '9815968146', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(119, '200-127', 'SAMPLE STUDENT', '9815968147', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(120, '200-128', 'SAMPLE STUDENT', '9815968148', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(121, '200-129', 'SAMPLE STUDENT', '9815968149', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(122, '200-130', 'SAMPLE STUDENT', '9815968150', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(123, '200-131', 'SAMPLE STUDENT', '9815968151', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(124, '200-132', 'SAMPLE STUDENT', '9815968152', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(125, '200-133', 'SAMPLE STUDENT', '9815968153', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(126, '200-134', 'SAMPLE STUDENT', '9815968154', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(127, '200-135', 'SAMPLE STUDENT', '9815968155', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(128, '200-136', 'SAMPLE STUDENT', '9815968156', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(129, '200-137', 'SAMPLE STUDENT', '9815968157', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(130, '200-138', 'SAMPLE STUDENT', '9815968158', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(131, '200-139', 'SAMPLE STUDENT', '9815968159', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(132, '200-140', 'SAMPLE STUDENT', '9815968160', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(133, '200-141', 'SAMPLE STUDENT', '9815968161', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(134, '200-142', 'SAMPLE STUDENT', '9815968162', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(135, '200-143', 'SAMPLE STUDENT', '9815968163', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(136, '200-144', 'SAMPLE STUDENT', '9815968164', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(137, '200-145', 'SAMPLE STUDENT', '9815968165', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(138, '200-146', 'SAMPLE STUDENT', '9815968166', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(139, '200-147', 'SAMPLE STUDENT', '9815968167', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(140, '200-148', 'SAMPLE STUDENT', '9815968168', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(141, '200-149', 'SAMPLE STUDENT', '9815968169', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(142, '200-150', 'SAMPLE STUDENT', '9815968170', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(143, '200-151', 'SAMPLE STUDENT', '9815968171', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(144, '200-152', 'SAMPLE STUDENT', '9815968172', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(145, '200-153', 'SAMPLE STUDENT', '9815968173', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(146, '200-154', 'SAMPLE STUDENT', '9815968174', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(147, '200-155', 'SAMPLE STUDENT', '9815968175', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0),
(148, '200-156', 'SAMPLE STUDENT', '9815968176', 'dwightkishna1997@gmail.com', '1ST SEMESTER', '2025-2026', 'A', 'Unenrolled', NULL, NULL, NULL, 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_accounts`
--

CREATE TABLE `student_accounts` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `balance` decimal(10,2) DEFAULT 0.00,
  `total_paid` decimal(10,2) DEFAULT NULL,
  `registration_fee` tinyint(1) NOT NULL DEFAULT 0,
  `extra_fee` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student_accounts`
--

INSERT INTO `student_accounts` (`id`, `student_id`, `total_amount`, `balance`, `total_paid`, `registration_fee`, `extra_fee`) VALUES
(1, '124', 0.00, 0.00, 0.00, 0, 0),
(2, '143', 5000.00, 3500.00, 1500.00, 1, 0),
(3, '200-11', 0.00, 0.00, 0.00, 0, 0),
(4, '200-12', 0.00, 0.00, 0.00, 0, 0),
(5, '200-13', 0.00, 0.00, 0.00, 0, 0),
(6, '200-14', 0.00, 0.00, 0.00, 0, 0),
(7, '200-15', 0.00, 0.00, 0.00, 0, 0),
(8, '200-16', 0.00, 0.00, 0.00, 0, 0),
(9, '200-17', 0.00, 0.00, 0.00, 0, 0),
(10, '200-18', 0.00, 0.00, 0.00, 0, 0),
(11, '200-19', 0.00, 0.00, 0.00, 0, 0),
(12, '200-20', 0.00, 0.00, 0.00, 0, 0),
(13, '200-21', 0.00, 0.00, 0.00, 0, 0),
(14, '200-22', 0.00, 0.00, 0.00, 0, 0),
(15, '200-23', 0.00, 0.00, 0.00, 0, 0),
(16, '200-24', 0.00, 0.00, 0.00, 0, 0),
(17, '200-25', 0.00, 0.00, 0.00, 0, 0),
(18, '200-26', 0.00, 0.00, 0.00, 0, 0),
(19, '200-27', 0.00, 0.00, 0.00, 0, 0),
(20, '200-28', 0.00, 0.00, 0.00, 0, 0),
(21, '200-29', 0.00, 0.00, 0.00, 0, 0),
(22, '200-30', 0.00, 0.00, 0.00, 0, 0),
(23, '200-31', 0.00, 0.00, 0.00, 0, 0),
(24, '200-32', 0.00, 0.00, 0.00, 0, 0),
(25, '200-33', 0.00, 0.00, 0.00, 0, 0),
(26, '200-34', 0.00, 0.00, 0.00, 0, 0),
(27, '200-35', 0.00, 0.00, 0.00, 0, 0),
(28, '200-36', 0.00, 0.00, 0.00, 0, 0),
(29, '200-37', 0.00, 0.00, 0.00, 0, 0),
(30, '200-38', 0.00, 0.00, 0.00, 0, 0),
(31, '200-39', 0.00, 0.00, 0.00, 0, 0),
(32, '200-40', 0.00, 0.00, 0.00, 0, 0),
(33, '200-41', 0.00, 0.00, 0.00, 0, 0),
(34, '200-42', 0.00, 0.00, 0.00, 0, 0),
(35, '200-43', 0.00, 0.00, 0.00, 0, 0),
(36, '200-44', 0.00, 0.00, 0.00, 0, 0),
(37, '200-45', 0.00, 0.00, 0.00, 0, 0),
(38, '200-46', 0.00, 0.00, 0.00, 0, 0),
(39, '200-47', 0.00, 0.00, 0.00, 0, 0),
(40, '200-48', 0.00, 0.00, 0.00, 0, 0),
(41, '200-49', 0.00, 0.00, 0.00, 0, 0),
(42, '200-50', 0.00, 0.00, 0.00, 0, 0),
(43, '200-51', 0.00, 0.00, 0.00, 0, 0),
(44, '200-52', 0.00, 0.00, 0.00, 0, 0),
(45, '200-53', 0.00, 0.00, 0.00, 0, 0),
(46, '200-54', 0.00, 0.00, 0.00, 0, 0),
(47, '200-55', 0.00, 0.00, 0.00, 0, 0),
(48, '200-56', 0.00, 0.00, 0.00, 0, 0),
(49, '200-57', 0.00, 0.00, 0.00, 0, 0),
(50, '200-58', 0.00, 0.00, 0.00, 0, 0),
(51, '200-59', 0.00, 0.00, 0.00, 0, 0),
(52, '200-60', 0.00, 0.00, 0.00, 0, 0),
(53, '200-61', 0.00, 0.00, 0.00, 0, 0),
(54, '200-62', 0.00, 0.00, 0.00, 0, 0),
(55, '200-63', 0.00, 0.00, 0.00, 0, 0),
(56, '200-64', 0.00, 0.00, 0.00, 0, 0),
(57, '200-65', 0.00, 0.00, 0.00, 0, 0),
(58, '200-66', 0.00, 0.00, 0.00, 0, 0),
(59, '200-67', 0.00, 0.00, 0.00, 0, 0),
(60, '200-68', 0.00, 0.00, 0.00, 0, 0),
(61, '200-69', 0.00, 0.00, 0.00, 0, 0),
(62, '200-70', 0.00, 0.00, 0.00, 0, 0),
(63, '200-71', 0.00, 0.00, 0.00, 0, 0),
(64, '200-72', 0.00, 0.00, 0.00, 0, 0),
(65, '200-73', 0.00, 0.00, 0.00, 0, 0),
(66, '200-74', 0.00, 0.00, 0.00, 0, 0),
(67, '200-75', 0.00, 0.00, 0.00, 0, 0),
(68, '200-76', 0.00, 0.00, 0.00, 0, 0),
(69, '200-77', 0.00, 0.00, 0.00, 0, 0),
(70, '200-78', 0.00, 0.00, 0.00, 0, 0),
(71, '200-79', 0.00, 0.00, 0.00, 0, 0),
(72, '200-80', 0.00, 0.00, 0.00, 0, 0),
(73, '200-81', 0.00, 0.00, 0.00, 0, 0),
(74, '200-82', 0.00, 0.00, 0.00, 0, 0),
(75, '200-83', 0.00, 0.00, 0.00, 0, 0),
(76, '200-84', 0.00, 0.00, 0.00, 0, 0),
(77, '200-85', 0.00, 0.00, 0.00, 0, 0),
(78, '200-86', 0.00, 0.00, 0.00, 0, 0),
(79, '200-87', 0.00, 0.00, 0.00, 0, 0),
(80, '200-88', 0.00, 0.00, 0.00, 0, 0),
(81, '200-89', 0.00, 0.00, 0.00, 0, 0),
(82, '200-90', 0.00, 0.00, 0.00, 0, 0),
(83, '200-91', 0.00, 0.00, 0.00, 0, 0),
(84, '200-92', 0.00, 0.00, 0.00, 0, 0),
(85, '200-93', 0.00, 0.00, 0.00, 0, 0),
(86, '200-94', 0.00, 0.00, 0.00, 0, 0),
(87, '200-95', 0.00, 0.00, 0.00, 0, 0),
(88, '200-96', 0.00, 0.00, 0.00, 0, 0),
(89, '200-97', 0.00, 0.00, 0.00, 0, 0),
(90, '200-98', 0.00, 0.00, 0.00, 0, 0),
(91, '200-99', 0.00, 0.00, 0.00, 0, 0),
(92, '200-100', 0.00, 0.00, 0.00, 0, 0),
(93, '200-101', 0.00, 0.00, 0.00, 0, 0),
(94, '200-102', 0.00, 0.00, 0.00, 0, 0),
(95, '200-103', 0.00, 0.00, 0.00, 0, 0),
(96, '200-104', 0.00, 0.00, 0.00, 0, 0),
(97, '200-105', 0.00, 0.00, 0.00, 0, 0),
(98, '200-106', 0.00, 0.00, 0.00, 0, 0),
(99, '200-107', 0.00, 0.00, 0.00, 0, 0),
(100, '200-108', 0.00, 0.00, 0.00, 0, 0),
(101, '200-109', 0.00, 0.00, 0.00, 0, 0),
(102, '200-110', 0.00, 0.00, 0.00, 0, 0),
(103, '200-111', 0.00, 0.00, 0.00, 0, 0),
(104, '200-112', 0.00, 0.00, 0.00, 0, 0),
(105, '200-113', 0.00, 0.00, 0.00, 0, 0),
(106, '200-114', 0.00, 0.00, 0.00, 0, 0),
(107, '200-115', 0.00, 0.00, 0.00, 0, 0),
(108, '200-116', 0.00, 0.00, 0.00, 0, 0),
(109, '200-117', 0.00, 0.00, 0.00, 0, 0),
(110, '200-118', 0.00, 0.00, 0.00, 0, 0),
(111, '200-119', 0.00, 0.00, 0.00, 0, 0),
(112, '200-120', 0.00, 0.00, 0.00, 0, 0),
(113, '200-121', 0.00, 0.00, 0.00, 0, 0),
(114, '200-122', 0.00, 0.00, 0.00, 0, 0),
(115, '200-123', 0.00, 0.00, 0.00, 0, 0),
(116, '200-124', 0.00, 0.00, 0.00, 0, 0),
(117, '200-125', 0.00, 0.00, 0.00, 0, 0),
(118, '200-126', 0.00, 0.00, 0.00, 0, 0),
(119, '200-127', 0.00, 0.00, 0.00, 0, 0),
(120, '200-128', 0.00, 0.00, 0.00, 0, 0),
(121, '200-129', 0.00, 0.00, 0.00, 0, 0),
(122, '200-130', 0.00, 0.00, 0.00, 0, 0),
(123, '200-131', 0.00, 0.00, 0.00, 0, 0),
(124, '200-132', 0.00, 0.00, 0.00, 0, 0),
(125, '200-133', 0.00, 0.00, 0.00, 0, 0),
(126, '200-134', 0.00, 0.00, 0.00, 0, 0),
(127, '200-135', 0.00, 0.00, 0.00, 0, 0),
(128, '200-136', 0.00, 0.00, 0.00, 0, 0),
(129, '200-137', 0.00, 0.00, 0.00, 0, 0),
(130, '200-138', 0.00, 0.00, 0.00, 0, 0),
(131, '200-139', 0.00, 0.00, 0.00, 0, 0),
(132, '200-140', 0.00, 0.00, 0.00, 0, 0),
(133, '200-141', 0.00, 0.00, 0.00, 0, 0),
(134, '200-142', 0.00, 0.00, 0.00, 0, 0),
(135, '200-143', 0.00, 0.00, 0.00, 0, 0),
(136, '200-144', 0.00, 0.00, 0.00, 0, 0),
(137, '200-145', 0.00, 0.00, 0.00, 0, 0),
(138, '200-146', 0.00, 0.00, 0.00, 0, 0),
(139, '200-147', 0.00, 0.00, 0.00, 0, 0),
(140, '200-148', 0.00, 0.00, 0.00, 0, 0),
(141, '200-149', 0.00, 0.00, 0.00, 0, 0),
(142, '200-150', 0.00, 0.00, 0.00, 0, 0),
(143, '200-151', 0.00, 0.00, 0.00, 0, 0),
(144, '200-152', 0.00, 0.00, 0.00, 0, 0),
(145, '200-153', 0.00, 0.00, 0.00, 0, 0),
(146, '200-154', 0.00, 0.00, 0.00, 0, 0),
(147, '200-155', 0.00, 0.00, 0.00, 0, 0),
(148, '200-156', 0.00, 0.00, 0.00, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_credentials`
--

CREATE TABLE `student_credentials` (
  `id` int(11) NOT NULL,
  `student_id` varchar(100) NOT NULL,
  `tor_informative_copy` tinyint(1) DEFAULT 0,
  `tor_informative_copy_date` date DEFAULT NULL,
  `tor_informative_copy_by` varchar(100) DEFAULT NULL,
  `gmc` tinyint(1) DEFAULT 0,
  `gmc_date` date DEFAULT NULL,
  `gmc_by` varchar(100) DEFAULT NULL,
  `ctc_request` tinyint(1) DEFAULT 0,
  `ctc_request_date` date DEFAULT NULL,
  `ctc_request_by` varchar(100) DEFAULT NULL,
  `account_card` tinyint(1) DEFAULT 0,
  `account_card_date` date DEFAULT NULL,
  `account_card_by` varchar(100) DEFAULT NULL,
  `ctc_tor_granted` tinyint(1) DEFAULT 0,
  `ctc_tor_granted_date` date DEFAULT NULL,
  `ctc_tor_granted_by` varchar(100) DEFAULT NULL,
  `psa_birth_marriage_certificate` tinyint(1) DEFAULT 0,
  `psa_birth_marriage_certificate_date` date DEFAULT NULL,
  `psa_birth_marriage_certificate_by` varchar(100) DEFAULT NULL,
  `photo_2x2_hardcopy` tinyint(1) DEFAULT 0,
  `photo_2x2_hardcopy_date` date DEFAULT NULL,
  `photo_2x2_hardcopy_by` varchar(100) DEFAULT NULL,
  `photo_2x2_softcopy` tinyint(1) DEFAULT 0,
  `photo_2x2_softcopy_date` date DEFAULT NULL,
  `photo_2x2_softcopy_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_credentials`
--

INSERT INTO `student_credentials` (`id`, `student_id`, `tor_informative_copy`, `tor_informative_copy_date`, `tor_informative_copy_by`, `gmc`, `gmc_date`, `gmc_by`, `ctc_request`, `ctc_request_date`, `ctc_request_by`, `account_card`, `account_card_date`, `account_card_by`, `ctc_tor_granted`, `ctc_tor_granted_date`, `ctc_tor_granted_by`, `psa_birth_marriage_certificate`, `psa_birth_marriage_certificate_date`, `psa_birth_marriage_certificate_by`, `photo_2x2_hardcopy`, `photo_2x2_hardcopy_date`, `photo_2x2_hardcopy_by`, `photo_2x2_softcopy`, `photo_2x2_softcopy_date`, `photo_2x2_softcopy_by`) VALUES
(1, '124', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_monitor`
--

CREATE TABLE `student_monitor` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `admission_status` varchar(20) DEFAULT 'Not Release',
  `admission_date` date DEFAULT NULL,
  `admission_by` varchar(100) DEFAULT NULL,
  `enrolled_status` varchar(20) DEFAULT 'Not Release',
  `enrolled_date` date DEFAULT NULL,
  `enrolled_by` varchar(100) DEFAULT NULL,
  `assessment_status` varchar(20) DEFAULT 'Not Release',
  `assessment_date` date DEFAULT NULL,
  `assessment_by` varchar(100) DEFAULT NULL,
  `release_docs_status` varchar(20) DEFAULT 'Not Release',
  `release_docs_date` date DEFAULT NULL,
  `release_docs_by` varchar(100) DEFAULT NULL,
  `release_docs_tracking` varchar(100) DEFAULT NULL,
  `tor_status` varchar(20) DEFAULT 'Not Release',
  `tor_date` date DEFAULT NULL,
  `tor_by` varchar(100) DEFAULT NULL,
  `tor_tracking` varchar(100) DEFAULT NULL,
  `let_status` varchar(20) DEFAULT 'Not Passer',
  `let_month` varchar(2) DEFAULT NULL,
  `let_year` varchar(4) DEFAULT NULL,
  `let_by` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_monitor`
--

INSERT INTO `student_monitor` (`id`, `student_id`, `admission_status`, `admission_date`, `admission_by`, `enrolled_status`, `enrolled_date`, `enrolled_by`, `assessment_status`, `assessment_date`, `assessment_by`, `release_docs_status`, `release_docs_date`, `release_docs_by`, `release_docs_tracking`, `tor_status`, `tor_date`, `tor_by`, `tor_tracking`, `let_status`, `let_month`, `let_year`, `let_by`, `updated_at`) VALUES
(1, '124', 'Not Release', NULL, NULL, 'Not Release', NULL, NULL, 'Not Release', NULL, NULL, 'Not Release', NULL, NULL, NULL, 'Not Release', NULL, NULL, NULL, 'Passer', 'Ma', '2026', NULL, '2026-08-14 22:23:16'),
(2, '200-11', 'Not Release', NULL, NULL, 'Not Release', NULL, NULL, 'Not Release', NULL, NULL, 'Not Release', NULL, NULL, NULL, 'Not Release', NULL, NULL, NULL, 'Passer', 'Ma', '2026', NULL, '2026-08-14 22:23:29'),
(3, '200-12', 'Not Release', NULL, NULL, 'Not Release', NULL, NULL, 'Not Release', NULL, NULL, 'Not Release', NULL, NULL, NULL, 'Not Release', NULL, NULL, NULL, 'Passer', 'Ma', '2026', NULL, '2026-08-14 22:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `fullname`, `email`, `course`, `date_hired`, `date_added`) VALUES
(1, 'Dwight Kishna R. Sevilla', 'dwightkishna1997@gmail.com', 'BSIT', '2026-03-19', '2026-03-19 02:01:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `role`, `date_created`, `must_change_password`) VALUES
(1, 'crulona', 'password', 'Christie Dianne S. Rulona', 'superadmin', '2026-03-19 01:46:14', 0),
(3, 'registrar', 'tcp2026!', 'Mylene Mae Managa', 'admin', '2026-03-26 12:24:50', 1),
(7, 'dsevilla', '12345678', 'Dwight Sevilla', 'User', '2026-08-21 01:24:23', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `docs_history`
--
ALTER TABLE `docs_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_fees`
--
ALTER TABLE `document_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fee_key` (`fee_key`);

--
-- Indexes for table `document_request_monitoring`
--
ALTER TABLE `document_request_monitoring`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_history`
--
ALTER TABLE `email_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_signatures`
--
ALTER TABLE `email_signatures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `let_year`
--
ALTER TABLE `let_year`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `year` (`year`);

--
-- Indexes for table `or_booklets`
--
ALTER TABLE `or_booklets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrar_directory`
--
ALTER TABLE `registrar_directory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrar_email_history`
--
ALTER TABLE `registrar_email_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `semester_sections`
--
ALTER TABLE `semester_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_accounts`
--
ALTER TABLE `student_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_credentials`
--
ALTER TABLE `student_credentials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `student_monitor`
--
ALTER TABLE `student_monitor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `docs_history`
--
ALTER TABLE `docs_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `document_fees`
--
ALTER TABLE `document_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `document_request_monitoring`
--
ALTER TABLE `document_request_monitoring`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_history`
--
ALTER TABLE `email_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_signatures`
--
ALTER TABLE `email_signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `let_year`
--
ALTER TABLE `let_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `or_booklets`
--
ALTER TABLE `or_booklets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registrar_directory`
--
ALTER TABLE `registrar_directory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `registrar_email_history`
--
ALTER TABLE `registrar_email_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `semester_sections`
--
ALTER TABLE `semester_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `student_accounts`
--
ALTER TABLE `student_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `student_credentials`
--
ALTER TABLE `student_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_monitor`
--
ALTER TABLE `student_monitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
