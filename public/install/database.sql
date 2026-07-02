-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2024 at 12:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zolahost`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `role_id`, `name`, `email`, `mobile`, `address`, `username`, `email_verified_at`, `image`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 0, 'Super Admin', 'admin@site.com', '+880.09174524313', '{\"address\":\"Address\",\"state\":\"State\",\"zip\":\"Zip\",\"country\":\"AF\",\"city\":null}', 'admin', NULL, '667fe031159321719656497.png', '$2y$10$el35r0DVW8rbSEx0xm5xDu5IsbxmiaA1CZe3tfeub4iA4HxD1QSxq', 1, '6dx58EiZqvwxVmxCNuRqdgpApGYGaWnh32onMbqYbEblutSYFGMFQXLcmMk0', NULL, '2024-06-29 04:21:37');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `api_response` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `title` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `click_url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_password_resets`
--

CREATE TABLE `admin_password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing_settings`
--

CREATE TABLE `billing_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `create_default_invoice_days` int(11) NOT NULL DEFAULT 0 COMMENT 'Default day to create a new invoice from cron jobs\r\n',
  `create_invoice` text NOT NULL COMMENT 'Days to create a new invoice for service using billing cycle',
  `create_domain_invoice_days` int(11) NOT NULL DEFAULT 0 COMMENT 'Day to create a new invoice for domain \r\n',
  `invoice_send_reminder_days` int(11) NOT NULL DEFAULT 0 COMMENT 'For unpaid invoice reminder',
  `invoice_first_over_due_reminder` int(11) NOT NULL DEFAULT 0,
  `invoice_second_over_due_reminder` int(11) NOT NULL DEFAULT 0,
  `invoice_third_over_due_reminder` int(11) NOT NULL DEFAULT 0,
  `late_fee_days` int(11) NOT NULL DEFAULT 0 COMMENT 'For add late fee',
  `invoice_late_fee_amount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `invoice_late_fee_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=> Fixed, 2=> %',
  `invoice_send_reminder` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Enable / Disable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `billing_settings`
--

INSERT INTO `billing_settings` (`id`, `create_default_invoice_days`, `create_invoice`, `create_domain_invoice_days`, `invoice_send_reminder_days`, `invoice_first_over_due_reminder`, `invoice_second_over_due_reminder`, `invoice_third_over_due_reminder`, `late_fee_days`, `invoice_late_fee_amount`, `invoice_late_fee_type`, `invoice_send_reminder`, `created_at`, `updated_at`) VALUES
(1, 10, '{\"monthly\":\"10\",\"quarterly\":\"20\",\"semi_annually\":\"30\",\"annually\":\"40\",\"biennially\":\"50\",\"triennially\":\"60\"}', 1, 5, 1, 2, 3, 5, 10.0000000000000000, 2, 1, NULL, '2023-01-30 13:18:49');

-- --------------------------------------------------------

--
-- Table structure for table `cancel_requests`
--

CREATE TABLE `cancel_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `hosting_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `reason` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=>Immediate,\r\n2=>End of Billing Period',
  `status` tinyint(1) DEFAULT 2 COMMENT '1=>Completed,\r\n2=>Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configurable_groups`
--

CREATE TABLE `configurable_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configurable_group_options`
--

CREATE TABLE `configurable_group_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `option_type` tinyint(1) NOT NULL DEFAULT 0,
  `configurable_group_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `order` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configurable_group_sub_options`
--

CREATE TABLE `configurable_group_sub_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `configurable_group_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `configurable_group_option_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `order` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `used` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `discount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `min_order_amount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000 COMMENT 'Required order amount for get discount ',
  `type` tinyint(1) NOT NULL COMMENT '0=> %, 1=>Fixed',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_jobs`
--

CREATE TABLE `cron_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `cron_schedule_id` int(11) NOT NULL DEFAULT 0,
  `next_run` datetime DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  `is_running` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cron_jobs`
--

INSERT INTO `cron_jobs` (`id`, `name`, `alias`, `action`, `url`, `cron_schedule_id`, `next_run`, `last_run`, `is_running`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Invoice Generate', 'invoice_generate', '[\"App\\\\Http\\\\Controllers\\\\CronController\", \"invoiceGenerate\"]', NULL, 3, '2023-07-29 15:58:00', '2023-07-29 15:58:26', 1, 1, '2023-06-21 23:29:14', '2023-07-29 10:09:45'),
(2, 'Remove Shopping Carts', 'remove_shopping_carts', '[\"App\\\\Http\\\\Controllers\\\\CronController\", \"removeShoppingCarts\"]', NULL, 3, '2023-07-29 15:58:31', '2023-07-29 15:58:26', 1, 1, '2023-06-22 06:04:49', '2023-07-29 09:58:26'),
(7, 'Unpaid Invoice Reminder', 'unpaid_invoice_reminder', '[\"App\\\\Http\\\\Controllers\\\\CronController\", \"unpaidInvoiceReminder\"]', NULL, 3, '2023-07-29 15:58:31', '2023-07-29 15:58:26', 1, 1, '2023-06-22 06:04:49', '2023-07-29 09:58:26'),
(8, 'First Overdue Reminder', 'first_overdue_reminder', '[\"App\\\\Http\\\\Controllers\\\\CronController\", \"firstOverdueReminder\"]', NULL, 3, '2023-07-29 15:58:31', '2023-07-29 15:58:26', 1, 1, '2023-06-22 06:04:49', '2023-07-29 09:58:26'),
(9, 'Second Overdue Reminder', 'second_overdue_reminder', '[\"App\\\\Http\\\\Controllers\\\\CronController\", \"secondOverdueReminder\"]', NULL, 3, '2023-07-29 15:58:31', '2023-07-29 15:58:26', 1, 1, '2023-06-22 06:04:49', '2023-07-29 09:58:26'),
(10, 'Third Overdue Reminder', 'third_overdue_reminder', '[\"App\\\\Http\\\\Controllers\\\\CronController\", \"thirdOverdueReminder\"]', NULL, 3, '2023-07-29 15:58:31', '2023-07-29 15:58:26', 1, 1, '2023-06-22 06:04:49', '2023-07-29 09:58:26'),
(11, 'Add Late Fee', 'add_late_fee', '[\"App\\\\Http\\\\Controllers\\\\CronController\", \"addLateFee\"]', NULL, 3, '2023-07-29 15:58:31', '2023-07-29 15:58:26', 1, 1, '2023-06-22 06:04:49', '2023-07-29 09:58:26');

-- --------------------------------------------------------

--
-- Table structure for table `cron_job_logs`
--

CREATE TABLE `cron_job_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cron_job_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_schedules`
--

CREATE TABLE `cron_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `interval` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cron_schedules`
--

INSERT INTO `cron_schedules` (`id`, `name`, `interval`, `status`, `created_at`, `updated_at`) VALUES
(1, '5 Minutes', 300, 1, '2023-06-21 08:14:52', '2023-06-21 08:14:52'),
(2, '10 Minutes', 600, 1, '2023-06-21 23:28:22', '2023-06-21 23:28:22'),
(3, '15 Minutes', 900, 1, '2023-07-29 07:35:38', '2023-07-29 07:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `invoice_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `method_code` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `method_currency` varchar(40) DEFAULT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `final_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `detail` text DEFAULT NULL,
  `btc_amount` varchar(255) DEFAULT NULL,
  `btc_wallet` varchar(255) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `payment_try` int(10) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>success, 2=>pending, 3=>cancel',
  `from_api` tinyint(1) NOT NULL DEFAULT 0,
  `admin_feedback` varchar(255) DEFAULT NULL,
  `success_url` varchar(255) DEFAULT NULL,
  `failed_url` varchar(255) DEFAULT NULL,
  `last_cron` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_tokens`
--

CREATE TABLE `device_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_app` tinyint(1) NOT NULL DEFAULT 0,
  `token` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE `domains` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=> Means empty invoice/allow creating a new invoice,\r\n1=> Already created',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `deposit_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `coupon_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `hosting_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `domain_setup_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'TLD / Domain setup id',
  `domain_register_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Domain service provider / register id',
  `contact_id` int(20) NOT NULL DEFAULT 0 COMMENT 'For Resell Biz\r\n',
  `customer_id` int(20) NOT NULL DEFAULT 0 COMMENT 'For Resell Biz\r\n',
  `resell_order_id` int(20) NOT NULL DEFAULT 0 COMMENT 'For Resell Biz	',
  `domain` varchar(255) NOT NULL,
  `admin_notes` text DEFAULT NULL COMMENT 'For admin only',
  `ns1` varchar(255) DEFAULT NULL,
  `ns2` varchar(255) DEFAULT NULL,
  `ns3` varchar(255) DEFAULT NULL,
  `ns4` varchar(255) DEFAULT NULL,
  `whois_guard` int(11) NOT NULL DEFAULT 0 COMMENT 'For Namecheap',
  `id_protection` tinyint(1) NOT NULL DEFAULT 0,
  `first_payment_amount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `recurring_amount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `discount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `status` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=> Active,\r\n2=> Pending, \r\n3=> Pending Registration,\r\n4=> Expired, \r\n5=> Cancelled,\r\n',
  `reg_period` int(11) NOT NULL DEFAULT 1,
  `expiry_date` varchar(40) DEFAULT NULL,
  `next_due_date` varchar(40) DEFAULT NULL,
  `next_invoice_date` varchar(40) DEFAULT NULL,
  `reg_date` varchar(40) DEFAULT NULL COMMENT 'Registration date',
  `last_cron` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domain_pricings`
--

CREATE TABLE `domain_pricings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `domain_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `one_year_price` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `one_year_id_protection` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `one_year_renew` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `two_year_price` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `two_year_id_protection` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `two_year_renew` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `three_year_price` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `three_year_id_protection` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `three_year_renew` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `four_year_price` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `four_year_id_protection` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `four_year_renew` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `five_year_price` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `five_year_id_protection` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `five_year_renew` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `six_year_price` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `six_year_id_protection` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `six_year_renew` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domain_registers`
--

CREATE TABLE `domain_registers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) NOT NULL,
  `alias` varchar(40) NOT NULL,
  `ns1` varchar(255) DEFAULT NULL,
  `ns2` varchar(255) DEFAULT NULL,
  `ns3` varchar(255) DEFAULT NULL,
  `ns4` varchar(255) DEFAULT NULL,
  `params` text NOT NULL,
  `test_mode` tinyint(1) NOT NULL DEFAULT 0,
  `default` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `setup_done` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'When the admin did his first setup',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `domain_registers`
--

INSERT INTO `domain_registers` (`id`, `name`, `alias`, `ns1`, `ns2`, `ns3`, `ns4`, `params`, `test_mode`, `default`, `status`, `setup_done`, `created_at`, `updated_at`) VALUES
(1, 'Namecheap', 'Namecheap', 'ns1.nc.com', 'ns2.nc.com', 'ns3.nc.com', 'ns4.nc.com', '{\"username\":{\"title\":\"Username\",\"value\":\"...\"},\"sandbox_username\":{\"title\":\"Sandbox Username <br\\/><code>(This will be used only if you set the test mode on)<\\/code>\",\"value\":\"...\",\"test_mode\":true},\"api_key\":{\"title\":\"Api Key\",\"required\":true,\"value\":\"...\"}}', 1, 0, 1, 1, NULL, '2023-01-31 12:50:10'),
(4, 'Resell Biz', 'Resell', 'ns1.rb.com', 'ns2.rb.com', 'ns3.rb.com', 'ns4.rb.com', '{\"auth_user_id\":{\"title\":\"Auth User Id (Reseller ID)\",\"required\":true,\"value\":\"...\"},\"api_key\":{\"title\":\"Api Key\",\"required\":true,\"value\":\"...\"}}', 1, 1, 1, 1, NULL, '2023-02-04 10:09:02'),
(5, 'NameSilo', 'NameSilo', 'ns1.dnsowl.com', 'ns2.dnsowl.com', 'ns3.dnsowl.com', NULL, '{\"api_key\":{\"title\":\"API Key\",\"required\":true,\"value\":\"\"}}', 1, 0, 0, 0, NULL, NULL),
(6, 'ResellerClub', 'ResellerClub', 'ns1.resellerclub.com', 'ns2.resellerclub.com', NULL, NULL, '{\"auth_user_id\":{\"title\":\"Auth User Id (Reseller ID)\",\"required\":true,\"value\":\"\"},\"api_key\":{\"title\":\"Api Key\",\"required\":true,\"value\":\"\"}}', 1, 0, 0, 0, NULL, NULL),
(7, 'NetEarthOne', 'NetEarthOne', 'ns1.netearthone.com', 'ns2.netearthone.com', NULL, NULL, '{\"auth_user_id\":{\"title\":\"Auth User Id (Reseller ID)\",\"required\":true,\"value\":\"\"},\"api_key\":{\"title\":\"Api Key\",\"required\":true,\"value\":\"\"}}', 1, 0, 0, 0, NULL, NULL),
(8, 'LogicBoxes', 'LogicBoxes', 'ns1.logicboxes.com', 'ns2.logicboxes.com', NULL, NULL, '{\"auth_user_id\":{\"title\":\"Auth User Id (Reseller ID)\",\"required\":true,\"value\":\"\"},\"api_key\":{\"title\":\"Api Key\",\"required\":true,\"value\":\"\"}}', 1, 0, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `domain_setups`
--

CREATE TABLE `domain_setups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `extension` varchar(255) NOT NULL COMMENT 'Extension / TLD ',
  `id_protection` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `extensions`
--

CREATE TABLE `extensions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `script` text DEFAULT NULL,
  `shortcode` text DEFAULT NULL COMMENT 'object',
  `support` text DEFAULT NULL COMMENT 'help section',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `extensions`
--

INSERT INTO `extensions` (`id`, `act`, `name`, `description`, `image`, `script`, `shortcode`, `support`, `status`, `created_at`, `updated_at`) VALUES
(1, 'tawk-chat', 'Tawk.to', 'Key location is shown bellow', 'tawky_big.png', '<script>\r\n                        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();\r\n                        (function(){\r\n                        var s1=document.createElement(\"script\"),s0=document.getElementsByTagName(\"script\")[0];\r\n                        s1.async=true;\r\n                        s1.src=\"https://embed.tawk.to/{{app_key}}\";\r\n                        s1.charset=\"UTF-8\";\r\n                        s1.setAttribute(\"crossorigin\",\"*\");\r\n                        s0.parentNode.insertBefore(s1,s0);\r\n                        })();\r\n                    </script>', '{\"app_key\":{\"title\":\"App Key\",\"value\":\"------\"}}', 'twak.png', 0, '2019-10-18 23:16:05', '2022-03-22 05:22:24'),
(2, 'google-recaptcha2', 'Google Recaptcha 2', 'Key location is shown bellow', 'recaptcha3.png', '<script src=\"https://www.google.com/recaptcha/api.js\"></script>\n<div class=\"g-recaptcha\" data-sitekey=\"{{site_key}}\" data-callback=\"verifyCaptcha\"></div>\n<div id=\"g-recaptcha-error\"></div>', '{\"site_key\":{\"title\":\"Site Key\",\"value\":\"6LdPC88fAAAAADQlUf_DV6Hrvgm-pZuLJFSLDOWV\"},\"secret_key\":{\"title\":\"Secret Key\",\"value\":\"6LdPC88fAAAAAG5SVaRYDnV2NpCrptLg2XLYKRKB\"}}', 'recaptcha.png', 0, '2019-10-18 23:16:05', '2023-01-15 12:44:24'),
(3, 'custom-captcha', 'Custom Captcha', 'Just put any random string', 'customcaptcha.png', NULL, '{\"random_key\":{\"title\":\"Random String\",\"value\":\"SecureString\"}}', 'na', 0, '2019-10-18 23:16:05', '2022-03-17 06:33:11'),
(4, 'google-analytics', 'Google Analytics', 'Key location is shown bellow', 'google_analytics.png', '<script async src=\"https://www.googletagmanager.com/gtag/js?id={{measurement_id}}\"></script>\n                <script>\n                  window.dataLayer = window.dataLayer || [];\n                  function gtag(){dataLayer.push(arguments);}\n                  gtag(\"js\", new Date());\n                \n                  gtag(\"config\", \"{{measurement_id}}\");\n                </script>', '{\"measurement_id\":{\"title\":\"Measurement ID\",\"value\":\"------\"}}', 'ganalytics.png', 0, NULL, '2021-05-04 10:19:12'),
(5, 'fb-comment', 'Facebook Comment ', 'Key location is shown bellow', 'Facebook.png', '<div id=\"fb-root\"></div><script async defer crossorigin=\"anonymous\" src=\"https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v4.0&appId={{app_key}}&autoLogAppEvents=1\"></script>', '{\"app_key\":{\"title\":\"App Key\",\"value\":\"----\"}}', 'fb_com.png', 0, NULL, '2023-02-06 13:02:55');

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `act` varchar(40) DEFAULT NULL,
  `form_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `frontends`
--

