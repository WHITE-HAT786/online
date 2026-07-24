-- =====================================================================
--  WebDialer — MySQL schema + seed data
--  Import via phpMyAdmin or:  mysql -u root -p < database.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `webdialer`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `webdialer`;

-- ============ USERS ============
DROP TABLE IF EXISTS `pkg_user`;
CREATE TABLE `pkg_user` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name`     VARCHAR(120) NOT NULL,
  `username`      VARCHAR(60)  NOT NULL,
  `email`         VARCHAR(150) NOT NULL,
  `phone`         VARCHAR(30)  DEFAULT NULL,
  `password`      VARCHAR(255) NOT NULL,          -- stored plain per user request
  `avatar`        VARCHAR(255) DEFAULT NULL,
  `timezone`      VARCHAR(80)  DEFAULT '(UTC-05:00) Eastern Time (US & Canada)',
  `language`      VARCHAR(30)  DEFAULT 'English (US)',
  `status`        ENUM('active','disabled') DEFAULT 'active',
  `remember_token` VARCHAR(120) DEFAULT NULL,
  `reset_token`   VARCHAR(120) DEFAULT NULL,
  `reset_expires` DATETIME DEFAULT NULL,
  `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ USER SETTINGS ============
DROP TABLE IF EXISTS `pkg_setting`;
CREATE TABLE `pkg_setting` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`               INT UNSIGNED NOT NULL,
  `default_caller_id`     VARCHAR(120) DEFAULT NULL,
  `default_sip_id`        INT UNSIGNED DEFAULT NULL,
  `call_recording`        TINYINT(1) DEFAULT 1,
  `auto_answer`           TINYINT(1) DEFAULT 0,
  `dial_pad_sound`        TINYINT(1) DEFAULT 1,
  `call_end_sound`        TINYINT(1) DEFAULT 0,
  `theme`                 ENUM('light','dark','system') DEFAULT 'light',
  `primary_color`         VARCHAR(10) DEFAULT '#1a56ff',
  `data_retention`        VARCHAR(20) DEFAULT '6 Months',
  `date_format`           VARCHAR(30) DEFAULT 'MM DD, YYYY',
  `two_factor_enabled`    TINYINT(1) DEFAULT 0,
  `created_at`            DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user` (`user_id`),
  CONSTRAINT `fk_setting_user` FOREIGN KEY (`user_id`) REFERENCES `pkg_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ SUBSCRIPTION ============
DROP TABLE IF EXISTS `pkg_subscription`;
CREATE TABLE `pkg_subscription` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        INT UNSIGNED NOT NULL,
  `plan_name`      VARCHAR(50) NOT NULL DEFAULT 'Basic',
  `price`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `billing_cycle`  ENUM('monthly','yearly') DEFAULT 'monthly',
  `status`         ENUM('active','cancelled','expired') DEFAULT 'active',
  `next_billing`   DATE DEFAULT NULL,
  `card_brand`     VARCHAR(20) DEFAULT NULL,
  `card_last4`     VARCHAR(4)  DEFAULT NULL,
  `card_expires`   VARCHAR(10) DEFAULT NULL,
  `sip_limit`      INT DEFAULT 1,
  `user_limit`     INT DEFAULT 1,
  `minute_limit`   INT DEFAULT 1000,
  `recording_gb`   INT DEFAULT 5,
  `created_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user` (`user_id`),
  CONSTRAINT `fk_sub_user` FOREIGN KEY (`user_id`) REFERENCES `pkg_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ LOGIN LOG ============
DROP TABLE IF EXISTS `pkg_login_log`;
CREATE TABLE `pkg_login_log` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED DEFAULT NULL,
  `username`   VARCHAR(60) DEFAULT NULL,
  `ip`         VARCHAR(60) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `status`     ENUM('success','failed') DEFAULT 'success',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ NOTIFICATIONS ============
DROP TABLE IF EXISTS `pkg_notification`;
CREATE TABLE `pkg_notification` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`   INT UNSIGNED NOT NULL,
  `type`      VARCHAR(50) DEFAULT 'info',
  `title`     VARCHAR(180) NOT NULL,
  `message`   TEXT DEFAULT NULL,
  `is_read`   TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `pkg_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ SIP ACCOUNTS ============
