-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2023 at 04:13 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bumsys_demo`
--

DELIMITER $$
--
-- Procedures
--
CREATE PROCEDURE `filldates` (`dateStart` DATE, `dateEnd` DATE)   BEGIN
  WHILE dateStart <= dateEnd DO
    insert into tablename (_date) VALUES (dateStart);
    SET dateStart = date_add(dateStart, INTERVAL 1 DAY);
  END WHILE;
END$$

CREATE PROCEDURE `fill_date_dimension` (IN `startdate` DATE, IN `stopdate` DATE)   BEGIN
    DECLARE currentdate DATE;
    SET currentdate = startdate;
    WHILE currentdate <= stopdate DO
        insert into time_dimension VALUES (
                        currentdate
                        );
        SET currentdate = ADDDATE(currentdate,INTERVAL 1 DAY);
    END WHILE;
END$$

--
-- Functions
--
CREATE FUNCTION `combine_description` (`texts1` TEXT CHARSET utf8, `texts2` TEXT CHARSET utf8) RETURNS TEXT CHARSET utf8 COLLATE utf8_general_ci DETERMINISTIC return concat(if(texts1 is null, '', texts1), if(texts2 is null or texts2 = '', '', concat(' (', texts2, ')')))$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_base_stock`
--

CREATE TABLE `product_base_stock` (
  `pbs_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `vp_id` int(10) UNSIGNED NOT NULL COMMENT 'Variable/Unit Product Id, One product may have multiple unit',
  `warehouse` int(10) UNSIGNED NOT NULL,
  `base_stock_in` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `base_qty` decimal(20,4) NOT NULL DEFAULT 1.0000,
  `batch_id` int(10) UNSIGNED DEFAULT NULL,
  `batch_expiry_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_base_stock`
--

INSERT INTO `product_base_stock` (`pbs_id`, `product_id`, `vp_id`, `warehouse`, `base_stock_in`, `base_qty`, `batch_id`, `batch_expiry_date`) VALUES
(1, 4, 4, 1, '499.0000', '1.0000', NULL, NULL),
(2, 6, 6, 1, '500.0000', '1.0000', NULL, NULL),
(3, 3, 3, 1, '500.0000', '1.0000', NULL, NULL),
(4, 5, 5, 1, '500.0000', '1.0000', NULL, NULL),
(5, 1, 1, 1, '499.0000', '1.0000', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ro_accounts`
--

CREATE TABLE `ro_accounts` (
  `accounts_id` int(10) UNSIGNED NOT NULL,
  `accounts_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL CHECK (`accounts_name` <> ''),
  `accounts_type` varchar(50) DEFAULT NULL,
  `accounts_currency` char(3) DEFAULT NULL,
  `accounts_opening_balance` decimal(60,11) NOT NULL DEFAULT 0.00000000000,
  `accounts_balance` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `accounts_bank_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `accounts_bank_acc_number` bigint(20) DEFAULT NULL,
  `accounts_bank_acc_details` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `accounts_status` varchar(50) DEFAULT NULL,
  `accounts_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `accounts_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `accounts_add_by` int(10) UNSIGNED DEFAULT NULL,
  `accounts_update_by` int(10) UNSIGNED DEFAULT NULL,
  `negative_value_is_allow` tinyint(1) NOT NULL DEFAULT 0,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_accounts`
--

INSERT INTO `ro_accounts` (`accounts_id`, `accounts_name`, `accounts_type`, `accounts_currency`, `accounts_opening_balance`, `accounts_balance`, `accounts_bank_name`, `accounts_bank_acc_number`, `accounts_bank_acc_details`, `accounts_status`, `accounts_add_on`, `accounts_update_on`, `accounts_add_by`, `accounts_update_by`, `negative_value_is_allow`, `is_trash`) VALUES
(1, 'Demo Accounts', 'Local (Cash)', 'BDT', '50000.00000000000', '50022.00000000000', '', NULL, '', 'Active', '2023-04-25 13:55:31', '2023-04-25 14:03:39', 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_advance_payments`
--

CREATE TABLE `ro_advance_payments` (
  `advance_payment_id` int(10) UNSIGNED NOT NULL,
  `advance_payment_date` date NOT NULL,
  `advance_payment_reference` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL CHECK (`advance_payment_reference` <> ''),
  `advance_payment_pay_to` int(10) UNSIGNED NOT NULL,
  `advance_payment_amount` decimal(40,11) NOT NULL DEFAULT 0.00000000000,
  `advance_payment_pay_from` int(10) UNSIGNED NOT NULL,
  `advance_payment_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `advance_payment_payment_method` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `advance_payment_pay_by` int(10) UNSIGNED DEFAULT NULL,
  `advance_payment_pay_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_api_secrets`
--

CREATE TABLE `ro_api_secrets` (
  `api_id` int(10) UNSIGNED NOT NULL,
  `api_name` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `api_status` enum('Active','Deactive') DEFAULT 'Deactive',
  `api_secret_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `api_source_address` varchar(55) NOT NULL,
  `api_shop_id` int(10) UNSIGNED DEFAULT NULL,
  `api_accounts_id` int(10) UNSIGNED DEFAULT NULL,
  `api_warehouse_id` int(10) UNSIGNED DEFAULT NULL,
  `api_expire` date DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_bg_product_items`
--

CREATE TABLE `ro_bg_product_items` (
  `bg_product_id` int(10) UNSIGNED NOT NULL,
  `bg_item_product_id` int(10) UNSIGNED NOT NULL,
  `bg_product_price` decimal(30,11) NOT NULL DEFAULT 0.00000000000,
  `bg_product_qnt` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `is_raw_materials` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_billers`
--

CREATE TABLE `ro_billers` (
  `biller_user_id` int(10) UNSIGNED NOT NULL,
  `biller_shop_id` int(10) UNSIGNED NOT NULL,
  `biller_accounts_id` int(10) UNSIGNED NOT NULL,
  `biller_warehouse_id` int(10) UNSIGNED DEFAULT NULL,
  `biller_max_discount` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '90%',
  `allow_changing_price` tinyint(1) NOT NULL DEFAULT 0,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_billers`
--

INSERT INTO `ro_billers` (`biller_user_id`, `biller_shop_id`, `biller_accounts_id`, `biller_warehouse_id`, `biller_max_discount`, `allow_changing_price`, `is_trash`) VALUES
(1, 1, 1, 1, '50%', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_bills`
--

CREATE TABLE `ro_bills` (
  `bills_id` int(10) UNSIGNED NOT NULL,
  `bills_reference` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `bills_date` date NOT NULL,
  `bills_company_id` int(10) UNSIGNED NOT NULL,
  `bills_amount` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `bills_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `bills_attachment` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `bills_add_by` int(10) UNSIGNED DEFAULT NULL,
  `bills_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_bill_items`
--

CREATE TABLE `ro_bill_items` (
  `bill_items_id` int(10) UNSIGNED NOT NULL,
  `bill_items_bill_id` int(10) UNSIGNED NOT NULL,
  `bill_items_date` date DEFAULT NULL,
  `bill_items_company` int(10) UNSIGNED NOT NULL,
  `bill_items_category` int(10) UNSIGNED DEFAULT NULL,
  `bill_items_amount` decimal(50,11) NOT NULL DEFAULT 0.00000000000,
  `bill_items_note` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `bill_items_add_by` int(10) UNSIGNED DEFAULT NULL,
  `bill_items_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_calls`
--

CREATE TABLE `ro_calls` (
  `call_id` int(10) UNSIGNED NOT NULL,
  `call_type` enum('Direct Call','Voice Message') NOT NULL DEFAULT 'Direct Call',
  `call_datetime` datetime DEFAULT NULL,
  `call_direction` enum('Incoming','Outgoing') DEFAULT NULL,
  `call_status` enum('Answered','Missed','Rejected','Not Answered','Busy','Unreachable','Pending') DEFAULT NULL,
  `call_reason` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `client_identity` varchar(20) DEFAULT NULL,
  `duration` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `feedback` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `specimen_copy_received` enum('Yes','No','Partial','Not Sure') DEFAULT NULL,
  `feedback_informative` enum('Yes','No') DEFAULT NULL,
  `sale_our_product` enum('Yes','No','Sold Before') DEFAULT NULL,
  `use_our_product` enum('Yes','No','Used Before') DEFAULT NULL,
  `mr_feedback` enum('Positive','Negative','No Comment') DEFAULT NULL,
  `other_info` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `reviewer` int(10) UNSIGNED DEFAULT NULL,
  `representative` int(10) UNSIGNED DEFAULT NULL,
  `vm_id` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_capital`
--

CREATE TABLE `ro_capital` (
  `capital_id` int(10) UNSIGNED NOT NULL,
  `capital_received_date` date NOT NULL,
  `capital_accounts` int(10) UNSIGNED NOT NULL,
  `capital_amounts` decimal(50,11) NOT NULL DEFAULT 0.00000000000,
  `capital_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `capital_add_by` int(10) UNSIGNED DEFAULT NULL,
  `capital_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_cases`
--

CREATE TABLE `ro_cases` (
  `case_id` int(10) UNSIGNED NOT NULL,
  `case_datetime` datetime DEFAULT NULL,
  `case_title` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `case_priority` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `case_type` enum('Refund Request','Packaging Issues','Delivery Issue','Technical Issues','Query','Damaged Item','Exchange','Others') DEFAULT 'Query',
  `case_status` enum('Pending','Open','Replied','Customer Responded','Solved','Informed','On Hold','Closed') DEFAULT 'Pending',
  `case_site` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `case_customer` int(10) UNSIGNED DEFAULT NULL,
  `case_releted_service` bigint(20) UNSIGNED DEFAULT NULL,
  `case_person` int(10) UNSIGNED DEFAULT NULL,
  `case_assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `case_belongs_to` int(10) UNSIGNED DEFAULT NULL,
  `case_added_by_agent` int(10) UNSIGNED DEFAULT NULL,
  `case_is_pin` tinyint(1) NOT NULL DEFAULT 0,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_case_replies`
--

CREATE TABLE `ro_case_replies` (
  `reply_id` int(10) UNSIGNED NOT NULL,
  `reply_type` enum('Public','Private') DEFAULT NULL,
  `reply_case_id` int(10) UNSIGNED DEFAULT NULL,
  `reply_datetime` datetime DEFAULT NULL,
  `reply_details` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `reply_attachment` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `reply_by_agent` int(10) UNSIGNED DEFAULT NULL,
  `reply_by_customer` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_chat_users`
--

CREATE TABLE `ro_chat_users` (
  `chat_id` int(10) UNSIGNED NOT NULL,
  `chat_user_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_closings`
--

CREATE TABLE `ro_closings` (
  `closings_id` int(10) UNSIGNED NOT NULL,
  `closings_customer` int(10) UNSIGNED NOT NULL,
  `closings_title` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `closings_date` date NOT NULL,
  `closings_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `closings_add_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_companies`
--

CREATE TABLE `ro_companies` (
  `company_id` int(10) UNSIGNED NOT NULL,
  `company_name` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `company_opening_balance` decimal(26,11) NOT NULL DEFAULT 0.00000000000,
  `company_type` enum('Manufacturer','Supplier','Vendor','Assembler','Binders','Others') DEFAULT NULL,
  `company_contact_person` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `company_address` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `company_city` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `company_state` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `company_postal_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `company_country` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `company_phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `company_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `company_website` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `company_add_by` int(10) UNSIGNED DEFAULT NULL,
  `company_update_by` int(10) UNSIGNED DEFAULT NULL,
  `company_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_companies`
--

INSERT INTO `ro_companies` (`company_id`, `company_name`, `company_opening_balance`, `company_type`, `company_contact_person`, `company_address`, `company_city`, `company_state`, `company_postal_code`, `company_country`, `company_phone`, `company_email`, `company_website`, `company_add_by`, `company_update_by`, `company_add_on`, `company_update_on`, `is_trash`) VALUES
(1, 'Test Company', '0.00000000000', 'Manufacturer', '', '', '', '', '', '', 'xxxx', '', '', 1, NULL, '2023-04-25 14:01:30', '2023-04-25 14:01:30', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_currencies`
--

CREATE TABLE `ro_currencies` (
  `currency_code` char(3) NOT NULL,
  `currency_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `currency_rate` decimal(20,11) NOT NULL DEFAULT 0.00000000000,
  `currency_symbol` char(1) DEFAULT NULL,
  `currency_decimal_mark` char(1) DEFAULT NULL,
  `currency_thousand_seperator` char(1) DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_customers`
--

CREATE TABLE `ro_customers` (
  `customer_id` int(10) UNSIGNED NOT NULL,
  `customer_group_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `customer_name_in_local_len` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `customer_type` enum('Distributor','Wholesaler','Retailer','Consumer') DEFAULT 'Retailer',
  `customer_opening_balance` decimal(37,11) NOT NULL DEFAULT 0.00000000000,
  `customer_balance` decimal(40,11) NOT NULL DEFAULT 0.00000000000,
  `customer_due` decimal(40,11) NOT NULL DEFAULT 0.00000000000,
  `customer_shipping_rate` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `customer_discount` varchar(50) NOT NULL DEFAULT '0',
  `customer_contact_person` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `customer_address` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `customer_division` tinyint(1) UNSIGNED DEFAULT NULL,
  `customer_district` int(2) UNSIGNED DEFAULT NULL,
  `customer_upazila` int(4) UNSIGNED DEFAULT NULL,
  `customer_city` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `customer_postal_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `customer_country` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `customer_phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `customer_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `customer_website` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `customer_photo` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `send_notif` tinyint(1) NOT NULL DEFAULT 0,
  `customer_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_add_by` int(10) UNSIGNED DEFAULT NULL,
  `customer_update_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_customers`
--

INSERT INTO `ro_customers` (`customer_id`, `customer_group_id`, `customer_name`, `customer_name_in_local_len`, `customer_type`, `customer_opening_balance`, `customer_balance`, `customer_due`, `customer_shipping_rate`, `customer_discount`, `customer_contact_person`, `customer_address`, `customer_division`, `customer_district`, `customer_upazila`, `customer_city`, `customer_postal_code`, `customer_country`, `customer_phone`, `customer_email`, `customer_website`, `customer_photo`, `send_notif`, `customer_add_on`, `customer_update_on`, `customer_add_by`, `customer_update_by`, `is_trash`) VALUES
(1, NULL, 'Walk-in Customer', NULL, 'Retailer', '0.00000000000', '0.00000000000', '0.00000000000', '0.0000', '0', 'Walk-in Customer', 'Customer Address', NULL, NULL, NULL, 'NA', '', '', 'xxxxxxxxx', 'customer@tld.com', '', '', 0, '2019-12-23 07:58:12', '2023-04-25 13:50:25', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_customers_group`
--

CREATE TABLE `ro_customers_group` (
  `c_group_id` int(10) UNSIGNED NOT NULL,
  `c_group_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `c_group_discount` varchar(50) NOT NULL DEFAULT '0',
  `c_group_fixed_shipping` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `c_group_shipping_rate` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `c_group_sms_notification` tinyint(1) NOT NULL DEFAULT 0,
  `c_group_email_notification` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_deleted_data`
--

CREATE TABLE `ro_deleted_data` (
  `deleted_id` bigint(20) UNSIGNED NOT NULL,
  `deleted_from` varchar(100) DEFAULT NULL,
  `deleted_data` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_districts`
--

CREATE TABLE `ro_districts` (
  `district_id` int(2) UNSIGNED NOT NULL,
  `district_division_id` tinyint(1) UNSIGNED DEFAULT NULL,
  `district_name` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `district_bn_name` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `district_lat` varchar(15) DEFAULT NULL,
  `district_lon` varchar(15) DEFAULT NULL,
  `district_url` varchar(50) DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_districts`
--

INSERT INTO `ro_districts` (`district_id`, `district_division_id`, `district_name`, `district_bn_name`, `district_lat`, `district_lon`, `district_url`, `is_trash`) VALUES
(1, 4, 'Barguna', NULL, NULL, NULL, NULL, 0),
(2, 4, 'Barisal', NULL, NULL, NULL, NULL, 0),
(3, 4, 'Bhola', NULL, NULL, NULL, NULL, 0),
(4, 4, 'Jhalakati', NULL, NULL, NULL, NULL, 0),
(5, 4, 'Patuakhali', NULL, NULL, NULL, NULL, 0),
(6, 4, 'Pirojpur', NULL, NULL, NULL, NULL, 0),
(7, 1, 'Bandarban', NULL, NULL, NULL, NULL, 0),
(8, 1, 'Chattogram', NULL, NULL, NULL, NULL, 0),
(9, 1, 'Cox\'s Bazar', NULL, NULL, NULL, NULL, 0),
(10, 1, 'Khagrachhari', NULL, NULL, NULL, NULL, 0),
(11, 1, 'Rangamati', NULL, NULL, NULL, NULL, 0),
(12, 1, 'Brahmanbaria', NULL, NULL, NULL, NULL, 0),
(13, 1, 'Chandpur', NULL, NULL, NULL, NULL, 0),
(14, 1, 'Comilla', NULL, NULL, NULL, NULL, 0),
(15, 1, 'Feni', NULL, NULL, NULL, NULL, 0),
(16, 1, 'Lakshmipur', NULL, NULL, NULL, NULL, 0),
(17, 1, 'Noakhali', NULL, NULL, NULL, NULL, 0),
(18, 6, 'Dhaka', NULL, NULL, NULL, NULL, 0),
(19, 6, 'Dhaka Mahanagari', NULL, NULL, NULL, NULL, 0),
(20, 6, 'Faridpur', NULL, NULL, NULL, NULL, 0),
(21, 6, 'Foreign', NULL, NULL, NULL, NULL, 0),
(22, 6, 'Gazipur', NULL, NULL, NULL, NULL, 0),
(23, 6, 'Gopalganj', NULL, NULL, NULL, NULL, 0),
(24, 8, 'Jamalpur', NULL, NULL, NULL, NULL, 0),
(25, 6, 'Kishoreganj', NULL, NULL, NULL, NULL, 0),
(26, 6, 'Madaripur', NULL, NULL, NULL, NULL, 0),
(27, 6, 'Manikganj', NULL, NULL, NULL, NULL, 0),
(28, 6, 'Munshiganj', NULL, NULL, NULL, NULL, 0),
(29, 8, 'Mymensingh', NULL, NULL, NULL, NULL, 0),
(30, 6, 'Narayanganj', NULL, NULL, NULL, NULL, 0),
(31, 6, 'Narsingdi', NULL, NULL, NULL, NULL, 0),
(32, 8, 'Netrokona', NULL, NULL, NULL, NULL, 0),
(33, 6, 'Rajbari', NULL, NULL, NULL, NULL, 0),
(34, 6, 'Shariatpur', NULL, NULL, NULL, NULL, 0),
(35, 8, 'Sherpur', NULL, NULL, NULL, NULL, 0),
(36, 6, 'Tangail', NULL, NULL, NULL, NULL, 0),
(37, 7, 'Dinajpur', NULL, NULL, NULL, NULL, 0),
(38, 7, 'Gaibandha', NULL, NULL, NULL, NULL, 0),
(39, 7, 'Kurigram', NULL, NULL, NULL, NULL, 0),
(40, 7, 'Lalmonirhat', NULL, NULL, NULL, NULL, 0),
(41, 7, 'Nilphamari', NULL, NULL, NULL, NULL, 0),
(42, 7, 'Panchagarh', NULL, NULL, NULL, NULL, 0),
(43, 7, 'Rangpur', NULL, NULL, NULL, NULL, 0),
(44, 7, 'Thakurgaon', NULL, NULL, NULL, NULL, 0),
(45, 3, 'Bagerhat', NULL, NULL, NULL, NULL, 0),
(46, 3, 'Chuadanga', NULL, NULL, NULL, NULL, 0),
(47, 3, 'Jessore', NULL, NULL, NULL, NULL, 0),
(48, 3, 'Jhenaidah', NULL, NULL, NULL, NULL, 0),
(49, 3, 'Khulna', NULL, NULL, NULL, NULL, 0),
(50, 3, 'Kushtia', NULL, NULL, NULL, NULL, 0),
(51, 3, 'Magura', NULL, NULL, NULL, NULL, 0),
(52, 3, 'Meherpur', NULL, NULL, NULL, NULL, 0),
(53, 3, 'Narail', NULL, NULL, NULL, NULL, 0),
(54, 3, 'Satkhira', NULL, NULL, NULL, NULL, 0),
(55, 2, 'Bogra', NULL, NULL, NULL, NULL, 0),
(56, 2, 'Joypurhat', NULL, NULL, NULL, NULL, 0),
(57, 2, 'Naogaon', NULL, NULL, NULL, NULL, 0),
(58, 2, 'Natore', NULL, NULL, NULL, NULL, 0),
(59, 2, 'Chapainawabganj', NULL, NULL, NULL, NULL, 0),
(60, 2, 'Pabna', NULL, NULL, NULL, NULL, 0),
(61, 2, 'Rajshahi', NULL, NULL, NULL, NULL, 0),
(62, 2, 'Sirajganj', NULL, NULL, NULL, NULL, 0),
(63, 5, 'Habiganj', NULL, NULL, NULL, NULL, 0),
(64, 5, 'Moulvibazar', NULL, NULL, NULL, NULL, 0),
(65, 5, 'Sunamganj', NULL, NULL, NULL, NULL, 0),
(66, 5, 'Sylhet', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_divisions`
--

CREATE TABLE `ro_divisions` (
  `division_id` tinyint(1) UNSIGNED NOT NULL,
  `division_name` varchar(25) NOT NULL,
  `division_bn_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `division_url` varchar(50) DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_divisions`
--

INSERT INTO `ro_divisions` (`division_id`, `division_name`, `division_bn_name`, `division_url`, `is_trash`) VALUES
(1, 'Chattagram', 'চট্টগ্রাম', 'www.chittagongdiv.gov.bd', 0),
(2, 'Rajshahi', 'রাজশাহী', 'www.rajshahidiv.gov.bd', 0),
(3, 'Khulna', 'খুলনা', 'www.khulnadiv.gov.bd', 0),
(4, 'Barisal', 'বরিশাল', 'www.barisaldiv.gov.bd', 0),
(5, 'Sylhet', 'সিলেট', 'www.sylhetdiv.gov.bd', 0),
(6, 'Dhaka', 'ঢাকা', 'www.dhakadiv.gov.bd', 0),
(7, 'Rangpur', 'রংপুর', 'www.rangpurdiv.gov.bd', 0),
(8, 'Mymensingh', 'ময়মনসিংহ', 'www.mymensinghdiv.gov.bd', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_employees`
--

CREATE TABLE `ro_employees` (
  `emp_id` int(10) UNSIGNED NOT NULL,
  `emp_PIN` char(10) NOT NULL,
  `emp_department_id` int(10) UNSIGNED NOT NULL,
  `emp_email` varchar(100) DEFAULT NULL,
  `emp_firstname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `emp_lastname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `emp_positions` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_working_area` varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_fathers_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_mothers_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_nationality` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_gender` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_marital_status` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_religion` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_country` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_present_address` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_permanent_address` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_contact_number` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `emp_work_number` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_emergency_contact_number` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_date_of_birth` date DEFAULT NULL,
  `emp_blood_group` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_national_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_nature` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_photo` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `emp_salary` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `emp_opening_salary` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `emp_opening_overtime` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `emp_opening_bonus` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `emp_payable_salary` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `emp_payable_overtime` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `emp_payable_bonus` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `emp_advance_loan` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `emp_join_date` date DEFAULT NULL,
  `emp_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `emp_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `emp_add_by` int(10) UNSIGNED DEFAULT NULL,
  `emp_update_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_employees`
--

INSERT INTO `ro_employees` (`emp_id`, `emp_PIN`, `emp_department_id`, `emp_email`, `emp_firstname`, `emp_lastname`, `emp_positions`, `emp_working_area`, `emp_fathers_name`, `emp_mothers_name`, `emp_nationality`, `emp_gender`, `emp_marital_status`, `emp_religion`, `emp_country`, `emp_present_address`, `emp_permanent_address`, `emp_contact_number`, `emp_work_number`, `emp_emergency_contact_number`, `emp_date_of_birth`, `emp_blood_group`, `emp_national_id`, `emp_type`, `emp_nature`, `emp_photo`, `emp_salary`, `emp_opening_salary`, `emp_opening_overtime`, `emp_opening_bonus`, `emp_payable_salary`, `emp_payable_overtime`, `emp_payable_bonus`, `emp_advance_loan`, `emp_join_date`, `emp_add_on`, `emp_update_on`, `emp_add_by`, `emp_update_by`, `is_trash`) VALUES
(1, '214', 1, 'talk@bumsys.org', 'Bumsys', 'Demo', '', NULL, '', '', '', 'Male', 'Single', 'Islam', '', '', '', '', NULL, '', NULL, NULL, NULL, 'Permanent', 'Full-Time', '', '0.00000000000', '0.00000000000', '0.00000000000', '0.00000000000', '0.00000000000', '0.00000000000', '0.00000000000', '0.00000000000', NULL, '2023-04-25 13:50:25', '2023-04-25 13:50:25', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_emp_department`
--

CREATE TABLE `ro_emp_department` (
  `dep_id` int(10) UNSIGNED NOT NULL,
  `dep_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_emp_department`
--

INSERT INTO `ro_emp_department` (`dep_id`, `dep_name`, `is_trash`) VALUES
(1, 'Admin', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_firewall`
--

CREATE TABLE `ro_firewall` (
  `fw_id` int(10) UNSIGNED NOT NULL,
  `fw_status` enum('Active','Deactive') DEFAULT NULL,
  `fw_ip_address` varchar(16) DEFAULT NULL,
  `fw_action` enum('Blocked','Permitted') DEFAULT NULL,
  `fw_comment` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `fw_added_by` int(10) UNSIGNED DEFAULT NULL,
  `fw_added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `fw_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_incomes`
--

CREATE TABLE `ro_incomes` (
  `incomes_id` bigint(20) UNSIGNED NOT NULL,
  `incomes_date` date NOT NULL,
  `incomes_accounts_id` int(10) UNSIGNED NOT NULL,
  `incomes_from` int(10) UNSIGNED DEFAULT NULL,
  `incomes_amount` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `incomes_shop_id` int(10) UNSIGNED DEFAULT NULL,
  `incomes_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `incomes_add_by` int(10) UNSIGNED DEFAULT NULL,
  `incomes_update_by` int(10) UNSIGNED DEFAULT NULL,
  `incomes_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `incomes_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_institute`
--

CREATE TABLE `ro_institute` (
  `institute_id` int(10) UNSIGNED NOT NULL,
  `institute_name` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `institute_type` enum('School','College','University','Coaching','Library','Store') DEFAULT NULL,
  `institute_eiin` int(10) UNSIGNED DEFAULT NULL,
  `institute_upazila` int(4) UNSIGNED NOT NULL,
  `institute_website` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `institute_location` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `institute_map` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_journals`
--

CREATE TABLE `ro_journals` (
  `journals_id` int(10) UNSIGNED NOT NULL,
  `journals_date` date NOT NULL,
  `journals_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `journals_opening_balance` decimal(50,11) NOT NULL DEFAULT 0.00000000000,
  `journals_add_by` int(10) UNSIGNED DEFAULT NULL,
  `journals_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_journal_records`
--

CREATE TABLE `ro_journal_records` (
  `journal_records_id` int(10) UNSIGNED NOT NULL,
  `journal_records_datetime` datetime NOT NULL,
  `journal_records_reference` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `journal_records_journal_id` int(10) UNSIGNED NOT NULL,
  `journal_records_accounts` int(10) UNSIGNED NOT NULL,
  `journal_records_payments_type` enum('Outgoing','Incoming') DEFAULT NULL,
  `journal_records_payment_amount` decimal(40,11) NOT NULL DEFAULT 0.00000000000,
  `journal_records_narration` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `journal_records_add_by` int(10) UNSIGNED DEFAULT NULL,
  `journal_records_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_loan`
--

CREATE TABLE `ro_loan` (
  `loan_id` int(10) UNSIGNED NOT NULL,
  `loan_borrower` int(10) UNSIGNED NOT NULL,
  `loan_paying_from` int(10) UNSIGNED NOT NULL,
  `loan_amount` decimal(50,11) NOT NULL DEFAULT 0.00000000000,
  `loan_installment_interval` int(2) DEFAULT NULL,
  `loan_installment_starting_from` date NOT NULL,
  `loan_installment_amount` decimal(30,11) NOT NULL DEFAULT 0.00000000000,
  `loan_granter` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `loan_details` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `loan_attachment` longblob DEFAULT NULL,
  `loan_pay_by` int(10) UNSIGNED DEFAULT NULL,
  `loan_pay_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_loan_installment`
--

CREATE TABLE `ro_loan_installment` (
  `loan_installment_id` int(10) UNSIGNED NOT NULL,
  `loan_installment_date` date NOT NULL,
  `loan_ids` int(10) UNSIGNED NOT NULL,
  `loan_installment_provider` int(10) UNSIGNED NOT NULL,
  `loan_installment_receiving_accounts` int(10) UNSIGNED DEFAULT NULL,
  `loan_installment_paying_amount` decimal(30,11) NOT NULL DEFAULT 0.00000000000,
  `loan_installment_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `loan_installment_receive_by` int(10) UNSIGNED DEFAULT NULL,
  `loan_installment_paying_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_login_attempts`
--

CREATE TABLE `ro_login_attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `attempt_ipaddr` varchar(16) DEFAULT NULL,
  `attempt_user_id` int(10) UNSIGNED DEFAULT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_messages`
--

CREATE TABLE `ro_messages` (
  `msg_id` int(10) UNSIGNED NOT NULL,
  `msg_from_user` int(10) UNSIGNED DEFAULT NULL,
  `msg_to_user` int(10) UNSIGNED DEFAULT NULL,
  `msg_text` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `msg_datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_notes`
--

CREATE TABLE `ro_notes` (
  `note_id` int(10) UNSIGNED NOT NULL,
  `note_type` enum('feedback','note') DEFAULT 'note',
  `note_text` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `note_created_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_options`
--

CREATE TABLE `ro_options` (
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `option_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `option_value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `autoload` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_options`
--

INSERT INTO `ro_options` (`option_id`, `option_name`, `option_value`, `autoload`, `is_trash`) VALUES
(1, 'companyName', 'Your Company Name', NULL, 0),
(2, 'companyAddress', '32, Purana Paltan, Dhaka-100', NULL, 0),
(3, 'rootDomain', 'demo.bumsys.org', NULL, 0),
(4, 'autoLogoutTime', '300', NULL, 0),
(5, 'timeZone', 'Asia/Dhaka', NULL, 0),
(6, 'dateFormat', 'Y-m-d', NULL, 0),
(7, 'mysqlDateFormat', '%Y-%m-%d', NULL, 0),
(8, 'timeFormat', 'H:i', NULL, 0),
(9, 'mysqlTimeFormat', '%H:%i', NULL, 0),
(10, 'currencySymbol', '৳', NULL, 0),
(11, 'currencySymbolPosition', 'left', NULL, 0),
(12, 'thousandSeparator', ',', NULL, 0),
(13, 'decimalSeparator', '.', NULL, 0),
(14, 'decimalPlaces', '2', NULL, 0),
(15, 'allowToSaleStockOutProductInPOS', '', NULL, 0),
(16, 'allowToAddStockOutProductInPOS', '', NULL, 0),
(17, 'defaultSaleQnt', '1', NULL, 0),
(18, 'defaultProductCategory', '', NULL, 0),
(19, 'defaultProductBrand', '', NULL, 0),
(20, 'defaultProductEdition', '', NULL, 0),
(21, 'defaultProductGeneric', '', NULL, 0),
(22, 'defaultProductOrder', 'totalSoldQnt', NULL, 0),
(23, 'defaultProductOrderBy', 'DESC', NULL, 0),
(24, 'maxProductDisplay', '', NULL, 0),
(25, 'cronjobIsRunning', 'true', NULL, 0),
(26, 'enableProductVariations', '1', NULL, 0),
(27, 'defaultProductFilter', 'a:4:{i:0;s:8:\"category\";i:1;s:5:\"brand\";i:2;s:7:\"generic\";i:3;s:7:\"edition\";}', NULL, 0),
(28, 'productSettingsCanAddGeneric', '0', NULL, 0),
(29, 'productSettingsCanAddBrands', '0', NULL, 0),
(30, 'productSettingsCanAddBookInfo', '0', NULL, 0),
(31, 'posSaleAutoMarkAsPaid', '1', NULL, 0),
(32, 'posSaleAutoAdjustAmount', '1', NULL, 0),
(33, 'maxInvalidLoginAttemptToBlockUser', '-1', NULL, 0),
(34, 'maxInvalidLoginAttemptToBlockHost', '5', NULL, 0),
(35, 'canAccessOnlyPermittedIP', '0', NULL, 0),
(36, 'invoiceType', 'normal', NULL, 0),
(37, 'autoConfirmStockTransfer', 'Yes', NULL, 0),
(38, 'invoiceHeader', '', NULL, 0),
(39, 'invoiceFooter', '', NULL, 0),
(40, 'invoiceShowProductDiscount', '1', NULL, 0),
(41, 'invoiceWidth', '480', NULL, 0),
(42, 'invoiceShowCompanyName', '0', NULL, 0),
(43, 'invoiceShowCompanyAddress', '0', NULL, 0),
(44, 'invoiceShowShopLogo', '1', NULL, 0),
(45, 'activeModule', 'a:1:{i:0;s:40:&quot;module/customer-support/introductory.php&quot;;}', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_payments`
--

CREATE TABLE `ro_payments` (
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `payment_reference` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `payment_to_company` int(10) UNSIGNED DEFAULT NULL,
  `payment_to_employee` int(10) UNSIGNED DEFAULT NULL,
  `payment_purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_status` enum('Complete','Cancel','Return') DEFAULT NULL,
  `payment_type` enum('Bill','Due Bill','Salary','Advance Adjustment') DEFAULT NULL,
  `payment_amount` decimal(40,11) NOT NULL DEFAULT 0.00000000000,
  `payment_from` int(10) UNSIGNED DEFAULT NULL,
  `payment_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_method` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `payment_cheque_no` varchar(30) DEFAULT NULL,
  `payment_cheque_date` date DEFAULT NULL,
  `payment_attachement` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `payment_made_by` int(10) UNSIGNED DEFAULT NULL,
  `payment_update_by` int(10) UNSIGNED DEFAULT NULL,
  `payment_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_payments_categories`
--

CREATE TABLE `ro_payments_categories` (
  `payment_category_id` int(10) UNSIGNED NOT NULL,
  `payment_category_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_category_shop_id` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_payments_return`
--

CREATE TABLE `ro_payments_return` (
  `payments_return_id` int(10) UNSIGNED NOT NULL,
  `payments_return_type` enum('Incoming','Outgoing') DEFAULT NULL,
  `payments_return_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payments_return_accounts` int(10) UNSIGNED NOT NULL,
  `payments_return_payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payments_return_emp_id` int(10) UNSIGNED DEFAULT NULL,
  `payments_return_company_id` int(10) UNSIGNED DEFAULT NULL,
  `payments_return_customer_id` int(10) UNSIGNED DEFAULT NULL,
  `payments_return_sales_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payments_return_purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payments_return_amount` decimal(40,11) NOT NULL DEFAULT 0.00000000000,
  `payments_return_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_return_method` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `payment_return_cheque_no` varchar(30) DEFAULT NULL,
  `payment_return_cheque_date` date DEFAULT NULL,
  `payment_return_attachement` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `payments_return_by` int(10) UNSIGNED DEFAULT NULL,
  `payments_return_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_payment_adjustment`
--

CREATE TABLE `ro_payment_adjustment` (
  `pa_id` int(10) UNSIGNED NOT NULL,
  `pa_date` date NOT NULL,
  `pa_company` int(10) UNSIGNED NOT NULL,
  `pa_amount` decimal(30,11) NOT NULL DEFAULT 0.00000000000,
  `pa_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pa_add_by` int(10) UNSIGNED NOT NULL,
  `pa_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_payment_items`
--

CREATE TABLE `ro_payment_items` (
  `payment_items_id` bigint(20) UNSIGNED NOT NULL,
  `payment_items_payments_id` bigint(20) UNSIGNED NOT NULL,
  `payment_items_date` date NOT NULL,
  `payment_items_type` enum('Bill','Due Bill','Salary','Overtime','Bonus','Advance Adjustment') DEFAULT NULL,
  `payment_items_category_id` int(10) UNSIGNED DEFAULT NULL,
  `payment_items_company` int(10) UNSIGNED DEFAULT NULL,
  `payment_items_employee` int(10) UNSIGNED DEFAULT NULL,
  `payment_items_amount` decimal(40,11) NOT NULL DEFAULT 0.00000000000,
  `payment_items_accounts` int(10) UNSIGNED DEFAULT NULL,
  `payment_items_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_items_made_by` int(10) UNSIGNED DEFAULT NULL,
  `payment_itemss_advance_payments_id` int(10) UNSIGNED DEFAULT NULL,
  `payment_items_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_persons`
--

CREATE TABLE `ro_persons` (
  `person_id` int(10) UNSIGNED NOT NULL,
  `person_full_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `person_type` enum('Teacher','Student','Guardian','Service Holder','Merchant') DEFAULT NULL,
  `person_address` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `person_designation` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `person_student_class` int(2) UNSIGNED DEFAULT NULL,
  `person_institute` int(10) UNSIGNED DEFAULT NULL,
  `person_upazila` int(4) UNSIGNED DEFAULT NULL,
  `person_district` int(2) UNSIGNED DEFAULT NULL,
  `person_city` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `person_division` tinyint(1) UNSIGNED DEFAULT NULL,
  `person_postal_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `person_phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `person_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `person_website` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `person_photo` longblob DEFAULT NULL,
  `person_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `person_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `leads_collect_by` int(10) UNSIGNED DEFAULT NULL,
  `leads_collect_date` date DEFAULT NULL,
  `last_call_time` datetime DEFAULT NULL,
  `leads_source` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `person_add_by` int(10) UNSIGNED DEFAULT NULL,
  `person_update_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_persons_subject`
--

CREATE TABLE `ro_persons_subject` (
  `person_id` int(10) UNSIGNED NOT NULL,
  `subject_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_persons_tag`
--

CREATE TABLE `ro_persons_tag` (
  `person_id` int(10) UNSIGNED NOT NULL,
  `tags` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_products`
--

CREATE TABLE `ro_products` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_code` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `product_type` enum('Normal','Bundle','Grouped','Variable','Child') DEFAULT 'Normal',
  `product_group` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_parent_id` int(10) UNSIGNED DEFAULT NULL,
  `product_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `product_edition` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_unit` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_variations` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_purchase_price` decimal(30,11) NOT NULL DEFAULT 0.00000000000,
  `product_sale_price` decimal(30,11) NOT NULL DEFAULT 0.00000000000,
  `product_distributor_discount` varchar(50) NOT NULL DEFAULT '0',
  `product_wholesaler_discount` varchar(50) NOT NULL DEFAULT '0',
  `product_retailer_discount` varchar(50) NOT NULL DEFAULT '0',
  `product_consumer_discount` varchar(50) NOT NULL DEFAULT '0',
  `product_weight` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_height` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_width` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_photo` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_category_id` int(10) UNSIGNED DEFAULT NULL,
  `product_brand_id` int(10) UNSIGNED DEFAULT NULL,
  `product_generic` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_published_date` date DEFAULT NULL,
  `product_pages` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_isbn` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `product_alert_qnt` decimal(20,4) DEFAULT 5.0000,
  `product_packet_qnt` decimal(20,4) DEFAULT 0.0000,
  `product_initial_stock` decimal(20,4) DEFAULT 0.0000,
  `product_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `product_add_by` int(10) UNSIGNED DEFAULT NULL,
  `product_update_by` int(10) UNSIGNED DEFAULT NULL,
  `maintain_stock` tinyint(1) NOT NULL DEFAULT 1,
  `has_sub_product` tinyint(1) NOT NULL DEFAULT 0,
  `has_expiry_date` tinyint(1) NOT NULL DEFAULT 0,
  `is_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_products`
--

INSERT INTO `ro_products` (`product_id`, `product_code`, `product_name`, `product_type`, `product_group`, `product_parent_id`, `product_description`, `product_edition`, `product_unit`, `product_variations`, `product_purchase_price`, `product_sale_price`, `product_distributor_discount`, `product_wholesaler_discount`, `product_retailer_discount`, `product_consumer_discount`, `product_weight`, `product_height`, `product_width`, `product_photo`, `product_category_id`, `product_brand_id`, `product_generic`, `product_published_date`, `product_pages`, `product_isbn`, `product_alert_qnt`, `product_packet_qnt`, `product_initial_stock`, `product_add_on`, `product_update_on`, `product_add_by`, `product_update_by`, `maintain_stock`, `has_sub_product`, `has_expiry_date`, `is_disabled`, `is_trash`) VALUES
(1, '1682431158844', 'Normal Product', 'Normal', NULL, NULL, '', NULL, NULL, NULL, '8.00000000000', '10.00000000000', '', '', '', '', '', '', '', NULL, 1, NULL, NULL, NULL, NULL, NULL, '0.0000', '0.0000', '0.0000', '2023-04-25 13:59:29', '2023-04-25 13:59:29', 1, NULL, 1, 0, 0, 0, 0),
(2, '1682431227201', 'Variable Product', 'Variable', NULL, NULL, '', NULL, NULL, 'a:2:{s:5:&quot;Color&quot;;a:2:{i:0;s:5:&quot;Green&quot;;i:1;s:3:&quot;Red&quot;;}s:4:&quot;Size&quot;;a:2:{i:0;s:2:&quot;XL&quot;;i:1;s:1:&quot;M&quot;;}}', '0.00000000000', '0.00000000000', '', '', '', '', '', '', '', NULL, 1, NULL, NULL, NULL, NULL, NULL, '0.0000', '0.0000', '0.0000', '2023-04-25 14:00:58', '2023-04-25 14:00:58', 1, NULL, 1, 0, 0, 0, 0),
(3, '1682431240475', 'Variable Product - Green, XL', 'Child', '', 2, '', NULL, NULL, 'a:2:{s:5:&quot;Color&quot;;s:5:&quot;Green&quot;;s:4:&quot;Size&quot;;s:2:&quot;XL&quot;;}', '10.00000000000', '12.00000000000', '', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL, NULL, '5.0000', '0.0000', '0.0000', '2023-04-25 14:00:58', '2023-04-25 14:00:58', 1, NULL, 1, 0, 0, 0, 0),
(4, '1682431240476', 'Variable Product - Green, M', 'Child', '', 2, '', NULL, NULL, 'a:2:{s:5:&quot;Color&quot;;s:5:&quot;Green&quot;;s:4:&quot;Size&quot;;s:1:&quot;M&quot;;}', '10.00000000000', '12.00000000000', '', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL, NULL, '5.0000', '0.0000', '0.0000', '2023-04-25 14:00:58', '2023-04-25 14:00:58', 1, NULL, 1, 0, 0, 0, 0),
(5, '1682431240477', 'Variable Product - Red, XL', 'Child', '', 2, '', NULL, NULL, 'a:2:{s:5:&quot;Color&quot;;s:3:&quot;Red&quot;;s:4:&quot;Size&quot;;s:2:&quot;XL&quot;;}', '10.00000000000', '12.00000000000', '', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL, NULL, '5.0000', '0.0000', '0.0000', '2023-04-25 14:00:58', '2023-04-25 14:00:58', 1, NULL, 1, 0, 0, 0, 0),
(6, '1682431240478', 'Variable Product - Red, M', 'Child', '', 2, '', NULL, NULL, 'a:2:{s:5:&quot;Color&quot;;s:3:&quot;Red&quot;;s:4:&quot;Size&quot;;s:1:&quot;M&quot;;}', '10.00000000000', '12.00000000000', '', '', '', '', '', '', '', '', 1, NULL, NULL, NULL, NULL, NULL, '5.0000', '0.0000', '0.0000', '2023-04-25 14:00:58', '2023-04-25 14:00:58', 1, NULL, 1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_attributes`
--

CREATE TABLE `ro_product_attributes` (
  `pa_id` int(10) UNSIGNED NOT NULL,
  `pa_name` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pa_type` enum('Select','Color','Radio') DEFAULT NULL,
  `pa_description` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_product_attributes`
--

INSERT INTO `ro_product_attributes` (`pa_id`, `pa_name`, `pa_type`, `pa_description`, `is_trash`) VALUES
(1, 'Color', 'Color', NULL, 0),
(2, 'Size', 'Select', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_authors`
--

CREATE TABLE `ro_product_authors` (
  `author_id` int(10) UNSIGNED NOT NULL,
  `author_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `author_birth_date` date DEFAULT NULL,
  `author_death_date` date DEFAULT NULL,
  `author_mobile` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `author_address` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `author_country` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `author_description` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `author_website` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_author_relations`
--

CREATE TABLE `ro_product_author_relations` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_author_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_batches`
--

CREATE TABLE `ro_product_batches` (
  `batch_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `batch_number` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `batch_manufacturing_date` datetime DEFAULT NULL,
  `batch_expiry_date` datetime DEFAULT NULL,
  `batch_description` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_brands`
--

CREATE TABLE `ro_product_brands` (
  `brand_id` int(10) UNSIGNED NOT NULL,
  `brand_name` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `brand_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_category`
--

CREATE TABLE `ro_product_category` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_shop_id` int(10) UNSIGNED DEFAULT NULL,
  `category_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_product_category`
--

INSERT INTO `ro_product_category` (`category_id`, `category_shop_id`, `category_name`, `is_trash`) VALUES
(1, NULL, 'Test Category', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_editions`
--

CREATE TABLE `ro_product_editions` (
  `edition_id` int(10) UNSIGNED NOT NULL,
  `edition_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `edition_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_generic`
--

CREATE TABLE `ro_product_generic` (
  `generic_id` int(10) UNSIGNED NOT NULL,
  `generic_name` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `generic_description` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `generic_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_meta`
--

CREATE TABLE `ro_product_meta` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `meta_type` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `meta_key` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `meta_value` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_product_meta`
--

INSERT INTO `ro_product_meta` (`product_id`, `meta_type`, `meta_key`, `meta_value`) VALUES
(2, 'Default-Variation', 'Color', 'Green'),
(2, 'Default-Variation', 'Size', 'M'),
(3, 'Variation', 'Color', 'Green'),
(3, 'Variation', 'Size', 'XL'),
(4, 'Variation', 'Color', 'Green'),
(4, 'Variation', 'Size', 'M'),
(5, 'Variation', 'Color', 'Red'),
(5, 'Variation', 'Size', 'XL'),
(6, 'Variation', 'Color', 'Red'),
(6, 'Variation', 'Size', 'M');

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_price`
--

CREATE TABLE `ro_product_price` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `shop_id` int(10) UNSIGNED NOT NULL,
  `purchase_price` decimal(30,11) NOT NULL DEFAULT 0.00000000000,
  `sale_price` decimal(30,11) NOT NULL DEFAULT 0.00000000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_stock`
--

CREATE TABLE `ro_product_stock` (
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `stock_type` enum('initial','adjustment','sale-production','sale-processing','sale','sale-order','wastage-sale','sale-return','purchase','purchase-order','purchase-return','transfer-in','transfer-out','specimen-copy','specimen-copy-return','undeclared') DEFAULT 'undeclared',
  `stock_entry_date` date NOT NULL,
  `stock_sales_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_transfer_id` int(10) UNSIGNED DEFAULT NULL,
  `stock_sc_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'SC = Specimen Copy',
  `stock_employee_id` int(10) UNSIGNED DEFAULT NULL,
  `stock_se_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'SE = Stock Entry',
  `stock_warehouse_id` int(10) UNSIGNED NOT NULL,
  `stock_shop_id` int(10) UNSIGNED DEFAULT NULL,
  `stock_product_id` int(10) UNSIGNED NOT NULL,
  `stock_batch_id` int(10) UNSIGNED DEFAULT NULL,
  `stock_item_price` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `stock_item_qty` decimal(20,4) NOT NULL DEFAULT 1.0000,
  `stock_item_discount` decimal(25,11) NOT NULL DEFAULT 0.00000000000,
  `stock_item_subtotal` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `stock_item_description` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stock_item_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_item_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock_trashed_on` timestamp NULL DEFAULT NULL,
  `stock_created_by` int(10) UNSIGNED DEFAULT NULL,
  `is_bundle_item` tinyint(1) NOT NULL DEFAULT 0,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_product_stock`
--

INSERT INTO `ro_product_stock` (`stock_id`, `stock_type`, `stock_entry_date`, `stock_sales_id`, `stock_purchase_id`, `stock_transfer_id`, `stock_sc_id`, `stock_employee_id`, `stock_se_id`, `stock_warehouse_id`, `stock_shop_id`, `stock_product_id`, `stock_batch_id`, `stock_item_price`, `stock_item_qty`, `stock_item_discount`, `stock_item_subtotal`, `stock_item_description`, `stock_item_add_on`, `stock_item_update_on`, `stock_trashed_on`, `stock_created_by`, `is_bundle_item`, `is_trash`) VALUES
(1, 'purchase', '2023-04-25', NULL, 1, NULL, NULL, NULL, NULL, 1, 1, 4, NULL, '10.00000000000', '500.0000', '0.00000000000', '5000.00000000000', NULL, '2023-04-25 14:02:20', '2023-04-25 14:02:20', NULL, 1, 0, 0),
(2, 'purchase', '2023-04-25', NULL, 1, NULL, NULL, NULL, NULL, 1, 1, 6, NULL, '10.00000000000', '500.0000', '0.00000000000', '5000.00000000000', NULL, '2023-04-25 14:02:20', '2023-04-25 14:02:20', NULL, 1, 0, 0),
(3, 'purchase', '2023-04-25', NULL, 1, NULL, NULL, NULL, NULL, 1, 1, 3, NULL, '10.00000000000', '500.0000', '0.00000000000', '5000.00000000000', NULL, '2023-04-25 14:02:20', '2023-04-25 14:02:20', NULL, 1, 0, 0),
(4, 'purchase', '2023-04-25', NULL, 1, NULL, NULL, NULL, NULL, 1, 1, 5, NULL, '10.00000000000', '500.0000', '0.00000000000', '5000.00000000000', NULL, '2023-04-25 14:02:20', '2023-04-25 14:02:20', NULL, 1, 0, 0),
(5, 'purchase', '2023-04-25', NULL, 1, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, '8.00000000000', '500.0000', '0.00000000000', '4000.00000000000', NULL, '2023-04-25 14:02:20', '2023-04-25 14:02:20', NULL, 1, 0, 0),
(6, 'sale', '2023-04-25', 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 4, NULL, '12.00000000000', '1.0000', '0.00000000000', '12.00000000000', '', '2023-04-25 14:03:39', '2023-04-25 14:03:39', NULL, 1, 0, 0),
(7, 'sale', '2023-04-25', 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, '10.00000000000', '1.0000', '0.00000000000', '10.00000000000', '', '2023-04-25 14:03:39', '2023-04-25 14:03:39', NULL, 1, 0, 0);

--
-- Triggers `ro_product_stock`
--
DELIMITER $$
CREATE TRIGGER `stock_update_after_delete` AFTER DELETE ON `ro_product_stock` FOR EACH ROW BEGIN

    -- Update, When:
    -- 1. The product has no unit 
    -- 2. and the product already have in the product_base_stock

    IF  (
            (
                SELECT COUNT(*) FROM ro_products WHERE product_id = OLD.stock_product_id and product_unit is null
            ) > 0
            and 
            (
                select count(*) from product_base_stock 
                where product_id = OLD.stock_product_id and (batch_id is null or batch_id = OLD.stock_batch_id) and warehouse = OLD.stock_warehouse_id
            ) > 0

        ) THEN
            -- update it
            UPDATE product_base_stock SET base_stock_in = base_stock_in + (

                                                            -- In case of deletion the stock counting will reverts.
                                                            -- Suppose, when we insert purchase the stock counting will: stock + purchase
                                                            -- But when we delete the stock counting will: stock - purchase

                                                            (   
                                                                -- All stock out, such as sale, specimen copy etc now treatated as stock in, in case of delete entry
                                                                CASE WHEN ( 
                                                                            OLD.stock_type = 'sale' OR 
                                                                            OLD.stock_type = 'wastage-sale' OR
                                                                            OLD.stock_type = 'purchase-return' OR
                                                                            OLD.stock_type = 'specimen-copy' OR
                                                                            OLD.stock_type = 'transfer-out'
                                                                        ) and OLD.stock_item_qty IS NOT NULL 
                                                                    THEN OLD.stock_item_qty 
                                                                    ELSE 0 
                                                                END

                                                                - -- subtract stock out from stock in

                                                                -- All stock in such as purchase, return etc now treated as stock out, in case of delete entry
                                                                CASE WHEN ( 
                                                                            OLD.stock_type = 'initial' OR 
                                                                            OLD.stock_type = 'adjustment' OR 
                                                                            OLD.stock_type = 'sale-production' OR 
                                                                            OLD.stock_type = 'sale-return' OR
                                                                            OLD.stock_type = 'purchase' OR
                                                                            OLD.stock_type = 'specimen-copy-return' OR
                                                                            OLD.stock_type = 'transfer-in'
                                                                        ) and OLD.stock_item_qty IS NOT NULL 
                                                                    THEN OLD.stock_item_qty 
                                                                    ELSE 0 
                                                                END 
                                                            )
                                                        
                                                            * -- And multiply with base quantity, which is taken from product unit 
                                                            
                                                            base_qty -- This fiends value taken from product_base_stock, not from product_unit. Because we have already collect this value now just updatting
                                                    )
            WHERE product_id = OLD.stock_product_id and (batch_id is null or batch_id = OLD.stock_batch_id) and warehouse = OLD.stock_warehouse_id;

    ELSE

        -- Delete previous stock 
        delete from product_base_stock 
                    where product_id = OLD.stock_product_id and (batch_id is null or batch_id = OLD.stock_batch_id) and warehouse = OLD.stock_warehouse_id;
            
        -- Insert New stock
        insert into product_base_stock 
            (product_id, vp_id, warehouse, base_stock_in, base_qty, batch_id, batch_expiry_date)
            SELECT 
                    product_stock.stock_product_id as product_id,
                    product.product_id AS vp_id, 
                    stock_warehouse_id as warehouse, 
                    sum(
                        (   -- All stock in sunch as purchase, return etc
                                CASE WHEN ( 
                                            stock_type = 'initial' OR 
                                            stock_type = 'adjustment' OR 
                                            stock_type = 'sale-production' OR 
                                            stock_type = 'sale-return' OR
                                            stock_type = 'purchase' OR
                                            stock_type = 'specimen-copy-return' OR
                                            stock_type = 'transfer-in'
                                        ) and stock_item_qty IS NOT NULL 
                                    THEN stock_item_qty 
                                    ELSE 0 
                                END 
                        
                            - -- subtract stock out from stock in
                            
                            -- All stock out, such as sale, specimen copy etc
                            CASE WHEN ( 
                                        stock_type = 'sale' OR 
                                        stock_type = 'wastage-sale' OR
                                        stock_type = 'purchase-return' OR
                                        stock_type = 'specimen-copy' OR
                                        stock_type = 'transfer-out'
                                    ) and stock_item_qty IS NOT NULL 
                                THEN stock_item_qty 
                                ELSE 0 
                            END
                        )
                    
                        * -- And multiply with base quantity, which is taken from product unit 
                        
                        if(vp_unit.base_qnt is null, 1, vp_unit.base_qnt) 
                
                    ) as base_stock_in,
                if(np_unit.base_qnt is null, 1, np_unit.base_qnt) as base_qty,
                stock_batch_id as batch_id,
                batch_expiry_date
            FROM `ro_products` as product
            left join ro_products as vp on vp.product_parent_id = product.product_parent_id and vp.product_variations = product.product_variations or vp.product_id = product.product_id
            left join ro_product_stock as product_stock on vp.product_id = product_stock.stock_product_id
            left join ro_product_units as vp_unit on vp.product_unit = vp_unit.unit_name -- vp = variable product
            left join ro_product_units as np_unit on product.product_unit = np_unit.unit_name -- np = normal product
            left join ro_product_batches as batch on batch.batch_id = stock_batch_id and batch.product_id = stock_product_id
            where stock_warehouse_id is not null and product.is_trash = 0 and product_stock.is_trash = 0 and 
            product_stock.stock_product_id = OLD.stock_product_id and 
            product_stock.stock_warehouse_id = OLD.stock_warehouse_id and 
            (product_stock.stock_batch_id is null or product_stock.stock_batch_id = OLD.stock_batch_id)
            group by product.product_id, product_stock.stock_product_id, stock_warehouse_id, stock_batch_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stock_update_after_insert` AFTER INSERT ON `ro_product_stock` FOR EACH ROW BEGIN

    -- Update, When:
    -- 1. The product has no unit 
    -- 2. and the product already have in the product_base_stock

    IF  (
            (
                SELECT COUNT(*) FROM ro_products WHERE product_id = NEW.stock_product_id and product_unit is null
            ) > 0
            and 
            (
                select count(*) from product_base_stock 
                where product_id = NEW.stock_product_id and (batch_id is null or batch_id = NEW.stock_batch_id) and warehouse = NEW.stock_warehouse_id
            ) > 0

        ) THEN

            -- Update the product Stock
            UPDATE product_base_stock SET base_stock_in = base_stock_in + (
                                                            (   -- All stock in sunch as purchase, return etc
                                                                    CASE WHEN ( 
                                                                                NEW.stock_type = 'initial' OR 
                                                                                NEW.stock_type = 'adjustment' OR 
                                                                                NEW.stock_type = 'sale-production' OR 
                                                                                NEW.stock_type = 'sale-return' OR
                                                                                NEW.stock_type = 'purchase' OR
                                                                                NEW.stock_type = 'specimen-copy-return' OR
                                                                                NEW.stock_type = 'transfer-in'
                                                                            ) and NEW.stock_item_qty IS NOT NULL 
                                                                        THEN NEW.stock_item_qty 
                                                                        ELSE 0 
                                                                    END 
                                                            
                                                                - -- subtract stock out from stock in
                                                                
                                                                -- All stock out, such as sale, specimen copy etc
                                                                CASE WHEN ( 
                                                                            NEW.stock_type = 'sale' OR 
                                                                            NEW.stock_type = 'wastage-sale' OR
                                                                            NEW.stock_type = 'purchase-return' OR
                                                                            NEW.stock_type = 'specimen-copy' OR
                                                                            NEW.stock_type = 'transfer-out'
                                                                        ) and NEW.stock_item_qty IS NOT NULL 
                                                                    THEN NEW.stock_item_qty 
                                                                    ELSE 0 
                                                                END
                                                            )
                                                        
                                                            * -- And multiply with base quantity, which is taken from product unit 
                                                            
                                                            base_qty -- This fiends value taken from product_base_stock, not from product_unit. Because we have already collect this value now just updatting
                                                    )
            WHERE NEW.is_trash = 0 and product_id = NEW.stock_product_id and (batch_id is null or batch_id = NEW.stock_batch_id) and warehouse = NEW.stock_warehouse_id;

        ELSE
    
            -- Delete previous stock 
            delete from product_base_stock 
                        where product_id = NEW.stock_product_id and (batch_id is null or batch_id = NEW.stock_batch_id) and warehouse = NEW.stock_warehouse_id;
                
            -- Insert New stock
            insert into product_base_stock 
                (product_id, vp_id, warehouse, base_stock_in, base_qty, batch_id, batch_expiry_date)
                SELECT 
                        product_stock.stock_product_id as product_id,
                        product.product_id AS vp_id, 
                        stock_warehouse_id as warehouse, 
                        sum(
                            (   -- All stock in sunch as purchase, return etc
                                    CASE WHEN ( 
                                                stock_type = 'initial' OR 
                                                stock_type = 'adjustment' OR 
                                                stock_type = 'sale-production' OR 
                                                stock_type = 'sale-return' OR
                                                stock_type = 'purchase' OR
                                                stock_type = 'specimen-copy-return' OR
                                                stock_type = 'transfer-in'
                                            ) and stock_item_qty IS NOT NULL 
                                        THEN stock_item_qty 
                                        ELSE 0 
                                    END 
                            
                                - -- subtract stock out from stock in
                                
                                -- All stock out, such as sale, specimen copy etc
                                CASE WHEN ( 
                                            stock_type = 'sale' OR 
                                            stock_type = 'wastage-sale' OR
                                            stock_type = 'purchase-return' OR
                                            stock_type = 'specimen-copy' OR
                                            stock_type = 'transfer-out'
                                        ) and stock_item_qty IS NOT NULL 
                                    THEN stock_item_qty 
                                    ELSE 0 
                                END
                            )
                        
                            * -- And multiply with base quantity, which is taken from product unit 
                            
                            if(vp_unit.base_qnt is null, 1, vp_unit.base_qnt) 
                    
                        ) as base_stock_in,
                    if(np_unit.base_qnt is null, 1, np_unit.base_qnt) as base_qty,
                    stock_batch_id as batch_id,
                    batch_expiry_date
                FROM `ro_products` as product
                left join ro_products as vp on vp.product_parent_id = product.product_parent_id and vp.product_variations = product.product_variations or vp.product_id = product.product_id
                left join ro_product_stock as product_stock on vp.product_id = product_stock.stock_product_id
                left join ro_product_units as vp_unit on vp.product_unit = vp_unit.unit_name -- vp = variable product
                left join ro_product_units as np_unit on product.product_unit = np_unit.unit_name -- np = normal product
                left join ro_product_batches as batch on batch.batch_id = stock_batch_id and batch.product_id = stock_product_id
                where stock_warehouse_id is not null and product.is_trash = 0 and product_stock.is_trash = 0 and 
                product_stock.stock_product_id = NEW.stock_product_id and 
                product_stock.stock_warehouse_id = NEW.stock_warehouse_id and 
                (product_stock.stock_batch_id is null or product_stock.stock_batch_id = NEW.stock_batch_id)
                group by product.product_id, product_stock.stock_product_id, stock_warehouse_id, stock_batch_id;
            END IF; 
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stock_update_after_update` AFTER UPDATE ON `ro_product_stock` FOR EACH ROW BEGIN
    
    -- If the stock_item_qty is update or stock_type is update then run
    if(NEW.stock_item_qty <=> OLD.stock_item_qty || NEW.stock_type <=> OLD.stock_type) THEN

        -- Delete previous stock 
        delete from product_base_stock 
                    where product_id = NEW.stock_product_id and (batch_id is null or batch_id = NEW.stock_batch_id) and warehouse = NEW.stock_warehouse_id;
            
        -- Insert New stock
        insert into product_base_stock 
            (product_id, vp_id, warehouse, base_stock_in, base_qty, batch_id, batch_expiry_date)
            SELECT 
                    product_stock.stock_product_id as product_id,
                    product.product_id AS vp_id, 
                    stock_warehouse_id as warehouse, 
                    sum(
                        (   -- All stock in sunch as purchase, return etc
                                CASE WHEN ( 
                                            stock_type = 'initial' OR 
                                            stock_type = 'adjustment' OR 
                                            stock_type = 'sale-production' OR 
                                            stock_type = 'sale-return' OR
                                            stock_type = 'purchase' OR
                                            stock_type = 'specimen-copy-return' OR
                                            stock_type = 'transfer-in'
                                        ) and stock_item_qty IS NOT NULL 
                                    THEN stock_item_qty 
                                    ELSE 0 
                                END 
                        
                            - -- subtract stock out from stock in
                            
                            -- All stock out, such as sale, specimen copy etc
                            CASE WHEN ( 
                                        stock_type = 'sale' OR 
                                        stock_type = 'wastage-sale' OR
                                        stock_type = 'purchase-return' OR
                                        stock_type = 'specimen-copy' OR
                                        stock_type = 'transfer-out'
                                    ) and stock_item_qty IS NOT NULL 
                                THEN stock_item_qty 
                                ELSE 0 
                            END
                        )
                    
                        * -- And multiply with base quantity, which is taken from product unit 
                        
                        if(vp_unit.base_qnt is null, 1, vp_unit.base_qnt) 
                
                    ) as base_stock_in,
                if(np_unit.base_qnt is null, 1, np_unit.base_qnt) as base_qty,
                stock_batch_id as batch_id,
                batch_expiry_date
            FROM `ro_products` as product
            left join ro_products as vp on vp.product_parent_id = product.product_parent_id and vp.product_variations = product.product_variations or vp.product_id = product.product_id
            left join ro_product_stock as product_stock on vp.product_id = product_stock.stock_product_id
            left join ro_product_units as vp_unit on vp.product_unit = vp_unit.unit_name -- vp = variable product
            left join ro_product_units as np_unit on product.product_unit = np_unit.unit_name -- np = normal product
            left join ro_product_batches as batch on batch.batch_id = stock_batch_id and batch.product_id = stock_product_id
            where stock_warehouse_id is not null and product.is_trash = 0 and product_stock.is_trash = 0 and 
            product_stock.stock_product_id = NEW.stock_product_id and 
            product_stock.stock_warehouse_id = NEW.stock_warehouse_id and 
            (product_stock.stock_batch_id is null or product_stock.stock_batch_id = NEW.stock_batch_id)
            group by product.product_id, product_stock.stock_product_id, stock_warehouse_id, stock_batch_id;

    END IF; 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_units`
--

CREATE TABLE `ro_product_units` (
  `unit_id` int(10) UNSIGNED NOT NULL,
  `unit_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `short_name` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `equal_unit_id` int(10) UNSIGNED DEFAULT NULL,
  `equal_unit_qnt` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `base_qnt` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `unit_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_product_units`
--

INSERT INTO `ro_product_units` (`unit_id`, `unit_name`, `short_name`, `equal_unit_id`, `equal_unit_qnt`, `base_qnt`, `unit_description`, `is_trash`) VALUES
(1, 'Pisces', 'Pcs', NULL, '0.0000', '1.0000', '', 0),
(2, 'Dozen', 'DOZ', 1, '12.0000', '12.0000', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_product_variations`
--

CREATE TABLE `ro_product_variations` (
  `pv_id` int(10) UNSIGNED NOT NULL,
  `pa_name` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pv_name` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `pv_description` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_product_variations`
--

INSERT INTO `ro_product_variations` (`pv_id`, `pa_name`, `pv_name`, `pv_description`, `is_trash`) VALUES
(1, 'Color', 'Red', NULL, 0),
(2, 'Color', 'Green', NULL, 0),
(3, 'Color', 'Blue', NULL, 0),
(4, 'Size', 'M', NULL, 0),
(5, 'Size', 'XL', NULL, 0),
(6, 'Size', 'L', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_purchases`
--

CREATE TABLE `ro_purchases` (
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_reference` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `purchase_status` enum('Received','Pending','Ordered') DEFAULT NULL,
  `purchase_company_id` int(10) UNSIGNED NOT NULL,
  `purchase_warehouse_id` int(10) UNSIGNED NOT NULL,
  `purchase_shop_id` int(10) UNSIGNED DEFAULT NULL,
  `purchase_quantity` decimal(20,4) DEFAULT NULL,
  `purchase_total_amount` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_product_discount` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_discount` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_tariff_charges` decimal(25,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_tariff_charges_details` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `purchase_shipping` decimal(25,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_grand_total` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_paid_amount` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_change` decimal(25,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_due` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_due_date` date DEFAULT NULL,
  `purchase_payment_status` enum('paid','partial','due') DEFAULT NULL,
  `purchase_payment_method` enum('Cash','Cheque','Cards','Others') DEFAULT NULL,
  `purchase_total_item` int(10) UNSIGNED DEFAULT NULL,
  `purchase_total_packets` int(10) UNSIGNED DEFAULT 0,
  `purchase_surcharge` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `purchase_note` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `purchase_attachments` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `purchase_return_id` int(10) UNSIGNED DEFAULT NULL,
  `purchase_created_by` int(10) UNSIGNED DEFAULT NULL,
  `purchase_update_by` int(10) UNSIGNED DEFAULT NULL,
  `purchase_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `purchase_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_return` tinyint(1) NOT NULL DEFAULT 0,
  `is_wastage` tinyint(1) NOT NULL DEFAULT 0,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_purchases`
--

INSERT INTO `ro_purchases` (`purchase_id`, `purchase_date`, `purchase_reference`, `purchase_status`, `purchase_company_id`, `purchase_warehouse_id`, `purchase_shop_id`, `purchase_quantity`, `purchase_total_amount`, `purchase_product_discount`, `purchase_discount`, `purchase_tariff_charges`, `purchase_tariff_charges_details`, `purchase_shipping`, `purchase_grand_total`, `purchase_paid_amount`, `purchase_change`, `purchase_due`, `purchase_due_date`, `purchase_payment_status`, `purchase_payment_method`, `purchase_total_item`, `purchase_total_packets`, `purchase_surcharge`, `purchase_note`, `purchase_attachments`, `purchase_return_id`, `purchase_created_by`, `purchase_update_by`, `purchase_add_on`, `purchase_update_on`, `is_return`, `is_wastage`, `is_trash`) VALUES
(1, '2023-04-25', NULL, 'Received', 1, 1, 1, '2500.0000', '24000.00000000000', '0.00000000000', '0.00000000000', '0.00000000000', 'a:2:{s:6:&quot;tariff&quot;;a:1:{i:0;s:0:&quot;&quot;;}s:5:&quot;value&quot;;a:1:{i:0;s:1:&quot;0&quot;;}}', '0.00000000000', '24000.00000000000', '0.00000000000', '0.00000000000', '24000.00000000000', NULL, 'due', 'Cash', 5, 0, '0.00000000000', '', NULL, NULL, 1, NULL, '2023-04-25 14:02:20', '2023-04-25 14:02:20', 0, 0, 0);

--
-- Triggers `ro_purchases`
--
DELIMITER $$
CREATE TRIGGER `stock_delete_after_purchase_delete` BEFORE DELETE ON `ro_purchases` FOR EACH ROW BEGIN
            DELETE FROM ro_product_stock WHERE stock_purchase_id = OLD.purchase_id;
    END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stock_update_after_purchase_update` AFTER UPDATE ON `ro_purchases` FOR EACH ROW BEGIN
            if( NEW.is_trash = 1 ) THEN
                UPDATE ro_product_stock SET stock_trashed_on = CURRENT_TIMESTAMP WHERE stock_purchase_id = OLD.purchase_id;
            END IF;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ro_received_payments`
--

CREATE TABLE `ro_received_payments` (
  `received_payments_id` int(10) UNSIGNED NOT NULL,
  `received_payments_datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `received_payments_type` enum('Advance Collection','Received Payments','Sales Payments','Wastage Sales Payments','Discounts') DEFAULT NULL,
  `received_payments_shop` int(10) UNSIGNED DEFAULT NULL,
  `received_payments_accounts` int(10) UNSIGNED DEFAULT NULL,
  `received_payments_sales_id` bigint(20) UNSIGNED DEFAULT NULL,
  `received_payments_from` int(10) UNSIGNED NOT NULL,
  `received_payments_amount` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `received_payments_bonus` decimal(30,11) NOT NULL DEFAULT 0.00000000000,
  `received_payments_details` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `received_payments_method` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `received_payments_cheque_no` varchar(30) DEFAULT NULL,
  `received_payments_cheque_date` date DEFAULT NULL,
  `received_payments_reference` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `received_payments_add_by` int(10) UNSIGNED DEFAULT NULL,
  `received_payments_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_received_payments`
--

INSERT INTO `ro_received_payments` (`received_payments_id`, `received_payments_datetime`, `received_payments_type`, `received_payments_shop`, `received_payments_accounts`, `received_payments_sales_id`, `received_payments_from`, `received_payments_amount`, `received_payments_bonus`, `received_payments_details`, `received_payments_method`, `received_payments_cheque_no`, `received_payments_cheque_date`, `received_payments_reference`, `received_payments_add_by`, `received_payments_add_on`, `is_trash`) VALUES
(1, '2023-04-25 14:03:39', 'Sales Payments', 1, 1, 1, 1, '22.00000000000', '0.00000000000', '', 'Cash', NULL, NULL, '', 1, '2023-04-25 14:03:39', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_salaries`
--

CREATE TABLE `ro_salaries` (
  `salary_id` bigint(20) UNSIGNED NOT NULL,
  `salary_emp_id` int(10) UNSIGNED NOT NULL,
  `salary_type` enum('Salary','Overtime','Bonus') NOT NULL,
  `salary_month` date NOT NULL,
  `salary_amount` decimal(40,11) NOT NULL DEFAULT 0.00000000000,
  `salary_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `salary_add_by` int(10) UNSIGNED DEFAULT NULL,
  `salary_update_by` int(10) UNSIGNED DEFAULT NULL,
  `salary_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `salary_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_sales`
--

CREATE TABLE `ro_sales` (
  `sales_id` bigint(20) UNSIGNED NOT NULL,
  `sales_order_date` date DEFAULT NULL,
  `sales_delivery_date` date NOT NULL,
  `sales_status` enum('Order Placed','In Production','Processing','Call not Picked','Confirmed','Hold','Delivered','Cancelled') DEFAULT NULL,
  `sales_reference` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sales_customer_id` int(10) UNSIGNED NOT NULL,
  `sales_warehouse_id` int(10) UNSIGNED NOT NULL,
  `sales_shop_id` int(10) UNSIGNED NOT NULL,
  `sales_quantity` decimal(20,4) NOT NULL,
  `sales_total_amount` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `sales_product_discount` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `sales_discount` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `sales_tariff_charges` decimal(25,11) NOT NULL DEFAULT 0.00000000000,
  `sales_tariff_charges_details` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sales_shipping` decimal(25,11) NOT NULL DEFAULT 0.00000000000,
  `sales_adjustment` decimal(25,11) NOT NULL DEFAULT 0.00000000000,
  `sales_grand_total` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `sales_paid_amount` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `sales_change` decimal(25,11) NOT NULL DEFAULT 0.00000000000,
  `sales_due` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `sales_due_date` date DEFAULT NULL,
  `sales_payment_status` enum('paid','partial','due') DEFAULT NULL,
  `sales_payment_method` enum('Cash','Cheque','Cards','Bank Transfer','Others') DEFAULT NULL,
  `sales_total_item` int(10) UNSIGNED DEFAULT NULL,
  `sales_total_packets` int(10) UNSIGNED DEFAULT 0,
  `sales_by_pos` tinyint(1) DEFAULT 1,
  `sales_by_website` tinyint(1) DEFAULT 0,
  `sales_surcharge` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `sales_note` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `sales_shipping_address` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `sales_attachments` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `sales_return_id` int(10) UNSIGNED DEFAULT NULL,
  `sales_created_by` int(10) UNSIGNED DEFAULT NULL,
  `sales_update_by` int(10) UNSIGNED DEFAULT NULL,
  `sales_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `sales_update_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_return` tinyint(1) NOT NULL DEFAULT 0,
  `is_exchange` tinyint(1) NOT NULL DEFAULT 0,
  `is_wastage` tinyint(1) NOT NULL DEFAULT 0,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_sales`
--

INSERT INTO `ro_sales` (`sales_id`, `sales_order_date`, `sales_delivery_date`, `sales_status`, `sales_reference`, `sales_customer_id`, `sales_warehouse_id`, `sales_shop_id`, `sales_quantity`, `sales_total_amount`, `sales_product_discount`, `sales_discount`, `sales_tariff_charges`, `sales_tariff_charges_details`, `sales_shipping`, `sales_adjustment`, `sales_grand_total`, `sales_paid_amount`, `sales_change`, `sales_due`, `sales_due_date`, `sales_payment_status`, `sales_payment_method`, `sales_total_item`, `sales_total_packets`, `sales_by_pos`, `sales_by_website`, `sales_surcharge`, `sales_note`, `sales_shipping_address`, `sales_attachments`, `sales_return_id`, `sales_created_by`, `sales_update_by`, `sales_add_on`, `sales_update_on`, `is_return`, `is_exchange`, `is_wastage`, `is_trash`) VALUES
(1, NULL, '2023-04-25', 'Delivered', 'SALE/POS/11/1', 1, 1, 1, '2.0000', '22.00000000000', '0.00000000000', '0.00000000000', '0.00000000000', 'a:2:{s:6:&quot;tariff&quot;;a:1:{i:0;s:0:&quot;&quot;;}s:5:&quot;value&quot;;a:1:{i:0;s:1:&quot;0&quot;;}}', '0.00000000000', '0.00000000000', '22.00000000000', '22.00000000000', '0.00000000000', '0.00000000000', NULL, 'paid', NULL, 2, 0, 1, 0, '0.00000000000', '', '', NULL, NULL, 1, NULL, '2023-04-25 14:03:39', '2023-04-25 14:03:39', 0, 0, 0, 0);

--
-- Triggers `ro_sales`
--
DELIMITER $$
CREATE TRIGGER `stock_delete_after_sale_delete` BEFORE DELETE ON `ro_sales` FOR EACH ROW BEGIN
            DELETE FROM ro_product_stock WHERE stock_sales_id = OLD.sales_id;
    END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stock_update_after_sale_update` AFTER UPDATE ON `ro_sales` FOR EACH ROW BEGIN
            if( NEW.is_trash = 1 ) THEN
                UPDATE ro_product_stock SET stock_trashed_on = CURRENT_TIMESTAMP WHERE stock_sales_id= OLD.sales_id;
            END IF;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ro_sc_distribution`
--

CREATE TABLE `ro_sc_distribution` (
  `scd_id` int(10) UNSIGNED NOT NULL,
  `scd_date` date DEFAULT NULL,
  `scd_distributor` int(10) UNSIGNED NOT NULL,
  `scd_person_id` int(10) UNSIGNED NOT NULL,
  `scd_product_id` int(10) UNSIGNED NOT NULL,
  `scd_product_qnt` decimal(9,4) NOT NULL DEFAULT 0.0000,
  `scd_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `scd_add_by` int(10) UNSIGNED DEFAULT NULL,
  `is_bundle_item` tinyint(1) NOT NULL DEFAULT 0,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_shops`
--

CREATE TABLE `ro_shops` (
  `shop_id` int(10) UNSIGNED NOT NULL,
  `shop_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shop_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `shop_city` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `shop_state` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `shop_postal_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `shop_country` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `shop_phone` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `shop_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `shop_invoice_type` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'normal',
  `shop_invoice_footer` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shop_logo` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_shops`
--

INSERT INTO `ro_shops` (`shop_id`, `shop_name`, `shop_address`, `shop_city`, `shop_state`, `shop_postal_code`, `shop_country`, `shop_phone`, `shop_email`, `shop_invoice_type`, `shop_invoice_footer`, `shop_logo`, `is_trash`) VALUES
(1, 'Shop-1', '', '', '', '', '', 'xxxxx', '', 'details', '', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_sip_credentials`
--

CREATE TABLE `ro_sip_credentials` (
  `sip_id` int(10) UNSIGNED NOT NULL,
  `sip_representative` int(10) UNSIGNED NOT NULL,
  `sip_username` varchar(55) NOT NULL,
  `sip_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sip_domain` varchar(100) NOT NULL,
  `sip_websocket_addr` varchar(255) NOT NULL,
  `sip_created_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_sms_sender`
--

CREATE TABLE `ro_sms_sender` (
  `sms_id` int(10) UNSIGNED NOT NULL,
  `send_to` varchar(20) DEFAULT NULL,
  `send_time` datetime DEFAULT NULL,
  `sms_text` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `send_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_sms_template`
--

CREATE TABLE `ro_sms_template` (
  `st_id` int(10) UNSIGNED NOT NULL,
  `st_text` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `st_pin` tinyint(1) NOT NULL DEFAULT 0,
  `st_created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_specimen_copies`
--

CREATE TABLE `ro_specimen_copies` (
  `sc_id` int(10) UNSIGNED NOT NULL,
  `sc_date` date NOT NULL,
  `sc_employee_id` int(10) UNSIGNED NOT NULL,
  `sc_warehouse_id` int(10) UNSIGNED NOT NULL,
  `sc_type` enum('Dispatch','Return') DEFAULT NULL,
  `sc_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `sc_add_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `ro_specimen_copies`
--
DELIMITER $$
CREATE TRIGGER `stock_delete_after_specimen_copy_delete` BEFORE DELETE ON `ro_specimen_copies` FOR EACH ROW BEGIN
            DELETE FROM ro_product_stock WHERE ro_product_stock.stock_sc_id  = OLD.sc_id;
    END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stock_update_after_specimen_copy_update` AFTER UPDATE ON `ro_specimen_copies` FOR EACH ROW BEGIN
            if( NEW.is_trash = 1 ) THEN
                UPDATE ro_product_stock SET stock_trashed_on = CURRENT_TIMESTAMP WHERE ro_product_stock.stock_sc_id  = OLD.sc_id;
            END IF;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ro_stock_entries`
--

CREATE TABLE `ro_stock_entries` (
  `se_id` int(10) UNSIGNED NOT NULL,
  `se_date` date NOT NULL,
  `se_type` enum('Production','Adjustment','Initial') DEFAULT 'Initial',
  `se_warehouse_id` int(10) UNSIGNED NOT NULL,
  `se_shop_id` int(10) UNSIGNED DEFAULT NULL,
  `se_note` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `se_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `se_add_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `ro_stock_entries`
--
DELIMITER $$
CREATE TRIGGER `stock_delete_after_stock_entry_delete` BEFORE DELETE ON `ro_stock_entries` FOR EACH ROW BEGIN
            DELETE FROM ro_product_stock WHERE stock_se_id = OLD.se_id;
    END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stock_update_after_stock_entry_update` AFTER UPDATE ON `ro_stock_entries` FOR EACH ROW BEGIN
            if( NEW.is_trash = 1 ) THEN
                UPDATE ro_product_stock SET stock_trashed_on = CURRENT_TIMESTAMP WHERE stock_se_id = OLD.se_id;
            END IF;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ro_stock_transfer`
--

CREATE TABLE `ro_stock_transfer` (
  `stock_transfer_id` int(10) UNSIGNED NOT NULL,
  `stock_transfer_date` date NOT NULL,
  `stock_transfer_status` enum('Awaiting Confirmation','Confirmed','Rejected') DEFAULT NULL,
  `stock_transfer_reference` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stock_transfer_from_warehouse` int(10) UNSIGNED DEFAULT NULL,
  `stock_transfer_to_warehouse` int(10) UNSIGNED DEFAULT NULL,
  `stock_transfer_total_amount` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `stock_transfer_item_total_discount` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `stock_transfer_grand_total` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `stock_transfer_remarks` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `stock_transfer_reject_note` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stock_transfer_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `ro_stock_transfer`
--
DELIMITER $$
CREATE TRIGGER `stock_delete_after_stock_transfer_delete` BEFORE DELETE ON `ro_stock_transfer` FOR EACH ROW BEGIN
            DELETE FROM ro_product_stock WHERE ro_product_stock.stock_transfer_id = OLD.stock_transfer_id;
    END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stock_update_after_stock_transfer_update` AFTER UPDATE ON `ro_stock_transfer` FOR EACH ROW BEGIN
            if( NEW.is_trash = 1 ) THEN
                UPDATE ro_product_stock SET stock_trashed_on = CURRENT_TIMESTAMP WHERE ro_product_stock.stock_transfer_id = OLD.stock_transfer_id;
            END IF;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ro_tariff_and_charges`
--

CREATE TABLE `ro_tariff_and_charges` (
  `tc_id` int(10) UNSIGNED NOT NULL,
  `tc_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tc_value` varchar(20) NOT NULL,
  `tc_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_transfer_money`
--

CREATE TABLE `ro_transfer_money` (
  `transfer_money_id` int(10) UNSIGNED NOT NULL,
  `transfer_money_date` date NOT NULL,
  `transfer_money_from` int(10) UNSIGNED NOT NULL,
  `transfer_money_to` int(10) UNSIGNED NOT NULL,
  `transfer_money_amount` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `transfer_money_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `transfer_money_made_by` int(10) UNSIGNED DEFAULT NULL,
  `transfer_money_made_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_upazilas`
--

CREATE TABLE `ro_upazilas` (
  `upazila_id` int(4) UNSIGNED NOT NULL,
  `upazila_district_id` int(2) UNSIGNED DEFAULT NULL,
  `upazila_name` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `upazila_bn_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `upazila_url` varchar(50) DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_upazilas`
--

INSERT INTO `ro_upazilas` (`upazila_id`, `upazila_district_id`, `upazila_name`, `upazila_bn_name`, `upazila_url`, `is_trash`) VALUES
(1, 1, 'Amtali', NULL, NULL, 0),
(2, 1, 'Bamna', NULL, NULL, 0),
(3, 1, 'Barguna Sadar', NULL, NULL, 0),
(4, 1, 'Betagi', NULL, NULL, 0),
(5, 1, 'Patharghata', NULL, NULL, 0),
(6, 1, 'Taltali', NULL, NULL, 0),
(7, 2, 'Agailjhara', NULL, NULL, 0),
(8, 2, 'Babuganj', NULL, NULL, 0),
(9, 2, 'Bakerganj', NULL, NULL, 0),
(10, 2, 'Banaripara', NULL, NULL, 0),
(11, 2, 'Barisal Sadar', NULL, NULL, 0),
(12, 2, 'Gournadi', NULL, NULL, 0),
(13, 2, 'Hizla', NULL, NULL, 0),
(14, 2, 'Mehendiganj', NULL, NULL, 0),
(15, 2, 'Muladi', NULL, NULL, 0),
(16, 2, 'Nalchiti', NULL, NULL, 0),
(17, 2, 'Ujirpur', NULL, NULL, 0),
(18, 3, 'Bhola Sadar', NULL, NULL, 0),
(19, 3, 'Borhanuddin', NULL, NULL, 0),
(20, 3, 'Charfassion', NULL, NULL, 0),
(21, 3, 'Daulatkhan', NULL, NULL, 0),
(22, 3, 'Lalmohan', NULL, NULL, 0),
(23, 3, 'Manpura', NULL, NULL, 0),
(24, 3, 'Tajumuddin', NULL, NULL, 0),
(25, 4, 'Jhalkhati Sadar', NULL, NULL, 0),
(26, 4, 'Kathalia', NULL, NULL, 0),
(27, 4, 'Nalchiti', NULL, NULL, 0),
(28, 4, 'Rajapur', NULL, NULL, 0),
(29, 5, 'Bauphal', NULL, NULL, 0),
(30, 5, 'Dashmina', NULL, NULL, 0),
(31, 5, 'Dumki', NULL, NULL, 0),
(32, 5, 'Galachipa', NULL, NULL, 0),
(33, 5, 'Kalapara', NULL, NULL, 0),
(34, 5, 'Mirjaganj', NULL, NULL, 0),
(35, 5, 'Patuakhali Sadar', NULL, NULL, 0),
(36, 5, 'Rangabali', NULL, NULL, 0),
(37, 6, 'Bhandaria', NULL, NULL, 0),
(38, 6, 'Kaukhali', NULL, NULL, 0),
(39, 6, 'Mathbaria', NULL, NULL, 0),
(40, 6, 'Najirpur', NULL, NULL, 0),
(41, 6, 'Pirojpur Sadar', NULL, NULL, 0),
(42, 6, 'Swarupkati', NULL, NULL, 0),
(43, 6, 'Zianagar', NULL, NULL, 0),
(44, 7, 'Ali Kadam', NULL, NULL, 0),
(45, 7, 'Bandarban Sadar', NULL, NULL, 0),
(46, 7, 'Lama', NULL, NULL, 0),
(47, 7, 'Naikkhang Chhari', NULL, NULL, 0),
(48, 7, 'Ramu', NULL, NULL, 0),
(49, 7, 'Roang Chhari', NULL, NULL, 0),
(50, 7, 'Ruma', NULL, NULL, 0),
(51, 7, 'Thanchi', NULL, NULL, 0),
(52, 8, 'Anwara', NULL, NULL, 0),
(53, 8, 'Bakalia', NULL, NULL, 0),
(54, 8, 'Bandar', NULL, NULL, 0),
(55, 8, 'Banshkhali', NULL, NULL, 0),
(56, 8, 'Boalkhali', NULL, NULL, 0),
(57, 8, 'Chandanaish', NULL, NULL, 0),
(58, 8, 'Chandgaon', NULL, NULL, 0),
(59, 8, 'Chittagong Sadar', NULL, NULL, 0),
(60, 8, 'Double Mooring', NULL, NULL, 0),
(61, 8, 'Fatickchari', NULL, NULL, 0),
(62, 8, 'Hathazari', NULL, NULL, 0),
(63, 8, 'Lohagara', NULL, NULL, 0),
(64, 8, 'Mirsarai', NULL, NULL, 0),
(65, 8, 'Pahartali', NULL, NULL, 0),
(66, 8, 'Panchlaish', NULL, NULL, 0),
(67, 8, 'Patia', NULL, NULL, 0),
(68, 8, 'Rangunia', NULL, NULL, 0),
(69, 8, 'Raojan', NULL, NULL, 0),
(70, 8, 'Satkania', NULL, NULL, 0),
(71, 8, 'Sitakunda', NULL, NULL, 0),
(72, 8, 'Swandip', NULL, NULL, 0),
(73, 9, 'Chakoria', NULL, NULL, 0),
(74, 9, 'Cox\'s Bazar Sadar', NULL, NULL, 0),
(75, 9, 'Kutubdia', NULL, NULL, 0),
(76, 9, 'Maheshkhali', NULL, NULL, 0),
(77, 9, 'Pekua', NULL, NULL, 0),
(78, 9, 'Ramu', NULL, NULL, 0),
(79, 9, 'Teknaf', NULL, NULL, 0),
(80, 9, 'Ukhia', NULL, NULL, 0),
(81, 10, 'Dighinala', NULL, NULL, 0),
(82, 10, 'Khagrachhari Sadar', NULL, NULL, 0),
(83, 10, 'Laxmi Chhari', NULL, NULL, 0),
(84, 10, 'Mahalchhari', NULL, NULL, 0),
(85, 10, 'Manik Chhari', NULL, NULL, 0),
(86, 10, 'Matiranga', NULL, NULL, 0),
(87, 10, 'Panchhari', NULL, NULL, 0),
(88, 10, 'Ramgarh', NULL, NULL, 0),
(89, 11, 'Baghaichhari', NULL, NULL, 0),
(90, 11, 'Barkal', NULL, NULL, 0),
(91, 11, 'Bilaichhari', NULL, NULL, 0),
(92, 11, 'Jurachhari', NULL, NULL, 0),
(93, 11, 'Kaptai', NULL, NULL, 0),
(94, 11, 'Kawkhali', NULL, NULL, 0),
(95, 11, 'Langodu', NULL, NULL, 0),
(96, 11, 'Naniar Char', NULL, NULL, 0),
(97, 11, 'Rajasthali', NULL, NULL, 0),
(98, 11, 'Rangamati Sadar', NULL, NULL, 0),
(99, 12, 'Akhaura', NULL, NULL, 0),
(100, 12, 'Ashuganj', NULL, NULL, 0),
(101, 12, 'Banchharampur', NULL, NULL, 0),
(102, 12, 'Bijoynagar', NULL, NULL, 0),
(103, 12, 'Brahmanbaria Sadar', NULL, NULL, 0),
(104, 12, 'Kasba', NULL, NULL, 0),
(105, 12, 'Nabinagar', NULL, NULL, 0),
(106, 12, 'Nasirnagar', NULL, NULL, 0),
(107, 12, 'Sarail', NULL, NULL, 0),
(108, 13, 'Chandpur Sadar', NULL, NULL, 0),
(109, 13, 'Faridganj', NULL, NULL, 0),
(110, 13, 'Haimchar', NULL, NULL, 0),
(111, 13, 'Haziganj', NULL, NULL, 0),
(112, 13, 'Kachua', NULL, NULL, 0),
(113, 13, 'Matlab North', NULL, NULL, 0),
(114, 13, 'Matlab South', NULL, NULL, 0),
(115, 13, 'Shahrasti', NULL, NULL, 0),
(116, 14, 'Barura', NULL, NULL, 0),
(117, 14, 'Brahmanpara', NULL, NULL, 0),
(118, 14, 'Burichong', NULL, NULL, 0),
(119, 14, 'Chandina', NULL, NULL, 0),
(120, 14, 'Chouddagram', NULL, NULL, 0),
(121, 14, 'Comilla Adarsha Sadar', NULL, NULL, 0),
(122, 14, 'Comilla Sadar (south)', NULL, NULL, 0),
(123, 14, 'Daudkandi', NULL, NULL, 0),
(124, 14, 'Debidwar', NULL, NULL, 0),
(125, 14, 'Homna', NULL, NULL, 0),
(126, 14, 'Laksam', NULL, NULL, 0),
(127, 14, 'Manoharganj', NULL, NULL, 0),
(128, 14, 'Meghna', NULL, NULL, 0),
(129, 14, 'Muradnagar', NULL, NULL, 0),
(130, 14, 'Nangalkot', NULL, NULL, 0),
(131, 14, 'Titash', NULL, NULL, 0),
(132, 15, 'Chhagalnaiya', NULL, NULL, 0),
(133, 15, 'Dagonbhuiyan', NULL, NULL, 0),
(134, 15, 'Feni Sadar', NULL, NULL, 0),
(135, 15, 'Fulgazi', NULL, NULL, 0),
(136, 15, 'Porshuram', NULL, NULL, 0),
(137, 15, 'Sonagazi', NULL, NULL, 0),
(138, 16, 'Kamalnagar', NULL, NULL, 0),
(139, 16, 'Laxmipur Sadar', NULL, NULL, 0),
(140, 16, 'Ramganj', NULL, NULL, 0),
(141, 16, 'Ramgati', NULL, NULL, 0),
(142, 16, 'Roypur', NULL, NULL, 0),
(143, 17, 'Begumganj', NULL, NULL, 0),
(144, 17, 'Chatkhil', NULL, NULL, 0),
(145, 17, 'Companyganj', NULL, NULL, 0),
(146, 17, 'Hatia', NULL, NULL, 0),
(147, 17, 'Kabirhat', NULL, NULL, 0),
(148, 17, 'Noakhali Sadar', NULL, NULL, 0),
(149, 17, 'Senbag', NULL, NULL, 0),
(150, 17, 'Sonaimuri', NULL, NULL, 0),
(151, 17, 'Subarnachar', NULL, NULL, 0),
(152, 18, 'Dhamrai', NULL, NULL, 0),
(153, 18, 'Dohar', NULL, NULL, 0),
(154, 18, 'Keraniganj', NULL, NULL, 0),
(155, 18, 'Nawabganj', NULL, NULL, 0),
(156, 18, 'Savar', NULL, NULL, 0),
(157, 19, 'Adabar', NULL, NULL, 0),
(158, 19, 'Badda', NULL, NULL, 0),
(159, 19, 'Bimanbandar', NULL, NULL, 0),
(160, 19, 'Dakshinkhan', NULL, NULL, 0),
(161, 19, 'Darus Salam', NULL, NULL, 0),
(162, 19, 'Demra', NULL, NULL, 0),
(163, 19, 'Dhaka Cant.', NULL, NULL, 0),
(164, 19, 'Dhanmondi', NULL, NULL, 0),
(165, 19, 'Gulshan', NULL, NULL, 0),
(166, 19, 'Hazaribagh', NULL, NULL, 0),
(167, 19, 'Jatrabari', NULL, NULL, 0),
(168, 19, 'Kadamtali', NULL, NULL, 0),
(169, 19, 'Kafrul', NULL, NULL, 0),
(170, 19, 'Kamrangirchar', NULL, NULL, 0),
(171, 19, 'Khilgaon', NULL, NULL, 0),
(172, 19, 'Khilkhet', NULL, NULL, 0),
(173, 19, 'Kotwali', NULL, NULL, 0),
(174, 19, 'Lalbagh', NULL, NULL, 0),
(175, 19, 'Mirpur', NULL, NULL, 0),
(176, 19, 'Mohammadpur', NULL, NULL, 0),
(177, 19, 'Motijheel', NULL, NULL, 0),
(178, 19, 'New Market', NULL, NULL, 0),
(179, 19, 'Pallabi', NULL, NULL, 0),
(180, 19, 'Paltan', NULL, NULL, 0),
(181, 19, 'Ramna', NULL, NULL, 0),
(182, 19, 'Sabujbagh', NULL, NULL, 0),
(183, 19, 'Shah Ali', NULL, NULL, 0),
(184, 19, 'Shahbagh', NULL, NULL, 0),
(185, 19, 'Sher-e-bangla Nagar', NULL, NULL, 0),
(186, 19, 'Shyampur', NULL, NULL, 0),
(187, 19, 'Sutrapur', NULL, NULL, 0),
(188, 19, 'Tejgaon', NULL, NULL, 0),
(189, 19, 'Tejgaon I/a', NULL, NULL, 0),
(190, 19, 'Turag', NULL, NULL, 0),
(191, 19, 'Uttara', NULL, NULL, 0),
(192, 19, 'Uttarkhan', NULL, NULL, 0),
(193, 20, 'Alfadanga', NULL, NULL, 0),
(194, 20, 'Bhanga', NULL, NULL, 0),
(195, 20, 'Boalmari', NULL, NULL, 0),
(196, 20, 'Char Bhadrasan', NULL, NULL, 0),
(197, 20, 'Faridpur Sadar', NULL, NULL, 0),
(198, 20, 'Madhukhali', NULL, NULL, 0),
(199, 20, 'Nagarkanda', NULL, NULL, 0),
(200, 20, 'Sadarpur', NULL, NULL, 0),
(201, 20, 'Saltha', NULL, NULL, 0),
(202, 21, 'Abu Dhabi', NULL, NULL, 0),
(203, 21, 'Doha', NULL, NULL, 0),
(204, 21, 'Jeddah', NULL, NULL, 0),
(205, 21, 'Manama', NULL, NULL, 0),
(206, 21, 'Oman', NULL, NULL, 0),
(207, 21, 'Ras Al-khaimah', NULL, NULL, 0),
(208, 21, 'Riyadh', NULL, NULL, 0),
(209, 21, 'Tripoli', NULL, NULL, 0),
(210, 22, 'Gazipur Sadar', NULL, NULL, 0),
(211, 22, 'Kaliakair', NULL, NULL, 0),
(212, 22, 'Kaliganj', NULL, NULL, 0),
(213, 22, 'Kapasia', NULL, NULL, 0),
(214, 22, 'Sreepur', NULL, NULL, 0),
(215, 22, 'Tongi', NULL, NULL, 0),
(216, 23, 'Gopalganj', NULL, NULL, 0),
(217, 23, 'Kashiani', NULL, NULL, 0),
(218, 23, 'Kotalipara', NULL, NULL, 0),
(219, 23, 'Muksudpur', NULL, NULL, 0),
(220, 23, 'Tungipara', NULL, NULL, 0),
(221, 24, 'Bakshiganj', NULL, NULL, 0),
(222, 24, 'Dewanganj', NULL, NULL, 0),
(223, 24, 'Islampur', NULL, NULL, 0),
(224, 24, 'Jamalpur Sadar', NULL, NULL, 0),
(225, 24, 'Madarganj', NULL, NULL, 0),
(226, 24, 'Melandaha', NULL, NULL, 0),
(227, 24, 'Sarishabari', NULL, NULL, 0),
(228, 25, 'Austagram', NULL, NULL, 0),
(229, 25, 'Bajitpur', NULL, NULL, 0),
(230, 25, 'Bhairab', NULL, NULL, 0),
(231, 25, 'Hossainpur', NULL, NULL, 0),
(232, 25, 'Itna', NULL, NULL, 0),
(233, 25, 'Karimganj', NULL, NULL, 0),
(234, 25, 'Katiadi', NULL, NULL, 0),
(235, 25, 'Kishoreganj Sadar', NULL, NULL, 0),
(236, 25, 'Kuliarchar', NULL, NULL, 0),
(237, 25, 'Mithamoin', NULL, NULL, 0),
(238, 25, 'Nikli', NULL, NULL, 0),
(239, 25, 'Pakundia', NULL, NULL, 0),
(240, 25, 'Tarail', NULL, NULL, 0),
(241, 26, 'Kalkini', NULL, NULL, 0),
(242, 26, 'Madaripur', NULL, NULL, 0),
(243, 26, 'Rajoir', NULL, NULL, 0),
(244, 26, 'Shibchar', NULL, NULL, 0),
(245, 27, 'Doulatpur', NULL, NULL, 0),
(246, 27, 'Ghior', NULL, NULL, 0),
(247, 27, 'Harirampur', NULL, NULL, 0),
(248, 27, 'Manikganj', NULL, NULL, 0),
(249, 27, 'Saturia', NULL, NULL, 0),
(250, 27, 'Shibaloy', NULL, NULL, 0),
(251, 27, 'Singair', NULL, NULL, 0),
(252, 28, 'Gazaria', NULL, NULL, 0),
(253, 28, 'Louhajang', NULL, NULL, 0),
(254, 28, 'Munshiganj', NULL, NULL, 0),
(255, 28, 'Serajdikhan', NULL, NULL, 0),
(256, 28, 'Sreenagar', NULL, NULL, 0),
(257, 28, 'Tongibari', NULL, NULL, 0),
(258, 29, 'Bhaluka', NULL, NULL, 0),
(259, 29, 'Dhobaura', NULL, NULL, 0),
(260, 29, 'Fulbaria', NULL, NULL, 0),
(261, 29, 'Gafargaon', NULL, NULL, 0),
(262, 29, 'Gouripur', NULL, NULL, 0),
(263, 29, 'Haluaghat', NULL, NULL, 0),
(264, 29, 'Ishwarganj', NULL, NULL, 0),
(265, 29, 'Muktagacha', NULL, NULL, 0),
(266, 29, 'Mymensingh', NULL, NULL, 0),
(267, 29, 'Nandail', NULL, NULL, 0),
(268, 29, 'Phulpur', NULL, NULL, 0),
(269, 29, 'Trishal', NULL, NULL, 0),
(270, 30, 'Araihazar', NULL, NULL, 0),
(271, 30, 'Bandar', NULL, NULL, 0),
(272, 30, 'Fatulla', NULL, NULL, 0),
(273, 30, 'Narayanganj', NULL, NULL, 0),
(274, 30, 'Rupganj', NULL, NULL, 0),
(275, 30, 'Siddhirganj', NULL, NULL, 0),
(276, 30, 'Sonargaon', NULL, NULL, 0),
(277, 31, 'Belabo', NULL, NULL, 0),
(278, 31, 'Monohardi', NULL, NULL, 0),
(279, 31, 'Narsingdi', NULL, NULL, 0),
(280, 31, 'Palash', NULL, NULL, 0),
(281, 31, 'Raipura', NULL, NULL, 0),
(282, 31, 'Shibpur', NULL, NULL, 0),
(283, 32, 'Atpara', NULL, NULL, 0),
(284, 32, 'Barhatta', NULL, NULL, 0),
(285, 32, 'Durgapur', NULL, NULL, 0),
(286, 32, 'Kalmakanda', NULL, NULL, 0),
(287, 32, 'Kendua', NULL, NULL, 0),
(288, 32, 'Khaliajuri', NULL, NULL, 0),
(289, 32, 'Madan', NULL, NULL, 0),
(290, 32, 'Mohanganj', NULL, NULL, 0),
(291, 32, 'Netrokona', NULL, NULL, 0),
(292, 32, 'Purbadhala', NULL, NULL, 0),
(293, 33, 'Baliakandi', NULL, NULL, 0),
(294, 33, 'Goalanda', NULL, NULL, 0),
(295, 33, 'Kalukhali', NULL, NULL, 0),
(296, 33, 'Pangsa', NULL, NULL, 0),
(297, 33, 'Rajbari', NULL, NULL, 0),
(298, 34, 'Bhedarganj', NULL, NULL, 0),
(299, 34, 'Damudya', NULL, NULL, 0),
(300, 34, 'Gosairhat', NULL, NULL, 0),
(301, 34, 'Naria', NULL, NULL, 0),
(302, 34, 'Shariatpur', NULL, NULL, 0),
(303, 34, 'Zajira', NULL, NULL, 0),
(304, 35, 'Jhenaigati', NULL, NULL, 0),
(305, 35, 'Nakla', NULL, NULL, 0),
(306, 35, 'Nalitabari', NULL, NULL, 0),
(307, 35, 'Sherpur Sadar', NULL, NULL, 0),
(308, 35, 'Sreebardi', NULL, NULL, 0),
(309, 36, 'Basail', NULL, NULL, 0),
(310, 36, 'Bhuapur', NULL, NULL, 0),
(311, 36, 'Delduar', NULL, NULL, 0),
(312, 36, 'Dhanbari', NULL, NULL, 0),
(313, 36, 'Ghatail', NULL, NULL, 0),
(314, 36, 'Gopalpur', NULL, NULL, 0),
(315, 36, 'Kalihati', NULL, NULL, 0),
(316, 36, 'Madhupur', NULL, NULL, 0),
(317, 36, 'Mirzapur', NULL, NULL, 0),
(318, 36, 'Nagarpur', NULL, NULL, 0),
(319, 36, 'Sakhipur', NULL, NULL, 0),
(320, 36, 'Tangail Sadar', NULL, NULL, 0),
(321, 37, 'Biral', NULL, NULL, 0),
(322, 37, 'Birampur', NULL, NULL, 0),
(323, 37, 'Birganj', NULL, NULL, 0),
(324, 37, 'Bochaganj', NULL, NULL, 0),
(325, 37, 'Chirirbandar', NULL, NULL, 0),
(326, 37, 'Dinajpur Sadar', NULL, NULL, 0),
(327, 37, 'Fulbari', NULL, NULL, 0),
(328, 37, 'Ghoraghat', NULL, NULL, 0),
(329, 37, 'Hakimpur', NULL, NULL, 0),
(330, 37, 'Kaharole', NULL, NULL, 0),
(331, 37, 'Khansama', NULL, NULL, 0),
(332, 37, 'Nawabganj', NULL, NULL, 0),
(333, 37, 'Parbatipur', NULL, NULL, 0),
(334, 38, 'Gaibandha Sadar', NULL, NULL, 0),
(335, 38, 'Gobindaganj', NULL, NULL, 0),
(336, 38, 'Palashbari', NULL, NULL, 0),
(337, 38, 'Phulchhari', NULL, NULL, 0),
(338, 38, 'Sadullapur', NULL, NULL, 0),
(339, 38, 'Saghatta', NULL, NULL, 0),
(340, 38, 'Sundarganj', NULL, NULL, 0),
(341, 39, 'Bhurungamari', NULL, NULL, 0),
(342, 39, 'Chilmari', NULL, NULL, 0),
(343, 39, 'Kurigram Sadar', NULL, NULL, 0),
(344, 39, 'Nageshwari', NULL, NULL, 0),
(345, 39, 'Phulbari', NULL, NULL, 0),
(346, 39, 'Rajarhat', NULL, NULL, 0),
(347, 39, 'Rajibpur', NULL, NULL, 0),
(348, 39, 'Raumari', NULL, NULL, 0),
(349, 39, 'Ulipur', NULL, NULL, 0),
(350, 40, 'Aditmari', NULL, NULL, 0),
(351, 40, 'Hatibandha', NULL, NULL, 0),
(352, 40, 'Kaliganj', NULL, NULL, 0),
(353, 40, 'Lalmonirhat Sadar', NULL, NULL, 0),
(354, 40, 'Patgram', NULL, NULL, 0),
(355, 41, 'Dimla', NULL, NULL, 0),
(356, 41, 'Domar', NULL, NULL, 0),
(357, 41, 'Jaldhaka', NULL, NULL, 0),
(358, 41, 'Kishoreganj', NULL, NULL, 0),
(359, 41, 'Nilphamari Sadar', NULL, NULL, 0),
(360, 41, 'Saidpur', NULL, NULL, 0),
(361, 42, 'Atwari', NULL, NULL, 0),
(362, 42, 'Boda', NULL, NULL, 0),
(363, 42, 'Debiganj', NULL, NULL, 0),
(364, 42, 'Panchagarh Sadar', NULL, NULL, 0),
(365, 42, 'Tetulia', NULL, NULL, 0),
(366, 43, 'Badarganj', NULL, NULL, 0),
(367, 43, 'Gangachara', NULL, NULL, 0),
(368, 43, 'Kaunia', NULL, NULL, 0),
(369, 43, 'Mithapukur', NULL, NULL, 0),
(370, 43, 'Pirgachha', NULL, NULL, 0),
(371, 43, 'Pirganj', NULL, NULL, 0),
(372, 43, 'Rangpur Sadar', NULL, NULL, 0),
(373, 43, 'Taraganj', NULL, NULL, 0),
(374, 44, 'Baliadangi', NULL, NULL, 0),
(375, 44, 'Haripur', NULL, NULL, 0),
(376, 44, 'Pirganj', NULL, NULL, 0),
(377, 44, 'Ranisankail', NULL, NULL, 0),
(378, 44, 'Thakurgaon Sadar', NULL, NULL, 0),
(379, 45, 'Bagerhat Sadar', NULL, NULL, 0),
(380, 45, 'Chitalmari', NULL, NULL, 0),
(381, 45, 'Fakirhat', NULL, NULL, 0),
(382, 45, 'Kachua', NULL, NULL, 0),
(383, 45, 'Mollahat', NULL, NULL, 0),
(384, 45, 'Mongla', NULL, NULL, 0),
(385, 45, 'Morrelganj', NULL, NULL, 0),
(386, 45, 'Rampal', NULL, NULL, 0),
(387, 45, 'Sharonkhola', NULL, NULL, 0),
(388, 46, 'Alamdanga', NULL, NULL, 0),
(389, 46, 'Chuadanga Sadar', NULL, NULL, 0),
(390, 46, 'Damurhuda', NULL, NULL, 0),
(391, 46, 'Jibon Nagar', NULL, NULL, 0),
(392, 47, 'Avoynagar', NULL, NULL, 0),
(393, 47, 'Bagharpara', NULL, NULL, 0),
(394, 47, 'Chowgacha', NULL, NULL, 0),
(395, 47, 'Jessore Sadar', NULL, NULL, 0),
(396, 47, 'Jhikorgacha', NULL, NULL, 0),
(397, 47, 'Keshabpur', NULL, NULL, 0),
(398, 47, 'Monirampur', NULL, NULL, 0),
(399, 47, 'Sarsha', NULL, NULL, 0),
(400, 48, 'Harinakundu', NULL, NULL, 0),
(401, 48, 'Jhenidah Sadar', NULL, NULL, 0),
(402, 48, 'Kaliganj', NULL, NULL, 0),
(403, 48, 'Kotchandpur', NULL, NULL, 0),
(404, 48, 'Maheshpur', NULL, NULL, 0),
(405, 48, 'Shailkupa', NULL, NULL, 0),
(406, 49, 'Batiaghata', NULL, NULL, 0),
(407, 49, 'Dakop', NULL, NULL, 0),
(408, 49, 'Daulatpur', NULL, NULL, 0),
(409, 49, 'Dighalia', NULL, NULL, 0),
(410, 49, 'Dumuria', NULL, NULL, 0),
(411, 49, 'Khalishpur', NULL, NULL, 0),
(412, 49, 'Khan Jahan Ali', NULL, NULL, 0),
(413, 49, 'Khulna Sadar', NULL, NULL, 0),
(414, 49, 'Koyra', NULL, NULL, 0),
(415, 49, 'Paikgacha', NULL, NULL, 0),
(416, 49, 'Phultala', NULL, NULL, 0),
(417, 49, 'Rupsha', NULL, NULL, 0),
(418, 49, 'Sona Danga', NULL, NULL, 0),
(419, 49, 'Terokhada', NULL, NULL, 0),
(420, 50, 'Bheramara', NULL, NULL, 0),
(421, 50, 'Daulatpur', NULL, NULL, 0),
(422, 50, 'Khoksha', NULL, NULL, 0),
(423, 50, 'Kumarkhali', NULL, NULL, 0),
(424, 50, 'Kushtia Sadar', NULL, NULL, 0),
(425, 50, 'Mirpur', NULL, NULL, 0),
(426, 51, 'Magura Sadar', NULL, NULL, 0),
(427, 51, 'Mohammedpur', NULL, NULL, 0),
(428, 51, 'Shalikha', NULL, NULL, 0),
(429, 51, 'Sreepur', NULL, NULL, 0),
(430, 52, 'Gangni', NULL, NULL, 0),
(431, 52, 'Meherpur Sadar', NULL, NULL, 0),
(432, 52, 'Mujib Nagor', NULL, NULL, 0),
(433, 53, 'Kalia', NULL, NULL, 0),
(434, 53, 'Lohagara', NULL, NULL, 0),
(435, 53, 'Narail Sadar', NULL, NULL, 0),
(436, 54, 'Ashasuni', NULL, NULL, 0),
(437, 54, 'Debhata', NULL, NULL, 0),
(438, 54, 'Kalaroa', NULL, NULL, 0),
(439, 54, 'Kaliganj', NULL, NULL, 0),
(440, 54, 'Satkhira Sadar', NULL, NULL, 0),
(441, 54, 'Shaymnagar', NULL, NULL, 0),
(442, 54, 'Tala', NULL, NULL, 0),
(443, 55, 'Adamdighi', NULL, NULL, 0),
(444, 55, 'Bogra Sadar', NULL, NULL, 0),
(445, 55, 'Dhunat', NULL, NULL, 0),
(446, 55, 'Dhupchanchia', NULL, NULL, 0),
(447, 55, 'Gabtali', NULL, NULL, 0),
(448, 55, 'Kahaloo', NULL, NULL, 0),
(449, 55, 'Nandigram', NULL, NULL, 0),
(450, 55, 'Sariakandi', NULL, NULL, 0),
(451, 55, 'Shahjahanpur', NULL, NULL, 0),
(452, 55, 'Sherpur', NULL, NULL, 0),
(453, 55, 'Shibganj', NULL, NULL, 0),
(454, 55, 'Sonatola', NULL, NULL, 0),
(455, 56, 'Akkelpur', NULL, NULL, 0),
(456, 56, 'Joypurhat Sadar', NULL, NULL, 0),
(457, 56, 'Kalai', NULL, NULL, 0),
(458, 56, 'Khetlal', NULL, NULL, 0),
(459, 56, 'Panchbibi', NULL, NULL, 0),
(460, 57, 'Atrai', NULL, NULL, 0),
(461, 57, 'Badalgachhi', NULL, NULL, 0),
(462, 57, 'Dhamoirhat', NULL, NULL, 0),
(463, 57, 'Manda', NULL, NULL, 0),
(464, 57, 'Mohadevpur', NULL, NULL, 0),
(465, 57, 'Naogaon Sadar', NULL, NULL, 0),
(466, 57, 'Niamatpur', NULL, NULL, 0),
(467, 57, 'Patnitala', NULL, NULL, 0),
(468, 57, 'Porsha', NULL, NULL, 0),
(469, 57, 'Raninagar', NULL, NULL, 0),
(470, 57, 'Sapahar', NULL, NULL, 0),
(471, 58, 'Bagatipara', NULL, NULL, 0),
(472, 58, 'Baraigram', NULL, NULL, 0),
(473, 58, 'Gurudaspur', NULL, NULL, 0),
(474, 58, 'Lalpur', NULL, NULL, 0),
(475, 58, 'Naldanga', NULL, NULL, 0),
(476, 58, 'Natore Sadar', NULL, NULL, 0),
(477, 58, 'Singra', NULL, NULL, 0),
(478, 59, 'Bholahat', NULL, NULL, 0),
(479, 59, 'Gomastapur', NULL, NULL, 0),
(480, 59, 'Nachole', NULL, NULL, 0),
(481, 59, 'Nawabganj Sadar', NULL, NULL, 0),
(482, 59, 'Shibganj', NULL, NULL, 0),
(483, 60, 'Atgharia', NULL, NULL, 0),
(484, 60, 'Bera', NULL, NULL, 0),
(485, 60, 'Bhangura', NULL, NULL, 0),
(486, 60, 'Chatmohar', NULL, NULL, 0),
(487, 60, 'Faridpur', NULL, NULL, 0),
(488, 60, 'Ishurdi', NULL, NULL, 0),
(489, 60, 'Pabna Sadar', NULL, NULL, 0),
(490, 60, 'Santhia', NULL, NULL, 0),
(491, 60, 'Sujanagar', NULL, NULL, 0),
(492, 61, 'Bagha', NULL, NULL, 0),
(493, 61, 'Bagmara', NULL, NULL, 0),
(494, 61, 'Boalia', NULL, NULL, 0),
(495, 61, 'Charghat', NULL, NULL, 0),
(496, 61, 'Durgapur', NULL, NULL, 0),
(497, 61, 'Godagari', NULL, NULL, 0),
(498, 61, 'Mohanpur', NULL, NULL, 0),
(499, 61, 'Motihar', NULL, NULL, 0),
(500, 61, 'Paba', NULL, NULL, 0),
(501, 61, 'Puthia', NULL, NULL, 0),
(502, 61, 'Rajpara', NULL, NULL, 0),
(503, 61, 'Shah Makhdum', NULL, NULL, 0),
(504, 61, 'Tanore', NULL, NULL, 0),
(505, 62, 'Belkuchi', NULL, NULL, 0),
(506, 62, 'Chouhali', NULL, NULL, 0),
(507, 62, 'Kamarkhanda', NULL, NULL, 0),
(508, 62, 'Kazipur', NULL, NULL, 0),
(509, 62, 'Raiganj', NULL, NULL, 0),
(510, 62, 'Shahjadpur', NULL, NULL, 0),
(511, 62, 'Sirajganj Sadar', NULL, NULL, 0),
(512, 62, 'Tarash', NULL, NULL, 0),
(513, 62, 'Ullahpara', NULL, NULL, 0),
(514, 63, 'Azmiriganj', NULL, NULL, 0),
(515, 63, 'Bahubal', NULL, NULL, 0),
(516, 63, 'Baniachang', NULL, NULL, 0),
(517, 63, 'Chunarughat', NULL, NULL, 0),
(518, 63, 'Habiganj Sadar', NULL, NULL, 0),
(519, 63, 'Lakhai', NULL, NULL, 0),
(520, 63, 'Madhabpur', NULL, NULL, 0),
(521, 63, 'Nobiganj', NULL, NULL, 0),
(522, 64, 'Baralekha', NULL, NULL, 0),
(523, 64, 'Jury', NULL, NULL, 0),
(524, 64, 'Kamolganj', NULL, NULL, 0),
(525, 64, 'Kulaura', NULL, NULL, 0),
(526, 64, 'Moulvibazar Sadar', NULL, NULL, 0),
(527, 64, 'Rajnagar', NULL, NULL, 0),
(528, 64, 'Sreemangal', NULL, NULL, 0),
(529, 65, 'Bishwambharpur', NULL, NULL, 0),
(530, 65, 'Chhatak', NULL, NULL, 0),
(531, 65, 'Derai', NULL, NULL, 0),
(532, 65, 'Dharma Pasha', NULL, NULL, 0),
(533, 65, 'Doarabazar', NULL, NULL, 0),
(534, 65, 'Jagannathpur', NULL, NULL, 0),
(535, 65, 'Jamalgonj', NULL, NULL, 0),
(536, 65, 'Shulla', NULL, NULL, 0),
(537, 65, 'South Sunamganj', NULL, NULL, 0),
(538, 65, 'Sunamganj Sadar', NULL, NULL, 0),
(539, 65, 'Tahirpur', NULL, NULL, 0),
(540, 66, 'Balaganj', NULL, NULL, 0),
(541, 66, 'Beani Bazar', NULL, NULL, 0),
(542, 66, 'Bishawnath', NULL, NULL, 0),
(543, 66, 'Compani Ganj', NULL, NULL, 0),
(544, 66, 'Fenchugonj', NULL, NULL, 0),
(545, 66, 'Goainghat', NULL, NULL, 0),
(546, 66, 'Golapgonj', NULL, NULL, 0),
(547, 66, 'Jaintapur', NULL, NULL, 0),
(548, 66, 'Kanaighat', NULL, NULL, 0),
(549, 66, 'Osmaninagar', NULL, NULL, 0),
(550, 66, 'South Surma', NULL, NULL, 0),
(551, 66, 'Sylhet Sadar', NULL, NULL, 0),
(552, 66, 'Zakiganj', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_users`
--

CREATE TABLE `ro_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_emp_id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_group_id` int(10) UNSIGNED NOT NULL,
  `user_language` varchar(10) DEFAULT NULL,
  `user_permissions` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_homepage` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `user_pass` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_pass_aaccesskey` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `user_fpass_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `user_fpass_key_expire` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `user_pass_need_update` tinyint(1) DEFAULT NULL,
  `user_last_login_from` varchar(15) DEFAULT NULL,
  `user_status` enum('Active','Lock','Ban') DEFAULT 'Active',
  `user_locked_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_users`
--

INSERT INTO `ro_users` (`user_id`, `user_emp_id`, `user_name`, `user_email`, `user_group_id`, `user_language`, `user_permissions`, `user_homepage`, `user_pass`, `user_pass_aaccesskey`, `user_fpass_key`, `user_fpass_key_expire`, `user_pass_need_update`, `user_last_login_from`, `user_status`, `user_locked_reason`, `is_trash`) VALUES
(1, 1, 'bumsys', 'talk@bumsys.org', 1, '', 'a:1:{i:0;s:10:\"SuperAdmin\";}', NULL, '$2y$10$Rjhbb/QIAlEi5jhqdlDxO.N4yd/Ce/7t/u.G.zxt5c.LlsBYH.Jb.', '972816a80c7e96dee8e8f182689bedb12359bafa', NULL, NULL, NULL, NULL, 'Active', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_users_login_history`
--

CREATE TABLE `ro_users_login_history` (
  `login_id` bigint(20) UNSIGNED NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_users_id` int(10) UNSIGNED NOT NULL,
  `login_ip` varchar(16) DEFAULT NULL,
  `login_user_aggent` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_users_login_history`
--

INSERT INTO `ro_users_login_history` (`login_id`, `login_time`, `login_users_id`, `login_ip`, `login_user_aggent`) VALUES
(1, '2023-04-25 13:51:09', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36'),
(2, '2023-04-25 13:56:15', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36'),
(3, '2023-04-25 13:56:39', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Table structure for table `ro_user_group`
--

CREATE TABLE `ro_user_group` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `group_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `group_permission` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_user_group`
--

INSERT INTO `ro_user_group` (`group_id`, `group_name`, `group_permission`, `is_trash`) VALUES
(1, 'Super Admin', 'a:1:{i:0;s:10:\"SuperAdmin\";}', 0),
(2, 'Accountant', 'a:40:{i:0;s:17:&quot;accounts/transfer&quot;;i:1;s:14:&quot;expenses/bills&quot;;i:2;s:35:&quot;settings/departments/new-department&quot;;i:3;s:20:&quot;products/new-product&quot;;i:4;s:22:&quot;settings/shop/new-shop&quot;;i:5;s:21:&quot;products/new-purchase&quot;;i:6;s:17:&quot;expenses/salaries&quot;;i:7;s:27:&quot;peoples/billers/biller-list&quot;;i:8;s:30:&quot;peoples/companies/company-list&quot;;i:9;s:22:&quot;reports/company-report&quot;;i:10;s:23:&quot;reports/customer-report&quot;;i:11;s:26:&quot;reports/customer-statement&quot;;i:12;s:31:&quot;peoples/customers/customer-list&quot;;i:13;s:23:&quot;reports/employee-report&quot;;i:14;s:23:&quot;employees/employee-list&quot;;i:15;s:20:&quot;accounts/new-account&quot;;i:16;s:26:&quot;peoples/billers/new-biller&quot;;i:17;s:29:&quot;peoples/companies/new-company&quot;;i:18;s:30:&quot;peoples/customers/new-customer&quot;;i:19;s:30:&quot;peoples/employees/new-employee&quot;;i:20;s:23:&quot;expenses/advance-salary&quot;;i:21;s:22:&quot;reports/product-report&quot;;i:22;s:30:&quot;sales/sales-advance-collection&quot;;i:23;s:29:&quot;sales/sales-received-payments&quot;;i:24;s:14:&quot;sales/pos-sale&quot;;i:25;s:18:&quot;sales/sales-return&quot;;i:26;s:21:&quot;accounts/account-list&quot;;i:27;s:17:&quot;accounts/overview&quot;;i:28;s:36:&quot;settings/departments/department-list&quot;;i:29;s:26:&quot;products/product-purchases&quot;;i:30;s:23:&quot;settings/shop/shop-list&quot;;i:31;s:27:&quot;expenses/payment-categories&quot;;i:32;s:17:&quot;expenses/payments&quot;;i:33;s:27:&quot;products/product-categories&quot;;i:34;s:26:&quot;products/product-warehouse&quot;;i:35;s:19:&quot;settings/item-units&quot;;i:36;s:21:&quot;products/product-list&quot;;i:37;s:13:&quot;expenses/loan&quot;;i:38;s:16:&quot;accounts/capital&quot;;i:39;s:16:&quot;accounts/incomes&quot;;}', 0),
(3, 'Shop Manager', 'a:24:{i:0;s:20:&quot;products/new-product&quot;;i:1;s:21:&quot;products/new-purchase&quot;;i:2;s:30:&quot;peoples/companies/company-list&quot;;i:3;s:23:&quot;reports/customer-report&quot;;i:4;s:26:&quot;reports/customer-statement&quot;;i:5;s:31:&quot;peoples/customers/customer-list&quot;;i:6;s:13:&quot;invoice-print&quot;;i:7;s:17:&quot;my-shop/discounts&quot;;i:8;s:18:&quot;my-shop/new-return&quot;;i:9;s:21:&quot;my-shop/shop-expenses&quot;;i:10;s:16:&quot;my-shop/pos-sale&quot;;i:11;s:28:&quot;my-shop/shop-product-returns&quot;;i:12;s:25:&quot;my-shop/received-payments&quot;;i:13;s:29:&quot;peoples/companies/new-company&quot;;i:14;s:30:&quot;peoples/customers/new-customer&quot;;i:15;s:3:&quot;pos&quot;;i:16;s:22:&quot;reports/product-report&quot;;i:17;s:31:&quot;my-shop/shop-advance-collection&quot;;i:18;s:21:&quot;my-shop/shop-overview&quot;;i:19;s:26:&quot;products/product-purchases&quot;;i:20;s:27:&quot;products/product-categories&quot;;i:21;s:26:&quot;products/product-warehouse&quot;;i:22;s:19:&quot;settings/item-units&quot;;i:23;s:21:&quot;products/product-list&quot;;}', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_voice_message`
--

CREATE TABLE `ro_voice_message` (
  `vm_id` int(10) UNSIGNED NOT NULL,
  `vm_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `vm_record` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `vm_status` enum('sending','completed','deactive') DEFAULT NULL,
  `vm_added_on` datetime DEFAULT NULL,
  `vm_added_by` int(10) UNSIGNED DEFAULT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_warehouses`
--

CREATE TABLE `ro_warehouses` (
  `warehouse_id` int(10) UNSIGNED NOT NULL,
  `warehouse_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `warehouse_shop` int(10) UNSIGNED DEFAULT NULL,
  `warehouse_contacts` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `warehouse_location` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ro_warehouses`
--

INSERT INTO `ro_warehouses` (`warehouse_id`, `warehouse_name`, `warehouse_shop`, `warehouse_contacts`, `warehouse_location`, `is_trash`) VALUES
(1, 'Warehouse-1', 1, '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ro_wastage_sale`
--

CREATE TABLE `ro_wastage_sale` (
  `wastage_sale_id` int(10) UNSIGNED NOT NULL,
  `wastage_sale_date` date NOT NULL,
  `wastage_sale_reference` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `wastage_sale_customer` int(10) UNSIGNED NOT NULL,
  `wastage_sale_total_amount` decimal(60,11) NOT NULL DEFAULT 0.00000000000,
  `wastage_sale_discount` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `wastage_sale_tariff_charges` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `wastage_sale_tariff_charges_details` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `wastage_sale_grand_total` decimal(60,11) NOT NULL DEFAULT 0.00000000000,
  `wastage_sale_paid_amount` decimal(60,11) NOT NULL DEFAULT 0.00000000000,
  `wastage_sale_due_amount` decimal(60,11) NOT NULL DEFAULT 0.00000000000,
  `wastage_sale_paying_by` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `wastage_sale_attachment` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `wastage_sale_created_by` int(10) UNSIGNED DEFAULT NULL,
  `wastage_sale_note` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `wastage_sale_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ro_wastage_sale_items`
--

CREATE TABLE `ro_wastage_sale_items` (
  `wastage_sale_items_id` int(10) UNSIGNED NOT NULL,
  `wastage_sale_id` int(10) UNSIGNED NOT NULL,
  `wastage_sale_items_details` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `wastage_sale_items_price` decimal(35,11) NOT NULL DEFAULT 0.00000000000,
  `wastage_sale_items_qnt` decimal(20,4) DEFAULT NULL,
  `wastage_sale_items_subtotal` decimal(65,11) NOT NULL DEFAULT 0.00000000000,
  `wastage_sale_items_add_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_trash` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_dimension`
--

CREATE TABLE `time_dimension` (
  `db_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_dimension`
--

INSERT INTO `time_dimension` (`db_date`) VALUES
('2022-01-01'),
('2022-01-02'),
('2022-01-03'),
('2022-01-04'),
('2022-01-05'),
('2022-01-06'),
('2022-01-07'),
('2022-01-08'),
('2022-01-09'),
('2022-01-10'),
('2022-01-11'),
('2022-01-12'),
('2022-01-13'),
('2022-01-14'),
('2022-01-15'),
('2022-01-16'),
('2022-01-17'),
('2022-01-18'),
('2022-01-19'),
('2022-01-20'),
('2022-01-21'),
('2022-01-22'),
('2022-01-23'),
('2022-01-24'),
('2022-01-25'),
('2022-01-26'),
('2022-01-27'),
('2022-01-28'),
('2022-01-29'),
('2022-01-30'),
('2022-01-31'),
('2022-02-01'),
('2022-02-02'),
('2022-02-03'),
('2022-02-04'),
('2022-02-05'),
('2022-02-06'),
('2022-02-07'),
('2022-02-08'),
('2022-02-09'),
('2022-02-10'),
('2022-02-11'),
('2022-02-12'),
('2022-02-13'),
('2022-02-14'),
('2022-02-15'),
('2022-02-16'),
('2022-02-17'),
('2022-02-18'),
('2022-02-19'),
('2022-02-20'),
('2022-02-21'),
('2022-02-22'),
('2022-02-23'),
('2022-02-24'),
('2022-02-25'),
('2022-02-26'),
('2022-02-27'),
('2022-02-28'),
('2022-03-01'),
('2022-03-02'),
('2022-03-03'),
('2022-03-04'),
('2022-03-05'),
('2022-03-06'),
('2022-03-07'),
('2022-03-08'),
('2022-03-09'),
('2022-03-10'),
('2022-03-11'),
('2022-03-12'),
('2022-03-13'),
('2022-03-14'),
('2022-03-15'),
('2022-03-16'),
('2022-03-17'),
('2022-03-18'),
('2022-03-19'),
('2022-03-20'),
('2022-03-21'),
('2022-03-22'),
('2022-03-23'),
('2022-03-24'),
('2022-03-25'),
('2022-03-26'),
('2022-03-27'),
('2022-03-28'),
('2022-03-29'),
('2022-03-30'),
('2022-03-31'),
('2022-04-01'),
('2022-04-02'),
('2022-04-03'),
('2022-04-04'),
('2022-04-05'),
('2022-04-06'),
('2022-04-07'),
('2022-04-08'),
('2022-04-09'),
('2022-04-10'),
('2022-04-11'),
('2022-04-12'),
('2022-04-13'),
('2022-04-14'),
('2022-04-15'),
('2022-04-16'),
('2022-04-17'),
('2022-04-18'),
('2022-04-19'),
('2022-04-20'),
('2022-04-21'),
('2022-04-22'),
('2022-04-23'),
('2022-04-24'),
('2022-04-25'),
('2022-04-26'),
('2022-04-27'),
('2022-04-28'),
('2022-04-29'),
('2022-04-30'),
('2022-05-01'),
('2022-05-02'),
('2022-05-03'),
('2022-05-04'),
('2022-05-05'),
('2022-05-06'),
('2022-05-07'),
('2022-05-08'),
('2022-05-09'),
('2022-05-10'),
('2022-05-11'),
('2022-05-12'),
('2022-05-13'),
('2022-05-14'),
('2022-05-15'),
('2022-05-16'),
('2022-05-17'),
('2022-05-18'),
('2022-05-19'),
('2022-05-20'),
('2022-05-21'),
('2022-05-22'),
('2022-05-23'),
('2022-05-24'),
('2022-05-25'),
('2022-05-26'),
('2022-05-27'),
('2022-05-28'),
('2022-05-29'),
('2022-05-30'),
('2022-05-31'),
('2022-06-01'),
('2022-06-02'),
('2022-06-03'),
('2022-06-04'),
('2022-06-05'),
('2022-06-06'),
('2022-06-07'),
('2022-06-08'),
('2022-06-09'),
('2022-06-10'),
('2022-06-11'),
('2022-06-12'),
('2022-06-13'),
('2022-06-14'),
('2022-06-15'),
('2022-06-16'),
('2022-06-17'),
('2022-06-18'),
('2022-06-19'),
('2022-06-20'),
('2022-06-21'),
('2022-06-22'),
('2022-06-23'),
('2022-06-24'),
('2022-06-25'),
('2022-06-26'),
('2022-06-27'),
('2022-06-28'),
('2022-06-29'),
('2022-06-30'),
('2022-07-01'),
('2022-07-02'),
('2022-07-03'),
('2022-07-04'),
('2022-07-05'),
('2022-07-06'),
('2022-07-07'),
('2022-07-08'),
('2022-07-09'),
('2022-07-10'),
('2022-07-11'),
('2022-07-12'),
('2022-07-13'),
('2022-07-14'),
('2022-07-15'),
('2022-07-16'),
('2022-07-17'),
('2022-07-18'),
('2022-07-19'),
('2022-07-20'),
('2022-07-21'),
('2022-07-22'),
('2022-07-23'),
('2022-07-24'),
('2022-07-25'),
('2022-07-26'),
('2022-07-27'),
('2022-07-28'),
('2022-07-29'),
('2022-07-30'),
('2022-07-31'),
('2022-08-01'),
('2022-08-02'),
('2022-08-03'),
('2022-08-04'),
('2022-08-05'),
('2022-08-06'),
('2022-08-07'),
('2022-08-08'),
('2022-08-09'),
('2022-08-10'),
('2022-08-11'),
('2022-08-12'),
('2022-08-13'),
('2022-08-14'),
('2022-08-15'),
('2022-08-16'),
('2022-08-17'),
('2022-08-18'),
('2022-08-19'),
('2022-08-20'),
('2022-08-21'),
('2022-08-22'),
('2022-08-23'),
('2022-08-24'),
('2022-08-25'),
('2022-08-26'),
('2022-08-27'),
('2022-08-28'),
('2022-08-29'),
('2022-08-30'),
('2022-08-31'),
('2022-09-01'),
('2022-09-02'),
('2022-09-03'),
('2022-09-04'),
('2022-09-05'),
('2022-09-06'),
('2022-09-07'),
('2022-09-08'),
('2022-09-09'),
('2022-09-10'),
('2022-09-11'),
('2022-09-12'),
('2022-09-13'),
('2022-09-14'),
('2022-09-15'),
('2022-09-16'),
('2022-09-17'),
('2022-09-18'),
('2022-09-19'),
('2022-09-20'),
('2022-09-21'),
('2022-09-22'),
('2022-09-23'),
('2022-09-24'),
('2022-09-25'),
('2022-09-26'),
('2022-09-27'),
('2022-09-28'),
('2022-09-29'),
('2022-09-30'),
('2022-10-01'),
('2022-10-02'),
('2022-10-03'),
('2022-10-04'),
('2022-10-05'),
('2022-10-06'),
('2022-10-07'),
('2022-10-08'),
('2022-10-09'),
('2022-10-10'),
('2022-10-11'),
('2022-10-12'),
('2022-10-13'),
('2022-10-14'),
('2022-10-15'),
('2022-10-16'),
('2022-10-17'),
('2022-10-18'),
('2022-10-19'),
('2022-10-20'),
('2022-10-21'),
('2022-10-22'),
('2022-10-23'),
('2022-10-24'),
('2022-10-25'),
('2022-10-26'),
('2022-10-27'),
('2022-10-28'),
('2022-10-29'),
('2022-10-30'),
('2022-10-31'),
('2022-11-01'),
('2022-11-02'),
('2022-11-03'),
('2022-11-04'),
('2022-11-05'),
('2022-11-06'),
('2022-11-07'),
('2022-11-08'),
('2022-11-09'),
('2022-11-10'),
('2022-11-11'),
('2022-11-12'),
('2022-11-13'),
('2022-11-14'),
('2022-11-15'),
('2022-11-16'),
('2022-11-17'),
('2022-11-18'),
('2022-11-19'),
('2022-11-20'),
('2022-11-21'),
('2022-11-22'),
('2022-11-23'),
('2022-11-24'),
('2022-11-25'),
('2022-11-26'),
('2022-11-27'),
('2022-11-28'),
('2022-11-29'),
('2022-11-30'),
('2022-12-01'),
('2022-12-02'),
('2022-12-03'),
('2022-12-04'),
('2022-12-05'),
('2022-12-06'),
('2022-12-07'),
('2022-12-08'),
('2022-12-09'),
('2022-12-10'),
('2022-12-11'),
('2022-12-12'),
('2022-12-13'),
('2022-12-14'),
('2022-12-15'),
('2022-12-16'),
('2022-12-17'),
('2022-12-18'),
('2022-12-19'),
('2022-12-20'),
('2022-12-21'),
('2022-12-22'),
('2022-12-23'),
('2022-12-24'),
('2022-12-25'),
('2022-12-26'),
('2022-12-27'),
('2022-12-28'),
('2022-12-29'),
('2022-12-30'),
('2022-12-31'),
('2023-01-01'),
('2023-01-02'),
('2023-01-03'),
('2023-01-04'),
('2023-01-05'),
('2023-01-06'),
('2023-01-07'),
('2023-01-08'),
('2023-01-09'),
('2023-01-10'),
('2023-01-11'),
('2023-01-12'),
('2023-01-13'),
('2023-01-14'),
('2023-01-15'),
('2023-01-16'),
('2023-01-17'),
('2023-01-18'),
('2023-01-19'),
('2023-01-20'),
('2023-01-21'),
('2023-01-22'),
('2023-01-23'),
('2023-01-24'),
('2023-01-25'),
('2023-01-26'),
('2023-01-27'),
('2023-01-28'),
('2023-01-29'),
('2023-01-30'),
('2023-01-31'),
('2023-02-01'),
('2023-02-02'),
('2023-02-03'),
('2023-02-04'),
('2023-02-05'),
('2023-02-06'),
('2023-02-07'),
('2023-02-08'),
('2023-02-09'),
('2023-02-10'),
('2023-02-11'),
('2023-02-12'),
('2023-02-13'),
('2023-02-14'),
('2023-02-15'),
('2023-02-16'),
('2023-02-17'),
('2023-02-18'),
('2023-02-19'),
('2023-02-20'),
('2023-02-21'),
('2023-02-22'),
('2023-02-23'),
('2023-02-24'),
('2023-02-25'),
('2023-02-26'),
('2023-02-27'),
('2023-02-28'),
('2023-03-01'),
('2023-03-02'),
('2023-03-03'),
('2023-03-04'),
('2023-03-05'),
('2023-03-06'),
('2023-03-07'),
('2023-03-08'),
('2023-03-09'),
('2023-03-10'),
('2023-03-11'),
('2023-03-12'),
('2023-03-13'),
('2023-03-14'),
('2023-03-15'),
('2023-03-16'),
('2023-03-17'),
('2023-03-18'),
('2023-03-19'),
('2023-03-20'),
('2023-03-21'),
('2023-03-22'),
('2023-03-23'),
('2023-03-24'),
('2023-03-25'),
('2023-03-26'),
('2023-03-27'),
('2023-03-28'),
('2023-03-29'),
('2023-03-30'),
('2023-03-31'),
('2023-04-01'),
('2023-04-02'),
('2023-04-03'),
('2023-04-04'),
('2023-04-05'),
('2023-04-06'),
('2023-04-07'),
('2023-04-08'),
('2023-04-09'),
('2023-04-10'),
('2023-04-11'),
('2023-04-12'),
('2023-04-13'),
('2023-04-14'),
('2023-04-15'),
('2023-04-16'),
('2023-04-17'),
('2023-04-18'),
('2023-04-19'),
('2023-04-20'),
('2023-04-21'),
('2023-04-22'),
('2023-04-23'),
('2023-04-24'),
('2023-04-25'),
('2023-04-26'),
('2023-04-27'),
('2023-04-28'),
('2023-04-29'),
('2023-04-30'),
('2023-05-01'),
('2023-05-02'),
('2023-05-03'),
('2023-05-04'),
('2023-05-05'),
('2023-05-06'),
('2023-05-07'),
('2023-05-08'),
('2023-05-09'),
('2023-05-10'),
('2023-05-11'),
('2023-05-12'),
('2023-05-13'),
('2023-05-14'),
('2023-05-15'),
('2023-05-16'),
('2023-05-17'),
('2023-05-18'),
('2023-05-19'),
('2023-05-20'),
('2023-05-21'),
('2023-05-22'),
('2023-05-23'),
('2023-05-24'),
('2023-05-25'),
('2023-05-26'),
('2023-05-27'),
('2023-05-28'),
('2023-05-29'),
('2023-05-30'),
('2023-05-31'),
('2023-06-01'),
('2023-06-02'),
('2023-06-03'),
('2023-06-04'),
('2023-06-05'),
('2023-06-06'),
('2023-06-07'),
('2023-06-08'),
('2023-06-09'),
('2023-06-10'),
('2023-06-11'),
('2023-06-12'),
('2023-06-13'),
('2023-06-14'),
('2023-06-15'),
('2023-06-16'),
('2023-06-17'),
('2023-06-18'),
('2023-06-19'),
('2023-06-20'),
('2023-06-21'),
('2023-06-22'),
('2023-06-23'),
('2023-06-24'),
('2023-06-25'),
('2023-06-26'),
('2023-06-27'),
('2023-06-28'),
('2023-06-29'),
('2023-06-30'),
('2023-07-01'),
('2023-07-02'),
('2023-07-03'),
('2023-07-04'),
('2023-07-05'),
('2023-07-06'),
('2023-07-07'),
('2023-07-08'),
('2023-07-09'),
('2023-07-10'),
('2023-07-11'),
('2023-07-12'),
('2023-07-13'),
('2023-07-14'),
('2023-07-15'),
('2023-07-16'),
('2023-07-17'),
('2023-07-18'),
('2023-07-19'),
('2023-07-20'),
('2023-07-21'),
('2023-07-22'),
('2023-07-23'),
('2023-07-24'),
('2023-07-25'),
('2023-07-26'),
('2023-07-27'),
('2023-07-28'),
('2023-07-29'),
('2023-07-30'),
('2023-07-31'),
('2023-08-01'),
('2023-08-02'),
('2023-08-03'),
('2023-08-04'),
('2023-08-05'),
('2023-08-06'),
('2023-08-07'),
('2023-08-08'),
('2023-08-09'),
('2023-08-10'),
('2023-08-11'),
('2023-08-12'),
('2023-08-13'),
('2023-08-14'),
('2023-08-15'),
('2023-08-16'),
('2023-08-17'),
('2023-08-18'),
('2023-08-19'),
('2023-08-20'),
('2023-08-21'),
('2023-08-22'),
('2023-08-23'),
('2023-08-24'),
('2023-08-25'),
('2023-08-26'),
('2023-08-27'),
('2023-08-28'),
('2023-08-29'),
('2023-08-30'),
('2023-08-31'),
('2023-09-01'),
('2023-09-02'),
('2023-09-03'),
('2023-09-04'),
('2023-09-05'),
('2023-09-06'),
('2023-09-07'),
('2023-09-08'),
('2023-09-09'),
('2023-09-10'),
('2023-09-11'),
('2023-09-12'),
('2023-09-13'),
('2023-09-14'),
('2023-09-15'),
('2023-09-16'),
('2023-09-17'),
('2023-09-18'),
('2023-09-19'),
('2023-09-20'),
('2023-09-21'),
('2023-09-22'),
('2023-09-23'),
('2023-09-24'),
('2023-09-25'),
('2023-09-26'),
('2023-09-27'),
('2023-09-28'),
('2023-09-29'),
('2023-09-30'),
('2023-10-01'),
('2023-10-02'),
('2023-10-03'),
('2023-10-04'),
('2023-10-05'),
('2023-10-06'),
('2023-10-07'),
('2023-10-08'),
('2023-10-09'),
('2023-10-10'),
('2023-10-11'),
('2023-10-12'),
('2023-10-13'),
('2023-10-14'),
('2023-10-15'),
('2023-10-16'),
('2023-10-17'),
('2023-10-18'),
('2023-10-19'),
('2023-10-20'),
('2023-10-21'),
('2023-10-22'),
('2023-10-23'),
('2023-10-24'),
('2023-10-25'),
('2023-10-26'),
('2023-10-27'),
('2023-10-28'),
('2023-10-29'),
('2023-10-30'),
('2023-10-31'),
('2023-11-01'),
('2023-11-02'),
('2023-11-03'),
('2023-11-04'),
('2023-11-05'),
('2023-11-06'),
('2023-11-07'),
('2023-11-08'),
('2023-11-09'),
('2023-11-10'),
('2023-11-11'),
('2023-11-12'),
('2023-11-13'),
('2023-11-14'),
('2023-11-15'),
('2023-11-16'),
('2023-11-17'),
('2023-11-18'),
('2023-11-19'),
('2023-11-20'),
('2023-11-21'),
('2023-11-22'),
('2023-11-23'),
('2023-11-24'),
('2023-11-25'),
('2023-11-26'),
('2023-11-27'),
('2023-11-28'),
('2023-11-29'),
('2023-11-30'),
('2023-12-01'),
('2023-12-02'),
('2023-12-03'),
('2023-12-04'),
('2023-12-05'),
('2023-12-06'),
('2023-12-07'),
('2023-12-08'),
('2023-12-09'),
('2023-12-10'),
('2023-12-11'),
('2023-12-12'),
('2023-12-13'),
('2023-12-14'),
('2023-12-15'),
('2023-12-16'),
('2023-12-17'),
('2023-12-18'),
('2023-12-19'),
('2023-12-20'),
('2023-12-21'),
('2023-12-22'),
('2023-12-23'),
('2023-12-24'),
('2023-12-25'),
('2023-12-26'),
('2023-12-27'),
('2023-12-28'),
('2023-12-29'),
('2023-12-30'),
('2023-12-31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product_base_stock`
--
ALTER TABLE `product_base_stock`
  ADD PRIMARY KEY (`pbs_id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`vp_id`,`warehouse`,`batch_id`),
  ADD KEY `vp_id` (`vp_id`),
  ADD KEY `warehouse` (`warehouse`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `ro_accounts`
--
ALTER TABLE `ro_accounts`
  ADD PRIMARY KEY (`accounts_id`),
  ADD UNIQUE KEY `accounts_bank_acc_number` (`accounts_bank_acc_number`),
  ADD KEY `accounts_add_by` (`accounts_add_by`),
  ADD KEY `accounts_update_by` (`accounts_update_by`);

--
-- Indexes for table `ro_advance_payments`
--
ALTER TABLE `ro_advance_payments`
  ADD PRIMARY KEY (`advance_payment_id`),
  ADD KEY `advance_payment_pay_to` (`advance_payment_pay_to`),
  ADD KEY `advance_payment_pay_from` (`advance_payment_pay_from`),
  ADD KEY `advance_payment_pay_by` (`advance_payment_pay_by`);

--
-- Indexes for table `ro_api_secrets`
--
ALTER TABLE `ro_api_secrets`
  ADD PRIMARY KEY (`api_id`),
  ADD KEY `api_shop_id` (`api_shop_id`),
  ADD KEY `api_accounts_id` (`api_accounts_id`),
  ADD KEY `api_warehouse_id` (`api_warehouse_id`);

--
-- Indexes for table `ro_bg_product_items`
--
ALTER TABLE `ro_bg_product_items`
  ADD PRIMARY KEY (`bg_product_id`,`bg_item_product_id`),
  ADD KEY `bgpt_item_product` (`bg_item_product_id`);

--
-- Indexes for table `ro_billers`
--
ALTER TABLE `ro_billers`
  ADD PRIMARY KEY (`biller_user_id`,`biller_shop_id`,`biller_accounts_id`),
  ADD KEY `biller_shop_id` (`biller_shop_id`),
  ADD KEY `biller_accounts_id` (`biller_accounts_id`),
  ADD KEY `biller_warehouse_id` (`biller_warehouse_id`);

--
-- Indexes for table `ro_bills`
--
ALTER TABLE `ro_bills`
  ADD PRIMARY KEY (`bills_id`),
  ADD KEY `bills_id` (`bills_id`,`bills_company_id`,`is_trash`),
  ADD KEY `bills_company_id` (`bills_company_id`),
  ADD KEY `bills_add_by` (`bills_add_by`);

--
-- Indexes for table `ro_bill_items`
--
ALTER TABLE `ro_bill_items`
  ADD PRIMARY KEY (`bill_items_id`),
  ADD KEY `bill_items_bill_id` (`bill_items_bill_id`,`bill_items_company`,`is_trash`),
  ADD KEY `bill_items_category` (`bill_items_category`),
  ADD KEY `bill_items_add_by` (`bill_items_add_by`);

--
-- Indexes for table `ro_calls`
--
ALTER TABLE `ro_calls`
  ADD PRIMARY KEY (`call_id`),
  ADD KEY `client_identity` (`client_identity`),
  ADD KEY `reviewer` (`reviewer`),
  ADD KEY `representative` (`representative`),
  ADD KEY `vm_id` (`vm_id`);

--
-- Indexes for table `ro_capital`
--
ALTER TABLE `ro_capital`
  ADD PRIMARY KEY (`capital_id`),
  ADD KEY `capital_accounts` (`capital_accounts`),
  ADD KEY `capital_add_by` (`capital_add_by`);

--
-- Indexes for table `ro_cases`
--
ALTER TABLE `ro_cases`
  ADD PRIMARY KEY (`case_id`),
  ADD KEY `case_customer` (`case_customer`),
  ADD KEY `case_person` (`case_person`),
  ADD KEY `case_assigned_to` (`case_assigned_to`),
  ADD KEY `case_belongs_to` (`case_belongs_to`),
  ADD KEY `case_added_by_agent` (`case_added_by_agent`);

--
-- Indexes for table `ro_case_replies`
--
ALTER TABLE `ro_case_replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `reply_case_id` (`reply_case_id`),
  ADD KEY `reply_by_agent` (`reply_by_agent`),
  ADD KEY `reply_by_customer` (`reply_by_customer`);

--
-- Indexes for table `ro_chat_users`
--
ALTER TABLE `ro_chat_users`
  ADD PRIMARY KEY (`chat_id`),
  ADD UNIQUE KEY `chat_user_id` (`chat_user_id`);

--
-- Indexes for table `ro_closings`
--
ALTER TABLE `ro_closings`
  ADD PRIMARY KEY (`closings_id`),
  ADD KEY `closings_customer` (`closings_customer`),
  ADD KEY `closings_add_by` (`closings_add_by`);

--
-- Indexes for table `ro_companies`
--
ALTER TABLE `ro_companies`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `company_add_by` (`company_add_by`),
  ADD KEY `company_update_by` (`company_update_by`);

--
-- Indexes for table `ro_currencies`
--
ALTER TABLE `ro_currencies`
  ADD PRIMARY KEY (`currency_code`);

--
-- Indexes for table `ro_customers`
--
ALTER TABLE `ro_customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `customer_district` (`customer_district`),
  ADD KEY `customer_division` (`customer_division`),
  ADD KEY `customer_add_by` (`customer_add_by`),
  ADD KEY `customer_update_by` (`customer_update_by`),
  ADD KEY `customer_group_id` (`customer_group_id`),
  ADD KEY `customer_upazila` (`customer_upazila`);

--
-- Indexes for table `ro_customers_group`
--
ALTER TABLE `ro_customers_group`
  ADD PRIMARY KEY (`c_group_id`),
  ADD UNIQUE KEY `c_group_name` (`c_group_name`);

--
-- Indexes for table `ro_deleted_data`
--
ALTER TABLE `ro_deleted_data`
  ADD PRIMARY KEY (`deleted_id`),
  ADD KEY `deleted_by` (`deleted_by`);

--
-- Indexes for table `ro_districts`
--
ALTER TABLE `ro_districts`
  ADD PRIMARY KEY (`district_id`),
  ADD KEY `districts_div_id` (`district_division_id`);

--
-- Indexes for table `ro_divisions`
--
ALTER TABLE `ro_divisions`
  ADD PRIMARY KEY (`division_id`);

--
-- Indexes for table `ro_employees`
--
ALTER TABLE `ro_employees`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `emp_PIN` (`emp_PIN`),
  ADD KEY `employees_dep_id` (`emp_department_id`);

--
-- Indexes for table `ro_emp_department`
--
ALTER TABLE `ro_emp_department`
  ADD PRIMARY KEY (`dep_id`),
  ADD UNIQUE KEY `dep_name` (`dep_name`);

--
-- Indexes for table `ro_firewall`
--
ALTER TABLE `ro_firewall`
  ADD PRIMARY KEY (`fw_id`),
  ADD KEY `firewall_creator` (`fw_added_by`);

--
-- Indexes for table `ro_incomes`
--
ALTER TABLE `ro_incomes`
  ADD PRIMARY KEY (`incomes_id`),
  ADD KEY `incomes_accounts_id` (`incomes_accounts_id`),
  ADD KEY `incomes_shop_id` (`incomes_shop_id`),
  ADD KEY `incomes_from` (`incomes_from`),
  ADD KEY `incomes_add_by` (`incomes_add_by`),
  ADD KEY `incomes_update_by` (`incomes_update_by`);

--
-- Indexes for table `ro_institute`
--
ALTER TABLE `ro_institute`
  ADD PRIMARY KEY (`institute_id`),
  ADD KEY `institute_upazila` (`institute_upazila`);

--
-- Indexes for table `ro_journals`
--
ALTER TABLE `ro_journals`
  ADD PRIMARY KEY (`journals_id`),
  ADD KEY `journals_add_by` (`journals_add_by`);

--
-- Indexes for table `ro_journal_records`
--
ALTER TABLE `ro_journal_records`
  ADD PRIMARY KEY (`journal_records_id`),
  ADD KEY `journal_records_journal_id` (`journal_records_journal_id`),
  ADD KEY `journal_records_accounts` (`journal_records_accounts`),
  ADD KEY `journal_records_add_by` (`journal_records_add_by`);

--
-- Indexes for table `ro_loan`
--
ALTER TABLE `ro_loan`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `loan_borrower` (`loan_borrower`),
  ADD KEY `loan_paying_from` (`loan_paying_from`),
  ADD KEY `loan_pay_by` (`loan_pay_by`);

--
-- Indexes for table `ro_loan_installment`
--
ALTER TABLE `ro_loan_installment`
  ADD PRIMARY KEY (`loan_installment_id`),
  ADD KEY `loan_ids` (`loan_ids`),
  ADD KEY `loan_installment_provider` (`loan_installment_provider`),
  ADD KEY `loan_installment_receive_by` (`loan_installment_receive_by`);

--
-- Indexes for table `ro_login_attempts`
--
ALTER TABLE `ro_login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_attempts_user` (`attempt_user_id`);

--
-- Indexes for table `ro_messages`
--
ALTER TABLE `ro_messages`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indexes for table `ro_notes`
--
ALTER TABLE `ro_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `note_created_by` (`note_created_by`);

--
-- Indexes for table `ro_options`
--
ALTER TABLE `ro_options`
  ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_name` (`option_name`);

--
-- Indexes for table `ro_payments`
--
ALTER TABLE `ro_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payment_date_and_is_trash` (`payment_id`,`payment_date`,`is_trash`),
  ADD KEY `payment_accounts` (`payment_id`,`payment_from`),
  ADD KEY `payment_company` (`payment_id`,`payment_to_company`),
  ADD KEY `payment_employee` (`payment_id`,`payment_to_employee`),
  ADD KEY `payment_to_company` (`payment_to_company`),
  ADD KEY `payment_to_employee` (`payment_to_employee`),
  ADD KEY `payment_from` (`payment_from`),
  ADD KEY `payment_made_by` (`payment_made_by`),
  ADD KEY `payment_update_by` (`payment_update_by`),
  ADD KEY `payment_purchase_id` (`payment_purchase_id`);

--
-- Indexes for table `ro_payments_categories`
--
ALTER TABLE `ro_payments_categories`
  ADD PRIMARY KEY (`payment_category_id`),
  ADD UNIQUE KEY `payment_category_name` (`payment_category_name`),
  ADD KEY `payment_category_shop_id` (`payment_category_shop_id`);

--
-- Indexes for table `ro_payments_return`
--
ALTER TABLE `ro_payments_return`
  ADD PRIMARY KEY (`payments_return_id`),
  ADD KEY `payments_return_payment_id` (`payments_return_payment_id`),
  ADD KEY `payments_return_accounts` (`payments_return_accounts`),
  ADD KEY `payments_return_emp_id` (`payments_return_emp_id`),
  ADD KEY `payments_return_company_id` (`payments_return_company_id`),
  ADD KEY `payments_return_customer_id` (`payments_return_customer_id`),
  ADD KEY `payments_return_sales_id` (`payments_return_sales_id`),
  ADD KEY `payments_return_purchase_id` (`payments_return_purchase_id`),
  ADD KEY `payments_return_by` (`payments_return_by`);

--
-- Indexes for table `ro_payment_adjustment`
--
ALTER TABLE `ro_payment_adjustment`
  ADD PRIMARY KEY (`pa_id`),
  ADD KEY `pa_company` (`pa_company`);

--
-- Indexes for table `ro_payment_items`
--
ALTER TABLE `ro_payment_items`
  ADD PRIMARY KEY (`payment_items_id`),
  ADD KEY `payment_items_payments_id` (`payment_items_payments_id`,`payment_items_date`,`is_trash`),
  ADD KEY `payment_items_payments_id_2` (`payment_items_payments_id`,`payment_items_accounts`),
  ADD KEY `payment_items_payments_id_3` (`payment_items_payments_id`,`payment_items_company`),
  ADD KEY `payment_items_payments_id_4` (`payment_items_payments_id`,`payment_items_employee`),
  ADD KEY `payment_items_category_id` (`payment_items_category_id`),
  ADD KEY `payment_items_made_by` (`payment_items_made_by`),
  ADD KEY `payment_itemss_advance_payments_id` (`payment_itemss_advance_payments_id`);

--
-- Indexes for table `ro_persons`
--
ALTER TABLE `ro_persons`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `person_phone` (`person_phone`,`person_type`,`person_email`,`person_student_class`,`person_district`,`leads_source`,`is_trash`),
  ADD KEY `person_institute` (`person_institute`),
  ADD KEY `person_upazila` (`person_upazila`),
  ADD KEY `person_district` (`person_district`),
  ADD KEY `person_division` (`person_division`),
  ADD KEY `leads_collect_by` (`leads_collect_by`),
  ADD KEY `person_add_by` (`person_add_by`),
  ADD KEY `person_update_by` (`person_update_by`);

--
-- Indexes for table `ro_persons_subject`
--
ALTER TABLE `ro_persons_subject`
  ADD PRIMARY KEY (`person_id`,`subject_name`);

--
-- Indexes for table `ro_persons_tag`
--
ALTER TABLE `ro_persons_tag`
  ADD PRIMARY KEY (`person_id`,`tags`);

--
-- Indexes for table `ro_products`
--
ALTER TABLE `ro_products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD KEY `product_name` (`product_name`,`product_type`,`product_parent_id`,`product_edition`,`product_unit`,`product_category_id`,`product_brand_id`,`product_generic`,`is_trash`),
  ADD KEY `products_unit` (`product_unit`),
  ADD KEY `products_category` (`product_category_id`),
  ADD KEY `products_brand` (`product_brand_id`),
  ADD KEY `products_generic` (`product_generic`),
  ADD KEY `products_edition` (`product_edition`),
  ADD KEY `products_add_by` (`product_add_by`),
  ADD KEY `products_update_by` (`product_update_by`);

--
-- Indexes for table `ro_product_attributes`
--
ALTER TABLE `ro_product_attributes`
  ADD PRIMARY KEY (`pa_id`),
  ADD UNIQUE KEY `pa_name` (`pa_name`);

--
-- Indexes for table `ro_product_authors`
--
ALTER TABLE `ro_product_authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `ro_product_author_relations`
--
ALTER TABLE `ro_product_author_relations`
  ADD PRIMARY KEY (`product_id`,`product_author_id`),
  ADD KEY `par_product_author` (`product_author_id`);

--
-- Indexes for table `ro_product_batches`
--
ALTER TABLE `ro_product_batches`
  ADD PRIMARY KEY (`product_id`,`batch_number`),
  ADD KEY `batch_id` (`batch_id`,`product_id`);

--
-- Indexes for table `ro_product_brands`
--
ALTER TABLE `ro_product_brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `brand_name` (`brand_name`);

--
-- Indexes for table `ro_product_category`
--
ALTER TABLE `ro_product_category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`),
  ADD KEY `category_shop_id` (`category_shop_id`);

--
-- Indexes for table `ro_product_editions`
--
ALTER TABLE `ro_product_editions`
  ADD PRIMARY KEY (`edition_id`),
  ADD UNIQUE KEY `edition_name` (`edition_name`);

--
-- Indexes for table `ro_product_generic`
--
ALTER TABLE `ro_product_generic`
  ADD PRIMARY KEY (`generic_id`),
  ADD UNIQUE KEY `generic_name` (`generic_name`);

--
-- Indexes for table `ro_product_meta`
--
ALTER TABLE `ro_product_meta`
  ADD PRIMARY KEY (`product_id`,`meta_key`);

--
-- Indexes for table `ro_product_price`
--
ALTER TABLE `ro_product_price`
  ADD PRIMARY KEY (`product_id`,`shop_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `ro_product_stock`
--
ALTER TABLE `ro_product_stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `stock_type` (`stock_type`,`stock_entry_date`,`stock_sales_id`,`stock_purchase_id`,`stock_transfer_id`,`stock_sc_id`,`stock_employee_id`,`stock_se_id`,`stock_warehouse_id`,`stock_shop_id`,`stock_product_id`),
  ADD KEY `sales_constraint` (`stock_sales_id`,`stock_entry_date`,`stock_warehouse_id`,`stock_shop_id`,`is_trash`),
  ADD KEY `purchase_constraint` (`stock_purchase_id`,`stock_entry_date`,`stock_warehouse_id`,`stock_shop_id`,`is_trash`),
  ADD KEY `stock_transfer` (`stock_transfer_id`,`stock_entry_date`,`is_trash`),
  ADD KEY `stock_speciment_copy` (`stock_sc_id`,`stock_entry_date`,`stock_employee_id`,`stock_warehouse_id`,`is_trash`),
  ADD KEY `stock_entry` (`stock_se_id`,`stock_entry_date`,`stock_warehouse_id`,`stock_shop_id`,`is_trash`),
  ADD KEY `product_batch_number` (`stock_batch_id`,`stock_product_id`),
  ADD KEY `stock_product_id` (`stock_product_id`),
  ADD KEY `stock_warehouse_id` (`stock_warehouse_id`),
  ADD KEY `stock_created_by` (`stock_created_by`);

--
-- Indexes for table `ro_product_units`
--
ALTER TABLE `ro_product_units`
  ADD PRIMARY KEY (`unit_id`),
  ADD UNIQUE KEY `unit_name` (`unit_name`),
  ADD UNIQUE KEY `short_name` (`short_name`);

--
-- Indexes for table `ro_product_variations`
--
ALTER TABLE `ro_product_variations`
  ADD PRIMARY KEY (`pv_id`),
  ADD KEY `product_variations` (`pa_name`);

--
-- Indexes for table `ro_purchases`
--
ALTER TABLE `ro_purchases`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `purchase_id` (`purchase_id`,`purchase_date`,`purchase_warehouse_id`,`purchase_shop_id`,`is_trash`),
  ADD KEY `purchase_shop_id` (`purchase_shop_id`),
  ADD KEY `purchase_created_by` (`purchase_created_by`),
  ADD KEY `purchase_update_by` (`purchase_update_by`),
  ADD KEY `purchase_company_id` (`purchase_company_id`),
  ADD KEY `purchase_warehouse_id` (`purchase_warehouse_id`);

--
-- Indexes for table `ro_received_payments`
--
ALTER TABLE `ro_received_payments`
  ADD PRIMARY KEY (`received_payments_id`),
  ADD KEY `received_payments_shop` (`received_payments_shop`),
  ADD KEY `received_payments_accounts` (`received_payments_accounts`),
  ADD KEY `received_payments_sales_id` (`received_payments_sales_id`),
  ADD KEY `received_payments_from` (`received_payments_from`),
  ADD KEY `received_payments_add_by` (`received_payments_add_by`);

--
-- Indexes for table `ro_salaries`
--
ALTER TABLE `ro_salaries`
  ADD PRIMARY KEY (`salary_id`),
  ADD KEY `salary_emp_id` (`salary_emp_id`),
  ADD KEY `salary_add_by` (`salary_add_by`),
  ADD KEY `salary_update_by` (`salary_update_by`);

--
-- Indexes for table `ro_sales`
--
ALTER TABLE `ro_sales`
  ADD PRIMARY KEY (`sales_id`),
  ADD UNIQUE KEY `sales_reference` (`sales_reference`),
  ADD KEY `sales_id` (`sales_id`,`sales_delivery_date`,`sales_warehouse_id`,`sales_shop_id`,`is_trash`),
  ADD KEY `sales_shop_id` (`sales_shop_id`),
  ADD KEY `sales_created_by` (`sales_created_by`),
  ADD KEY `sales_update_by` (`sales_update_by`),
  ADD KEY `sales_customer_id` (`sales_customer_id`),
  ADD KEY `sales_warehouse_id` (`sales_warehouse_id`);

--
-- Indexes for table `ro_sc_distribution`
--
ALTER TABLE `ro_sc_distribution`
  ADD PRIMARY KEY (`scd_id`),
  ADD KEY `scd_distributor` (`scd_distributor`),
  ADD KEY `scd_person_id` (`scd_person_id`),
  ADD KEY `scd_product_id` (`scd_product_id`),
  ADD KEY `scd_add_by` (`scd_add_by`);

--
-- Indexes for table `ro_shops`
--
ALTER TABLE `ro_shops`
  ADD PRIMARY KEY (`shop_id`);

--
-- Indexes for table `ro_sip_credentials`
--
ALTER TABLE `ro_sip_credentials`
  ADD PRIMARY KEY (`sip_id`),
  ADD KEY `sip_representative` (`sip_representative`),
  ADD KEY `sip_created_by` (`sip_created_by`);

--
-- Indexes for table `ro_sms_sender`
--
ALTER TABLE `ro_sms_sender`
  ADD PRIMARY KEY (`sms_id`),
  ADD KEY `send_by` (`send_by`);

--
-- Indexes for table `ro_sms_template`
--
ALTER TABLE `ro_sms_template`
  ADD PRIMARY KEY (`st_id`),
  ADD KEY `st_created_by` (`st_created_by`);

--
-- Indexes for table `ro_specimen_copies`
--
ALTER TABLE `ro_specimen_copies`
  ADD PRIMARY KEY (`sc_id`),
  ADD KEY `sc_id` (`sc_id`,`sc_date`,`sc_employee_id`,`sc_warehouse_id`,`is_trash`),
  ADD KEY `sc_employee_id` (`sc_employee_id`),
  ADD KEY `sc_warehouse_id` (`sc_warehouse_id`),
  ADD KEY `sc_add_by` (`sc_add_by`);

--
-- Indexes for table `ro_stock_entries`
--
ALTER TABLE `ro_stock_entries`
  ADD PRIMARY KEY (`se_id`),
  ADD KEY `se_id` (`se_id`,`se_date`,`se_warehouse_id`,`se_shop_id`,`is_trash`),
  ADD KEY `se_warehouse_id` (`se_warehouse_id`),
  ADD KEY `se_add_by` (`se_add_by`);

--
-- Indexes for table `ro_stock_transfer`
--
ALTER TABLE `ro_stock_transfer`
  ADD PRIMARY KEY (`stock_transfer_id`),
  ADD KEY `stock_transfer_id` (`stock_transfer_id`,`stock_transfer_date`,`is_trash`),
  ADD KEY `stock_transfer_from_warehouse` (`stock_transfer_from_warehouse`),
  ADD KEY `stock_transfer_to_warehouse` (`stock_transfer_to_warehouse`);

--
-- Indexes for table `ro_tariff_and_charges`
--
ALTER TABLE `ro_tariff_and_charges`
  ADD PRIMARY KEY (`tc_id`);

--
-- Indexes for table `ro_transfer_money`
--
ALTER TABLE `ro_transfer_money`
  ADD PRIMARY KEY (`transfer_money_id`),
  ADD KEY `transfer_money_from` (`transfer_money_from`),
  ADD KEY `transfer_money_to` (`transfer_money_to`),
  ADD KEY `transfer_money_made_by` (`transfer_money_made_by`);

--
-- Indexes for table `ro_upazilas`
--
ALTER TABLE `ro_upazilas`
  ADD PRIMARY KEY (`upazila_id`),
  ADD KEY `upazilas_dist_id` (`upazila_district_id`);

--
-- Indexes for table `ro_users`
--
ALTER TABLE `ro_users`
  ADD PRIMARY KEY (`user_id`,`user_emp_id`,`user_group_id`),
  ADD UNIQUE KEY `user_emp_id` (`user_emp_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD KEY `users_group_id` (`user_group_id`);

--
-- Indexes for table `ro_users_login_history`
--
ALTER TABLE `ro_users_login_history`
  ADD PRIMARY KEY (`login_id`),
  ADD KEY `login_users_id` (`login_users_id`);

--
-- Indexes for table `ro_user_group`
--
ALTER TABLE `ro_user_group`
  ADD PRIMARY KEY (`group_id`),
  ADD UNIQUE KEY `group_name` (`group_name`);

--
-- Indexes for table `ro_voice_message`
--
ALTER TABLE `ro_voice_message`
  ADD PRIMARY KEY (`vm_id`),
  ADD KEY `vm_added_by` (`vm_added_by`);

--
-- Indexes for table `ro_warehouses`
--
ALTER TABLE `ro_warehouses`
  ADD PRIMARY KEY (`warehouse_id`),
  ADD KEY `warehouse_shop` (`warehouse_shop`);

--
-- Indexes for table `ro_wastage_sale`
--
ALTER TABLE `ro_wastage_sale`
  ADD PRIMARY KEY (`wastage_sale_id`),
  ADD KEY `wastage_sale_id` (`wastage_sale_id`,`is_trash`),
  ADD KEY `wastage_sale_customer` (`wastage_sale_customer`),
  ADD KEY `wastage_sale_created_by` (`wastage_sale_created_by`);

--
-- Indexes for table `ro_wastage_sale_items`
--
ALTER TABLE `ro_wastage_sale_items`
  ADD PRIMARY KEY (`wastage_sale_items_id`),
  ADD KEY `wastage_sale_id` (`wastage_sale_id`,`is_trash`);

--
-- Indexes for table `time_dimension`
--
ALTER TABLE `time_dimension`
  ADD PRIMARY KEY (`db_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product_base_stock`
--
ALTER TABLE `product_base_stock`
  MODIFY `pbs_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ro_accounts`
--
ALTER TABLE `ro_accounts`
  MODIFY `accounts_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_advance_payments`
--
ALTER TABLE `ro_advance_payments`
  MODIFY `advance_payment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_api_secrets`
--
ALTER TABLE `ro_api_secrets`
  MODIFY `api_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_bills`
--
ALTER TABLE `ro_bills`
  MODIFY `bills_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_bill_items`
--
ALTER TABLE `ro_bill_items`
  MODIFY `bill_items_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_calls`
--
ALTER TABLE `ro_calls`
  MODIFY `call_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_capital`
--
ALTER TABLE `ro_capital`
  MODIFY `capital_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_cases`
--
ALTER TABLE `ro_cases`
  MODIFY `case_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_case_replies`
--
ALTER TABLE `ro_case_replies`
  MODIFY `reply_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_chat_users`
--
ALTER TABLE `ro_chat_users`
  MODIFY `chat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_closings`
--
ALTER TABLE `ro_closings`
  MODIFY `closings_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_companies`
--
ALTER TABLE `ro_companies`
  MODIFY `company_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_customers`
--
ALTER TABLE `ro_customers`
  MODIFY `customer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_customers_group`
--
ALTER TABLE `ro_customers_group`
  MODIFY `c_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_deleted_data`
--
ALTER TABLE `ro_deleted_data`
  MODIFY `deleted_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_districts`
--
ALTER TABLE `ro_districts`
  MODIFY `district_id` int(2) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `ro_divisions`
--
ALTER TABLE `ro_divisions`
  MODIFY `division_id` tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ro_employees`
--
ALTER TABLE `ro_employees`
  MODIFY `emp_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_emp_department`
--
ALTER TABLE `ro_emp_department`
  MODIFY `dep_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_firewall`
--
ALTER TABLE `ro_firewall`
  MODIFY `fw_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_incomes`
--
ALTER TABLE `ro_incomes`
  MODIFY `incomes_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_institute`
--
ALTER TABLE `ro_institute`
  MODIFY `institute_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_journals`
--
ALTER TABLE `ro_journals`
  MODIFY `journals_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_journal_records`
--
ALTER TABLE `ro_journal_records`
  MODIFY `journal_records_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_loan`
--
ALTER TABLE `ro_loan`
  MODIFY `loan_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_loan_installment`
--
ALTER TABLE `ro_loan_installment`
  MODIFY `loan_installment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_login_attempts`
--
ALTER TABLE `ro_login_attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_messages`
--
ALTER TABLE `ro_messages`
  MODIFY `msg_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_notes`
--
ALTER TABLE `ro_notes`
  MODIFY `note_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_options`
--
ALTER TABLE `ro_options`
  MODIFY `option_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `ro_payments`
--
ALTER TABLE `ro_payments`
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_payments_categories`
--
ALTER TABLE `ro_payments_categories`
  MODIFY `payment_category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_payments_return`
--
ALTER TABLE `ro_payments_return`
  MODIFY `payments_return_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_payment_adjustment`
--
ALTER TABLE `ro_payment_adjustment`
  MODIFY `pa_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_payment_items`
--
ALTER TABLE `ro_payment_items`
  MODIFY `payment_items_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_persons`
--
ALTER TABLE `ro_persons`
  MODIFY `person_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_products`
--
ALTER TABLE `ro_products`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ro_product_attributes`
--
ALTER TABLE `ro_product_attributes`
  MODIFY `pa_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ro_product_authors`
--
ALTER TABLE `ro_product_authors`
  MODIFY `author_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_product_batches`
--
ALTER TABLE `ro_product_batches`
  MODIFY `batch_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_product_brands`
--
ALTER TABLE `ro_product_brands`
  MODIFY `brand_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_product_category`
--
ALTER TABLE `ro_product_category`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_product_editions`
--
ALTER TABLE `ro_product_editions`
  MODIFY `edition_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_product_generic`
--
ALTER TABLE `ro_product_generic`
  MODIFY `generic_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_product_stock`
--
ALTER TABLE `ro_product_stock`
  MODIFY `stock_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ro_product_units`
--
ALTER TABLE `ro_product_units`
  MODIFY `unit_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ro_product_variations`
--
ALTER TABLE `ro_product_variations`
  MODIFY `pv_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ro_purchases`
--
ALTER TABLE `ro_purchases`
  MODIFY `purchase_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_received_payments`
--
ALTER TABLE `ro_received_payments`
  MODIFY `received_payments_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_salaries`
--
ALTER TABLE `ro_salaries`
  MODIFY `salary_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_sales`
--
ALTER TABLE `ro_sales`
  MODIFY `sales_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_sc_distribution`
--
ALTER TABLE `ro_sc_distribution`
  MODIFY `scd_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_shops`
--
ALTER TABLE `ro_shops`
  MODIFY `shop_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_sip_credentials`
--
ALTER TABLE `ro_sip_credentials`
  MODIFY `sip_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_sms_sender`
--
ALTER TABLE `ro_sms_sender`
  MODIFY `sms_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_sms_template`
--
ALTER TABLE `ro_sms_template`
  MODIFY `st_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_specimen_copies`
--
ALTER TABLE `ro_specimen_copies`
  MODIFY `sc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_stock_entries`
--
ALTER TABLE `ro_stock_entries`
  MODIFY `se_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_stock_transfer`
--
ALTER TABLE `ro_stock_transfer`
  MODIFY `stock_transfer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_tariff_and_charges`
--
ALTER TABLE `ro_tariff_and_charges`
  MODIFY `tc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_transfer_money`
--
ALTER TABLE `ro_transfer_money`
  MODIFY `transfer_money_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_upazilas`
--
ALTER TABLE `ro_upazilas`
  MODIFY `upazila_id` int(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=553;

--
-- AUTO_INCREMENT for table `ro_users`
--
ALTER TABLE `ro_users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_users_login_history`
--
ALTER TABLE `ro_users_login_history`
  MODIFY `login_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ro_user_group`
--
ALTER TABLE `ro_user_group`
  MODIFY `group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ro_voice_message`
--
ALTER TABLE `ro_voice_message`
  MODIFY `vm_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_warehouses`
--
ALTER TABLE `ro_warehouses`
  MODIFY `warehouse_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ro_wastage_sale`
--
ALTER TABLE `ro_wastage_sale`
  MODIFY `wastage_sale_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ro_wastage_sale_items`
--
ALTER TABLE `ro_wastage_sale_items`
  MODIFY `wastage_sale_items_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_base_stock`
--
ALTER TABLE `product_base_stock`
  ADD CONSTRAINT `product_base_stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `ro_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_base_stock_ibfk_2` FOREIGN KEY (`vp_id`) REFERENCES `ro_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_base_stock_ibfk_3` FOREIGN KEY (`warehouse`) REFERENCES `ro_warehouses` (`warehouse_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product_base_stock_ibfk_4` FOREIGN KEY (`batch_id`) REFERENCES `ro_product_batches` (`batch_id`);

--
-- Constraints for table `ro_accounts`
--
ALTER TABLE `ro_accounts`
  ADD CONSTRAINT `ro_accounts_ibfk_1` FOREIGN KEY (`accounts_add_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_accounts_ibfk_2` FOREIGN KEY (`accounts_update_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_advance_payments`
--
ALTER TABLE `ro_advance_payments`
  ADD CONSTRAINT `ro_advance_payments_ibfk_1` FOREIGN KEY (`advance_payment_pay_to`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_advance_payments_ibfk_2` FOREIGN KEY (`advance_payment_pay_from`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_advance_payments_ibfk_3` FOREIGN KEY (`advance_payment_pay_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_api_secrets`
--
ALTER TABLE `ro_api_secrets`
  ADD CONSTRAINT `ro_api_secrets_ibfk_1` FOREIGN KEY (`api_shop_id`) REFERENCES `ro_shops` (`shop_id`),
  ADD CONSTRAINT `ro_api_secrets_ibfk_2` FOREIGN KEY (`api_accounts_id`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_api_secrets_ibfk_3` FOREIGN KEY (`api_warehouse_id`) REFERENCES `ro_warehouses` (`warehouse_id`);

--
-- Constraints for table `ro_bg_product_items`
--
ALTER TABLE `ro_bg_product_items`
  ADD CONSTRAINT `bgpt_item_product` FOREIGN KEY (`bg_item_product_id`) REFERENCES `ro_products` (`product_id`),
  ADD CONSTRAINT `bgpt_product` FOREIGN KEY (`bg_product_id`) REFERENCES `ro_products` (`product_id`);

--
-- Constraints for table `ro_billers`
--
ALTER TABLE `ro_billers`
  ADD CONSTRAINT `ro_billers_ibfk_1` FOREIGN KEY (`biller_user_id`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_billers_ibfk_2` FOREIGN KEY (`biller_shop_id`) REFERENCES `ro_shops` (`shop_id`),
  ADD CONSTRAINT `ro_billers_ibfk_3` FOREIGN KEY (`biller_accounts_id`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_billers_ibfk_4` FOREIGN KEY (`biller_warehouse_id`) REFERENCES `ro_warehouses` (`warehouse_id`);

--
-- Constraints for table `ro_bills`
--
ALTER TABLE `ro_bills`
  ADD CONSTRAINT `ro_bills_ibfk_1` FOREIGN KEY (`bills_company_id`) REFERENCES `ro_companies` (`company_id`),
  ADD CONSTRAINT `ro_bills_ibfk_2` FOREIGN KEY (`bills_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_bill_items`
--
ALTER TABLE `ro_bill_items`
  ADD CONSTRAINT `ro_bill_items_ibfk_1` FOREIGN KEY (`bill_items_bill_id`,`bill_items_company`,`is_trash`) REFERENCES `ro_bills` (`bills_id`, `bills_company_id`, `is_trash`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ro_bill_items_ibfk_2` FOREIGN KEY (`bill_items_category`) REFERENCES `ro_payments_categories` (`payment_category_id`),
  ADD CONSTRAINT `ro_bill_items_ibfk_3` FOREIGN KEY (`bill_items_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_calls`
--
ALTER TABLE `ro_calls`
  ADD CONSTRAINT `ro_calls_ibfk_1` FOREIGN KEY (`reviewer`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_calls_ibfk_2` FOREIGN KEY (`representative`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_calls_ibfk_3` FOREIGN KEY (`vm_id`) REFERENCES `ro_voice_message` (`vm_id`);

--
-- Constraints for table `ro_capital`
--
ALTER TABLE `ro_capital`
  ADD CONSTRAINT `ro_capital_ibfk_1` FOREIGN KEY (`capital_accounts`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_capital_ibfk_2` FOREIGN KEY (`capital_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_cases`
--
ALTER TABLE `ro_cases`
  ADD CONSTRAINT `ro_cases_ibfk_1` FOREIGN KEY (`case_customer`) REFERENCES `ro_customers` (`customer_id`),
  ADD CONSTRAINT `ro_cases_ibfk_2` FOREIGN KEY (`case_person`) REFERENCES `ro_persons` (`person_id`),
  ADD CONSTRAINT `ro_cases_ibfk_3` FOREIGN KEY (`case_assigned_to`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_cases_ibfk_4` FOREIGN KEY (`case_belongs_to`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_cases_ibfk_5` FOREIGN KEY (`case_added_by_agent`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_case_replies`
--
ALTER TABLE `ro_case_replies`
  ADD CONSTRAINT `ro_case_replies_ibfk_1` FOREIGN KEY (`reply_case_id`) REFERENCES `ro_cases` (`case_id`),
  ADD CONSTRAINT `ro_case_replies_ibfk_2` FOREIGN KEY (`reply_by_agent`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_case_replies_ibfk_3` FOREIGN KEY (`reply_by_customer`) REFERENCES `ro_customers` (`customer_id`);

--
-- Constraints for table `ro_closings`
--
ALTER TABLE `ro_closings`
  ADD CONSTRAINT `ro_closings_ibfk_1` FOREIGN KEY (`closings_customer`) REFERENCES `ro_customers` (`customer_id`),
  ADD CONSTRAINT `ro_closings_ibfk_2` FOREIGN KEY (`closings_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_companies`
--
ALTER TABLE `ro_companies`
  ADD CONSTRAINT `ro_companies_ibfk_1` FOREIGN KEY (`company_add_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_companies_ibfk_2` FOREIGN KEY (`company_update_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_customers`
--
ALTER TABLE `ro_customers`
  ADD CONSTRAINT `ro_customers_ibfk_1` FOREIGN KEY (`customer_district`) REFERENCES `ro_districts` (`district_id`),
  ADD CONSTRAINT `ro_customers_ibfk_2` FOREIGN KEY (`customer_division`) REFERENCES `ro_divisions` (`division_id`),
  ADD CONSTRAINT `ro_customers_ibfk_3` FOREIGN KEY (`customer_add_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_customers_ibfk_4` FOREIGN KEY (`customer_update_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_customers_ibfk_5` FOREIGN KEY (`customer_group_id`) REFERENCES `ro_customers_group` (`c_group_id`),
  ADD CONSTRAINT `ro_customers_ibfk_6` FOREIGN KEY (`customer_upazila`) REFERENCES `ro_upazilas` (`upazila_id`);

--
-- Constraints for table `ro_deleted_data`
--
ALTER TABLE `ro_deleted_data`
  ADD CONSTRAINT `ro_deleted_data_ibfk_1` FOREIGN KEY (`deleted_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_districts`
--
ALTER TABLE `ro_districts`
  ADD CONSTRAINT `districts_div_id` FOREIGN KEY (`district_division_id`) REFERENCES `ro_divisions` (`division_id`);

--
-- Constraints for table `ro_employees`
--
ALTER TABLE `ro_employees`
  ADD CONSTRAINT `employees_dep_id` FOREIGN KEY (`emp_department_id`) REFERENCES `ro_emp_department` (`dep_id`);

--
-- Constraints for table `ro_firewall`
--
ALTER TABLE `ro_firewall`
  ADD CONSTRAINT `firewall_creator` FOREIGN KEY (`fw_added_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_incomes`
--
ALTER TABLE `ro_incomes`
  ADD CONSTRAINT `ro_incomes_ibfk_1` FOREIGN KEY (`incomes_accounts_id`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_incomes_ibfk_2` FOREIGN KEY (`incomes_shop_id`) REFERENCES `ro_shops` (`shop_id`),
  ADD CONSTRAINT `ro_incomes_ibfk_3` FOREIGN KEY (`incomes_from`) REFERENCES `ro_customers` (`customer_id`),
  ADD CONSTRAINT `ro_incomes_ibfk_4` FOREIGN KEY (`incomes_add_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_incomes_ibfk_5` FOREIGN KEY (`incomes_update_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_institute`
--
ALTER TABLE `ro_institute`
  ADD CONSTRAINT `ro_institute_ibfk_1` FOREIGN KEY (`institute_upazila`) REFERENCES `ro_upazilas` (`upazila_id`);

--
-- Constraints for table `ro_journals`
--
ALTER TABLE `ro_journals`
  ADD CONSTRAINT `ro_journals_ibfk_1` FOREIGN KEY (`journals_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_journal_records`
--
ALTER TABLE `ro_journal_records`
  ADD CONSTRAINT `ro_journal_records_ibfk_1` FOREIGN KEY (`journal_records_journal_id`) REFERENCES `ro_journals` (`journals_id`),
  ADD CONSTRAINT `ro_journal_records_ibfk_2` FOREIGN KEY (`journal_records_accounts`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_journal_records_ibfk_3` FOREIGN KEY (`journal_records_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_loan`
--
ALTER TABLE `ro_loan`
  ADD CONSTRAINT `ro_loan_ibfk_1` FOREIGN KEY (`loan_borrower`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_loan_ibfk_2` FOREIGN KEY (`loan_paying_from`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_loan_ibfk_3` FOREIGN KEY (`loan_pay_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_loan_installment`
--
ALTER TABLE `ro_loan_installment`
  ADD CONSTRAINT `ro_loan_installment_ibfk_1` FOREIGN KEY (`loan_ids`) REFERENCES `ro_loan` (`loan_id`),
  ADD CONSTRAINT `ro_loan_installment_ibfk_2` FOREIGN KEY (`loan_installment_provider`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_loan_installment_ibfk_3` FOREIGN KEY (`loan_installment_receive_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_login_attempts`
--
ALTER TABLE `ro_login_attempts`
  ADD CONSTRAINT `login_attempts_user` FOREIGN KEY (`attempt_user_id`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_notes`
--
ALTER TABLE `ro_notes`
  ADD CONSTRAINT `ro_notes_ibfk_1` FOREIGN KEY (`note_created_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_payments`
--
ALTER TABLE `ro_payments`
  ADD CONSTRAINT `ro_payments_ibfk_1` FOREIGN KEY (`payment_to_company`) REFERENCES `ro_companies` (`company_id`),
  ADD CONSTRAINT `ro_payments_ibfk_2` FOREIGN KEY (`payment_to_employee`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_payments_ibfk_3` FOREIGN KEY (`payment_from`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_payments_ibfk_4` FOREIGN KEY (`payment_made_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_payments_ibfk_5` FOREIGN KEY (`payment_update_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_payments_ibfk_6` FOREIGN KEY (`payment_purchase_id`) REFERENCES `ro_purchases` (`purchase_id`);

--
-- Constraints for table `ro_payments_categories`
--
ALTER TABLE `ro_payments_categories`
  ADD CONSTRAINT `ro_payments_categories_ibfk_1` FOREIGN KEY (`payment_category_shop_id`) REFERENCES `ro_shops` (`shop_id`);

--
-- Constraints for table `ro_payments_return`
--
ALTER TABLE `ro_payments_return`
  ADD CONSTRAINT `ro_payments_return_ibfk_1` FOREIGN KEY (`payments_return_payment_id`) REFERENCES `ro_payments` (`payment_id`),
  ADD CONSTRAINT `ro_payments_return_ibfk_2` FOREIGN KEY (`payments_return_accounts`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_payments_return_ibfk_3` FOREIGN KEY (`payments_return_emp_id`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_payments_return_ibfk_4` FOREIGN KEY (`payments_return_company_id`) REFERENCES `ro_companies` (`company_id`),
  ADD CONSTRAINT `ro_payments_return_ibfk_5` FOREIGN KEY (`payments_return_customer_id`) REFERENCES `ro_customers` (`customer_id`),
  ADD CONSTRAINT `ro_payments_return_ibfk_6` FOREIGN KEY (`payments_return_sales_id`) REFERENCES `ro_sales` (`sales_id`),
  ADD CONSTRAINT `ro_payments_return_ibfk_7` FOREIGN KEY (`payments_return_purchase_id`) REFERENCES `ro_purchases` (`purchase_id`),
  ADD CONSTRAINT `ro_payments_return_ibfk_8` FOREIGN KEY (`payments_return_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_payment_adjustment`
--
ALTER TABLE `ro_payment_adjustment`
  ADD CONSTRAINT `ro_payment_adjustment_ibfk_1` FOREIGN KEY (`pa_company`) REFERENCES `ro_companies` (`company_id`);

--
-- Constraints for table `ro_payment_items`
--
ALTER TABLE `ro_payment_items`
  ADD CONSTRAINT `ro_payment_items_ibfk_1` FOREIGN KEY (`payment_items_payments_id`,`payment_items_date`,`is_trash`) REFERENCES `ro_payments` (`payment_id`, `payment_date`, `is_trash`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ro_payment_items_ibfk_2` FOREIGN KEY (`payment_items_payments_id`,`payment_items_accounts`) REFERENCES `ro_payments` (`payment_id`, `payment_from`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ro_payment_items_ibfk_3` FOREIGN KEY (`payment_items_payments_id`,`payment_items_company`) REFERENCES `ro_payments` (`payment_id`, `payment_to_company`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ro_payment_items_ibfk_4` FOREIGN KEY (`payment_items_payments_id`,`payment_items_employee`) REFERENCES `ro_payments` (`payment_id`, `payment_to_employee`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ro_payment_items_ibfk_5` FOREIGN KEY (`payment_items_category_id`) REFERENCES `ro_payments_categories` (`payment_category_id`),
  ADD CONSTRAINT `ro_payment_items_ibfk_6` FOREIGN KEY (`payment_items_made_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_payment_items_ibfk_7` FOREIGN KEY (`payment_itemss_advance_payments_id`) REFERENCES `ro_advance_payments` (`advance_payment_id`);

--
-- Constraints for table `ro_persons`
--
ALTER TABLE `ro_persons`
  ADD CONSTRAINT `ro_persons_ibfk_1` FOREIGN KEY (`person_institute`) REFERENCES `ro_institute` (`institute_id`),
  ADD CONSTRAINT `ro_persons_ibfk_2` FOREIGN KEY (`person_upazila`) REFERENCES `ro_upazilas` (`upazila_id`),
  ADD CONSTRAINT `ro_persons_ibfk_3` FOREIGN KEY (`person_district`) REFERENCES `ro_districts` (`district_id`),
  ADD CONSTRAINT `ro_persons_ibfk_4` FOREIGN KEY (`person_division`) REFERENCES `ro_divisions` (`division_id`),
  ADD CONSTRAINT `ro_persons_ibfk_5` FOREIGN KEY (`leads_collect_by`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_persons_ibfk_6` FOREIGN KEY (`person_add_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_persons_ibfk_7` FOREIGN KEY (`person_update_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_persons_subject`
--
ALTER TABLE `ro_persons_subject`
  ADD CONSTRAINT `ro_persons_subject_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `ro_persons` (`person_id`);

--
-- Constraints for table `ro_persons_tag`
--
ALTER TABLE `ro_persons_tag`
  ADD CONSTRAINT `ro_persons_tag_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `ro_persons` (`person_id`);

--
-- Constraints for table `ro_products`
--
ALTER TABLE `ro_products`
  ADD CONSTRAINT `products_add_by` FOREIGN KEY (`product_add_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `products_brand` FOREIGN KEY (`product_brand_id`) REFERENCES `ro_product_brands` (`brand_id`),
  ADD CONSTRAINT `products_category` FOREIGN KEY (`product_category_id`) REFERENCES `ro_product_category` (`category_id`),
  ADD CONSTRAINT `products_edition` FOREIGN KEY (`product_edition`) REFERENCES `ro_product_editions` (`edition_name`) ON UPDATE CASCADE,
  ADD CONSTRAINT `products_generic` FOREIGN KEY (`product_generic`) REFERENCES `ro_product_generic` (`generic_name`) ON UPDATE CASCADE,
  ADD CONSTRAINT `products_unit` FOREIGN KEY (`product_unit`) REFERENCES `ro_product_units` (`unit_name`) ON UPDATE CASCADE,
  ADD CONSTRAINT `products_update_by` FOREIGN KEY (`product_update_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_product_author_relations`
--
ALTER TABLE `ro_product_author_relations`
  ADD CONSTRAINT `par_product_author` FOREIGN KEY (`product_author_id`) REFERENCES `ro_product_authors` (`author_id`),
  ADD CONSTRAINT `par_product_id` FOREIGN KEY (`product_id`) REFERENCES `ro_products` (`product_id`);

--
-- Constraints for table `ro_product_batches`
--
ALTER TABLE `ro_product_batches`
  ADD CONSTRAINT `batches_product_id` FOREIGN KEY (`product_id`) REFERENCES `ro_products` (`product_id`);

--
-- Constraints for table `ro_product_category`
--
ALTER TABLE `ro_product_category`
  ADD CONSTRAINT `ro_product_category_ibfk_1` FOREIGN KEY (`category_shop_id`) REFERENCES `ro_shops` (`shop_id`);

--
-- Constraints for table `ro_product_meta`
--
ALTER TABLE `ro_product_meta`
  ADD CONSTRAINT `meta_product_id` FOREIGN KEY (`product_id`) REFERENCES `ro_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ro_product_price`
--
ALTER TABLE `ro_product_price`
  ADD CONSTRAINT `ro_product_price_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `ro_products` (`product_id`),
  ADD CONSTRAINT `ro_product_price_ibfk_2` FOREIGN KEY (`shop_id`) REFERENCES `ro_shops` (`shop_id`);

--
-- Constraints for table `ro_product_stock`
--
ALTER TABLE `ro_product_stock`
  ADD CONSTRAINT `product_batch_number` FOREIGN KEY (`stock_batch_id`,`stock_product_id`) REFERENCES `ro_product_batches` (`batch_id`, `product_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_constraint` FOREIGN KEY (`stock_purchase_id`,`stock_entry_date`,`stock_warehouse_id`,`stock_shop_id`,`is_trash`) REFERENCES `ro_purchases` (`purchase_id`, `purchase_date`, `purchase_warehouse_id`, `purchase_shop_id`, `is_trash`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ro_product_stock_ibfk_1` FOREIGN KEY (`stock_warehouse_id`) REFERENCES `ro_warehouses` (`warehouse_id`),
  ADD CONSTRAINT `sales_constraint` FOREIGN KEY (`stock_sales_id`,`stock_entry_date`,`stock_warehouse_id`,`stock_shop_id`,`is_trash`) REFERENCES `ro_sales` (`sales_id`, `sales_delivery_date`, `sales_warehouse_id`, `sales_shop_id`, `is_trash`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_created_by` FOREIGN KEY (`stock_created_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `stock_entry` FOREIGN KEY (`stock_se_id`,`stock_entry_date`,`stock_warehouse_id`,`stock_shop_id`,`is_trash`) REFERENCES `ro_stock_entries` (`se_id`, `se_date`, `se_warehouse_id`, `se_shop_id`, `is_trash`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_product_id` FOREIGN KEY (`stock_product_id`) REFERENCES `ro_products` (`product_id`),
  ADD CONSTRAINT `stock_speciment_copy` FOREIGN KEY (`stock_sc_id`,`stock_entry_date`,`stock_employee_id`,`stock_warehouse_id`,`is_trash`) REFERENCES `ro_specimen_copies` (`sc_id`, `sc_date`, `sc_employee_id`, `sc_warehouse_id`, `is_trash`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_transfer` FOREIGN KEY (`stock_transfer_id`,`stock_entry_date`,`is_trash`) REFERENCES `ro_stock_transfer` (`stock_transfer_id`, `stock_transfer_date`, `is_trash`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ro_product_variations`
--
ALTER TABLE `ro_product_variations`
  ADD CONSTRAINT `product_variations` FOREIGN KEY (`pa_name`) REFERENCES `ro_product_attributes` (`pa_name`) ON UPDATE CASCADE;

--
-- Constraints for table `ro_purchases`
--
ALTER TABLE `ro_purchases`
  ADD CONSTRAINT `ro_purchases_ibfk_1` FOREIGN KEY (`purchase_shop_id`) REFERENCES `ro_shops` (`shop_id`),
  ADD CONSTRAINT `ro_purchases_ibfk_2` FOREIGN KEY (`purchase_created_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_purchases_ibfk_3` FOREIGN KEY (`purchase_update_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_purchases_ibfk_4` FOREIGN KEY (`purchase_company_id`) REFERENCES `ro_companies` (`company_id`),
  ADD CONSTRAINT `ro_purchases_ibfk_5` FOREIGN KEY (`purchase_warehouse_id`) REFERENCES `ro_warehouses` (`warehouse_id`);

--
-- Constraints for table `ro_received_payments`
--
ALTER TABLE `ro_received_payments`
  ADD CONSTRAINT `ro_received_payments_ibfk_1` FOREIGN KEY (`received_payments_shop`) REFERENCES `ro_shops` (`shop_id`),
  ADD CONSTRAINT `ro_received_payments_ibfk_2` FOREIGN KEY (`received_payments_accounts`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_received_payments_ibfk_3` FOREIGN KEY (`received_payments_sales_id`) REFERENCES `ro_sales` (`sales_id`),
  ADD CONSTRAINT `ro_received_payments_ibfk_4` FOREIGN KEY (`received_payments_from`) REFERENCES `ro_customers` (`customer_id`),
  ADD CONSTRAINT `ro_received_payments_ibfk_5` FOREIGN KEY (`received_payments_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_salaries`
--
ALTER TABLE `ro_salaries`
  ADD CONSTRAINT `ro_salaries_ibfk_1` FOREIGN KEY (`salary_emp_id`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_salaries_ibfk_2` FOREIGN KEY (`salary_add_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_salaries_ibfk_3` FOREIGN KEY (`salary_update_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_sales`
--
ALTER TABLE `ro_sales`
  ADD CONSTRAINT `ro_sales_ibfk_1` FOREIGN KEY (`sales_shop_id`) REFERENCES `ro_shops` (`shop_id`),
  ADD CONSTRAINT `ro_sales_ibfk_2` FOREIGN KEY (`sales_created_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_sales_ibfk_3` FOREIGN KEY (`sales_update_by`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_sales_ibfk_4` FOREIGN KEY (`sales_customer_id`) REFERENCES `ro_customers` (`customer_id`),
  ADD CONSTRAINT `ro_sales_ibfk_5` FOREIGN KEY (`sales_warehouse_id`) REFERENCES `ro_warehouses` (`warehouse_id`);

--
-- Constraints for table `ro_sc_distribution`
--
ALTER TABLE `ro_sc_distribution`
  ADD CONSTRAINT `ro_sc_distribution_ibfk_1` FOREIGN KEY (`scd_distributor`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_sc_distribution_ibfk_2` FOREIGN KEY (`scd_person_id`) REFERENCES `ro_persons` (`person_id`),
  ADD CONSTRAINT `ro_sc_distribution_ibfk_3` FOREIGN KEY (`scd_product_id`) REFERENCES `ro_products` (`product_id`),
  ADD CONSTRAINT `ro_sc_distribution_ibfk_4` FOREIGN KEY (`scd_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_sip_credentials`
--
ALTER TABLE `ro_sip_credentials`
  ADD CONSTRAINT `ro_sip_credentials_ibfk_1` FOREIGN KEY (`sip_representative`) REFERENCES `ro_users` (`user_id`),
  ADD CONSTRAINT `ro_sip_credentials_ibfk_2` FOREIGN KEY (`sip_created_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_sms_sender`
--
ALTER TABLE `ro_sms_sender`
  ADD CONSTRAINT `ro_sms_sender_ibfk_1` FOREIGN KEY (`send_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_sms_template`
--
ALTER TABLE `ro_sms_template`
  ADD CONSTRAINT `ro_sms_template_ibfk_1` FOREIGN KEY (`st_created_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_specimen_copies`
--
ALTER TABLE `ro_specimen_copies`
  ADD CONSTRAINT `ro_specimen_copies_ibfk_1` FOREIGN KEY (`sc_employee_id`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `ro_specimen_copies_ibfk_2` FOREIGN KEY (`sc_warehouse_id`) REFERENCES `ro_warehouses` (`warehouse_id`),
  ADD CONSTRAINT `ro_specimen_copies_ibfk_3` FOREIGN KEY (`sc_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_stock_entries`
--
ALTER TABLE `ro_stock_entries`
  ADD CONSTRAINT `ro_stock_entries_ibfk_1` FOREIGN KEY (`se_warehouse_id`) REFERENCES `ro_warehouses` (`warehouse_id`),
  ADD CONSTRAINT `ro_stock_entries_ibfk_2` FOREIGN KEY (`se_add_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_stock_transfer`
--
ALTER TABLE `ro_stock_transfer`
  ADD CONSTRAINT `ro_stock_transfer_ibfk_1` FOREIGN KEY (`stock_transfer_from_warehouse`) REFERENCES `ro_warehouses` (`warehouse_id`),
  ADD CONSTRAINT `ro_stock_transfer_ibfk_2` FOREIGN KEY (`stock_transfer_to_warehouse`) REFERENCES `ro_warehouses` (`warehouse_id`);

--
-- Constraints for table `ro_transfer_money`
--
ALTER TABLE `ro_transfer_money`
  ADD CONSTRAINT `ro_transfer_money_ibfk_1` FOREIGN KEY (`transfer_money_from`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_transfer_money_ibfk_2` FOREIGN KEY (`transfer_money_to`) REFERENCES `ro_accounts` (`accounts_id`),
  ADD CONSTRAINT `ro_transfer_money_ibfk_3` FOREIGN KEY (`transfer_money_made_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_upazilas`
--
ALTER TABLE `ro_upazilas`
  ADD CONSTRAINT `upazilas_dist_id` FOREIGN KEY (`upazila_district_id`) REFERENCES `ro_districts` (`district_id`);

--
-- Constraints for table `ro_users`
--
ALTER TABLE `ro_users`
  ADD CONSTRAINT `users_emp_id` FOREIGN KEY (`user_emp_id`) REFERENCES `ro_employees` (`emp_id`),
  ADD CONSTRAINT `users_group_id` FOREIGN KEY (`user_group_id`) REFERENCES `ro_user_group` (`group_id`);

--
-- Constraints for table `ro_users_login_history`
--
ALTER TABLE `ro_users_login_history`
  ADD CONSTRAINT `ro_users_login_history_ibfk_1` FOREIGN KEY (`login_users_id`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_voice_message`
--
ALTER TABLE `ro_voice_message`
  ADD CONSTRAINT `ro_voice_message_ibfk_1` FOREIGN KEY (`vm_added_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_warehouses`
--
ALTER TABLE `ro_warehouses`
  ADD CONSTRAINT `ro_warehouses_ibfk_1` FOREIGN KEY (`warehouse_shop`) REFERENCES `ro_shops` (`shop_id`);

--
-- Constraints for table `ro_wastage_sale`
--
ALTER TABLE `ro_wastage_sale`
  ADD CONSTRAINT `ro_wastage_sale_ibfk_1` FOREIGN KEY (`wastage_sale_customer`) REFERENCES `ro_customers` (`customer_id`),
  ADD CONSTRAINT `ro_wastage_sale_ibfk_2` FOREIGN KEY (`wastage_sale_created_by`) REFERENCES `ro_users` (`user_id`);

--
-- Constraints for table `ro_wastage_sale_items`
--
ALTER TABLE `ro_wastage_sale_items`
  ADD CONSTRAINT `ro_wastage_sale_items_ibfk_1` FOREIGN KEY (`wastage_sale_id`,`is_trash`) REFERENCES `ro_wastage_sale` (`wastage_sale_id`, `is_trash`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
