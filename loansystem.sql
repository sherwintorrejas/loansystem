-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2024 at 04:25 AM
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
-- Database: `loansystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminid`, `username`, `password`) VALUES
(1, 'testadmin', 'testadmin');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `BillingID` int(11) NOT NULL,
  `DateGenerated` date NOT NULL,
  `BorrowerID` int(11) NOT NULL,
  `AccountType` varchar(50) NOT NULL,
  `LoanedAmount` decimal(10,2) NOT NULL,
  `ReceivedAmount` decimal(10,2) DEFAULT NULL,
  `AmountToPay` decimal(10,2) NOT NULL,
  `Interest` decimal(5,2) NOT NULL,
  `Penalty` decimal(5,2) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `BillingStatus` enum('Completed','Overdue') NOT NULL,
  `DueDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `loanid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `loan_amount` decimal(10,2) NOT NULL,
  `payable_months` int(11) NOT NULL,
  `status` enum('Pending','Rejected','Approved') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_transactions`
--

CREATE TABLE `loan_transactions` (
  `lntranid` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `transaction_type` enum('Increase') NOT NULL,
  `amount_increase` decimal(10,2) NOT NULL,
  `payable_months_increase` int(11) NOT NULL,
  `admin_remarks` varchar(255) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `savingdatabase`
--

CREATE TABLE `savingdatabase` (
  `savings_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `savings_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `last_activity_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `savingdatabase`
--

INSERT INTO `savingdatabase` (`savings_id`, `user_id`, `savings_amount`, `last_activity_date`) VALUES
(338085, 2, 826.00, '2024-05-11');

-- --------------------------------------------------------

--
-- Table structure for table `savingstransaction`
--

CREATE TABLE `savingstransaction` (
  `transaction_id` varchar(10) NOT NULL,
  `savings_id` int(11) NOT NULL,
  `transaction_type` enum('Deposit','Withdrawal') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `last_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Failed','Rejected','Completed') NOT NULL,
  `date_time` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `savingstransaction`
--

INSERT INTO `savingstransaction` (`transaction_id`, `savings_id`, `transaction_type`, `amount`, `last_amount`, `status`, `date_time`) VALUES
('663ed20290', 338085, 'Deposit', 106.00, 212.00, 'Completed', '2024-05-11'),
('663ed363bd', 338085, 'Deposit', 100.00, 306.00, 'Completed', '2024-05-11'),
('663ed39e8c', 338085, 'Deposit', 100.00, 406.00, 'Completed', '2024-05-11'),
('663ed3f837', 338085, 'Deposit', 100.00, 406.00, 'Completed', '2024-05-11'),
('663ed40c71', 338085, 'Deposit', 104.00, 510.00, 'Completed', '2024-05-11'),
('663ed44e8d', 338085, 'Deposit', 105.00, 615.00, 'Completed', '2024-05-11'),
('663ed453d8', 338085, 'Deposit', 105.00, 720.00, 'Completed', '2024-05-11'),
('663ee1b0ca', 338085, 'Deposit', 106.00, 826.00, 'Completed', '2024-05-11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `account_type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `birthday` date NOT NULL,
  `age` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_account_number` varchar(50) NOT NULL,
  `card_holder_name` varchar(100) NOT NULL,
  `tin_number` varchar(50) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_address` varchar(255) NOT NULL,
  `company_phone_number` varchar(20) NOT NULL,
  `position` varchar(100) NOT NULL,
  `monthly_earnings` decimal(10,2) NOT NULL,
  `proof_of_billing` varchar(255) NOT NULL,
  `valid_id_primary` varchar(255) NOT NULL,
  `coe` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','active','disabled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `account_type`, `name`, `address`, `gender`, `birthday`, `age`, `email`, `contact_number`, `bank_name`, `bank_account_number`, `card_holder_name`, `tin_number`, `company_name`, `company_address`, `company_phone_number`, `position`, `monthly_earnings`, `proof_of_billing`, `valid_id_primary`, `coe`, `username`, `password`, `status`) VALUES
(2, 'Premium', 'shen', 'minglanilla', 'Male', '2000-02-23', 24, 'Omambacjunmark@gmail.com', '2654897852', 'gcash', '464151515135', 'admin', '1254984', 'none', 'none', '181961', 'dwawd', 18199.00, '../view/pof/USE CASE 4.png', '../view/vid/USE CASE 2.png', '../view/coe/Use case diagram.png', 'admin', '$2y$10$GID0ts93XvtrkLOzuVdvIODvNZo5dFJmLGBZZ2SKM569fVP6q/.HW', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminid`),
  ADD UNIQUE KEY `username_unique` (`username`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`BillingID`),
  ADD KEY `BorrowerID` (`BorrowerID`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loanid`),
  ADD KEY `fk_loan_user_id` (`user_id`);

--
-- Indexes for table `loan_transactions`
--
ALTER TABLE `loan_transactions`
  ADD PRIMARY KEY (`lntranid`),
  ADD KEY `fk_loan_id` (`loan_id`);

--
-- Indexes for table `savingdatabase`
--
ALTER TABLE `savingdatabase`
  ADD PRIMARY KEY (`savings_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `savingstransaction`
--
ALTER TABLE `savingstransaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `savings_id` (`savings_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `BillingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loanid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_transactions`
--
ALTER TABLE `loan_transactions`
  MODIFY `lntranid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `savingdatabase`
--
ALTER TABLE `savingdatabase`
  MODIFY `savings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=338086;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`BorrowerID`) REFERENCES `users` (`id`);

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loan_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `loan_transactions`
--
ALTER TABLE `loan_transactions`
  ADD CONSTRAINT `fk_loan_id` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loanid`);

--
-- Constraints for table `savingdatabase`
--
ALTER TABLE `savingdatabase`
  ADD CONSTRAINT `savingdatabase_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `savingstransaction`
--
ALTER TABLE `savingstransaction`
  ADD CONSTRAINT `savingstransaction_ibfk_1` FOREIGN KEY (`savings_id`) REFERENCES `savingdatabase` (`savings_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
