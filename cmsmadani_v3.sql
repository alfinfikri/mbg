-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 10, 2025 at 05:40 PM
-- Server version: 8.0.40-0ubuntu0.22.04.1
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmsmadani_v3`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint UNSIGNED NOT NULL,
  `log_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `subject_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seotitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` bigint UNSIGNED NOT NULL,
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL DEFAULT '1',
  `updated_by` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `parent` bigint NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seotitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint UNSIGNED NOT NULL,
  `parent` bigint NOT NULL DEFAULT '0',
  `post_id` bigint DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `active` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `status` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `components`
--

CREATE TABLE `components` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'component',
  `active` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `components`
--

INSERT INTO `components` (`id`, `title`, `author`, `folder`, `type`, `active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Gallery', 'CMSMadani Kota Serang', 'gallery', 'component', 'Y', 1, 1, '2019-10-10 19:25:05', '2024-04-16 09:56:37'),
(2, 'Contact', 'CMSMadani Kota Serang', 'contact', 'component', 'Y', 1, 1, '2019-10-10 19:25:17', '2024-04-16 09:56:29');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallerys`
--

CREATE TABLE `gallerys` (
  `id` bigint UNSIGNED NOT NULL,
  `album_id` bigint DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_gallery` int DEFAULT NULL,
  `id_album` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `infografis`
--

CREATE TABLE `infografis` (
  `id` bigint UNSIGNED NOT NULL,
  `gambar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL DEFAULT '1',
  `updated_by` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint UNSIGNED NOT NULL,
  `parent` bigint NOT NULL DEFAULT '0',
  `group` bigint NOT NULL DEFAULT '1',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` bigint NOT NULL DEFAULT '1',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `parent`, `group`, `title`, `url`, `class`, `target`, `position`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 'Home', '/', NULL, 'none', 1, 1, 1, '2019-10-14 15:23:50', '2019-10-14 16:15:31'),
(12, 11, 1, 'Tentang', 'pages/about-us', NULL, '_blank', 3, 1, 1, '2022-02-22 04:43:55', '2022-02-22 17:00:04'),
(13, 11, 1, 'Pelayanan', 'pages/services', NULL, '_blank', 4, 1, 1, '2022-02-22 04:45:50', '2022-02-22 17:00:09'),
(14, 0, 1, 'Profil', '#', NULL, 'none', 2, 1, 1, '2022-02-22 17:01:32', '2023-01-29 19:46:02'),
(17, 14, 1, 'Visi & Misi', 'pages/visi-dan-misi', NULL, 'none', 3, 1, 1, '2023-01-12 01:08:42', '2024-03-20 11:29:03'),
(18, 19, 1, 'Informasi Publik Berkala', 'pages/informasi-publik-berkala', NULL, 'none', 10, 1, 1, '2023-01-14 06:54:23', '2024-04-16 11:08:35'),
(19, 0, 1, 'Informasi Publik', '#', NULL, 'none', 9, 1, 1, '2023-01-14 06:58:00', '2024-04-16 11:08:35'),
(22, 14, 1, 'Arti Lambang', 'pages/arti-lambang-kota-serang', NULL, 'none', 5, 1, 1, '2023-01-29 19:57:06', '2024-04-16 11:08:35'),
(23, 14, 1, 'Penghargaan', 'pages/prestasi-dan-penghargaan', NULL, 'none', 6, 1, 1, '2023-01-29 19:57:38', '2024-04-16 11:08:35'),
(24, 14, 1, 'Letak Geografis', 'pages/letak-geografis', NULL, 'none', 7, 1, 1, '2023-01-29 19:58:19', '2024-04-16 11:08:35'),
(25, 14, 1, 'Sejarah', 'pages/sejarah-kota-serang', NULL, 'none', 4, 1, 1, '2023-01-29 20:00:00', '2024-04-16 11:08:35'),
(26, 19, 1, 'Pendidikan', 'pages/pendidikan', NULL, 'none', 11, 1, 1, '2023-01-29 20:21:38', '2024-04-16 11:08:35'),
(27, 19, 1, 'Kependudukan', 'pages/kependudukan', NULL, 'none', 12, 1, 1, '2023-01-29 20:33:15', '2024-04-16 11:08:35'),
(28, 19, 1, 'Perdagangan Jasa', 'pages/perdagangan-jasa', NULL, 'none', 13, 1, 1, '2023-01-29 20:34:01', '2024-04-16 11:08:35'),
(31, 0, 1, 'Layanan Publik', '#', NULL, 'none', 15, 1, 1, '2023-01-29 23:01:39', '2024-04-16 11:08:35'),
(32, 31, 1, 'Siaga 112', 'tel:112', NULL, '_blank', 19, 1, 1, '2023-01-29 23:03:30', '2024-04-16 11:08:35'),
(33, 31, 1, 'Ragem', 'https://ragem.serangkota.go.id', NULL, '_blank', 18, 1, 1, '2023-01-29 23:04:06', '2024-04-16 11:08:35'),
(35, 31, 1, 'Serangkota', 'https://serangkota.go.id', NULL, 'none', 17, 1, 1, '2023-01-29 23:05:16', '2024-04-16 11:08:35'),
(38, 46, 1, 'Galeri Album', 'album/all', NULL, 'none', 22, 1, 1, '2023-02-12 21:19:49', '2024-04-16 11:08:35'),
(46, 0, 1, 'Pusat Media', '#', NULL, 'none', 20, 1, 1, '2024-03-18 11:54:10', '2024-04-16 11:08:35'),
(47, 46, 1, 'Semua Berita', 'category/all', NULL, 'none', 21, 1, 1, '2024-03-18 11:56:22', '2024-04-16 11:08:35'),
(51, 14, 1, 'Pejabat Daerah Kota Serang', 'pages/pejabat-daerah-kota-serang', NULL, 'none', 8, 1, 1, '2024-03-18 12:26:38', '2024-04-16 11:08:35'),
(54, 19, 1, 'Kearifan Local', 'pages/wisata-kearifan-lokal', NULL, 'none', 14, 1, 1, '2024-03-28 11:35:31', '2024-04-16 11:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_09_22_084207_create_activity_log_table', 1),
(5, '2019_09_22_084507_create_permission_tables', 1),
(6, '2019_10_03_031736_create_settings_table', 2),
(7, '2019_10_08_114314_create_tags_table', 3),
(8, '2019_10_09_042443_create_categories_table', 4),
(9, '2019_10_10_081154_create_comments_table', 5),
(10, '2019_10_10_113552_create_themes_table', 6),
(11, '2019_10_11_030739_create_components_table', 7),
(12, '2019_10_11_090451_create_contacts_table', 8),
(13, '2019_10_11_100436_create_gallerys_table', 9),
(14, '2019_10_11_100452_create_albums_table', 9),
(15, '2019_10_11_221843_create_pages_table', 10),
(16, '2019_10_14_042900_create_posts_table', 11),
(17, '2019_10_14_043042_create_post_gallerys_table', 11),
(18, '2019_10_14_222137_create_menus_table', 12),
(19, '2019_10_15_055433_create_subscribes_table', 13),
(20, '2022_01_30_130613_create_banner_table', 14),
(21, '2022_02_10_043838_create_runningtext_table', 15);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` int UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\User', 1),
(6, 'App\\User', 6);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seotitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('support@serangkota.go.id', '$2y$10$CDP3ls7QA.xZsqo8iOBPfOt87wMpwn.LtmurmXvRVuVi82y0DYrKC', '2022-10-10 22:02:10'),
('support@serangkota.go.id', '$2y$10$CDP3ls7QA.xZsqo8iOBPfOt87wMpwn.LtmurmXvRVuVi82y0DYrKC', '2022-10-10 22:02:10'),
('gatot.teguhramadhan@gmail.com', '$2y$10$RCj.5MK5Xse/ZMxTprWioeI9HIz5miGCp47zJRjsjek30LdVNZCvC', '2023-09-25 16:39:32');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'create-users', 'web', '2019-10-04 19:32:00', '2019-10-04 19:32:00'),
(2, 'read-users', 'web', '2019-10-04 19:32:00', '2019-10-04 19:32:00'),
(3, 'update-users', 'web', '2019-10-04 19:32:00', '2019-10-04 19:32:00'),
(4, 'delete-users', 'web', '2019-10-04 19:32:00', '2019-10-04 19:32:00'),
(5, 'create-roles', 'web', '2019-10-04 19:32:07', '2019-10-04 19:32:07'),
(6, 'read-roles', 'web', '2019-10-04 19:32:07', '2019-10-04 19:32:07'),
(7, 'update-roles', 'web', '2019-10-04 19:32:07', '2019-10-04 19:32:07'),
(8, 'delete-roles', 'web', '2019-10-04 19:32:08', '2019-10-04 19:32:08'),
(9, 'create-permissions', 'web', '2019-10-04 19:32:22', '2019-10-04 19:32:22'),
(10, 'read-permissions', 'web', '2019-10-04 19:32:22', '2019-10-04 19:32:22'),
(11, 'update-permissions', 'web', '2019-10-04 19:32:22', '2019-10-04 19:32:22'),
(12, 'delete-permissions', 'web', '2019-10-04 19:32:22', '2019-10-04 19:32:22'),
(13, 'create-settings', 'web', '2019-10-05 19:53:03', '2019-10-05 19:53:03'),
(14, 'read-settings', 'web', '2019-10-05 19:53:03', '2019-10-05 19:53:03'),
(15, 'update-settings', 'web', '2019-10-05 19:53:03', '2019-10-05 19:53:03'),
(16, 'delete-settings', 'web', '2019-10-05 19:53:03', '2019-10-05 19:53:03'),
(17, 'create-posts', 'web', '2019-10-08 04:14:28', '2019-10-08 04:14:28'),
(18, 'read-posts', 'web', '2019-10-08 04:14:28', '2019-10-08 04:14:28'),
(19, 'update-posts', 'web', '2019-10-08 04:14:28', '2019-10-08 04:14:28'),
(20, 'delete-posts', 'web', '2019-10-08 04:14:28', '2019-10-08 04:14:28'),
(21, 'create-categories', 'web', '2019-10-08 04:14:38', '2019-10-08 04:14:38'),
(22, 'read-categories', 'web', '2019-10-08 04:14:38', '2019-10-08 04:14:38'),
(23, 'update-categories', 'web', '2019-10-08 04:14:38', '2019-10-08 04:14:38'),
(24, 'delete-categories', 'web', '2019-10-08 04:14:38', '2019-10-08 04:14:38'),
(25, 'create-tags', 'web', '2019-10-08 04:14:44', '2019-10-08 04:14:44'),
(26, 'read-tags', 'web', '2019-10-08 04:14:44', '2019-10-08 04:14:44'),
(27, 'update-tags', 'web', '2019-10-08 04:14:44', '2019-10-08 04:14:44'),
(28, 'delete-tags', 'web', '2019-10-08 04:14:44', '2019-10-08 04:14:44'),
(29, 'create-comments', 'web', '2019-10-08 04:14:57', '2019-10-08 04:14:57'),
(30, 'read-comments', 'web', '2019-10-08 04:14:57', '2019-10-08 04:14:57'),
(31, 'update-comments', 'web', '2019-10-08 04:14:57', '2019-10-08 04:14:57'),
(32, 'delete-comments', 'web', '2019-10-08 04:14:58', '2019-10-08 04:14:58'),
(33, 'create-pages', 'web', '2019-10-08 04:15:03', '2019-10-08 04:15:03'),
(34, 'read-pages', 'web', '2019-10-08 04:15:03', '2019-10-08 04:15:03'),
(35, 'update-pages', 'web', '2019-10-08 04:15:03', '2019-10-08 04:15:03'),
(36, 'delete-pages', 'web', '2019-10-08 04:15:03', '2019-10-08 04:15:03'),
(37, 'create-themes', 'web', '2019-10-08 04:15:10', '2019-10-08 04:15:10'),
(38, 'read-themes', 'web', '2019-10-08 04:15:10', '2019-10-08 04:15:10'),
(39, 'update-themes', 'web', '2019-10-08 04:15:10', '2019-10-08 04:15:10'),
(40, 'delete-themes', 'web', '2019-10-08 04:15:10', '2019-10-08 04:15:10'),
(41, 'create-menumanager', 'web', '2019-10-08 04:15:31', '2019-10-08 04:15:31'),
(42, 'read-menumanager', 'web', '2019-10-08 04:15:31', '2019-10-08 04:15:31'),
(43, 'update-menumanager', 'web', '2019-10-08 04:15:31', '2019-10-08 04:15:31'),
(44, 'delete-menumanager', 'web', '2019-10-08 04:15:31', '2019-10-08 04:15:31'),
(45, 'create-components', 'web', '2019-10-08 04:15:50', '2019-10-08 04:15:50'),
(46, 'read-components', 'web', '2019-10-08 04:15:50', '2019-10-08 04:15:50'),
(47, 'update-components', 'web', '2019-10-08 04:15:50', '2019-10-08 04:15:50'),
(48, 'delete-components', 'web', '2019-10-08 04:15:50', '2019-10-08 04:15:50'),
(49, 'create-contacts', 'web', '2019-10-11 01:22:14', '2019-10-11 01:22:14'),
(50, 'read-contacts', 'web', '2019-10-11 01:22:14', '2019-10-11 01:22:14'),
(51, 'update-contacts', 'web', '2019-10-11 01:22:14', '2019-10-11 01:22:14'),
(52, 'delete-contacts', 'web', '2019-10-11 01:22:14', '2019-10-11 01:22:14'),
(53, 'create-gallerys', 'web', '2019-10-11 01:22:23', '2019-10-11 01:22:23'),
(54, 'read-gallerys', 'web', '2019-10-11 01:22:23', '2019-10-11 01:22:23'),
(55, 'update-gallerys', 'web', '2019-10-11 01:22:23', '2019-10-11 01:22:23'),
(56, 'delete-gallerys', 'web', '2019-10-11 01:22:23', '2019-10-11 01:22:23'),
(57, 'create-subscribes', 'web', '2019-10-14 22:00:09', '2019-10-14 22:00:09'),
(58, 'read-subscribes', 'web', '2019-10-14 22:00:09', '2019-10-14 22:00:09'),
(59, 'update-subscribes', 'web', '2019-10-14 22:00:09', '2019-10-14 22:00:09'),
(60, 'delete-subscribes', 'web', '2019-10-14 22:00:09', '2019-10-14 22:00:09'),
(61, 'create-clark', 'web', '2019-10-14 22:00:54', '2019-10-14 22:00:54'),
(62, 'read-clark', 'web', '2019-10-14 22:00:54', '2019-10-14 22:00:54'),
(63, 'update-clark', 'web', '2019-10-14 22:00:54', '2019-10-14 22:00:54'),
(64, 'delete-clark', 'web', '2019-10-14 22:00:54', '2019-10-14 22:00:54'),
(65, 'create-banner', 'web', '2022-02-11 04:49:51', '2022-02-11 04:51:05'),
(66, 'read-banner', 'web', '2022-02-11 04:49:51', '2022-02-11 04:50:49'),
(67, 'update-banner', 'web', '2022-02-11 04:49:51', '2022-02-11 04:50:42'),
(68, 'delete-banner', 'web', '2022-02-11 04:49:51', '2022-02-11 04:50:33'),
(69, 'create-runningtext', 'web', '2022-02-11 04:52:45', '2022-02-11 04:52:45'),
(70, 'read-runningtext', 'web', '2022-02-11 04:52:45', '2022-02-11 04:52:45'),
(71, 'update-runningtext', 'web', '2022-02-11 04:52:45', '2022-02-11 04:52:45'),
(72, 'delete-runningtext', 'web', '2022-02-11 04:52:45', '2022-02-11 04:52:45'),
(73, 'create-infografis', 'web', '2022-08-28 19:46:42', '2022-08-28 19:46:42'),
(74, 'read-infografis', 'web', '2022-08-28 19:46:42', '2022-08-28 19:46:42'),
(75, 'update-infografis', 'web', '2022-08-28 19:46:42', '2022-08-28 19:46:42'),
(76, 'delete-infografis', 'web', '2022-08-28 19:46:42', '2022-08-28 19:46:42'),
(77, 'create-viewpage', 'web', '2023-03-09 17:08:30', '2023-03-09 17:08:30'),
(78, 'read-viewpage', 'web', '2023-03-09 17:08:30', '2023-03-09 17:08:30'),
(79, 'update-viewpage', 'web', '2023-03-09 17:08:30', '2023-03-09 17:08:30'),
(80, 'delete-viewpage', 'web', '2023-03-09 17:08:30', '2023-03-09 17:08:30'),
(81, 'create-laporancms', 'web', '2023-03-09 19:07:14', '2023-03-09 19:07:14'),
(82, 'read-laporancms', 'web', '2023-03-09 19:07:14', '2023-03-09 19:07:14'),
(83, 'update-laporancms', 'web', '2023-03-09 19:07:14', '2023-03-09 19:07:14'),
(84, 'delete-laporancms', 'web', '2023-03-09 19:07:14', '2023-03-09 19:07:14'),
(89, 'create-laporan', 'web', '2023-03-18 06:59:39', '2023-03-18 06:59:39'),
(90, 'read-laporan', 'web', '2023-03-18 06:59:39', '2023-03-18 06:59:39'),
(91, 'update-laporan', 'web', '2023-03-18 06:59:39', '2023-03-18 06:59:39'),
(92, 'delete-laporan', 'web', '2023-03-18 06:59:39', '2023-03-18 06:59:39');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint NOT NULL,
  `category_id` bigint NOT NULL DEFAULT '1',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seotitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('general','pagination','picture','video') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `active` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `headline` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `comment` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `hits` bigint NOT NULL DEFAULT '1',
  `tanggal` date DEFAULT NULL,
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `post_gallerys`
--

CREATE TABLE `post_gallerys` (
  `id` bigint UNSIGNED NOT NULL,
  `post_id` bigint NOT NULL DEFAULT '1',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'web', '2019-10-04 18:58:36', '2019-10-04 18:58:36'),
(2, 'admin', 'web', '2019-10-04 18:58:54', '2019-10-04 18:58:54'),
(3, 'editor', 'web', '2019-10-04 18:59:08', '2019-10-04 18:59:08'),
(4, 'admin 2', 'web', '2019-10-04 18:59:16', '2023-03-13 01:50:39'),
(5, 'admin 3', 'web', '2023-02-09 03:54:09', '2023-03-13 01:50:28'),
(6, 'superadmin 2', 'web', '2023-05-03 23:22:18', '2023-05-03 23:22:18');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` int UNSIGNED NOT NULL,
  `role_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 2),
