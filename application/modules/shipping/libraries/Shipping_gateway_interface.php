<?php
// application/modules/shipping/libraries/Kerry_express_gateway.php

require_once APPPATH . 'modules/shipping/libraries/Shipping_gateway_interface.php';

class Kerry_express_gateway implements ShippingGatewayInterface
{
    private $api_key;
    private $api_secret;
    private $api_endpoint;
    private $ci; // CodeIgniter instance

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->model('shipping/shipping_model');

        // ดึง config จาก database
        $shipping_config = $this->ci->shipping_model->get_shipping_gateway_config('kerry_express');
        if ($shipping_config) {
            $this->set_config($shipping_config);
        }
    }

    public function set_config(array $config)
    {
        $this->api_key = $config['api_key'] ?? '';
        $this->api_secret = $config['api_secret'] ?? '';
        $this->api_endpoint = $config['api_endpoint'] ?? 'https://api.kerryexpress.com/v1/'; // ตัวอย่าง URL
    }

    private function _call_api($method, $path, $data = [])
    {
        // Logic สำหรับเรียก API ของ Kerry Express
        // อาจจะต้องใช้ cURL หรือ Guzzle HTTP client
        // รวมถึงการสร้าง Header สำหรับ Authentication (ตามเอกสาร API ของ Kerry)

        $url = $this->api_endpoint . $path;
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->_generate_token(), // สมมติว่ามีระบบ Token
        ];

        // ตัวอย่าง cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'GET') {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if ($http_code >= 200 && $http_code < 300) {
            return ['status' => 'success', 'data' => $response_data];
        } else {
            return ['status' => 'failed', 'message' => $response_data['message'] ?? 'API error', 'code' => $http_code];
        }
    }

    private function _generate_token()
    {
        // Logic สำหรับสร้าง/ดึง API Token ของ Kerry (ตามเอกสาร API ของ Kerry)
        // อาจจะต้องเรียก API เพื่อขอ Token ด้วย api_key และ api_secret
        // และอาจจะ cache Token ไว้เพื่อลดการเรียกซ้ำ
        return 'YOUR_GENERATED_KERRY_TOKEN'; // Placeholder
    }

    public function create_shipment(array $shipment_details)
    {
        // Map $shipment_details ไปยังโครงสร้าง Request ของ Kerry API
        $payload = [
            'recipient_name' => $shipment_details['recipient_name'],
            'recipient_address' => $shipment_details['recipient_address'],
            'recipient_phone' => $shipment_details['recipient_phone'],
            'package_weight' => $shipment_details['weight'], // kg
            'package_dimension' => [
                'width' => $shipment_details['width'],
                'height' => $shipment_details['height'],
                'depth' => $shipment_details['depth'],
            ],
            'order_reference_id' => $shipment_details['order_id'], // อ้างอิง ID คำสั่งซื้อของคุณ
            // ... อื่นๆ ตามที่ Kerry API ต้องการ
        ];

        $api_response = $this->_call_api('POST', 'shipments', $payload);

        if ($api_response['status'] === 'success') {
            return [
                'status'        => 'success',
                'message'       => $this->ci->lang->line('Shipment created successfully.'),
                'tracking_id'   => $api_response['data']['tracking_number'],
                'label_url'     => $api_response['data']['label_url'] ?? null,
                'gateway_response_data' => $api_response['data'] // เก็บข้อมูล response เต็มๆ
            ];
        } else {
            return [
                'status'        => 'failed',
                'message'       => $this->ci->lang->line('Failed to create shipment: ') . ($api_response['message'] ?? 'Unknown error'),
                'tracking_id'   => null,
                'label_url'     => null,
                'gateway_response_data' => $api_response // เก็บข้อมูล response เต็มๆ
            ];
        }
    }

    public function get_tracking_status(string $tracking_id)
    {
        $api_response = $this->_call_api('GET', "tracking/{$tracking_id}");

        if ($api_response['status'] === 'success') {
            // Map response ของ Kerry ไปยังโครงสร้างที่เราต้องการ
            $tracking_status_map = [
                'DELIVERED' => 'delivered',
                'IN_TRANSIT' => 'in_transit',
                'PENDING' => 'pending',
                'EXCEPTION' => 'exception',
                // ... อื่นๆ
            ];

            return [
                'status'        => 'success',
                'message'       => $this->ci->lang->line('Tracking status retrieved successfully.'),
                'tracking_status' => $tracking_status_map[$api_response['data']['current_status']] ?? 'unknown',
                'events'        => $api_response['data']['tracking_events'] ?? [], // รายละเอียดแต่ละขั้นตอน
                'raw_data'      => $api_response['data']
            ];
        } else {
            return [
                'status'        => 'failed',
                'message'       => $this->ci->lang->line('Failed to retrieve tracking status: ') . ($api_response['message'] ?? 'Unknown error'),
                'tracking_status' => 'unknown',
                'events'        => [],
                'raw_data'      => $api_response
            ];
        }
    }

    public function cancel_shipment(string $tracking_id)
    {
        // Logic สำหรับยกเลิกพัสดุผ่าน Kerry API
        $api_response = $this->_call_api('POST', "shipments/{$tracking_id}/cancel"); // ตัวอย่าง endpoint

        if ($api_response['status'] === 'success') {
            return [
                'status'        => 'success',
                'message'       => $this->ci->lang->line('Shipment cancelled successfully.'),
                'raw_data'      => $api_response['data']
            ];
        } else {
            return [
                'status'        => 'failed',
                'message'       => $this->ci->lang->line('Failed to cancel shipment: ') . ($api_response['message'] ?? 'Unknown error'),
                'raw_data'      => $api_response
            ];
        }
    }

    public function get_label_url(string $tracking_id)
    {
        // Logic สำหรับดึง URL ของ Shipping Label (อาจจะต้องมี endpoint แยกหรืออยู่ใน response ของ create_shipment)
        $api_response = $this->_call_api('GET', "shipments/{$tracking_id}/label_url"); // ตัวอย่าง endpoint
        if ($api_response['status'] === 'success' && isset($api_response['data']['label_url'])) {
            return $api_response['data']['label_url'];
        }
        return null;
    }

    public function handle_webhook(array $webhook_data)
    {
        // Logic สำหรับตรวจสอบและจัดการ Webhook จาก Kerry Express
        // Webhook จะส่งข้อมูลสถานะการเปลี่ยนแปลงมาให้
        // คุณต้องตรวจสอบ Signature ของ Webhook เพื่อความปลอดภัย
        // และแยกแยะข้อมูลเพื่อดึง tracking_id และ new_status

        // ตัวอย่าง: Kerry อาจส่ง JSON มาแบบนี้
        /*
        {
            "event": "shipment.status_updated",
            "data": {
                "tracking_number": "KP000000001",
                "old_status": "IN_TRANSIT",
                "new_status": "DELIVERED",
                "timestamp": "2023-10-27T10:00:00Z",
                "metadata": { ... }
            },
            "signature": "..." // สำหรับ verify webhook
        }
        */

        $tracking_id = $webhook_data['data']['tracking_number'] ?? null;
        $new_status_raw = $webhook_data['data']['new_status'] ?? null;
        $event_type = $webhook_data['event'] ?? null;

        if (!$tracking_id || !$new_status_raw) {
            return ['status' => 'failed', 'message' => 'Invalid webhook data.'];
        }

        // Map status
        $tracking_status_map = [
            'DELIVERED' => 'delivered',
            'IN_TRANSIT' => 'in_transit',
            'PENDING' => 'pending',
            'EXCEPTION' => 'exception',
            'CANCELLED' => 'cancelled',
            // ... อื่นๆ
        ];
        $new_status = $tracking_status_map[$new_status_raw] ?? 'unknown';

        // บันทึก/อัปเดตสถานะใน database
        // $this->ci->shipping_model->update_shipment_status($tracking_id, $new_status, $webhook_data);

        return [
            'status' => 'success',
            'message' => 'Webhook processed.',
            'tracking_id' => $tracking_id,
            'new_status' => $new_status
        ];
    }
}
