-- MySQL dump 10.13  Distrib 8.4.7, for Win64 (x86_64)
--
-- Host: localhost    Database: towncore
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_category_post`
--

DROP TABLE IF EXISTS `blog_category_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_category_post` (
  `blog_category_id` bigint unsigned NOT NULL,
  `post_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`blog_category_id`,`post_id`),
  KEY `blog_category_post_post_id_foreign` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_category_post`
--

LOCK TABLES `blog_category_post` WRITE;
/*!40000 ALTER TABLE `blog_category_post` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_category_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_comments`
--

DROP TABLE IF EXISTS `blog_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `author_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_comments_approved_by_foreign` (`approved_by`),
  KEY `blog_comments_post_id_status_created_at_index` (`post_id`,`status`,`created_at`),
  KEY `blog_comments_parent_id_status_index` (`parent_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_comments`
--

LOCK TABLES `blog_comments` WRITE;
/*!40000 ALTER TABLE `blog_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_tag_post`
--

DROP TABLE IF EXISTS `blog_tag_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_tag_post` (
  `blog_tag_id` bigint unsigned NOT NULL,
  `post_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`blog_tag_id`,`post_id`),
  KEY `blog_tag_post_post_id_foreign` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_tag_post`
--

LOCK TABLES `blog_tag_post` WRITE;
/*!40000 ALTER TABLE `blog_tag_post` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_tag_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_tags`
--

DROP TABLE IF EXISTS `blog_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_tags_slug_unique` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_tags`
--

LOCK TABLES `blog_tags` WRITE;
/*!40000 ALTER TABLE `blog_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_project`
--

DROP TABLE IF EXISTS `category_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_project` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_project_project_id_category_id_unique` (`project_id`,`category_id`),
  KEY `category_project_category_id_foreign` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_project`
--

LOCK TABLES `category_project` WRITE;
/*!40000 ALTER TABLE `category_project` DISABLE KEYS */;
INSERT INTO `category_project` VALUES (1,2,6);
/*!40000 ALTER TABLE `category_project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_checklist_items`
--

DROP TABLE IF EXISTS `client_checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_checklist_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_checklist_items_client_id_foreign` (`client_id`),
  KEY `client_checklist_items_created_by_foreign` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_checklist_items`
--

