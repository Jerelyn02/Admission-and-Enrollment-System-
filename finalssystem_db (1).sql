-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2025 at 05:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finalssystem_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(50) NOT NULL DEFAULT 'user',
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `name`, `email`, `password`, `user_type`, `email_verified`, `verification_code`) VALUES
(2, 'neilgwapings', 'neilgwapo@email.com', '321', 'admin', 0, NULL),
(10, 'neilpogi', 'neilpogi@gnail.com', '123', 'user', 0, NULL),
(11, 'vincent', 'vincent@gmall.com', '123', 'user', 0, NULL),
(12, 'tragis', 'islit@yeah.com', '713', 'user', 0, NULL),
(13, 'vincent', 'neil@ymail.com', '123', 'user', 0, NULL),
(14, 'elijah', 'burata@gnail.com', '123', 'user', 0, NULL),
(15, 'yugo', 'jego@gnil.com', '123', 'user', 0, NULL),
(16, 'trykolang', 'tjto@gmail.com', '1234', 'user', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admission_info`
--

CREATE TABLE `admission_info` (
  `ai_id` int(20) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `entry` varchar(20) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `strand` varchar(50) DEFAULT NULL,
  `lrn` bigint(12) DEFAULT NULL,
  `program` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission_info`
--

INSERT INTO `admission_info` (`ai_id`, `username`, `entry`, `type`, `strand`, `lrn`, `program`) VALUES
(12, 'neilpogi@gnail.com', 'New', 'Grade 12 student', 'TVL', 123456789012, 'BSIT'),
(13, 'vincent@gmall.com', 'Transferee', 'Grade 12 student', 'STEM', 123456789019, 'BSIT'),
(14, 'islit@yeah.com', 'New', 'Grade 12 student', 'TVL', 123456789019, 'BSCS'),
(15, 'neil@ymail.com', 'New', 'SHS Graduate', 'TVL', 123456789013, 'BSIT'),
(16, 'burata@gnail.com', 'New', 'SHS Graduate', 'TVL', 123456789018, 'BSIT'),
(17, 'jego@gnil.com', 'Transferee', 'SHS Graduate', 'TVL', 123456789016, 'BSIT');

-- --------------------------------------------------------

--
-- Table structure for table `application_status`
--

CREATE TABLE `application_status` (
  `username` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `grade_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application_status`
--

INSERT INTO `application_status` (`username`, `status`, `grade_status`) VALUES
('burata@gnail.com', 1, 2),
('islit@yeah.com', 1, 2),
('jego@gnil.com', 1, 2),
('neil@ymail.com', 1, 1),
('neilpogi@gnail.com', 0, 0),
('vincent@gmall.com', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `check_status`
--

CREATE TABLE `check_status` (
  `username` varchar(50) NOT NULL,
  `admission_info_completed` tinyint(1) DEFAULT 0,
  `personal_info_completed` tinyint(1) DEFAULT 0,
  `family_bg_completed` tinyint(1) DEFAULT 0,
  `education_bg_completed` tinyint(1) DEFAULT 0,
  `med_his_info_completed` tinyint(1) DEFAULT 0,
  `control_number_click` tinyint(4) DEFAULT 0,
  `control_number` varchar(20) DEFAULT NULL,
  `current_stage` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `check_status`
--

INSERT INTO `check_status` (`username`, `admission_info_completed`, `personal_info_completed`, `family_bg_completed`, `education_bg_completed`, `med_his_info_completed`, `control_number_click`, `control_number`, `current_stage`) VALUES
('burata@gnail.com', 1, 1, 1, 1, 1, 1, 'CN-00005', 4),
('islit@yeah.com', 1, 1, 1, 1, 1, 1, 'CN-00003', 3),
('jego@gnil.com', 1, 1, 1, 1, 1, 1, 'CN-00006', 4),
('neil@ymail.com', 1, 1, 1, 1, 1, 1, 'CN-00004', 4),
('neilpogi@gnail.com', 1, 1, 1, 1, 1, 1, 'CN-00001', 3),
('TJ14@gmail.com', 0, 1, 1, 1, 1, 0, NULL, 1),
('vincent@gmall.com', 1, 1, 1, 1, 1, 1, 'CN-00002', 3);

-- --------------------------------------------------------

--
-- Table structure for table `confirmed_exams`
--

CREATE TABLE `confirmed_exams` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `schedule` datetime NOT NULL,
  `meeting_link` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `confirmed_exams`
--

INSERT INTO `confirmed_exams` (`id`, `username`, `schedule`, `meeting_link`, `created_at`) VALUES
(4, 'vincent@gmall.com', '2025-06-13 15:20:00', 'https://meet.google.com/rjg-sqtc-etp', '2025-06-13 13:19:09'),
(5, 'islit@yeah.com', '2025-06-13 20:07:00', 'https://meet.google.com/rjg-sqtc-etp', '2025-06-13 16:21:28'),
(6, 'neil@ymail.com', '2025-06-13 15:20:00', 'https://meet.google.com/rjg-sqtc-etp', '2025-06-13 18:10:38'),
(7, 'burata@gnail.com', '2025-06-13 15:20:00', 'https://meet.google.com/rjg-sqtc-etp', '2025-06-14 02:00:44'),
(9, 'jego@gnil.com', '2025-06-14 10:24:00', 'https://meet.google.com/rjg-sqtc-etp', '2025-06-14 02:27:03');

-- --------------------------------------------------------

--
-- Table structure for table `education_bg`
--

CREATE TABLE `education_bg` (
  `id` int(20) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `elemname` varchar(50) DEFAULT NULL,
  `elemaddress` varchar(50) DEFAULT NULL,
  `elemyear` int(11) DEFAULT NULL,
  `elemtype` varchar(11) DEFAULT NULL,
  `midname` varchar(50) DEFAULT NULL,
  `midaddress` varchar(50) DEFAULT NULL,
  `midyear` int(11) DEFAULT NULL,
  `midtype` varchar(11) DEFAULT NULL,
  `seniorname` varchar(50) DEFAULT NULL,
  `senioraddress` varchar(50) DEFAULT NULL,
  `senioryear` int(11) DEFAULT NULL,
  `seniortype` varchar(11) DEFAULT NULL,
  `vocname` varchar(50) NOT NULL,
  `vocaddress` varchar(50) NOT NULL,
  `vocyear` int(11) NOT NULL,
  `voctype` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education_bg`
--

INSERT INTO `education_bg` (`id`, `username`, `elemname`, `elemaddress`, `elemyear`, `elemtype`, `midname`, `midaddress`, `midyear`, `midtype`, `seniorname`, `senioraddress`, `senioryear`, `seniortype`, `vocname`, `vocaddress`, `vocyear`, `voctype`) VALUES
(6, 'neilpogi@gnail.com', 'pogi school', 'brgy. pogi', 2014, 'Private', 'Gwapo Junior school', 'brgy gwapo', 2017, 'Public', 'Pighati University', 'brgy. pighati', 2019, 'Private', '', '', 0, 'Select Item'),
(7, 'vincent@gmall.com', 'pogi school', 'brgy. pogi', 2014, 'Public', 'Gwapo Junior school', 'brgy gwapo', 2017, 'Public', 'Pighati University', 'brgy. pighati', 2019, 'Select Item', '', '', 0, 'Select Item'),
(8, 'islit@yeah.com', 'pogi school', 'brgy. pogi', 2014, 'Private', 'Gwapo Junior school', 'brgy gwapo', 2017, 'Private', 'Pighati University', 'brgy. pighati', 2019, 'Public', '', '', 0, 'Select Item'),
(9, 'neil@ymail.com', 'pogi school', 'brgy. pogi', 2014, 'Public', 'Gwapo Junior school', 'brgy gwapo', 2017, 'Public', 'Pighati University', 'brgy. pighati', 2019, 'Public', '', '', 0, 'Select Item'),
(10, 'burata@gnail.com', 'Bayag Elementary School', 'Brgy. Bayag', 2014, 'Public', 'Bading Highschool', 'Badingan City', 2017, 'Private', 'Sigma University', 'Tindahan ni Sigma', 2019, 'Private', '', '', 0, 'Select Item'),
(11, 'jego@gnil.com', 'Bayag Elementary School', 'Brgy. Bayag', 2055, 'Public', 'Bading Highschool', 'Badingan City', 2013, 'Private', 'Sigma University', 'Tindahan ni Sigma', 2045, 'Private', '', '', 0, 'Select Item'),
(12, 'TJ14@gmail.com', 'Bayag Elementary School', 'Brgy. Bayag', 2014, 'Public', '44', '4444', 2011, 'Private', 'Sigma University', 'Tindahan ni Sigma', 2011, 'Private', 'f', 'f', 0, 'Public');

-- --------------------------------------------------------

--
-- Table structure for table `exam_answers`
--

CREATE TABLE `exam_answers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_no` int(11) NOT NULL,
  `answer` text DEFAULT 'N/A',
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_answers`
--

INSERT INTO `exam_answers` (`id`, `email`, `exam_id`, `question_no`, `answer`, `submitted_at`) VALUES
(1, 'vincent@gmall.com', 29, 1, 'Neil', '2025-06-14 00:10:52'),
(2, 'islit@yeah.com', 31, 1, 'N/A', '2025-06-14 01:51:39'),
(3, 'neil@ymail.com', 29, 1, 'N/A', '2025-06-14 02:11:06'),
(4, 'burata@gnail.com', 29, 1, 'Elijah', '2025-06-14 10:01:03'),
(5, 'jego@gnil.com', 33, 1, 'Neil', '2025-06-14 10:27:50'),
(6, 'jego@gnil.com', 33, 2, 'yugo', '2025-06-14 10:27:50'),
(7, 'jego@gnil.com', 33, 3, 'may kapiling siyang iba', '2025-06-14 10:27:50'),
(8, 'jego@gnil.com', 33, 4, 'vash', '2025-06-14 10:27:50');

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `attempt_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `is_submitted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_attempts`
--

INSERT INTO `exam_attempts` (`attempt_id`, `email`, `exam_id`, `started_at`, `ended_at`, `is_submitted`) VALUES
(2, 'vincent@gmall.com', 29, '2025-06-13 21:19:11', '2025-06-14 00:10:52', 1),
(3, 'islit@yeah.com', 31, '2025-06-14 00:21:33', '2025-06-14 01:51:39', 1),
(4, 'neil@ymail.com', 29, '2025-06-14 02:10:45', '2025-06-14 02:11:06', 1),
(5, 'burata@gnail.com', 29, '2025-06-14 10:00:53', '2025-06-14 10:01:03', 1),
(6, 'jego@gnil.com', 33, '2025-06-14 10:27:05', '2025-06-14 10:27:50', 1);

-- --------------------------------------------------------

--
-- Table structure for table `exam_category`
--

CREATE TABLE `exam_category` (
  `exam_id` int(5) NOT NULL,
  `category` varchar(100) NOT NULL,
  `time_min` varchar(5) NOT NULL,
  `schedule` datetime DEFAULT NULL,
  `meeting_link` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_category`
--

INSERT INTO `exam_category` (`exam_id`, `category`, `time_min`, `schedule`, `meeting_link`) VALUES
(29, 'BSIT', '30', '2025-06-13 15:20:00', 'https://meet.google.com/rjg-sqtc-etp'),
(30, 'BSIT', '30', '2025-06-15 13:59:00', 'https://meet.google.com/rjg-sqtc-etp'),
(31, 'BSCS', '20', '2025-06-13 20:07:00', 'https://meet.google.com/rjg-sqtc-etp'),
(33, 'BSIT', '30', '2025-06-14 10:24:00', 'https://meet.google.com/rjg-sqtc-etp'),
(35, 'BSIT', '30', '2025-06-12 21:40:00', 'https://meet.google.com/mwe-unir-rkn'),
(36, 'BSIT', '10', '2025-06-14 10:15:00', 'https://ph.pinterest.com/');

-- --------------------------------------------------------

--
-- Table structure for table `family_bg`
--

CREATE TABLE `family_bg` (
  `id` int(20) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  `gurdianname` varchar(50) DEFAULT NULL,
  `gnumber` bigint(11) DEFAULT NULL,
  `goccupation` varchar(50) DEFAULT NULL,
  `fathername` varchar(50) DEFAULT NULL,
  `fnumber` bigint(11) DEFAULT NULL,
  `foccupation` varchar(50) DEFAULT NULL,
  `mothername` varchar(50) DEFAULT NULL,
  `mnumber` bigint(11) DEFAULT NULL,
  `moccupation` varchar(50) DEFAULT NULL,
  `fam_month_inc` varchar(20) DEFAULT NULL,
  `numsibling` int(15) DEFAULT NULL,
  `birthorder` varchar(50) DEFAULT NULL,
  `soloparent` varchar(20) DEFAULT NULL,
  `fam_work_abroad` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `family_bg`
--

INSERT INTO `family_bg` (`id`, `username`, `gurdianname`, `gnumber`, `goccupation`, `fathername`, `fnumber`, `foccupation`, `mothername`, `mnumber`, `moccupation`, `fam_month_inc`, `numsibling`, `birthorder`, `soloparent`, `fam_work_abroad`) VALUES
(6, 'neilpogi@gnail.com', 'San Miguel', 12345678901, 'Leader of the Heavenly Army', 'Nestor Ibalio', 12345678902, 'Security Officer', 'Delia Ibalio', 12345678903, 'Cook', '10000', 4, 'Third', 'No', 'No'),
(7, 'vincent@gmall.com', 'San Miguel', 12345678901, 'Leader of the Heavenly Army', 'Nestor Ibalio', 12345678902, 'Security Officer', 'Delia Ibalio', 12345678903, 'Cook', '10000', 4, 'Third', 'No', 'No'),
(8, 'islit@yeah.com', 'San Miguel', 12345678901, 'Leader of the Heavenly Army', 'Nestor Ibalio', 12345678902, 'Security Officer', 'Delia Ibalio', 12345678903, 'Cook', '10000', 4, 'Third', 'No', 'No'),
(9, 'neil@ymail.com', 'San Miguel', 12345678901, 'Leader of the Heavenly Army', 'Nestor Ibalio', 12345678902, 'Security Officer', 'Delia Ibalio', 12345678903, 'Cook', '10000', 4, 'Third', 'No', 'No'),
(10, 'burata@gnail.com', 'San Michael', 9876678934, 'Gurdian Angel', 'Gabriel Burata', 9876678935, 'Massager', 'Tony Burata', 9876678936, 'Human Trafficker', '10', -2, '-1', 'No', 'No'),
(11, 'jego@gnil.com', 'San Michael', 9876678934, 'Gurdian Angel', 'Gabriel Burata', 9876678935, 'Massager', 'Tony Burata', 9876678936, 'Human Trafficker', '1', 0, 'Fifth', 'Yes', 'No'),
(12, 'TJ14@gmail.com', 'San', 9876678934, 'Gurdian Angel', 'Gabriel Burata', 9876678935, 'Massager', 'Tony Burata', 9876678936, 'Human Trafficker', '10', -2, '-1', 'Yes', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `freshmen_files`
--

CREATE TABLE `freshmen_files` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `control_number` varchar(100) DEFAULT NULL,
  `report_card` varchar(255) DEFAULT NULL,
  `gmc` varchar(255) DEFAULT NULL,
  `birth_cert` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freshmen_files`
--

INSERT INTO `freshmen_files` (`id`, `username`, `control_number`, `report_card`, `gmc`, `birth_cert`, `submitted_at`) VALUES
(1, 'neilpogi@gnail.com', 'CN-00001', 'neilpogi@gnail.com_report_card_1749810184.jpg', 'neilpogi@gnail.com_gmc_1749810184.jpg', 'neilpogi@gnail.com_birth_cert_1749810184.jpg', '2025-06-13 04:23:04'),
(2, 'islit@yeah.com', 'CN-00003', 'islit@yeah.com_report_card_1749831584.jpg', 'islit@yeah.com_gmc_1749831584.jpg', 'islit@yeah.com_birth_cert_1749831584.jpg', '2025-06-13 10:19:44'),
(3, 'neil@ymail.com', 'CN-00004', 'neil@ymail.com_report_card_1749838173.jpg', 'neil@ymail.com_gmc_1749838173.jpg', 'neil@ymail.com_birth_cert_1749838173.jpg', '2025-06-13 12:09:33'),
(4, 'burata@gnail.com', 'CN-00005', 'burata@gnail.com_report_card_1749866235.png', 'burata@gnail.com_gmc_1749866235.png', 'burata@gnail.com_birth_cert_1749866235.png', '2025-06-13 19:57:15');

-- --------------------------------------------------------

--
-- Table structure for table `med_his_info`
--

CREATE TABLE `med_his_info` (
  `id` int(20) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `medications` text NOT NULL,
  `medical_conditions` text NOT NULL,
  `allergies` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `med_his_info`
--

INSERT INTO `med_his_info` (`id`, `username`, `medications`, `medical_conditions`, `allergies`) VALUES
(6, 'neilpogi@gnail.com', 'kiss', 'Scoliosis,Diabetes,Asthma,Heart Disease', 'na makita siyang nasa iba'),
(7, 'vincent@gmall.com', 'kiss', 'None', 'na makita siyang nasa iba'),
(8, 'islit@yeah.com', 'kiss', 'Asthma,Heart Disease', 'na makita siyang nasa iba'),
(9, 'neil@ymail.com', 'kiss', 'Scoliosis,Asthma,Heart Disease', 'na makita siyang nasa iba'),
(10, 'burata@gnail.com', 'halik ni azel', 'Scoliosis,Diabetes,Asthma,Heart Disease', 'makita si azel na malungkot'),
(11, 'jego@gnil.com', 'Jabol', 'Scoliosis,Asthma,Heart Disease', 'makita si Jerelyn, jelai, anne, khat at teodoro na malungkot');

-- --------------------------------------------------------

--
-- Table structure for table `personal_info`
--

CREATE TABLE `personal_info` (
  `id` int(20) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `phonenumber` bigint(11) DEFAULT NULL,
  `civilstatus` varchar(20) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `birthday` varchar(50) DEFAULT NULL,
  `birthplace` varchar(50) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_info`
--

INSERT INTO `personal_info` (`id`, `username`, `firstname`, `middlename`, `lastname`, `region`, `province`, `town`, `phonenumber`, `civilstatus`, `sex`, `birthday`, `birthplace`, `religion`) VALUES
(7, 'neilpogi@gnail.com', 'Neil Pogi', 'Diaz', 'Ibalio', 'Region IV-A', 'Cavite', 'Dasmariñas', 9058036798, 'Single', 'Male', '2004-11-29', 'Trece Martires', 'Christian'),
(8, 'vincent@gmall.com', 'Neil Gwapo', 'Diaz', 'Ibalio', 'Region VII', 'Cebu', 'Cebu City', 90580367989, 'Single', 'Male', '2005-06-13', 'Cebu City', 'Christian'),
(9, 'islit@yeah.com', 'Tragis', '', 'Scott', 'Region III', 'Laguna', 'Calamba', 9058036798, 'Single', 'Male', '2003-07-24', 'Metro ng Tubig', 'Inglesia ni Cris Brown'),
(10, 'neil@ymail.com', 'Neil Gwapings', 'Diaz', 'Ibalio', 'Region IV-A', 'Cavite', 'Dasmariñas', 9058036798, 'Single', 'Male', '2004-07-19', 'Trece Martires', 'Christian'),
(11, 'burata@gnail.com', 'Elijah', 'Maliit', 'Burata', 'Region IV-A', 'Cavite', 'Dasmariñas', 9876543219, 'Married', 'Female', '2004-12-28', 'North Pole', 'Diddy Party'),
(12, 'jego@gnil.com', 'Jray Lang', 'D.', 'Kaiiwan', 'Region VII', 'Davao del Sur', 'Davao City', 9876543218, 'Single', 'Female', '1998-06-25', 'Sa Cr', 'Diddy Party'),
(13, 'TJ14@gmail.com', 'tj', 'tttttt', 'tttt', 'SOCCSKSARGEN (Region XII)', 'Cotabato', 'Makilala', 9876543218, 'Single', 'Male', '3333-03-03', 't', 't');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(5) NOT NULL,
  `question_no` varchar(500) NOT NULL,
  `question` varchar(500) NOT NULL,
  `opt1` varchar(500) NOT NULL,
  `opt2` varchar(500) NOT NULL,
  `opt3` varchar(500) NOT NULL,
  `opt4` varchar(500) NOT NULL,
  `answer` varchar(500) NOT NULL,
  `exam_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question_no`, `question`, `opt1`, `opt2`, `opt3`, `opt4`, `answer`, `exam_id`) VALUES
(17, '1', 'sino tall, nonchalant, handsome member?', 'Neil', 'Jovan', 'Jaspher', 'Elijah', 'Neil', 29),
(18, '1', 'what is the most scariest thing in the universe?', 'Death', 'Momo', 'Job Aplication', 'Mawala Siya', 'Job Application', 30),
(19, '2', 'sino tall, nonchalant, handsome member?', 'Jaspher', 'Elijah', 'Vash', 'Neil', 'Neil', 30),
(20, '1', 'sino tall, nonchalant, handsome member?', 'Neil', 'Jovan', 'Jaspher', 'Elijah', 'Neil', 33),
(22, '1', 'fghgh', 'rjy', 'ry', 'tik', 'e', 'e', 31),
(23, '1', 'sino ang tall, nonchalant, handsome?', 'duke', 'cenat', 'ksi', 'yugo', 'yugo', 36),
(24, '2', 'ano ang isa sa kinakatakutan ng sang-katauhan?', 'babae', 'job application', 'diddy party', 'may kapiling siyang iba', 'job application', 36),
(25, '3', 'sino malakas kumain?', 'tj', 'jaspher', 'neil', 'Bash', 'Bash', 36),
(26, '2', 'sino ang tall, nonchalant, handsome?', 'duke', 'cenat', 'ksi', 'yugo', 'yugo', 33),
(27, '3', 'ano ang isa sa kinakatakutan ng sang-katauhan?', 'babae', 'job application', 'diddy party', 'may kapiling siyang iba', 'job application', 33),
(28, '4', 'sino malakas kumain?', 'arrabis', 'pecure', 'vash', 'neilrho', 'pecure', 33);

-- --------------------------------------------------------

--
-- Table structure for table `second_courser_files`
--

CREATE TABLE `second_courser_files` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `control_number` varchar(100) DEFAULT NULL,
  `tor` varchar(255) DEFAULT NULL,
  `gmc` varchar(255) DEFAULT NULL,
  `birth_cert` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transferee_files`
--

CREATE TABLE `transferee_files` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `control_number` varchar(100) DEFAULT NULL,
  `tor` varchar(255) DEFAULT NULL,
  `dismissal` varchar(255) DEFAULT NULL,
  `gmc` varchar(255) DEFAULT NULL,
  `nbi` varchar(255) DEFAULT NULL,
  `birth_cert` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transferee_files`
--

INSERT INTO `transferee_files` (`id`, `username`, `control_number`, `tor`, `dismissal`, `gmc`, `nbi`, `birth_cert`, `submitted_at`) VALUES
(1, 'vincent@gmall.com', 'CN-00002', 'vincent@gmall.com_tor_1749812983.jpg', 'vincent@gmall.com_dismissal_1749812983.jpg', 'vincent@gmall.com_gmc_1749812983.jpg', 'vincent@gmall.com_nbi_1749812983.jpg', 'vincent@gmall.com_birth_cert_1749812983.jpg', '2025-06-13 05:09:43'),
(2, 'jego@gnil.com', 'CN-00006', 'jego@gnil.com_tor_1749867508.png', 'jego@gnil.com_dismissal_1749867508.png', 'jego@gnil.com_gmc_1749867508.png', 'jego@gnil.com_nbi_1749867508.png', 'jego@gnil.com_birth_cert_1749867508.jpg', '2025-06-13 20:18:28'),
(3, 'jego@gnil.com', 'CN-00006', 'jego@gnil.com_tor_1749867945.jpg', 'jego@gnil.com_dismissal_1749867945.jpg', 'jego@gnil.com_gmc_1749867945.jpg', 'jego@gnil.com_nbi_1749867945.jpg', 'jego@gnil.com_birth_cert_1749867945.jpg', '2025-06-13 20:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_exams`
--

CREATE TABLE `uploaded_exams` (
  `upload_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `time_min` int(11) NOT NULL,
  `schedule` datetime NOT NULL,
  `meeting_link` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploaded_exams`
--

INSERT INTO `uploaded_exams` (`upload_id`, `exam_id`, `category`, `time_min`, `schedule`, `meeting_link`, `uploaded_at`) VALUES
(21, 30, 'BSIT', 30, '2025-06-15 13:59:00', 'https://meet.google.com/rjg-sqtc-etp', '2025-06-12 13:26:17'),
(23, 31, 'BSCS', 20, '2025-06-13 20:07:00', 'https://meet.google.com/rjg-sqtc-etp', '2025-06-12 13:37:37'),
(25, 35, 'BSIT', 30, '2025-06-12 21:40:00', 'https://meet.google.com/mwe-unir-rkn', '2025-06-12 13:51:18'),
(26, 29, 'BSIT', 30, '2025-06-13 15:20:00', 'https://meet.google.com/rjg-sqtc-etp', '2025-06-13 07:18:52'),
(27, 33, 'BSIT', 30, '2025-06-14 10:24:00', 'https://meet.google.com/rjg-sqtc-etp', '2025-06-14 02:23:28');

-- --------------------------------------------------------

--
-- Table structure for table `user_chosen_schedule`
--

CREATE TABLE `user_chosen_schedule` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `chosen_schedule` datetime NOT NULL,
  `selected_at` datetime DEFAULT current_timestamp(),
  `button_activate` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_chosen_schedule`
--

INSERT INTO `user_chosen_schedule` (`id`, `email`, `chosen_schedule`, `selected_at`, `button_activate`) VALUES
(2, 'vincent@gmall.com', '2025-06-13 15:20:00', '2025-06-13 21:19:09', 1),
(3, 'islit@yeah.com', '2025-06-13 20:07:00', '2025-06-14 00:21:28', 1),
(4, 'neil@ymail.com', '2025-06-13 15:20:00', '2025-06-14 02:10:38', 1),
(5, 'burata@gnail.com', '2025-06-13 15:20:00', '2025-06-14 10:00:44', 1),
(7, 'jego@gnil.com', '2025-06-14 10:24:00', '2025-06-14 10:27:03', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admission_info`
--
ALTER TABLE `admission_info`
  ADD PRIMARY KEY (`ai_id`);

--
-- Indexes for table `application_status`
--
ALTER TABLE `application_status`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `check_status`
--
ALTER TABLE `check_status`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `confirmed_exams`
--
ALTER TABLE `confirmed_exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `education_bg`
--
ALTER TABLE `education_bg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`attempt_id`);

--
-- Indexes for table `exam_category`
--
ALTER TABLE `exam_category`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `family_bg`
--
ALTER TABLE `family_bg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `freshmen_files`
--
ALTER TABLE `freshmen_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `med_his_info`
--
ALTER TABLE `med_his_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_info`
--
ALTER TABLE `personal_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_exam` (`exam_id`);

--
-- Indexes for table `second_courser_files`
--
ALTER TABLE `second_courser_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transferee_files`
--
ALTER TABLE `transferee_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploaded_exams`
--
ALTER TABLE `uploaded_exams`
  ADD PRIMARY KEY (`upload_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `user_chosen_schedule`
--
ALTER TABLE `user_chosen_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `admission_info`
--
ALTER TABLE `admission_info`
  MODIFY `ai_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `confirmed_exams`
--
ALTER TABLE `confirmed_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `education_bg`
--
ALTER TABLE `education_bg`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `exam_answers`
--
ALTER TABLE `exam_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exam_category`
--
ALTER TABLE `exam_category`
  MODIFY `exam_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `family_bg`
--
ALTER TABLE `family_bg`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `freshmen_files`
--
ALTER TABLE `freshmen_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `med_his_info`
--
ALTER TABLE `med_his_info`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `personal_info`
--
ALTER TABLE `personal_info`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `second_courser_files`
--
ALTER TABLE `second_courser_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transferee_files`
--
ALTER TABLE `transferee_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `uploaded_exams`
--
ALTER TABLE `uploaded_exams`
  MODIFY `upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user_chosen_schedule`
--
ALTER TABLE `user_chosen_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_exam` FOREIGN KEY (`exam_id`) REFERENCES `exam_category` (`exam_id`) ON DELETE CASCADE;

--
-- Constraints for table `uploaded_exams`
--
ALTER TABLE `uploaded_exams`
  ADD CONSTRAINT `uploaded_exams_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_category` (`exam_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_chosen_schedule`
--
ALTER TABLE `user_chosen_schedule`
  ADD CONSTRAINT `user_chosen_schedule_ibfk_1` FOREIGN KEY (`email`) REFERENCES `accounts` (`email`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
