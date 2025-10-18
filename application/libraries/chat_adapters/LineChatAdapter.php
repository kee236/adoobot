<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'libraries/chat_adapters/ChatAdapterInterface.php');

class LineChatAdapter implements ChatAdapterInterface {
    
    protected $ci;
    public function __construct() {
        $this->ci =& get_instance();
        // โหลด Service ของ LINE ที่เราสร้างไว้
        if (!isset($this->ci->lineapiservice)) {
            $this->ci->load->library('services/LineApiService');
        }
    }

    public function get_subscribers($page_table_id, $options = []) {
        $search_value = $options['search_value'] ?? '';
        
        $this->ci->db->select("messenger_bot_subscriber.*, display_name as full_name");
        $this->ci->db->from('messenger_bot_subscriber');
        $this->ci->db->where(['page_id' => $page_table_id, 'social_media' => 'line', 'is_subscribed' => '1']);
        
        if ($search_value != '') {
            $this->ci->db->where("(display_name LIKE '%{$search_value}%')");
        }
        $this->ci->db->order_by('last_interaction_time', 'DESC');
        
        $query = $this->ci->db->get();
        return $query->result_array();
    }

    public function get_conversations($page_table_id) {
        return $this->get_subscribers($page_table_id);
    }

    public function get_conversation_replies($subscriber_id, $page_table_id) {
        // (ดึง Log การแชทของ LINE จากตาราง messenger_bot_message_log)
        return []; // Placeholder
    }

    public function send_reply($reply_data) {
        try {
            // ดึงข้อมูล Channel จาก page_id
            $channel_info = $this->ci->basic->get_data('line_oa_channels', ['where' => ['page_id' => $reply_data['page_table_id']]]);
            if(empty($channel_info)) throw new Exception('LINE Channel not configured.');
            
            $this->ci->lineapiservice->set_channel($channel_info[0]['id']);
            
            $message_obj = [
                ['type' => 'text', 'text' => $reply_data['reply_message']]
            ];
            
            // ใช้ Service ที่เราสร้างไว้ส่ง Push Message
            $result = $this->ci->lineapiservice->send_push_message($reply_data['from_user_id'], $message_obj);
            
            if(isset($result['status']) && $result['status'] == 'error') {
                 return ['status' => '0', 'message' => $result['message']];
            }
            return ['status' => '1', 'message' => 'LINE Reply Sent Successfully'];

        } catch (Exception $e) {
            return ['status' => '0', 'message' => $e->getMessage()];
        }
    }
    
    public function send_postback_reply($reply_data) {
        // LINE ไม่มี Concept การส่ง Postback จากฝั่ง Admin แบบ FB/IG
        // อาจจะต้องเปลี่ยนเป็นส่ง Flex Message หรือ Template แทน
        return ['status' => '0', 'message' => 'Not supported on LINE'];
    }
}
