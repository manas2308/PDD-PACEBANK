-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2025 at 05:40 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banking`
--

-- --------------------------------------------------------

--
-- Table structure for table `acc_types`
--

CREATE TABLE `acc_types` (
  `acctype_id` int(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` longtext NOT NULL,
  `rate` varchar(200) NOT NULL,
  `code` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `acc_types`
--

INSERT INTO `acc_types` (`acctype_id`, `name`, `description`, `rate`, `code`) VALUES
(1, 'Savings', '<p>Savings accounts&nbsp;are typically the first official bank account anybody opens. Children may open an account with a parent to begin a pattern of saving. Teenagers open accounts to stash cash earned&nbsp;from a first job&nbsp;or household chores.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Savings accounts are an excellent place to park&nbsp;emergency cash. Opening a savings account also marks the beginning of your relationship with a financial institution. For example, when joining a credit union, your &ldquo;share&rdquo; or savings account establishes your membership.</p>\r\n', '8', '9417'),
(2, ' pension type', '<p>RRetirement accounts&nbsp;offer&nbsp;tax advantages. In very general terms, you get to&nbsp;avoid paying income tax on interest&nbsp;you earn from a savings account or CD each year. But you may have to pay taxes on those earnings at a later date. Still, keeping your money sheltered from taxes may help you over the long term. Most banks offer IRAs (both&nbsp;Traditional IRAs&nbsp;and&nbsp;Roth IRAs), and they may also provide&nbsp;retirement accounts for small businesses.</p>\r\n\r\n<p>&nbsp;</p>\r\n', '8.2', '1'),
(4, 'Recurring deposit', '<p>Recurring deposit account or RD account is opened by those who want to save certain amount of money regularly for a certain period of time and earn a higher interest rate.&nbsp;In RD&nbsp;account a&nbsp;fixed amount is deposited&nbsp;every month for a specified period and the total amount is repaid with interest at the end of the particular fixed period.&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>The period of deposit is minimum six months and maximum ten years.&nbsp;The interest rates vary&nbsp;for different plans based on the amount one saves and the period of time and also on banks. No withdrawals are allowed from the RD account. However, the bank may allow to close the account before the maturity period.</p>\r\n\r\n<p>These accounts can be opened in single or joint names. Banks are also providing the Nomination facility to the RD account holders.&nbsp;</p>\r\n', '15', '1'),
(5, 'Fixed Deposit Account', '<p>In Fixed Deposit Account (also known as FD Account), a particular sum of money is deposited in a bank for specific&nbsp;period of time. It&rsquo;s one time deposit and one time take away (withdraw) account.&nbsp;The money deposited in this account can not be withdrawn before the expiry of period.&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>However, in case of need,&nbsp; the depositor can ask for closing the fixed deposit prematurely by paying a penalty. The penalty amount varies with banks.</p>\r\n\r\n<p>A high interest rate is paid on fixed deposits. The rate of interest paid for fixed deposit vary according to amount, period and also from bank to bank.</p>\r\n', '8', '0'),
(7, 'Current account', 'Current account is mainly for business persons, firms, companies, public enterprises etc and are never used for the purpose of investment or savings.These deposits are the most liquid deposits and there are no limits for number of transactions or the amount of transactions in a day. While, there is no interest paid on amount held in the account, banks charges certain &nbsp;service charges, on such accounts. The current accounts do not have any fixed maturity as these are on continuous basis accounts.</p>', '20', 'ACC-CAT-C5555'),
(10, 'Organization Account ', '<p>this type of account is used for organization like colleges , schools and software companies and it companies and hospital management type , same as the cooperate type accounts&nbsp;</p>\r\n', '4', 'ACC-CAT-7R1GO4');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `number` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `profile_pic` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `number`, `password`, `profile_pic`) VALUES
(2, 'SADMIN', 'admins@gmail.com', 'Bank-ADM-02308', '12345', 'admin-icn.png');

-- --------------------------------------------------------

--
-- Table structure for table `bankaccounts`
--

