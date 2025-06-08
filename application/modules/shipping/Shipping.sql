-- ตารางสำหรับบันทึกรายการจัดส่ง
CREATE TABLE `shipping_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL COMMENT 'ผู้ใช้ที่สร้างรายการจัดส่ง',
    `order_id` VARCHAR(255) NULL COMMENT 'ID คำสั่งซื้อที่เกี่ยวข้อง (จากระบบของเรา)',
    `shipping_provider` VARCHAR(50) NOT NULL COMMENT 'ชื่อผู้ให้บริการขนส่ง (e.g., kerry_express, flash_express)',
    `tracking_id` VARCHAR(100) UNIQUE NOT NULL COMMENT 'เลขติดตามพัสดุจากขนส่ง',
    `shipping_label_url` TEXT NULL COMMENT 'URL สำหรับพิมพ์ใบปะหน้า',
    `status` VARCHAR(50) NOT NULL DEFAULT 'pending' COMMENT 'สถานะปัจจุบัน (pending, in_transit, delivered, failed, cancelled)',
    `recipient_name` VARCHAR(255) NULL,
    `recipient_address` TEXT NULL,
    `recipient_phone` VARCHAR(50) NULL,
    `weight` DECIMAL(10, 2) NULL COMMENT 'น้ำหนักพัสดุ (kg)',
    `width` DECIMAL(10, 2) NULL,
    `height` DECIMAL(10, 2) NULL,
    `depth` DECIMAL(10, 2) NULL,
    `raw_gateway_response` JSON NULL COMMENT 'เก็บข้อมูล Response ดิบจาก Gateway ตอนสร้าง',
    `last_webhook_data` JSON NULL COMMENT 'เก็บข้อมูล Webhook ล่าสุด',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (`user_id`),
    INDEX (`order_id`),
    INDEX (`shipping_provider`),
    INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางสำหรับเก็บการตั้งค่า API Key ของ Shipping Gateway
CREATE TABLE `shipping_gateway_config` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gateway_name` VARCHAR(50) UNIQUE NOT NULL COMMENT 'ชื่อผู้ให้บริการขนส่ง (e.g., kerry_express)',
    `config_data` JSON NOT NULL COMMENT 'JSON object เก็บข้อมูล API Key, Secret, Endpoint etc.',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตารางสำหรับเก็บประวัติ Tracking Events (ละเอียดกว่า)
CREATE TABLE `shipping_tracking_events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tracking_id` VARCHAR(100) NOT NULL,
    `event_timestamp` DATETIME NOT NULL,
    `event_location` VARCHAR(255) NULL,
    `event_description` TEXT NULL,
    `raw_event_data` JSON NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX (`tracking_id`),
    FOREIGN KEY (`tracking_id`) REFERENCES `shipping_transactions`(`tracking_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
