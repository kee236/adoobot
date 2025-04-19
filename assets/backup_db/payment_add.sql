ALTER TABLE `payment_config`
    -- เพิ่มคอลัมน์สำหรับข้อมูลการโอนเงินผ่านธนาคาร
    ADD COLUMN `bank_transfer_enabled` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT 'Enable/Disable Bank Transfer' AFTER `deleted`,
    ADD COLUMN `bank_account_name` VARCHAR(255) NULL COMMENT 'Account Name for Bank Transfer' AFTER `bank_transfer_enabled`,
    ADD COLUMN `bank_name` VARCHAR(255) NULL COMMENT 'Bank Name for Bank Transfer' AFTER `bank_account_name`,
    ADD COLUMN `bank_account_number` VARCHAR(255) NULL COMMENT 'Account Number for Bank Transfer' AFTER `bank_name`,
    ADD COLUMN `bank_branch` VARCHAR(255) NULL COMMENT 'Bank Branch for Bank Transfer' AFTER `bank_account_number`,

    -- ปรับปรุงคอลัมน์ `currency` ให้มี THB และปรับปรุงค่า DEFAULT ให้เหมาะสม
    MODIFY `currency` ENUM('USD','AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','TWD','NZD','NOK','PHP','PLN','GBP','RUB','SGD','SEK','CHF','VND','THB') CHARACTER SET utf8 NOT NULL DEFAULT 'THB';



   -- สร้างตาราง ref_banks
   CREATE TABLE ref_banks (
       bank_code VARCHAR(20) PRIMARY KEY,
       bank_name_th VARCHAR(255),
       bank_name_en VARCHAR(255),
       bank_icon_url VARCHAR(255),
       active BOOLEAN,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   );

   -- สร้างตาราง payment_accounts
   CREATE TABLE payment_accounts (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT,
       bank_code VARCHAR(20),
       account_number VARCHAR(255),
       account_holder_name VARCHAR(255),
       branch_code VARCHAR(255) NULL,
       is_default BOOLEAN,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id), -- สมมติว่ามีตาราง users
       FOREIGN KEY (bank_code) REFERENCES ref_banks(bank_code)
   );

   -- ตัวอย่างการเพิ่มข้อมูลธนาคาร
   INSERT INTO ref_banks (bank_code, bank_name_th, bank_name_en, bank_icon_url, active) VALUES
   ('KBANK', 'ธนาคารกสิกรไทย', 'Kasikornbank', '/images/kbank_logo.png', TRUE),
   ('SCB', 'ธนาคารไทยพาณิชย์', 'Siam Commercial Bank', '/images/scb_logo.png', TRUE);
