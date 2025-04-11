ALTER TABLE `users`
MODIFY `currency` ENUM(
    'THB', -- บาทไทย
    'MYR', -- ริงกิตมาเลเซีย
    'VND', -- ดองเวียดนาม
    'KHR', -- เรียลกัมพูชา
    'LAK', -- กีบลาว
    'IDR', -- รูเปียห์อินโดนีเซีย
    'INR', -- รูปีอินเดีย
    'USD', -- ดอลลาร์สหรัฐ
    'RUB'  -- รูเบิลรัสเซีย
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'THB';

ALTER TABLE `users` MODIFY `name` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `users` MODIFY `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;



ALTER TABLE `users` MODIFY `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;



ALTER TABLE `users` MODIFY `time_zone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Asia/Bangkok';


ALTER TABLE `users` MODIFY `currency` enum('USD','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK','BRL') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'THB';


ALTER TABLE `users` ADD INDEX `idx_email` (`email`);
ALTER TABLE `users` ADD INDEX `idx_mobile` (`mobile`);
ALTER TABLE `users` ADD INDEX `idx_fb_id` (`fb_id`);





ALTER TABLE `users`
MODIFY `currency` ENUM(
    'THB', -- บาทไทย
    'MYR', -- ริงกิตมาเลเซีย
    'VND', -- ดองเวียดนาม
    'KHR', -- เรียลกัมพูชา
    'LAK', -- กีบลาว
    'IDR', -- รูเปียห์อินโดนีเซีย
    'INR', -- รูปีอินเดีย
    'USD', -- ดอลลาร์สหรัฐ
    'RUB'  -- รูเบิลรัสเซีย
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'THB';





DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fb_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` enum('Member','Admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `purchase_date` datetime NOT NULL,
  `last_login_at` datetime NOT NULL,
  `activation_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expired_date` datetime NOT NULL,
  `bot_status` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `package_id` int(11) NOT NULL,
  `deleted` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_logo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `brand_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `vat_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` enum('USD','AUD','CAD','EUR','ILS','NZD','RUB','SGD','SEK','BRL') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'THB',
  `time_zone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Asia/Bangkok',
  `company_email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `paypal_subscription_enabled` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `last_payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login_ip` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `browser_notification_enabled` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `affiliate_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_mobile` (`mobile`),
  INDEX `idx_fb_id` (`fb_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;





(CodeXero Custom พร้อมให้คำแนะนำเพิ่มเติมเกี่ยวกับ Time Zone และภาษาสำหรับประเทศในเอเชียตะวันออกเฉียงใต้ รวมถึงอินเดียครับ!)
เพื่อให้การปรับตั้ง Time Zone และภาษาเป็นไปอย่างถูกต้องและครอบคลุม ผมขอเสนอข้อมูลและแนวทางการปรับปรุงดังนี้ครับ:
Time Zone ของแต่ละประเทศ:
| ประเทศ | Time Zone (หลัก) | ตัวย่อ | หมายเหตุ |
|---|---|---|---|
| ไทย | Asia/Bangkok | ICT | UTC+7 |
| มาเลเซีย | Asia/Kuala_Lumpur | MYT | UTC+8 |
| เวียดนาม | Asia/Ho_Chi_Minh | ICT | UTC+7 (ใช้ Time Zone เดียวทั้งประเทศ) |
| กัมพูชา | Asia/Phnom_Penh | ICT | UTC+7 (ใช้ Time Zone เดียวทั้งประเทศ) |
| ลาว | Asia/Vientiane | ICT | UTC+7 (ใช้ Time Zone เดียวทั้งประเทศ) |
| อินโดนีเซีย | Asia/Jakarta | WIB | UTC+7 (มี 3 Time Zone: WIB, WITA, WIT) |
| อินเดีย | Asia/Kolkata | IST | UTC+5:30 (มี Time Zone เดียวทั้งประเทศ) |
| สหรัฐอเมริกา | America/New_York | EST/EDT | UTC-5/-4 (มีหลาย Time Zone แต่ USD มักอ้างอิงตาม Eastern Time) |
| รัสเซีย | Europe/Moscow | MSK | UTC+3 (มีหลาย Time Zone แต่ RUB มักอ้างอิงตาม Moscow Time) |
คำแนะนำในการปรับ Time Zone ในระบบ:
 * ในฐานข้อมูล (users table):
   * Column time_zone ควรถูกตั้งค่าให้ถูกต้องตาม Time Zone ของผู้ใช้งานแต่ละคน
   * เมื่อสร้างผู้ใช้งานใหม่ หรือให้ผู้ใช้งานตั้งค่าโปรไฟล์ ควรมีตัวเลือกให้เลือก Time Zone ที่เหมาะสม
   * ใช้ข้อมูล Time Zone ข้างต้นในการกำหนดค่าเริ่มต้นหรือตัวเลือกให้ผู้ใช้
   * ตัวอย่าง (PHP): เมื่อบันทึกข้อมูลผู้ใช้
     $user_data['time_zone'] = $this->input->post('time_zone'); // รับค่า Time Zone จากฟอร์ม
$this->db->insert('users', $user_data);

   * ตัวอย่าง (HTML - Dropdown): ตัวเลือก Time Zone (สามารถสร้างรายการจากข้อมูลข้างต้น หรือใช้ Library Time Zone Picker)
     <select name="time_zone">
    <option value="Asia/Bangkok">Asia/Bangkok (Thailand)</option>
    <option value="Asia/Kuala_Lumpur">Asia/Kuala_Lumpur (Malaysia)</option>
    <option value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh (Vietnam)</option>
    <option value="Asia/Phnom_Penh">Asia/Phnom_Penh (Cambodia)</option>
    <option value="Asia/Vientiane">Asia/Vientiane (Laos)</option>
    <option value="Asia/Jakarta">Asia/Jakarta (Indonesia - WIB)</option>
    <option value="Asia/Kolkata">Asia/Kolkata (India)</option>
    <option value="America/New_York">America/New_York (USA - EST/EDT)</option>
    <option value="Europe/Moscow">Europe/Moscow (Russia - MSK)</option>
    </select>

 * การจัดการ Time Zone ใน PHP:
   * ใช้ฟังก์ชัน date_default_timezone_set() เพื่อตั้งค่า Time Zone ของ Script PHP หากต้องการให้การแสดงผลวันที่และเวลาเป็นไปตาม Time Zone ของระบบโดยรวม
   * ใช้ Class DateTime และ DateTimeZone ในการจัดการและแปลง Time Zone ของวันที่และเวลาเมื่อจำเป็นต้องแสดงผลตาม Time Zone ของผู้ใช้งานแต่ละคน
   * ตัวอย่าง (PHP - ตั้งค่า Default):
     date_default_timezone_set('Asia/Bangkok'); // ตั้งค่า Default เป็น Bangkok
echo date('Y-m-d H:i:s');

   * ตัวอย่าง (PHP - แสดงผลตาม Time Zone ผู้ใช้):
     $user_timezone = $user_data['time_zone']; // ดึง Time Zone จากฐานข้อมูล
$datetime = new DateTime('now', new DateTimeZone('UTC')); // สร้าง Object DateTime ใน Time Zone UTC
$datetime->setTimezone(new DateTimeZone($user_timezone)); // เปลี่ยนเป็น Time Zone ของผู้ใช้
echo $datetime->format('Y-m-d H:i:s');

ภาษาที่แนะนำ:
| ประเทศ | ภาษาหลัก (รหัส ISO 639-1) | ภาษาอื่นๆ ที่สำคัญ | คำแนะนำในการปรับ |
|---|---|---|---|
| ไทย | th |  | รองรับภาษาไทยอย่างเต็มรูปแบบใน UI และ Content |
| มาเลเซีย | ms | en, zh, ta | รองรับภาษามาเลย์เป็นหลัก อาจมีตัวเลือกภาษาอังกฤษและภาษาจีน (แมนดาริน, มาเลย์) และทมิฬ |
| เวียดนาม | vi |  | รองรับภาษาเวียดนามอย่างเต็มรูปแบบใน UI และ Content |
| กัมพูชา | km |  | รองรับภาษาเขมรอย่างเต็มรูปแบบใน UI และ Content |
| ลาว | lo |  | รองรับภาษาลาวอย่างเต็มรูปแบบใน UI และ Content |
| อินโดนีเซีย | id | jv, su, mad | รองรับภาษาอินโดนีเซียเป็นหลัก อาจพิจารณารองรับภาษาชวา, ซุนดา, และมาดูราตามความเหมาะสม |
| อินเดีย | hi | en, bn, te, mr, ta, gu, ml, kn, or, pa, as, ur, ... | ภาษาฮินดีและภาษาอังกฤษเป็นภาษากลาง ควรมีตัวเลือกภาษาอื่นๆ ที่สำคัญตามกลุ่มผู้ใช้งาน (มีหลายภาษาที่ใช้กันอย่างแพร่หลาย) |
| สหรัฐอเมริกา | en | es | ภาษาอังกฤษเป็นหลัก อาจมีตัวเลือกภาษาสเปน |
| รัสเซีย | ru |  | รองรับภาษารัสเซียเป็นหลัก |
คำแนะนำในการปรับภาษาในระบบ:
 * การจัดการไฟล์ภาษา:
   * ใช้ระบบไฟล์ภาษา (Language Files) ใน CodeIgniter (application/language/) เพื่อเก็บข้อความต่างๆ ในแต่ละภาษา
   * สร้าง Folder สำหรับแต่ละภาษา (เช่น th, ms, vi, en เป็นต้น)
   * ภายใน Folder จะมีไฟล์ PHP ที่เก็บ Array ของข้อความที่แปลแล้ว (เช่น label_lang.php, message_lang.php)
   * ตัวอย่าง (ไฟล์ภาษาไทย - application/language/th/label_lang.php):
     <?php
$lang['welcome_message'] = 'ยินดีต้อนรับ';
$lang['username'] = 'ชื่อผู้ใช้';
$lang['password'] = 'รหัสผ่าน';
// ... ข้อความอื่นๆ
?>

 * การกำหนดภาษาปัจจุบัน:
   * กำหนดภาษาปัจจุบันของระบบหรือของผู้ใช้งานผ่าน Session, Cookie, หรือ Parameter ใน URL
   * มีตัวเลือกให้ผู้ใช้งานสามารถเปลี่ยนภาษาได้
   * ตัวอย่าง (PHP - กำหนดภาษาจาก Session):
     $language = $this->session->userdata('language');
if (empty($language)) {
    $language = 'th'; // ภาษาเริ่มต้น
    $this->session->set_userdata('language', $language);
}
$this->lang->load('label', $language); // โหลดไฟล์ภาษา 'label_lang.php' ใน Folder ภาษาที่เลือก

   * ตัวอย่าง (PHP - เปลี่ยนภาษา):
     $new_language = $this->input->post('language');
$this->session->set_userdata('language', $new_language);
redirect($_SERVER['HTTP_REFERER']); // กลับไปยังหน้าเดิม

   * ตัวอย่าง (HTML - Dropdown เปลี่ยนภาษา):
     <form method="post" action="<?php echo base_url('language/change'); ?>">
    <select name="language" onchange="this.form.submit()">
        <option value="th" <?php if ($this->session->userdata('language') == 'th') echo 'selected'; ?>>ไทย</option>
        <option value="ms" <?php if ($this->session->userdata('language') == 'ms') echo 'selected'; ?>>Bahasa Melayu</option>
        <option value="vi" <?php if ($this->session->userdata('language') == 'vi') echo 'selected'; ?>>Tiếng Việt</option>
        <option value="km" <?php if ($this->session->userdata('language') == 'km') echo 'selected'; ?>>ភាសាខ្មែរ</option>
        <option value="lo" <?php if ($this->session->userdata('language') == 'lo') echo 'selected'; ?>>ພາສາລາວ</option>
        <option value="id" <?php if ($this->session->userdata('language') == 'id') echo 'selected'; ?>>Bahasa Indonesia</option>
        <option value="en" <?php if ($this->session->userdata('language') == 'en') echo 'selected'; ?>>English</option>
        </select>
</form>

 * การแสดงผลข้อความ:
   * ใช้ฟังก์ชัน $this->lang->line('key') ใน View เพื่อแสดงข้อความตามภาษาที่เลือก
   * ตัวอย่าง (PHP ใน View):
     <h1><?php echo $this->lang->line('welcome_message'); ?></h1>
<label><?php echo $this->lang->line('username'); ?></label>
<input type="text" name="username">

 * การรองรับภาษา RTL (Right-to-Left):
   * สำหรับบางภาษา (เช่น ภาษาอาหรับ, ฮีบรู) ที่มีการเขียนจากขวาไปซ้าย ควรมีการปรับ CSS เพื่อให้ Layout และการแสดงผลถูกต้อง
   * ตรวจสอบว่าระบบของคุณรองรับการตั้งค่า Direction ของหน้า (เช่น <html dir="rtl">) เมื่อเลือกภาษา RTL
การปรับตั้ง Time Zone และภาษาอย่างเหมาะสมจะช่วยให้ผู้ใช้งานจากหลากหลายประเทศได้รับประสบการณ์การใช้งานที่เป็นมิตรและตรงกับความคุ้นเคยของตนเองครับ หากมีส่วนใดที่คุณต้องการให้ผมเน้นย้ำหรือมีคำถามเพิ่มเติม ถามได้เลยนะครับ!