CREATE TABLE `frontends` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data_keys` varchar(40) DEFAULT NULL,
  `data_values` longtext DEFAULT NULL,
  `seo_content` longtext DEFAULT NULL,
  `tempname` varchar(40) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `frontends`
--

INSERT INTO `frontends` (`id`, `data_keys`, `data_values`, `seo_content`, `tempname`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'seo.data', '{\"seo_image\":\"1\",\"keywords\":[\"ZolaHost\",\"hosting management\",\"hosting management and billing\"],\"description\":\"Ultimate solution for web hosting management and billing\",\"social_title\":\"ZolaHost\",\"social_description\":\"Ultimate solution for web hosting management and billing\",\"image\":\"667fe37fccde01719657343.png\"}', NULL, NULL, '', '2020-07-04 23:42:52', '2024-06-29 04:35:43'),
(24, 'about.content', '{\"has_image\":\"1\",\"heading\":\"Latest News\",\"sub_heading\":\"Register New Account\",\"description\":\"fdg sdfgsdf g ggg\",\"about_icon\":\"<i class=\\\"las la-address-card\\\"><\\/i>\",\"background_image\":\"60951a84abd141620384388.png\",\"about_image\":\"5f9914e907ace1603867881.jpg\"}', NULL, 'basic', NULL, '2020-10-28 00:51:20', '2021-05-07 10:16:28'),
(25, 'blog.content', '{\"heading\":\"Latest News\",\"subheading\":\"------\"}', NULL, 'basic', NULL, '2020-10-28 00:51:34', '2022-03-19 04:41:13'),
(27, 'contact_us.content', '{\"heading\":\"Get in Touch\",\"description\":\"Please do not hesitate to contact our experts if you want advise, have a query, or require technical support.\",\"email\":\"demo@gmail.com\",\"phone\":\"+0123456789\",\"address\":\"North Bhugichugi, Naura, Bumra\"}', NULL, 'basic', NULL, '2020-10-28 00:59:19', '2023-01-14 14:24:02'),
(28, 'counter.content', '{\"heading\":\"Latest News\",\"sub_heading\":\"Register New Account\"}', NULL, 'basic', NULL, '2020-10-28 01:04:02', '2020-10-28 01:04:02'),
(41, 'cookie.data', '{\"short_desc\":\"We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.\",\"description\":\"<div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">How do we protect your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">All provided delicate\\/credit data is sent through Stripe.<br>After an exchange, your private data (credit cards, social security numbers, financials, and so on) won\'t be put away on our workers.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Do we disclose any information to outside parties?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We don\'t sell, exchange, or in any case move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in working our site, leading our business, or adjusting you, since those gatherings consent to keep this data private. We may likewise deliver your data when we accept discharge is suitable to follow the law, implement our site strategies, or ensure our own or others\' rights, property, or wellbeing.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Children\'s Online Privacy Protection Act Compliance<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We are consistent with the prerequisites of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old. Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of age or more established.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Changes to our Privacy Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">If we decide to change our privacy policy, we will post those changes on this page.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">How long we retain your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">At the point when you register for our site, we cycle and keep your information we have about you however long you don\'t erase the record or withdraw yourself (subject to laws and guidelines).<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What we don\\u2019t do with your data<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We don\'t and will never share, unveil, sell, or in any case give your information to different organizations for the promoting of their items or administrations.<\\/p><\\/div>\",\"status\":1}', NULL, NULL, NULL, '2020-07-04 23:42:52', '2022-03-30 11:23:12'),
(42, 'policy_pages.element', '{\"title\":\"Privacy Policy\",\"details\":\"<div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">How do we protect your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">All provided delicate\\/credit data is sent through Stripe.<br \\/>After an exchange, your private data (credit cards, social security numbers, financials, and so on) won\'t be put away on our workers.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Do we disclose any information to outside parties?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t sell, exchange, or in any case move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in working our site, leading our business, or adjusting you, since those gatherings consent to keep this data private. We may likewise deliver your data when we accept discharge is suitable to follow the law, implement our site strategies, or ensure our own or others\' rights, property, or wellbeing.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Children\'s Online Privacy Protection Act Compliance<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We are consistent with the prerequisites of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old. Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of age or more established.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Changes to our Privacy Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">If we decide to change our privacy policy, we will post those changes on this page.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">How long we retain your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">At the point when you register for our site, we cycle and keep your information we have about you however long you don\'t erase the record or withdraw yourself (subject to laws and guidelines).<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">What we don\\u2019t do with your data<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t and will never share, unveil, sell, or in any case give your information to different organizations for the promoting of their items or administrations.<\\/p><\\/div>\"}', NULL, 'basic', 'privacy-policy', '2021-06-09 08:50:42', '2023-01-14 11:38:02'),
(43, 'policy_pages.element', '{\"title\":\"Terms of Service\",\"details\":\"<div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We claim all authority to dismiss, end, or handicap any help with or without cause per administrator discretion. This is a Complete independent facilitating, on the off chance that you misuse our ticket or Livechat or emotionally supportive network by submitting solicitations or protests we will impair your record. The solitary time you should reach us about the seaward facilitating is if there is an issue with the worker. We have not many substance limitations and everything is as per laws and guidelines. Try not to join on the off chance that you intend to do anything contrary to the guidelines, we do check these things and we will know, don\'t burn through our own and your time by joining on the off chance that you figure you will have the option to sneak by us and break the terms.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><ul class=\\\"font-18\\\" style=\\\"padding-left:15px;list-style-type:disc;font-size:18px;\\\"><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Configuration requests - If you have a fully managed dedicated server with us then we offer custom PHP\\/MySQL configurations, firewalls for dedicated IPs, DNS, and httpd configurations.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Software requests - Cpanel Extension Installation will be granted as long as it does not interfere with the security, stability, and performance of other users on the server.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Emergency Support - We do not provide emergency support \\/ Phone Support \\/ LiveChat Support. Support may take some hours sometimes.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Webmaster help - We do not offer any support for webmaster related issues and difficulty including coding, &amp; installs, Error solving. if there is an issue where a library or configuration of the server then we can help you if it\'s possible from our end.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Backups - We keep backups but we are not responsible for data loss, you are fully responsible for all backups.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">We Don\'t support any child porn or such material.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No spam-related sites or material, such as email lists, mass mail programs, and scripts, etc.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No harassing material that may cause people to retaliate against you.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No phishing pages.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">You may not run any exploitation script from the server. reason can be terminated immediately.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">If Anyone attempting to hack or exploit the server by using your script or hosting, we will terminate your account to keep safe other users.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Malicious Botnets are strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Spam, mass mailing, or email marketing in any way are strictly forbidden here.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Malicious hacking materials, trojans, viruses, &amp; malicious bots running or for download are forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Resource and cronjob abuse is forbidden and will result in suspension or termination.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Php\\/CGI proxies are strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">CGI-IRC is strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No fake or disposal mailers, mass mailing, mail bombers, SMS bombers, etc.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">NO CREDIT OR REFUND will be granted for interruptions of service, due to User Agreement violations.<\\/li><\\/ul><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Terms &amp; Conditions for Users<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">Before getting to this site, you are consenting to be limited by these site Terms and Conditions of Use, every single appropriate law, and guidelines, and concur that you are answerable for consistency with any material neighborhood laws. If you disagree with any of these terms, you are restricted from utilizing or getting to this site.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Support<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">Whenever you have downloaded our item, you may get in touch with us for help through email and we will give a valiant effort to determine your issue. We will attempt to answer using the Email for more modest bug fixes, after which we will refresh the center bundle. Content help is offered to confirmed clients by Tickets as it were. Backing demands made by email and Livechat.<\\/p><p class=\\\"my-3 font-18 font-weight-bold\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">On the off chance that your help requires extra adjustment of the System, at that point, you have two alternatives:<\\/p><ul class=\\\"font-18\\\" style=\\\"padding-left:15px;list-style-type:disc;font-size:18px;\\\"><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Hang tight for additional update discharge.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Or on the other hand, enlist a specialist (We offer customization for extra charges).<\\/li><\\/ul><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Ownership<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">You may not guarantee scholarly or selective possession of any of our items, altered or unmodified. All items are property, we created them. Our items are given \\\"with no guarantees\\\" without guarantee of any sort, either communicated or suggested. On no occasion will our juridical individual be subject to any harms including, however not restricted to, immediate, roundabout, extraordinary, accidental, or significant harms or different misfortunes emerging out of the utilization of or powerlessness to utilize our items.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Warranty<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t offer any guarantee or assurance of these Services in any way. When our Services have been modified we can\'t ensure they will work with all outsider plugins, modules, or internet browsers. Program similarity ought to be tried against the show formats on the demo worker. If you don\'t mind guarantee that the programs you use will work with the component, as we can not ensure that our systems will work with all program mixes.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Unauthorized\\/Illegal Usage<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">You may not utilize our things for any illicit or unapproved reason or may you, in the utilization of the stage, disregard any laws in your locale (counting yet not restricted to copyright laws) just as the laws of your nation and International law. Specifically, it is disallowed to utilize the things on our foundation for pages that advance: brutality, illegal intimidation, hard sexual entertainment, bigotry, obscenity content or warez programming joins.<br \\/><br \\/>You can\'t imitate, copy, duplicate, sell, exchange or adventure any of our segment, utilization of the offered on our things, or admittance to the administration without the express composed consent by us or item proprietor.<br \\/><br \\/>Our Members are liable for all substance posted on the discussion and demo and movement that happens under your record.<br \\/><br \\/>We hold the chance of hindering your participation account quickly if we will think about a particularly not allowed conduct.<br \\/><br \\/>If you make a record on our site, you are liable for keeping up the security of your record, and you are completely answerable for all exercises that happen under the record and some other activities taken regarding the record. You should quickly inform us, of any unapproved employments of your record or some other penetrates of security.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Fiverr, Seoclerks Sellers Or Affiliates<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We do NOT ensure full SEO campaign conveyance within 24 hours. We make no assurance for conveyance time by any means. We give our best assessment to orders during the putting in of requests, anyway, these are gauges. We won\'t be considered liable for loss of assets, negative surveys or you being prohibited for late conveyance. If you are selling on a site that requires time touchy outcomes, utilize Our SEO Services at your own risk.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Payment\\/Refund Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">No refund or cash back will be made. After a deposit has been finished, it is extremely unlikely to invert it. You should utilize your equilibrium on requests our administrations, Hosting, SEO campaign. You concur that once you complete a deposit, you won\'t document a debate or a chargeback against us in any way, shape, or form.<br \\/><br \\/>If you document a debate or chargeback against us after a deposit, we claim all authority to end every single future request, prohibit you from our site. False action, for example, utilizing unapproved or taken charge cards will prompt the end of your record. There are no special cases.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:rgb(111,111,111);font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:rgb(54,54,54);\\\">Free Balance \\/ Coupon Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We offer numerous approaches to get FREE Balance, Coupons and Deposit offers yet we generally reserve the privilege to audit it and deduct it from your record offset with any explanation we may it is a sort of misuse. If we choose to deduct a few or all of free Balance from your record balance, and your record balance becomes negative, at that point the record will naturally be suspended. If your record is suspended because of a negative Balance you can request to make a custom payment to settle your equilibrium to actuate your record.<\\/p><\\/div>\"}', NULL, 'basic', 'terms-of-service', '2021-06-09 08:51:18', '2021-06-09 08:51:18'),
(44, 'maintenance.data', '{\"description\":\"<div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"text-align: center; font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"text-align: center; margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div>\",\"image\":\"667fe161ab7771719656801.png\"}', NULL, 'basic', NULL, '2020-07-04 23:42:52', '2024-06-29 04:32:21'),
(45, 'invoice_address.content', '{\"address\":\"Company Name - Webscape Studio\"}', NULL, 'basic', NULL, '2022-06-05 08:49:42', '2022-06-05 08:49:42'),
(46, 'footer.content', '{\"description\":\"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book\"}', NULL, 'basic', NULL, '2022-06-12 05:24:05', '2023-01-14 11:19:33'),
(48, 'domain.content', '{\"heading\":\"Secure your domain name\",\"subheading\":\"Your domain name is your online business\\u2019s most valuable asset. After all, it\\u2019s much more than just your web address.\",\"text\":\"Search your dream domain name. Check domain name availability and secure yours now.\"}', NULL, 'basic', NULL, '2022-06-12 05:58:57', '2022-06-12 07:32:34'),
(49, 'blog.element', '{\"title\":\"Thank you for choosing ZolaHost!\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<br \\/><div>Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero\'s De Finibus Bonorum et Malorum for use in a type specimen book. It usually begins with: \\u201cLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\\u201d The purpose of lorem ipsum is to create a natural looking block of text (sentence, paragraph, page, etc.) that doesn\'t distract from the layout. A practice not without controversy, laying out pages with meaningless filler text can be very useful when the focus is meant to be on design, not content. The passage experienced a surge in popularity during the 1960s when Letraset used it on their dry-transfer sheets, and again during the 90s as desktop publishers bundled the text with their software. Today it\'s seen all around the web; on templates, websites, and stock designs. Use our generator to get your own, or read on for the authoritative history of lorem ipsum<br \\/><\\/div>\"}', NULL, 'basic', 'thank-you-for-choosing-zolahost!', '2023-01-14 12:12:49', '2023-01-14 12:12:49'),
(50, 'faq.element', '{\"question\":\"Frequently gets updated based on new data insights\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-14 12:33:55', '2023-01-14 12:33:55'),
(51, 'faq.element', '{\"question\":\"Showcases expertise, trust, and authority within your niche\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-14 12:34:08', '2023-01-14 12:34:08'),
(52, 'faq.element', '{\"question\":\"Covers a broad range of intent (transactional, informational, etc.)\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-14 12:34:18', '2023-01-14 12:34:18'),
(53, 'faq.element', '{\"question\":\"Lands new users to the website by solving problems\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-14 12:34:29', '2023-01-14 12:34:29'),
(54, 'blog.element', '{\"title\":\"From its medieval origins to the digital era, learn everything there is to know about the ubiquitous lorem ipsum passage\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<br \\/><div>Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero\'s De Finibus Bonorum et Malorum for use in a type specimen book. It usually begins with: \\u201cLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\\u201d The purpose of lorem ipsum is to create a natural looking block of text (sentence, paragraph, page, etc.) that doesn\'t distract from the layout. A practice not without controversy, laying out pages with meaningless filler text can be very useful when the focus is meant to be on design, not content. The passage experienced a surge in popularity during the 1960s when Letraset used it on their dry-transfer sheets, and again during the 90s as desktop publishers bundled the text with their software. Today it\'s seen all around the web; on templates, websites, and stock designs. Use our generator to get your own, or read on for the authoritative history of lorem ipsum<br \\/><\\/div>\"}', NULL, 'basic', 'from-its-medieval-origins-to-the-digital-era-learn-everything-there-is-to-know-about-the-ubiquitous-lorem-ipsum-passage', '2023-01-15 06:56:19', '2023-01-15 06:56:50'),
(57, 'subscribe.content', '{\"heading\":\"Subscribe To Get Notification\",\"subheading\":\"Lorem ipsum dolor sit amet consectetur adipisicing elit consectetur adipisicing elit  sit amet con m ipsum\"}', NULL, 'basic', NULL, '2023-01-22 14:23:01', '2023-01-22 14:23:01'),
(58, 'faq.element', '{\"question\":\"Lorem ipsum dolor sit amet, consectetur adipisc\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-25 14:15:54', '2023-01-25 14:16:01'),
(59, 'faq.element', '{\"question\":\"At vero eos et accusamus et iusto odio dignissimos duci\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-25 14:16:16', '2023-01-25 14:16:16'),
(60, 'faq.element', '{\"question\":\"On the other hand, we denounce with righteous\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-25 14:16:41', '2023-01-25 14:16:41'),
(61, 'faq.element', '{\"question\":\"Nemo enim ipsam voluptatem quia voluptas sit aspernatur\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-25 14:17:00', '2023-01-25 14:17:00'),
(62, 'faq.element', '{\"question\":\"Contrary to popular belief, Lorem Ipsum is not simply\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-25 14:20:28', '2023-01-25 14:20:28'),
(63, 'faq.element', '{\"question\":\"There are many variations of passages of Lorem Ipsum\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-25 14:20:36', '2023-01-25 14:20:36'),
(64, 'faq.element', '{\"question\":\"In no small part, the importance of FAQ pages\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-25 14:20:58', '2023-01-25 14:20:58'),
(65, 'faq.element', '{\"question\":\"Frequently gets updated based on new data insights\",\"answer\":\"In no small part, the importance of FAQ pages has been driven in recent years by the growth in voice search, mobile search, and personal\\/home assistants and speakers\"}', NULL, 'basic', NULL, '2023-01-25 14:21:14', '2023-01-25 14:21:14'),
(66, 'blog.element', '{\"title\":\"The point of using Lorem Ipsum is that it has a more-or-less no\",\"description\":\"<div style=\\\"color:rgb(80,80,80);font-family:Montserrat, sans-serif;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;\\\">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various\\u00a0<\\/span><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);\\\">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).<\\/span>versions have evolved over the yea<\\/span><\\/div><div style=\\\"color:rgb(80,80,80);font-family:Montserrat, sans-serif;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;\\\"><br \\/><\\/span><\\/div><div style=\\\"color:rgb(80,80,80);font-family:Montserrat, sans-serif;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);\\\">es and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various\\u00a0<\\/span><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);\\\">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).<\\/span>versions have evolved over the\\u00a0<\\/span>rs, sometimes by accident, sometimes on purpose (injected humour and the like)<\\/span><\\/div>\"}', NULL, 'basic', 'the-point-of-using-lorem-ipsum-is-that-it-has-a-more-or-less-no', '2023-01-25 14:22:29', '2023-01-25 14:22:29'),
(67, 'blog.element', '{\"title\":\"It is a long established fact that a reader will be distracted by the readable content of a\",\"description\":\"<div style=\\\"color:rgb(80,80,80);font-family:Montserrat, sans-serif;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;\\\">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various\\u00a0<\\/span><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);\\\">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).<\\/span>versions have evolved over the yea<\\/span><\\/div><div style=\\\"color:rgb(80,80,80);font-family:Montserrat, sans-serif;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;\\\"><br \\/><\\/span><\\/div><div style=\\\"color:rgb(80,80,80);font-family:Montserrat, sans-serif;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);\\\">es and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various\\u00a0<\\/span><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);\\\"><span style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;color:rgb(0,0,0);\\\">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).<\\/span>versions have evolved over the\\u00a0<\\/span>rs, sometimes by accident, sometimes on purpose (injected humour and the like).<\\/span><\\/div>\"}', NULL, 'basic', 'it-is-a-long-established-fact-that-a-reader-will-be-distracted-by-the-readable-content-of-a', '2023-01-25 14:22:49', '2023-01-25 14:22:49'),
(68, 'blog.element', '{\"title\":\"Mollitia saepe ipsam nihil soluta quaerat vitae commodi placeat.\",\"description\":\"<span style=\\\"color:rgb(102,102,102);font-family:arial;font-size:medium;text-align:justify;\\\">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere<\\/span><br \\/>\"}', NULL, 'basic', 'mollitia-saepe-ipsam-nihil-soluta-quaerat-vitae-commodi-placeat', '2023-01-25 14:23:34', '2023-01-25 14:23:34'),
(69, 'banned.content', '{\"heading\":\"This account is Banned\",\"has_image\":\"1\",\"image\":\"63da811a517721675264282.png\"}', NULL, 'basic', NULL, '2023-02-01 14:14:07', '2023-02-01 15:11:22'),
(70, 'maintenance.data', '{\"has_image\":\"1\",\"heading\":\"THE SITE IS UNDER MAINTENANCE\",\"description\":\"<h2 style=\\\"text-align:center;\\\"><font size=\\\"6\\\">We\'re just tuning up a few things.<\\/font><\\/h2><p>We apologize for the inconvenience but Front is currently undergoing planned maintenance. Thanks for your patience.<\\/p>\",\"image\":\"63da8490c1fb91675265168.png\"}', NULL, NULL, NULL, '2023-02-01 15:25:38', '2023-02-01 15:26:48'),
(71, 'kyc.content', '{\"kyc_required\":\"You are not KYC verified. For being KYC verified.\",\"kyc_pending\":\"Your documents for KYC verification is under review. Please wait for admin approval\",\"kyc_reject\":\"Your KYC document has been rejected. Please resubmit the document for further review.\"}', NULL, 'basic', NULL, '2023-02-06 09:09:41', '2023-02-06 09:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `gateways`
--

CREATE TABLE `gateways` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code` int(10) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) NOT NULL DEFAULT 'NULL',
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `gateway_parameters` text DEFAULT NULL,
  `supported_currencies` text DEFAULT NULL,
  `crypto` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: fiat currency, 1: crypto currency',
  `extra` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gateways`
--

INSERT INTO `gateways` (`id`, `form_id`, `code`, `name`, `alias`, `image`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `created_at`, `updated_at`) VALUES
(1, 0, 101, 'Paypal', 'Paypal', '663a38d7b455d1715091671.png', 1, '{\"paypal_email\":{\"title\":\"PayPal Email\",\"global\":true,\"value\":\"sb-owud61543012@business.example.com\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:04:38'),
(2, 0, 102, 'Perfect Money', 'PerfectMoney', '663a3920e30a31715091744.png', 1, '{\"passphrase\":{\"title\":\"ALTERNATE PASSPHRASE\",\"global\":true,\"value\":\"hR26aw02Q1eEeUPSIfuwNypXX\"},\"wallet_id\":{\"title\":\"PM Wallet\",\"global\":false,\"value\":\"\"}}', '{\"USD\":\"$\",\"EUR\":\"\\u20ac\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 01:35:33'),
(3, 0, 103, 'Stripe Hosted', 'Stripe', '663a39861cb9d1715091846.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:48:36'),
(4, 0, 104, 'Skrill', 'Skrill', '663a39494c4a91715091785.png', 1, '{\"pay_to_email\":{\"title\":\"Skrill Email\",\"global\":true,\"value\":\"merchant@skrill.com\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"---\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JOD\":\"JOD\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"KWD\":\"KWD\",\"MAD\":\"MAD\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"OMR\":\"OMR\",\"PLN\":\"PLN\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"SAR\":\"SAR\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TND\":\"TND\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\",\"COP\":\"COP\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 01:30:16'),
(5, 0, 105, 'PayTM', 'Paytm', '663a390f601191715091727.png', 1, '{\"MID\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"DIY12386817555501617\"},\"merchant_key\":{\"title\":\"Merchant Key\",\"global\":true,\"value\":\"bKMfNxPPf_QdZppa\"},\"WEBSITE\":{\"title\":\"Paytm Website\",\"global\":true,\"value\":\"DIYtestingweb\"},\"INDUSTRY_TYPE_ID\":{\"title\":\"Industry Type\",\"global\":true,\"value\":\"Retail\"},\"CHANNEL_ID\":{\"title\":\"CHANNEL ID\",\"global\":true,\"value\":\"WEB\"},\"transaction_url\":{\"title\":\"Transaction URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/oltp-web\\/processTransaction\"},\"transaction_status_url\":{\"title\":\"Transaction STATUS URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/paytmchecksum\\/paytmCallback.jsp\"}}', '{\"AUD\":\"AUD\",\"ARS\":\"ARS\",\"BDT\":\"BDT\",\"BRL\":\"BRL\",\"BGN\":\"BGN\",\"CAD\":\"CAD\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"HRK\":\"HRK\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EGP\":\"EGP\",\"EUR\":\"EUR\",\"GEL\":\"GEL\",\"GHS\":\"GHS\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"KES\":\"KES\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"MAD\":\"MAD\",\"NPR\":\"NPR\",\"NZD\":\"NZD\",\"NGN\":\"NGN\",\"NOK\":\"NOK\",\"PKR\":\"PKR\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"ZAR\":\"ZAR\",\"KRW\":\"KRW\",\"LKR\":\"LKR\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"UGX\":\"UGX\",\"UAH\":\"UAH\",\"AED\":\"AED\",\"GBP\":\"GBP\",\"USD\":\"USD\",\"VND\":\"VND\",\"XOF\":\"XOF\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 03:00:44'),
(6, 0, 106, 'Payeer', 'Payeer', '663a38c9e2e931715091657.png', 0, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"866989763\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"7575\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"RUB\":\"RUB\"}', 0, '{\"status\":{\"title\": \"Status URL\",\"value\":\"ipn.Payeer\"}}', NULL, '2019-09-14 13:14:22', '2020-12-28 01:26:58'),
(7, 0, 107, 'PayStack', 'Paystack', '663a38fc814e91715091708.png', 1, '{\"public_key\":{\"title\":\"Public key\",\"global\":true,\"value\":\"pk_test_cd330608eb47970889bca397ced55c1dd5ad3783\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"sk_test_8a0b1f199362d7acc9c390bff72c4e81f74e2ac3\"}}', '{\"USD\":\"USD\",\"NGN\":\"NGN\"}', 0, '{\"callback\":{\"title\": \"Callback URL\",\"value\":\"ipn.Paystack\"},\"webhook\":{\"title\": \"Webhook URL\",\"value\":\"ipn.Paystack\"}}\r\n', NULL, '2019-09-14 13:14:22', '2021-05-21 01:49:51'),
(9, 0, 109, 'Flutterwave', 'Flutterwave', '663a36c2c34d61715091138.png', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"----------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------------------\"},\"encryption_key\":{\"title\":\"Encryption Key\",\"global\":true,\"value\":\"------------------\"}}', '{\"BIF\":\"BIF\",\"CAD\":\"CAD\",\"CDF\":\"CDF\",\"CVE\":\"CVE\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"GHS\":\"GHS\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"KES\":\"KES\",\"LRD\":\"LRD\",\"MWK\":\"MWK\",\"MZN\":\"MZN\",\"NGN\":\"NGN\",\"RWF\":\"RWF\",\"SLL\":\"SLL\",\"STD\":\"STD\",\"TZS\":\"TZS\",\"UGX\":\"UGX\",\"USD\":\"USD\",\"XAF\":\"XAF\",\"XOF\":\"XOF\",\"ZMK\":\"ZMK\",\"ZMW\":\"ZMW\",\"ZWD\":\"ZWD\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-06-05 11:37:45'),
(10, 0, 110, 'RazorPay', 'Razorpay', '663a393a527831715091770.png', 1, '{\"key_id\":{\"title\":\"Key Id\",\"global\":true,\"value\":\"rzp_test_kiOtejPbRZU90E\"},\"key_secret\":{\"title\":\"Key Secret \",\"global\":true,\"value\":\"osRDebzEqbsE1kbyQJ4y0re7\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:51:32'),
(11, 0, 111, 'Stripe Storefront', 'StripeJs', '663a3995417171715091861.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:53:10'),
(12, 0, 112, 'Instamojo', 'Instamojo', '663a384d54a111715091533.png', 1, '{\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_2241633c3bc44a3de84a3b33969\"},\"auth_token\":{\"title\":\"Auth Token\",\"global\":true,\"value\":\"test_279f083f7bebefd35217feef22d\"},\"salt\":{\"title\":\"Salt\",\"global\":true,\"value\":\"19d38908eeff4f58b2ddda2c6d86ca25\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:56:20'),
(13, 0, 501, 'Blockchain', 'Blockchain', '663a35efd0c311715090927.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"55529946-05ca-48ff-8710-f279d86b1cc5\"},\"xpub_code\":{\"title\":\"XPUB CODE\",\"global\":true,\"value\":\"xpub6CKQ3xxWyBoFAF83izZCSFUorptEU9AF8TezhtWeMU5oefjX3sFSBw62Lr9iHXPkXmDQJJiHZeTRtD9Vzt8grAYRhvbz4nEvBu3QKELVzFK\"}}', '{\"BTC\":\"BTC\"}', 1, NULL, NULL, '2019-09-14 13:14:22', '2022-03-21 07:41:56'),
(15, 0, 503, 'CoinPayments', 'Coinpayments', '663a36a8d8e1d1715091112.png', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"---------------\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"------------\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"93a1e014c4ad60a7980b4a7239673cb4\"}}', '{\"BTC\":\"Bitcoin\",\"BTC.LN\":\"Bitcoin (Lightning Network)\",\"LTC\":\"Litecoin\",\"CPS\":\"CPS Coin\",\"VLX\":\"Velas\",\"APL\":\"Apollo\",\"AYA\":\"Aryacoin\",\"BAD\":\"Badcoin\",\"BCD\":\"Bitcoin Diamond\",\"BCH\":\"Bitcoin Cash\",\"BCN\":\"Bytecoin\",\"BEAM\":\"BEAM\",\"BITB\":\"Bean Cash\",\"BLK\":\"BlackCoin\",\"BSV\":\"Bitcoin SV\",\"BTAD\":\"Bitcoin Adult\",\"BTG\":\"Bitcoin Gold\",\"BTT\":\"BitTorrent\",\"CLOAK\":\"CloakCoin\",\"CLUB\":\"ClubCoin\",\"CRW\":\"Crown\",\"CRYP\":\"CrypticCoin\",\"CRYT\":\"CryTrExCoin\",\"CURE\":\"CureCoin\",\"DASH\":\"DASH\",\"DCR\":\"Decred\",\"DEV\":\"DeviantCoin\",\"DGB\":\"DigiByte\",\"DOGE\":\"Dogecoin\",\"EBST\":\"eBoost\",\"EOS\":\"EOS\",\"ETC\":\"Ether Classic\",\"ETH\":\"Ethereum\",\"ETN\":\"Electroneum\",\"EUNO\":\"EUNO\",\"EXP\":\"EXP\",\"Expanse\":\"Expanse\",\"FLASH\":\"FLASH\",\"GAME\":\"GameCredits\",\"GLC\":\"Goldcoin\",\"GRS\":\"Groestlcoin\",\"KMD\":\"Komodo\",\"LOKI\":\"LOKI\",\"LSK\":\"LSK\",\"MAID\":\"MaidSafeCoin\",\"MUE\":\"MonetaryUnit\",\"NAV\":\"NAV Coin\",\"NEO\":\"NEO\",\"NMC\":\"Namecoin\",\"NVST\":\"NVO Token\",\"NXT\":\"NXT\",\"OMNI\":\"OMNI\",\"PINK\":\"PinkCoin\",\"PIVX\":\"PIVX\",\"POT\":\"PotCoin\",\"PPC\":\"Peercoin\",\"PROC\":\"ProCurrency\",\"PURA\":\"PURA\",\"QTUM\":\"QTUM\",\"RES\":\"Resistance\",\"RVN\":\"Ravencoin\",\"RVR\":\"RevolutionVR\",\"SBD\":\"Steem Dollars\",\"SMART\":\"SmartCash\",\"SOXAX\":\"SOXAX\",\"STEEM\":\"STEEM\",\"STRAT\":\"STRAT\",\"SYS\":\"Syscoin\",\"TPAY\":\"TokenPay\",\"TRIGGERS\":\"Triggers\",\"TRX\":\" TRON\",\"UBQ\":\"Ubiq\",\"UNIT\":\"UniversalCurrency\",\"USDT\":\"Tether USD (Omni Layer)\",\"USDT.BEP20\":\"Tether USD (BSC Chain)\",\"USDT.ERC20\":\"Tether USD (ERC20)\",\"USDT.TRC20\":\"Tether USD (Tron/TRC20)\",\"VTC\":\"Vertcoin\",\"WAVES\":\"Waves\",\"XCP\":\"Counterparty\",\"XEM\":\"NEM\",\"XMR\":\"Monero\",\"XSN\":\"Stakenet\",\"XSR\":\"SucreCoin\",\"XVG\":\"VERGE\",\"XZC\":\"ZCoin\",\"ZEC\":\"ZCash\",\"ZEN\":\"Horizen\"}', 1, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:07:14'),
(16, 0, 504, 'CoinPayments Fiat', 'CoinpaymentsFiat', '663a36b7b841a1715091127.png', 1, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"6515561\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:07:44'),
(17, 0, 505, 'Coingate', 'Coingate', '663a368e753381715091086.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"6354mwVCEw5kHzRJ6thbGo-N\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2022-03-30 09:24:57'),
(18, 0, 506, 'Coinbase Commerce', 'CoinbaseCommerce', '663a367e46ae51715091070.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"c47cd7df-d8e8-424b-a20a\"},\"secret\":{\"title\":\"Webhook Shared Secret\",\"global\":true,\"value\":\"55871878-2c32-4f64-ab66\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"JPY\":\"JPY\",\"GBP\":\"GBP\",\"AUD\":\"AUD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CNY\":\"CNY\",\"SEK\":\"SEK\",\"NZD\":\"NZD\",\"MXN\":\"MXN\",\"SGD\":\"SGD\",\"HKD\":\"HKD\",\"NOK\":\"NOK\",\"KRW\":\"KRW\",\"TRY\":\"TRY\",\"RUB\":\"RUB\",\"INR\":\"INR\",\"BRL\":\"BRL\",\"ZAR\":\"ZAR\",\"AED\":\"AED\",\"AFN\":\"AFN\",\"ALL\":\"ALL\",\"AMD\":\"AMD\",\"ANG\":\"ANG\",\"AOA\":\"AOA\",\"ARS\":\"ARS\",\"AWG\":\"AWG\",\"AZN\":\"AZN\",\"BAM\":\"BAM\",\"BBD\":\"BBD\",\"BDT\":\"BDT\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"BIF\":\"BIF\",\"BMD\":\"BMD\",\"BND\":\"BND\",\"BOB\":\"BOB\",\"BSD\":\"BSD\",\"BTN\":\"BTN\",\"BWP\":\"BWP\",\"BYN\":\"BYN\",\"BZD\":\"BZD\",\"CDF\":\"CDF\",\"CLF\":\"CLF\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"CRC\":\"CRC\",\"CUC\":\"CUC\",\"CUP\":\"CUP\",\"CVE\":\"CVE\",\"CZK\":\"CZK\",\"DJF\":\"DJF\",\"DKK\":\"DKK\",\"DOP\":\"DOP\",\"DZD\":\"DZD\",\"EGP\":\"EGP\",\"ERN\":\"ERN\",\"ETB\":\"ETB\",\"FJD\":\"FJD\",\"FKP\":\"FKP\",\"GEL\":\"GEL\",\"GGP\":\"GGP\",\"GHS\":\"GHS\",\"GIP\":\"GIP\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"GTQ\":\"GTQ\",\"GYD\":\"GYD\",\"HNL\":\"HNL\",\"HRK\":\"HRK\",\"HTG\":\"HTG\",\"HUF\":\"HUF\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"IMP\":\"IMP\",\"IQD\":\"IQD\",\"IRR\":\"IRR\",\"ISK\":\"ISK\",\"JEP\":\"JEP\",\"JMD\":\"JMD\",\"JOD\":\"JOD\",\"KES\":\"KES\",\"KGS\":\"KGS\",\"KHR\":\"KHR\",\"KMF\":\"KMF\",\"KPW\":\"KPW\",\"KWD\":\"KWD\",\"KYD\":\"KYD\",\"KZT\":\"KZT\",\"LAK\":\"LAK\",\"LBP\":\"LBP\",\"LKR\":\"LKR\",\"LRD\":\"LRD\",\"LSL\":\"LSL\",\"LYD\":\"LYD\",\"MAD\":\"MAD\",\"MDL\":\"MDL\",\"MGA\":\"MGA\",\"MKD\":\"MKD\",\"MMK\":\"MMK\",\"MNT\":\"MNT\",\"MOP\":\"MOP\",\"MRO\":\"MRO\",\"MUR\":\"MUR\",\"MVR\":\"MVR\",\"MWK\":\"MWK\",\"MYR\":\"MYR\",\"MZN\":\"MZN\",\"NAD\":\"NAD\",\"NGN\":\"NGN\",\"NIO\":\"NIO\",\"NPR\":\"NPR\",\"OMR\":\"OMR\",\"PAB\":\"PAB\",\"PEN\":\"PEN\",\"PGK\":\"PGK\",\"PHP\":\"PHP\",\"PKR\":\"PKR\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"RWF\":\"RWF\",\"SAR\":\"SAR\",\"SBD\":\"SBD\",\"SCR\":\"SCR\",\"SDG\":\"SDG\",\"SHP\":\"SHP\",\"SLL\":\"SLL\",\"SOS\":\"SOS\",\"SRD\":\"SRD\",\"SSP\":\"SSP\",\"STD\":\"STD\",\"SVC\":\"SVC\",\"SYP\":\"SYP\",\"SZL\":\"SZL\",\"THB\":\"THB\",\"TJS\":\"TJS\",\"TMT\":\"TMT\",\"TND\":\"TND\",\"TOP\":\"TOP\",\"TTD\":\"TTD\",\"TWD\":\"TWD\",\"TZS\":\"TZS\",\"UAH\":\"UAH\",\"UGX\":\"UGX\",\"UYU\":\"UYU\",\"UZS\":\"UZS\",\"VEF\":\"VEF\",\"VND\":\"VND\",\"VUV\":\"VUV\",\"WST\":\"WST\",\"XAF\":\"XAF\",\"XAG\":\"XAG\",\"XAU\":\"XAU\",\"XCD\":\"XCD\",\"XDR\":\"XDR\",\"XOF\":\"XOF\",\"XPD\":\"XPD\",\"XPF\":\"XPF\",\"XPT\":\"XPT\",\"YER\":\"YER\",\"ZMW\":\"ZMW\",\"ZWL\":\"ZWL\"}\r\n\r\n', 0, '{\"endpoint\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.CoinbaseCommerce\"}}', NULL, '2019-09-14 13:14:22', '2021-05-21 02:02:47'),
(24, 0, 113, 'Paypal Express', 'PaypalSdk', '663a38ed101a61715091693.png', 1, '{\"clientId\":{\"title\":\"Paypal Client ID\",\"global\":true,\"value\":\"Ae0-tixtSV7DvLwIh3Bmu7JvHrjh5EfGdXr_cEklKAVjjezRZ747BxKILiBdzlKKyp-W8W_T7CKH1Ken\"},\"clientSecret\":{\"title\":\"Client Secret\",\"global\":true,\"value\":\"EOhbvHZgFNO21soQJT1L9Q00M3rK6PIEsdiTgXRBt2gtGtxwRer5JvKnVUGNU5oE63fFnjnYY7hq3HBA\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-20 23:01:08'),
(25, 0, 114, 'Stripe Checkout', 'StripeV3', '663a39afb519f1715091887.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"},\"end_point\":{\"title\":\"End Point Secret\",\"global\":true,\"value\":\"whsec_lUmit1gtxwKTveLnSe88xCSDdnPOt8g5\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, '{\"webhook\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.StripeV3\"}}', NULL, '2019-09-14 13:14:22', '2021-05-21 00:58:38'),
(27, 0, 115, 'Mollie', 'Mollie', '663a387ec69371715091582.png', 1, '{\"mollie_email\":{\"title\":\"Mollie Email \",\"global\":true,\"value\":\"vi@gmail.com\"},\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_cucfwKTWfft9s337qsVfn5CC4vNkrn\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:44:45'),
(30, 0, 116, 'Cashmaal', 'Cashmaal', '663a361b16bd11715090971.png', 1, '{\"web_id\":{\"title\":\"Web Id\",\"global\":true,\"value\":\"3748\"},\"ipn_key\":{\"title\":\"IPN Key\",\"global\":true,\"value\":\"546254628759524554647987\"}}', '{\"PKR\":\"PKR\",\"USD\":\"USD\"}', 0, '{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.Cashmaal\"}}', NULL, NULL, '2021-06-22 08:05:04'),
(36, 0, 119, 'Mercado Pago', 'MercadoPago', '663a386c714a91715091564.png', 1, '{\"access_token\":{\"title\":\"Access Token\",\"global\":true,\"value\":\"3Vee5S2F\"}}', '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, '2021-07-17 09:44:29'),
(44, 0, 120, 'Authorize.net', 'Authorize', '663a35b9ca5991715090873.png', 1, '{\"login_id\":{\"title\":\"Login ID\",\"global\":true,\"value\":\"3Vee5S2F\"},\"transaction_key\":{\"title\":\"Transaction Key\",\"global\":true,\"value\":\"3Vee5S2F\"}}', '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, '2021-07-17 09:44:29'),
(46, 0, 121, 'NMI', 'NMI', '663a3897754cf1715091607.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"-------\"}}', '{\"AED\":\"AED\",\"ARS\":\"ARS\",\"AUD\":\"AUD\",\"BOB\":\"BOB\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"RUB\":\"RUB\",\"SEC\":\"SEC\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, NULL, '2022-08-28 10:12:37'),
(51, 0, 507, 'BTCPay', 'BTCPay', '663a35cd25a8d1715090893.png', 1, '{\"store_id\":{\"title\":\"Store Id\",\"global\":true,\"value\":\"-------\"},\"api_key\":{\"title\":\"Api Key\",\"global\":true,\"value\":\"------\"},\"server_name\":{\"title\":\"Server Name\",\"global\":true,\"value\":\"https:\\/\\/yourbtcpaserver.lndyn.com\"},\"secret_code\":{\"title\":\"Secret Code\",\"global\":true,\"value\":\"----------\"}}', '{\"BTC\":\"Bitcoin\",\"LTC\":\"Litecoin\"}', 1, '{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.BTCPay\"}}', NULL, NULL, NULL),
(52, 0, 508, 'Now payments hosted', 'NowPaymentsHosted', '663a38b8d57a81715091640.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"-------------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"--------------\"}}', '{\"BTG\":\"BTG\",\"ETH\":\"ETH\",\"XMR\":\"XMR\",\"ZEC\":\"ZEC\",\"XVG\":\"XVG\",\"ADA\":\"ADA\",\"LTC\":\"LTC\",\"BCH\":\"BCH\",\"QTUM\":\"QTUM\",\"DASH\":\"DASH\",\"XLM\":\"XLM\",\"XRP\":\"XRP\",\"XEM\":\"XEM\",\"DGB\":\"DGB\",\"LSK\":\"LSK\",\"DOGE\":\"DOGE\",\"TRX\":\"TRX\",\"KMD\":\"KMD\",\"REP\":\"REP\",\"BAT\":\"BAT\",\"ARK\":\"ARK\",\"WAVES\":\"WAVES\",\"BNB\":\"BNB\",\"XZC\":\"XZC\",\"NANO\":\"NANO\",\"TUSD\":\"TUSD\",\"VET\":\"VET\",\"ZEN\":\"ZEN\",\"GRS\":\"GRS\",\"FUN\":\"FUN\",\"NEO\":\"NEO\",\"GAS\":\"GAS\",\"PAX\":\"PAX\",\"USDC\":\"USDC\",\"ONT\":\"ONT\",\"XTZ\":\"XTZ\",\"LINK\":\"LINK\",\"RVN\":\"RVN\",\"BNBMAINNET\":\"BNBMAINNET\",\"ZIL\":\"ZIL\",\"BCD\":\"BCD\",\"USDT\":\"USDT\",\"USDTERC20\":\"USDTERC20\",\"CRO\":\"CRO\",\"DAI\":\"DAI\",\"HT\":\"HT\",\"WABI\":\"WABI\",\"BUSD\":\"BUSD\",\"ALGO\":\"ALGO\",\"USDTTRC20\":\"USDTTRC20\",\"GT\":\"GT\",\"STPT\":\"STPT\",\"AVA\":\"AVA\",\"SXP\":\"SXP\",\"UNI\":\"UNI\",\"OKB\":\"OKB\",\"BTC\":\"BTC\"}', 1, '', NULL, NULL, '2023-02-14 10:42:09'),
(53, 0, 509, 'Now payments checkout', 'NowPaymentsCheckout', '663a38a59d2541715091621.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"-------------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"--------------\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\"}', 1, '', NULL, NULL, '2023-02-14 10:42:09'),
(54, 0, 122, '2Checkout', 'TwoCheckout', '663a39b8e64b91715091896.png', 1, '{\"merchant_code\": {\"title\": \"Merchant Code\",\"global\": true,\"value\": \"---------\"},\"secret_key\": {\"title\": \"Secret Key\",\"global\": true,\"value\": \"--------\"}}', '{\"AFN\": \"AFN\",\"ALL\": \"ALL\",\"DZD\": \"DZD\",\"ARS\": \"ARS\",\"AUD\": \"AUD\",\"AZN\": \"AZN\",\"BSD\": \"BSD\",\"BDT\": \"BDT\",\"BBD\": \"BBD\",\"BZD\": \"BZD\",\"BMD\": \"BMD\",\"BOB\": \"BOB\",\"BWP\": \"BWP\",\"BRL\": \"BRL\",\"GBP\": \"GBP\",\"BND\": \"BND\",\"BGN\": \"BGN\",\"CAD\": \"CAD\",\"CLP\": \"CLP\",\"CNY\": \"CNY\",\"COP\": \"COP\",\"CRC\": \"CRC\",\"HRK\": \"HRK\",\"CZK\": \"CZK\",\"DKK\": \"DKK\",\"DOP\": \"DOP\",\"XCD\": \"XCD\",\"EGP\": \"EGP\",\"EUR\": \"EUR\",\"FJD\": \"FJD\",\"GTQ\": \"GTQ\",\"HKD\": \"HKD\",\"HNL\": \"HNL\",\"HUF\": \"HUF\",\"INR\": \"INR\",\"IDR\": \"IDR\",\"ILS\": \"ILS\",\"JMD\": \"JMD\",\"JPY\": \"JPY\",\"KZT\": \"KZT\",\"KES\": \"KES\",\"LAK\": \"LAK\",\"MMK\": \"MMK\",\"LBP\": \"LBP\",\"LRD\": \"LRD\",\"MOP\": \"MOP\",\"MYR\": \"MYR\",\"MVR\": \"MVR\",\"MRO\": \"MRO\",\"MUR\": \"MUR\",\"MXN\": \"MXN\",\"MAD\": \"MAD\",\"NPR\": \"NPR\",\"TWD\": \"TWD\",\"NZD\": \"NZD\",\"NIO\": \"NIO\",\"NOK\": \"NOK\",\"PKR\": \"PKR\",\"PGK\": \"PGK\",\"PEN\": \"PEN\",\"PHP\": \"PHP\",\"PLN\": \"PLN\",\"QAR\": \"QAR\",\"RON\": \"RON\",\"RUB\": \"RUB\",\"WST\": \"WST\",\"SAR\": \"SAR\",\"SCR\": \"SCR\",\"SGD\": \"SGD\",\"SBD\": \"SBD\",\"ZAR\": \"ZAR\",\"KRW\": \"KRW\",\"LKR\": \"LKR\",\"SEK\": \"SEK\",\"CHF\": \"CHF\",\"SYP\": \"SYP\",\"THB\": \"THB\",\"TOP\": \"TOP\",\"TTD\": \"TTD\",\"TRY\": \"TRY\",\"UAH\": \"UAH\",\"AED\": \"AED\",\"USD\": \"USD\",\"VUV\": \"VUV\",\"VND\": \"VND\",\"XOF\": \"XOF\",\"YER\": \"YER\"}', 0, '{\"approved_url\":{\"title\": \"Approved URL\",\"value\":\"ipn.TwoCheckout\"}}', NULL, NULL, '2023-02-14 10:42:09'),
(55, 0, 123, 'Checkout', 'Checkout', '663a3628733351715090984.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_f7f9a069-dcc5-45d8-aa72-e60f605c9514\"},\"public_key\":{\"title\":\"PUBLIC KEY\",\"global\":true,\"value\":\"pk_66e19b3f-a431-44ff-823f-d773d960f6b9\"},\"processing_channel_id\":{\"title\":\"PROCESSING CHANNEL\",\"global\":true,\"value\":\"---\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"AUD\":\"AUD\",\"CAN\":\"CAN\",\"CHF\":\"CHF\",\"SGD\":\"SGD\",\"JPY\":\"JPY\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, NULL),
(56, 0, 510, 'Binance', 'Binance', '663a35db4fd621715090907.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"tsu3tjiq0oqfbtmlbevoeraxhfbp3brejnm9txhjxcp4to29ujvakvfl1ibsn3ja\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"jzngq4t04ltw8d4iqpi7admfl8tvnpehxnmi34id1zvfaenbwwvsvw7llw3zdko8\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"231129033\"}}', '{\"BTC\":\"Bitcoin\",\"USD\":\"USD\",\"BNB\":\"BNB\"}', 1, '{\"cron\":{\"title\": \"Cron Job URL\",\"value\":\"ipn.Binance\"}}', NULL, NULL, '2023-02-14 05:08:04'),
(57, 0, 124, 'SslCommerz', 'SslCommerz', '663a397a70c571715091834.png', 1, '{\"store_id\": {\"title\": \"Store ID\",\"global\": true,\"value\": \"---------\"},\"store_password\": {\"title\": \"Store Password\",\"global\": true,\"value\": \"----------\"}}', '{\"BDT\":\"BDT\",\"USD\":\"USD\",\"EUR\":\"EUR\",\"SGD\":\"SGD\",\"INR\":\"INR\",\"MYR\":\"MYR\"}', 0, NULL, NULL, NULL, '2023-05-06 07:43:01'),
(58, 0, 125, 'Aamarpay', 'Aamarpay', '663a34d5d1dfc1715090645.png', 1, '{\"store_id\": {\"title\": \"Store ID\",\"global\": true,\"value\": \"---------\"},\"signature_key\": {\"title\": \"Signature Key\",\"global\": true,\"value\": \"----------\"}}', '{\"BDT\":\"BDT\"}', 0, NULL, NULL, NULL, '2023-05-06 07:43:01');

-- --------------------------------------------------------

--
-- Table structure for table `gateway_currencies`
--

CREATE TABLE `gateway_currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `symbol` varchar(40) DEFAULT NULL,
  `method_code` int(10) DEFAULT NULL,
  `gateway_alias` varchar(40) DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `max_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `gateway_parameter` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE `general_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tax` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'This field signifies the percentage',
  `site_name` varchar(40) DEFAULT NULL,
  `invoice_start` int(10) NOT NULL DEFAULT 1,
  `invoice_increment` int(10) NOT NULL DEFAULT 1,
  `deposit_module` tinyint(1) DEFAULT 1,
  `auto_domain_register` tinyint(1) NOT NULL DEFAULT 1,
  `last_cron` varchar(40) DEFAULT NULL,
  `available_version` varchar(40) DEFAULT '1.0',
  `cur_text` varchar(40) DEFAULT NULL COMMENT 'currency text',
  `cur_sym` varchar(40) DEFAULT NULL COMMENT 'currency symbol',
  `email_from` varchar(40) DEFAULT NULL,
  `email_from_name` varchar(255) DEFAULT NULL,
  `email_template` text DEFAULT NULL,
  `sms_template` varchar(255) DEFAULT NULL,
  `sms_from` varchar(255) DEFAULT NULL,
  `push_title` varchar(255) DEFAULT NULL,
  `push_template` varchar(255) DEFAULT NULL,
  `base_color` varchar(40) DEFAULT NULL,
  `mail_config` text DEFAULT NULL COMMENT 'email configuration',
  `sms_config` text DEFAULT NULL,
  `firebase_config` text DEFAULT NULL,
  `global_shortcodes` text DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0,
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email verification, 0 - dont check, 1 - check',
  `en` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email notification, 0 - dont send, 1 - send',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'mobile verication, 0 - dont check, 1 - check',
  `sn` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'sms notification, 0 - dont send, 1 - send',
  `pn` tinyint(1) NOT NULL DEFAULT 1,
  `force_ssl` tinyint(1) NOT NULL DEFAULT 0,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `secure_password` tinyint(1) NOT NULL DEFAULT 0,
  `agree` tinyint(1) NOT NULL DEFAULT 0,
  `multi_language` tinyint(1) NOT NULL DEFAULT 1,
  `registration` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Off	, 1: On',
  `show_livechat_user_panel` tinyint(1) NOT NULL DEFAULT 1,
  `active_template` varchar(40) DEFAULT NULL,
  `socialite_credentials` text DEFAULT NULL,
  `system_customized` tinyint(1) NOT NULL DEFAULT 0,
  `paginate_number` int(11) NOT NULL DEFAULT 0,
  `currency_format` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>Both\r\n2=>Text Only\r\n3=>Symbol Only',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`id`, `tax`, `site_name`, `invoice_start`, `invoice_increment`, `deposit_module`, `auto_domain_register`, `last_cron`, `available_version`, `cur_text`, `cur_sym`, `email_from`, `email_from_name`, `email_template`, `sms_template`, `sms_from`, `push_title`, `push_template`, `base_color`, `mail_config`, `sms_config`, `firebase_config`, `global_shortcodes`, `kv`, `ev`, `en`, `sv`, `sn`, `pn`, `force_ssl`, `maintenance_mode`, `secure_password`, `agree`, `multi_language`, `registration`, `show_livechat_user_panel`, `active_template`, `socialite_credentials`, `system_customized`, `paginate_number`, `currency_format`, `created_at`, `updated_at`) VALUES
(1, 0.00, 'ZolaHost', 500, 1, 0, 0, '2023-02-04 20:45:16', '1.0', 'USD', '$', 'info@viserlab.com', NULL, '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello {{fullname}} ({{username}})</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">{{message}}</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          © 2021 <a href=\"#\">{{site_name}}</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>', 'hi {{fullname}} ({{username}}), {{message}}', 'ViserAdmin', NULL, NULL, '2563EB', '{\"name\":\"php\"}', '{\"name\":\"nexmo\",\"clickatell\":{\"api_key\":\"----------------\"},\"infobip\":{\"username\":\"------------8888888\",\"password\":\"-----------------\"},\"message_bird\":{\"api_key\":\"-------------------\"},\"nexmo\":{\"api_key\":\"----------------------\",\"api_secret\":\"----------------------\"},\"sms_broadcast\":{\"username\":\"----------------------\",\"password\":\"-----------------------------\"},\"twilio\":{\"account_sid\":\"-----------------------\",\"auth_token\":\"---------------------------\",\"from\":\"----------------------\"},\"text_magic\":{\"username\":\"-----------------------\",\"apiv2_key\":\"-------------------------------\"},\"custom\":{\"method\":\"get\",\"url\":\"https:\\/\\/hostname\\/demo-api-v1\",\"headers\":{\"name\":[\"api_key\"],\"value\":[\"test_api 555\"]},\"body\":{\"name\":[\"from_number\"],\"value\":[\"5657545757\"]}}}', NULL, '{\n    \"site_name\":\"Name of your site\",\n    \"site_currency\":\"Currency of your site\",\n    \"currency_symbol\":\"Symbol of currency\"\n}', 0, 0, 1, 0, 0, 1, 0, 0, 0, 1, 1, 1, 1, 'basic', '{"google":{\"client_id\":\"------------\",\"client_secret\":\"-------------\",\"status\":1},\"facebook\":{\"client_id\":\"------\",\"client_secret\":\"------\",\"status\":1},\"linkedin\":{\"client_id\":\"-----\",\"client_secret\":\"-----\",\"status\":1}}', 0, 0, 0, NULL, '2024-06-29 04:32:38');

-- --------------------------------------------------------

--
-- Table structure for table `hostings`
--

CREATE TABLE `hostings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice` tinyint(1) NOT NULL DEFAULT 1 COMMENT '	0=> Means empty invoice/allow creating a new invoice, 1=> Already created',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `server_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `deposit_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `domain_setup_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '	TLD / Domain setup id',
  `domain` varchar(255) DEFAULT NULL,
  `package_name` varchar(255) DEFAULT NULL COMMENT 'From WHM API',
  `first_payment_amount` decimal(28,18) NOT NULL DEFAULT 0.000000000000000000,
  `recurring_amount` decimal(28,18) NOT NULL DEFAULT 0.000000000000000000,
  `setup_fee` decimal(28,8) NOT NULL DEFAULT 0.00000000 COMMENT 'Additional price / amount',
  `discount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `status` tinyint(1) NOT NULL DEFAULT 2,
  `billing_cycle` tinyint(1) NOT NULL DEFAULT 1,
  `stock_control` tinyint(1) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `ns1` varchar(255) DEFAULT NULL,
  `ns2` varchar(255) DEFAULT NULL,
  `ns3` varchar(255) DEFAULT NULL,
  `ns4` varchar(255) DEFAULT NULL,
  `ns1_ip` varchar(255) DEFAULT NULL,
  `ns2_ip` varchar(255) DEFAULT NULL,
  `ns3_ip` varchar(255) DEFAULT NULL,
  `ns4_ip` varchar(255) DEFAULT NULL,
  `dedicated_ip` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `assigned_ips` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL COMMENT '	For admin only',
  `suspend_reason` varchar(255) DEFAULT NULL,
  `termination_date` varchar(40) DEFAULT NULL,
  `suspend_date` varchar(40) DEFAULT NULL,
  `next_due_date` varchar(40) DEFAULT NULL,
  `next_invoice_date` varchar(40) DEFAULT NULL,
  `reg_date` varchar(40) DEFAULT NULL COMMENT '	Registration date',
  `last_cron` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hosting_configs`