DROP TABLE IF EXISTS `pkg_sip`;
CREATE TABLE `pkg_sip` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        INT UNSIGNED NOT NULL,
  `account_name`   VARCHAR(120) NOT NULL,
  `subtitle`       VARCHAR(160) DEFAULT NULL,
  `sip_username`   VARCHAR(120) NOT NULL,
  `sip_password`   VARCHAR(255) NOT NULL,
  `sip_server`     VARCHAR(160) NOT NULL,
  `sip_port`       INT DEFAULT 5060,
  `transport`      ENUM('UDP','TCP','TLS') DEFAULT 'UDP',
  `caller_id`      VARCHAR(60)  DEFAULT NULL,
  `is_default`     TINYINT(1) DEFAULT 0,
  `status`         ENUM('registered','offline','disabled') DEFAULT 'offline',
  `last_registered` DATETIME DEFAULT NULL,
  `icon_color`     VARCHAR(20) DEFAULT 'green',
  `icon`           VARCHAR(60) DEFAULT 'fa-circle-nodes',
  `created_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_sip_user` FOREIGN KEY (`user_id`) REFERENCES `pkg_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ CONTACTS ============
DROP TABLE IF EXISTS `pkg_contact`;
CREATE TABLE `pkg_contact` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `first_name` VARCHAR(80) NOT NULL,
  `last_name`  VARCHAR(80) DEFAULT NULL,
  `company`    VARCHAR(120) DEFAULT NULL,
  `phone`      VARCHAR(40) NOT NULL,
  `phone_type` ENUM('Mobile','Work','Home') DEFAULT 'Mobile',
  `email`      VARCHAR(150) DEFAULT NULL,
  `group_name` ENUM('Customers','Partners','Clients','Suppliers') DEFAULT 'Customers',
  `avatar_color` VARCHAR(20) DEFAULT 'blue',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_contact_user` FOREIGN KEY (`user_id`) REFERENCES `pkg_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ CALLS ============
DROP TABLE IF EXISTS `pkg_call`;
CREATE TABLE `pkg_call` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        INT UNSIGNED NOT NULL,
  `sip_id`         INT UNSIGNED DEFAULT NULL,
  `direction`      ENUM('incoming','outgoing','missed') NOT NULL,
  `from_number`    VARCHAR(60) NOT NULL,
  `to_number`      VARCHAR(60) NOT NULL,
  `contact_id`     INT UNSIGNED DEFAULT NULL,
  `status`         ENUM('completed','missed','failed','busy','no_answer') DEFAULT 'completed',
  `duration_sec`   INT DEFAULT 0,
  `channel`        VARCHAR(120) DEFAULT NULL,
  `unique_id`      VARCHAR(120) DEFAULT NULL,
  `recording_url`  VARCHAR(255) DEFAULT NULL,
  `started_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
  `ended_at`       DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_sip` (`sip_id`),
  CONSTRAINT `fk_call_user` FOREIGN KEY (`user_id`) REFERENCES `pkg_user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_call_sip`  FOREIGN KEY (`sip_id`)  REFERENCES `pkg_sip`  (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ CALL NOTES ============
DROP TABLE IF EXISTS `pkg_call_note`;
CREATE TABLE `pkg_call_note` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `call_id`    INT UNSIGNED NOT NULL,
  `user_id`    INT UNSIGNED NOT NULL,
  `note`       TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_call` (`call_id`),
  CONSTRAINT `fk_note_call` FOREIGN KEY (`call_id`) REFERENCES `pkg_call` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ CALL DTMF ============
