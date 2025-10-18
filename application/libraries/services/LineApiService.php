<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ใช้ Guzzle ที่โหลดผ่าน Composer
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class LineApiService {
    
    protected $ci;
    protected $client; // Guzzle HTTP Client
    protected $channel_access_token;
    protected $channel_secret;

    const API_BASE_URL = 'https://api.line.me/v2/bot/';

    public function __construct() {
        $this->ci =& get_instance();
        $this->client = new Client([
            'base_uri' => self::API_BASE_URL,
            'timeout'  => 5.0,
        ]);
    }

    /**
     * ตั้งค่า Token สำหรับ Channel ที่จะทำงานด้วย
     * @param int $line_channel_table_id ID จากตาราง line_oa_channels
     */
    public function set_channel($line_channel_table_id) {
        $channel_data = $this->ci->basic->get_data('line_oa_channels', ['where' => ['id' => $line_channel_table_id]]);
        if(empty($channel_data)) {
            throw new Exception('LINE Channel configuration not found.');
        }
        $this->channel_access_token = $channel_data[0]['channel_access_token'];
        $this->channel_secret = $channel_data[0]['channel_secret'];
    }
    
    /**
     * ตรวจสอบ Signature ของ Webhook (สำคัญมากสำหรับ LINE)
     * @param string $request_body
     * @param string $signature
     * @return bool
     */
    public function validate_webhook_signature($request_body, $signature) {
        if (empty($this->channel_secret)) {
            throw new Exception('Channel Secret is not set.');
        }
        $hash = hash_hmac('sha256', $request_body, $this->channel_secret, true);
        $base64_hash = base64_encode($hash);
        return hash_equals($base64_hash, $signature);
    }

    /**
     * ส่งข้อความตอบกลับ (Reply)
     * @param string $reply_token
     * @param array $messages (รูปแบบ Array ของ Message Object)
     * @return array
     */
    public function send_reply_message($reply_token, $messages) {
        return $this->send_request('message/reply', 'POST', [
            'replyToken' => $reply_token,
            'messages' => $messages
        ]);
    }

    /**
     * ส่งข้อความหาผู้ใช้โดยตรง (Push)
     * @param string $to_user_id (LINE User ID)
     * @param array $messages (รูปแบบ Array ของ Message Object)
     * @return array
     */
    public function send_push_message($to_user_id, $messages) {
        return $this->send_request('message/push', 'POST', [
            'to' => $to_user_id,
            'messages' => $messages
        ]);
    }

    /**
     * ดึงข้อมูลโปรไฟล์ผู้ใช้
     * @param string $user_id
     * @return array
     */
    public function get_user_profile($user_id) {
        return $this->send_request("profile/{$user_id}", 'GET');
    }

    /**
     * ฟังก์ชัน Helper สำหรับยิง API
     */
    private function send_request($endpoint, $method, $body = []) {
        if (empty($this->channel_access_token)) {
            throw new Exception('Channel Access Token is not set.');
        }

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->channel_access_token,
                'Content-Type'  => 'application/json'
            ]
        ];

        if ($method == 'POST') {
            $options['json'] = $body;
        }

        try {
            $response = $this->client->request($method, $endpoint, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Log error
            log_message('error', 'LINE API Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
