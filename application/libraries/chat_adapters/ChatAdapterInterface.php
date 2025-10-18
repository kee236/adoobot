<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Interface (สัญญา) สำหรับ Chat Adapter
 * บังคับให้ทุก Adapter (FB, IG, LINE) ต้องมีฟังก์ชันเหล่านี้
 */
interface ChatAdapterInterface {

    /**
     * ดึงข้อมูลผู้ติดตาม (Subscriber) ของเพจ/แชนเนล
     * @param int $page_table_id
     * @param array $options (เช่น search_value, start_limit)
     * @return array
     */
    public function get_subscribers($page_table_id, $options = []);

    /**
     * ดึงรายการห้องสนทนาล่าสุด
     * @param int $page_table_id
     * @return array
     */
    public function get_conversations($page_table_id);

    /**
     * ดึงข้อความในห้องสนทนา (แชทเก่า)
     * @param string $subscriber_id (PSID, IGID, หรือ Line User ID)
     * @param int $page_table_id
     * @return array
     */
    public function get_conversation_replies($subscriber_id, $page_table_id);

    /**
     * ส่งข้อความตอบกลับ
     * @param array $reply_data (ข้อมูลเช่น subscriber_id, message)
     * @return array
     */
    public function send_reply($reply_data);

    /**
     * ส่ง Postback (เช่น ปุ่ม Template)
     * @param array $reply_data
     * @return array
     */
    public function send_postback_reply($reply_data);
}