--

CREATE TABLE `hosting_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hosting_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `configurable_group_option_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `configurable_group_sub_option_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` int(11) DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `hosting_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `domain_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `reminder` text DEFAULT NULL COMMENT 'An object/json for managing the invoice reminding notify process',
  `amount` decimal(28,18) NOT NULL DEFAULT 0.000000000000000000,
  `refund_amount` decimal(28,18) NOT NULL DEFAULT 0.000000000000000000,
  `admin_notes` text DEFAULT NULL COMMENT '	For admin only',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>Paid,\r\n2=>Unpaid,\r\n3=>Payment Pending, \r\n4=>Cancelled,\r\n5=>Refunded',
  `due_date` varchar(40) DEFAULT NULL,
  `paid_date` varchar(40) DEFAULT NULL,
  `next_due_date` varchar(40) DEFAULT NULL,
  `created` varchar(40) DEFAULT NULL COMMENT 'Modify created at',
  `last_cron` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `hosting_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `domain_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=> Hosting, 1=> Domain',
  `description` text NOT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `tax` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'This field signifies the percentage',
  `reg_period` int(11) NOT NULL DEFAULT 0 COMMENT 'For renew domain',
  `trx_type` varchar(40) DEFAULT NULL COMMENT '+ or - ',
  `recurring_amount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `next_due_date` varchar(40) DEFAULT NULL COMMENT 'For renew domain',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `code` varchar(40) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: not default language, 1: default language',
  `image` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `is_default`, `image`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 1, '667fe1f067f1a1719656944.png', '2024-06-29 04:29:04', '2024-06-29 04:29:04');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sender` varchar(40) DEFAULT NULL,
  `sent_from` varchar(40) DEFAULT NULL,
  `sent_to` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `notification_type` varchar(40) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_read` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `push_title` varchar(255) DEFAULT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `push_body` text DEFAULT NULL,
  `shortcodes` text DEFAULT NULL,
  `email_status` tinyint(1) NOT NULL DEFAULT 1,
  `email_sent_from_name` varchar(40) DEFAULT NULL,
  `email_sent_from_address` varchar(40) DEFAULT NULL,
  `sms_status` tinyint(1) NOT NULL DEFAULT 1,
  `sms_sent_from` varchar(40) DEFAULT NULL,
  `push_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_templates`
--

INSERT INTO `notification_templates` (`id`, `act`, `name`, `subject`, `push_title`, `email_body`, `sms_body`, `push_body`, `shortcodes`, `email_status`, `email_sent_from_name`, `email_sent_from_address`, `sms_status`, `sms_sent_from`, `push_status`, `created_at`, `updated_at`) VALUES
(1, 'BAL_ADD', 'Balance - Added', 'Your Account has been Credited', NULL, '<div><div style=\"font-family: Montserrat, sans-serif;\">{{amount}} {{site_currency}} has been added to your account .</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><span style=\"color: rgb(33, 37, 41); font-family: Montserrat, sans-serif;\">Your Current Balance is :&nbsp;</span><font style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">{{post_balance}}&nbsp; {{site_currency}}&nbsp;</span></font><br></div><div><font style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></font></div><div>Admin note:&nbsp;<span style=\"color: rgb(33, 37, 41); font-size: 12px; font-weight: 600; white-space: nowrap; text-align: var(--bs-body-text-align);\">{{remark}}</span></div>', '{{amount}} {{site_currency}} credited in your account. Your Current Balance {{post_balance}} {{site_currency}} . Transaction: #{{trx}}. Admin note is \"{{remark}}\"', NULL, '{\"trx\":\"Transaction number for the action\",\"amount\":\"Amount inserted by the admin\",\"remark\":\"Remark inserted by the admin\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, NULL, NULL, 0, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:18:28'),
(2, 'BAL_SUB', 'Balance - Subtracted', 'Your Account has been Debited', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">{{amount}} {{site_currency}} has been subtracted from your account .</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><span style=\"color: rgb(33, 37, 41); font-family: Montserrat, sans-serif;\">Your Current Balance is :&nbsp;</span><font style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">{{post_balance}}&nbsp; {{site_currency}}</span></font><br><div><font style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></font></div><div>Admin Note: {{remark}}</div>', '{{amount}} {{site_currency}} debited from your account. Your Current Balance {{post_balance}} {{site_currency}} . Transaction: #{{trx}}. Admin Note is {{remark}}', NULL, '{\"trx\":\"Transaction number for the action\",\"amount\":\"Amount inserted by the admin\",\"remark\":\"Remark inserted by the admin\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:24:11'),
(3, 'DEPOSIT_COMPLETE', 'Deposit - Automated - Successful', 'Deposit Completed Successfully', NULL, '<div>Your deposit of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>has been completed Successfully.<span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#000000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Received : {{method_amount}} {{method_currency}}<br></div><div>Paid via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><font size=\"5\"><span style=\"font-weight: bolder;\"><br></span></font></div><div><font size=\"5\">Your current Balance is&nbsp;<span style=\"font-weight: bolder;\">{{post_balance}} {{site_currency}}</span></font></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>', '{{amount}} {{site_currency}} Deposit successfully by {{method_name}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:25:43'),
(4, 'DEPOSIT_APPROVE', 'Deposit - Manual - Approved', 'Your Deposit is Approved', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>is Approved .<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Amount : {{amount}} {{site_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"5\"><span style=\"font-weight: bolder;\"><br></span></font></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"5\">Your current Balance is&nbsp;<span style=\"font-weight: bolder;\">{{post_balance}} {{site_currency}}</span></font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div>', 'Admin Approve Your {{amount}} {{site_currency}} payment request by {{method_name}} transaction : {{trx}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:26:07'),
(5, 'DEPOSIT_REJECT', 'Deposit - Manual - Rejected', 'Your Deposit Request is Rejected', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}} has been rejected</span>.<span style=\"font-weight: bolder;\"><br></span></div><div><br></div><div><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge: {{charge}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number was : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">if you have any queries, feel free to contact us.<br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><br><br></div><span style=\"color: rgb(33, 37, 41); font-family: Montserrat, sans-serif;\">{{rejection_message}}</span><br>', 'Admin Rejected Your {{amount}} {{site_currency}} payment request by {{method_name}}\r\n\r\n{{rejection_message}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"rejection_message\":\"Rejection message by the admin\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-05 03:45:27'),
(6, 'DEPOSIT_REQUEST', 'Deposit - Manual - Requested', 'Deposit Request Submitted Successfully', NULL, '<div>Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>submitted successfully<span style=\"font-weight: bolder;\">&nbsp;.<br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Payable : {{method_amount}} {{method_currency}}<br></div><div>Pay via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>', '{{amount}} {{site_currency}} Deposit requested by {{method_name}}. Charge: {{charge}} . Trx: {{trx}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:29:19'),
(7, 'PASS_RESET_CODE', 'Password - Reset - Code', 'Password Reset', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">We have received a request to reset the password for your account on&nbsp;<span style=\"font-weight: bolder;\">{{time}} .<br></span></div><div style=\"font-family: Montserrat, sans-serif;\">Requested From IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>.</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><div>Your account recovery code is:&nbsp;&nbsp;&nbsp;<font size=\"6\"><span style=\"font-weight: bolder;\">{{code}}</span></font></div><div><br></div></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"4\" color=\"#CC0000\">If you do not wish to reset your password, please disregard this message.&nbsp;</font><br></div><div><font size=\"4\" color=\"#CC0000\"><br></font></div>', 'Your account recovery code is: {{code}}', NULL, '{\"code\":\"Verification code for password reset\",\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}', 1, NULL, NULL, 0, NULL, 0, '2021-11-03 12:00:00', '2022-03-20 20:47:05'),
(8, 'PASS_RESET_DONE', 'Password - Reset - Confirmation', 'You have reset your password', NULL, '<p style=\"font-family: Montserrat, sans-serif;\">You have successfully reset your password.</p><p style=\"font-family: Montserrat, sans-serif;\">You changed from&nbsp; IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{time}}</span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><font color=\"#ff0000\">If you did not change that, please contact us as soon as possible.</font></span></p>', 'Your password has been changed successfully', NULL, '{\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-05 03:46:35'),
(9, 'ADMIN_SUPPORT_REPLY', 'Support - Reply', 'Reply Support Ticket', NULL, '<div><p><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\">A member from our support team has replied to the following ticket:</span></span></p><p><span style=\"font-weight: bolder;\"><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\"><br></span></span></span></p><p><span style=\"font-weight: bolder;\">[Ticket#{{ticket_id}}] {{ticket_subject}}<br><br>Click here to reply:&nbsp; {{link}}</span></p><p>----------------------------------------------</p><p>Here is the reply :<br></p><p>{{reply}}<br></p></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>', 'Your Ticket#{{ticket_id}} :  {{ticket_subject}} has been replied.', NULL, '{\"ticket_id\":\"ID of the support ticket\",\"ticket_subject\":\"Subject  of the support ticket\",\"reply\":\"Reply made by the admin\",\"link\":\"URL to view the support ticket\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-03-20 20:47:51'),
(10, 'EVER_CODE', 'Verification - Email', 'Please verify your email address', NULL, '<br><div><div style=\"font-family: Montserrat, sans-serif;\">Thanks For joining us.<br></div><div style=\"font-family: Montserrat, sans-serif;\">Please use the below code to verify your email address.<br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Your email verification code is:<font size=\"6\"><span style=\"font-weight: bolder;\">&nbsp;{{code}}</span></font></div></div>', '---', NULL, '{\"code\":\"Email verification code\"}', 1, NULL, NULL, 0, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:32:07'),
(11, 'SVER_CODE', 'Verification - SMS', 'Verify Your Mobile Number', NULL, '---', 'Your phone verification code is: {{code}}', NULL, '{\"code\":\"SMS Verification Code\"}', 0, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-03-20 19:24:37'),
(15, 'DEFAULT', 'Default Template', '{{subject}}', NULL, '{{message}}', '{{message}}', NULL, '{\"subject\":\"Subject\",\"message\":\"Message\"}', 1, NULL, NULL, 1, NULL, 0, '2019-09-14 13:14:22', '2021-11-04 09:38:55'),
(16, 'KYC_APPROVE', 'KYC Approved', 'KYC has been approved', NULL, NULL, NULL, NULL, '[]', 1, NULL, NULL, 1, NULL, 0, NULL, NULL),
(17, 'KYC_REJECT', 'KYC Rejected', 'KYC has been rejected', NULL, NULL, NULL, NULL, '{\"reason\":\"Rejection Reason\"}', 1, NULL, NULL, 1, NULL, 0, NULL, NULL),
(18, 'SERVICE_SUSPEND', 'Service Suspension Notification', 'Service Suspension Notification', NULL, '<p><span style=\"font-size: 0.875rem;\">This is a notification that your service has now been suspended. The details of this suspension are below:</span><br></p><p>Product/Service: {{service_name}}<br>Due Date: {{service_next_due_date}}<br>Suspension Reason: <strong>{{service_suspension_reason}}</strong></p><p>Please contact us as soon as possible to get your service reactivated.</p><p>\r\n\r\n\r\n\r\n</p><p><br></p>', 'This is a notification that your service has now been suspended. The details of this suspension are below:\r\n\r\nProduct/Service: {{service_name}}\r\nDue Date: {{service_next_due_date}}\r\nSuspension Reason: {{service_suspension_reason}}\r\n\r\nPlease contact us as soon as possible to get your service reactivated.', NULL, '{\"service_name\":\"Service/Product Name\",\"service_next_due_date\":\"Next Due Date of the Service/Product\",\"service_suspension_reason\":\"Reason of suspension\"}', 1, NULL, NULL, 1, NULL, 0, NULL, NULL),
(19, 'SERVICE_UNSUSPEND', 'Service Unsuspension Notification', 'Service Unsuspension Notification', NULL, '<p>This is a notification that your service has now been unsuspended. The details of this unsuspension are below:</p><p><br></p><p>Product/Service: {{service_name}}</p><p>Due Date: {{service_next_due_date}}</p>', 'This is a notification that your service has now been unsuspended. The details of this unsuspension are below:\r\n\r\nProduct/Service: {{service_name}}\r\nDue Date: {{service_next_due_date}}', NULL, '{\"service_name\":\"Service/Product Name\",\"service_next_due_date\":\"Next Due Date of the Service/Product\"}', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-06 08:50:24'),
(20, 'HOSTING_ACCOUNT', 'Hosting Account Welcome Email', 'New Account Information', NULL, '<p>Thank you for your order from us! Your hosting account has now been setup and this email contains all the information you will need in order to begin using your account.</p>\r\n<p>If you have requested a domain name during sign up, please keep in mind that your domain name will not be visible on the internet instantly. This process is called propagation and can take up to 48 hours. Until your domain has propagated, your website and email will not function, we have provided a temporary url which you may use to view your website and upload files in the meantime.</p><p><br></p>\r\n<p><strong>New Account Information</strong></p>\r\n<p>Hosting Package: {{service_product_name}}<br>Domain: {{service_domain}}<br>First Payment Amount: {{service_first_payment_amount}}&nbsp;{{site_currency}}<br>Recurring Amount: {{service_recurring_amount}}&nbsp;{{site_currency}}<br>Billing Cycle: {{service_billing_cycle}}<br>Next Due Date: {{service_next_due_date}}</p><p><br></p>\r\n<p><strong>Login Details</strong></p>\r\n<p>Username: {{service_username}}<br>Password: {{service_password}}</p>\r\n<p>Control Panel URL: <a href=\"http://{$service_server_ip}:2082/\">http://{{service_server_ip}}:2082/</a><br>Once your domain has propagated, you may also use <a href=\"http://www.{$service_domain}:2082/\">http://www.{{service_domain}}:2082/</a></p><p><br></p>\r\n<p><strong>Server Information</strong></p>\r\n<p>Server IP: {{service_server_ip}}</p>\r\n<p>If you are using an existing domain with your new hosting account, you will need to update the nameservers to point to the nameservers listed below.</p>\r\n<p>Nameserver 1: {{ns1}} ({{ns1_ip}})<br>Nameserver 2: {{ns2}} ({{ns2_ip}})<br>Nameserver 3: {{ns3}} ({{ns3_ip}})<br>Nameserver 4: {{ns4}} ({{ns4_ip}})</p><p><br></p>\r\n<p><strong>Uploading Your Website</strong></p>\r\n<p>Temporarily you may use one of the addresses given below to manage your web site:</p>\r\n<p>Temporary FTP Hostname: {{service_server_ip}}<br>Temporary Webpage URL: <a href=\"http://{$service_server_ip}/~{$service_username}/\">http://{{service_server_ip}}/~{{service_username}}/</a></p>\r\n<p>And once your domain has propagated you may use the details below:</p>\r\n<p>FTP Hostname: {{service_domain}}<br>Webpage URL: <a href=\"http://www.{$service_domain}\">http://www.</a><a href=\"http://www.%7B%24service_domain%7D:2082/\" style=\"background-color: rgb(255, 255, 255); font-size: 14px;\">{{service_domain}}</a></p><p><br></p>\r\n<p><strong>Email Settings</strong></p>\r\n\r\n<p>POP3 Host Address: mail.{{service_domain}}<br>SMTP Host Address: mail.{{service_domain}}<br>Username: The email address you are checking email for<br></p>', 'Thank you for your order from us! Your hosting account has now been setup and this email contains all the information you will need in order to begin using your account.\r\n\r\nIf you have requested a domain name during sign up, please keep in mind that your domain name will not be visible on the internet instantly. This process is called propagation and can take up to 48 hours. Until your domain has propagated, your website and email will not function, we have provided a temporary url which you may use to view your website and upload files in the meantime.\r\n\r\nNew Account Information\r\n\r\nHosting Package: {{service_product_name}}\r\nDomain: {{service_domain}}\r\nFirst Payment Amount: {{service_first_payment_amount}} {{site_currency}}\r\nRecurring Amount: {{service_recurring_amount}} {{site_currency}}\r\nBilling Cycle: {{service_billing_cycle}}\r\nNext Due Date: {{service_next_due_date}}\r\n\r\nLogin Details\r\n\r\nUsername: {{service_username}}\r\nPassword: {{service_password}}\r\n\r\nControl Panel URL: http://{{service_server_ip}}:2082/\r\nOnce your domain has propagated, you may also use http://www.{{service_domain}}:2082/\r\n\r\nServer Information\r\n\r\nServer IP: {{service_server_ip}}\r\n\r\nIf you are using an existing domain with your new hosting account, you will need to update the nameservers to point to the nameservers listed below.\r\n\r\nNameserver 1: {{ns1}} ({{ns1_ip}})\r\nNameserver 2: {{ns2}} ({{ns2_ip}})\r\nNameserver 3: {{ns3}} ({{ns3_ip}})\r\nNameserver 4: {{ns4}} ({{ns4_ip}})\r\n\r\nUploading Your Website\r\n\r\nTemporarily you may use one of the addresses given below to manage your web site:\r\n\r\nTemporary FTP Hostname: {{service_server_ip}}\r\nTemporary Webpage URL: http://{{service_server_ip}}/~{{service_username}}/\r\n\r\nAnd once your domain has propagated you may use the details below:\r\n\r\nFTP Hostname: {{service_domain}}\r\nWebpage URL: http://www.{{service_domain}}\r\n\r\nEmail Settings\r\n\r\nPOP3 Host Address: mail.{{service_domain}}\r\nSMTP Host Address: mail.{{service_domain}}\r\nUsername: The email address you are checking email for', NULL, '{\"service_product_name\":\"Service Name\",\"service_domain\":\"Domain\",\"service_first_payment_amount\":\"First Payment Amount\",\"service_recurring_amount\":\"Recurring Amount\",\"service_billing_cycle\":\"Billing Cycle\",\"service_next_due_date\":\"Next Due Date\",\"service_username\":\"Username\",\"service_password\":\"Password\",\"ns1\":\"Nameserver 1\",\"ns2\":\"Nameserver 2\",\"ns3\":\"Nameserver 3\",\"ns4\":\"Nameserver 4\",\"ns1_ip\":\"Server IP 1\",\"ns2_ip\":\"Server IP 2\",\"ns3_ip\":\"Server IP 3\",\"ns4_ip\":\"Server IP 4\",\"service_server_ip\":\"Service Server IP\"}', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-06 09:42:28'),
(21, 'RESELLER_ACCOUNT', 'Reseller Account Welcome Email', 'Reseller Account Information', NULL, '<p>If you have requested a domain name during sign up then this will not be visible on the internet for between 24 and 72 hours. This process is called Propagation. Until your domain has Propagated your website and email will not function, we have provided a temporary url which you may use to view your website and upload files in the meantime.</p>\r\n<p><br></p>\r\n\r\n<p><strong>New Account Info</strong></p>\r\n<p>Domain: {{service_domain}}<br>Username: {{service_username}}<br>Password: {{service_password}}<br>Hosting Package: {{service_product_name}}</p><p><br></p>\r\n<p>Control Panel: <a href=\"http://{$service_server_ip}:2082/\">http://</a><a href=\"http://%7B%24service_server_ip%7D/~%7B$service_username%7D/\" style=\"background-color: rgb(255, 255, 255); font-size: 14px;\">{{service_server_ip}}</a><a href=\"http://{$service_server_ip}:2082/\" style=\"background-color: rgb(255, 255, 255);\">:2082/</a></p><p>Web Host Manager: <a href=\"http://{$service_server_ip}:2086/\">http://</a><a href=\"http://%7B%24service_server_ip%7D/~%7B$service_username%7D/\" style=\"background-color: rgb(255, 255, 255); font-size: 14px;\">{{service_server_ip}}</a><a href=\"http://{$service_server_ip}:2086/\" style=\"background-color: rgb(255, 255, 255);\">:2086/</a></p>\r\n<p>-------------------------------------------------------------------------------------------- <br><strong>Web Host Manager Quick Start</strong> <br>-------------------------------------------------------------------------------------------- <br><br>To access your Web Host Manager, use the following address:<br><br><a href=\"http://%7B%24service_server_ip%7D:2086/\" style=\"color: rgb(115, 103, 240); background-color: rgb(255, 255, 255); font-size: 14px;\">http://</a><a href=\"http://%7B%24service_server_ip%7D/~%7B$service_username%7D/\" style=\"background-color: rgb(255, 255, 255); font-size: 14px;\">{{service_server_ip}}</a><a href=\"http://%7B%24service_server_ip%7D:2086/\" style=\"font-size: 14px; background-color: rgb(255, 255, 255);\">:2086/</a></p><p><br>The <strong>http://</strong> must be in the address line to connect to port :2086 <br>Please use the username/password given above. <br><br><strong><em>To Create a New Account <br></em></strong><br>The first thing you need to do is scroll down on the left and click on \' Package\' so that you can create your own hosting packages. You cannot install a domain onto your account without first creating packages.<br><br>1. Click on \' a New Account\' from the left hand side menu <br>2. Put the domain in the \'\' box (no www or http or spaces ? just domainname.com). After putting in the domain, hit TAB and it will automatically create a username. Also, enter a password for the account.<br>3. Your package selection should be one that you created earlier <br>4. Then press the create button <br><br>This will give you a confirmation page (you should print this for your records)</p>\r\n<p>Please do not click on anything that you are not sure what it does. Please do not try to alter the WHM Theme from the selection box - fatal errors may occur.</p>\r\n<p>--------------------------------------------------------------------------------------------</p>\r\n<p>Temporarily you may use one of the addresses given below manage your web site</p>\r\n<p>Temporary FTP Hostname: {{service_server_ip}}</p><p>Temporary Webpage URL: <a href=\"http://{$service_server_ip}/~{$service_username}/\" style=\"background-color: rgb(255, 255, 255);\">http://{{service_server_ip}}</a>~<a href=\"http://%7B%24service_server_ip%7D/~%7B$service_username%7D/\" style=\"background-color: rgb(255, 255, 255); font-size: 14px;\">{{service_username}}</a>/</p><p>Temporary Control Panel: <a href=\"http://{$service_server_ip}/cpanel\">http://</a><a href=\"http://%7B%24service_server_ip%7D/~%7B$service_username%7D/\" style=\"background-color: rgb(255, 255, 255); font-size: 14px;\">{{service_server_ip}}</a><a href=\"http://{$service_server_ip}/cpanel\" style=\"background-color: rgb(255, 255, 255);\">/cpanel</a></p>\r\n<p>Once your domain has Propagated</p>\r\n<p>FTP Hostname: www.{{service_domain}}</p><p>Webpage URL: <a href=\"http://www.{$service_domain}\">http://www.</a><a href=\"http://%7B%24service_server_ip%7D/~%7B$service_username%7D/\" style=\"color: rgb(115, 103, 240); background-color: rgb(255, 255, 255); font-size: 14px;\">{{service_domain}}</a></p><p>Control Panel: <a href=\"http://www.{$service_domain}/cpanel\">http://www.</a><a href=\"http://%7B%24service_server_ip%7D/~%7B$service_username%7D/\" style=\"color: rgb(115, 103, 240); background-color: rgb(255, 255, 255); font-size: 14px;\">{{service_domain}}</a><a href=\"http://www.{$service_domain}/cpanel\" style=\"background-color: rgb(255, 255, 255);\">/cpanel</a></p><p>Web Host Manager: <a href=\"http://www.{$service_domain}/whm\">http://www.</a><a href=\"http://%7B%24service_server_ip%7D/~%7B$service_username%7D/\" style=\"color: rgb(115, 103, 240); background-color: rgb(255, 255, 255); font-size: 14px;\">{{service_domain}}</a><a href=\"http://www.{$service_domain}/whm\" style=\"background-color: rgb(255, 255, 255);\">/whm</a></p><p><br></p>\r\n<p><strong>Mail settings</strong></p>\r\n<p>Catch all email with your default email account</p>\r\n<p>POP3 Host Address : mail.{{service_domain}}</p><p>SMTP Host Address: mail.{{service_domain}}</p><p>Username: {{service_username}}</p><p>Password: {{service_password}}</p>\r\n<p>Additional mail accounts that you add</p>\r\n<p>POP3 Host Address : mail.{{service_domain}}</p><p>SMTP Host Address: mail.{{service_domain}}</p><p><br></p>', 'If you have requested a domain name during sign up then this will not be visible on the internet for between 24 and 72 hours. This process is called Propagation. Until your domain has Propagated your website and email will not function, we have provided a temporary url which you may use to view your website and upload files in the meantime.\r\n\r\n\r\n\r\nNew Account Info\r\n\r\nDomain: {{service_domain}}\r\nUsername: {{service_username}}\r\nPassword: {{service_password}}\r\nHosting Package: {{service_product_name}}\r\n\r\n\r\n\r\nControl Panel: http://{{service_server_ip}}:2082/\r\n\r\nWeb Host Manager: http://{{service_server_ip}}:2086/\r\n\r\n--------------------------------------------------------------------------------------------\r\nWeb Host Manager Quick Start\r\n--------------------------------------------------------------------------------------------\r\n\r\nTo access your Web Host Manager, use the following address:\r\n\r\nhttp://{{service_server_ip}}:2086/\r\n\r\n\r\nThe http:// must be in the address line to connect to port :2086\r\nPlease use the username/password given above.\r\n\r\nTo Create a New Account\r\n\r\nThe first thing you need to do is scroll down on the left and click on \' Package\' so that you can create your own hosting packages. You cannot install a domain onto your account without first creating packages.\r\n\r\n1. Click on \' a New Account\' from the left hand side menu\r\n2. Put the domain in the \'\' box (no www or http or spaces ? just domainname.com). After putting in the domain, hit TAB and it will automatically create a username. Also, enter a password for the account.\r\n3. Your package selection should be one that you created earlier\r\n4. Then press the create button\r\n\r\nThis will give you a confirmation page (you should print this for your records)\r\n\r\nPlease do not click on anything that you are not sure what it does. Please do not try to alter the WHM Theme from the selection box - fatal errors may occur.\r\n\r\n--------------------------------------------------------------------------------------------\r\n\r\nTemporarily you may use one of the addresses given below manage your web site\r\n\r\nTemporary FTP Hostname: {{service_server_ip}}\r\n\r\nTemporary Webpage URL: http://{{service_server_ip}}~{{service_username}}/\r\n\r\nTemporary Control Panel: http://{{service_server_ip}}/cpanel\r\n\r\nOnce your domain has Propagated\r\n\r\nFTP Hostname: www.{{service_domain}}\r\n\r\nWebpage URL: http://www.{{service_domain}}\r\n\r\nControl Panel: http://www.{{service_domain}}/cpanel\r\n\r\nWeb Host Manager: http://www.{{service_domain}}/whm\r\n\r\n\r\n\r\nMail settings\r\n\r\nCatch all email with your default email account\r\n\r\nPOP3 Host Address : mail.{{service_domain}}\r\n\r\nSMTP Host Address: mail.{{service_domain}}\r\n\r\nUsername: {{service_username}}\r\n\r\nPassword: {{service_password}}\r\n\r\nAdditional mail accounts that you add\r\n\r\nPOP3 Host Address : mail.{{service_domain}}\r\n\r\nSMTP Host Address: mail.{{service_domain}}', NULL, '{\"service_domain\":\"Domain\",\"service_username\":\"Service Username\",\"service_password\":\"Service Password\",\"service_product_name\":\"Service Name\",\"service_server_ip\":\"Server IP Address\"}\r\n\r\n', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-06 10:04:27'),
(22, 'VPS_SERVER', 'Dedicated/VPS Server Welcome Email', 'New Dedicated Server Information', NULL, '<p><strong>PLEASE PRINT THIS MESSAGE FOR YOUR RECORDS - PLEASE READ THIS EMAIL IN FULL.</strong></p>\r\n<p>We are pleased to tell you that the server you ordered has now been set up and is operational.</p>\r\n<p><strong>Server Details<br></strong>=============================</p>\r\n<p>{{service_product_name}}</p>\r\n<p>Main IP: {{service_dedicated_ip}}<br>Root pass: {{service_password}}</p>\r\n<p>IP address allocation: <br>{{service_assigned_ips}}</p>\r\n<p>ServerName: {{service_domain}}</p><p><br></p>\r\n<p><strong>WHM Access<br></strong>=============================<br><a href=\"http://xxxxx:2086/\">http://{{service_dedicated_ip}}:2086</a><br>Username: root<br>Password: {{service_password}}</p>\r\n<p><strong>Custom DNS Server Addresses</strong><br>=============================<br>The custom DNS addresses you should set for your domain to use are: <br>Primary DNS: {{ns1}}<br>Secondary DNS: {{ns2}}</p>\r\n<p>You will have to login to your registrar and find the area where you can specify both of your custom name server addresses.</p>\r\n<p>After adding these custom nameservers to your domain registrar control panel, it will take 24 to 48 hours for your domain to delegate authority to your DNS server. Once this has taken effect, your DNS server has control over the DNS records for the domains which use your custom name server addresses.</p>\r\n<p><strong>SSH Access Information<br></strong>=============================<br>Main IP Address: {{service_dedicated_ip}}<br>Server Name: {{service_domain}}<br>Root Password:&nbsp;<span style=\"text-align: var(--bs-body-text-align);\">{{service_password}}</span></p>\r\n<p><br></p><br>', 'PLEASE PRINT THIS MESSAGE FOR YOUR RECORDS - PLEASE READ THIS EMAIL IN FULL.\r\n\r\nWe are pleased to tell you that the server you ordered has now been set up and is operational.\r\n\r\nServer Details\r\n=============================\r\n\r\n{{service_product_name}}\r\n\r\nMain IP: {{service_dedicated_ip}}\r\nRoot pass: {{service_password}}\r\n\r\nIP address allocation:\r\n{{service_assigned_ips}}\r\n\r\nServerName: {{service_domain}}\r\n\r\n\r\n\r\nWHM Access\r\n=============================\r\nhttp://{{service_dedicated_ip}}:2086\r\nUsername: root\r\nPassword: {{service_password}}\r\n\r\nCustom DNS Server Addresses\r\n=============================\r\nThe custom DNS addresses you should set for your domain to use are:\r\nPrimary DNS: {{ns1}}\r\nSecondary DNS: {{ns2}}\r\n\r\nYou will have to login to your registrar and find the area where you can specify both of your custom name server addresses.\r\n\r\nAfter adding these custom nameservers to your domain registrar control panel, it will take 24 to 48 hours for your domain to delegate authority to your DNS server. Once this has taken effect, your DNS server has control over the DNS records for the domains which use your custom name server addresses.\r\n\r\nSSH Access Information\r\n=============================\r\nMain IP Address: {{service_dedicated_ip}}\r\nServer Name: {{service_domain}}\r\nRoot Password: {{service_password}}', NULL, '{\"service_product_name\":\"Service Name\",\"service_dedicated_ip\":\"Service Dedicated IP\",\"service_password\":\"Service Password\",\"service_assigned_ips\":\"Service Assigned Ip\",\"service_domain\":\"Domain\",\"ns1\":\"Nameserver 1\",\"ns2\":\"Nameserver 2\"}\r\n', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-06 10:14:36'),
(23, 'OTHER_PRODUCT', 'Other Product/Service Welcome Email', 'New Product Information', NULL, '<p>Your order for {{service_product_name}} has now been activated. Please keep this message for your records.</p>\r\n<p>Product/Service: {{service_product_name}}<br>Amount: {{service_recurring_amount}}<br>Billing Cycle: {{service_billing_cycle}}<br>Next Due Date: {{service_next_due_date}}</p><br>', 'Your order for {{service_product_name}} has now been activated. Please keep this message for your records.\r\n\r\nProduct/Service: {{service_product_name}}\r\nAmount: {{service_recurring_amount}}\r\nBilling Cycle: {{service_billing_cycle}}\r\nNext Due Date: {{service_next_due_date}}', NULL, '{\"service_product_name\":\"Service Name\",\"service_recurring_amount\":\"Recurring Amount\",\"service_billing_cycle\":\"Billing Cycle\",\"service_next_due_date\":\"Next Due Date\"}\r\n\r\n', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-06 10:23:04'),
(24, 'DOMAIN_REGISTER', 'Domain Register', 'Domain Registration Confirmation', NULL, '<p><span style=\"font-size: 0.875rem;\">This message is to confirm that your domain purchase has been successful. The details of the domain purchase are below:</span><br></p><p>Registration Date: {{domain_reg_date}}</p><p>Domain: {{domain_name}}</p><p>Registration Period: {{domain_reg_period}} Years</p><p>Amount: {{first_payment_amount}}<span style=\"font-size: 0.875rem;\">{{site_currency}}</span></p><p>Next Due Date: {{next_due_date}}</p><p><br></p>', 'This message is to confirm that your domain purchase has been successful. The details of the domain purchase are below:\r\n\r\nRegistration Date: {{domain_reg_date}}\r\n\r\nDomain: {{domain_name}}\r\n\r\nRegistration Period: {{domain_reg_period}} Years\r\n\r\nAmount: {{first_payment_amount}}{{site_currency}}\r\n\r\nNext Due Date: {{next_due_date}}', NULL, '{\"domain_reg_date\":\"Domain registration date\",\"domain_name\":\"Domain Name\",\"domain_reg_period\":\"Domain registration period\",\"first_payment_amount\":\"First payment amount\",\"next_due_date\":\"Domain next due date\"}', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-06 10:52:36'),
(25, 'INVOICE_PAYMENT_REMINDER', 'Invoice Payment Reminder', 'Invoice Payment Reminder', NULL, '<p><span style=\"font-size: 0.875rem;\">This is a billing reminder that your invoice no. {{invoice_number}} which was generated on {{invoice_created}} is due on {{invoice_due_date}}.</span><br></p>\r\n<p><span style=\"font-size: 0.875rem;\">Invoice: {{invoice_number}}</span><br></p><p>Due Date: {{invoice_due_date}}</p>\r\n<p>You can log in to your client area to view and pay the invoice at {{invoice_link}}</p>', 'This is a billing reminder that your invoice no. {{invoice_number}} which was generated on {{invoice_created}} is due on {{invoice_due_date}}.\r\n\r\nInvoice: {{invoice_number}}\r\n\r\nDue Date: {{invoice_due_date}}\r\n\r\nYou can log in to your client area to view and pay the invoice at {{invoice_link}}', NULL, '{\"invoice_number\":\"Invoice Number\",\"invoice_created\":\"Invoice Created at\",\"invoice_due_date\":\"Invoice Due Date\",\"invoice_link\":\"Invoice Link\"}', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-07 05:07:38'),
(26, 'FIRST_INVOICE_OVERDUE_NOTICE', 'First Invoice Overdue Notice', 'First Invoice Overdue Notice', NULL, '<p><span style=\"color: rgb(33, 37, 41); font-size: 1rem;\">This is the first billing notice</span><span style=\"font-size: 0.875rem;\">&nbsp;your invoice no. {{invoice_number}} which was generated on {{invoice_created}} is due on {{invoice_due_date}}.</span><br></p><p><span style=\"font-size: 0.875rem;\">Invoice: {{invoice_number}}</span><br></p><p>Due Date: {{invoice_due_date}}</p><p>You can log in to your client area to view and pay the invoice at {{invoice_link}}</p>', 'This is the first billing notice your invoice no. {{invoice_number}} which was generated on {{invoice_created}} is due on {{invoice_due_date}}.\r\n\r\nInvoice: {{invoice_number}}\r\n\r\nDue Date: {{invoice_due_date}}\r\n\r\nYou can log in to your client area to view and pay the invoice at {{invoice_link}}', NULL, '{\"invoice_number\":\"Invoice Number\",\"invoice_created\":\"Invoice Created at\",\"invoice_due_date\":\"Invoice Due Date\",\"invoice_link\":\"Invoice Link\"}\r\n', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-07 05:08:19'),
(27, 'SECOND_INVOICE_OVERDUE_NOTICE', 'Second Invoice Overdue Notice', 'Second Invoice Overdue Notice', NULL, '<p><span style=\"color: rgb(33, 37, 41); font-size: 1rem;\">This is the second billing notice</span><span style=\"font-size: 0.875rem;\">&nbsp;your invoice no. {{invoice_number}} which was generated on {{invoice_created}} is due on {{invoice_due_date}}.</span><br></p><p><span style=\"font-size: 0.875rem;\">Invoice: {{invoice_number}}</span><br></p><p>Due Date: {{invoice_due_date}}</p><p>You can log in to your client area to view and pay the invoice at {{invoice_link}}</p>', 'This is the second billing notice your invoice no. {{invoice_number}} which was generated on {{invoice_created}} is due on {{invoice_due_date}}.\r\n\r\nInvoice: {{invoice_number}}\r\n\r\nDue Date: {{invoice_due_date}}\r\n\r\nYou can log in to your client area to view and pay the invoice at {{invoice_link}}', NULL, '{\"invoice_number\":\"Invoice Number\",\"invoice_created\":\"Invoice Created at\",\"invoice_due_date\":\"Invoice Due Date\",\"invoice_link\":\"Invoice Link\"}\r\n', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-07 05:09:03'),
(28, 'THIRD_INVOICE_OVERDUE_NOTICE', 'Third Invoice Overdue Notice', 'Third Invoice Overdue Notice', NULL, '<p><span style=\"color: rgb(33, 37, 41); font-size: 1rem;\">This is the third and final billing notice that your invoice no</span><span style=\"font-size: 0.875rem;\">. {{invoice_number}} which was generated on {{invoice_created}} is due on {{invoice_due_date}}.</span><br></p><p><span style=\"font-size: 0.875rem;\">Invoice: {{invoice_number}}</span><br></p><p>Due Date: {{invoice_due_date}}</p><p>You can log in to your client area to view and pay the invoice at {{invoice_link}}</p>', 'This is the third and final billing notice that your invoice no. {{invoice_number}} which was generated on {{invoice_created}} is due on {{invoice_due_date}}.\r\n\r\nInvoice: {{invoice_number}}\r\n\r\nDue Date: {{invoice_due_date}}\r\n\r\nYou can log in to your client area to view and pay the invoice at {{invoice_link}}', NULL, '{\"invoice_number\":\"Invoice Number\",\"invoice_created\":\"Invoice Created at\",\"invoice_due_date\":\"Invoice Due Date\",\"invoice_link\":\"Invoice Link\"}\r\n', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-07 05:09:39'),
(29, 'ORDER_NOTIFICATION', 'New Order Notification', 'New Order Notification', NULL, '<p><strong style=\"font-size: 0.875rem; text-align: var(--bs-body-text-align);\">Order Information</strong><br></p><p>Order ID: {{order_id}}<br>Date/Time: {{created_at}}<br>Invoice Id: {{invoice_id}}<br>Amount: {{amount}}</p><p><br></p><p><strong>Customer Information</strong></p><p>Name: {{name}}<br>Email: {{email}}<br>Address: {{address}}<br>City: {{city}}<br>State: {{state}}<br>Postcode: {{zip}}<br>Country: {{country}}<br>Phone Number: {{phone}}</p><p><br></p><p><strong>Order Items</strong></p><p>{{order_items}}</p><p><br></p><p><strong>ISP Information</strong></p><p>IP: {{client_ip}}<br></p><p><br></p><p>\n', 'Order Information\r\n\r\nOrder ID: {{order_id}}\r\nDate/Time: {{created_at}}\r\nInvoice Id: {{invoice_id}}\r\nAmount: {{amount}}\r\n\r\n\r\n\r\nCustomer Information\r\n\r\nName: {{name}}\r\nEmail: {{email}}\r\nAddress: {{address}}\r\nCity: {{city}}\r\nState: {{state}}\r\nPostcode: {{zip}}\r\nCountry: {{country}}\r\nPhone Number: {{phone}}\r\n\r\n\r\n\r\nOrder Items\r\n\r\n{{order_items}}\r\n\r\n\r\n\r\nISP Information\r\n\r\nIP: {{client_ip}}\r\n\r\n\r\n\r\n{{invoice_link}}', NULL, '{\"order_id\":\"Order Id\",\"created_at\":\"Order Created at\",\"invoice_id\":\"Invoice Id\",\"amount\":\"Order Amount\",\"name\":\"User Name\",\"email\":\"User Email\",\"address\":\"User Address\",\"city\":\"User City\",\"state\":\"User State\",\"zip\":\"User Zip Code\",\"country\":\"User Country\",\"phone\":\"User Phone\",\"invoice_link\":\"Invoice Link\",\"client_ip\":\"User IP Address\",\"items\":\"Order Items\"}', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-22 11:00:18'),
(30, 'DOMAIN_RENEW_NOTIFICATION', 'Domain Renewal Notification', 'Domain Renewal Notification', NULL, '<p><span style=\"font-size: 0.875rem; text-align: var(--bs-body-text-align);\">Thank you for your domain renewal order. Your domain renewal request for the domain listed below has now been completed.</span><br></p><p><span style=\"font-size: 0.875rem; text-align: var(--bs-body-text-align);\"><br></span></p><p>Domain: {{domain}}<br>Renewal Length: {{reg_period}}</p><p>Renewal Price: {{recurring_amount}}</p><p>Next Due Date: {{next_due_date}}</p><p><br></p>', 'Thank you for your domain renewal order. Your domain renewal request for the domain listed below has now been completed.\r\n\r\nDomain: {{domain}}\r\nRenewal Length: {{reg_period}}\r\n\r\nRenewal Price: {{recurring_amount}}\r\n\r\nNext Due Date: {{next_due_date}}', NULL, '{\"domain\":\"Domain Name\",\"reg_period\":\"Domain Registration Period\",\"recurring_amount\":\"Domain Recurring Amount\",\"next_due_date\":\"Domainn Next Due Date\"}', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-22 12:20:40'),
(31, 'INVOICE_REFUND_NOTIFICATION', 'Invoice Refund Notification', 'Invoice Refund Notification', NULL, '<p><span style=\"font-size: 0.875rem; text-align: var(--bs-body-text-align);\">This is confirmation that a&nbsp; refund has been processed for Invoice #{{invoice_id}}</span><br></p><p>The refund has been \"credit\" credited to your account balance.</p><p><br></p><p>Amount Refunded: {{refund_amount}}{{site_currency}}<br></p><p>Transaction #: {{trx_id}}<br></p><p>You may review your invoice history at any time by logging in to your client area.</p>', 'This is confirmation that a  refund has been processed for Invoice #{{invoice_id}}\nThe refund has been \"credit\" credited to your account balance.\n\nAmount Refunded: {{refund_amount}}\nTransaction #: {{trx_id}}\nYou may review your invoice history at any time by logging in to your client area.', NULL, '{\"invoice_id\":\"Invoice Id\",\"refund_amount\":\"Refunded Amount\",\"trx_id\":\"Transaction Id\"}', 1, NULL, NULL, 1, NULL, 0, NULL, '2022-06-23 05:02:40');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `coupon_id` int(10) UNSIGNED DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,18) NOT NULL DEFAULT 0.000000000000000000,
  `discount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `after_discount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `ip_address` text NOT NULL,
  `admin_notes` text DEFAULT NULL COMMENT '	For admin only\r\n',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=> Active,\r\n2=> Pending, \r\n3=> Cancelled,',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `slug` varchar(40) DEFAULT NULL,
  `tempname` varchar(40) DEFAULT NULL COMMENT 'template name',
  `secs` text DEFAULT NULL,
  `seo_content` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`, `slug`, `tempname`, `secs`, `seo_content`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Home', '/', 'templates.basic.', '[\"domain\",\"service\"]', NULL, 1, '2020-07-11 06:23:58', '2023-01-15 09:29:42'),
(5, 'Contact', 'contact', 'templates.basic.', NULL, NULL, 1, '2020-10-22 01:14:53', '2023-01-14 14:22:06'),
(21, 'Faq', 'faq', 'templates.basic.', '[\"faq\"]', NULL, 0, '2023-01-14 11:46:15', '2023-01-14 14:20:50'),
(22, 'Announcement', 'announcements', 'templates.basic.', NULL, NULL, 1, '2023-01-14 11:46:15', '2023-01-23 13:09:58');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `group` varchar(40) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `group`, `code`) VALUES
(1, 'Dashboard', 'AdminController', 'admin.dashboard'),
(2, 'Order Statistics', 'AdminController', 'admin.order.statistics'),
(3, 'All Notifications', 'AdminController', 'admin.notifications'),
(4, 'Read Notification', 'AdminController', 'admin.notification.read'),
(5, 'Read All Notifications', 'AdminController', 'admin.notifications.readAll'),
(6, 'Request Report', 'AdminController', 'admin.request.report'),
(7, 'Submit Request Report', 'AdminController', 'admin.request.report.submit'),
(8, 'Download Attachment', 'AdminController', 'admin.download.attachment'),
(9, 'Check Slug Availability', 'AdminController', 'admin.check.slug'),
(10, 'Active Services', 'AdminController', 'admin.active.services'),
(11, 'Active Domains', 'AdminController', 'admin.active.domains'),
(12, 'Automation Errors', 'AdminController', 'admin.automation.errors'),
(13, 'Delete Automation Errors', 'AdminController', 'admin.delete.automation.errors'),
(14, 'Read Automation Errors', 'AdminController', 'admin.read.automation.errors'),
(15, 'Delete Automation Error', 'AdminController', 'admin.delete.automation.error'),
(16, 'All Domains', 'AdminController', 'admin.domains'),
(17, 'All Services', 'AdminController', 'admin.services'),
(18, 'All Requests', 'CancelRequestController', 'admin.cancel.requests'),
(19, 'Pending Requests', 'CancelRequestController', 'admin.cancel.request.pending'),
(20, 'Completed Requests', 'CancelRequestController', 'admin.cancel.request.completed'),
(21, 'Cancel Request', 'CancelRequestController', 'admin.cancel.request'),
(22, 'Delete Request', 'CancelRequestController', 'admin.cancel.request.delete'),
(23, 'Module Command', 'HostingModuleController', 'admin.module.command'),
(24, 'Login Cpanel', 'HostingModuleController', 'admin.module.cpanel.login'),
(25, 'Module Command', 'DomainModuleController', 'admin.domain.module.command'),
(26, 'Domain Contact', 'DomainModuleController', 'admin.order.domain.contact'),
(27, 'ALL Products', 'ProductController', 'admin.products'),
(28, 'Add Product', 'ProductController', 'admin.product.add.page'),
(29, 'Submit Add Product', 'ProductController', 'admin.product.add'),
(30, 'Edit Product', 'ProductController', 'admin.product.update.page'),
(31, 'Update Product', 'ProductController', 'admin.product.update'),
(32, 'Change Status', 'ProductController', 'admin.product.status'),
(33, 'Get Whm Packages', 'ProductController', 'admin.get.whm.package'),
(34, 'All Invoices', 'InvoiceController', 'admin.invoices'),
(35, 'Cancelled Invoices', 'InvoiceController', 'admin.invoices.cancelled'),
(36, 'Paid Invoices', 'InvoiceController', 'admin.invoices.paid'),
(37, 'Unpaid Invoices', 'InvoiceController', 'admin.invoices.unpaid'),
(38, 'Pending Payment Invoices', 'InvoiceController', 'admin.invoices.payment.pending'),
(39, 'Refunded Invoices', 'InvoiceController', 'admin.invoices.refunded'),
(40, 'Invoice Details', 'InvoiceController', 'admin.invoices.details'),
(41, 'Update Invoice', 'InvoiceController', 'admin.invoice.update'),
(42, 'Download Invoice', 'InvoiceController', 'admin.invoice.download'),
(43, 'Delete Invoice Item', 'InvoiceController', 'admin.invoice.item.delete'),
(44, 'Refund Invoice', 'InvoiceController', 'admin.invoice.refund'),
(45, 'All Domain Invoices', 'InvoiceController', 'admin.invoices.domain.all'),
(46, 'All  Hosting Invoices', 'InvoiceController', 'admin.invoices.hosting.all'),
(47, 'Payment Transactions', 'InvoiceController', 'admin.invoices.payment.transactions'),
(48, 'Hosting Order Details', 'ServiceController', 'admin.order.hosting.details'),
(49, 'Update Hosting Order', 'ServiceController', 'admin.order.hosting.update'),
(50, 'Change Hosting Order', 'ServiceController', 'admin.change.order.hosting.product'),
(51, 'Domain Order Details', 'ServiceController', 'admin.order.domain.details'),
(52, 'Update Domain Order', 'ServiceController', 'admin.order.domain.update'),
(53, 'All Orders', 'OrderController', 'admin.orders'),
(54, 'Pending Orders', 'OrderController', 'admin.orders.pending'),
(55, 'Active Orders', 'OrderController', 'admin.orders.active'),
(56, 'Cancelled Orders', 'OrderController', 'admin.orders.cancelled'),
(57, 'Order Details', 'OrderController', 'admin.orders.details'),
(58, 'Accept Order', 'OrderController', 'admin.order.accept'),
(59, 'Cancel Order', 'OrderController', 'admin.order.cancel'),
(60, 'Make Order To Pending', 'OrderController', 'admin.order.mark.pending'),
(61, 'Order Notes', 'OrderController', 'admin.order.notes'),
(62, 'All Coupons', 'CouponController', 'admin.coupons'),
(63, 'Add Coupon', 'CouponController', 'admin.coupon.add'),
(64, 'Update Coupon', 'CouponController', 'admin.coupon.update'),
(65, 'Change Status', 'CouponController', 'admin.coupon.status'),
(66, 'Billing Setting', 'BillingSettingController', 'admin.billing.setting'),
(67, 'Update Billing Setting', 'BillingSettingController', 'admin.billing.setting.update'),
(68, 'Advanced Billing Setting', 'BillingSettingController', 'admin.billing.setting.advanced'),
(69, 'All Service Category', 'ServiceCategoryController', 'admin.service.category'),
(70, 'Add Service Category', 'ServiceCategoryController', 'admin.service.category.add'),
(71, 'Update Service Category', 'ServiceCategoryController', 'admin.service.category.update'),
(72, 'Change Status', 'ServiceCategoryController', 'admin.service.category.status'),
(73, 'All TLD(Top Level Domain)', 'TldController', 'admin.tld'),
(74, 'Add TLD(Top Level Domain)', 'TldController', 'admin.tld.add'),
(75, 'Update TLD(Top Level Domain)', 'TldController', 'admin.tld.update'),
(76, 'Update Tld Pricing', 'TldController', 'admin.tld.update.pricing'),
(77, 'Change Status', 'TldController', 'admin.tld.status'),
(78, 'Domain Register', 'DomainRegisterController', 'admin.register.domain'),
(79, 'Update Domain Register', 'DomainRegisterController', 'admin.register.domain.update'),
(80, 'Auto Domain Register', 'DomainRegisterController', 'admin.register.domain.auto'),
(81, 'Change Status', 'DomainRegisterController', 'admin.register.domain.status'),
(82, 'Groups', 'ConfigurableController', 'admin.configurable.groups'),
(83, 'Add Group', 'ConfigurableController', 'admin.configurable.group.add'),
(84, 'Update Group', 'ConfigurableController', 'admin.configurable.group.update'),
(85, 'Change Group Status', 'ConfigurableController', 'admin.configurable.group.status'),
(86, 'Group Options', 'ConfigurableController', 'admin.configurable.group.options'),
(87, 'Add Group Option', 'ConfigurableController', 'admin.configurable.group.add.option'),
(88, 'Update Group Option', 'ConfigurableController', 'admin.configurable.group.update.option'),
(89, 'Change Group Option Status', 'ConfigurableController', 'admin.configurable.group.option.status'),
(90, 'Group Sub Options', 'ConfigurableController', 'admin.configurable.group.sub.options'),
(91, 'Add Group Sub Option', 'ConfigurableController', 'admin.configurable.group.add.sub.option'),
(92, 'Update Group Sub Option', 'ConfigurableController', 'admin.configurable.group.update.sub.option'),
(93, 'Change Group Sub Option Status', 'ConfigurableController', 'admin.configurable.group.sub.option.status'),
(94, 'Server Groups', 'ServerController', 'admin.groups.server'),
(95, 'Add Server Group', 'ServerController', 'admin.group.server.add'),
(96, 'Update Server Group', 'ServerController', 'admin.group.server.update'),
(97, 'Change Server  Group Status', 'ServerController', 'admin.group.server.status'),
(98, 'Servers', 'ServerController', 'admin.servers'),
(99, 'Add Server', 'ServerController', 'admin.server.add.page'),
(100, 'Submit Server', 'ServerController', 'admin.server.add'),
(101, 'Edit Server', 'ServerController', 'admin.server.edit.page'),
(102, 'Update Server', 'ServerController', 'admin.server.update'),
(103, 'Server Login By Whm', 'ServerController', 'admin.server.login.whm'),
(104, 'Change Server Status', 'ServerController', 'admin.server.status'),
(105, 'Test Connection', 'ServerController', 'admin.server.test.connection'),
(106, 'All Clients', 'ManageUsersController', 'admin.users.all'),
(107, 'Active Clients', 'ManageUsersController', 'admin.users.active'),
(108, 'Banned Clients', 'ManageUsersController', 'admin.users.banned'),
(109, 'Email Verified Clients', 'ManageUsersController', 'admin.users.email.verified'),
(110, 'Email Unverified Clients', 'ManageUsersController', 'admin.users.email.unverified'),
(111, 'Mobile Unverified Clients', 'ManageUsersController', 'admin.users.mobile.unverified'),
(112, 'Unverified Kyc Clients', 'ManageUsersController', 'admin.users.kyc.unverified'),
(113, 'Pending Kyc Clients', 'ManageUsersController', 'admin.users.kyc.pending'),
(114, 'Mobile Verified Clients', 'ManageUsersController', 'admin.users.mobile.verified'),
(115, 'Clients With Balance', 'ManageUsersController', 'admin.users.with.balance'),
(116, 'Clients Details', 'ManageUsersController', 'admin.users.detail'),
(117, 'Kyc Details', 'ManageUsersController', 'admin.users.kyc.details'),
(118, 'Approve Kyc', 'ManageUsersController', 'admin.users.kyc.approve'),
(119, 'Reject Kyc', 'ManageUsersController', 'admin.users.kyc.reject'),
(120, 'Update Clients', 'ManageUsersController', 'admin.users.update'),
(121, 'Add Sub Balance Clients', 'ManageUsersController', 'admin.users.add.sub.balance'),
(122, 'Single Notification', 'ManageUsersController', 'admin.users.notification.single'),
(123, 'Single Notification', 'ManageUsersController', 'admin.users.notification.single'),
(124, 'Login As Client', 'ManageUsersController', 'admin.users.login'),
(125, 'Change Status', 'ManageUsersController', 'admin.users.status'),
(126, 'All Notification', 'ManageUsersController', 'admin.users.notification.all'),
(127, 'Send All Notification', 'ManageUsersController', 'admin.users.notification.all.send'),
(128, 'Notification Log', 'ManageUsersController', 'admin.users.notification.log'),
(129, 'Clients Orders', 'ManageUsersController', 'admin.users.orders'),
(130, 'Clients Invoices', 'ManageUsersController', 'admin.users.invoices'),
(131, 'Clients Cancellations', 'ManageUsersController', 'admin.users.cancellations'),
(132, 'Clients Services', 'ManageUsersController', 'admin.users.services'),
(133, 'Clients Domains', 'ManageUsersController', 'admin.users.domains'),
(134, 'Add New Client', 'ManageUsersController', 'admin.users.add.new.form'),
(135, 'Submit New Client', 'ManageUsersController', 'admin.users.add.new'),
(136, 'All Subscribers', 'SubscriberController', 'admin.subscriber.index'),
(137, 'Send Email To All', 'SubscriberController', 'admin.subscriber.send.email'),
(138, 'Remove Subscriber', 'SubscriberController', 'admin.subscriber.remove'),
(139, 'Send Email To All', 'SubscriberController', 'admin.subscriber.send.email'),
(140, 'All Gateways', 'AutomaticGatewayController', 'admin.gateway.automatic.index'),
(141, 'Edit Gateway', 'AutomaticGatewayController', 'admin.gateway.automatic.edit'),
(142, 'Update Gateway', 'AutomaticGatewayController', 'admin.gateway.automatic.update'),
(143, 'Remove Gateway', 'AutomaticGatewayController', 'admin.gateway.automatic.remove'),
(144, 'Change Status', 'AutomaticGatewayController', 'admin.gateway.automatic.status'),
(145, 'All Gateways', 'ManualGatewayController', 'admin.gateway.manual.index'),
(146, 'Add Gateway', 'ManualGatewayController', 'admin.gateway.manual.create'),
(147, 'Submit Gateway', 'ManualGatewayController', 'admin.gateway.manual.store'),
(148, 'Edit Gateway', 'ManualGatewayController', 'admin.gateway.manual.edit'),
(149, 'Update Gateway', 'ManualGatewayController', 'admin.gateway.manual.update'),
(150, 'Change Status', 'ManualGatewayController', 'admin.gateway.manual.status'),
(151, 'All Deposits', 'DepositController', 'admin.deposit.list'),
(152, 'Pending Deposits', 'DepositController', 'admin.deposit.pending'),
(153, 'Rejected Deposits', 'DepositController', 'admin.deposit.rejected'),
(154, 'Approved Deposits', 'DepositController', 'admin.deposit.approved'),
(155, 'Successful Deposits', 'DepositController', 'admin.deposit.successful'),
(156, 'Initiated Deposits', 'DepositController', 'admin.deposit.initiated'),
(157, 'Deposit Details', 'DepositController', 'admin.deposit.details'),
(158, 'Reject Deposit', 'DepositController', 'admin.deposit.reject'),
(159, 'Approve Deposit', 'DepositController', 'admin.deposit.approve'),
(160, 'Transaction Report', 'ReportController', 'admin.report.transaction'),
(161, 'Login History', 'ReportController', 'admin.report.login.history'),
(162, 'IP History Of Login', 'ReportController', 'admin.report.login.ipHistory'),
(163, 'Notification History', 'ReportController', 'admin.report.notification.history'),
(164, 'Email Details Of Notification', 'ReportController', 'admin.report.email.details'),
(165, 'All Tickets', 'SupportTicketController', 'admin.ticket.index'),
(166, 'Pending Tickets', 'SupportTicketController', 'admin.ticket.pending'),
(167, 'Closed Tickets', 'SupportTicketController', 'admin.ticket.closed'),
(168, 'Answered Tickets', 'SupportTicketController', 'admin.ticket.answered'),
(169, 'View Ticket', 'SupportTicketController', 'admin.ticket.view'),
(170, 'Reply Ticket', 'SupportTicketController', 'admin.ticket.reply'),
(171, 'Close Ticket', 'SupportTicketController', 'admin.ticket.close'),
(172, 'Download Ticket', 'SupportTicketController', 'admin.ticket.download'),
(173, 'Delete Ticket', 'SupportTicketController', 'admin.ticket.delete'),
(174, 'Manage Language', 'LanguageController', 'admin.language.manage'),
(175, 'Add New Language', 'LanguageController', 'admin.language.manage.store'),
(176, 'Delete Language', 'LanguageController', 'admin.language.manage.delete'),
(177, 'Update Language', 'LanguageController', 'admin.language.manage.update'),
(178, 'Language Key', 'LanguageController', 'admin.language.key'),
(179, 'Import Language', 'LanguageController', 'admin.language.import.lang'),
(180, 'Add Language Key', 'LanguageController', 'admin.language.store.key'),
(181, 'Delete Language Key', 'LanguageController', 'admin.language.delete.key'),
(182, 'Update Language Key', 'LanguageController', 'admin.language.update.key'),
(183, 'Get Language Key', 'LanguageController', 'admin.language.get.key'),
(184, 'System Setting', 'GeneralSettingController', 'admin.system.setting'),
(185, 'General Setting', 'GeneralSettingController', 'admin.setting.index'),
(186, 'Update General Setting', 'GeneralSettingController', 'admin.setting.update'),
(187, 'System Configuration', 'GeneralSettingController', 'admin.setting.system.configuration'),
(188, 'Update System Configuration', 'GeneralSettingController', 'admin.setting.system.configuration.update'),
(189, 'Logo Icon', 'GeneralSettingController', 'admin.setting.logo.icon'),
(190, 'Update Logo Icon', 'GeneralSettingController', 'admin.setting.logo.icon.update'),
(191, 'Custom Css', 'GeneralSettingController', 'admin.setting.custom.css'),
(192, 'Update Custom Css', 'GeneralSettingController', 'admin.setting.custom.css.update'),
(195, 'Maintenance Mode', 'GeneralSettingController', 'admin.maintenance.mode'),
(196, 'Update Maintenance Mode', 'GeneralSettingController', 'admin.maintenance.mode.update'),
(197, 'Kyc Setting', 'KycController', 'admin.kyc.setting'),
(198, 'Update Kyc Setting', 'KycController', 'admin.kyc.setting.update'),
(199, 'Global Notification Setting', 'NotificationController', 'admin.setting.notification.global'),
(200, 'Update Global Notification Setting', 'NotificationController', 'admin.setting.notification.global.update'),
(201, 'Notification Templates', 'NotificationController', 'admin.setting.notification.templates'),
(202, 'Edit Notification Templates', 'NotificationController', 'admin.setting.notification.template.edit'),
(203, 'Update Notification Templates', 'NotificationController', 'admin.setting.notification.template.update'),
(204, 'Email Notification', 'NotificationController', 'admin.setting.notification.email'),
(205, 'Update Email Notification', 'NotificationController', 'admin.setting.notification.email.update'),
(206, 'Test Email', 'NotificationController', 'admin.setting.notification.email.test'),
(207, 'Sms Notification', 'NotificationController', 'admin.setting.notification.sms'),
(208, 'Update Sms Notification', 'NotificationController', 'admin.setting.notification.sms.update'),
(209, 'Test Sms', 'NotificationController', 'admin.setting.notification.sms.test'),
(210, 'All Extensions', 'ExtensionController', 'admin.extensions.index'),
(211, 'Update Extensions', 'ExtensionController', 'admin.extensions.update'),
(212, 'Change Status', 'ExtensionController', 'admin.extensions.status'),
(213, 'System Information', 'SystemController', 'admin.system.info'),
(214, 'Server Information', 'SystemController', 'admin.system.server.info'),
(215, 'System Optimize', 'SystemController', 'admin.system.optimize'),
(216, 'System Optimize Clear', 'SystemController', 'admin.system.optimize.clear'),
(217, 'Update System', 'SystemController', 'admin.system.update'),
(218, 'Submit Update System', 'SystemController', 'admin.system.update.upload'),
(219, 'SEO Manager', 'FrontendController', 'admin.seo'),
(220, 'Manage Templates', 'FrontendController', 'admin.frontend.templates'),
(221, 'Active Templates', 'FrontendController', 'admin.frontend.templates.active'),
(222, 'Frontend Sections', 'FrontendController', 'admin.frontend.sections'),
(223, 'Submit Frontend Sections Element', 'FrontendController', 'admin.frontend.sections.content'),
(224, 'Frontend Sections Element', 'FrontendController', 'admin.frontend.sections.element'),
(225, 'Remove Frontend Sections Element', 'FrontendController', 'admin.frontend.remove'),
(227, 'All Staffs', 'StaffController', 'admin.staff.index'),
(228, 'Add And Update Staff', 'StaffController', 'admin.staff.save'),
(229, 'Change Status', 'StaffController', 'admin.staff.status'),
(230, 'Login As Staff', 'StaffController', 'admin.staff.login'),
(231, 'All Roles', 'RolesController', 'admin.roles.index'),
(232, 'Add New Role', 'RolesController', 'admin.roles.add'),
(233, 'Edit Role', 'RolesController', 'admin.roles.edit'),
(234, 'Update Role', 'RolesController', 'admin.roles.save'),
(235, 'Setting Cookie', 'GeneralSettingController', 'admin.setting.cookie'),
(236, 'Setting Cookie Update', 'GeneralSettingController', 'admin.setting.cookie.update'),
(237, 'Users List', 'ManageUsersController', 'admin.users.list'),
(238, 'Cron Job Setting', 'CronConfigurationController', 'admin.cron.index'),
(239, 'Add Cron', 'CronConfigurationController', 'admin.cron.store'),
(240, 'Update Cron', 'CronConfigurationController', 'admin.cron.update'),
(241, 'Delete Cron', 'CronConfigurationController', 'admin.cron.delete'),
(242, 'Cron Schedule', 'CronConfigurationController', 'admin.cron.schedule'),
(243, 'Add Cron Schedule', 'CronConfigurationController', 'admin.cron.schedule.store'),
(244, 'Change Schedule Status', 'CronConfigurationController', 'admin.cron.schedule.status'),
(245, 'Pause Cron Schedule', 'CronConfigurationController', 'admin.cron.schedule.pause'),
(246, 'Cron Schedule Logs', 'CronConfigurationController', 'admin.cron.schedule.logs'),
(247, 'Cron Schedule Log Resolved', 'CronConfigurationController', 'admin.cron.schedule.log.resolved'),
(248, 'Cron Log Flush', 'CronConfigurationController', 'admin.cron.log.flush');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pricings`
--

CREATE TABLE `pricings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `configurable_group_sub_option_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `monthly_setup_fee` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `quarterly_setup_fee` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `semi_annually_setup_fee` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `annually_setup_fee` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `biennially_setup_fee` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `triennially_setup_fee` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `monthly` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `quarterly` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `semi_annually` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `annually` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `biennially` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `triennially` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `server_group_id` int(10) UNSIGNED DEFAULT 0,
  `server_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `package_name` varchar(255) DEFAULT NULL COMMENT 'From WHM API',
  `product_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 => Shared Hosting,\r\n2 => Reseller Hosting,\r\n3 => Server/VPS,\r\n4 => Other,',
  `payment_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=> One Time, 2=> Recurring',
  `module_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 => None,\r\n1 => cPanel,',
  `module_option` tinyint(1) NOT NULL DEFAULT 3 COMMENT '1 => Automatically setup the product as soon as the first payment is received,\r\n2 => Automatically setup the product when you manually accept a pending order,\r\n3 => Do not automatically setup this product,',
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `welcome_email` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 => Hosting Account Welcome Email,\r\n2 => Reseller Account Welcome Email,\r\n3 => Dedicated/VPS Server Welcome Email,\r\n4 => Other Product/Service Welcome Email,',
  `domain_register` tinyint(1) NOT NULL DEFAULT 0,
  `stock_control` tinyint(1) NOT NULL DEFAULT 0,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_configurations`
