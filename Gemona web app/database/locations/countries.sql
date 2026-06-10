-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 17, 2024 at 09:44 AM
-- Server version: 8.1.0
-- PHP Version: 8.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopking`
--

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int NOT NULL,
  `code` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint NOT NULL DEFAULT '5' COMMENT '5 = Active , 10 = Inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `code`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'AF', 'Afghanistan', 5, '2021-04-06 01:06:30', '2021-10-11 00:34:13', NULL),
(2, 'AL', 'Albania', 5, '2021-04-06 01:06:30', NULL, NULL),
(3, 'DZ', 'Algeria', 5, '2021-04-06 01:06:30', NULL, NULL),
(4, 'AS', 'American Samoa', 5, '2021-04-06 01:06:30', NULL, NULL),
(5, 'AD', 'Andorra', 5, '2021-04-06 01:06:30', NULL, NULL),
(6, 'AO', 'Angola', 5, '2021-04-06 01:06:30', NULL, NULL),
(7, 'AI', 'Anguilla', 5, '2021-04-06 01:06:30', NULL, NULL),
(8, 'AQ', 'Antarctica', 5, '2021-04-06 01:06:30', NULL, NULL),
(9, 'AG', 'Antigua And Barbuda', 5, '2021-04-06 01:06:30', NULL, NULL),
(10, 'AR', 'Argentina', 5, '2021-04-06 01:06:30', NULL, NULL),
(11, 'AM', 'Armenia', 5, '2021-04-06 01:06:30', NULL, NULL),
(12, 'AW', 'Aruba', 5, '2021-04-06 01:06:30', NULL, NULL),
(13, 'AU', 'Australia', 5, '2021-04-06 01:06:30', NULL, NULL),
(14, 'AT', 'Austria', 5, '2021-04-06 01:06:30', NULL, NULL),
(15, 'AZ', 'Azerbaijan', 5, '2021-04-06 01:06:30', NULL, NULL),
(16, 'BS', 'Bahamas The', 5, '2021-04-06 01:06:30', NULL, NULL),
(17, 'BH', 'Bahrain', 5, '2021-04-06 01:06:30', NULL, NULL),
(18, 'BD', 'Bangladesh', 5, '2021-04-06 01:06:30', NULL, NULL),
(19, 'BB', 'Barbados', 5, '2021-04-06 01:06:30', NULL, NULL),
(20, 'BY', 'Belarus', 5, '2021-04-06 01:06:30', NULL, NULL),
(21, 'BE', 'Belgium', 5, '2021-04-06 01:06:30', NULL, NULL),
(22, 'BZ', 'Belize', 5, '2021-04-06 01:06:30', NULL, NULL),
(23, 'BJ', 'Benin', 5, '2021-04-06 01:06:30', NULL, NULL),
(24, 'BM', 'Bermuda', 5, '2021-04-06 01:06:30', NULL, NULL),
(25, 'BT', 'Bhutan', 5, '2021-04-06 01:06:30', NULL, NULL),
(26, 'BO', 'Bolivia', 5, '2021-04-06 01:06:30', NULL, NULL),
(27, 'BA', 'Bosnia and Herzegovina', 5, '2021-04-06 01:06:30', NULL, NULL),
(28, 'BW', 'Botswana', 5, '2021-04-06 01:06:30', NULL, NULL),
(29, 'BV', 'Bouvet Island', 5, '2021-04-06 01:06:30', NULL, NULL),
(30, 'BR', 'Brazil', 5, '2021-04-06 01:06:30', NULL, NULL),
(31, 'IO', 'British Indian Ocean Territory', 5, '2021-04-06 01:06:30', NULL, NULL),
(32, 'BN', 'Brunei', 5, '2021-04-06 01:06:30', NULL, NULL),
(33, 'BG', 'Bulgaria', 5, '2021-04-06 01:06:30', NULL, NULL),
(34, 'BF', 'Burkina Faso', 5, '2021-04-06 01:06:30', NULL, NULL),
(35, 'BI', 'Burundi', 5, '2021-04-06 01:06:30', NULL, NULL),
(36, 'KH', 'Cambodia', 5, '2021-04-06 01:06:30', NULL, NULL),
(37, 'CM', 'Cameroon', 5, '2021-04-06 01:06:30', NULL, NULL),
(38, 'CA', 'Canada', 5, '2021-04-06 01:06:30', NULL, NULL),
(39, 'CV', 'Cape Verde', 5, '2021-04-06 01:06:30', NULL, NULL),
(40, 'KY', 'Cayman Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(41, 'CF', 'Central African Republic', 5, '2021-04-06 01:06:30', NULL, NULL),
(42, 'TD', 'Chad', 5, '2021-04-06 01:06:30', NULL, NULL),
(43, 'CL', 'Chile', 5, '2021-04-06 01:06:30', NULL, NULL),
(44, 'CN', 'China', 5, '2021-04-06 01:06:30', NULL, NULL),
(45, 'CX', 'Christmas Island', 5, '2021-04-06 01:06:30', NULL, NULL),
(46, 'CC', 'Cocos (Keeling) Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(47, 'CO', 'Colombia', 5, '2021-04-06 01:06:30', NULL, NULL),
(48, 'KM', 'Comoros', 5, '2021-04-06 01:06:30', NULL, NULL),
(49, 'CG', 'Republic Of The Congo', 5, '2021-04-06 01:06:30', NULL, NULL),
(50, 'CD', 'Democratic Republic Of The Congo', 5, '2021-04-06 01:06:30', NULL, NULL),
(51, 'CK', 'Cook Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(52, 'CR', 'Costa Rica', 5, '2021-04-06 01:06:30', NULL, NULL),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)', 5, '2021-04-06 01:06:30', NULL, NULL),
(54, 'HR', 'Croatia (Hrvatska)', 5, '2021-04-06 01:06:30', NULL, NULL),
(55, 'CU', 'Cuba', 5, '2021-04-06 01:06:30', NULL, NULL),
(56, 'CY', 'Cyprus', 5, '2021-04-06 01:06:30', NULL, NULL),
(57, 'CZ', 'Czech Republic', 5, '2021-04-06 01:06:30', NULL, NULL),
(58, 'DK', 'Denmark', 5, '2021-04-06 01:06:30', NULL, NULL),
(59, 'DJ', 'Djibouti', 5, '2021-04-06 01:06:30', NULL, NULL),
(60, 'DM', 'Dominica', 5, '2021-04-06 01:06:30', NULL, NULL),
(61, 'DO', 'Dominican Republic', 5, '2021-04-06 01:06:30', NULL, NULL),
(62, 'TP', 'East Timor', 5, '2021-04-06 01:06:30', NULL, NULL),
(63, 'EC', 'Ecuador', 5, '2021-04-06 01:06:30', NULL, NULL),
(64, 'EG', 'Egypt', 5, '2021-04-06 01:06:30', NULL, NULL),
(65, 'SV', 'El Salvador', 5, '2021-04-06 01:06:30', NULL, NULL),
(66, 'GQ', 'Equatorial Guinea', 5, '2021-04-06 01:06:30', NULL, NULL),
(67, 'ER', 'Eritrea', 5, '2021-04-06 01:06:30', NULL, NULL),
(68, 'EE', 'Estonia', 5, '2021-04-06 01:06:30', NULL, NULL),
(69, 'ET', 'Ethiopia', 5, '2021-04-06 01:06:30', NULL, NULL),
(70, 'XA', 'External Territories of Australia', 5, '2021-04-06 01:06:30', NULL, NULL),
(71, 'FK', 'Falkland Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(72, 'FO', 'Faroe Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(73, 'FJ', 'Fiji Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(74, 'FI', 'Finland', 5, '2021-04-06 01:06:30', NULL, NULL),
(75, 'FR', 'France', 5, '2021-04-06 01:06:30', NULL, NULL),
(76, 'GF', 'French Guiana', 5, '2021-04-06 01:06:30', NULL, NULL),
(77, 'PF', 'French Polynesia', 5, '2021-04-06 01:06:30', NULL, NULL),
(78, 'TF', 'French Southern Territories', 5, '2021-04-06 01:06:30', NULL, NULL),
(79, 'GA', 'Gabon', 5, '2021-04-06 01:06:30', NULL, NULL),
(80, 'GM', 'Gambia The', 5, '2021-04-06 01:06:30', NULL, NULL),
(81, 'GE', 'Georgia', 5, '2021-04-06 01:06:30', NULL, NULL),
(82, 'DE', 'Germany', 5, '2021-04-06 01:06:30', NULL, NULL),
(83, 'GH', 'Ghana', 5, '2021-04-06 01:06:30', NULL, NULL),
(84, 'GI', 'Gibraltar', 5, '2021-04-06 01:06:30', NULL, NULL),
(85, 'GR', 'Greece', 5, '2021-04-06 01:06:30', NULL, NULL),
(86, 'GL', 'Greenland', 5, '2021-04-06 01:06:30', NULL, NULL),
(87, 'GD', 'Grenada', 5, '2021-04-06 01:06:30', NULL, NULL),
(88, 'GP', 'Guadeloupe', 5, '2021-04-06 01:06:30', NULL, NULL),
(89, 'GU', 'Guam', 5, '2021-04-06 01:06:30', NULL, NULL),
(90, 'GT', 'Guatemala', 5, '2021-04-06 01:06:30', NULL, NULL),
(91, 'XU', 'Guernsey and Alderney', 5, '2021-04-06 01:06:30', NULL, NULL),
(92, 'GN', 'Guinea', 5, '2021-04-06 01:06:30', NULL, NULL),
(93, 'GW', 'Guinea-Bissau', 5, '2021-04-06 01:06:30', NULL, NULL),
(94, 'GY', 'Guyana', 5, '2021-04-06 01:06:30', NULL, NULL),
(95, 'HT', 'Haiti', 5, '2021-04-06 01:06:30', NULL, NULL),
(96, 'HM', 'Heard and McDonald Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(97, 'HN', 'Honduras', 5, '2021-04-06 01:06:30', NULL, NULL),
(98, 'HK', 'Hong Kong S.A.R.', 5, '2021-04-06 01:06:30', NULL, NULL),
(99, 'HU', 'Hungary', 5, '2021-04-06 01:06:30', NULL, NULL),
(100, 'IS', 'Iceland', 5, '2021-04-06 01:06:30', NULL, NULL),
(101, 'IN', 'India', 5, '2021-04-06 01:06:30', NULL, NULL),
(102, 'ID', 'Indonesia', 5, '2021-04-06 01:06:30', NULL, NULL),
(103, 'IR', 'Iran', 5, '2021-04-06 01:06:30', NULL, NULL),
(104, 'IQ', 'Iraq', 5, '2021-04-06 01:06:30', NULL, NULL),
(105, 'IE', 'Ireland', 5, '2021-04-06 01:06:30', NULL, NULL),
(106, 'IL', 'Israel', 5, '2021-04-06 01:06:30', NULL, NULL),
(107, 'IT', 'Italy', 5, '2021-04-06 01:06:30', NULL, NULL),
(108, 'JM', 'Jamaica', 5, '2021-04-06 01:06:30', NULL, NULL),
(109, 'JP', 'Japan', 5, '2021-04-06 01:06:30', NULL, NULL),
(110, 'XJ', 'Jersey', 5, '2021-04-06 01:06:30', NULL, NULL),
(111, 'JO', 'Jordan', 5, '2021-04-06 01:06:30', NULL, NULL),
(112, 'KZ', 'Kazakhstan', 5, '2021-04-06 01:06:30', NULL, NULL),
(113, 'KE', 'Kenya', 5, '2021-04-06 01:06:30', NULL, NULL),
(114, 'KI', 'Kiribati', 5, '2021-04-06 01:06:30', NULL, NULL),
(115, 'KP', 'Korea North', 5, '2021-04-06 01:06:30', NULL, NULL),
(116, 'KR', 'Korea South', 5, '2021-04-06 01:06:30', NULL, NULL),
(117, 'KW', 'Kuwait', 5, '2021-04-06 01:06:30', NULL, NULL),
(118, 'KG', 'Kyrgyzstan', 5, '2021-04-06 01:06:30', NULL, NULL),
(119, 'LA', 'Laos', 5, '2021-04-06 01:06:30', NULL, NULL),
(120, 'LV', 'Latvia', 5, '2021-04-06 01:06:30', NULL, NULL),
(121, 'LB', 'Lebanon', 5, '2021-04-06 01:06:30', NULL, NULL),
(122, 'LS', 'Lesotho', 5, '2021-04-06 01:06:30', NULL, NULL),
(123, 'LR', 'Liberia', 5, '2021-04-06 01:06:30', NULL, NULL),
(124, 'LY', 'Libya', 5, '2021-04-06 01:06:30', NULL, NULL),
(125, 'LI', 'Liechtenstein', 5, '2021-04-06 01:06:30', NULL, NULL),
(126, 'LT', 'Lithuania', 5, '2021-04-06 01:06:30', NULL, NULL),
(127, 'LU', 'Luxembourg', 5, '2021-04-06 01:06:30', NULL, NULL),
(128, 'MO', 'Macau S.A.R.', 5, '2021-04-06 01:06:30', NULL, NULL),
(129, 'MK', 'Macedonia', 5, '2021-04-06 01:06:30', NULL, NULL),
(130, 'MG', 'Madagascar', 5, '2021-04-06 01:06:30', NULL, NULL),
(131, 'MW', 'Malawi', 5, '2021-04-06 01:06:30', NULL, NULL),
(132, 'MY', 'Malaysia', 5, '2021-04-06 01:06:30', NULL, NULL),
(133, 'MV', 'Maldives', 5, '2021-04-06 01:06:30', NULL, NULL),
(134, 'ML', 'Mali', 5, '2021-04-06 01:06:30', NULL, NULL),
(135, 'MT', 'Malta', 5, '2021-04-06 01:06:30', NULL, NULL),
(136, 'XM', 'Man (Isle of)', 5, '2021-04-06 01:06:30', NULL, NULL),
(137, 'MH', 'Marshall Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(138, 'MQ', 'Martinique', 5, '2021-04-06 01:06:30', NULL, NULL),
(139, 'MR', 'Mauritania', 5, '2021-04-06 01:06:30', NULL, NULL),
(140, 'MU', 'Mauritius', 5, '2021-04-06 01:06:30', NULL, NULL),
(141, 'YT', 'Mayotte', 5, '2021-04-06 01:06:30', NULL, NULL),
(142, 'MX', 'Mexico', 5, '2021-04-06 01:06:30', NULL, NULL),
(143, 'FM', 'Micronesia', 5, '2021-04-06 01:06:30', NULL, NULL),
(144, 'MD', 'Moldova', 5, '2021-04-06 01:06:30', NULL, NULL),
(145, 'MC', 'Monaco', 5, '2021-04-06 01:06:30', NULL, NULL),
(146, 'MN', 'Mongolia', 5, '2021-04-06 01:06:30', NULL, NULL),
(147, 'MS', 'Montserrat', 5, '2021-04-06 01:06:30', NULL, NULL),
(148, 'MA', 'Morocco', 5, '2021-04-06 01:06:30', NULL, NULL),
(149, 'MZ', 'Mozambique', 5, '2021-04-06 01:06:30', NULL, NULL),
(150, 'MM', 'Myanmar', 5, '2021-04-06 01:06:30', NULL, NULL),
(151, 'NA', 'Namibia', 5, '2021-04-06 01:06:30', NULL, NULL),
(152, 'NR', 'Nauru', 5, '2021-04-06 01:06:30', NULL, NULL),
(153, 'NP', 'Nepal', 5, '2021-04-06 01:06:30', NULL, NULL),
(154, 'AN', 'Netherlands Antilles', 5, '2021-04-06 01:06:30', NULL, NULL),
(155, 'NL', 'Netherlands The', 5, '2021-04-06 01:06:30', NULL, NULL),
(156, 'NC', 'New Caledonia', 5, '2021-04-06 01:06:30', NULL, NULL),
(157, 'NZ', 'New Zealand', 5, '2021-04-06 01:06:30', NULL, NULL),
(158, 'NI', 'Nicaragua', 5, '2021-04-06 01:06:30', NULL, NULL),
(159, 'NE', 'Niger', 5, '2021-04-06 01:06:30', NULL, NULL),
(160, 'NG', 'Nigeria', 5, '2021-04-06 01:06:30', NULL, NULL),
(161, 'NU', 'Niue', 5, '2021-04-06 01:06:30', NULL, NULL),
(162, 'NF', 'Norfolk Island', 5, '2021-04-06 01:06:30', NULL, NULL),
(163, 'MP', 'Northern Mariana Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(164, 'NO', 'Norway', 5, '2021-04-06 01:06:30', NULL, NULL),
(165, 'OM', 'Oman', 5, '2021-04-06 01:06:30', NULL, NULL),
(166, 'PK', 'Pakistan', 5, '2021-04-06 01:06:30', NULL, NULL),
(167, 'PW', 'Palau', 5, '2021-04-06 01:06:30', NULL, NULL),
(168, 'PS', 'Palestinian Territory Occupied', 5, '2021-04-06 01:06:30', NULL, NULL),
(169, 'PA', 'Panama', 5, '2021-04-06 01:06:30', NULL, NULL),
(170, 'PG', 'Papua new Guinea', 5, '2021-04-06 01:06:30', NULL, NULL),
(171, 'PY', 'Paraguay', 5, '2021-04-06 01:06:30', NULL, NULL),
(172, 'PE', 'Peru', 5, '2021-04-06 01:06:30', NULL, NULL),
(173, 'PH', 'Philippines', 5, '2021-04-06 01:06:30', NULL, NULL),
(174, 'PN', 'Pitcairn Island', 5, '2021-04-06 01:06:30', NULL, NULL),
(175, 'PL', 'Poland', 5, '2021-04-06 01:06:30', NULL, NULL),
(176, 'PT', 'Portugal', 5, '2021-04-06 01:06:30', NULL, NULL),
(177, 'PR', 'Puerto Rico', 5, '2021-04-06 01:06:30', NULL, NULL),
(178, 'QA', 'Qatar', 5, '2021-04-06 01:06:30', NULL, NULL),
(179, 'RE', 'Reunion', 5, '2021-04-06 01:06:30', NULL, NULL),
(180, 'RO', 'Romania', 5, '2021-04-06 01:06:30', NULL, NULL),
(181, 'RU', 'Russia', 5, '2021-04-06 01:06:30', NULL, NULL),
(182, 'RW', 'Rwanda', 5, '2021-04-06 01:06:30', NULL, NULL),
(183, 'SH', 'Saint Helena', 5, '2021-04-06 01:06:30', NULL, NULL),
(184, 'KN', 'Saint Kitts And Nevis', 5, '2021-04-06 01:06:30', NULL, NULL),
(185, 'LC', 'Saint Lucia', 5, '2021-04-06 01:06:30', NULL, NULL),
(186, 'PM', 'Saint Pierre and Miquelon', 5, '2021-04-06 01:06:30', NULL, NULL),
(187, 'VC', 'Saint Vincent And The Grenadines', 5, '2021-04-06 01:06:30', NULL, NULL),
(188, 'WS', 'Samoa', 5, '2021-04-06 01:06:30', NULL, NULL),
(189, 'SM', 'San Marino', 5, '2021-04-06 01:06:30', NULL, NULL),
(190, 'ST', 'Sao Tome and Principe', 5, '2021-04-06 01:06:30', NULL, NULL),
(191, 'SA', 'Saudi Arabia', 5, '2021-04-06 01:06:30', NULL, NULL),
(192, 'SN', 'Senegal', 5, '2021-04-06 01:06:30', NULL, NULL),
(193, 'RS', 'Serbia', 5, '2021-04-06 01:06:30', NULL, NULL),
(194, 'SC', 'Seychelles', 5, '2021-04-06 01:06:30', NULL, NULL),
(195, 'SL', 'Sierra Leone', 5, '2021-04-06 01:06:30', NULL, NULL),
(196, 'SG', 'Singapore', 5, '2021-04-06 01:06:30', NULL, NULL),
(197, 'SK', 'Slovakia', 5, '2021-04-06 01:06:30', NULL, NULL),
(198, 'SI', 'Slovenia', 5, '2021-04-06 01:06:30', NULL, NULL),
(199, 'XG', 'Smaller Territories of the UK', 5, '2021-04-06 01:06:30', NULL, NULL),
(200, 'SB', 'Solomon Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(201, 'SO', 'Somalia', 5, '2021-04-06 01:06:30', NULL, NULL),
(202, 'ZA', 'South Africa', 5, '2021-04-06 01:06:30', NULL, NULL),
(203, 'GS', 'South Georgia', 5, '2021-04-06 01:06:30', NULL, NULL),
(204, 'SS', 'South Sudan', 5, '2021-04-06 01:06:30', NULL, NULL),
(205, 'ES', 'Spain', 5, '2021-04-06 01:06:30', NULL, NULL),
(206, 'LK', 'Sri Lanka', 5, '2021-04-06 01:06:30', NULL, NULL),
(207, 'SD', 'Sudan', 5, '2021-04-06 01:06:30', NULL, NULL),
(208, 'SR', 'Suriname', 5, '2021-04-06 01:06:30', NULL, NULL),
(209, 'SJ', 'Svalbard And Jan Mayen Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(210, 'SZ', 'Swaziland', 5, '2021-04-06 01:06:30', NULL, NULL),
(211, 'SE', 'Sweden', 5, '2021-04-06 01:06:30', NULL, NULL),
(212, 'CH', 'Switzerland', 5, '2021-04-06 01:06:30', NULL, NULL),
(213, 'SY', 'Syria', 5, '2021-04-06 01:06:30', NULL, NULL),
(214, 'TW', 'Taiwan', 5, '2021-04-06 01:06:30', NULL, NULL),
(215, 'TJ', 'Tajikistan', 5, '2021-04-06 01:06:30', NULL, NULL),
(216, 'TZ', 'Tanzania', 5, '2021-04-06 01:06:30', NULL, NULL),
(217, 'TH', 'Thailand', 5, '2021-04-06 01:06:30', NULL, NULL),
(218, 'TG', 'Togo', 5, '2021-04-06 01:06:30', NULL, NULL),
(219, 'TK', 'Tokelau', 5, '2021-04-06 01:06:30', NULL, NULL),
(220, 'TO', 'Tonga', 5, '2021-04-06 01:06:30', NULL, NULL),
(221, 'TT', 'Trinidad And Tobago', 5, '2021-04-06 01:06:30', NULL, NULL),
(222, 'TN', 'Tunisia', 5, '2021-04-06 01:06:30', NULL, NULL),
(223, 'TR', 'Turkey', 5, '2021-04-06 01:06:30', NULL, NULL),
(224, 'TM', 'Turkmenistan', 5, '2021-04-06 01:06:30', NULL, NULL),
(225, 'TC', 'Turks And Caicos Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(226, 'TV', 'Tuvalu', 5, '2021-04-06 01:06:30', NULL, NULL),
(227, 'UG', 'Uganda', 5, '2021-04-06 01:06:30', NULL, NULL),
(228, 'UA', 'Ukraine', 5, '2021-04-06 01:06:30', NULL, NULL),
(229, 'AE', 'United Arab Emirates', 5, '2021-04-06 01:06:30', NULL, NULL),
(230, 'GB', 'United Kingdom', 5, '2021-04-06 01:06:30', NULL, NULL),
(231, 'US', 'United States', 5, '2021-04-06 01:06:30', NULL, NULL),
(232, 'UM', 'United States Minor Outlying Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(233, 'UY', 'Uruguay', 5, '2021-04-06 01:06:30', NULL, NULL),
(234, 'UZ', 'Uzbekistan', 5, '2021-04-06 01:06:30', NULL, NULL),
(235, 'VU', 'Vanuatu', 5, '2021-04-06 01:06:30', NULL, NULL),
(236, 'VA', 'Vatican City State (Holy See)', 5, '2021-04-06 01:06:30', NULL, NULL),
(237, 'VE', 'Venezuela', 5, '2021-04-06 01:06:30', NULL, NULL),
(238, 'VN', 'Vietnam', 5, '2021-04-06 01:06:30', NULL, NULL),
(239, 'VG', 'Virgin Islands (British)', 5, '2021-04-06 01:06:30', NULL, NULL),
(240, 'VI', 'Virgin Islands (US)', 5, '2021-04-06 01:06:30', NULL, NULL),
(241, 'WF', 'Wallis And Futuna Islands', 5, '2021-04-06 01:06:30', NULL, NULL),
(242, 'EH', 'Western Sahara', 5, '2021-04-06 01:06:30', NULL, NULL),
(243, 'YE', 'Yemen', 5, '2021-04-06 01:06:30', NULL, NULL),
(244, 'YU', 'Yugoslavia', 5, '2021-04-06 01:06:30', NULL, NULL),
(245, 'ZM', 'Zambia', 5, '2021-04-06 01:06:30', NULL, NULL),
(246, 'ZW', 'Zimbabwe', 5, '2021-04-06 01:06:30', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