(2, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(38, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(46, 2),
(49, 2),
(50, 2),
(51, 2),
(52, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(2, 3),
(3, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(25, 3),
(26, 3),
(27, 3),
(28, 3),
(33, 3),
(34, 3),
(35, 3),
(36, 3),
(1, 2),
(2, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(38, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(46, 2),
(49, 2),
(50, 2),
(51, 2),
(52, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(2, 3),
(3, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(25, 3),
(26, 3),
(27, 3),
(28, 3),
(33, 3),
(34, 3),
(35, 3),
(36, 3),
(3, 5),
(17, 5),
(18, 5),
(19, 5),
(25, 5),
(26, 5),
(27, 5),
(3, 4),
(17, 4),
(18, 4),
(19, 4),
(25, 4),
(26, 4),
(27, 4),
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(1, 6),
(2, 6),
(3, 6),
(4, 6),
(5, 6),
(6, 6),
(7, 6),
(8, 6),
(9, 6),
(10, 6),
(11, 6),
(12, 6),
(13, 6),
(14, 6),
(15, 6),
(16, 6),
(17, 6),
(18, 6),
(19, 6),
(20, 6),
(21, 6),
(22, 6),
(23, 6),
(24, 6),
(25, 6),
(26, 6),
(27, 6),
(28, 6),
(29, 6),
(30, 6),
(31, 6),
(32, 6),
(33, 6),
(34, 6),
(35, 6),
(36, 6),
(37, 6),
(38, 6),
(39, 6),
(40, 6),
(41, 6),
(42, 6),
(43, 6),
(44, 6),
(45, 6),
(46, 6),
(47, 6),
(48, 6),
(49, 6),
(50, 6),
(51, 6),
(52, 6),
(53, 6),
(54, 6),
(55, 6),
(56, 6),
(57, 6),
(58, 6),
(59, 6),
(60, 6),
(61, 6),
(62, 6),
(63, 6),
(64, 6),
(65, 6),
(66, 6),
(67, 6),
(68, 6),
(69, 6),
(70, 6),
(71, 6),
(72, 6),
(73, 6),
(74, 6),
(75, 6),
(76, 6),
(77, 6),
(78, 6),
(79, 6),
(80, 6),
(81, 6),
(82, 6),
(83, 6),
(84, 6),
(89, 6),
(90, 6),
(91, 6),
(92, 6);

-- --------------------------------------------------------

--
-- Table structure for table `runningtext`
--

CREATE TABLE `runningtext` (
  `id` bigint UNSIGNED NOT NULL,
  `isitext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL DEFAULT '1',
  `updated_by` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `runningtext`
--

INSERT INTO `runningtext` (`id`, `isitext`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. 123', 1, 1, '2022-08-29 22:44:29', '2022-08-30 12:41:03');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int UNSIGNED NOT NULL,
  `groups` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `groups`, `options`, `value`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'General', 'web_name', 'Kota Serang', 1, 1, '2019-10-05 20:48:11', '2024-04-16 10:00:22'),
(2, 'General', 'web_url', 'https://serangkota.go.id/', 1, 1, '2019-10-05 21:12:28', '2024-04-16 09:58:45'),
(3, 'General', 'web_description', 'Kota Serang Provinsi Banten', 1, 1, '2019-10-05 21:13:01', '2024-04-16 09:59:54'),
(4, 'General', 'web_keyword', 'CMS Madani SerangKota', 1, 1, '2019-10-05 21:13:42', '2019-10-05 21:13:42'),
(5, 'General', 'web_author', 'Kota Serang', 1, 1, '2019-10-05 21:13:56', '2024-04-16 10:00:07'),
(6, 'General', 'email', 'cmsdev@serangkota.go.id', 1, 1, '2019-10-05 21:14:09', '2024-04-16 10:00:34'),
(7, 'General', 'telephone', '112', 1, 1, '2019-10-05 21:14:26', '2022-01-26 20:54:09'),
(8, 'General', 'fax', '000-0000-0000', 1, 1, '2019-10-05 21:14:38', '2019-10-05 21:14:38'),
(9, 'General', 'address', 'Kota Serang, Provinsi Banten, Indonesia', 1, 1, '2019-10-05 21:14:50', '2019-10-05 21:14:50'),
(10, 'General', 'latitude', '-6.1753871', 1, 1, '2019-10-05 21:15:21', '2019-10-05 21:15:21'),
(11, 'General', 'longitude', '106.8249641', 1, 1, '2019-10-05 21:15:54', '2019-10-05 21:15:54'),
(12, 'General', 'facebook', 'https://www.facebook.com/people/Pemerintah-Kota-Serang/100071289763275', 1, 1, '2019-10-05 21:16:37', '2024-03-19 11:02:55'),
(13, 'General', 'twitter', 'https://www.twitter.com/pemkotserang', 1, 1, '2019-10-05 21:16:57', '2022-01-26 09:43:00'),
(14, 'General', 'youtube', 'https://www.youtube.com/pemkotserang', 1, 1, '2019-10-05 21:17:12', '2022-01-26 20:52:30'),
(15, 'Image', 'favicon', 'kotser.png', 1, 1, '2019-10-05 21:17:38', '2024-04-16 09:57:57'),
(16, 'Image', 'logo', 'kotser.png', 1, 1, '2019-10-05 21:20:27', '2024-04-16 09:58:03'),
(17, 'Image', 'medium_size', '640x480', 1, 1, '2019-10-05 21:23:00', '2019-10-05 21:23:00'),
(18, 'Config', 'maintenance_mode', 'N', 1, 1, '2019-10-05 21:33:27', '2019-10-05 21:34:46'),
(19, 'Config', 'member_registration', 'N', 1, 1, '2019-10-05 21:33:54', '2019-10-05 21:34:53'),
(20, 'Config', 'comment', 'N', 1, 1, '2019-10-05 21:34:21', '2023-01-11 08:46:04'),
(21, 'Config', 'item_per_page', '6', 1, 1, '2019-10-05 21:34:40', '2023-01-11 08:46:19'),
(22, 'Config', 'google_analytics_id', '425568651', 1, 1, '2019-10-05 21:35:45', '2024-01-30 11:40:17'),
(23, 'Config', 'recaptcha_key', '6LdZeTsmAAAAADMcDm4LKpAfflS-Y7QcYo-4kchV', 1, 1, '2019-10-05 21:36:15', '2024-01-29 03:52:18'),
(24, 'Config', 'recaptcha_secret', '6LdZeTsmAAAAAKNrGadVPepLxMNgVTFRD97AiCAg', 1, 1, '2019-10-05 21:36:40', '2024-01-29 03:52:55'),
(25, 'Mail', 'mail_protocol', 'SMTP', 1, 1, '2019-10-05 21:37:27', '2019-10-05 21:37:27'),
(26, 'Mail', 'mail_hostname', 'mail.serangkota.go.id', 1, 1, '2019-10-05 21:37:51', '2022-01-27 22:27:12'),
(27, 'Mail', 'mail_username', 'cmsdev@serangkota.go.id', 1, 1, '2019-10-05 21:39:13', '2023-06-04 13:44:21'),
(28, 'Mail', 'mail_password', '-', 1, 1, '2019-10-05 21:39:33', '2024-03-28 08:12:05'),
(29, 'Mail', 'mail_port', '587', 1, 1, '2019-10-05 21:39:51', '2022-11-16 02:26:11'),
(30, 'Other', 'sitemap', 'sitemap.xml', 1, 1, '2019-10-15 20:01:21', '2019-10-15 20:01:21'),
(31, 'Other', 'sitemap_priority', '0.8', 1, 1, '2019-10-15 20:08:49', '2019-10-16 19:18:58'),
(32, 'Other', 'sitemap_frequency', 'monthly', 1, 1, '2019-10-16 19:25:16', '2019-10-16 19:25:16'),
(33, 'Other', 'backup', 'backup', 1, 1, '2019-10-16 19:32:50', '2019-10-16 19:32:50'),
(34, 'Image', 'logo_footer', 'kotser.png', 1, 1, '2019-10-31 15:26:25', '2024-04-16 09:58:10'),
(35, 'Config', 'slug', 'detailpost/slug', 1, 1, '2019-11-13 14:32:55', '2019-11-13 15:09:19'),
(36, 'Image', 'loader1', 'light-without-letter.svg', 1, 1, NULL, NULL),
(37, 'Image', 'loader2', 'letter.svg', 1, 1, NULL, NULL),
(39, 'General', 'instagram', 'https://www.instagram.com/', 1, 1, '2024-03-18 07:16:41', '2024-03-18 07:16:41'),
(40, 'Image', 'maps', 'maps.svg', 1, 1, '2024-03-18 10:57:46', '2024-03-18 10:57:46'),
(41, 'General', 'map_wilayah', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31737.19075158429!2d106.15901994999999!3d-6.11065355!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e418b0dbb534a61%3A0x301e8f1fc28b8d0!2sSerang%2C%20Kec.%20Serang%2C%20Kota%20Serang%2C%20Banten!5e0!3m2!1sid!2sid!4v1715945779460!5m2!1sid!2sid\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 1, 1, '2024-03-28 08:04:01', '2024-04-16 10:00:58');

-- --------------------------------------------------------

--
-- Table structure for table `shetabit_visits`
--

CREATE TABLE `shetabit_visits` (
  `id` bigint UNSIGNED NOT NULL,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `referer` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `languages` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `useragent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `headers` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `device` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `platform` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `browser` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitable_id` bigint UNSIGNED DEFAULT NULL,
  `visitor_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitor_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribes`
--

CREATE TABLE `subscribes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `follow` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscribes`
--

INSERT INTO `subscribes` (`id`, `name`, `email`, `follow`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'gagaltotal666', 'gatotteguhramadhan666@gmail.com', 'Y', 1, 1, '2021-10-14 22:15:33', '2021-10-14 22:15:56');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seotitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count` bigint NOT NULL DEFAULT '1',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE `themes` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `themes`
--

INSERT INTO `themes` (`id`, `title`, `author`, `folder`, `active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Serang Kota', 'Diskominfo Kota Serang', 'spaces', 'Y', 1, 1, '2021-10-30 20:55:25', '2021-10-30 20:55:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `block` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Y',
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint NOT NULL DEFAULT '1',
  `updated_by` bigint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `telp`, `bio`, `block`, `picture`, `remember_token`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'cmsserangkota', 'cmsdev@serangkota.go.id', NULL, '$2y$10$iGEef.CgOZ/zavClnWBdteUneWa9Sf1du8ctoCbnI.hkbuIstEVRC', '08987881597', 'Admin CMS kota serang', 'N', 'userlogo.png', NULL, 1, 1, '2019-09-22 00:56:35', '2024-05-17 11:39:47'),
(6, 'gatot teguh ramadhan', 'gatotteguhramadhan45', 'gatot.teguhramadhan@gmail.com', NULL, '$2y$10$aGkf3f2prIHALMAH5Yo6wey6OHaBdhNV3zads.OxWTfjA4UYVvdmO', '0878787878787', 'mandor cms madani', 'N', 'userlogo.png', NULL, 1, 1, '2023-05-03 23:23:22', '2024-03-29 12:02:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `albums_index` (`seotitle`(191),`title`(191)) USING BTREE;

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `categories_index` (`seotitle`(191),`title`(191)) USING BTREE;

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id_index` (`post_id`);

--
-- Indexes for table `components`
--
ALTER TABLE `components`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallerys`
--
ALTER TABLE `gallerys`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `album_id_index` (`album_id`) USING BTREE;

--
-- Indexes for table `infografis`
--
ALTER TABLE `infografis`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `pages_index` (`seotitle`(191),`title`(191)) USING BTREE;

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `email` (`email`(191));

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `posts_index` (`seotitle`(191),`title`(191)) USING BTREE;

--
-- Indexes for table `post_gallerys`
--
ALTER TABLE `post_gallerys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id_index` (`post_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD KEY `role_has_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `runningtext`
--
ALTER TABLE `runningtext`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shetabit_visits`
--
ALTER TABLE `shetabit_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shetabit_visits_visitable_type_visitable_id_index` (`visitable_type`,`visitable_id`),
  ADD KEY `shetabit_visits_visitor_type_visitor_id_index` (`visitor_type`,`visitor_id`);

--
-- Indexes for table `subscribes`
--
ALTER TABLE `subscribes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `tags_index` (`seotitle`(191),`title`(191)) USING BTREE;

--
-- Indexes for table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `components`
--
ALTER TABLE `components`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallerys`
--
ALTER TABLE `gallerys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `infografis`
--
ALTER TABLE `infografis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_gallerys`
--
ALTER TABLE `post_gallerys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `runningtext`
--
ALTER TABLE `runningtext`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `shetabit_visits`
--
ALTER TABLE `shetabit_visits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribes`
--
ALTER TABLE `subscribes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `themes`
--
ALTER TABLE `themes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
