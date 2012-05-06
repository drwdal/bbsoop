<?php
/* ==================================================

IMGBOARD Copyright 2008–2010 Authorized Clone LLC.

http://authorizedclone.com/
authorizedclone@gmail.com

This file is part of IMGBOARD.

IMGBOARD is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

IMGBOARD is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with IMGBOARD.  If not, see <http://www.gnu.org/licenses/>.

================================================== */
if ( ! defined ( 'IMGBOARD_INIT' ) ) { header ( 'Status: 403', TRUE, 403 ); die( ); }

/* BUILD QUERIES
================================================== */
global $DB;

$migrations = Array ( );
$migrations[0] = Array ( );
$migrations[0][4] = Array ( );
$migrations[0][4][0] = Array ( );

$migrations[0][4][0]['settings'] = array ( );
$migrations[0][4][0]['settings'][] = "INSERT INTO `settings` (`ID`, `created_at`, `updated_at`, `name`, `value`, `category`, `default`, `description`, `type`, `option_values`, `option_labels`, `editable`, `load_at_startup`, `order_by` ) VALUES (NULL, UTC_TIMESTAMP( ), NULL, UPPER('MINIMUM_ACCOUNT_AGE_NEW_TOPIC'), '180', 'POSTING', '180', 'Minimum account age (in seconds) that must be attained before the account can post new topics', 'integer', NULL, NULL, '0', '0', '2')";
$migrations[0][4][0]['settings'][] = "INSERT INTO `settings` (`ID`, `created_at`, `updated_at`, `name`, `value`, `category`, `default`, `description`, `type`, `option_values`, `option_labels`, `editable`, `load_at_startup`, `order_by` ) VALUES (NULL, UTC_TIMESTAMP( ), NULL, UPPER('MINIMUM_ACCOUNT_AGE_NEW_REPLY'), '45', 'POSTING', '45', 'Minimum account age (in seconds) that must be attained before the account can post new replies', 'integer', NULL, NULL, '0', '0', '3')";
$migrations[0][4][0]['settings'][] = "INSERT INTO `settings` (`ID`, `created_at`, `updated_at`, `name`, `value`, `category`, `default`, `description`, `type`, `option_values`, `option_labels`, `editable`, `load_at_startup`, `order_by` ) VALUES (NULL, UTC_TIMESTAMP( ), NULL, UPPER('MINIMUM_ACCOUNT_AGE_NEW_MEDIA'), '180', 'POSTING', '180', 'Minimum account age (in seconds) that must be attained before the account can post new media', 'integer', NULL, NULL, '0', '0', '4')";
$migrations[0][4][0]['settings'][] = "INSERT INTO `settings` (`ID`, `created_at`, `updated_at`, `name`, `value`, `category`, `default`, `description`, `type`, `option_values`, `option_labels`, `editable`, `load_at_startup`, `order_by` ) VALUES (NULL, UTC_TIMESTAMP( ), NULL, UPPER('MINIMUM_ACCOUNT_AGE_NEW_BULLETIN'), '600', 'BULLETINS', '600', 'Minimum account age (in seconds) that must be attained before the account can post new bulletins', 'integer', NULL, NULL, '0', '0', '4')";
$migrations[0][4][0]['settings'][] = "ALTER TABLE `wordfilters`  COMMENT =  '0.4.0'";

$migrations[0][4][0]['wordfilters'] = array ( );
$migrations[0][4][0]['wordfilters'][] = "ALTER TABLE `wordfilters` CHANGE `mode` `mode` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'match'";
$migrations[0][4][0]['wordfilters'][] = "ALTER TABLE `wordfilters` ADD `method` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'default'";
$migrations[0][4][0]['wordfilters'][] = "ALTER TABLE `wordfilters` ADD `user_level_exempt` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '5'";
$migrations[0][4][0]['wordfilters'][] = "ALTER TABLE `wordfilters`  COMMENT =  '0.4.0'";

$migrations[0][4][0]['topics'] = array ( );
$migrations[0][4][0]['topics'][] = "ALTER TABLE `topics` ADD INDEX `status` ( `status` )";
$migrations[0][4][0]['topics'][] = "ALTER TABLE `topics`  COMMENT =  '0.4.0'";

$migrations[0][4][0]['replies'] = array ( );
$migrations[0][4][0]['replies'][] = "ALTER TABLE `replies` ADD INDEX `status` ( `status` );";
$migrations[0][4][0]['replies'][] = "ALTER TABLE `replies`  COMMENT =  '0.4.0'";

$migrations[0][4][0]['media'] = array ( );
$migrations[0][4][0]['media'][] = "ALTER TABLE `media` ADD INDEX `status` ( `status` )";
$migrations[0][4][0]['media'][] = "ALTER TABLE `media` DROP INDEX `fingerprint`, ADD INDEX `fingerprint` ( `signature` )";
$migrations[0][4][0]['media'][] = "ALTER TABLE `media` CHANGE `signature` `signature` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$migrations[0][4][0]['media'][] = "ALTER TABLE `media`  COMMENT =  '0.4.0'";

$migrations[0][4][0]['private_messages'] = array ( );
$migrations[0][4][0]['private_messages'][] = "ALTER TABLE `private_messages` CHANGE `read` `is_read` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'bool'";
$migrations[0][4][0]['private_messages'][] = "ALTER TABLE `private_messages` ADD `reply_allowed` TINYINT UNSIGNED NOT NULL DEFAULT '1'";
$migrations[0][4][0]['private_messages'][] = "ALTER TABLE `private_messages`  COMMENT =  '0.4.0'";

$migrations[0][4][0]['replies_reports'] = array ( );
$migrations[0][4][0]['replies_reports'][] = "ALTER TABLE `replies_reports` ADD INDEX `created_at` ( `created_at` )";
$migrations[0][4][0]['replies_reports'][] = "ALTER TABLE `replies_reports`  COMMENT =  '0.4.0'";

$migrations[0][4][0]['topics_reports'] = array ( );
$migrations[0][4][0]['topics_reports'][] = "ALTER TABLE `topics_reports` ADD INDEX `created_at` ( `created_at` )";
$migrations[0][4][0]['topics_reports'][] = "ALTER TABLE `topics_reports`  COMMENT =  '0.4.0'";

$migrations[0][4][0]['user_accounts'] = array ( );
$migrations[0][4][0]['user_accounts'][] = "ALTER TABLE `user_accounts` ADD `unread_private_messages_count` INT UNSIGNED NOT NULL DEFAULT '0'";
$migrations[0][4][0]['user_accounts'][] = "ALTER TABLE `user_accounts` ADD `remote_address` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `temp_password`";
$migrations[0][4][0]['user_accounts'][] = "ALTER TABLE `user_accounts` ADD INDEX ( `remote_address` )";
$migrations[0][4][0]['user_accounts'][] = "ALTER TABLE `user_accounts`  COMMENT =  '0.4.0'";

$migrations[0][4][0]['user_settings'] = array ( );
$migrations[0][4][0]['user_settings'][] = "CREATE TABLE `user_settings` ( `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `created_at` DATETIME NOT NULL, `user_ID` INT UNSIGNED NOT NULL, `setting_ID` INT UNSIGNED NOT NULL, `value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, INDEX ( `user_ID` , `setting_ID` ) ) ENGINE = InnoDB COMMENT='0.4.0'";

/* CHECK DATABASE VERSION FOR EACH TABLE (mark/remember any table of lesser version than current)
================================================== */


/* APPLY EACH MIGRATION TO THE TABLES THAT NEED UPGRADING
================================================== */



// TODO: add SQL to clear partial_cache columns in replies and topics
?>