CREATE TABLE `bankaccounts` (
  `account_id` int(20) NOT NULL,
  `acc_name` varchar(200) NOT NULL,
  `account_number` varchar(200) NOT NULL,
  `acc_type` varchar(200) NOT NULL,
  `acc_rates` varchar(200) NOT NULL,
  `status` varchar(200) NOT NULL,
  `amount` varchar(200) NOT NULL,
  `client_id` varchar(200) NOT NULL,
  `client_name` varchar(200) NOT NULL,
  `client_national_id` varchar(200) NOT NULL,
  `client_phoneno` varchar(200) NOT NULL,
  `client_number` varchar(200) NOT NULL,
  `client_email` varchar(200) NOT NULL,
  `client_adr` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `pin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bankaccounts`
--

INSERT INTO `bankaccounts` (`account_id`, `acc_name`, `account_number`, `acc_type`, `acc_rates`, `status`, `amount`, `client_id`, `client_name`, `client_national_id`, `client_phoneno`, `client_number`, `client_email`, `client_adr`, `created_at`, `pin`) VALUES
(13, 'Christine Moore', '42187390521', 'Savings', '20', 'Active', '0', '4', 'Christine Moore', '478545445812', '7785452210', 'Bank-CLIENT-9501', 'christine@mail.com', '445 Bleck Street', '2025-02-01 04:48:58.871184', 0),
(14, 'Harry M Den', '357146928', 'Savings', '20', 'Active', '0', '5', 'Harry Den', '100014001000', '7412560000', 'Bank-CLIENT-7014', 'harryden@mail.com', '114 Allace Avenue', '2025-02-01 04:39:14.318512', 0),
(15, 'Amanda Stiefel', '287359614', 'Savings ', '20', 'Active', '0', '8', 'Amanda Stiefel', '478000001', '7850000014', 'Bank-CLIENT-0423', 'amanda@mail.com', '92 Maple Street', '2025-01-26 09:27:54.000000', 0),
(16, 'Johnnie Reyes', '705239816', ' Retirement ', '10', 'Active', '0', '6', 'Johnnie J. Reyes', '147455554', '7412545454', 'Bank-CLIENT-1698', 'reyes@mail.com', '23 Hinkle Deegan Lake Road', '2025-01-26 09:45:11.000000', 0),
(17, 'Liam M. Moore', '719360482', 'Savings ', '20', 'Active', '0', '9', 'Liam Moore', '170014695', '7014569696', 'Bank-CLIENT-4716', 'liamoore@mail.com', '46 Timberbrook Lane', '2025-01-26 10:58:37.000000', 0),
(18, 'Johnny M. Doen', '724310586', 'Fixed Deposit Account ', '40', 'Active', '0', '3', 'John Doe', '36756481', '9897890089', 'Bank-CLIENT-8127', 'johndoe@gmail.com', '127007 Localhost', '2025-01-26 11:10:15.000000', 0),
(21, 'klaus', '1710868270', ' pension type', '8.2', 'Active', '', '3', 'John Doe', '36756481', '9897890088', '3', 'johndoe@gmail.com', '127007 Localhost', '2025-01-31 04:02:44.000000', 0),
(22, 'suresh', '6135052231', 'Recurring deposit', '15', 'Active', '', '40', 'suresh ', '45647809', '9826341672', '40', 'hiteshlevaku@gmail.com', 'asdagafh', '2025-02-06 05:57:11.000000', 0),
(23, 'Manas Reddy', '7886281982', 'Savings', '8', 'Active', '', '45', 'Manas Reddy', '647165629590', '9398355713', '45', 'client@gmail.com', 'wakkanda street, new jersey', '2025-02-17 14:06:33.657657', 230821),
(24, 'Manas ', '1355011490', 'Current account', '20', 'Active', '', '45', 'Manas Reddy', '647165629590', '9398355713', '45', 'client@gmail.com', 'wakkanda street, new jersey', '2025-02-19 09:49:06.245226', 123456),
(25, 'Manasa Reddy', '1798722830', 'Savings', '8', 'Active', '', '45', 'Manas Reddy', '647165629590', '9398355713', '45', 'client@gmail.com', 'wakkanda street, new jersey', '2025-02-19 10:25:22.469083', 111111),
(34, 'Hitesh', '9307637788', 'Savings', '8', 'Active', '', '50', 'Hitesh reddy', '8728 8900 7638 8777', '8712288169', '50', 'manasfrnd@gmail.com', 'wdkqwdnla', '2025-03-12 02:48:31.970747', 222222),
(35, 'SIRAJ', '6395248399', 'Savings', '8', 'Active', '', '52', 'SIRAJ', '9590 6577 7821', '9398355713', '52', 'manasfrnd@gmail.com', '#36/256-51, BHARATH NAGAR, OPP TO KESAVA REDDY SCHOOL, CHINNACHOW, KADAPA, AP', '2025-03-17 13:35:30.361106', 143341);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `national_id` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `name`, `national_id`, `phone`, `address`, `email`, `password`) VALUES
(3, 'John Doe', '36756481', '9897890088', '127007 Localhost', 'johndoe@gmail.com', 'a69681bcf334ae130217fea4505fd3c994f5683f'),
(4, 'Christine Moore', '478545445812', '7785452210', '445 Bleck Street', 'christine@mail.com', '55c3b5386c486feb662a0785f340938f518d547f'),
(5, 'Harry Den', '100014001000', '7412560000', '114 Allace Avenue', 'harryden@mail.com', '55c3b5386c486feb662a0785f340938f518d547f'),
(6, 'Johnnie J. Reyes', '147455554', '7412545454', '23 Hinkle Deegan Lake Road', 'reyes@mail.com', '55c3b5386c486feb662a0785f340938f518d547f'),
(8, 'Amanda Stiefel', '478000001', '7850000014', '92 Maple Street', 'amanda@mail.com', '55c3b5386c486feb662a0785f340938f518d547f'),
(9, 'Liam Moore', '170014695', '7014569696', '46 Timberbrook Lane', 'liamoore@mail.com', '55c3b5386c486feb662a0785f340938f518d547f'),
(45, 'Manas Reddy', '647165629590', '9398355713', 'wakkanda street, new jersey', 'client@gmail.com', '$2y$10$xkP498IZseRWAukUWn6CXOVc4xi6npwOwfnamKqGZw58ty83sSJZq'),
(49, 'raju', '454545', '987543210', 'tirupati', 'raju@gmail.com', '$2y$10$.yaq/iag5n0mijixVBOI0eC05frU6ClIt9WY9a37NRFCR2jYP9Wp6'),
(50, 'Hitesh reddy', '8728 8900 7638 8777', '8712288169', 'wdkqwdnla', 'manasfrnd@gmail.com', '$2y$10$7q8ug2PyzqzG.XRG941xWunTN1C7ACaisxgBT.J/aYQ7tYF4lQaVG'),
(51, 'Manas Reddy', '3432 3222 2121', '9398355713', '39/650/6-2,Beside gurukul vidyapeeth school', 'manasfrnd@gmail.com', 'Manas@2308'),
(52, 'SIRAJ', '9590 6577 7821', '9398355713', '#36/256-51, BHARATH NAGAR, OPP TO KESAVA REDDY SCHOOL, CHINNACHOW, KADAPA, AP', 'manasfrnd@gmail.com', '$2y$10$SIltqSC85JGp7NXGsU8q/O5n54R6NZzbvtxctNKh6U4LaIZe2GmcC');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(20) NOT NULL,
  `notification_details` text NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `notification_details`, `created_at`) VALUES
