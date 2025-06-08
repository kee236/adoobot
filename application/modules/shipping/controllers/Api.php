<?php
// application/modules/shipping/controllers/Api.php

require_once APPPATH . 'modules/api/controllers/Base_api_controller.php'; // สมมติว่ามี Base_api_controller ใน module api

class Api extends Base_api_controller
{
    private $active_shipping_gateways = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('shipping/shipping_model');
        $this->load->language('shipping/shipping');

        // โหลดและตั้งค่า Shipping Gateways ที่ใช้งานอยู่
        $this->_load_active_shipping_gateways();
    }

    /**
     * โหลดและตั้งค่า Shipping Gateways ที่ใช้งานอยู่
     * ควรดึงข้อมูลจาก DB หรือ Cache เพื่อประสิทธิภาพ
     */
    private function _load_active_shipping_gateways()
    {
        // ควรดึงรายชื่อ Gateway ที่ใช้งานได้จาก config/DB
        $available_gateways = ['kerry_express', 'flash_express', 'jt_express'];

        foreach ($available_gateways as $gateway_name) {
            $library_name = ucfirst(str_replace('_', '', $gateway_name)) . '_gateway';
            $library_path = 'shipping/' . $library_name;

            if (file_exists(APPPATH . 'modules/shipping/libraries/' . $library_name . '.php')) {
                $this->load->library($library_path);
                if (isset($this->$library_name) && $this->$library_name instanceof ShippingGatewayInterface) {
                    $config = $this->shipping_model->get_shipping_gateway_config($gateway_name);
                    if ($config) {
                        $this->$library_name->set_config($config);
                        $this->active_shipping_gateways[$gateway_name] = $this->$library_name;
                    }
                }
            }
        }
    }

    /**
     * ฟังก์ชันสำหรับตรวจสอบ API Key
     * (ควรถูกเรียกใน Base_api_controller หรือใน constructor)
     * หรืออาจจะใช้ CodeIgniter Hooks สำหรับ Authentication
     */
    protected function _authenticate_api_key()
    {
        $api_key = $this->input->get_request_header('X-API-Key') ?? $this->input->get('api_key');

        if (empty($api_key)) {
            $this->response_error(401, $this->lang->line('API Key is missing.'));
        }

        // ตรวจสอบ API Key กับฐานข้อมูล (สมมติว่ามีตาราง api_keys)
        $this->load->model('api_model'); // สมมติว่ามี api_model ใน module api
        $is_valid = $this->api_model->is_valid_api_key($api_key);

        if (!$is_valid) {
            $this->response_error(403, $this->lang->line('Invalid API Key.'));
        }

        // สามารถบันทึก log การใช้งาน API หรือตรวจสอบ Rate Limiting ที่นี่
        // $this->api_model->log_api_request($api_key, $this->router->fetch_method());
    }

    /**
     * [POST] /api/v1/shipping/shipments
     * สร้างรายการจัดส่งใหม่
     * @param json payload
     * {
     * "order_id": "ORD12345",
     * "shipping_provider": "kerry_express",
     * "recipient": {
     * "name": "สมชาย ใจดี",
     * "address": "123 หมู่ 5 ต.บางปลา อ.บางพลี จ.สมุทรปราการ 10540",
     * "phone": "0812345678"
     * },
     * "package": {
     * "weight": 2.5,
     * "width": 10,
     * "height": 5,
     * "depth": 15
     * },
     * "items": [
     * {"name": "เสื้อยืด", "quantity": 1, "price": 250},
     * {"name": "กางเกงยีนส์", "quantity": 1, "price": 500}
     * ],
     * "notes": "ห้ามโยน"
     * }
     */
    public function create_shipment_post()
    {
        $this->_authenticate_api_key(); // ตรวจสอบ API Key

        $request_data = $this->input->raw_input_stream;
        $payload = json_decode($request_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response_error(400, $this->lang->line('Invalid JSON payload.'));
        }

        // Validate Input
        $required_fields = ['order_id', 'shipping_provider', 'recipient', 'package'];
        foreach ($required_fields as $field) {
            if (!isset($payload[$field])) {
                $this->response_error(400, sprintf($this->lang->line('Missing required field: %s'), $field));
            }
        }
        if (!isset($payload['recipient']['name']) || !isset($payload['recipient']['address']) || !isset($payload['recipient']['phone'])) {
            $this->response_error(400, $this->lang->line('Recipient details (name, address, phone) are required.'));
        }
        if (!isset($payload['package']['weight'])) {
            $this->response_error(400, $this->lang->line('Package weight is required.'));
        }


        $selected_gateway = $payload['shipping_provider'];
        if (!isset($this->active_shipping_gateways[$selected_gateway])) {
            $this->response_error(400, $this->lang->line('Invalid or unconfigured shipping provider.'));
        }

        $gateway = $this->active_shipping_gateways[$selected_gateway];

        $shipment_details = [
            'order_id'          => $payload['order_id'],
            'recipient_name'    => $payload['recipient']['name'],
            'recipient_address' => $payload['recipient']['address'],
            'recipient_phone'   => $payload['recipient']['phone'],
            'weight'            => $payload['package']['weight'],
            'width'             => $payload['package']['width'] ?? 0,
            'height'            => $payload['package']['height'] ?? 0,
            'depth'             => $payload['package']['depth'] ?? 0,
            'items'             => $payload['items'] ?? [],
            'notes'             => $payload['notes'] ?? '',
            'user_id'           => $this->api_user_id // ID ของผู้ใช้/บัญชี API ที่เรียก (ดึงมาจาก authentication)
        ];

        $result = $gateway->create_shipment($shipment_details);

        if ($result['status'] === 'success') {
            // บันทึกข้อมูลการจัดส่งลงใน Database
            $shipping_record_data = [
                'order_id'                  => $shipment_details['order_id'],
                'shipping_provider'         => $selected_gateway,
                'tracking_id'               => $result['tracking_id'],
                'shipping_label_url'        => $result['label_url'] ?? null,
                'status'                    => 'pending',
                'user_id'                   => $shipment_details['user_id'],
                'recipient_name'            => $shipment_details['recipient_name'],
                'recipient_address'         => $shipment_details['recipient_address'],
                'recipient_phone'           => $shipment_details['recipient_phone'],
                'weight'                    => $shipment_details['weight'],
                'width'                     => $shipment_details['width'],
                'height'                    => $shipment_details['height'],
                'depth'                     => $shipment_details['depth'],
                'raw_gateway_response'      => json_encode($result['gateway_response_data'] ?? []),
                'created_at'                => date('Y-m-d H:i:s'),
                'last_updated'              => date('Y-m-d H:i:s')
            ];
            $shipment_db_id = $this->shipping_model->create_shipment_record($shipping_record_data);

            $response_data = [
                'status'        => 'success',
                'message'       => $this->lang->line('Shipment created successfully.'),
                'tracking_id'   => $result['tracking_id'],
                'shipping_label_url' => $result['label_url'],
                'internal_shipment_id' => $shipment_db_id,
                'provider_response' => $result['gateway_response_data'] ?? []
            ];
            $this->response_success($response_data);
        } else {
            $this->response_error(500, $this->lang->line('Failed to create shipment: ') . $result['message'], $result['raw_data'] ?? null);
        }
    }

    /**
     * [GET] /api/v1/shipping/tracking/{tracking_id}
     * ดึงสถานะการติดตามพัสดุ
     * @param string $tracking_id เลขติดตามพัสดุ
     */
    public function get_tracking_get($tracking_id)
    {
        $this->_authenticate_api_key();

        if (empty($tracking_id)) {
            $this->response_error(400, $this->lang->line('Tracking ID is missing.'));
        }

        $shipment_info = $this->shipping_model->get_shipment_by_tracking_id($tracking_id);

        if (!$shipment_info) {
            $this->response_error(404, $this->lang->line('Shipment not found.'));
        }

        $gateway_name = $shipment_info['shipping_provider'];
        if (!isset($this->active_shipping_gateways[$gateway_name])) {
            $this->response_error(500, $this->lang->line('Shipping provider not configured for this shipment.'));
        }

        $gateway = $this->active_shipping_gateways[$gateway_name];
        $tracking_result = $gateway->get_tracking_status($tracking_id);

        if ($tracking_result['status'] === 'success') {
            // อัปเดตสถานะใน DB หากมีการเปลี่ยนแปลงจาก Gateway
            if ($tracking_result['tracking_status'] !== $shipment_info['status']) {
                $this->shipping_model->update_shipment_status($tracking_id, $tracking_result['tracking_status']);
            }

            $response_data = [
                'status'        => 'success',
                'tracking_id'   => $tracking_id,
                'current_status' => $tracking_result['tracking_status'],
                'status_message' => $tracking_result['message'],
                'tracking_events' => $tracking_result['events'],
                'raw_provider_data' => $tracking_result['raw_data'] ?? []
            ];
            $this->response_success($response_data);
        } else {
            $this->response_error(500, $this->lang->line('Failed to retrieve tracking status: ') . $tracking_result['message'], $tracking_result['raw_data'] ?? null);
        }
    }

    /**
     * [PUT] /api/v1/shipping/shipments/{tracking_id}/cancel
     * ยกเลิกรายการจัดส่ง
     * @param string $tracking_id เลขติดตามพัสดุ
     */
    public function cancel_shipment_put($tracking_id)
    {
        $this->_authenticate_api_key();

        if (empty($tracking_id)) {
            $this->response_error(400, $this->lang->line('Tracking ID is missing.'));
        }

        $shipment_info = $this->shipping_model->get_shipment_by_tracking_id($tracking_id);

        if (!$shipment_info) {
            $this->response_error(404, $this->lang->line('Shipment not found.'));
        }

        // ตรวจสอบสถานะว่ายกเลิกได้หรือไม่ (เช่น ยังไม่ถูกจัดส่ง)
        if (in_array($shipment_info['status'], ['delivered', 'cancelled', 'failed'])) {
            $this->response_error(400, $this->lang->line('Shipment cannot be cancelled in its current status.'));
        }

        $gateway_name = $shipment_info['shipping_provider'];
        if (!isset($this->active_shipping_gateways[$gateway_name])) {
            $this->response_error(500, $this->lang->line('Shipping provider not configured for this shipment.'));
        }

        $gateway = $this->active_shipping_gateways[$gateway_name];
        $result = $gateway->cancel_shipment($tracking_id);

        if ($result['status'] === 'success') {
            // อัปเดตสถานะใน database
            $this->shipping_model->update_shipment_status($tracking_id, 'cancelled');
            $this->response_success([
                'status'        => 'success',
                'message'       => $this->lang->line('Shipment cancelled successfully.'),
                'tracking_id'   => $tracking_id
            ]);
        } else {
            $this->response_error(500, $this->lang->line('Failed to cancel shipment: ') . $result['message'], $result['raw_data'] ?? null);
        }
    }


    /**
     * [GET] /api/v1/shipping/labels/{tracking_id}
     * ดึง URL สำหรับพิมพ์ Shipping Label
     * @param string $tracking_id เลขติดตามพัสดุ
     */
    public function get_shipping_label_get($tracking_id)
    {
        $this->_authenticate_api_key();

        if (empty($tracking_id)) {
            $this->response_error(400, $this->lang->line('Tracking ID is missing.'));
        }

        $shipment_info = $this->shipping_model->get_shipment_by_tracking_id($tracking_id);

        if (!$shipment_info) {
            $this->response_error(404, $this->lang->line('Shipment not found.'));
        }

        if (empty($shipment_info['shipping_label_url'])) {
            // ลองเรียกจาก Gateway อีกครั้ง หากไม่มี URL ใน DB
            $gateway_name = $shipment_info['shipping_provider'];
            if (isset($this->active_shipping_gateways[$gateway_name])) {
                $gateway = $this->active_shipping_gateways[$gateway_name];
                $label_url = $gateway->get_label_url($tracking_id);
                if ($label_url) {
                    // อัปเดต DB ด้วย URL ที่ได้มา
                    $this->shipping_model->update_shipment_status($tracking_id, $shipment_info['status'], ['shipping_label_url' => $label_url]);
                    $shipment_info['shipping_label_url'] = $label_url;
                }
            }
        }

        if ($shipment_info['shipping_label_url']) {
            $this->response_success([
                'status'        => 'success',
                'tracking_id'   => $tracking_id,
                'shipping_label_url' => $shipment_info['shipping_label_url']
            ]);
        } else {
            $this->response_error(404, $this->lang->line('Shipping label not found for this shipment.'));
        }
    }

    /**
     * Webhook Endpoint สำหรับรับการแจ้งเตือนจากบริษัทขนส่ง
     * URL: /api/v1/shipping/webhook/{gateway_name}
     * ตัวอย่าง: /api/v1/shipping/webhook/kerry_express
     */
    public function webhook_post($gateway_name)
    {
        // ไม่ต้องตรวจสอบ API Key เพราะเป็น Callback จากภายนอก
        // แต่ต้องมีการตรวจสอบ Signature/IP Whitelist เพื่อความปลอดภัย
        $raw_webhook_data = file_get_contents('php://input');
        $webhook_data = json_decode($raw_webhook_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'Shipping Webhook: Invalid JSON from ' . $gateway_name . '. Raw: ' . $raw_webhook_data);
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON payload.']);
            return;
        }

        if (!isset($this->active_shipping_gateways[$gateway_name])) {
            log_message('error', 'Shipping Webhook: Invalid or unconfigured gateway name ' . $gateway_name);
            http_response_code(404);
            echo json_encode(['error' => 'Invalid shipping gateway.']);
            return;
        }

        $gateway = $this->active_shipping_gateways[$gateway_name];

        // *** สำคัญมาก: ตรวจสอบ Webhook Signature/Security ของ Gateway นั้นๆ ***
        // เช่น $gateway->verify_webhook_signature($raw_webhook_data, $_SERVER['HTTP_X_KERRY_SIGNATURE'] ?? '');
        // หากไม่ถูกต้อง ให้ return 403
        // if (!$gateway->verify_webhook_signature($raw_webhook_data, $this->input->get_request_header('X-Signature'))) {
        //     log_message('warning', 'Shipping Webhook: Signature verification failed for ' . $gateway_name . '. IP: ' . $this->input->ip_address());
        //     http_response_code(403);
        //     echo json_encode(['error' => 'Signature verification failed.']);
        //     return;
        // }


        $result = $gateway->handle_webhook($webhook_data);

        if ($result['status'] === 'success') {
            $tracking_id = $result['tracking_id'];
            $new_status = $result['new_status'];
            $this->shipping_model->update_shipment_status($tracking_id, $new_status, ['last_webhook_data' => $raw_webhook_data]);

            http_response_code(200);
            echo json_encode(['success' => true, 'message' => $this->lang->line('Webhook processed successfully.'), 'tracking_id' => $tracking_id, 'new_status' => $new_status]);
        } else {
            log_message('error', 'Shipping Webhook: Failed to process for ' . $gateway_name . '. Error: ' . $result['message']);
            http_response_code(400);
            echo json_encode(['error' => $this->lang->line('Failed to process webhook: ') . $result['message']]);
        }
    }
}
