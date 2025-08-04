<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * บันทึกข้อมูล Tracking Number ลงในฐานข้อมูล
     */
    public function save_tracking_info($order_id, $shipping_provider, $tracking_number) {
        $data = [
            'order_id' => $order_id,
            'shipping_provider' => $shipping_provider,
            'tracking_number' => $tracking_number,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('ecommerce_shipping_tracking', $data);
    }

    /**
     * ดึงข้อมูล Tracking Number จากฐานข้อมูล
     */
    public function get_tracking_info($order_id) {
        $this->db->where('order_id', $order_id);
        return $this->db->get('ecommerce_shipping_tracking')->row_array();
    }
}