--

CREATE TABLE `product_configurations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `configurable_group_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `server_group_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `service_role` varchar(40) NOT NULL DEFAULT 'any',
  `location` varchar(120) DEFAULT NULL,
  `hostname` varchar(255) NOT NULL,
  `protocol` varchar(45) DEFAULT NULL,
  `port` varchar(45) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `max_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `current_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `health_status` varchar(30) NOT NULL DEFAULT 'unknown',
  `health_message` varchar(255) DEFAULT NULL,
  `health_checked_at` timestamp NULL DEFAULT NULL,
  `deployment_status` varchar(40) NOT NULL DEFAULT 'manual',
  `deployment_version` varchar(120) DEFAULT NULL,
  `deployment_log` longtext DEFAULT NULL,
  `last_deployed_at` timestamp NULL DEFAULT NULL,
  `api_token` text NOT NULL,
  `security_token` varchar(255) NOT NULL,
  `ns1` varchar(255) NOT NULL,
  `ns1_ip` varchar(255) NOT NULL,
  `ns2` varchar(255) NOT NULL,
  `ns2_ip` varchar(255) NOT NULL,
  `ns3` varchar(255) DEFAULT NULL,
  `ns3_ip` varchar(255) DEFAULT NULL,
  `ns4` varchar(255) DEFAULT NULL,
  `ns4_ip` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `server_groups`
--

CREATE TABLE `server_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shopping_carts`
--

CREATE TABLE `shopping_carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `domain_register_id` int(10) UNSIGNED DEFAULT 0,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `domain_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'When renew domain',
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `domain_setup_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Domain setup / TLD id',
  `coupon_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=> Only Hosting, \r\n2=> Hosting and Domain both,\r\n3=> Only Domain,\r\n4=> Domain Renew,',
  `id_protection` tinyint(4) NOT NULL DEFAULT 0,
  `reg_period` int(11) NOT NULL DEFAULT 0 COMMENT 'For domain',
  `billing_cycle` tinyint(1) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `hostname` varchar(40) DEFAULT NULL,
  `ns1` varchar(255) DEFAULT NULL,
  `ns2` varchar(255) DEFAULT NULL,
  `ns3` varchar(255) DEFAULT NULL,
  `ns4` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `price` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000 COMMENT 'Basic amount / price',
  `setup_fee` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000 COMMENT 'Additional amount / price',
  `discount_applied` tinyint(1) NOT NULL DEFAULT 0,
  `coupon_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Discount type of coupon \r\n0=> %, 1=> Fixed',
  `coupon_discount` decimal(28,8) NOT NULL DEFAULT 0.00000000 COMMENT 'Discount rate / amount of the coupon ',
  `discount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000 COMMENT 'Discount amount',
  `total` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000 COMMENT '(Price + setup) / (Basic+ additional)',
  `after_discount` decimal(28,16) NOT NULL DEFAULT 0.0000000000000000,
  `config_options` varchar(255) DEFAULT NULL COMMENT 'For service / hosting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_attachments`
