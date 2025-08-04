<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_gateway extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('ecommerce/order_model');
        $this->load->model('shipping_gateway/shipping_model');
        
        // โหลด Library ของผู้ให้บริการทั้งหมดที่เราสร้างไว้
        $this->load->library('shipping_gateway/kerry_api');
        $this->load->library('shipping_gateway/flash_api');
        $this->load->library('shipping_gateway/jandt_api');
    }

    /**
     * Endpoint สำหรับสร้างใบนำส่งพัสดุ
     * เรียกใช้จากหน้า Admin Panel เมื่อต้องการสร้าง Shipping Label
     */
    public function create_shipping_label() {
        $this->output->set_content_type('application/json');
        
        $order_id = $this->input->post('order_id');
        $shipping_method = $this->input->post('shipping_method'); // เช่น 'kerry_express'
        
        if (empty($order_id) || empty($shipping_method)) {
            echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
            return;
        }

        // ดึงข้อมูลคำสั่งซื้อจาก Model
        $order_details = $this->order_model->get_order_details($order_id);
        if (empty($order_details)) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลคำสั่งซื้อ']);
            return;
        }

        $response = [];
        switch ($shipping_method) {
            case 'kerry_express':
                $response = $this->kerry_api->create_shipment($order_details);
                break;
            case 'flash_express':
                $response = $this->flash_api->create_shipment($order_details);
                break;
            case 'jandt_express':
                $response = $this->jandt_api->create_shipment($order_details);
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'ไม่รองรับวิธีการจัดส่งนี้']);
                return;
        }

        if (isset($response['status']) && $response['status'] === 'success') {
            $tracking_number = isset($response['trackingNo']) ? $response['trackingNo'] : $response['billcode']; // ใช้ key ให้ถูกตาม API
            
            // บันทึกข้อมูลการติดตามลงในฐานข้อมูล
            $this->shipping_model->save_tracking_info($order_id, $shipping_method, $tracking_number);

            echo json_encode([
                'status' => 'success',
                'message' => 'สร้างใบนำส่งสำเร็จ',
                'tracking_number' => $tracking_number
            ]);
        } else {
            $error_message = isset($response['message']) ? $response['message'] : 'เกิดข้อผิดพลาดไม่ทราบสาเหตุ';
            echo json_encode(['status' => 'error', 'message' => $error_message]);
        }
    }

    /**
     * ฟังก์ชันสำหรับตรวจสอบสถานะพัสดุ (ใช้กับระบบ Tracking)
     */
    public function get_tracking_status() {
        // ... โค้ดสำหรับรับเลขพัสดุและเรียกใช้ Library เพื่อตรวจสอบสถานะ
    }
}
