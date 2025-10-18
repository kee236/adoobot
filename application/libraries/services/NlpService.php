<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ใช้ Phayathai Library ที่โหลดผ่าน Composer
use Phayathai\Phayathai;

class NlpService {

    protected $ci;

    public function __construct() {
        $this->ci =& get_instance();
    }

    /**
     * ทำการตัดคำภาษาไทย (Tokenize)
     * @param string $text ข้อความภาษาไทย
     * @return array รายการคำที่ตัดแล้ว
     */
    public function tokenize($text) {
        if (empty($text)) {
            return [];
        }

        try {
            // ใช้ Phayathai::split ในการตัดคำ
            $words = Phayathai::split($text);
            
            // กรองคำสั้นๆ, ช่องว่าง หรือคำที่ไม่มีความหมายออก
            $filtered_words = array_filter($words, function($word) {
                return !empty(trim($word)) && mb_strlen($word) > 1;
            });
            
            // ทำให้เป็นตัวพิมพ์เล็กทั้งหมดเพื่อง่ายต่อการเปรียบเทียบ
            return array_map('mb_strtolower', $filtered_words);

        } catch (Exception $e) {
            log_message('error', 'NlpService Error: ' . $e->getMessage());
            // หาก Library มีปัญหา ให้กลับไปใช้วิธีแยกตามช่องว่างแบบเดิม
            return explode(' ', mb_strtolower($text));
        }
    }

    /**
     * เปรียบเทียบว่าคีย์เวิร์ดอยู่ในข้อความหรือไม่ (แบบ NLP)
     * @param string $user_message ข้อความจากผู้ใช้
     * @param string $keyword_string คีย์เวิร์ดของบอท (เช่น "สนใจ,ราคา,โปรโมชั่น")
     * @return bool
     */
    public function keywords_match($user_message, $keyword_string) {
        // 1. ตัดคำข้อความของผู้ใช้
        $user_tokens = $this->tokenize($user_message);
        if (empty($user_tokens)) {
            return false;
        }

        // 2. แยกคีย์เวิร์ดของบอท (ที่คั่นด้วยลูกน้ำ)
        $bot_keywords = explode(',', $keyword_string);
        
        foreach ($bot_keywords as $keyword) {
            $keyword = trim($keyword);
            if (empty($keyword)) continue;

            // 3. ตัดคำคีย์เวิร์ดของบอท
            // เช่น "สนใจสินค้า" จะกลายเป็น ["สนใจ", "สินค้า"]
            $keyword_tokens = $this->tokenize($keyword);
            
            if(empty($keyword_tokens)) continue;

            // 4. ตรวจสอบว่า "คำทั้งหมด" ในคีย์เวิร์ด อยู่ใน "ข้อความของผู้ใช้" หรือไม่
            // array_intersect จะหาคำที่เหมือนกัน
            $matches = array_intersect($keyword_tokens, $user_tokens);

            if (count($matches) == count($keyword_tokens)) {
                // ถ้าจำนวนคำที่ตรงกัน == จำนวนคำของคีย์เวิร์ด
                // แปลว่า "Match"
                // เช่น Keyword ["สนใจ", "สินค้า"]
                // User Tokens ["ผม", "สนใจ", "สินค้า", "ตัว", "นี้"]
                // Match!
                return true;
            }
        }
        
        return false;
    }
}