(20, 'Amanda Stiefel Has Deposited $ 2658 To Bank Account 287359614', '2025-01-26 10:47:22.000000'),
(21, 'Liam Moore Has Deposited $ 5650 To Bank Account 719360482', '2025-01-26 10:59:14.000000'),
(22, 'Liam Moore Has Withdrawn $ 777 From Bank Account 719360482', '2025-01-26 10:59:38.000000'),
(23, 'Liam Moore Has Transfered $ 1256 From Bank Account 719360482 To Bank Account 287359614', '2025-01-26 11:00:15.000000'),
(24, 'John Doe Has Deposited $ 8550 To Bank Account 724310586', '2025-01-26 11:10:49.000000'),
(25, 'Liam Moore Has Deposited $ 600 To Bank Account 719360482', '2023-02-16 11:10:57.000000'),
(26, 'Liam Moore Has Withdrawn $ 120 From Bank Account 719360482', '2023-02-16 11:11:14.000000'),
(27, 'John Doe Has Transfered $ 100 From Bank Account 724310586 To Bank Account 719360482', '2023-02-16 11:11:38.000000'),
(28, 'Harry Den Has Deposited $ 6800 To Bank Account 357146928', '2023-02-16 11:14:09.000000');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_temp`
--

CREATE TABLE `password_reset_temp` (
  `email` varchar(250) NOT NULL,
  `reset_key` varchar(250) NOT NULL,
  `expDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `password_reset_temp`
--

INSERT INTO `password_reset_temp` (`email`, `reset_key`, `expDate`) VALUES
('manasfrnd@gmail.com', '22efce4fc8dfc16faf73ceaefe489bad89b42d8fe0', '2025-02-22 20:48:23'),
('manasfrnd@gmail.com', '22efce4fc8dfc16faf73ceaefe489bad9f062b0336', '2025-02-22 21:07:20'),
('manasfrnd@gmail.com', '45851a481fada5b4ce3ff68738b4b688', '2025-02-22 21:38:46'),
('manasfrnd@gmail.com', '129050a3588623a653e4ca5d22f067c2', '2025-02-22 21:38:51'),
('manasfrnd@gmail.com', 'b125eaee7b5c3be46200a77b474d2572', '2025-02-22 21:38:52'),
('manasfrnd@gmail.com', '013e8101ece7269a41d295596e0e1183', '2025-02-22 21:38:54'),
('manasfrnd@gmail.com', '173c9c25a65fcb8a43014627d9626803', '2025-02-22 21:40:56'),
('manasfrnd@gmail.com', '8bc43df95c344ae04aed9505b4e86a53', '2025-02-22 21:40:58'),
('manasfrnd@gmail.com', 'aa30a45247e1df92cfa864e541c9c142', '2025-02-22 22:02:50'),
('manasfrnd@gmail.com', 'a8aa8793580ebfed4f45b632a07efffa', '2025-02-22 22:02:56'),
('manasfrnd@gmail.com', 'b96f15e061ef240fe400d1a6d761318a', '2025-02-22 22:03:41'),
('manasfrnd@gmail.com', '2395136f6de61a4645275d88a3d2faf8', '2025-02-22 22:12:11'),
('manasfrnd@gmail.com', '1f9cd8e1767e58f8bc747cdd2bab3c8e', '2025-02-22 22:12:37'),
('manasfrnd@gmail.com', '52a80c280b87c6d313595110fbbf9b01', '2025-02-22 22:17:10'),
('manasfrnd@gmail.com', 'f25b20180b1de639bbc9e08619bab148', '2025-02-22 22:24:48'),
('kalyani.mv004@gmail.com', 'ae61a96ffe51e9cb2571da6d10909d5b', '2025-02-23 08:24:27'),
('manasfrnd@gmail.com', 'dab812f0392d3d43fb8d2170003efda5', '2025-02-23 09:12:15'),
('manasfrnd@gmail.com', '4d40bbf398d7cd82195bdc4a14b9f10b', '2025-02-23 09:13:12'),
('manasfrnd@gmail.com', '5c0df0fe84a59269b58ef5e676023032', '2025-02-23 09:34:09'),
('manasfrnd@gmail.com', 'a5c2962025339464fb0e42e3312dd2d8', '2025-02-23 09:35:14'),
('manasfrnd@gmail.com', 'a479989482384098f2150d36b76169b2', '2025-02-23 09:38:19'),
('manasfrnd@gmail.com', 'c9ee04a47e997982e186141e6da203d4', '2025-03-15 20:50:28'),
('manasfrnd@gmail.com', '45bb194c97820f15fe1cbe06da575edd', '2025-03-15 20:56:38'),
('manasfrnd@gmail.com', 'b463bfeb1bd44c95b277f7bd7424dd90', '2025-03-15 21:00:05'),
('manasfrnd@gmail.com', '00e0b6cff9753b62c093de29ce4aada9', '2025-03-15 21:00:05'),
('manasfrnd@gmail.com', '39b9f1f183aab3b3d4df52a7c1353e2c', '2025-03-15 21:00:08');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `staff_number` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `sex` varchar(200) NOT NULL,
  `profile_pic` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `staff_number`, `phone`, `email`, `password`, `sex`, `profile_pic`) VALUES