LOCK TABLES `client_checklist_items` WRITE;
/*!40000 ALTER TABLE `client_checklist_items` DISABLE KEYS */;
INSERT INTO `client_checklist_items` VALUES (1,7,4,'DOMAIN REG','in_progress',1,'2026-04-15 22:54:31','2026-04-15 22:54:31'),(2,7,4,'websetup','pending',2,'2026-04-15 23:12:59','2026-04-15 23:12:59');
/*!40000 ALTER TABLE `client_checklist_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_files`
--

DROP TABLE IF EXISTS `client_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `folder_id` bigint unsigned DEFAULT NULL,
  `uploaded_by` bigint unsigned NOT NULL,
  `original_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL DEFAULT '0',
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_files_user_id_foreign` (`user_id`),
  KEY `client_files_uploaded_by_foreign` (`uploaded_by`),
  KEY `client_files_folder_id_foreign` (`folder_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_files`
--

LOCK TABLES `client_files` WRITE;
/*!40000 ALTER TABLE `client_files` DISABLE KEYS */;
INSERT INTO `client_files` VALUES (2,5,1,5,'1_11zon.jpg','client-5/zujldX03sStJt5BRUxSnUSSI4QOtcpKoYKFWmLyI.jpg','image/jpeg',95992,NULL,'2026-04-15 00:22:19','2026-04-15 00:22:19'),(3,5,1,5,'eula.1031.txt','client-5/NpUYF36nPcNez4zWNYbiAuazKY2A7xJ9P5wES4xc.txt','text/plain',17734,NULL,'2026-04-15 02:48:29','2026-04-15 02:48:29');
/*!40000 ALTER TABLE `client_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_folders`
--

DROP TABLE IF EXISTS `client_folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_folders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_folders_user_id_foreign` (`user_id`),
  KEY `client_folders_parent_id_foreign` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_folders`
--

LOCK TABLES `client_folders` WRITE;
/*!40000 ALTER TABLE `client_folders` DISABLE KEYS */;
INSERT INTO `client_folders` VALUES (1,5,NULL,'scop kariah','2026-04-15 00:22:04','2026-04-15 00:22:04'),(2,5,NULL,'design','2026-04-15 20:47:47','2026-04-15 20:47:47'),(3,5,NULL,'IMAGES','2026-04-15 20:48:12','2026-04-15 20:48:12');
/*!40000 ALTER TABLE `client_folders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversation_participants`
--

DROP TABLE IF EXISTS `conversation_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversation_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversation_participants_conversation_id_user_id_unique` (`conversation_id`,`user_id`),
  KEY `conversation_participants_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversation_participants`
--

LOCK TABLES `conversation_participants` WRITE;
/*!40000 ALTER TABLE `conversation_participants` DISABLE KEYS */;
INSERT INTO `conversation_participants` VALUES (1,1,1,'2026-04-02 17:28:00','2026-04-01 14:58:48','2026-04-02 17:28:00'),(2,1,3,'2026-04-05 14:08:36','2026-04-01 14:58:48','2026-04-05 14:08:36'),(3,1,2,NULL,'2026-04-01 14:58:48','2026-04-01 14:58:48'),(4,2,1,'2026-04-04 21:23:12','2026-04-01 15:19:23','2026-04-04 21:23:12'),(5,2,2,'2026-04-01 16:02:38','2026-04-01 15:19:23','2026-04-01 16:02:38'),(6,3,3,'2026-04-05 14:08:23','2026-04-04 21:21:38','2026-04-05 14:08:23'),(7,3,2,NULL,'2026-04-04 21:21:38','2026-04-04 21:21:38'),(8,3,1,'2026-04-06 18:58:07','2026-04-04 21:21:38','2026-04-06 18:58:07'),(9,4,5,'2026-04-15 21:34:44','2026-04-15 21:31:07','2026-04-15 21:34:44'),(10,4,4,'2026-04-15 22:07:31','2026-04-15 21:31:07','2026-04-15 22:07:31'),(11,5,8,'2026-04-15 17:23:12','2026-04-15 17:06:04','2026-04-15 17:23:12'),(12,5,6,NULL,'2026-04-15 17:06:04','2026-04-15 17:06:04'),(13,3,8,NULL,'2026-04-15 17:31:16','2026-04-15 17:31:16'),(14,3,4,NULL,'2026-04-15 17:31:16','2026-04-15 17:31:16'),(15,1,4,NULL,'2026-04-15 17:33:05','2026-04-15 17:33:05');
/*!40000 ALTER TABLE `conversation_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('project','direct') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'direct',
  `project_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_project_id_foreign` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
INSERT INTO `conversations` VALUES (1,'project',1,'2026-04-01 14:58:48','2026-04-01 14:58:48'),(2,'direct',NULL,'2026-04-01 15:19:23','2026-04-01 15:20:21'),(3,'project',2,'2026-04-04 21:21:38','2026-04-05 12:29:28'),(4,'direct',NULL,'2026-04-15 21:31:07','2026-04-15 21:31:11'),(5,'direct',NULL,'2026-04-15 17:06:04','2026-04-15 17:06:09');
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faq_items`
--

DROP TABLE IF EXISTS `faq_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faq_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `categories` json DEFAULT NULL,
  `question` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faq_items_is_active_category_sort_order_index` (`is_active`,`category`,`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faq_items`
--

LOCK TABLES `faq_items` WRITE;
/*!40000 ALTER TABLE `faq_items` DISABLE KEYS */;
INSERT INTO `faq_items` VALUES (1,'Services','[\"Services\"]','What services do you offer?','We provide website development, custom software, mobile apps, cloud operations, SEO, media production, and digital growth support.',1,1,'2026-04-16 05:11:30','2026-04-16 05:11:30'),(2,'Services','[\"Services\"]','Can you customize an existing system?','Yes. We can extend, modernize, or re-architect existing systems while keeping data continuity and business operations intact.',2,1,'2026-04-16 05:11:30','2026-04-16 05:11:30'),(3,'Process','[\"Process\"]','How long does delivery take?','Small projects may take a few weeks, while larger systems are delivered in phases. Timeline is agreed after scope and workflow mapping.',1,1,'2026-04-16 05:11:30','2026-04-16 05:11:30'),(4,'Process','[\"Process\"]','Do you offer post-launch support?','Yes. We provide maintenance plans, release support, monitoring, and optimization after launch.',2,1,'2026-04-16 05:11:30','2026-04-16 05:11:30'),(5,'Cloud','[\"Cloud\"]','Do you provide hosting and backups?','Yes. We host websites and software platforms with backup, security hardening, and uptime monitoring workflows.',1,1,'2026-04-16 05:11:30','2026-04-16 05:11:30'),(6,'Commerce','[\"Commerce\"]','How does software purchase work?','Choose a product in Shop, select a payment method, submit your request, and our team confirms onboarding and delivery steps.',1,1,'2026-04-16 05:11:30','2026-04-16 05:11:30');
/*!40000 ALTER TABLE `faq_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `freelancer_invoices`
--

DROP TABLE IF EXISTS `freelancer_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `freelancer_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `freelancer_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `due_date` date DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `freelancer_invoices_invoice_number_unique` (`invoice_number`),
  KEY `freelancer_invoices_freelancer_id_foreign` (`freelancer_id`),
  KEY `freelancer_invoices_project_id_foreign` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `freelancer_invoices`
--

LOCK TABLES `freelancer_invoices` WRITE;
/*!40000 ALTER TABLE `freelancer_invoices` DISABLE KEYS */;
INSERT INTO `freelancer_invoices` VALUES (1,2,3,'',0.00,NULL,NULL,'pending',NULL,'2026-04-05 12:34:31','2026-04-05 12:34:31'),(2,8,2,'INV-2026-00002',2000.00,'fgfgfrfgfg','2026-04-16','approved',NULL,'2026-04-15 17:18:08','2026-04-15 17:22:57');
/*!40000 ALTER TABLE `freelancer_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `freelancer_payment_logs`
--

DROP TABLE IF EXISTS `freelancer_payment_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `freelancer_payment_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `freelancer_payment_id` bigint unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `freelancer_payment_logs_freelancer_payment_id_foreign` (`freelancer_payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `freelancer_payment_logs`
--

LOCK TABLES `freelancer_payment_logs` WRITE;
/*!40000 ALTER TABLE `freelancer_payment_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `freelancer_payment_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `freelancer_payments`
--

DROP TABLE IF EXISTS `freelancer_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `freelancer_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `freelancer_id` bigint unsigned NOT NULL,
  `agreed_amount` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `freelancer_payments_project_id_foreign` (`project_id`),
  KEY `freelancer_payments_freelancer_id_foreign` (`freelancer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `freelancer_payments`
--

LOCK TABLES `freelancer_payments` WRITE;
/*!40000 ALTER TABLE `freelancer_payments` DISABLE KEYS */;
INSERT INTO `freelancer_payments` VALUES (1,1,2,150000.00,0.00,'unpaid','2026-04-01 13:48:23','2026-04-01 13:48:35'),(2,2,8,20000.00,0.00,'unpaid','2026-04-15 17:36:02','2026-04-15 17:36:02');
/*!40000 ALTER TABLE `freelancer_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TZS',
  `exchange_rate` decimal(14,4) DEFAULT NULL,
  `converted_amount` decimal(14,2) DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_project_id_foreign` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,1,3000.00,1500.00,'USD',2500.0000,7500000.00,'partial','2026-04-22 21:00:00','2026-04-01 10:34:58','2026-04-01 13:33:06'),(2,2,1800.00,1800.00,'USD',2500.0000,4500000.00,'paid','2026-04-09 21:00:00','2026-04-01 14:41:57','2026-04-01 21:11:52');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('new','contacted','converted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `converted_user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leads_converted_user_id_foreign` (`converted_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_chat_messages`
--

DROP TABLE IF EXISTS `live_chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_chat_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `sender_type` enum('visitor','agent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent_id` bigint unsigned DEFAULT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `live_chat_messages_agent_id_foreign` (`agent_id`),
  KEY `live_chat_messages_session_id_index` (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_chat_messages`
--

LOCK TABLES `live_chat_messages` WRITE;
/*!40000 ALTER TABLE `live_chat_messages` DISABLE KEYS */;
INSERT INTO `live_chat_messages` VALUES (1,1,'visitor',NULL,'hello','2026-04-07 18:16:26','2026-04-07 18:16:26'),(2,1,'visitor',NULL,'hello','2026-04-07 21:29:39','2026-04-07 21:29:39');
/*!40000 ALTER TABLE `live_chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_chat_sessions`
--

DROP TABLE IF EXISTS `live_chat_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_chat_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visitor_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitor_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_id` bigint unsigned DEFAULT NULL,
  `status` enum('waiting','active','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `live_chat_sessions_session_key_unique` (`session_key`),
  KEY `live_chat_sessions_agent_id_foreign` (`agent_id`),
  KEY `live_chat_sessions_status_index` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_chat_sessions`
--

LOCK TABLES `live_chat_sessions` WRITE;
/*!40000 ALTER TABLE `live_chat_sessions` DISABLE KEYS */;
INSERT INTO `live_chat_sessions` VALUES (1,'eBd8iafirGSvxg1hkNo22XED8I0Fbt1mojUn9F5bO4frVmQd','scp[','Roger@SafarisWithAHeart.com',NULL,'waiting',NULL,'2026-04-07 18:14:09','2026-04-07 18:14:09');
/*!40000 ALTER TABLE `live_chat_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` enum('image','video','document') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint unsigned NOT NULL,
  `uploaded_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_uploaded_by_foreign` (`uploaded_by`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (1,'SBT_172_original.webp','media/598QmpqJlLfDl6csUEv950pWPZfrgMczTkMDPgYg.webp','image',186468,1,'2026-04-01 23:45:07','2026-04-01 23:45:07'),(5,'Artboard 6 copy.png','media/MetATvqkPH5y13wAkGDn3Xwcj3OxKDvIu9kkbjmE.png','image',29781,4,'2026-04-16 03:51:49','2026-04-16 03:51:49'),(6,'Artboard 6.png','media/J4k9jhie3U2S4GAr7w2wtjU6CSZ6gA0kjVFNOsEY.png','image',31402,4,'2026-04-16 03:51:49','2026-04-16 03:51:49');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `sender_id` bigint unsigned NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `message_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_conversation_id_foreign` (`conversation_id`),
  KEY `messages_project_id_foreign` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,1,1,3,'Test from tinker','text',NULL,NULL,NULL,'2026-04-01 09:36:53','2026-04-01 09:36:53'),(2,1,1,2,'Hello from freelancer','text',NULL,NULL,NULL,'2026-04-01 09:38:04','2026-04-01 09:38:04'),(3,1,1,3,'YOH','text',NULL,NULL,NULL,'2026-04-01 09:54:49','2026-04-01 09:54:49'),(4,1,1,2,'I SEE','text',NULL,NULL,NULL,'2026-04-01 09:55:24','2026-04-01 09:55:24'),(5,1,1,2,NULL,'text','messages/1/ecT3sAcQQLsiXFkuAwJUP6a07cJqYH3bhEGFZKYJ.jpg',NULL,NULL,'2026-04-01 09:55:33','2026-04-01 09:55:33'),(6,1,1,1,'I SEE','text',NULL,NULL,NULL,'2026-04-01 09:55:59','2026-04-01 09:55:59'),(7,1,1,3,NULL,'audio','messages/1/7cmW8i5NXKqRsdncduCMnIAiTgMIxMIJgk9yEtdM.webm',NULL,NULL,'2026-04-01 10:16:25','2026-04-01 10:16:25'),(8,1,1,2,'😂😂😂','text',NULL,NULL,NULL,'2026-04-01 10:16:57','2026-04-01 10:16:57'),(9,1,1,2,'Shared location','location',NULL,-3.3339120,36.6524050,'2026-04-01 10:17:34','2026-04-01 10:17:34'),(10,1,1,3,'👆','text',NULL,NULL,NULL,'2026-04-01 11:18:40','2026-04-01 11:18:40'),(11,1,1,3,NULL,'audio','messages/1/T8GD5yfoOF5dHBehdAnBgoUdWOWbFQW1McAQrvd9.webm',NULL,NULL,'2026-04-01 11:18:54','2026-04-01 11:18:54'),(12,1,1,2,NULL,'image','messages/1/uEsfspXlC9ZHOKl1p6eueu8Nadxc3x5KY3Bn7A3g.jpg',NULL,NULL,'2026-04-01 11:23:11','2026-04-01 11:23:11'),(13,2,NULL,1,'yoh','text',NULL,NULL,NULL,'2026-04-01 15:19:32','2026-04-01 15:19:32'),(14,2,NULL,2,'hello boss','text',NULL,NULL,NULL,'2026-04-01 15:20:21','2026-04-01 15:20:21'),(15,3,2,1,'hello','text',NULL,NULL,NULL,'2026-04-04 21:21:51','2026-04-04 21:21:51'),(16,3,2,3,'helllo','text',NULL,NULL,NULL,'2026-04-05 12:29:28','2026-04-05 12:29:28'),(17,4,NULL,5,'PU','text',NULL,NULL,NULL,'2026-04-15 21:31:11','2026-04-15 21:31:11'),(18,5,NULL,8,'yoh','text',NULL,NULL,NULL,'2026-04-15 17:06:09','2026-04-15 17:06:09');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_03_29_083604_create_projects_table',2),(6,'2026_03_29_083605_create_project_files_table',2),(7,'2026_04_01_000000_create_messages_table',3),(8,'2026_04_01_100000_add_type_and_location_to_messages_table',4),(9,'2026_04_01_200000_create_invoices_table',5),(10,'2026_04_01_210000_add_currency_fields_to_invoices_table',6),(11,'2026_04_01_220000_create_settings_table',7),(12,'2026_04_01_230000_create_project_categories_table',8),(13,'2026_04_02_000000_upgrade_invoices_for_installments',9),(14,'2026_04_02_000001_create_payments_table',9),(15,'2026_04_02_100000_create_freelancer_payments_table',10),(16,'2026_04_02_110000_create_freelancer_invoices_table',11),(17,'2026_04_02_120000_create_notifications_table',12),(18,'2026_04_02_130000_add_rejection_note_to_freelancer_invoices_table',13),(19,'2026_04_02_200000_create_conversations_table',14),(20,'2026_04_02_200001_create_conversation_participants_table',14),(21,'2026_04_02_200002_add_conversation_id_to_messages_table',14),(22,'2026_04_02_200003_backfill_project_conversations',14),(23,'2026_04_01_183440_create_portfolios_table',15),(24,'2026_04_02_200000_add_subcategory_fields_to_project_categories_table',16),(25,'2026_04_02_210000_create_category_project_pivot_table',17),(26,'2026_04_02_220000_add_seo_fields_to_project_categories_table',18),(27,'2026_04_02_230000_create_pages_table',19),(28,'2026_04_02_240000_add_pricing_to_project_categories_table',20),(29,'2026_04_02_250000_add_theme_colors_to_settings_table',21),(30,'2026_04_02_260000_create_contact_messages_table',22),(31,'2026_04_02_270000_create_posts_table',23),(32,'2026_04_02_280000_create_subscribers_table',24),(33,'2026_04_02_290000_create_media_table',25),(34,'2026_04_02_290000_add_logo_media_id_to_settings_table',26),(35,'2026_04_03_000000_create_leads_table',27),(36,'2026_04_05_100000_create_page_sections_table',28),(37,'2026_04_07_201004_create_live_chat_tables',29),(38,'2026_04_08_001504_add_push_token_to_users_table',30),(39,'2026_04_14_100000_create_subscription_plans_table',31),(40,'2026_04_14_100001_create_subscriptions_table',31),(41,'2026_04_14_100002_create_client_files_table',31),(42,'2026_04_14_200000_create_client_folders_table',32),(43,'2026_04_14_200001_add_folder_id_to_client_files_table',32),(44,'2026_04_14_194601_create_subscription_requests_table',33),(45,'2026_04_15_120000_add_account_state_and_profile_fields_to_users_table',34),(46,'2026_04_15_120100_create_client_checklist_items_table',34),(47,'2026_04_15_120200_add_light_and_dark_logo_media_to_settings_table',34),(48,'2026_04_15_190000_add_username_phone_and_trial_fields_to_users_table',35),(49,'2026_04_15_190100_add_payment_methods_to_settings_table',35),(50,'2026_04_15_190200_add_payment_fields_to_subscription_requests_table',35),(51,'2026_04_15_190300_update_freelancer_invoices_table',36),(52,'2026_04_16_010000_add_professional_fields_to_portfolios_table',37),(53,'2026_04_16_020000_add_product_fields_to_portfolios_table',38),(54,'2026_04_16_030000_create_software_purchase_requests_table',39),(55,'2026_04_16_030100_create_faq_items_table',39),(56,'2026_04_16_030200_add_gallery_images_to_project_categories_table',39),(57,'2026_04_16_120000_add_categories_to_faq_items_table',40),(58,'2026_04_16_120100_add_body_images_to_posts_table',40),(59,'2026_04_16_120200_create_blog_comments_table',40),(60,'2026_04_16_130000_add_hero_fields_to_settings_table',41),(61,'2026_04_16_130100_add_shop_detail_fields_to_portfolios_table',41),(62,'2026_04_16_210000_create_blog_categories_and_tags_tables',41);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('0abd7b63-0f3f-4a4c-9e1a-62f892092537','App\\Notifications\\UserAlertNotification','App\\Models\\User',3,'{\"title\":\"Invoice created\",\"message\":\"A new invoice was created for project \\\"App development\\\".\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/invoices\\/2\",\"project_id\":2,\"invoice_id\":2,\"note\":null}','2026-04-01 14:42:38','2026-04-01 14:41:57','2026-04-01 14:42:38'),('7d9a842a-1697-4016-9605-5aaa49d11161','App\\Notifications\\UserAlertNotification','App\\Models\\User',2,'{\"title\":\"New message\",\"message\":\"Towncore Admin sent a message.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/freelancer\\/messages?conversation=2\",\"project_id\":null,\"invoice_id\":null,\"note\":null}','2026-04-01 15:20:10','2026-04-01 15:19:32','2026-04-01 15:20:10'),('5ccef272-1fd0-4698-a0f9-d338ae9b795f','App\\Notifications\\UserAlertNotification','App\\Models\\User',1,'{\"title\":\"New message\",\"message\":\"Scop Kariah sent a message.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/admin\\/messages?conversation=2\",\"project_id\":null,\"invoice_id\":null,\"note\":null}','2026-04-01 15:26:10','2026-04-01 15:20:21','2026-04-01 15:26:10'),('7d14a0ea-e06c-4288-a199-a67ce28e6c32','App\\Notifications\\UserAlertNotification','App\\Models\\User',3,'{\"title\":\"Invoice updated\",\"message\":\"Invoice INV-0002 was updated for project \\\"App development\\\".\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/invoices\\/2\",\"project_id\":2,\"invoice_id\":2,\"note\":null}','2026-04-01 22:09:46','2026-04-01 21:11:52','2026-04-01 22:09:46'),('6ec6e90d-f696-4f31-adcc-de61c267b86e','App\\Notifications\\UserAlertNotification','App\\Models\\User',2,'{\"title\":\"New project assigned\",\"message\":\"You have been assigned to project \\\"App development\\\". Head over to start reviewing the details.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/projects\\/2\",\"project_id\":2,\"invoice_id\":null,\"note\":null}',NULL,'2026-04-04 20:52:02','2026-04-04 20:52:02'),('67500d33-6274-4cb9-8443-cd36776e0fef','App\\Notifications\\UserAlertNotification','App\\Models\\User',2,'{\"title\":\"New project assigned\",\"message\":\"You have been assigned to project \\\"App development\\\". Head over to start reviewing the details.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/projects\\/2\",\"project_id\":2,\"invoice_id\":null,\"note\":null}',NULL,'2026-04-04 21:21:24','2026-04-04 21:21:24'),('554bbdd3-aeec-4274-a84c-2674b0cd2459','App\\Notifications\\UserAlertNotification','App\\Models\\User',2,'{\"title\":\"New message\",\"message\":\"Towncore Admin sent a message.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/freelancer\\/messages?conversation=3\",\"project_id\":2,\"invoice_id\":null,\"note\":null}',NULL,'2026-04-04 21:21:51','2026-04-04 21:21:51'),('3525dc4b-f12f-444f-955b-3969b9d21e69','App\\Notifications\\UserAlertNotification','App\\Models\\User',3,'{\"title\":\"New message\",\"message\":\"Towncore Admin sent a message.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/client\\/messages?conversation=3\",\"project_id\":2,\"invoice_id\":null,\"note\":null}','2026-04-05 12:29:41','2026-04-04 21:21:52','2026-04-05 12:29:41'),('8d9587a1-9fe4-405b-b3b4-9e31ae57f3b0','App\\Notifications\\UserAlertNotification','App\\Models\\User',1,'{\"title\":\"New message\",\"message\":\"Scop 2 sent a message.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/admin\\/messages?conversation=3\",\"project_id\":2,\"invoice_id\":null,\"note\":null}','2026-04-06 18:58:03','2026-04-05 12:29:28','2026-04-06 18:58:03'),('9a9f59f8-15fa-46cc-8e1d-00ae761124b0','App\\Notifications\\UserAlertNotification','App\\Models\\User',2,'{\"title\":\"New message\",\"message\":\"Scop 2 sent a message.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/freelancer\\/messages?conversation=3\",\"project_id\":2,\"invoice_id\":null,\"note\":null}',NULL,'2026-04-05 12:29:30','2026-04-05 12:29:30'),('26a6482a-9c9e-4557-8244-3b0a1b68a86f','App\\Notifications\\UserAlertNotification','App\\Models\\User',1,'{\"title\":\"New project submitted\",\"message\":\"Scop 2 submitted a new project: \\\"ttt\\\". Please review and assign a freelancer.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/admin\\/projects\\/3\",\"project_id\":3,\"invoice_id\":null,\"note\":null}','2026-04-05 13:15:28','2026-04-05 12:31:28','2026-04-05 13:15:28'),('4fb04255-3adc-4aff-9ac5-96336453bc7f','App\\Notifications\\UserAlertNotification','App\\Models\\User',2,'{\"title\":\"New project assigned\",\"message\":\"You have been assigned to project \\\"ttt\\\". Head over to start reviewing the details.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/projects\\/3\",\"project_id\":3,\"invoice_id\":null,\"note\":null}',NULL,'2026-04-05 12:33:11','2026-04-05 12:33:11'),('e3e924b4-3380-4824-b13f-2933452969fd','App\\Notifications\\UserAlertNotification','App\\Models\\User',2,'{\"title\":\"New project assigned\",\"message\":\"You have been assigned to project \\\"ttt\\\". Head over to start reviewing the details.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/projects\\/3\",\"project_id\":3,\"invoice_id\":null,\"note\":null}',NULL,'2026-04-06 18:57:15','2026-04-06 18:57:15'),('9607f056-0765-4067-ab28-f1909d3c18f1','App\\Notifications\\UserAlertNotification','App\\Models\\User',4,'{\"title\":\"New message\",\"message\":\"Demo Client sent a message.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/admin\\/messages?conversation=4\",\"project_id\":null,\"invoice_id\":null,\"note\":null}','2026-04-15 22:05:23','2026-04-15 21:31:14','2026-04-15 22:05:23'),('689cd2a6-e037-494a-94cd-b7049303cf90','App\\Notifications\\NewUserCredentialsNotification','App\\Models\\User',7,'{\"title\":\"Account created\",\"message\":\"Your login credentials have been issued. You must change your password on first login.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/login\"}','2026-04-15 23:14:43','2026-04-15 22:54:02','2026-04-15 23:14:43'),('1b6214ca-e06b-4462-bc81-cde847234d3c','App\\Notifications\\UserAlertNotification','App\\Models\\User',6,'{\"title\":\"New message\",\"message\":\"john sent a message.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/public\\/freelancer\\/messages?conversation=5\",\"project_id\":null,\"invoice_id\":null,\"note\":null}',NULL,'2026-04-15 17:06:11','2026-04-15 17:06:11'),('eb5c0a09-2ff3-4adf-acdd-68c59c11fa0b','App\\Notifications\\UserAlertNotification','App\\Models\\User',8,'{\"title\":\"New project assigned\",\"message\":\"You have been assigned to project \\\"App development\\\". Head over to start reviewing the details.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/projects\\/2\",\"project_id\":2,\"invoice_id\":null,\"note\":null}','2026-04-15 17:21:22','2026-04-15 17:17:14','2026-04-15 17:21:22'),('8898d632-179f-48a7-beb1-5249b64f1e5b','App\\Notifications\\UserAlertNotification','App\\Models\\User',6,'{\"title\":\"New project assigned\",\"message\":\"You have been assigned to project \\\"ttt\\\". Head over to start reviewing the details.\",\"action_url\":\"http:\\/\\/localhost\\/towncore\\/projects\\/3\",\"project_id\":3,\"invoice_id\":null,\"note\":null}',NULL,'2026-04-15 17:18:53','2026-04-15 17:18:53');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_sections`
--

DROP TABLE IF EXISTS `page_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `page_id` bigint unsigned NOT NULL,
  `type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_index` smallint unsigned NOT NULL DEFAULT '0',
  `data` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_sections_page_id_foreign` (`page_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_sections`
--

LOCK TABLES `page_sections` WRITE;
/*!40000 ALTER TABLE `page_sections` DISABLE KEYS */;
INSERT INTO `page_sections` VALUES (1,2,'hero','Hero Section',0,'{\"title\": \"We Build Digital Futures\", \"subtitle\": \"Towncore connects ambitious businesses with elite freelance talent to deliver world-class digital projects.\"}',1,'2026-04-04 20:16:31','2026-04-04 20:16:31'),(2,2,'story','Our Story',1,'{\"content\": \"<h2>How It All Started</h2><p>Towncore was born from a simple observation: great talent exists everywhere, but finding it and working with it effectively is still incredibly hard. We set out to change that.</p><p>Since our founding, we have connected hundreds of businesses with skilled freelancers, delivering projects across web development, design, marketing, and beyond. Every project we touch is treated with the same care and craftsmanship we would apply to our own work.</p><p>We believe in transparency, quality, and long-term partnerships — not one-off transactions. When you work with Towncore, you are not just hiring a freelancer; you are gaining a dedicated team that is invested in your success.</p>\"}',1,'2026-04-04 20:16:31','2026-04-04 20:16:31'),(3,2,'timeline','Our Journey',2,'{\"items\": [{\"year\": \"2020\", \"label\": \"Founded\", \"description\": \"Towncore launched with a mission to democratize access to top freelance talent.\"}, {\"year\": \"2021\", \"label\": \"First 100 Clients\", \"description\": \"Reached our first hundred active clients across three continents.\"}, {\"year\": \"2022\", \"label\": \"Platform Upgrade\", \"description\": \"Rebuilt the platform from the ground up with real-time project tracking.\"}, {\"year\": \"2023\", \"label\": \"Community Launch\", \"description\": \"Launched WorkMyWork — a dedicated hub for our freelance community.\"}, {\"year\": \"2024\", \"label\": \"500+ Projects\", \"description\": \"Delivered over 500 successful projects worth $2M+ in total client value.\"}], \"heading\": \"Our Journey\"}',1,'2026-04-04 20:16:31','2026-04-04 20:16:31'),(4,2,'services','Our Services',3,'{\"intro\": \"From concept to launch, we cover every digital discipline your business needs to thrive online.\", \"heading\": \"What We Do\"}',1,'2026-04-04 20:16:31','2026-04-04 20:16:31'),(5,2,'vision','Our Vision',4,'{\"content\": \"We envision a future where geography is irrelevant, and where the best person for any job can always be found, trusted, and rewarded fairly. Towncore is our contribution to that future.\", \"heading\": \"A world where talent has no borders.\"}',1,'2026-04-04 20:16:31','2026-04-04 20:16:31'),(6,2,'community','Community Hub',5,'{\"content\": \"WorkMyWork is our dedicated platform for freelancers — a place to find work, grow skills, connect with peers, and build a sustainable independent career. Join thousands of professionals who have already made it their professional home.\", \"heading\": \"Join Our Freelance Community\", \"link_url\": \"https://workmywork.towncolors.com\", \"link_label\": \"Visit WorkMyWork\"}',1,'2026-04-04 20:16:31','2026-04-04 20:16:31'),(7,2,'cta','Call to Action',6,'{\"title\": \"Ready to start your next project?\", \"subtitle\": \"Tell us what you\'re building and we\'ll match you with the perfect talent within 24 hours.\", \"button_url\": \"/register\", \"button_label\": \"Start a Project\"}',1,'2026-04-04 20:16:31','2026-04-04 20:16:31');
/*!40000 ALTER TABLE `page_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'About us','about-us','<p>Id&nbsp;nihil&nbsp;voluptas,&nbsp;fringilla&nbsp;integer!&nbsp;Eligendi&nbsp;laboriosam!&nbsp;Tempor.&nbsp;Ridiculus!&nbsp;Vehicula&nbsp;dui,&nbsp;corrupti?&nbsp;At&nbsp;culpa&nbsp;aliquid&nbsp;tempore&nbsp;voluptatem?&nbsp;Molestias,&nbsp;nunc&nbsp;consectetuer&nbsp;netus&nbsp;dapibus&nbsp;eaque,&nbsp;proin&nbsp;nulla&nbsp;asperiores&nbsp;blandit&nbsp;sodales,&nbsp;aspernatur&nbsp;diam&nbsp;beatae&nbsp;odio&nbsp;id&nbsp;magnam&nbsp;sapien,&nbsp;optio&nbsp;varius&nbsp;praesent&nbsp;ab&nbsp;praesentium,&nbsp;excepteur&nbsp;lectus&nbsp;error&nbsp;quos&nbsp;varius&nbsp;augue,&nbsp;magna&nbsp;libero!&nbsp;Vulputate&nbsp;quis&nbsp;posuere&nbsp;minim&nbsp;nonummy&nbsp;consequat&nbsp;morbi&nbsp;ipsa,&nbsp;sociosqu,&nbsp;eveniet&nbsp;montes&nbsp;litora.&nbsp;Aperiam&nbsp;corporis&nbsp;numquam.&nbsp;Sociosqu?&nbsp;Illo,&nbsp;nihil,&nbsp;tempus&nbsp;quisquam!&nbsp;Quis.&nbsp;Rem&nbsp;voluptas&nbsp;occaecati?&nbsp;In&nbsp;ipsa&nbsp;condimentum&nbsp;senectus,&nbsp;occaecat,&nbsp;adipiscing!&nbsp;Voluptates&nbsp;aliqua,&nbsp;numquam&nbsp;id!&nbsp;Dignissimos&nbsp;hac,&nbsp;assumenda&nbsp;justo&nbsp;exercitation&nbsp;leo!&nbsp;Adipisicing&nbsp;etiam&nbsp;reprehenderit.&nbsp;Voluptas?&nbsp;Ad&nbsp;faucibus&nbsp;diam&nbsp;ut&nbsp;accusamus&nbsp;dolore&nbsp;cursus.&nbsp;Nostrum.</p><p></p><p>Elit&nbsp;ante,&nbsp;ea&nbsp;suspendisse&nbsp;tortor&nbsp;tellus&nbsp;purus&nbsp;rerum&nbsp;accusamus&nbsp;quisque&nbsp;eiusmod.&nbsp;Nascetur&nbsp;cumque&nbsp;tempora.&nbsp;Adipiscing?&nbsp;Beatae.&nbsp;Dolor&nbsp;optio,&nbsp;reiciendis&nbsp;eros!&nbsp;Ac&nbsp;maiores&nbsp;tempore&nbsp;occaecat&nbsp;suspendisse,&nbsp;debitis&nbsp;corporis,&nbsp;beatae&nbsp;sint&nbsp;habitasse.&nbsp;Veniam&nbsp;incididunt,&nbsp;suscipit&nbsp;porttitor,&nbsp;animi&nbsp;nostrud,&nbsp;natoque&nbsp;felis&nbsp;sint&nbsp;mattis?&nbsp;Dicta&nbsp;laoreet,&nbsp;tempora&nbsp;accusamus&nbsp;condimentum&nbsp;animi&nbsp;congue&nbsp;feugiat&nbsp;proin,&nbsp;lacinia&nbsp;libero&nbsp;nostra&nbsp;facilisis&nbsp;culpa!&nbsp;Dicta&nbsp;primis,&nbsp;repellat&nbsp;taciti&nbsp;porta&nbsp;fermentum!&nbsp;Posuere&nbsp;dictum,&nbsp;natus&nbsp;qui&nbsp;torquent,&nbsp;doloremque?&nbsp;Phasellus,&nbsp;tincidunt,&nbsp;tempore,&nbsp;itaque&nbsp;repellendus&nbsp;autem,&nbsp;velit&nbsp;temporibus&nbsp;habitant&nbsp;nisi,&nbsp;habitant&nbsp;quisquam&nbsp;a&nbsp;sint&nbsp;montes&nbsp;perspiciatis!&nbsp;Fuga&nbsp;tempore,&nbsp;lacus&nbsp;diamlorem&nbsp;nostrum?&nbsp;Ex&nbsp;sapiente&nbsp;minim&nbsp;optio&nbsp;mauris&nbsp;natoque&nbsp;quae&nbsp;numquam,&nbsp;tellus?&nbsp;Debitis&nbsp;hic&nbsp;unde&nbsp;labore.</p><p></p><p>Tempora&nbsp;asperiores&nbsp;ullamco&nbsp;eget,&nbsp;ipsum&nbsp;hic&nbsp;fusce&nbsp;duis&nbsp;aliqua&nbsp;fringilla&nbsp;cras&nbsp;quam&nbsp;nonummy&nbsp;reprehenderit&nbsp;do&nbsp;distinctio,&nbsp;necessitatibus&nbsp;primis&nbsp;dolor&nbsp;dapibus&nbsp;purus&nbsp;rem&nbsp;consectetuer&nbsp;scelerisque&nbsp;praesentium&nbsp;nascetur,&nbsp;tempore&nbsp;cillum&nbsp;eleifend&nbsp;incididunt&nbsp;elit&nbsp;aperiam!&nbsp;Sint&nbsp;neque&nbsp;molestiae&nbsp;cupiditate,&nbsp;ab&nbsp;cubilia!&nbsp;Reprehenderit&nbsp;habitasse?&nbsp;Natus&nbsp;taciti&nbsp;molestias&nbsp;sociosqu&nbsp;lorem?&nbsp;Faucibus&nbsp;ad&nbsp;quo,&nbsp;sint&nbsp;eveniet&nbsp;a&nbsp;habitant&nbsp;omnis&nbsp;expedita&nbsp;inceptos&nbsp;turpis&nbsp;magna?&nbsp;Duis&nbsp;condimentum&nbsp;explicabo,&nbsp;primis&nbsp;pharetra&nbsp;risus&nbsp;facilisis&nbsp;voluptates,&nbsp;recusandae,&nbsp;facere&nbsp;quibusdam&nbsp;accumsan?&nbsp;Nostrud&nbsp;reiciendis&nbsp;commodo!&nbsp;Nisl&nbsp;necessitatibus.&nbsp;Quia,&nbsp;venenatis&nbsp;aliquid&nbsp;atque&nbsp;molestie&nbsp;sodales&nbsp;dui,&nbsp;quae&nbsp;elit&nbsp;scelerisque!&nbsp;Asperiores&nbsp;illum&nbsp;class&nbsp;eget&nbsp;congue&nbsp;irure,&nbsp;eaque&nbsp;class&nbsp;auctor&nbsp;pulvinar&nbsp;quos&nbsp;facilisi.&nbsp;Class&nbsp;hymenaeos&nbsp;sapiente&nbsp;mollitia.</p>',NULL,'Id nihil voluptas, fringilla integer! Eligendi laboriosam! Tempor.',1,'2026-04-01 21:42:50','2026-04-01 21:42:50'),(2,'About Us','about',NULL,NULL,NULL,1,'2026-04-04 19:49:31','2026-04-04 19:49:31');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_invoice_id_foreign` (`invoice_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,1500.00,'2026-04-01 13:21:00','2026-04-01 13:21:00'),(2,2,1800.00,'2026-04-01 14:43:42','2026-04-01 14:43:42');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolios`
--

DROP TABLE IF EXISTS `portfolios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `portfolios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `product_description` longtext COLLATE utf8mb4_unicode_ci,
  `client_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `industry` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completion_year` smallint unsigned DEFAULT NULL,
  `duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `services` json DEFAULT NULL,
  `technologies` json DEFAULT NULL,
  `results` text COLLATE utf8mb4_unicode_ci,
  `extra_info` text COLLATE utf8mb4_unicode_ci,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `image_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_gallery` json DEFAULT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `item_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'project',
  `is_purchasable` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(12,2) DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `purchase_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `portfolios_slug_unique` (`slug`),
  KEY `portfolios_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolios`
--

LOCK TABLES `portfolios` WRITE;
/*!40000 ALTER TABLE `portfolios` DISABLE KEYS */;
INSERT INTO `portfolios` VALUES (1,2,'car',NULL,'asdasasasasxzxasasasdadadadadadadadasasasazaas',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'portfolio/2/rHO8SqZvipQvDR5zJ8l1FFqgwMm6CxnRGgtBoeDK.webp',NULL,'rejected','project',0,NULL,'USD',NULL,'2026-04-01 16:03:20','2026-04-15 22:33:55'),(2,11,'Lomo Tanzania Safari Website Platform','lomo-tanzania-safari-website-platform','Designed and delivered a conversion-focused safari website with destination pages, itinerary storytelling, and strong mobile performance.',NULL,'Lomo Tanzania Safari','https://www.lomotanzaniasafari.com','Tourism & Safari','Tanzania',2025,'8 weeks','[\"Web Design\", \"SEO\", \"Content Strategy\", \"Booking UX\"]','[\"Laravel\", \"Tailwind CSS\", \"MySQL\"]','Improved inquiry quality and reduced bounce rate on tour package landing pages.',NULL,1,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-16 05:11:30'),(3,11,'Kunjan Afrika Travel Website','kunjan-afrika-travel-website','Built a high-trust travel brand website with package showcases, gallery sections, and streamlined contact conversion points.',NULL,'Kunjan Afrika','https://kunjanafrika.com','Travel & Hospitality','Tanzania',2025,'6 weeks','[\"Web Design\", \"Technical SEO\", \"Lead Forms\"]','[\"Laravel\", \"Alpine.js\", \"Tailwind CSS\"]','Increased lead submissions from organic pages and improved time-on-site for package pages.',NULL,1,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-16 05:11:30'),(4,6,'Asilia Africa Experience Showcase','asilia-africa-experience-showcase','Delivered an experience-led presentation structure for camps and safaris with a premium visual hierarchy.',NULL,'Asilia Africa','https://www.asiliaafrica.com','Luxury Hospitality','Tanzania',2024,'10 weeks','[\"UX Audit\", \"Content Architecture\", \"Performance Optimization\"]','[\"Laravel\", \"Vite\", \"Cloudflare\"]','Higher engagement with destination pages and stronger internal navigation depth.',NULL,1,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-15 22:30:32'),(5,6,'Nomad Tanzania Booking Funnel Refresh','nomad-tanzania-booking-funnel-refresh','Created campaign-ready landing structures and optimized key booking journey touchpoints.',NULL,'Nomad Tanzania','https://www.nomad-tanzania.com','Tourism','Tanzania',2024,'7 weeks','[\"Conversion Optimization\", \"Landing Page Design\", \"Analytics\"]','[\"Laravel\", \"Google Analytics\", \"Tag Manager\"]','Better conversion tracking clarity and improved campaign landing performance.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-15 22:30:32'),(6,6,'Serengeti Balloon Safaris Digital Presence','serengeti-balloon-safaris-digital-presence','Modernized destination storytelling and improved media delivery for faster content loading.',NULL,'Serengeti Balloon Safaris','https://www.serengetiballoon.com','Adventure Tourism','Tanzania',2023,'5 weeks','[\"Website Refresh\", \"Media Optimization\", \"Local SEO\"]','[\"Laravel\", \"Image Optimization\", \"MySQL\"]','Improved mobile load times and stronger visibility for branded search terms.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-15 22:30:32'),(7,6,'Tanzania Tourism Information Architecture','tanzania-tourism-information-architecture','Reworked large-scale content grouping to improve discoverability across regions and attractions.',NULL,'Tanzania Tourism','https://www.tanzaniatourism.go.tz','Government Tourism','Tanzania',2023,'9 weeks','[\"Content Mapping\", \"Navigation Design\", \"Accessibility Review\"]','[\"Laravel\", \"Bootstrap\", \"MySQL\"]','Users reached key destination content in fewer clicks and with lower drop-off.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-15 22:30:32'),(8,6,'Precision Air Corporate Web Refresh','precision-air-corporate-web-refresh','Refined corporate communication pages and improved clarity for service and policy information.',NULL,'Precision Air','https://www.precisionairtz.com','Aviation','Tanzania',2022,'6 weeks','[\"UI Refresh\", \"Content Strategy\", \"Support Workflows\"]','[\"Laravel\", \"Tailwind CSS\", \"Redis\"]','Higher completion rate on support-related user journeys.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-15 22:30:32'),(9,6,'NMB Bank Product Experience Pages','nmb-bank-product-experience-pages','Built consistent product presentation templates and improved discoverability of key financial products.',NULL,'NMB Bank Tanzania','https://www.nmbbank.co.tz','Banking & Finance','Tanzania',2022,'8 weeks','[\"Product Page UX\", \"SEO Structure\", \"Performance\"]','[\"Laravel\", \"Alpine.js\", \"Nginx\"]','Improved user progression from product pages to inquiry actions.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-15 22:30:32'),(10,6,'AzamPay Merchant Web Onboarding','azampay-merchant-web-onboarding','Implemented merchant-focused onboarding flows and clear product communication paths.',NULL,'AzamPay','https://www.azampay.co.tz','Fintech','Tanzania',2024,'7 weeks','[\"Conversion UX\", \"Form Optimization\", \"Brand Web\"]','[\"Laravel\", \"Vue.js\", \"Tailwind CSS\"]','Better onboarding form completion and improved clarity for merchant packages.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-15 22:30:32'),(11,6,'Twiga Cement Corporate Information Hub','twiga-cement-corporate-information-hub','Created a structured corporate web experience for products, investor information, and company updates.',NULL,'Twiga Cement','https://www.twigacement.com','Manufacturing','Tanzania',2021,'6 weeks','[\"Corporate Web Design\", \"Content Governance\", \"SEO Basics\"]','[\"Laravel\", \"Blade\", \"MySQL\"]','Improved information findability for corporate and stakeholder audiences.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-15 22:30:32','2026-04-15 22:30:32'),(12,11,'Safari With A Heart Commercial Website','safari-with-a-heart-commercial-website','Built a polished safari package website focused on inquiry conversion and storytelling.',NULL,'Safari With A Heart','https://safariwithaheart.com','Safari & Conservation Tourism','Tanzania',2025,'7 weeks','[\"Website Redesign\", \"Booking UX\", \"Performance\"]','[\"Laravel\", \"Tailwind CSS\", \"MySQL\"]','Higher visitor retention and increased direct safari inquiry submissions.',NULL,1,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-16 02:30:45','2026-04-16 05:11:30'),(13,11,'Safari With A Heart Nonprofit Portal','safari-with-a-heart-nonprofit-portal','Created a mission-driven nonprofit web experience with clear donation and impact reporting sections.',NULL,'Safari With A Heart Foundation','https://safariwithaheart.org','Nonprofit & Community Impact','Tanzania',2025,'6 weeks','[\"Donation Flow UX\", \"Campaign Pages\", \"SEO\"]','[\"Laravel\", \"Alpine.js\", \"Tailwind CSS\"]','Improved donor journey clarity and newsletter sign-up growth.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-16 02:30:45','2026-04-16 05:11:30'),(14,11,'Exceptional Safaries Lead-Gen Website','exceptional-safaries-lead-gen-website','Delivered an inquiry-first travel website with focused package pages and conversion funnel.',NULL,'Exceptional Safaries','https://exceptionalsafaries.com','Travel & Safari','Tanzania',2024,'8 weeks','[\"Sales Funnel Design\", \"CRM-ready Forms\", \"Technical SEO\"]','[\"Laravel\", \"Vite\", \"MySQL\"]','Increased qualified leads and reduced drop-off in the inquiry form process.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-16 02:30:45','2026-04-16 05:11:30'),(15,11,'African Queen Adventure Brand Website','african-queen-adventure-brand-website','Designed a modern adventure site with guided package discovery and automated inquiry routing.',NULL,'African Queen Adventure','https://africanqueenadventure.com','Adventure Tourism','Tanzania',2024,'6 weeks','[\"Brand Website\", \"Destination Pages\", \"Contact Automation\"]','[\"Laravel\", \"Blade\", \"Tailwind CSS\"]','Improved conversion from destination pages to consultation requests.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-16 02:30:45','2026-04-16 05:11:30'),(16,11,'BrandHub TZ Commerce Website','brandhub-tz-commerce-website','Built an online catalog and ordering experience for mugs, T-shirts, branded prints, and design services.',NULL,'BrandHub TZ','https://brandhub.tz','Printing & Design','Tanzania',2025,'9 weeks','[\"Ecommerce Setup\", \"Catalog UX\", \"Order Workflow\"]','[\"Laravel\", \"Livewire\", \"MySQL\"]','Faster quote turnaround and improved order conversion for custom products.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-16 02:30:45','2026-04-16 05:11:30'),(17,11,'Makando Car Hire Booking Website','makando-car-hire-booking-website','Developed a booking-focused car hire website with clear fleet details and reservation workflow.',NULL,'Makando Car Hire','https://makandocarhire.com','Transport & Car Rental','Tanzania',2025,'7 weeks','[\"Fleet Listing UX\", \"Booking Requests\", \"SEO Setup\"]','[\"Laravel\", \"Tailwind CSS\", \"MySQL\"]','Improved booking form completion and reduced inquiry response time.',NULL,0,NULL,NULL,'approved','project',0,NULL,'USD',NULL,'2026-04-16 02:30:45','2026-04-16 05:11:30'),(18,11,'MediCore Hospital Management System','medicore-hospital-management-system','Complete hospital information system with EMR workflows, appointment scheduling, billing, pharmacy, and role-based access.',NULL,'Towncore Product Suite',NULL,'Healthcare','Global',2026,'SaaS License','[\"Patient Records\", \"Billing\", \"Appointments\", \"Pharmacy\"]','[\"Laravel\", \"Vue.js\", \"MySQL\", \"REST API\"]','Helps hospitals streamline patient flow, reduce paperwork, and improve service delivery.',NULL,1,NULL,NULL,'approved','product',1,2499.00,'USD','mailto:sales@towncore.local?subject=Purchase%20MediCore%20Hospital%20Management%20System','2026-04-16 02:30:45','2026-04-16 05:11:30'),(19,11,'ExamFlow School Examination System','examflow-school-examination-system','School examination platform for question banks, timed assessments, grading, and detailed performance reporting.',NULL,'Towncore Product Suite',NULL,'Education','Global',2026,'SaaS License','[\"Exam Authoring\", \"Auto Marking\", \"Result Analytics\", \"Report Cards\"]','[\"Laravel\", \"Livewire\", \"MySQL\"]','Saves exam administration time and improves accuracy of grading and student performance insights.',NULL,1,NULL,NULL,'approved','product',1,1499.00,'USD','mailto:sales@towncore.local?subject=Purchase%20ExamFlow%20School%20Examination%20System','2026-04-16 02:30:45','2026-04-16 05:11:30'),(20,11,'TouriDesk Tourism SaaS & Ticket Booking Suite','touridesk-tourism-saas-ticket-booking-suite','All-in-one tourism SaaS for agencies handling package sales, ticket inventory, customer records, and booking operations.',NULL,'Towncore Product Suite',NULL,'Tourism Technology','Global',2026,'SaaS License','[\"Tour Packages\", \"Ticket Booking\", \"Payment Tracking\", \"Customer CRM\"]','[\"Laravel\", \"Alpine.js\", \"MySQL\", \"API Integrations\"]','Improves booking management, reduces operational overhead, and speeds up customer response.',NULL,1,NULL,NULL,'approved','product',1,1899.00,'USD','mailto:sales@towncore.local?subject=Purchase%20TouriDesk%20Tourism%20SaaS%20Suite','2026-04-16 02:30:45','2026-04-16 05:11:30'),(21,11,'SecureVault Password Store App','securevault-password-store-app','Secure password management app for teams with encryption, access controls, and audit visibility.',NULL,'Towncore Product Suite',NULL,'Cybersecurity','Global',2026,'SaaS License','[\"Encrypted Vault\", \"Team Sharing\", \"Audit Logs\", \"2FA Security\"]','[\"Laravel\", \"Vue.js\", \"AES Encryption\"]','Reduces credential leaks and centralizes secure password governance.',NULL,0,NULL,NULL,'approved','product',1,699.00,'USD','mailto:sales@towncore.local?subject=Purchase%20SecureVault%20Password%20Store%20App','2026-04-16 02:30:45','2026-04-16 05:11:30'),(22,11,'CivicVote Digital Voting System','civicvote-digital-voting-system','Secure digital voting platform for organizations, cooperatives, and associations requiring transparent elections.',NULL,'Towncore Product Suite',NULL,'Governance & Associations','Global',2026,'SaaS License','[\"Voter Registry\", \"Ballot Management\", \"Results Dashboard\", \"Audit Trails\"]','[\"Laravel\", \"Livewire\", \"MySQL\"]','Improves election transparency and speeds up result publication.',NULL,0,NULL,NULL,'approved','product',1,1299.00,'USD','mailto:sales@towncore.local?subject=Purchase%20CivicVote%20Digital%20Voting%20System','2026-04-16 02:30:45','2026-04-16 05:11:30'),(23,11,'DataHub Enterprise Data Management System','datahub-enterprise-data-management-system','A structured data and records management solution for organizations managing high volumes of business documents.',NULL,'Towncore Product Suite',NULL,'Business Operations','Global',2026,'SaaS License','[\"Document Registry\", \"Records Workflow\", \"Permissions\", \"Advanced Search\"]','[\"Laravel\", \"Elasticsearch\", \"MySQL\"]','Improves data governance and reduces time spent retrieving records.',NULL,0,NULL,NULL,'approved','product',1,1599.00,'USD','mailto:sales@towncore.local?subject=Purchase%20DataHub%20Data%20Management%20System','2026-04-16 02:30:45','2026-04-16 05:11:30');
/*!40000 ALTER TABLE `portfolios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_images` json DEFAULT NULL,
  `meta_title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (6,'Choosing Between Custom Software And Off-The-Shelf Tools','choosing-between-custom-software-and-off-the-shelf-tools','<h2>Start with workflow complexity</h2><p>If your process is highly standardized, off-the-shelf tools are usually enough. If your process creates competitive advantage and contains complex approvals, custom software becomes more valuable.</p><h2>Decision criteria</h2><ul><li>How often does your process change?</li><li>Do you need strict ownership and data controls?</li><li>Are manual workarounds costing significant time?</li><li>Will integration requirements keep expanding?</li></ul><h2>Total cost perspective</h2><p>License fees look cheaper at first, but operational friction can become expensive. Compare implementation cost against annual productivity losses and error exposure.</p><h2>Hybrid strategy</h2><p>Many teams combine both: SaaS for non-core functions, custom modules for critical workflow and reporting layers.</p>','posts/2eMfj6eZXcTiOJP3H46riTE4x4ZKHZXvCBxqmQkY.webp','[]','Choosing Between Custom Software And Off-The-Shelf Tools','How to evaluate when custom software is justified and when existing SaaS tools are enough for your current stage.','published','2026-04-12 13:14:18','2026-04-16 05:11:30','2026-04-16 13:14:19'),(11,'Building A Reliable Delivery Workflow For Client Projects','building-reliable-delivery-workflow-for-client-projects','<h2>Delivery reliability is a system</h2><p>Teams that deliver consistently follow clear routines. They do not rely on heroics. They use structured checkpoints, issue ownership, and communication cadence.</p><h2>Weekly operating rhythm</h2><ul><li>Monday: priorities and blockers.</li><li>Midweek: progress and risk review.</li><li>Friday: client-facing summary and next actions.</li></ul><h2>Quality checkpoints</h2><p>Set acceptance criteria before development starts. Validate against those criteria before every handover. This avoids subjective review loops.</p><h2>Client communication</h2><p>Keep updates concise: completed work, current risks, and the exact decision needed from the client. Decision clarity reduces timeline drift.</p>',NULL,NULL,'Building A Reliable Delivery Workflow For Client Projects','A practical delivery system for agencies and product teams covering handover, communication cadence, and quality checkpoints.','published','2026-04-14 13:14:18','2026-04-16 13:14:19','2026-04-16 13:14:19'),(8,'How To Scope A Business Software Project Without Wasting Budget','scope-business-software-project-without-wasting-budget','<h2>Why scope matters</h2><p>Most software delays come from unclear assumptions. Teams begin with broad goals, but no defined workflows, no measurable outputs, and no ownership map. Scope creates decision boundaries so engineering can move quickly without rework.</p><h2>A simple scoping framework</h2><ul><li>Define the business event that starts the workflow.</li><li>Define each role that touches the process.</li><li>Define the final output and where it is stored.</li><li>Define the approval steps and exception paths.</li><li>Define reporting metrics and update cadence.</li></ul><h2>Budget guardrails</h2><p>Break implementation into phases: core workflow first, automation second, advanced reporting third. This keeps budget tied to usable outcomes and avoids overbuilding on day one.</p><h2>What to prepare before kickoff</h2><p>Bring sample forms, current spreadsheets, approval hierarchy, and top three pain points by financial impact. With these, your product team can design an implementation roadmap in days, not months.</p>',NULL,NULL,'How To Scope A Business Software Project Without Wasting Budget','A practical framework for defining scope, timelines, risks, and technical assumptions before starting custom software development.','published','2026-04-06 13:14:18','2026-04-16 13:14:19','2026-04-16 13:14:19'),(9,'Cloud Hosting Checklist For Growing Teams','cloud-hosting-checklist-for-growing-teams','<h2>Baseline infrastructure decisions</h2><p>Before launch, choose your deployment model, backup window, and log retention period. These foundational decisions reduce surprise outages and simplify incident response.</p><h2>Security essentials</h2><ul><li>Use role-based access with least privilege.</li><li>Rotate credentials and API keys on schedule.</li><li>Enforce HTTPS and secure session management.</li><li>Patch OS and framework dependencies monthly.</li></ul><h2>Operational readiness</h2><p>Set uptime checks, alert channels, and response ownership. Every critical service should have at least one rollback path and one tested restore path.</p><h2>Performance hygiene</h2><p>Track slow queries, cache hit rate, queue delays, and response time percentiles. Performance tuning is a process, not a one-time event.</p>',NULL,NULL,'Cloud Hosting Checklist For Growing Teams','A practical cloud readiness checklist covering security, backup, uptime, and release controls for production applications.','published','2026-04-08 13:14:18','2026-04-16 13:14:19','2026-04-16 13:14:19'),(10,'Designing Service Pages That Convert Leads Into Real Projects','designing-service-pages-that-convert-leads-into-real-projects','<h2>Lead conversion starts with clarity</h2><p>Visitors do not convert because of design alone. They convert when they quickly understand what you do, who it is for, and how to start.</p><h2>Essential page blocks</h2><ol><li>Problem statement connected to business impact.</li><li>Service outcomes with measurable benefits.</li><li>Proof section with portfolio and client context.</li><li>Process timeline with delivery phases.</li><li>Primary and secondary call-to-action.</li></ol><h2>Improve trust signals</h2><p>Add realistic duration and pricing ranges where possible. Transparent expectations reduce low-quality leads and improve client readiness.</p><h2>Conversion optimization tip</h2><p>Use one clear primary CTA per section. Too many options increase hesitation and lower completion rate.</p>',NULL,NULL,'Designing Service Pages That Convert Leads Into Real Projects','Learn the structure of high-performing service pages: positioning, proof, process clarity, and conversion-focused CTA blocks.','published','2026-04-10 13:14:18','2026-04-16 13:14:19','2026-04-16 13:14:19'),(12,'SEO Foundations Every Business Website Needs In 2026','seo-foundations-every-business-website-needs-in-2026','<h2>Start with technical readiness</h2><p>SEO performance starts before content. Ensure crawlable architecture, clear canonical tags, valid sitemap submission, and fast page rendering across mobile devices. These are baseline conditions, not optional enhancements.</p><h2>Content architecture that scales</h2><p>Build clear topic clusters around your core services. Each cluster should include one authoritative pillar page and supporting pages that answer specific user intent questions. Internal links should move users deeper into relevant solutions naturally.</p><h2>On-page essentials</h2><ul><li>One clear H1 per page aligned to search intent.</li><li>Specific meta titles and descriptions, not duplicated templates.</li><li>Structured headings that mirror reader decision flow.</li><li>Image optimization with meaningful alt text and compressed assets.</li><li>Schema markup where appropriate for business and article content.</li></ul><h2>Measure what matters</h2><p>Track qualified clicks, indexed pages, and lead-quality outcomes, not just impressions. SEO is valuable when it compounds trusted visibility into conversations and revenue.</p>',NULL,NULL,'SEO Foundations Every Business Website Needs In 2026','A practical SEO implementation guide covering technical setup, content structure, and on-page signals for sustainable rankings.','published','2026-04-04 13:14:18','2026-04-16 13:14:19','2026-04-16 13:14:19'),(13,'How To Plan A Website Redesign Without Losing Existing Traffic','how-to-plan-a-website-redesign-without-losing-existing-traffic','<h2>Redesign risk is usually migration risk</h2><p>Most traffic drops after redesign happen because URL structures, metadata, and internal links are changed without a controlled migration plan. Design changes are rarely the direct cause; broken relevance signals are.</p><h2>Pre-redesign audit checklist</h2><ul><li>Export current top pages by organic traffic and conversions.</li><li>Map every existing URL to its new URL destination.</li><li>Preserve high-performing titles and page intent where valid.</li><li>Document internal links to critical conversion pages.</li><li>Capture baseline technical metrics before launch.</li></ul><h2>Launch controls</h2><p>Implement 301 redirects, regenerate sitemap.xml, and run crawl validation immediately after release. Check index coverage and monitor Search Console warnings daily in the first two weeks.</p><h2>Post-launch optimization</h2><p>Use behavior data to improve clarity and conversion paths. Redesign should improve both visibility and business action, not aesthetics alone.</p>',NULL,NULL,'How To Plan A Website Redesign Without Losing Existing Traffic','Learn a proven redesign migration process that protects rankings, preserves URLs, and improves conversion clarity.','published','2026-04-05 13:14:18','2026-04-16 13:14:19','2026-04-16 13:14:19'),(14,'Content Marketing For Service Companies: A Practical Execution Model','content-marketing-for-service-companies-practical-execution-model','<h2>Why random content fails</h2><p>Posting frequently without strategy creates activity but not demand. Service businesses need content that aligns with buyer questions at each decision stage, from problem awareness to vendor evaluation.</p><h2>Three-layer content system</h2><ol><li>Authority content: long-form guides proving expertise.</li><li>Decision content: comparisons, frameworks, and pricing context.</li><li>Trust content: case studies, process walkthroughs, and outcomes.</li></ol><h2>Editorial cadence</h2><p>Publish fewer but stronger assets. One high-quality pillar article plus two focused support pieces per month often outperforms low-depth weekly posts.</p><h2>Distribution discipline</h2><p>Repurpose each article into social snippets, email summaries, and sales enablement materials. The same core insight should support marketing, sales, and onboarding conversations.</p>',NULL,NULL,'Content Marketing For Service Companies: A Practical Execution Model','A repeatable content workflow for service businesses to attract qualified leads through educational, intent-driven publishing.','published','2026-04-07 13:14:18','2026-04-16 13:14:19','2026-04-16 13:14:19'),(15,'Security Practices For Client Portals And File Sharing Systems','security-practices-for-client-portals-and-file-sharing-systems','<h2>Client portals carry trust risk</h2><p>When users upload contracts, financial records, or strategy documents, your platform becomes a trust boundary. Security controls must be visible in architecture and operations.</p><h2>Access and identity controls</h2><ul><li>Enforce strong password policy and optional MFA.</li><li>Use role-based permissions per workspace and project.</li><li>Expire inactive sessions and revoke stale tokens.</li><li>Audit login history and suspicious access attempts.</li></ul><h2>File handling safeguards</h2><p>Validate file types, enforce size limits, and store files outside public paths. Generate signed, temporary URLs for previews/downloads rather than permanent direct links.</p><h2>Operational protections</h2><p>Maintain encryption in transit, regular backups, and incident playbooks. Security maturity is shown by preparedness, not only prevention.</p>',NULL,NULL,'Security Practices For Client Portals And File Sharing Systems','A practical security checklist for protecting sensitive client files, communication channels, and account access in web portals.','published','2026-04-09 13:14:18','2026-04-16 13:14:19','2026-04-16 13:14:19'),(16,'Choosing The Right KPIs For Software Projects','choosing-the-right-kpis-for-software-projects','<h2>Vanity metrics create false confidence</h2><p>Teams often track commits, story points, and release counts while ignoring outcomes that matter to stakeholders. Good KPIs connect platform behavior to operational and revenue impact.</p><h2>KPI categories to track</h2><ul><li>Adoption: active users, feature usage depth, retention.</li><li>Operations: task completion time, error rates, rework reduction.</li><li>Commercial: lead conversion lift, cost-to-serve reduction, revenue velocity.</li><li>Reliability: uptime, incident frequency, mean time to recovery.</li></ul><h2>Set baseline before launch</h2><p>Measure current process performance first. Without baseline, post-launch improvements are hard to verify and harder to communicate to decision-makers.</p><h2>Review cycle</h2><p>Establish monthly KPI reviews with shared ownership between product, operations, and leadership. Metrics should guide roadmap decisions, not just reporting slides.</p>',NULL,NULL,'Choosing The Right KPIs For Software Projects','Define software KPIs that connect technical delivery to business outcomes, adoption quality, and operational efficiency.','published','2026-04-11 13:14:18','2026-04-16 13:14:19','2026-04-16 13:14:19'),(17,'From Idea To MVP: How To Launch Faster With Lower Risk','from-idea-to-mvp-how-to-launch-faster-with-lower-risk','<h2>MVP&nbsp;is&nbsp;a&nbsp;learning&nbsp;tool,&nbsp;not&nbsp;a&nbsp;small&nbsp;final&nbsp;product</h2><p>The&nbsp;goal&nbsp;of&nbsp;an&nbsp;MVP&nbsp;is&nbsp;to&nbsp;test&nbsp;whether&nbsp;your&nbsp;core&nbsp;value&nbsp;proposition&nbsp;solves&nbsp;a&nbsp;real&nbsp;problem&nbsp;for&nbsp;a&nbsp;clear&nbsp;user&nbsp;group.&nbsp;It&nbsp;should&nbsp;answer&nbsp;critical&nbsp;assumptions&nbsp;quickly.</p><h2>Define&nbsp;launch&nbsp;assumptions</h2><ul><li>Who&nbsp;is&nbsp;the&nbsp;first&nbsp;user&nbsp;segment?</li><li>What&nbsp;is&nbsp;the&nbsp;exact&nbsp;problem&nbsp;statement?</li><li>What&nbsp;behavior&nbsp;proves&nbsp;product&nbsp;value?</li><li>What&nbsp;data&nbsp;will&nbsp;determine&nbsp;next&nbsp;iteration?</li></ul><h2>Scope&nbsp;for&nbsp;speed</h2><p>Build&nbsp;only&nbsp;the&nbsp;path&nbsp;needed&nbsp;for&nbsp;a&nbsp;user&nbsp;to&nbsp;reach&nbsp;value&nbsp;once.&nbsp;Delay&nbsp;edge-case&nbsp;automation,&nbsp;advanced&nbsp;analytics,&nbsp;and&nbsp;deep&nbsp;customization&nbsp;until&nbsp;usage&nbsp;validates&nbsp;demand.</p><h2>Post-launch&nbsp;loops</h2><p>Collect&nbsp;usage&nbsp;and&nbsp;interview&nbsp;insights&nbsp;weekly.&nbsp;Convert&nbsp;repeating&nbsp;friction&nbsp;points&nbsp;into&nbsp;roadmap&nbsp;priorities&nbsp;and&nbsp;keep&nbsp;iteration&nbsp;cycles&nbsp;short.</p>','posts/0eUoeYdACrl0tfaayzLdazqzuWcAGeW0iNd4l1ia.webp','[]','From Idea To MVP: How To Launch Faster With Lower Risk','A practical MVP launch framework that helps founders validate value quickly without overbuilding early product scope.','published','2026-04-15 13:14:18','2026-04-16 13:14:19','2026-04-16 18:13:45');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_categories`
--

DROP TABLE IF EXISTS `project_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `long_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price_range` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_duration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery_images` json DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#F97316',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_categories_slug_unique` (`slug`),
  KEY `project_categories_parent_id_foreign` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_categories`
--

LOCK TABLES `project_categories` WRITE;
/*!40000 ALTER TABLE `project_categories` DISABLE KEYS */;
INSERT INTO `project_categories` VALUES (1,'Graphic design','graphic-design',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'#eab308','2026-04-01 12:30:20','2026-04-01 12:30:20'),(2,'Website design','website-design',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'#f97316','2026-04-01 12:30:32','2026-04-01 12:30:32'),(3,'Custom Web Development','custom-web-development',NULL,NULL,NULL,NULL,'categories/Nl23mmkIYJWV8MkqbyBRqkL4lhcDCFR0nvTAdcTW.webp','categories/featured/TWNPNwBksWue1E9cLGjxaUdP8ZYGcgMbonuq5It1.webp','[\"categories/gallery/6SRiHixBU2MpOJeMep0hupRZ7fqHzQg6Fetq75mo.webp\"]',NULL,'#10b981','2026-04-01 12:31:17','2026-04-16 18:33:09'),(4,'Content Creation','content-creation',NULL,NULL,NULL,NULL,'categories/d7RiFfShDhgzWO9nNfAO7wIExfQj0i1dX2Qu4ARQ.webp','categories/featured/Ce3SyBPsSNONmaY1AD6rKWyM6GkoUD7ulappyf2i.webp','[\"categories/gallery/Q4oWhEN13fn0Dw2pLQOg0eNgMSnFPCaSqzUX5KCi.webp\"]',NULL,'#3b82f6','2026-04-01 12:31:56','2026-04-16 18:23:05'),(5,'Photography and videography','photography-and-videography',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'#06b6d4','2026-04-01 12:33:02','2026-04-01 12:33:02'),(6,'App development','app-development',NULL,NULL,NULL,NULL,'categories/fEjs1twAQIZow0Bsa7RS7Z1QyZQ7bXterFdwdo08.webp','categories/featured/PInT5cBkGj23EJsahvSiLJGmVu3HxRdiGUFk3R0U.webp',NULL,NULL,'#8b5cf6','2026-04-01 12:33:23','2026-04-16 18:20:17'),(7,'SEO','seo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'#64748b','2026-04-01 12:33:48','2026-04-01 12:33:48'),(8,'Social Media Management','social-media-management',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'#ec4899','2026-04-01 12:34:14','2026-04-01 12:34:14'),(9,'prototype design','prototype-design','Nostra amet quasi dictumst magnis exercitationem aliqua adipisci non anim venenatis? Per! Auctor voluptatibus quae. Faucibus? Sem repellendus integer illum, asperiores tincidunt morbi, phasellus, facere sodales ex incidunt quis placerat nullam, tempor m',NULL,NULL,NULL,NULL,NULL,NULL,6,'#5e3517','2026-04-01 20:04:57','2026-04-01 20:04:57');
/*!40000 ALTER TABLE `project_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_files`
--

DROP TABLE IF EXISTS `project_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_files_project_id_foreign` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_files`
--

LOCK TABLES `project_files` WRITE;
/*!40000 ALTER TABLE `project_files` DISABLE KEYS */;
INSERT INTO `project_files` VALUES (1,1,'projects/1/upGQzU7iILKJ5DarQ9py4oWqmxGNuqSmSA8Xvqtl.jpg','2026-03-29 05:45:47','2026-03-29 05:45:47'),(2,2,'projects/2/c68kkQsaOB53ei4DTBw03vLtBGp8rTy4K5lWaErB.png','2026-04-01 14:40:54','2026-04-01 14:40:54'),(3,3,'projects/3/QJ0vFrguU2zRSnXtvTQMlTUprJeoOKkhfs70izgz.png','2026-04-05 12:31:28','2026-04-05 12:31:28');
/*!40000 ALTER TABLE `project_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `freelancer_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','assigned','in_progress','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_client_id_foreign` (`client_id`),
  KEY `projects_freelancer_id_foreign` (`freelancer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,3,2,'Web design','test action one submit','in_progress','2026-03-29 05:45:47','2026-04-01 09:33:23'),(2,3,8,'App development','make a delivery app','in_progress','2026-04-01 14:40:54','2026-04-15 17:31:37'),(3,3,6,'ttt','Accusantium fugit praesentium exercitation aliquet luctus reprehenderit animi ligula morbi maiores erat, conubia. Sociosqu saepe, placerat, purus id. Earum quisque id, eros hendrerit.\r\n\r\nIncidunt tincidunt eleifend nostra, natoque per! Cumque, minus dolorem, sunt dolore, morbi, quae duis similique enim, lectus, rhoncus, odit, in occaecati proident corrupti.\r\n\r\nAenean penatibus donec aliqua iure anim eius proident suscipit quisquam. Hic vel, sem, porttitor leo quo reiciendis, duis? Libero repellendus sagittis exercitation occaecati.\r\n\r\nEiusmod distinctio. Voluptates delectus illo lacinia quas posuere voluptas, ipsa! Minus? Interdum inventore, nihil iste donec, in metus, hac, arcu, lectus consequuntur aliquip.','assigned','2026-04-05 12:31:28','2026-04-15 17:18:53');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_media_id` bigint unsigned DEFAULT NULL,
  `light_logo_media_id` bigint unsigned DEFAULT NULL,
  `dark_logo_media_id` bigint unsigned DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `bank_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `primary_color` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#F97316',
  `secondary_color` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#EA580C',
  `background_color` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#F5F5F4',
  `payment_card_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `payment_paypal_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `payment_selcom_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `payment_mpesa_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `payment_bank_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mpesa_paybill` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_notes` text COLLATE utf8mb4_unicode_ci,
  `service_hero_media_id` bigint unsigned DEFAULT NULL,
  `blog_hero_media_id` bigint unsigned DEFAULT NULL,
  `shop_hero_media_id` bigint unsigned DEFAULT NULL,
  `cloud_hero_media_id` bigint unsigned DEFAULT NULL,
  `portfolio_hero_media_id` bigint unsigned DEFAULT NULL,
  `about_hero_media_id` bigint unsigned DEFAULT NULL,
  `contact_hero_media_id` bigint unsigned DEFAULT NULL,
  `service_hero_subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blog_hero_subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shop_hero_subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cloud_hero_subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portfolio_hero_subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_hero_subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_hero_subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_logo_media_id_foreign` (`logo_media_id`),
  KEY `settings_light_logo_media_id_foreign` (`light_logo_media_id`),
  KEY `settings_dark_logo_media_id_foreign` (`dark_logo_media_id`),
  KEY `settings_service_hero_media_id_foreign` (`service_hero_media_id`),
  KEY `settings_blog_hero_media_id_foreign` (`blog_hero_media_id`),
  KEY `settings_shop_hero_media_id_foreign` (`shop_hero_media_id`),
  KEY `settings_cloud_hero_media_id_foreign` (`cloud_hero_media_id`),
  KEY `settings_portfolio_hero_media_id_foreign` (`portfolio_hero_media_id`),
  KEY `settings_about_hero_media_id_foreign` (`about_hero_media_id`),
  KEY `settings_contact_hero_media_id_foreign` (`contact_hero_media_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'Town colors','logos/IdwUWgDR3iNEfK9wAYVqre9W46waAxOSNMoNnds7.png',6,5,6,'+255758273300','info@twncolors.com','Sakina kwa idd, Arusha, Tanzania','CRDB','#f5640a','#ea580c','#f5f5f4',0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-01 11:56:26','2026-04-16 05:28:23');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `software_purchase_requests`
--

DROP TABLE IF EXISTS `software_purchase_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `software_purchase_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `portfolio_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_reference` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `software_purchase_requests_portfolio_id_foreign` (`portfolio_id`),
  KEY `software_purchase_requests_user_id_foreign` (`user_id`),
  KEY `software_purchase_requests_status_created_at_index` (`status`,`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `software_purchase_requests`
--

LOCK TABLES `software_purchase_requests` WRITE;
/*!40000 ALTER TABLE `software_purchase_requests` DISABLE KEYS */;
INSERT INTO `software_purchase_requests` VALUES (1,19,9,'Demo Admin','admin@towncore.local','+255758273300','brandrtz','bank',NULL,NULL,'pending',NULL,'2026-04-16 05:27:46','2026-04-16 05:27:46'),(2,18,9,'Demo Admin','admin@towncore.local',NULL,NULL,'manual_request',NULL,NULL,'pending',NULL,'2026-04-16 18:29:42','2026-04-16 18:29:42');
/*!40000 ALTER TABLE `software_purchase_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribers_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscribers`
--

LOCK TABLES `subscribers` WRITE;
/*!40000 ALTER TABLE `subscribers` DISABLE KEYS */;
INSERT INTO `subscribers` VALUES (1,'roger@safariswithaheart.com','2026-04-02 12:53:59','2026-04-02 12:53:59'),(2,'towncolorsmail@gmail.com','2026-04-16 19:04:25','2026-04-16 19:04:25');
/*!40000 ALTER TABLE `subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_plans`
--

DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'green',
  `price_monthly` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_yearly` decimal(10,2) NOT NULL DEFAULT '0.00',
  `features` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_plans_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_plans`
--

LOCK TABLES `subscription_plans` WRITE;
/*!40000 ALTER TABLE `subscription_plans` DISABLE KEYS */;
INSERT INTO `subscription_plans` VALUES (1,'Green Essential','green-essential','green',19.99,199.99,'[\"Up to 3 active projects\", \"Basic file storage (1 GB)\", \"Client dashboard access\", \"Email support\"]',1,1,'2026-04-14 21:43:26','2026-04-14 21:43:26'),(2,'Blue Advantage','blue-advantage','blue',49.99,499.99,'[\"Up to 10 active projects\", \"Extended file storage (10 GB)\", \"Priority project assignment\", \"Chat & email support\", \"Monthly progress reports\"]',1,2,'2026-04-14 21:43:26','2026-04-14 21:43:26'),(3,'Purple Elite','purple-elite','purple',99.99,999.99,'[\"Unlimited active projects\", \"Large file storage (50 GB)\", \"Dedicated freelancer assignment\", \"24/7 priority support\", \"Weekly progress reports\", \"Custom invoice branding\"]',1,3,'2026-04-14 21:43:26','2026-04-14 21:43:26'),(4,'Black Ultimate','black-ultimate','black',199.99,1999.99,'[\"Unlimited active projects\", \"Unlimited file storage\", \"Dedicated account manager\", \"24/7 VIP support line\", \"Daily progress reports\", \"Custom invoice branding\", \"API access\", \"SLA guarantee\"]',1,4,'2026-04-14 21:43:26','2026-04-14 21:43:26');
/*!40000 ALTER TABLE `subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_requests`
--

DROP TABLE IF EXISTS `subscription_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `billing_cycle` enum('monthly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscription_requests_user_id_foreign` (`user_id`),
  KEY `subscription_requests_plan_id_foreign` (`plan_id`),
  KEY `subscription_requests_reviewed_by_foreign` (`reviewed_by`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_requests`
--

LOCK TABLES `subscription_requests` WRITE;
/*!40000 ALTER TABLE `subscription_requests` DISABLE KEYS */;
INSERT INTO `subscription_requests` VALUES (1,5,1,'monthly',NULL,NULL,NULL,'approved',4,'2026-04-15 21:11:15',NULL,'2026-04-15 21:10:32','2026-04-15 21:11:15'),(2,5,4,'monthly',NULL,NULL,NULL,'approved',4,'2026-04-15 21:11:50',NULL,'2026-04-15 21:11:44','2026-04-15 21:11:50'),(3,7,1,'yearly',NULL,NULL,NULL,'approved',4,'2026-04-15 23:14:10',NULL,'2026-04-15 23:12:31','2026-04-15 23:14:10');
/*!40000 ALTER TABLE `subscription_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `billing_cycle` enum('monthly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `start_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `status` enum('active','expired','cancelled','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_user_id_foreign` (`user_id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES (3,7,1,'yearly','2026-04-15','2027-04-15','active',NULL,'2026-04-15 23:14:10','2026-04-15 23:14:10');
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','client','freelancer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '0',
  `onboarding_completed` tinyint(1) NOT NULL DEFAULT '0',
  `profile_image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_start_date` date DEFAULT NULL,
  `trial_end_date` date DEFAULT NULL,
  `trial_used_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `push_token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `push_platform` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (9,'Demo Admin',NULL,'admin@towncore.local',NULL,'2026-04-16 05:11:30','admin','$2y$10$imG2KNXa/D7EVzZ8G4MNNuD63Xgw.zT/Ss8k/Zv9kZ2rUTY0XTLdC',0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-16 05:11:30','2026-04-16 05:25:18'),(10,'Demo Client',NULL,'client@towncore.local',NULL,'2026-04-16 05:11:30','client','$2y$10$qxSArHNN9udKnYt4RiDzVOkH0tFqImBq/ZTVB99JazOmma3XX1w4C',0,1,NULL,'2026-04-16','2026-04-20','2026-04-16 05:24:10',NULL,NULL,NULL,'2026-04-16 05:11:30','2026-04-16 05:24:10'),(11,'Demo Freelancer',NULL,'freelancer@towncore.local',NULL,'2026-04-16 05:11:30','freelancer','$2y$10$rUOwoUgSzg0u71eGYOH3O.Zvla0vjbJCnR3SrZJtkyVHBHWQg5NFi',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-16 05:11:30','2026-04-16 05:11:30');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'towncore'
--

--
-- Dumping routines for database 'towncore'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-17  1:29:09