DROP TABLE IF EXISTS `pkg_call_dtmf`;
CREATE TABLE `pkg_call_dtmf` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `call_id`    INT UNSIGNED NOT NULL,
  `digit`      VARCHAR(1) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_call` (`call_id`),
  CONSTRAINT `fk_dtmf_call` FOREIGN KEY (`call_id`) REFERENCES `pkg_call` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
--  SEED DATA — demo user + full sample content
-- =====================================================================
INSERT INTO `pkg_user`
 (`id`,`full_name`,`username`,`email`,`phone`,`password`,`avatar`,`timezone`,`status`)
VALUES
 (1,'John Doe','john','john.doe@example.com','+1 (202) 555-0143','123456',
  'https://i.pravatar.cc/200?img=15','(UTC-05:00) Eastern Time (US & Canada)','active');

INSERT INTO `pkg_setting`(`user_id`) VALUES (1);

INSERT INTO `pkg_subscription`
 (`user_id`,`plan_name`,`price`,`billing_cycle`,`status`,`next_billing`,`card_brand`,`card_last4`,`card_expires`,`sip_limit`,`user_limit`,`minute_limit`,`recording_gb`)
VALUES
 (1,'Professional',29.00,'monthly','active','2025-06-20','Visa','4242','12/28',10,5,5000,50);

INSERT INTO `pkg_sip`(`user_id`,`account_name`,`subtitle`,`sip_username`,`sip_password`,`sip_server`,`sip_port`,`transport`,`caller_id`,`is_default`,`status`,`last_registered`,`icon_color`,`icon`) VALUES
 (1,'Twilio Account','Primary Twilio Account','company123','pass123','sip.twilio.com',5060,'UDP','+1 202-555-0143',1,'registered',NOW(),'green','fa-circle-nodes'),
 (1,'Telnyx Account','Backup Telnyx','company456','pass456','sip.telnyx.com',5060,'TCP','+1 202-555-0187',0,'offline',NULL,'blue','fa-tower-broadcast'),
 (1,'Bandwidth Account','US Number','company789','pass789','sip.bandwidth.com',5061,'TLS','+1 202-555-0164',0,'offline',NULL,'purple','fa-signal');

INSERT INTO `pkg_contact`(`user_id`,`first_name`,`last_name`,`company`,`phone`,`phone_type`,`email`,`group_name`,`avatar_color`) VALUES
 (1,'Sarah','Miller','Acme Inc.','+1 (202) 555-0143','Mobile','sarah.miller@acme.com','Customers','green'),
 (1,'James','Brown','Globex Corporation','+1 (202) 555-0187','Mobile','james.brown@globex.com','Partners','blue'),
 (1,'Emily','Taylor','Initech','+1 (202) 555-0129','Work','emily.taylor@initech.com','Clients','red'),
 (1,'Michael','Wilson','Soylent Corp.','+1 (202) 555-0164','Mobile','michael.wilson@soylent.com','Customers','orange'),
 (1,'Olivia','Lee','Umbrella Corp.','+1 (202) 555-0112','Work','olivia.lee@umbrella.com','Partners','purple'),
 (1,'Daniel','Clark','Stark Industries','+1 (202) 555-0177','Mobile','daniel.clark@stark.com','Suppliers','teal'),
 (1,'Laura','Carter','Wayne Enterprises','+1 (202) 555-0133','Work','laura.carter@wayne.com','Clients','pink');

INSERT INTO `pkg_call`(`user_id`,`sip_id`,`direction`,`from_number`,`to_number`,`status`,`duration_sec`,`started_at`,`ended_at`) VALUES
 (1,1,'outgoing','+1 (202) 555-0183','+1 (305) 555-0147','completed',135,'2025-05-20 10:25:30','2025-05-20 10:27:45'),
 (1,1,'incoming','+1 (305) 555-0147','+1 (202) 555-0183','completed',108,'2025-05-20 10:18:44','2025-05-20 10:20:32'),
 (1,2,'outgoing','+1 (202) 555-0183','+1 (617) 555-0198','completed',323,'2025-05-20 09:55:12','2025-05-20 10:00:35'),
 (1,1,'missed',  '+1 (213) 555-0112','+1 (202) 555-0183','missed',      0,'2025-05-20 09:42:07','2025-05-20 09:42:07'),
 (1,3,'outgoing','+1 (202) 555-0183','+1 (408) 555-0166','completed',191,'2025-05-20 09:15:33','2025-05-20 09:18:44'),
 (1,3,'incoming','+1 (408) 555-0166','+1 (202) 555-0183','completed',242,'2025-05-20 08:59:21','2025-05-20 09:03:23'),
 (1,2,'outgoing','+1 (202) 555-0183','+1 (704) 555-0123','completed', 93,'2025-05-19 19:48:10','2025-05-19 19:49:43'),
 (1,2,'incoming','+1 (704) 555-0123','+1 (202) 555-0183','completed',165,'2025-05-19 19:32:55','2025-05-19 19:35:40'),
 (1,1,'outgoing','+1 (202) 555-0183','+1 (786) 555-0101','completed', 58,'2025-05-19 18:15:09','2025-05-19 18:16:07'),
 (1,3,'outgoing','+1 (202) 555-0183','+1 (301) 555-0177','completed',276,'2025-05-19 17:59:47','2025-05-19 18:04:23');

INSERT INTO `pkg_notification`(`user_id`,`type`,`title`,`message`) VALUES
 (1,'sip','Twilio Account registered','Your SIP account is online.'),
 (1,'call','Missed call','You missed a call from +1 (213) 555-0112'),
 (1,'billing','Invoice ready','Your next invoice is available.');
