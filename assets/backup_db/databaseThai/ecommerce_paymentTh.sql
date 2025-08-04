-- เพิ่มเมนูสำหรับตั้งค่าระบบชำระเงิน

INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES
(NULL, 'Payment Settings', 'ecommerce/payment_settings', 1, 'fas fa-credit-card', '268', 43, '0', '0', '0', '0', '0', 0);

-- เพิ่มเมนูสำหรับตั้งค่าระบบขนส่ง

INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES
(NULL, 'Shipping Settings', 'ecommerce/shipping_settings', 2, 'fas fa-truck', '268', 43, '0', '0', '0', '0', '0', 0);


DROP TABLE IF EXISTS `payment_config`;
CREATE TABLE IF NOT EXISTS `payment_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stripe_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Stripe Secret Key',
  `stripe_publishable_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Stripe Publishable Key',

  `omise_public_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Omise Public Key',
  `omise_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Omise Secret Key',
  `omise_test_mode` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=Live, 1=Test',

  `promptpay_phone_number` varchar(20) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'เบอร์โทรศัพท์ PromptPay',
  `promptpay_account_name` varchar(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'ชื่อบัญชี PromptPay',

  `manual_payment` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes',
  `manual_payment_instruction` mediumtext COLLATE utf8mb4_unicode_ci COMMENT 'รายละเอียดการโอนเงิน',
  
  `currency` enum('USD','THB','AUD','CAD','EUR','ILS','NZD','SGD') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'THB',

  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `payment_error_log`;
CREATE TABLE IF NOT EXISTS `payment_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `call_time` datetime DEFAULT NULL,
  `request_payload` text COLLATE utf8mb4_unicode_ci,
  `error_log` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ecommerce_cart`;
CREATE TABLE IF NOT EXISTS `ecommerce_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `subscriber_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'messenger_bot_subscriber.subscribe_id',
  `subtotal` float NOT NULL,
  `tax` float NOT NULL,
  `shipping` float NOT NULL,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coupon_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount` float NOT NULL,
  `grand_total` float NOT NULL COMMENT 'รวมยอดทั้งหมด',
  `currency` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'THB',
  `ordered_at` datetime NOT NULL,
  
  -- Shipping Address (ที่อยู่สำหรับจัดส่ง)
  `buyer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_mobile` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_email` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buyer_sub_district` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_district` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_province` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_postal_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_country` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Thailand',

  -- Billing Address (ที่อยู่สำหรับออกบิล) - ถ้าต้องการ
  `bill_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_mobile` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_email` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bill_sub_district` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_district` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_province` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_postal_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bill_country` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Thailand',

  `delivery_note` text COLLATE utf8mb4_unicode_ci,
  `store_pickup` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `pickup_point_details` text COLLATE utf8mb4_unicode_ci,

  -- Payment & Transaction
  `payment_method` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',

  -- Order Status
  `order_status` enum('pending','approved','rejected','shipped','delivered','completed') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'สถานะการจัดส่ง',
  `status_changed_at` datetime DEFAULT NULL,
  `status_changed_note` text COLLATE utf8mb4_unicode_ci,
  
  -- Time & Metadata
  `updated_at` datetime NOT NULL,
  `action_type` enum('add','remove','checkout') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'add',
  `confirmation_response` text COLLATE utf8mb4_unicode_ci,
  `delivery_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `user_id` (`user_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ecommerce_store`;
CREATE TABLE IF NOT EXISTS `ecommerce_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_unique_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `store_type` enum('physical','digital','service') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'physical',
  `store_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_logo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_favicon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_email` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  
  -- Store Address (ที่อยู่ร้านค้า)
  `store_address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_sub_district` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_district` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_province` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_postal_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_country` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Thailand',
  
  -- Payment Configuration
  `enabled_payment_methods` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'JSON array of enabled payment methods',
  `manual_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `cod_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `currency` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'THB',
  `currency_position` enum('left','right') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'right' COMMENT 'ตั้งค่าให้เงินบาทอยู่ด้านขวา',
  `decimal_point` tinyint(4) NOT NULL DEFAULT '2',
  `thousand_comma` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT 'ใช้ comma เป็นตัวคั่นหลักพัน',

  -- Other E-commerce Config
  `tax_percentage` float NOT NULL,
  `shipping_charge` float NOT NULL,
  `store_pickup_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'รับสินค้าที่ร้าน',
  `is_store_pickup` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_home_delivery` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `is_delivery_note` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',

  -- Time & Date
  `created_at` datetime NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `updated_at` datetime NOT NULL,
  
  -- Social Media & Branding
  `pixel_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_send_order_button` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `whatsapp_phone_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `whatsapp_send_order_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_unique_id` (`store_unique_id`),
  KEY `user_id` (`user_id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `ecommerce_attribute`;
CREATE TABLE IF NOT EXISTS `ecommerce_attribute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `attribute_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribute_values` JSON NOT NULL COMMENT 'ข้อมูลคุณสมบัติย่อยในรูปแบบ JSON เช่น [{"name":"แดง", "stock": 10}, {"name":"น้ำเงิน", "stock": 5}]',
  `optional` enum('0','1') NOT NULL DEFAULT '0',
  `multiselect` enum('0','1') NOT NULL DEFAULT '0',
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `fk_ecommerce_attribute_store_id` FOREIGN KEY (`store_id`) REFERENCES `ecommerce_store` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ecommerce_category`;
CREATE TABLE IF NOT EXISTS `ecommerce_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `thumbnail` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial` int(11) NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `fk_ecommerce_category_store_id` FOREIGN KEY (`store_id`) REFERENCES `ecommerce_store` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `ecommerce_product`;
CREATE TABLE IF NOT EXISTS `ecommerce_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_description` text COLLATE utf8mb4_unicode_ci,
  `product_video_id` varchar(100) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `original_price` float NOT NULL DEFAULT '0',
  `sell_price` float NOT NULL DEFAULT '0',
  `taxable` enum('0','1') NOT NULL DEFAULT '0',
  `stock_item` int(11) NOT NULL DEFAULT '0',
  `stock_display` enum('0','1') NOT NULL DEFAULT '0',
  `stock_prevent_purchase` enum('0','1') NOT NULL DEFAULT '0',
  `attribute_ids` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'JSON array of attribute IDs',
  `purchase_note` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` text COLLATE utf8mb4_unicode_ci,
  `featured_images` text COLLATE utf8mb4_unicode_ci,
  `digital_product_file` text COLLATE utf8mb4_unicode_ci,
  `category_id` int(11) NOT NULL,
  `sales_count` int(11) NOT NULL DEFAULT '0',
  `visit_count` int(11) NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `is_featured` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_ecommerce_product_store_id` FOREIGN KEY (`store_id`) REFERENCES `ecommerce_store` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ecommerce_product_category_id` FOREIGN KEY (`category_id`) REFERENCES `ecommerce_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ecommerce_orders`;
CREATE TABLE IF NOT EXISTS `ecommerce_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) NOT NULL COMMENT 'อ้างอิงถึงตะกร้าสินค้าเดิม',
  `store_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `grand_total` float NOT NULL,
  `shipping_charge` float NOT NULL,
  `tax` float NOT NULL,
  `payment_status` enum('pending','paid','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `order_status` enum('pending','approved','shipped','delivered','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shipping_info_id` int(11) NULL DEFAULT NULL COMMENT 'อ้างอิงถึงข้อมูลการจัดส่ง',
  `ordered_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_id` (`cart_id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `fk_ecommerce_orders_cart_id` FOREIGN KEY (`cart_id`) REFERENCES `ecommerce_cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ecommerce_shipping_tracking`;
CREATE TABLE IF NOT EXISTS `ecommerce_shipping_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `shipping_provider` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'เช่น Kerry, Flash, J&T',
  `tracking_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_url` text COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `shipping_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'สถานะการจัดส่งล่าสุด',
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  CONSTRAINT `fk_ecommerce_shipping_tracking_order_id` FOREIGN KEY (`order_id`) REFERENCES `ecommerce_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ecommerce_manual_payment_slip`;
CREATE TABLE IF NOT EXISTS `ecommerce_manual_payment_slip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `slip_image_url` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL รูปภาพสลิป',
  `amount` float NOT NULL,
  `paid_at` datetime NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` int(11) NULL DEFAULT NULL,
  `approved_at` datetime NULL DEFAULT NULL,
  `rejected_note` text COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `fk_ecommerce_manual_payment_slip_order_id` FOREIGN KEY (`order_id`) REFERENCES `ecommerce_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