(3, 'Staff ', 'Bank-STAFF-6785', '0704975742', 'staff@mail.com', 'staff123', 'Male', 'user-profile-min.png'),
(11, 'staff3', '3241', '9348090785', 'staff3@gmail.com', '12345', 'Other', 'uploads/ad.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `systemsettings`
--

CREATE TABLE `systemsettings` (
  `id` int(20) NOT NULL,
  `sys_name` longtext NOT NULL,
  `sys_tagline` longtext NOT NULL,
  `logo` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `systemsettings`
--

INSERT INTO `systemsettings` (`id`, `sys_name`, `sys_tagline`, `logo`) VALUES
(1, 'Online Banking', 'Manage your Transactions more efficiently.', 'bankinglg.png');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `tr_id` int(20) NOT NULL,
  `tr_code` varchar(200) NOT NULL,
  `account_id` varchar(200) NOT NULL,
  `acc_name` varchar(200) NOT NULL,
  `account_number` varchar(200) NOT NULL,
  `acc_type` varchar(200) NOT NULL,
  `acc_amount` varchar(200) NOT NULL,
  `tr_type` varchar(200) NOT NULL,
  `tr_status` varchar(200) NOT NULL,
  `client_id` varchar(200) NOT NULL,
  `client_name` varchar(200) NOT NULL,
  `client_national_id` varchar(200) NOT NULL,
  `transaction_amt` varchar(200) NOT NULL,
  `client_phone` varchar(200) NOT NULL,
  `receiving_acc_no` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `receiving_acc_name` varchar(200) NOT NULL,
  `receiving_acc_holder` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`tr_id`, `tr_code`, `account_id`, `acc_name`, `account_number`, `acc_type`, `acc_amount`, `tr_type`, `tr_status`, `client_id`, `client_name`, `client_national_id`, `transaction_amt`, `client_phone`, `receiving_acc_no`, `created_at`, `receiving_acc_name`, `receiving_acc_holder`) VALUES
(38, '2XsYuvHwMmlEfiTRgD97', '13', 'Christine Moore', '421873905', 'Current account ', '', 'Deposit', 'Success ', '4', 'Christine Moore', '478545445812', '2350', '7785452210', '', '2024-08-30 12:15:33.972970', '', ''),
(39, 'Q6zFbdlINi3Reyu8UPMD', '13', 'Christine Moore', '421873905', 'Current account ', '', 'Deposit', 'Success ', '4', 'Christine Moore', '478545445812', '660', '7785452210', '', '2024-08-30 12:16:45.034964', '', ''),
(40, 'pl1QXD8CgeKon6TRf3Fk', '13', 'Christine Moore', '421873905', 'Current account ', '', 'Withdrawal', 'Success ', '4', 'Christine Moore', '478545445812', '200', '7785452210', '', '2024-08-30 12:16:59.566360', '', ''),
(41, 'RGl1EohqrgS3K4MUAHaf', '14', 'Harry M Den', '357146928', 'Savings ', '', 'Deposit', 'Success ', '5', 'Harry Den', '100014001000', '2660', '7412560000', '', '2025-01-10 10:17:21.233304', '', ''),
(42, 'FfYSvxkq7T1iHs06p2Qa', '13', 'Christine Moore', '421873905', 'Current account ', '', 'Transfer', 'Success ', '4', 'Christine Moore', '478545445812', '665', '7785452210', '357146928', '2025-01-15 11:19:45.731760', 'Harry M Den', 'Harry Den'),
(43, 'wXOyVgizubsp6UnTNfL4', '15', 'Amanda Stiefel', '287359614', 'Savings ', '', 'Deposit', 'Success ', '8', 'Amanda Stiefel', '478000001', '2658', '7850000014', '', '2025-01-16 10:47:22.506549', '', ''),
(44, '1S6wRtU3zP0igpCYyTGF', '17', 'Liam M. Moore', '719360482', 'Savings ', '', 'Deposit', 'Success ', '9', 'Liam Moore', '170014695', '5650', '7014569696', '', '2025-01-16 10:59:14.851707', '', ''),
(45, 'GCNrZ7n3oJyM62SzpKWs', '17', 'Liam M. Moore', '719360482', 'Savings ', '', 'Withdrawal', 'Success ', '9', 'Liam Moore', '170014695', '777', '7014569696', '', '2025-01-16 10:59:38.175952', '', ''),
(47, 'm2OlYZgkQwTPp5VHS9WN', '18', 'Johnny M. Doen', '724310586', 'Fixed Deposit Account ', '', 'Deposit', 'Success ', '3', 'John Doe', '36756481', '8550', '9897890089', '', '2025-01-16 11:10:49.466257', '', ''),
(48, 'P5urU12mcnOBbG0NMVHX', '17', 'Liam M. Moore', '719360482', 'Savings ', '', 'Deposit', 'Success ', '9', 'Liam Moore', '170014695', '600', '7014569696', '', '2025-01-16 11:10:57.306089', '', ''),
(49, 'kQBMaoO42sAeqZtS9lFz', '17', 'Liam M. Moore', '719360482', 'Savings ', '', 'Withdrawal', 'Success ', '9', 'Liam Moore', '170014695', '120', '7014569696', '', '2025-01-16 11:11:14.817821', '', ''),
(50, '9jQsTd0YV6tfqCZzckGW', '18', 'Johnny M. Doen', '724310586', 'Fixed Deposit Account ', '', 'Transfer', 'Success ', '3', 'John Doe', '36756481', '100', '9897890089', '719360482', '2025-01-16 11:11:38.758246', 'Liam M. Moore', 'Liam Moore'),
(51, 'FMyw7YGtnpQPaZXTuWmR', '14', 'Harry M Den', '357146928', 'Savings ', '', 'Deposit', 'Success ', '5', 'Harry Den', '100014001000', '6800', '7412560000', '', '2025-01-16 11:14:09.179146', '', ''),
(52, '39ff6b20b8c58fe2', '13', 'Christine Moore', '42187390521', 'Savings', '0', 'Deposit', 'Completed', '4', 'Christine Moore', '478545445812', '120', '7785452210', '', '2025-02-01 07:43:38.000000', '', ''),
(53, '60a6fa4d4b7974e7', '13', 'Christine Moore', '42187390521', 'Savings', '0', 'Withdrawal', 'Completed', '4', 'Christine Moore', '478545445812', '100', '7785452210', '', '2025-02-01 08:43:09.000000', '', ''),
(54, '44dfe3ed5ba48bdf', '18', 'Johnny M. Doen', '724310586', 'Fixed Deposit Account ', '0', 'Withdrawal', 'Completed', '3', 'John Doe', '36756481', '100', '9897890089', '', '2025-02-01 08:43:29.000000', '', ''),
(55, 'd80b0f80d52a43ce', '13', 'Christine Moore', '42187390521', 'Savings', '0', 'Withdrawal', 'Completed', '4', 'Christine Moore', '478545445812', '11', '7785452210', '', '2025-02-01 08:44:11.000000', '', ''),
(57, '5b72e33216adf4ee', '13', 'Christine Moore', '42187390521', 'Savings', '0', 'Deposit', 'Completed', '4', 'Christine Moore', '478545445812', '1231', '7785452210', '', '2025-02-03 02:40:39.000000', '', ''),
(58, '0893063f641ec2e0', '13', 'Christine Moore', '42187390521', 'Savings', '0', 'Deposit', 'Completed', '4', 'Christine Moore', '478545445812', '5000', '7785452210', '', '2025-02-03 08:43:50.000000', '', ''),
(59, 'TR866646', '13', 'Christine Moore', '42187390521', 'Savings', '120', 'Transfer', 'Completed', '4', 'Christine Moore', '478545445812', '120', '7785452210', '42187390521', '2025-02-03 10:13:22.000000', 'Christine Moore', 'Christine Moore'),
(60, 'TR425833', '13', 'Christine Moore', '42187390521', 'Savings', '100', 'Transfer', 'Completed', '4', 'Christine Moore', '478545445812', '100', '7785452210', '719360482', '2025-02-03 10:22:22.000000', 'Liam M. Moore', 'Liam M. Moore'),
(61, 'TR364746', '13', 'Christine Moore', '42187390521', 'Savings', '100', 'Transfer', 'Completed', '4', 'Christine Moore', '478545445812', '100', '7785452210', '719360482', '2025-02-03 10:23:46.000000', 'Liam M. Moore', 'Liam M. Moore'),
(62, 'TR691246', '13', 'Christine Moore', '42187390521', 'Savings', '100', 'Transfer', 'Completed', '4', 'Christine Moore', '478545445812', '100', '7785452210', '724310586', '2025-02-03 10:23:56.000000', 'Johnny M. Doen', 'Johnny M. Doen'),
(63, 'TR179996', '13', 'Christine Moore', '42187390521', 'Savings', '100', 'Transfer', 'Completed', '4', 'Christine Moore', '478545445812', '100', '7785452210', '724310586', '2025-02-03 10:27:11.000000', 'Johnny M. Doen', 'Johnny M. Doen'),
(64, 'TR179996', '18', 'Johnny M. Doen', '724310586', 'Fixed Deposit Account ', '100', 'Credit', 'Completed', '3', 'John Doe', '36756481', '100', '9897890089', '42187390521', '2025-02-03 10:27:12.000000', 'Christine Moore', 'Christine Moore'),
(65, 'TR642529', '13', 'Christine Moore', '42187390521', 'Savings', '100', 'Transfer', 'Completed', '4', 'Christine Moore', '478545445812', '100', '7785452210', '287359614', '2025-02-03 10:27:23.000000', 'Amanda Stiefel', 'Amanda Stiefel'),
(66, 'TR642529', '15', 'Amanda Stiefel', '287359614', 'Savings ', '100', 'Credit', 'Completed', '8', 'Amanda Stiefel', '478000001', '100', '7850000014', '42187390521', '2025-02-03 10:27:23.000000', 'Christine Moore', 'Christine Moore'),
(67, 'TR101657', '13', 'Christine Moore', '42187390521', 'Savings', '122', 'Transfer', 'Completed', '4', 'Christine Moore', '478545445812', '122', '7785452210', '705239816', '2025-02-03 10:27:40.000000', 'Johnnie Reyes', 'Johnnie Reyes'),
(69, '0058197f0fe1457b', '21', 'klaus', '1710868270', ' pension type', '', 'Deposit', 'Completed', '3', 'John Doe', '36756481', '2000', '9897890088', '', '2025-02-03 10:28:31.000000', '', ''),
(72, 'c002f866ec32bf89', '13', 'Christine Moore', '42187390521', 'Savings', '0', 'Withdrawal', 'Completed', '4', 'Christine Moore', '478545445812', '200', '7785452210', '', '2025-02-08 02:53:46.000000', '', ''),
(73, 'f13e6393aa191c0d', '13', 'Christine Moore', '42187390521', 'Savings', '0', 'Withdrawal', 'Completed', '4', 'Christine Moore', '478545445812', '12', '7785452210', '', '2025-02-14 03:58:44.000000', '', ''),
(74, '5303e70f5b6312d8', '13', 'Christine Moore', '42187390521', '0', '0', 'Withdrawal', 'Completed', '4', 'Christine Moore', '478545445812', '10', '7785452210', '', '2025-02-14 04:07:38.000000', '', ''),
(75, '369d7bc8698f72f3', '13', 'Christine Moore', '42187390521', '0', '0', 'Withdrawal', 'Completed', '4', 'Christine Moore', '478545445812', '1', '7785452210', '', '2025-02-14 04:07:46.000000', '', ''),
(76, 'TR302886', '13', 'Christine Moore', '42187390521', 'Savings', '3', 'Transfer', 'Completed', '4', 'Christine Moore', '478545445812', '3', '7785452210', '287359614', '2025-02-14 04:21:43.000000', '0', 'Amanda Stiefel'),
(77, 'TR302886', '15', 'Amanda Stiefel', '287359614', 'Savings ', '3', 'Credit', 'Completed', '8', 'Amanda Stiefel', '478000001', '3', '7850000014', '42187390521', '2025-02-14 04:21:44.000000', '0', 'Christine Moore'),
(78, 'ad7f332ab4630c38', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '1000', '9398355713', '', '2025-02-17 13:47:40.000000', '', ''),
(79, '5788ef9ac76f607f', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '0', '9398355713', '', '2025-02-17 14:28:12.000000', '', ''),
(80, '87ef9d75407ecc08', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '200', '9398355713', '', '2025-02-17 14:28:54.000000', '', ''),
(81, '8f61c460f5970e50', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '', '2025-02-17 14:36:55.000000', '', ''),
(82, '7c0c4663f927da14', '13', 'Christine Moore', '42187390521', '0', '0', 'Withdrawal', 'Completed', '4', 'Christine Moore', '478545445812', '100', '7785452210', '', '2025-02-17 14:40:05.000000', '', ''),
(83, '834b5f4e6ab5077f', '15', 'Amanda Stiefel', '287359614', 'Savings ', '', 'Deposit', 'Completed', '8', 'Amanda Stiefel', '478000001', '50', '7850000014', '', '2025-02-17 14:43:39.000000', '', ''),
(84, '9f685e6023368c30', '13', 'Christine Moore', '42187390521', 'Savings', '0', 'Deposit', 'Completed', '4', 'Christine Moore', '478545445812', '200', '7785452210', '', '2025-02-17 14:44:16.000000', '', ''),
(85, '5a09d0fdaefd509f', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '123', '9398355713', '', '2025-02-18 02:45:57.000000', '', ''),
(86, '10a279da38b23581', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '', '2025-02-18 08:31:38.000000', '', ''),
(87, 'b04e6c2cc88d4ac6', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '', '2025-02-18 08:33:53.000000', '', ''),
(96, 'TR217517', '15', 'Amanda Stiefel', '287359614', 'Savings ', '120', 'Transfer', 'Completed', '8', 'Amanda Stiefel', '478000001', '120', '7850000014', '705239816', '2025-02-18 10:25:10.000000', '0', 'Johnnie Reyes'),
(97, 'TR217517', '16', 'Johnnie Reyes', '705239816', ' Retirement ', '120', 'Credit', 'Completed', '6', 'Johnnie J. Reyes', '147455554', '120', '7412545454', '287359614', '2025-02-18 10:25:10.000000', '0', 'Amanda Stiefel'),
(98, '0aa932da5d1b7760', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '120', '9398355713', '', '2025-02-19 03:44:50.000000', '', ''),
(99, '632cee2cc5540db4', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '111', '9398355713', '', '2025-02-19 07:05:20.000000', '', ''),
(100, '632cee2cc5540db4', '13', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '111', '9398355713', '', '2025-02-19 07:05:20.000000', '', ''),
(101, 'bd4202ecf49bed8a', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '22', '9398355713', '', '2025-02-19 07:35:43.000000', '', ''),
(102, 'bd4202ecf49bed8a', '13', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '22', '9398355713', '', '2025-02-19 07:35:43.000000', '', ''),
(103, '43afee83da156b15', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '22', '9398355713', '', '2025-02-19 07:49:22.000000', '', ''),
(104, '43afee83da156b15', '12334235344', 'Harry M Den', '12334235344', 'Savings', '', 'Deposit', 'Completed', '45', 'Harry M Den', '647165629590', '22', '9398355713', '', '2025-02-19 07:49:22.000000', '', ''),
(105, 'e57ae7bcfda0e8ba', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '', '2025-02-19 07:57:59.000000', '', ''),
(106, '0ce52637aa81f6b5', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '12', '9398355713', '', '2025-02-19 07:58:25.000000', '', ''),
(107, 'cf1419b8f21c5886', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '12', '9398355713', '', '2025-02-19 07:59:06.000000', '', ''),
(108, 'cf1419b8f21c5886', '2324522321', 'Harish', '2324522321', 'Savings', '', 'Deposit', 'Completed', '45', 'Harish', '647165629590', '12', '9398355713', '', '2025-02-19 07:59:06.000000', '', ''),
(109, '0d14a68b3315819b', '24', 'Manas ', '1355011490', 'Current account', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '1000', '9398355713', '', '2025-02-19 09:56:48.000000', '', ''),
(110, 'cfc9925c6acc7613', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '200', '9398355713', '', '2025-02-19 09:59:08.000000', '', ''),
(111, 'cfc9925c6acc7613', '630221154326414', 'Harini', '630221154326414', 'Savings', '', 'Deposit', 'Completed', '45', 'Harini', '647165629590', '200', '9398355713', '', '2025-02-19 09:59:08.000000', '', ''),
(112, 'e9767c055cdb5d6f', '24', 'Manas ', '1355011490', 'Current account', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '9', '9398355713', '', '2025-02-19 10:00:01.000000', '', ''),
(113, 'e9767c055cdb5d6f', '9429364576547', 'srujan', '9429364576547', 'Current account', '', 'Deposit', 'Completed', '45', 'srujan', '647165629590', '9', '9398355713', '', '2025-02-19 10:00:01.000000', '', ''),
(115, '782dd9becab54415', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '1222', '9398355713', '', '2025-02-20 05:12:24.000000', '', ''),
(116, 'ddb95c2db60ab0aa', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '120', '9398355713', '', '2025-02-20 05:13:07.000000', '', ''),
(117, '8b79fe8598109a49', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '123', '9398355713', '', '2025-02-20 05:13:43.000000', '', ''),
(118, '8b79fe8598109a49', '1222532571', 'Manas Reddy', '1222532571', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '123', '9398355713', '', '2025-02-20 05:13:44.000000', '', ''),
(119, 'b710fce12c1b654a', '27', 'Manas ', '4005701037', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '100000', '9398355713', '', '2025-02-22 09:21:41.000000', '', ''),
(120, '993a928c61cd702c', '27', 'Manas ', '4005701037', 'Savings', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '20000', '9398355713', '', '2025-02-22 09:22:50.000000', '', ''),
(122, 'ea1dae1a14bf9fc7', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '111', '9398355713', '', '2025-02-25 02:55:56.000000', '', ''),
(123, 'a920d8ef3ac50654', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '', '2025-02-25 16:22:24.000000', '', ''),
(124, 'a920d8ef3ac50654', '22234231324', 'John doe', '22234231324', 'Savings', '', 'Deposit', 'Completed', '45', 'John Doe', '647165629590', '100', '9398355713', '', '2025-02-25 16:22:24.000000', '', ''),
(125, 'e254ed84587c560d', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '11', '9398355713', '121232442424', '2025-02-25 16:31:43.000000', 'John doe', 'John Doe'),
(127, 'd64deabc0a346cd2', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '10', '9398355713', '3433266788', '2025-02-25 16:32:35.000000', 'Amanda Stiefel', 'Amanda Stiefel'),
(130, '8d327ab6ce8a4153', '24', 'Manas ', '1355011490', 'Current account', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '123', '9398355713', '4564756757', '2025-02-25 16:35:03.000000', 'Manas', 'Manas Reddy'),
(131, '8d327ab6ce8a4153', '4564756757', 'Manas', '4564756757', 'Current account', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '123', '9398355713', '4564756757', '2025-02-25 16:35:04.000000', 'Manas', 'Manas Reddy'),
(134, 'a44ea0f7444a484b', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '', '2025-02-25 16:54:10.000000', '', ''),
(135, 'a44ea0f7444a484b', '1234331212', 'Christine Moore', '1234331212', 'Savings', '', 'Deposit', 'Completed', '45', 'Christine Moore', '647165629590', '100', '9398355713', '', '2025-02-25 16:54:10.000000', '', ''),
(136, '898ea07cb6eac7f2', '24', 'Manas ', '1355011490', 'Current account', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '12', '9398355713', '12133233', '2025-02-25 16:56:27.000000', 'Manas', 'Manas Reddy'),
(138, 'b2bb6396d731a786', '24', 'Manas ', '1355011490', 'Current account', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '200', '9398355713', '', '2025-02-27 06:04:49.000000', '', ''),
(139, '0905bff6a63fc93a', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '123', '9398355713', '', '2025-02-28 16:51:16.000000', '', ''),
(140, '327f4795d35da676', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '0', '9398355713', '', '2025-03-01 04:33:34.000000', '', ''),
(141, 'bf498974a99b7653', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Deposit', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '', '2025-03-01 04:48:18.000000', '', ''),
(143, '1309c1a121866edf', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '122', '9398355713', '', '2025-03-01 05:17:47.000000', '', ''),
(144, '4c298b5e40720f07', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '89389147421', '2025-03-01 07:09:48.000000', 'Harry M Den', 'Harry M Den'),
(145, '4c298b5e40720f07', '89389147421', 'Harry M Den', '89389147421', 'Savings', '', 'Deposit', 'Completed', '45', 'Harry M Den', '647165629590', '100', '9398355713', '89389147421', '2025-03-01 07:09:48.000000', 'Harry M Den', 'Harry M Den'),
(146, '58f0063c988afa6b', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '122', '9398355713', '212431231321', '2025-03-01 07:10:37.000000', 'ddww', 'ddww'),
(147, '58f0063c988afa6b', '212431231321', 'ddww', '212431231321', 'Savings', '', 'Deposit', 'Completed', '45', 'ddww', '647165629590', '122', '9398355713', '212431231321', '2025-03-01 07:10:38.000000', 'ddww', 'ddww'),
(148, 'ad601d02de2d5225', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '12', '9398355713', '1212121212', '2025-03-01 07:11:58.000000', 'Manas', 'Amanda Stiefel'),
(149, 'ad601d02de2d5225', '1212121212', 'Manas', '1212121212', 'Savings', '', 'Transfer', 'Completed', '45', 'Amanda Stiefel', '647165629590', '12', '9398355713', '1212121212', '2025-03-01 07:11:59.000000', 'Manas', 'Amanda Stiefel'),
(150, 'db222d937a5a7e9a', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '5741270319', '2025-03-12 01:34:33.000000', 'Hitesh', 'Hitesh Reddy'),
(151, 'db222d937a5a7e9a', '5741270319', 'Hitesh', '5741270319', 'Savings', '', 'Transfer', 'Completed', '45', 'Hitesh Reddy', '647165629590', '100', '9398355713', '5741270319', '2025-03-12 01:34:33.000000', 'Hitesh', 'Hitesh Reddy'),
(152, 'a5231b049cba6805', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '20', '9398355713', '9307637788', '2025-03-12 01:39:27.000000', 'Hitesh', 'Hitesh Reddy'),
(153, 'a5231b049cba6805', '9307637788', 'Hitesh', '9307637788', 'Savings', '', 'Transfer', 'Completed', '45', 'Hitesh Reddy', '647165629590', '20', '9398355713', '9307637788', '2025-03-12 01:39:28.000000', 'Hitesh', 'Hitesh Reddy'),
(154, '46a13e2fa07e5efb', '24', 'Manas ', '1355011490', 'Current account', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '200', '9398355713', '9307637788', '2025-03-12 01:42:44.000000', 'Hitesh', 'Hitesh Reddy'),
(155, '46a13e2fa07e5efb', '9307637788', 'Hitesh', '9307637788', 'Current account', '', 'Transfer', 'Completed', '45', 'Hitesh Reddy', '647165629590', '200', '9398355713', '9307637788', '2025-03-12 01:42:45.000000', 'Hitesh', 'Hitesh Reddy'),
(156, 'b37985263718996e', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '9307637788', '2025-03-12 02:11:12.000000', 'Hitesh', 'Hitesh Reddy'),
(157, 'b37985263718996e', '9307637788', 'Hitesh', '9307637788', 'Savings', '', 'Transfer', 'Completed', '45', 'Hitesh Reddy', '647165629590', '100', '9398355713', '9307637788', '2025-03-12 02:11:13.000000', 'Hitesh', 'Hitesh Reddy'),
(161, 'd168d1809a045f88', '9307637788', 'Hitesh', '9307637788', 'Current account', '', 'Transfer', 'Completed', '45', 'Hitesh Reddy', '647165629590', '30', '9398355713', '9307637788', '2025-03-12 02:30:47.000000', 'Hitesh', 'Hitesh Reddy'),
(164, '626ac7ca72257a3c', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '11', '9398355713', '9307637788', '2025-03-12 02:38:46.000000', 'Hitesh', 'Hitesh Reddy'),
(165, '626ac7ca72257a3c', '9307637788', 'Hitesh', '9307637788', 'Savings', '', 'Transfer', 'Completed', '45', 'Hitesh Reddy', '647165629590', '11', '9398355713', '9307637788', '2025-03-12 02:38:47.000000', 'Hitesh', 'Hitesh Reddy'),
(166, '6fef4008d15cf44f', '34', 'Hitesh', '9307637788', 'Savings', '', 'Deposit', 'Completed', '50', 'Hitesh reddy', '8728 8900 7638 8777', '1000', '8712288169', '', '2025-03-12 02:51:54.000000', '', ''),
(167, '4b4a3d0ef5aadaa2', '24', 'Manas ', '1355011490', 'Current account', '', 'Withdrawal', 'Completed', '45', 'Manas Reddy', '647165629590', '200', '9398355713', '', '2025-03-14 04:51:50.000000', '', ''),
(168, '3730f22d274f17d8', '23', 'Manas Reddy', '7886281982', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '45335645455', '2025-03-14 04:52:48.000000', 'Harry M Den', 'Manas Reddy'),
(169, '3730f22d274f17d8', '45335645455', 'Harry M Den', '45335645455', 'Savings', '', 'Transfer', 'Completed', '45', 'Manas Reddy', '647165629590', '100', '9398355713', '45335645455', '2025-03-14 04:52:49.000000', 'Harry M Den', 'Manas Reddy'),
(170, 'a7548bc3cc5427e1', '35', 'SIRAJ', '6395248399', 'Savings', '', 'Deposit', 'Completed', '52', 'SIRAJ', '9590 6577 7821', '100000', '9398355713', '', '2025-03-17 13:31:08.000000', '', ''),
(171, 'd38273e64905bd67', '35', 'SIRAJ', '6395248399', 'Savings', '', 'Transfer', 'Completed', '52', 'SIRAJ', '9590 6577 7821', '10000', '9398355713', '7886281982', '2025-03-17 13:38:17.000000', 'Manas', 'Manas'),
(172, 'd38273e64905bd67', '7886281982', 'Manas', '7886281982', 'Savings', '', 'Transfer', 'Completed', '52', 'Manas', '9590 6577 7821', '10000', '9398355713', '7886281982', '2025-03-17 13:38:18.000000', 'Manas', 'Manas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acc_types`
--
ALTER TABLE `acc_types`
  ADD PRIMARY KEY (`acctype_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bankaccounts`
--
ALTER TABLE `bankaccounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `systemsettings`
--
ALTER TABLE `systemsettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`tr_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acc_types`
--
ALTER TABLE `acc_types`
  MODIFY `acctype_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bankaccounts`
--
ALTER TABLE `bankaccounts`
  MODIFY `account_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `systemsettings`
--
ALTER TABLE `systemsettings`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `tr_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
