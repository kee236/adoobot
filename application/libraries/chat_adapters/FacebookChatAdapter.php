<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'libraries/chat_adapters/ChatAdapterInterface.php');

class FacebookChatAdapter implements ChatAdapterInterface {
    
    protected $ci;

    public function __construct() {
        $this->ci =& get_instance();
        // โหลด Library ที่จำเป็นสำหรับ FB
        if (!isset($this->ci->fb_rx_login)) {
            $this->ci->load->library('fb_rx_login');
        }
    }

    public function get_subscribers($page_table_id, $options = []) {
        // --- (ย้ายโค้ด Logic เดิมจาก Message_manager.php ส่วนที่ดึง Subscriber ของ FB มาไว้ที่นี่) ---
        
        $search_value = $options['search_value'] ?? '';
        
        $this->ci->db->select('messenger_bot_subscriber.*, CONCAT(first_name, " ", last_name) as full_name');
        $this->ci->db->from('messenger_bot_subscriber');
        $this->ci->db->where(['page_id' => $page_table_id, 'social_media' => 'fb', 'is_subscribed' => '1']);
        
        if ($search_value != '') {
            $this->ci->db->where("(first_name LIKE '%{$search_value}%' OR last_name LIKE '%{$search_value}%' OR CONCAT(first_name, ' ', last_name) LIKE '%{$search_value}%')");
        }
        $this->ci->db->order_by('last_interaction_time', 'DESC');
        // $this->ci->db->limit($limit, $start);
        
        $query = $this->ci->db->get();
        return $query->result_array();
    }

    public function get_conversations($page_table_id) {
        // --- (ย้ายโค้ด Logic เดิมจาก Message_manager.php ส่วน get_pages_conversations ของ FB) ---
        // (ส่วนนี้ Logic จะคล้ายกับ get_subscribers แต่มีการ join ที่ซับซ้อนกว่า)
        return $this->get_subscribers($page_table_id);
    }

    public function get_conversation_replies($subscriber_id, $page_table_id) {
        // --- (ย้ายโค้ด Logic เดิมจาก Message_manager.php ส่วน get_replies ของ FB) ---
        $this->ci->db->select('*');
        $this->ci->db->from('messenger_bot_message_log');
        $this->ci->db->where(['page_table_id' => $page_table_id, 'subscriber_id' => $subscriber_id]);
        $this->ci->db->order_by('sent_time', 'ASC');
        $query = $this->ci->db->get();
        return $query->result_array();
    }

    public function send_reply($reply_data) {
        // --- (ย้ายโค้ด Logic เดิมจาก Message_manager.php ส่วน reply_action ของ FB) ---
        // นี่คือการเรียก cURL ไปที่ /messenger_bot/send_message_bot_reply
        // เราควร Refactor ให้เรียกใช้ Service โดยตรง แต่สำหรับตอนนี้ ใช้แบบเดิมไปก่อน
        
        $post_data = [
            'page_id' => $reply_data['page_table_id'],
            'subscriber_id' => $reply_data['from_user_id'],
            'reply_message' => $reply_data['reply_message']
            // ... (ข้อมูลอื่นๆ)
        ];
        
        $url = base_url("messenger_bot/send_message_bot_reply");
        // ... (โค้ด cURL เดิม) ...
        
        return ['status' => '1', 'message' => 'FB Reply Sent (Simulated)'];
    }
    
    public function send_postback_reply($reply_data) {
        // (Logic การส่ง Postback ของ FB)
        return ['status' => '1', 'message' => 'FB Postback Sent (Simulated)'];
    }
}
