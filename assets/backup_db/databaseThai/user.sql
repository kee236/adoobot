DROP TABLE IF EXISTS `user_login_info`;
CREATE TABLE IF NOT EXISTS `user_login_info` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `login_time` DATETIME NOT NULL,
  `login_ip` VARCHAR(45) NOT NULL COMMENT 'รองรับ IPv4 และ IPv6',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_user_login_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ประวัติการเข้าสู่ระบบของผู้ใช้';

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `email` VARCHAR(99) NOT NULL,
  `fb_id` VARCHAR(50) NOT NULL,
  `mobile` VARCHAR(20) NOT NULL,
  `password` VARCHAR(99) NOT NULL,
  `user_type` ENUM('Member','Admin') NOT NULL DEFAULT 'Member',
  `status` ENUM('1','0') NOT NULL DEFAULT '1',
  `add_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `purchase_date` DATETIME NULL DEFAULT NULL,
  `last_login_at` DATETIME NULL DEFAULT NULL,
  `activation_code` VARCHAR(20) DEFAULT NULL,
  `expired_date` DATETIME NULL DEFAULT NULL,
  `bot_status` ENUM('0','1') NOT NULL DEFAULT '1',
  `package_id` INT(11) NOT NULL DEFAULT '0',
  `deleted` ENUM('0','1') NOT NULL DEFAULT '0',
  `brand_logo` TEXT,
  `brand_url` TEXT,
  `time_zone` VARCHAR(255) DEFAULT 'Asia/Bangkok',
  `last_login_ip` VARCHAR(45) NOT NULL,
  `browser_notification_enabled` ENUM('0','1') NOT NULL DEFAULT '0',
  `affiliate_id` INT(11) NOT NULL DEFAULT '0',

  -- Fields for Thai Localization & E-commerce
  `company_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ชื่อบริษัท/ห้างร้าน',
  `tax_id` VARCHAR(20) NULL DEFAULT NULL COMMENT 'เลขประจำตัวผู้เสียภาษี',
  `address_line_1` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ที่อยู่บรรทัดที่ 1 (บ้านเลขที่, ถนน)',
  `address_line_2` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ที่อยู่บรรทัดที่ 2 (หมู่บ้าน, ซอย)',
  `sub_district` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ตำบล/แขวง',
  `district` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'อำเภอ/เขต',
  `province` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'จังหวัด',
  `postal_code` VARCHAR(10) NULL DEFAULT NULL COMMENT 'รหัสไปรษณีย์',

  -- Payment-related fields
  `currency` ENUM('USD','THB','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK','BRL') NOT NULL DEFAULT 'THB',
  `last_payment_method` VARCHAR(50) NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `fb_id`, `mobile`, `password`, `user_type`, `status`, `add_date`, `purchase_date`, `last_login_at`, `activation_code`, `expired_date`, `bot_status`, `package_id`, `deleted`, `brand_logo`, `brand_url`, `time_zone`, `last_login_ip`, `browser_notification_enabled`, `affiliate_id`, `company_name`, `tax_id`, `address_line_1`, `address_line_2`, `sub_district`, `district`, `province`, `postal_code`, `currency`, `last_payment_method`) VALUES
(1, 'Admin', 'admin@admin.com', '', '', '259534db5d66c3effb7aa2dbbee67ab0', 'Admin', '1', '2019-08-25 12:00:00', NULL, NULL, NULL, NULL, '1', 0, '0', NULL, NULL, 'Asia/Bangkok', '', '0', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'THB', '');