--

CREATE TABLE `support_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `support_message_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `support_ticket_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `message` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `ticket` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Open, 1: Answered, 2: Replied, 3: Closed',
  `priority` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Low, 2 = medium, 3 = heigh',
  `last_reply` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `post_balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx_type` varchar(40) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `remark` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `update_logs`
--

CREATE TABLE `update_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(40) DEFAULT NULL,
  `update_log` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstname` varchar(40) DEFAULT NULL,
  `lastname` varchar(40) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email` varchar(40) NOT NULL,
  `dial_code` varchar(40) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `ref_by` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `password` varchar(255) NOT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: banned, 1: active',
  `kyc_data` text DEFAULT NULL,
  `kyc_rejection_reason` varchar(255) DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: KYC Unverified, 2: KYC pending, 1: KYC verified',
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: email unverified, 1: email verified',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: mobile unverified, 1: mobile verified',
  `profile_complete` tinyint(1) NOT NULL DEFAULT 0,
  `ver_code` varchar(40) DEFAULT NULL COMMENT 'stores verification code',
  `ver_code_send_at` datetime DEFAULT NULL COMMENT 'verification send time',
  `ts` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: 2fa off, 1: 2fa on',
  `tv` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: 2fa unverified, 1: 2fa verified',
  `tsc` varchar(255) DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `provider` varchar(40) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_logins`
--

CREATE TABLE `user_logins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_ip` varchar(40) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `country` varchar(40) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `longitude` varchar(40) DEFAULT NULL,
  `latitude` varchar(40) DEFAULT NULL,
  `browser` varchar(40) DEFAULT NULL,
  `os` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`,`username`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `billing_settings`
