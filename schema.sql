
CREATE DATABASE IF NOT EXISTS `bbsoop`;
USE `bbsoop`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actions` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_ID` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `remote_address` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `action` varchar(30) NOT NULL,
  `record_class` varchar(45) NOT NULL,
  `record_table` varchar(45) NOT NULL,
  `record_ID` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `user_ID` (`user_ID`),
  KEY `record_class` (`record_table`,`record_ID`),
  KEY `record_class_2` (`record_class`),
  KEY `remote_address` (`remote_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bulletins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bulletins` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `user_ID` int(10) unsigned NOT NULL,
  `body` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `partial_cache` text,
  PRIMARY KEY (`ID`),
  KEY `created_at` (`created_at`),
  KEY `user_ID` (`user_ID`),
  KEY `status` (`status`),
  CONSTRAINT `bulletins_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `URI` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `HTML_body` longtext NOT NULL,
  UNIQUE KEY `URI` (`URI`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `visible_to` tinyint(4) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `topics_count` int(10) unsigned NOT NULL DEFAULT '0',
  `replies_count` int(10) unsigned NOT NULL DEFAULT '0',
  `media_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `URL` varchar(255) NOT NULL,
  `thumbnail_URI` varchar(255) DEFAULT NULL,
  `type` varchar(30) NOT NULL,
  `height` int(10) unsigned NOT NULL DEFAULT '0',
  `t_height` int(10) unsigned NOT NULL DEFAULT '0',
  `t_width` int(10) unsigned NOT NULL DEFAULT '0',
  `width` int(10) unsigned NOT NULL DEFAULT '0',
  `duration` int(10) unsigned NOT NULL DEFAULT '0',
  `original_file_name` varchar(255) NOT NULL,
  `file_size` int(10) unsigned NOT NULL DEFAULT '0',
  `signature` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `user_ID` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `posts_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'simple counter for the number of posts that reference this media',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `URI` (`URL`),
  KEY `user_ID` (`user_ID`),
  KEY `status` (`status`),
  KEY `fingerprint` (`signature`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `media_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `URI` varchar(60) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `parent_ID` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `partial_cache` text,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `URI` (`URI`),
  KEY `parent_ID` (`parent_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `private_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `private_messages` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `from_user_ID` int(10) unsigned NOT NULL,
  `to_user_ID` int(10) unsigned NOT NULL,
  `body` text NOT NULL,
  `conversation_ID` int(11) DEFAULT NULL COMMENT 'keeps multiple replies collected together as a conversation thread',
  `is_read` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'bool',
  `anonymous` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reply_allowed` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `to_user_ID` (`to_user_ID`),
  KEY `from_user_ID` (`from_user_ID`),
  KEY `conversation_ID` (`conversation_ID`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `private_messages_ibfk_1` FOREIGN KEY (`from_user_ID`) REFERENCES `user_accounts` (`ID`),
  CONSTRAINT `private_messages_ibfk_2` FOREIGN KEY (`to_user_ID`) REFERENCES `user_accounts` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `remote_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `remote_addresses` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `remote_address` varchar(255) NOT NULL,
  `type` varchar(4) NOT NULL DEFAULT 'IP4',
  `host_name` varchar(255) DEFAULT NULL,
  `first_seen` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `TOR` smallint(5) unsigned NOT NULL DEFAULT '0',
  `proxy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `permission_to_view` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `permission_to_post` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `permission_to_search` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `permission_to_register` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `users_count` int(11) NOT NULL DEFAULT '0',
  `replies_count` int(11) NOT NULL DEFAULT '0',
  `topics_count` int(11) NOT NULL DEFAULT '0',
  `media_count` int(11) NOT NULL DEFAULT '0',
  `bulletins_count` int(10) unsigned NOT NULL DEFAULT '0',
  `search_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `IP_address` (`remote_address`),
  KEY `permission_to_view` (`permission_to_view`),
  KEY `users_count` (`users_count`),
  KEY `search_count` (`search_count`),
  KEY `last_seen` (`last_seen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replies` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic_ID` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `user_ID` int(10) unsigned NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  `tripcode` varchar(14) DEFAULT NULL,
  `media_ID` int(10) unsigned DEFAULT NULL,
  `body` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `mod_edited` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `partial_cache` text,
  `favorites_count` int(10) unsigned NOT NULL DEFAULT '0',
  `reports_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `topic_ID` (`topic_ID`),
  KEY `user_ID` (`user_ID`),
  KEY `media_ID` (`media_ID`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`topic_ID`) REFERENCES `topics` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `replies_ibfk_3` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `replies_ibfk_4` FOREIGN KEY (`media_ID`) REFERENCES `media` (`ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `replies_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replies_favorites` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_ID` int(10) unsigned NOT NULL,
  `reply_ID` int(10) unsigned NOT NULL,
  `topic_ID` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `reply_ID` (`reply_ID`),
  KEY `user_ID` (`user_ID`),
  KEY `topic_ID` (`topic_ID`),
  CONSTRAINT `replies_favorites_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`),
  CONSTRAINT `replies_favorites_ibfk_2` FOREIGN KEY (`reply_ID`) REFERENCES `replies` (`ID`),
  CONSTRAINT `replies_favorites_ibfk_3` FOREIGN KEY (`topic_ID`) REFERENCES `topics` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `replies_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replies_reports` (
  `created_at` datetime NOT NULL,
  `user_ID` int(10) unsigned NOT NULL,
  `reply_ID` int(10) unsigned NOT NULL,
  `reason` varchar(30) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'new',
  KEY `user_ID` (`user_ID`),
  KEY `reply_ID` (`reply_ID`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `replies_reports_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `replies_reports_ibfk_2` FOREIGN KEY (`reply_ID`) REFERENCES `replies` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topics` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_ID` int(10) unsigned NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  `tripcode` varchar(12) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `bumped_at` datetime NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'draft',
  `sticky` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `locked` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `mod_edited` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `safe_for_work` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `category_ID` int(10) unsigned NOT NULL DEFAULT '1',
  `tags` varchar(255) DEFAULT NULL,
  `media_ID` int(10) unsigned DEFAULT NULL,
  `replies_count` int(10) unsigned NOT NULL DEFAULT '0',
  `views_count` int(10) unsigned NOT NULL DEFAULT '0',
  `media_count` int(10) unsigned NOT NULL DEFAULT '0',
  `partial_cache` text,
  `favorites_count` int(10) unsigned NOT NULL DEFAULT '0',
  `replies_favorites_count` int(10) unsigned NOT NULL DEFAULT '0',
  `reports_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `media_ID` (`media_ID`),
  KEY `category_ID` (`category_ID`),
  KEY `user_ID` (`user_ID`),
  KEY `created_at` (`created_at`),
  KEY `bumped_at` (`bumped_at`),
  KEY `safe_for_work` (`safe_for_work`),
  KEY `tags` (`tags`),
  KEY `status` (`status`),
  CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `topics_ibfk_2` FOREIGN KEY (`category_ID`) REFERENCES `categories` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `topics_ibfk_3` FOREIGN KEY (`media_ID`) REFERENCES `media` (`ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topics_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topics_favorites` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `user_ID` int(10) unsigned NOT NULL,
  `topic_ID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `user_ID` (`user_ID`),
  KEY `topic_ID` (`topic_ID`),
  CONSTRAINT `topics_favorites_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `topics_favorites_ibfk_2` FOREIGN KEY (`topic_ID`) REFERENCES `topics` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topics_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topics_reports` (
  `created_at` datetime NOT NULL,
  `user_ID` int(10) unsigned NOT NULL,
  `topic_ID` int(10) unsigned NOT NULL,
  `reason` varchar(30) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'new',
  KEY `user_ID` (`user_ID`),
  KEY `topic_ID` (`topic_ID`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `topics_reports_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `topics_reports_ibfk_2` FOREIGN KEY (`topic_ID`) REFERENCES `topics` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topics_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topics_views` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_ID` int(10) unsigned NOT NULL,
  `topic_ID` int(10) unsigned NOT NULL,
  `last_seen` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `user_ID` (`user_ID`),
  KEY `topic_ID` (`topic_ID`),
  CONSTRAINT `topics_views_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `topics_views_ibfk_2` FOREIGN KEY (`topic_ID`) REFERENCES `topics` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_accounts` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL COMMENT 'stored as a salted SHA1',
  `temp_password` varchar(60) DEFAULT NULL,
  `remote_address` varchar(255) DEFAULT NULL,
  `email_address` varchar(125) DEFAULT NULL,
  `email_address_confirmed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0 = public; 1 = regular; 4 = moderator; 5 = admin',
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `session_ID` varchar(60) DEFAULT NULL,
  `internal_notes` text,
  `replies_count` int(10) unsigned NOT NULL DEFAULT '0',
  `topics_count` int(10) unsigned NOT NULL DEFAULT '0',
  `search_count` int(10) unsigned NOT NULL DEFAULT '0',
  `media_count` int(10) unsigned NOT NULL DEFAULT '0',
  `ban_expires` datetime DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL,
  `replies_favorites_count` int(10) unsigned NOT NULL DEFAULT '0',
  `topics_favorites_count` int(10) unsigned NOT NULL DEFAULT '0',
  `topics_favorited_count` int(10) unsigned NOT NULL DEFAULT '0',
  `replies_favorited_count` int(10) unsigned NOT NULL DEFAULT '0',
  `bulletins_count` int(10) unsigned NOT NULL DEFAULT '0',
  `reports_count` int(10) unsigned NOT NULL DEFAULT '0',
  `unread_private_messages_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`),
  KEY `password` (`password`),
  KEY `temp_password` (`temp_password`),
  KEY `session_ID` (`session_ID`),
  KEY `remote_address` (`remote_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_settings` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `user_ID` int(10) unsigned NOT NULL,
  `setting_ID` int(10) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `user_ID` (`user_ID`,`setting_ID`),
  CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user_accounts` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wordfilters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wordfilters` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `mode` varchar(30) NOT NULL DEFAULT 'match',
  `method` varchar(30) NOT NULL DEFAULT 'default',
  `pattern` varchar(255) NOT NULL,
  `category` varchar(30) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active` tinyint(3) unsigned NOT NULL,
  `action` varchar(30) NOT NULL,
  `replacement` varchar(255) DEFAULT NULL,
  `user_message` varchar(255) NOT NULL,
  `user_level_exempt` tinyint(3) unsigned NOT NULL DEFAULT '5',
  PRIMARY KEY (`ID`),
  KEY `pattern` (`pattern`,`active`),
  KEY `user_level_exempt` (`user_level_exempt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `name` varchar(60) NOT NULL,
  `value` varchar(255) NOT NULL,
  `category` varchar(60) DEFAULT NULL,
  `default` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'text',
  `option_values` text,
  `option_labels` text,
  `editable` smallint(1) NOT NULL DEFAULT '1',
  `load_at_startup` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `order_by` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`),
  KEY `category` (`category`),
  KEY `load_at_startup` (`load_at_startup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='0.3.1';
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `settings` VALUES (1,'2010-04-27 16:03:04','2010-12-19 15:14:24','CACHE_LEVEL','1','CACHE','0','stores HTML output to improve load times and reduce server strain','integer','0\r\n1\r\n2','Off\r\nStandard\r\nMaximum',0,1,0),(2,'2010-04-27 16:03:55','2010-12-19 20:56:51','DEBUG_LEVEL','0','DEVELOPMENT','0','reports internal variables and settings','integer','0\r\n1\r\n2\r\n3\r\n4\r\n5','Off\r\nRouting\r\nPerformance\r\nOptions\r\nApplication\r\nServer',0,1,0),(3,'2010-04-27 16:04:43','2010-05-02 15:20:37','TRACK_REMOTE_ADDRESSES','1','MODERATION','0','assists moderation, but reduces the perception of user privacy','boolean','0\r\n1','yes\r\nno',0,1,10),(7,'2010-04-27 20:07:54','2010-04-30 18:49:38','SESSION_LIFETIME','31556926','SECURITY','31556926','length (in seconds) that a session login remains valid','integer',NULL,NULL,0,1,11),(8,'2010-04-27 20:08:45','2010-05-13 23:27:22','SESSION_PATH','/bbsoop/','SECURITY','/board/','restrict the session cookie to this URI within the domain','text',NULL,NULL,0,1,3),(11,'2010-04-28 23:11:32','2010-07-07 12:08:54','MAINTENANCE_MODE','0','CORE','0','disables public posting and viewing; useful during maintenance or upgrades','boolean','0\r\n1','Off\r\nOn',0,1,0),(13,'2010-04-28 23:45:28','2010-06-14 00:48:41','USE_IMAGEMAGICK','0','MEDIA','0','if available, image processing will use this more powerful library','boolean','0\r\n1','Off\r\nOn',0,0,100),(14,'2010-04-29 00:32:42','2010-05-02 05:23:19','THUMBNAILS_CROP_TO_FIT','0','MEDIA','0','Forces thumnails to defined dimensions','boolean','0\r\n1','Off\r\nOn',0,0,12),(15,'2010-04-29 01:23:52','2010-05-02 04:42:14','THUMBNAIL_HEIGHT','160','MEDIA','160','pixel dimensions','integer',NULL,NULL,0,0,10),(16,'2010-04-29 01:26:14','2010-05-02 04:42:14','THUMBNAIL_WIDTH','160','MEDIA','240','pixel dimensions','integer',NULL,NULL,0,0,11),(17,'2010-04-29 02:03:26','2010-05-07 13:53:15','ACCEPT_MIME_TYPES','image/jpeg, image/gif, image/png','MEDIA','image/jpeg, image/gif, image/png','tells the browser what files it can upload and tells the system which files to accept','text',NULL,NULL,0,0,5),(18,'2010-04-29 02:07:48','2010-05-02 22:33:27','REMOVE_EXIF_AND_PROFILE','1','MEDIA','1','EXIF data can include camera model and image date','boolean','0\r\n1','Off\r\nOn',0,0,90),(19,'2010-04-29 02:13:11','2010-05-03 11:03:36','JPEG_THUMBNAIL_QUALITY','60','MEDIA','60','affects disk space and bandwidth','integer','100\r\n90\r\n80\r\n70\r\n60\r\n50\r\n40\r\n30','100%\r\n90%\r\n80%\r\n70%\r\n60%\r\n50%\r\n40%\r\n30%',0,0,15),(20,'2010-04-29 02:19:46','2010-05-02 05:15:04','UPLOAD_MAXIMUM_FILE_SIZE','2097152','MEDIA','3145728','tells the browser what the maximum size is; use .htaccess to set the actual limit','integer','524288\r\n1048576\r\n1572864\r\n2097152\r\n2621440\r\n3145728\r\n3670016\r\n4194304','0.5 MB\r\n1.0 MB\r\n1.5 MB\r\n2.0 MB\r\n2.5 MB\r\n3.0 MB\r\n3.5 MB\r\n4.0 MB',0,0,1),(21,'2010-04-29 02:22:16','2010-05-02 00:39:51','UPLOAD_MAXIMUM_HEIGHT','3250','MEDIA','3250','Affects memory use, processing time','integer',NULL,NULL,0,0,2),(23,'2010-04-29 02:34:20','2010-04-30 19:29:50','PHOTO_AUTO_ROTATE','0','MEDIA','0','uses EXIF data, if available, to correct image orientation','boolean','0\r\n1','Off\r\nOn',1,0,91),(24,'2010-04-29 09:57:09','2010-05-02 00:39:51','UPLOAD_MAXIMUM_WIDTH','3250','MEDIA','3250','affects memory use and processing time','integer',NULL,NULL,0,0,3),(25,'2010-04-29 12:44:18','2010-04-30 19:29:50','GIF_THUMBNAIL_MAXIMUM_COLORS','64','MEDIA','256','this can reduce file size but decrease quality','integer','256\r\n128\r\n64\r\n32\r\n16','256\r\n128\r\n64\r\n32\r\n16',1,0,16),(26,'2010-04-29 13:06:49','2010-05-08 18:40:51','JPEG_LARGE_QUALITY','60','MEDIA','70','compresses large version of upload','integer','0\r\n100\r\n90\r\n80\r\n70\r\n60\r\n50\r\n40\r\n30','Original\r\n100%\r\n90%\r\n80%\r\n70%\r\n60%\r\n50%\r\n40%\r\n30%',1,0,30),(27,'2010-04-29 13:15:44','2010-04-30 20:13:32','PNG_THUMBNAIL_COMPRESSION','60','MEDIA','60','higher compression is slower, but reduces file size','integer','0\r\n10\r\n20\r\n30\r\n40\r\n50\r\n60\r\n70\r\n80\r\n90\r\n100','0%\r\n10%\r\n20%\r\n30%\r\n40%\r\n50%\r\n60%\r\n70%\r\n80%\r\n90%\r\n100%',1,0,17),(28,'2010-04-29 13:16:44','2010-04-30 19:31:42','PNG_CONVERT_TO_JPEG','0','MEDIA','0','reduces file size, but uses a lossy format','boolean','Off\r\nOn','0\r\n1',1,0,40),(29,'2010-04-29 13:19:05','2010-12-19 15:14:24','UPLOADS_ALLOWED','0','MEDIA','0','disabling this makes it a text-only board','boolean','0\r\n1','Off\r\nOn',0,0,0),(30,'2010-04-29 13:20:35','2010-05-02 05:10:27','RESIZE_ORIGINAL_IMAGE','1','MEDIA','0','reduces file dimensions to save disk space and bandwidth','boolean','0\r\n1','Off\r\nOn',1,0,20),(31,'2010-04-29 13:22:29','2010-05-02 23:59:32','ORIGINAL_IMAGE_RESIZE_HEIGHT','1200','MEDIA','1024','reduces file dimensions if RESIZE_ORIGINAL_IMAGE is on','integer','','',1,0,21),(32,'2010-04-29 13:42:28','2010-04-30 19:31:42','PNG_LARGE_COMPRESSION','0','MEDIA','0','higher compression is slower, but reduces file size','integer','0\r\n10\r\n20\r\n30\r\n40\r\n50\r\n60\r\n70\r\n80\r\n90\r\n100','0%\r\n10%\r\n20%\r\n30%\r\n40%\r\n50%\r\n60%\r\n70%\r\n80%\r\n90%\r\n100%',1,0,41),(33,'2010-04-29 13:47:35','2010-08-29 15:05:16','SETTINGS_SORTABLE','0','SETTINGS','0','allows manual input of setting order','boolean','0\r\n1','Off\r\nOn',0,0,0),(34,'2010-04-29 14:26:13','2010-05-02 23:59:32','ORIGINAL_IMAGE_RESIZE_WIDTH','1600','MEDIA','1024','reduces file dimensions if RESIZE_ORIGINAL_IMAGE is on','integer','','',1,0,22),(35,'2010-04-29 14:36:51','2010-05-02 06:17:37','CONVERT_IMAGES_GRAYSCALE','0','MEDIA','0','makes all uploads black and white','boolean','0\r\n1','Off\r\nOn',1,0,80),(37,'2010-04-29 15:20:39','2010-05-01 19:54:48','NEW_TOPICS_ALLOWED','1','POSTING','1','','boolean','0\r\n1','Off\r\nOn',0,1,1),(38,'2010-04-29 15:21:16','2010-05-01 19:54:48','NEW_REPLIES_ALLOWED','1','POSTING','1','','boolean','0\r\n1','On\r\nOff',0,1,0),(39,'2010-04-29 15:32:15','2010-12-19 15:14:24','WORDFILTER_TOPICS','0','POSTING','0','applies the wordfilter to new topics','boolean','Off\r\nOn','0\r\n1',0,0,0),(40,'2010-04-29 16:24:51','2010-12-19 15:14:24','WORDFILTER_REPLIES','0','POSTING','0','applies the wordfilter to new replies','boolean','0\r\n1','Off\r\nOn',0,0,0),(41,'2010-04-29 17:25:18','2010-12-19 15:14:24','TOPIC_TITLE_CHARACTERS_MAXIMUM','100','POSTING','255','maximum length, in characters (0 to 255)','integer','','',0,0,0),(43,'2010-04-29 17:37:28','2010-12-19 15:14:24','TOPIC_BODY_CHARACTERS_MAXIMUM','65535','POSTING','65535','maximum length, in characters (0 to 65535)','integer','','',0,0,0),(44,'2010-04-29 17:41:02','2010-04-30 18:49:38','TOPIC_TITLE_CHARACTERS_MINIMUM','1','POSTING','0','minimum length, in characters (0 to 255)','integer','','',0,0,20),(45,'2010-04-29 17:45:50','2010-04-30 18:49:38','TOPIC_BODY_CHARACTERS_MINIMUM','1','POSTING','0','minimum length, in characters (0 to 65535)','integer','','',0,0,22),(46,'2010-04-29 17:59:40','2010-05-01 13:57:23','REMOVE_UNICODE','0','POSTING','0','only allows characters within ASCII','boolean','','',0,0,40),(47,'2010-04-29 18:06:06','2010-04-30 18:49:38','REPLY_MINIMUM_COMPOSITION_TIME','5','POSTING','0','requires that replies take time to write; minor barrier to lazy bot-writers','integer','','',0,0,30),(48,'2010-04-30 01:02:39','2010-04-30 19:32:34','NONCE_LIFETIME','1800','SECURITY','1800','length of time, in seconds, that a security token is valid','integer','','',0,1,10),(49,'2010-04-30 01:22:40','2010-05-03 11:18:45','APP_CHARSET','UTF-8','CORE','UTF-8','UTF-8 strongly recommended','text','UTF-8\r\niso-8859-1','UTF-8\r\niso-8859-1',0,1,10),(51,'2010-04-30 01:48:21','2010-04-30 19:26:35','GIF_ALLOW_TRANSPARENCY','1','MEDIA','1','','boolean','','',1,0,60),(52,'2010-04-30 01:49:27','2010-04-30 19:26:35','PNG_ALLOW_TRANSPARENCY','1','MEDIA','1','','boolean','','',1,0,61),(53,'2010-04-30 02:09:06','2010-05-04 20:25:59','VALIDATE_REMOTE_ADDRESS','1','SECURITY','1','matches IP address while confirming sessions; logins expire if addresses change (may be obnoxious on mobile devices and dynamic ISPs)','boolean','','',0,1,0),(54,'2010-04-30 02:12:57','2010-05-04 19:56:34','VALIDATE_USER_AGENT','1','SECURITY','1','Matches browser user-agents while confirming sessions; logins expire if user-agents change','boolean','','',0,1,1),(55,'2010-04-30 02:20:15','2010-12-19 15:14:24','MODERATORS_FORCE_HTTPS','0','SECURITY','0','increases security to protect connections with privileged users; slightly slower','boolean','','',1,0,0),(56,'2010-04-30 02:32:57','2010-04-30 19:26:35','BACKGROUND_FILL_COLOR','ffffff','MEDIA','ffffff','when transparency is disabled, image backgrounds are filled with this (hexidecimal) color','text','','',1,0,65),(57,'2010-04-30 02:36:48','2010-04-30 19:31:42','GIF_ALLOW_ANIMATION','1','MEDIA','1','','boolean','','',1,0,35),(58,'2010-04-30 12:29:49','2010-05-02 16:00:59','LOAD_ALL_SETTINGS_AT_STARTUP','0','SETTINGS','0','convenient, but may require more memory','boolean','','',0,0,0),(59,'2010-04-30 23:05:21',NULL,'SESSION_DOMAIN','authorizedclone.com','SECURITY','authorizedclone.com','i.e. “example.com” or “www.example.com” or “.example.com”','text','','',0,0,2),(61,'2010-05-01 11:59:13','2010-05-02 22:24:03','REPLY_BODY_CHARACTERS_MINIMUM','1','POSTING','0','minimum length, in characters (0 to 65535)','integer','','',0,0,25),(62,'2010-05-01 12:00:54','2010-12-19 15:14:24','REPLY_BODY_CHARACTERS_MAXIMUM','65535','POSTING','65535','maximum length, in characters (0 to 65535)','integer','','',0,0,0),(64,'2010-05-02 00:24:17','2010-05-07 13:53:15','ACCEPT_FILE_EXTENSIONS','jpg, jpeg, gif, png','MEDIA','jpg, gif, png','tells the user what file extensions they can upload (not used to verify image types)','text','','',0,0,4),(65,'2010-05-02 15:19:48','2010-05-02 16:06:54','TOPICS_GO_LIVE_WHEN_POSTED','1','MODERATION','1','turning this off will hold new topics in the moderation queue','boolean','','',0,0,0),(66,'2010-05-02 15:20:29','2010-05-02 16:06:54','REPLIES_GO_LIVE_WHEN_POSTED','1','MODERATION','1','turning this off will hold new replies in the moderation queue','boolean','','',0,0,1),(67,'2010-05-02 15:22:48','2010-05-02 16:07:43','MEDIA_GOES_LIVE_WHEN_POSTED','1','MODERATION','1','turning this off will hold new images in the moderation queue','boolean','','',0,0,3),(68,'2010-05-02 22:23:47',NULL,'UPLOAD_OVERRIDES_REPLY_CHARACTERS_MINIMUM','1','POSTING','1','ignores the minimum reply length if an image is uploaded','boolean','','',0,0,27),(69,'2010-05-02 22:24:55','2010-05-02 22:25:10','UPLOAD_OVERRIDES_TOPIC_CHARACTERS_MINIMUM','1','POSTING','1','ignores the minimum topic length if an image is uploaded','boolean','','',0,0,24),(70,'2010-05-02 23:22:30','2010-05-02 23:41:58','DUPLICATE_IMAGES','1','MEDIA','1','smart will detect the duplicate and allow it, but reference the original file (saves disk space and processing overhead)','integer','0\r\n1\r\n2','Allow\r\nSmart\r\nReject',1,0,6),(71,'2010-05-03 18:36:42','2010-05-07 13:31:25','MAINTENANCE_MESSAGE','Down for maintenance—back soon.','CORE','Down for maintenance‚Äîback soon.','Displays this message to the end user when maintenance mode is on','text','','',1,0,1),(72,'2010-05-04 19:44:30',NULL,'PROXIES_CAN_POST','1','POSTING','1','when disabled, users with common proxy signatures will not be able to post','boolean','','',0,0,50),(73,'2010-05-04 19:52:57',NULL,'TOR_CAN_POST','1','POSTING','1','when disabled, known TOR exit nodes will not be able to post','boolean','','',0,0,51),(74,'2010-05-05 11:12:44','2010-12-19 15:14:24','REPLY_TIME_BETWEEN_EACH','0','POSTING','30','time (in seconds) that must pass before a user can post consecutive replies','integer','','',0,0,0),(75,'2010-05-05 11:13:29',NULL,'TOPIC_MINIMUM_COMPOSITION_TIME','20','POSTING','20','requires that topics take time to write; minor barrier to lazy bot-writers','integer','','',0,0,32),(76,'2010-05-05 11:14:13',NULL,'TOPIC_TIME_BETWEEN_EACH','120','POSTING','120','time (in seconds) that must pass before a user can post consecutive topics','integer','','',0,0,33),(77,'2010-05-05 11:39:07','2010-12-19 15:14:24','USER_LEVEL_EXEMPT_FROM_POST_TIMING_LIMITS','0','POSTING','0','this level—and those above it—will not be limited by wordfilters and post timing','integer','-1\r\n5\r\n4\r\n1\r\n0','None\r\nAdmin\r\nModerator\r\nRegular\r\nAll',0,0,0),(78,'2010-05-05 17:09:00','2010-05-07 13:52:58','USE_CATEGORIES','1','CATEGORIES','0','categories allow you to split up the main board into sub-boards','boolean','','',1,0,0),(79,'2010-05-05 17:12:00','2010-05-07 13:52:55','USER_LEVEL_ALLOW_CATEGORY_CREATION','5','CATEGORIES','5','this level—and those above it—will be able to create new categories','integer','5\r\n4\r\n1\r\n0\r\n-1','Admin\r\nModerator\r\nRegular\r\nPublic\r\nNone',1,0,10),(80,'2010-05-07 13:38:37','2010-06-05 14:42:38','TRIPCODES','1','POSTING','0','','boolean','','',0,0,8),(81,'2010-05-07 13:53:41',NULL,'ALLOW_ADULT_CONTENT','0','MEDIA','0','','boolean','','',1,0,100),(82,'2010-05-08 15:43:09','2010-12-19 15:14:24','TOPIC_MEDIA_COUNT_LIMIT','0','MEDIA','100','limits the number of media that can be uploaded to one topic; affects bandwidth','integer','','',1,0,0),(83,'2010-05-13 23:28:35','2010-12-19 15:14:24','USER_LEVEL_VALIDATE_SESSION','5','SECURITY','4','this level—and those above it—will have more secure sessions','integer','5\r\n4\r\n1\r\n0','Admin\r\nModerator\r\nRegular\r\nAll',0,1,0),(84,'2010-05-15 00:31:19','2010-05-28 13:13:33','NEW_BULLETINS_ALLOWED','1','BULLETINS','1','','boolean','','',1,0,0),(85,'2010-05-15 00:31:41','2010-05-28 13:13:33','BULLETINS_GO_LIVE_WHEN_POSTED','0','BULLETINS','0','','boolean','','',1,0,0),(86,'2010-05-15 00:33:54',NULL,'BULLETIN_TIME_BETWEEN_EACH','300','BULLETINS','300','time (in seconds) that must pass before a user can post consecutive bulletins','integer','','',1,0,11),(87,'2010-05-15 00:35:06',NULL,'USER_LEVEL_EXEMPT_FROM_BULLETIN_POST_TIMING_LIMITS','4','BULLETINS','4','','integer','-1\r\n5\r\n4\r\n1\r\n0','None\r\nAdmin\r\nModerator\r\nRegular\r\nAll',1,0,10),(88,'2010-05-15 22:09:59','2010-12-19 15:14:24','FAVORITES_ON','0','MODERATION','1','','boolean','','',0,0,0),(89,'2010-05-15 22:10:43','2010-12-19 15:14:24','REPORTING_ON','0','MODERATION','1','','boolean','','',0,0,0),(90,'2010-05-16 03:23:33','2010-07-10 16:04:23','ALLOW_APACHE_BENCHMARK','0','DEVELOPMENT','0','requests from apache benchmark can be denied by turning this off','boolean','','',0,1,0),(91,'2010-05-16 14:24:07',NULL,'BULLETIN_BODY_CHARACTERS_MINIMUM','3','BULLETINS','3','','integer','','',0,0,20),(92,'2010-05-16 14:24:30',NULL,'BULLETIN_BODY_CHARACTERS_MAXIMUM','500','BULLETINS','500','','integer','','',0,0,21),(93,'2010-05-16 19:30:53','2010-07-03 23:10:26','TIME_TO_EDIT_REPLIES','600','MODERATION','300','time (in seconds) that reply authors have to edit their posts','integer','','',0,0,63),(94,'2010-05-16 19:31:22','2010-07-03 23:10:26','TIME_TO_EDIT_TOPICS','600','MODERATION','300','time (in seconds) that topic authors have to edit their posts','integer','','',0,0,62),(95,'2010-05-16 19:43:02',NULL,'USER_LEVEL_EXEMPT_FROM_EDIT_TIMING_LIMITS','4','MODERATION','4','this level—and those above it—will always be able to edit posts','integer','5\r\n4\r\n1\r\n0','Admin\r\nModerator\r\nRegular\r\nAll',0,0,61),(96,'2010-05-16 21:40:38','2010-05-16 21:42:58','DATE_FORMAT','j F Y','CORE','j F Y','','text','','',0,1,0),(97,'2010-05-23 19:28:54','2010-08-30 02:22:01','SEND_EMAILS','1','SECURITY','1','application security can be increased by disabling email functions','boolean','','',1,0,0),(98,'2010-05-23 21:56:47',NULL,'ALLOW_TOPIC_EDITING','1','MODERATION','1','','boolean','','',1,0,30),(99,'2010-05-23 22:05:27',NULL,'ALLOW_REPLY_EDITING','1','MODERATION','1','','boolean','','',1,0,31),(100,'2010-05-26 18:45:15',NULL,'LOG_ACTIONS','1','CORE','1','','boolean','','',0,1,50),(102,'2010-05-30 15:16:57','2010-12-19 15:14:24','AUTOMATICALLY_REGISTER_ACCOUNTS','1','USER_ACCOUNTS','1','creates new user accounts automatically (no registration required)','boolean','','',1,1,0),(103,'2010-06-05 19:43:06','2010-12-19 15:14:24','MINIMUM_ACCOUNT_AGE_NEW_TOPIC','0','POSTING','180','Minimum account age (in seconds) that must be attained before the account can post new topics','integer',NULL,NULL,0,0,0),(104,'2010-06-05 20:18:21','2010-12-19 15:14:24','MINIMUM_ACCOUNT_AGE_NEW_REPLY','0','POSTING','45','Minimum account age (in seconds) that must be attained before the account can post new replies','integer',NULL,NULL,0,0,0),(105,'2010-06-05 20:18:51','2010-12-19 15:14:24','MINIMUM_ACCOUNT_AGE_NEW_MEDIA','0','POSTING','180','Minimum account age (in seconds) that must be attained before the account can post new media','integer',NULL,NULL,0,0,0),(106,'2010-06-05 20:34:52',NULL,'MINIMUM_ACCOUNT_AGE_NEW_BULLETIN','600','BULLETINS','600','Minimum account age (in seconds) that must be attained before the account can post new bulletins','integer',NULL,NULL,0,0,4),(107,'2010-06-15 23:11:01',NULL,'TOPICS_PER_PAGE','100','USER_SETTINGS','100','the number of topics displayed on each page','integer','20\r\n40\r\n60\r\n80\r\n100\r\n150\r\n200','20\r\n40\r\n60\r\n80\r\n100 (default)\r\n150\r\n200',1,0,10),(108,'2010-06-15 23:13:44',NULL,'IMAGES_CLICK_TO_EXPAND','0','USER_SETTINGS','0','clicking images will not load a new image, but will expand the image and replace with the large version','boolean','','',1,0,20),(109,'2010-06-15 23:17:40',NULL,'NSFW_FILTER','0','USER_SETTINGS','0','when on, topics flagged as NSFW will be hidden','boolean','','',1,0,5),(110,'2010-06-15 23:39:35','2010-06-18 19:58:57','TOPICS_SORT','bump','USER_SETTINGS','bumps','','text','bump\r\ncreated_at','Bumps\r\nCreated at',1,0,16),(111,'2010-07-03 18:00:17',NULL,'DETECT_MOBILE_DEVICES','1','CORE','1','checks user-agents for mobile devices with smaller screens, touch inputs, and/or limited bandwidth','boolean','','',1,1,0),(112,'2010-07-04 13:57:47','2010-07-04 14:04:35','LIMIT_TITLE_UNICODE','1','POSTING','0','removes many zero-width (“ZALGO”) or obnoxious characters','boolean','','',1,0,41),(113,'2010-07-05 22:38:32',NULL,'LIMIT_NAME_UNICODE','1','POSTING','0','removes many zero-width (“ZALGO”) or obnoxious characters','boolean','','',1,0,9);
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `categories` VALUES (NULL,'Default',NOW(),NULL,0,'Default board.',0,0,0);
