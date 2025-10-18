<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'libraries/chat_adapters/ChatAdapterInterface.php');

class InstagramChatAdapter implements ChatAdapterInterface {
    
    protected $ci;
    public function __construct() { $this->ci =& get_instance(); }

    public function get_subscribers($page_table_id, $options = []) {
        // --- (ย้าย Logic เดิมจาก Message_manager.php/instagram_message_dashboard) ---
        $search_value = $options['search_value'] ?? '';
        
        $this->ci->db->select('messenger_bot_subscriber.*, CONCAT(first_name, " ", last_name) as full_name');
        $this->ci->db->from('messenger_bot_subscriber');
        $this->ci->db->where(['page_id' => $page_table_id, 'social_media' => 'ig', 'is_subscribed' => '1']);
        
        if ($search_value != '') {
            $this->ci->db->where("(first_name LIKE '%{$search_value}%' OR last_name LIKE '%{$search_value}%' OR CONCAT(first_name, ' ', last_name) LIKE '%{$search_value}%')");
        }
        $this->ci->db->order_by('last_interaction_time', 'DESC');
        
        $query = $this->ci->db->get();
        return $query->result_array();
    }

    public function get_conversations($page_table_id) {
        return $this->get_subscribers($page_table_id);
    }

    public function get_conversation_replies($subscriber_id, $page_table_id) {
        // --- (ย้าย Logic เดิมจาก Message_manager.php ส่วน get_replies ของ IG) ---
        // (Logic ส่วนนี้อาจต้องใช้ API หรือดึงจากตาราง messenger_bot_message_log ที่มี social_media = 'ig')
        return []; // Placeholder
    }
    
    public function send_reply($reply_data) {
        // --- (ย้าย Logic เดิมจาก Message_manager.php ส่วน reply_action ของ IG) ---
        // (Logic การยิง API ตอบกลับของ IG)
        return ['status' => '1', 'message' => 'IG Reply Sent (Simulated)'];
    }
    
    public function send_postback_reply($reply_data) {
        // (Logic การส่ง Postback ของ IG)
        return ['status' => '1', 'message' => 'IG Postback Sent (Simulated)'];
    }
}