--
ALTER TABLE `billing_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cancel_requests`
--
ALTER TABLE `cancel_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `configurable_groups`
--
ALTER TABLE `configurable_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `configurable_group_options`
--
ALTER TABLE `configurable_group_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `configurable_group_sub_options`
--
ALTER TABLE `configurable_group_sub_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_job_logs`
--
ALTER TABLE `cron_job_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_schedules`
--
ALTER TABLE `cron_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_tokens`
--
ALTER TABLE `device_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `domain_pricings`
--
ALTER TABLE `domain_pricings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `domain_registers`
--
ALTER TABLE `domain_registers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `domain_setups`
--
ALTER TABLE `domain_setups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extensions`
--
ALTER TABLE `extensions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontends`
--
ALTER TABLE `frontends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gateways`
--
ALTER TABLE `gateways`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `gateway_currencies`
--
ALTER TABLE `gateway_currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostings`
--
ALTER TABLE `hostings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hosting_configs`
--
ALTER TABLE `hosting_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pricings`
--
ALTER TABLE `pricings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_configurations`
--
ALTER TABLE `product_configurations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `server_groups`
--
ALTER TABLE `server_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_attachments`
--
ALTER TABLE `support_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `update_logs`
--
ALTER TABLE `update_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`email`);

--
-- Indexes for table `user_logins`
--
ALTER TABLE `user_logins`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing_settings`
--
ALTER TABLE `billing_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cancel_requests`
--
ALTER TABLE `cancel_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `configurable_groups`
--
ALTER TABLE `configurable_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `configurable_group_options`
--
ALTER TABLE `configurable_group_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `configurable_group_sub_options`
--
ALTER TABLE `configurable_group_sub_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cron_job_logs`
--
ALTER TABLE `cron_job_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `cron_schedules`
--
ALTER TABLE `cron_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_tokens`
--
ALTER TABLE `device_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `domains`
--
ALTER TABLE `domains`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `domain_pricings`
--
ALTER TABLE `domain_pricings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `domain_registers`
--
ALTER TABLE `domain_registers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `domain_setups`
--
ALTER TABLE `domain_setups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `extensions`
--
ALTER TABLE `extensions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `frontends`
--
ALTER TABLE `frontends`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `gateways`
--
ALTER TABLE `gateways`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `gateway_currencies`
--
ALTER TABLE `gateway_currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hostings`
--
ALTER TABLE `hostings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hosting_configs`
--
ALTER TABLE `hosting_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `permission_role`
--
ALTER TABLE `permission_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=333;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricings`
--
ALTER TABLE `pricings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_configurations`
--
ALTER TABLE `product_configurations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `server_groups`
--
ALTER TABLE `server_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shopping_carts`
--
ALTER TABLE `shopping_carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_attachments`
--
ALTER TABLE `support_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `update_logs`
--
ALTER TABLE `update_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_logins`
--
ALTER TABLE `user_logins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT INTO `gateways` (`form_id`, `code`, `name`, `alias`, `image`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `created_at`, `updated_at`) VALUES
(0, 126, 'bKash', 'BKash', '67e1432683b5a1742816038.png', 1, '{\"username\":{\"title\":\"Username\",\"global\":true,\"value\":\"----------\"},\"password\":{\"title\":\"Password\",\"global\":true,\"value\":\"----------\"},\"app_key\":{\"title\":\"App Key\",\"global\":true,\"value\":\"----------\"},\"app_secret\":{\"title\":\"App Secret\",\"global\":true,\"value\":\"----------\"}}', '{\"BDT\":\"BDT\"}', 0, NULL, NULL, NULL, '2025-03-16 03:58:31');

ALTER TABLE `deposits` ADD `is_web` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'This will be 1 if the request is from NextJs application' AFTER `from_api`;

ALTER TABLE `deposits` CHANGE `admin_feedback` `admin_feedback` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `general_settings` ADD `config_progress` TEXT NULL DEFAULT NULL AFTER `currency_format`;




ALTER TABLE server_groups ADD COLUMN type tinyint(1) DEFAULT 1 AFTER name;
ALTER TABLE `server_groups` CHANGE `type` `type` TINYINT(1) NULL DEFAULT '1' COMMENT '1 => Cpanel, 2 => Directadmin, 3 => Plesk';

ALTER TABLE hostings ADD COLUMN plesk_customer_id varchar(255) AFTER invoice;
ALTER TABLE products DROP COLUMN module_type;

ALTER TABLE `servers` CHANGE `api_token` `api_token` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `servers` CHANGE `security_token` `security_token` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `hostings` CHANGE `package_name` `package_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'From API';


UPDATE `permissions` 
SET 
    `code` = 'admin.server.login', 
    `name` = 'Server Login' 
WHERE `code` = 'admin.server.login.whm';

UPDATE `permissions` 
SET 
    `code` = 'admin.get.server.package', 
    `name` = 'Get Server Packages' 
WHERE `code` = 'admin.get.whm.package';

UPDATE `permissions` 
SET 
    `code` = 'admin.module.login.hosting', 
    `name` = 'Login Hosting Account' 
WHERE `code` = 'admin.module.cpanel.login';


UPDATE `gateways` SET `supported_currencies` = '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\",\"ARS\":\"ARS\",\"BRL\":\"BRL\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"MXN\":\"MXN\",\"PEN\":\"PEN\",\"UYU\":\"UYU\",\"VEF\":\"VEF\",\"BOB\":\"BOB\"}' WHERE `gateways`.`id` = 36;
