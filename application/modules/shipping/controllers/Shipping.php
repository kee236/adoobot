<?php
// application/modules/shipping/controllers/Shipping.php

class Shipping extends CI_Controller
{
    private $active_shipping_gateways = [];

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1) {
            redirect('home/login_page', 'location');
        }

        $this->load->model('shipping/shipping_model');
        $this->load->language('shipping/shipping'); // โหลดไฟล์ภาษาของ module

        // โหลดและตั้งค่า Shipping Gateways ที่ใช้งานอยู่
        $this->_load_active_shipping_gateways();
    }

    private function _load_active_shipping_gateways()
    {
        // ดึงรายชื่อ Gateway ที่ใช้งานได้ (เช่น จาก DB หรือ config file)
        $available_gateways = ['kerry_express', 'flash_express', 'jt_express']; // ตัวอย่าง

        foreach ($available_gateways as $gateway_name) {
            $library_name = ucfirst(str_replace('_', '', $gateway_name)) . '_gateway'; // Kerry_express_gateway
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
     * แสดงหน้าตั้งค่า Shipping Gateways (สำหรับ Admin)
     */
    public function settings()
    {
        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/access_forbidden', 'location');
        }

        $data['page_title'] = $this->lang->line('Shipping Gateway Settings');
        $data['body'] = 'shipping/settings';
        $data['active_gateways'] = array_keys($this->active_shipping_gateways); // ส่งชื่อ Gateway ที่มี Library ไปให้ View
        $data['gateway_configs'] = [];

        foreach ($data['active_gateways'] as $gateway_name) {
            $data['gateway_configs'][$gateway_name] = $this->shipping_model->get_shipping_gateway_config($gateway_name);
        }

        $this->load->view('home_template', $data); // ใช้ template หลักของระบบ
    }

    /**
     * บันทึกการตั้งค่า Shipping Gateway (สำหรับ Admin)
     */
    public function save_settings()
    {
        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/access_forbidden', 'location');
        }

        $this->form_validation->set_rules('gateway_name', $this->lang->line('Gateway Name'), 'trim|required|in_list[kerry_express,flash_express,jt_express]'); // เพิ่มขนส่งใหม่ๆ ตรงนี้
        $this->form_validation->set_rules('api_key', $this->lang->line('API Key'), 'trim|required');
        $this->form_validation->set_rules('api_secret', $this->lang->line('API Secret'), 'trim'); // อาจไม่จำเป็นต้อง required เสมอไป
        $this->form_validation->set_rules('api_endpoint', $this->lang->line('API Endpoint'), 'trim|valid_url');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('shipping/settings', 'location');
        } else {
            $gateway_name = $this->input->post('gateway_name', true);
            $config_data = [
                'api_key' => $this->input->post('api_key', true),
                'api_secret' => $this->input->post('api_secret', true),
                'api_endpoint' => $this->input->post('api_endpoint', true),
            ];

            if ($this->shipping_model->save_shipping_gateway_config($gateway_name, $config_data)) {
                $this->session->set_flashdata('success_message', $this->lang->line('Shipping gateway settings saved successfully.'));
            } else {
                $this->session->set_flashdata('error_message', $this->lang->line('Failed to save shipping gateway settings.'));
            }
            redirect('shipping/settings', 'location');
        }
    }

    /**
     * แสดงฟอร์มสำหรับสร้างรายการจัดส่งใหม่
     * @param int $order_id (Optional) ID คำสั่งซื้อที่ต้องการสร้างรายการจัดส่ง
     */
    public function create_shipment_form($order_id = null)
    {
        // ตรวจสอบสิทธิ์ผู้ใช้ (อาจจะเป็น Admin หรือ User ที่มีสิทธิ์สร้าง Shipment)
        // ...

        $data['page_title'] = $this->lang->line('Create New Shipment');
        $data['body'] = 'shipping/create_shipment';
        $data['order_id'] = $order_id;
        $data['available_gateways'] = $this->active_shipping_gateways; // ส่ง Gateway ที่พร้อมใช้งาน

        // ดึงข้อมูลคำสั่งซื้อจาก Order Model เพื่อกรอกในฟอร์มอัตโนมัติ
        // $this->load->model('order_model');
        // $data['order_info'] = $this->order_model->get_order_details($order_id);

        $this->load->view('home_template', $data);
    }

    /**
     * ประมวลผลการสร้างรายการจัดส่ง
     */
    public function process_create_shipment()
    {
        // ตรวจสอบสิทธิ์ผู้ใช้
        // ...

        $this->form_validation->set_rules('order_id', $this->lang->line('Order ID'), 'trim|required');
        $this->form_validation->set_rules('selected_gateway', $this->lang->line('Shipping Provider'), 'trim|required');
        $this->form_validation->set_rules('recipient_name', $this->lang->line('Recipient Name'), 'trim|required');
        $this->form_validation->set_rules('recipient_address', $this->lang->line('Recipient Address'), 'trim|required');
        $this->form_validation->set_rules('recipient_phone', $this->lang->line('Recipient Phone'), 'trim|required');
        $this->form_validation->set_rules('weight', $this->lang->line('Weight'), 'trim|required|numeric');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('shipping/create_shipment_form/' . $this->input->post('order_id'), 'location');
        }

        $order_id = $this->input->post('order_id', true);
        $selected_gateway = $this->input->post('selected_gateway', true);

        if (!isset($this->active_shipping_gateways[$selected_gateway])) {
            $this->session->set_flashdata('error_message', $this->lang->line('Invalid shipping provider selected.'));
            redirect('shipping/create_shipment_form/' . $order_id, 'location');
        }

        $gateway = $this->active_shipping_gateways[$selected_gateway];

        $shipment_details = [
            'order_id'          => $order_id,
            'recipient_name'    => $this->input->post('recipient_name', true),
            'recipient_address' => $this->input->post('recipient_address', true),
            'recipient_phone'   => $this->input->post('recipient_phone', true),
            'weight'            => $this->input->post('weight'),
            'width'             => $this->input->post('width') ?? 0,
            'height'            => $this->input->post('height') ?? 0,
            'depth'             => $this->input->post('depth') ?? 0,
            'user_id'           => $this->session->userdata('user_id') // ผู้ใช้ที่สร้างรายการ
        ];

        $result = $gateway->create_shipment($shipment_details);

        if ($result['status'] === 'success') {
            // บันทึกข้อมูลการจัดส่งลงใน Database
            $shipping_record_data = [
                'order_id'                  => $order_id,
                'shipping_provider'         => $selected_gateway,
                'tracking_id'               => $result['tracking_id'],
                'shipping_label_url'        => $result['label_url'] ?? null,
                'status'                    => 'pending', // สถานะเริ่มต้น
                'user_id'                   => $this->session->userdata('user_id'),
                'created_at'                => date('Y-m-d H:i:s'),
                'raw_gateway_response'      => json_encode($result['gateway_response_data'] ?? [])
            ];
            $this->shipping_model->create_shipment_record($shipping_record_data);

            $this->session->set_flashdata('success_message', $this->lang->line('Shipment created successfully!') . ' ' . $this->lang->line('Tracking ID') . ': ' . $result['tracking_id']);
            redirect('shipping/shipment_list', 'location'); // หรือไปหน้าแสดงรายละเอียดการจัดส่งที่สร้าง
        } else {
            $this->session->set_flashdata('error_message', $this->lang->line('Failed to create shipment: ') . $result['message']);
            redirect('shipping/create_shipment_form/' . $order_id, 'location');
        }
    }

    /**
     * แสดงรายการจัดส่งทั้งหมด
     */
    public function shipment_list()
    {
        // ตรวจสอบสิทธิ์ผู้ใช้ (Admin ดูทั้งหมด, User ดูเฉพาะของตัวเอง)
        $user_id = ($this->session->userdata('user_type') == 'Admin') ? null : $this->session->userdata('user_id');

        $data['page_title'] = $this->lang->line('Shipment List');
        $data['body'] = 'shipping/shipment_list';
        $data['shipments'] = $this->shipping_model->get_all_shipments($user_id);

        $this->load->view('home_template', $data);
    }

    /**
     * แสดงรายละเอียดการติดตามพัสดุ
     * @param string $tracking_id
     */
    public function tracking_details($tracking_id = null)
    {
        if (empty($tracking_id)) {
            $this->session->set_flashdata('error_message', $this->lang->line('Invalid tracking ID.'));
            redirect('shipping/shipment_list', 'location');
        }

        $shipment_info = $this->shipping_model->get_shipment_by_tracking_id($tracking_id);

        if (!$shipment_info) {
            $this->session->set_flashdata('error_message', $this->lang->line('Shipment not found.'));
            redirect('shipping/shipment_list', 'location');
        }

        // ตรวจสอบสิทธิ์ในการดู (เช่น ต้องเป็นเจ้าของ หรือเป็น Admin)
        if ($this->session->userdata('user_type') != 'Admin' && $shipment_info['user_id'] != $this->session->userdata('user_id')) {
            redirect('home/access_forbidden', 'location');
        }

        $data['page_title'] = $this->lang->line('Tracking Details');
        $data['body'] = 'shipping/tracking_details';
        $data['shipment_info'] = $shipment_info;

        // ดึงสถานะปัจจุบันจาก Gateway (หรือจาก DB ถ้ามีการอัปเดตบ่อยๆ ผ่าน Webhook)
        $gateway_name = $shipment_info['shipping_provider'];
        if (isset($this->active_shipping_gateways[$gateway_name])) {
            $tracking_result = $this->active_shipping_gateways[$gateway_name]->get_tracking_status($tracking_id);
            $data['tracking_data'] = $tracking_result;
            // อัปเดตสถานะล่าสุดใน DB หากมีการเปลี่ยนแปลง
            if ($tracking_result['status'] === 'success' && $tracking_result['tracking_status'] !== $shipment_info['status']) {
                $this->shipping_model->update_shipment_status($tracking_id, $tracking_result['tracking_status']);
            }
        } else {
            $data['tracking_data'] = ['status' => 'failed', 'message' => $this->lang->line('Shipping provider not configured.')];
        }

        $this->load->view('home_template', $data);
    }

    /**
     * (Optional) ฟังก์ชันสำหรับจัดการ Webhook จากบริษัทขนส่ง
     * URL นี้จะเป็น Callback URL ที่ตั้งค่าไว้ในระบบของ Kerry/Flash/J&T
     * ต้องมีการตรวจสอบ Security (เช่น Signature Verification)
     */
    public function webhook($gateway_name)
    {
        // รับข้อมูล webhook (มักจะเป็น JSON payload)
        $raw_webhook_data = file_get_contents('php://input');
        $webhook_data = json_decode($raw_webhook_data, true);

        // ตรวจสอบ Signature (สำคัญมาก!)
        // foreach ($this->active_shipping_gateways as $name => $gateway_obj) {
        //     if ($name === $gateway_name) {
        //         // ดึง API Secret ของ Gateway นั้นๆ เพื่อตรวจสอบ Signature
        //         // $gateway_obj->verify_webhook_signature($raw_webhook_data, $_SERVER['HTTP_X_SIGNATURE']);
        //         // ถ้า Signature ไม่ถูกต้อง ให้ return 403 Forbidden
        //     }
        // }


        if (!isset($this->active_shipping_gateways[$gateway_name])) {
            http_response_code(404); // Not Found
            echo json_encode(['error' => $this->lang->line('Invalid shipping gateway for webhook.')]);
            return;
        }

        $gateway = $this->active_shipping_gateways[$gateway_name];
        $result = $gateway->handle_webhook($webhook_data);

        if ($result['status'] === 'success') {
            // อัปเดตสถานะใน database จาก webhook
            $tracking_id = $result['tracking_id'];
            $new_status = $result['new_status'];
            $this->shipping_model->update_shipment_status($tracking_id, $new_status, ['last_webhook_data' => $raw_webhook_data]);

            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => $this->lang->line('Webhook processed successfully.')]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => $this->lang->line('Failed to process webhook: ') . $result['message']]);
        }
    }
}
