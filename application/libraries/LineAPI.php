



// ... ใน class LineAPI { ...
public function verify_webhook_signature($body, $signature) {
    // ตรวจสอบ Channel Secret ใน config
    if (empty($this->channel_secret)) {
        log_message('error', 'LINE Channel Secret is not configured for webhook verification.');
        return false;
    }
    // ใช้ hash_hmac เพื่อสร้าง signature และเปรียบเทียบ
    $hash = hash_hmac('sha256', $body, $this->channel_secret, true);
    $expected_signature = base64_encode($hash);
    return hash_equals($expected_signature, $signature);
}

public function handle_webhook_event($event) {
    // โค้ดสำหรับจัดการแต่ละประเภทของ LINE Event
    // เช่น message, follow, unfollow, postback
    switch ($event['type']) {
        case 'message':
            $message_type = $event['message']['type'];
            $reply_token = $event['replyToken'];
            $user_id = $event['source']['userId'];

            if ($message_type == 'text') {
                $text = $event['message']['text'];
                log_message('info', 'LINE Text Message from ' . $user_id . ': ' . $text);
                // ตัวอย่าง: หากข้อความเป็น "สินค้า" ให้ตอบกลับรายการสินค้า
                if (mb_strtolower($text, 'UTF-8') == 'สินค้า') {
                    $this->ci->load->model('product_model');
                    $products = $this->ci->product_model->get_all_products(5); // ดึง 5 สินค้าล่าสุด
                    $product_list_text = "สินค้าแนะนำ:\n";
                    foreach ($products as $p) {
                        $product_list_text .= "- " . $p['name'] . " (" . $p['price'] . " THB)\n";
                    }
                    $this->reply_message($reply_token, $product_list_text);
                } else {
                    $this->reply_message($reply_token, 'คุณส่งข้อความมาว่า: ' . $text . ' ฉันยังไม่เข้าใจค่ะ');
                }
            }
            // เพิ่มการจัดการ message type อื่นๆ เช่น image, sticker, location
            break;
        case 'follow':
            $user_id = $event['source']['userId'];
            log_message('info', 'LINE User followed: ' . $user_id);
            // บันทึกผู้ติดตามใหม่ลงฐานข้อมูล
            // ส่งข้อความต้อนรับ
            $this->reply_message($event['replyToken'], 'สวัสดีครับ! ยินดีต้อนรับสู่ร้านของเรา มีอะไรให้ช่วยไหมครับ?');
            break;
        case 'postback':
            // จัดการ postback event จาก Rich Menu หรือ Template Message
            $data = $event['postback']['data'];
            log_message('info', 'LINE Postback Data: ' . $data);
            // เช่น หาก data เป็น 'action=view_cart' ให้ส่งลิงก์ตะกร้าสินค้า
            break;
        // เพิ่ม case สำหรับ event type อื่นๆ
        default:
            log_message('info', 'LINE Unhandled Event Type: ' . $event['type']);
            break;
    }
}
// ... (เมธอด send_message, get_myshop_products และอื่นๆ)
// ... (เพิ่มเมธอด reply_message สำหรับตอบกลับ)
public function reply_message($reply_token, $message_text) {
    if (empty($this->channel_access_token)) {
        log_message('error', 'LINE Channel Access Token is not configured.');
        return false;
    }

    $messages = [
        [
            'type' => 'text',
            'text' => $message_text,
        ],
    ];

    $data = [
        'replyToken' => $reply_token,
        'messages' => $messages,
    ];

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $this->channel_access_token,
    ];

    $ch = curl_init('https://api.line.me/v2/bot/message/reply'); // Reply message API
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        log_message('info', 'LINE reply message sent successfully.');
        return true;
    } else {
        log_message('error', 'Failed to send LINE reply message. HTTP Code: ' . $http_code . ', Response: ' . $result);
        return false;
    }
}
// ...
