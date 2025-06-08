DROP TABLE IF EXISTS `payment_config`;
CREATE TABLE IF NOT EXISTS `payment_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paypal_email` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paypal_payment_type` enum('manual','recurring') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `paypal_mode` enum('live','sandbox') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'live',
  `stripe_secret_key` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_publishable_key` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay_key_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay_key_secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paystack_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paystack_public_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mercadopago_public_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mercadopago_access_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `marcadopago_country` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mercadopago_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mollie_api_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `manual_payment` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `manual_payment_instruction` mediumtext COLLATE utf8mb4_unicode_ci,
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `sslcommerz_store_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sslcommerz_store_password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sslcommers_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senangpay_merchent_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `senangpay_secret_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `senangpay_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instamojo_api_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instamojo_auth_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instamojo_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `toyyibpay_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `toyyibpay_category_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `toyyibpay_mode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `xendit_secret_api_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `myfatoorah_api_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `myfatoorah_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paymaya_public_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paymaya_secret_key` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paymaya_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  
  -- เพิ่ม PromptPay
  `promptpay_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `promptpay_id` varchar(255) COLLATE utf8mb4_unicode_ci NULL, -- เบอร์โทรศัพท์ หรือ เลขประจำตัวผู้เสียภาษี
  
  -- เพิ่ม Omise
  `omise_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `omise_public_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `omise_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `package_id` INT NULL,
    `order_id` VARCHAR(255) NOT NULL UNIQUE, -- ID การสั่งซื้อจากระบบ ChatPion ของเรา
    `gateway_transaction_id` VARCHAR(255) NULL, -- ID อ้างอิงจาก Payment Gateway (เช่น Omise Charge ID, PayPal Transaction ID)
    `gateway` VARCHAR(50) NOT NULL, -- ชื่อ Payment Gateway ที่ใช้ (e.g., 'paypal', 'stripe', 'promptpay', 'omise')
    `amount` DECIMAL(10, 2) NOT NULL, -- จำนวนเงินที่ควรจะชำระ
    `paid_amount` DECIMAL(10, 2) NULL, -- จำนวนเงินที่ชำระจริง (อาจแตกต่างเล็กน้อยหากมีส่วนลด หรือการปัดเศษ)
    `currency` VARCHAR(3) NOT NULL, -- สกุลเงิน (เช่น 'THB', 'USD')
    `status` ENUM('pending', 'completed', 'failed', 'cancelled', 'amount_mismatch', 'refunded') NOT NULL DEFAULT 'pending', -- สถานะการชำระเงิน
    `payment_date` DATETIME NULL, -- วันที่และเวลาที่ Payment Gateway ยืนยันการชำระเงินสำเร็จ
    `transaction_time` DATETIME NOT NULL, -- วันที่และเวลาที่สร้าง Transaction นี้ขึ้นในระบบ (ก่อนส่งไป Gateway)
    `metadata` JSON NULL, -- เก็บข้อมูลเพิ่มเติมที่เกี่ยวข้องกับ Transaction ในรูปแบบ JSON (เช่น response จาก gateway)
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_gateway_transaction_id` (`gateway_transaction_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
