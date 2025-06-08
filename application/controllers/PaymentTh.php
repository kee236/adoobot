<?PHP 

// application/controllers/Home.php (หรือ Payment.php)

public function payment_status($status_code = 'unknown')
{
    if ($this->session->userdata('logged_in') != 1) {
        redirect('home/login_page', 'location');
    }

    $data['status']         = $status_code;
    $data['message']        = $this->session->flashdata('success_message') ??
                             $this->session->flashdata('error_message') ??
                             $this->session->flashdata('info_message');

    // ดึงข้อมูล Transaction ล่าสุดของผู้ใช้
    // ควรดึง transaction ล่าสุดที่มีสถานะเกี่ยวข้องกับการชำระเงิน เช่น pending, completed, failed
    // หรืออาจจะส่ง transaction_id มาจาก callback/redirect
    $user_id = $this->session->userdata('user_id');
    $latest_transaction = $this->payment_model->get_latest_transaction_by_user($user_id);

    // หากมีการ redirect มาจาก Payment Gateway โดยตรง อาจจะไม่มี transaction_id ที่ชัดเจนใน URL
    // ดังนั้นจึงควรดึงข้อมูลจาก session หรือหา transaction ที่เกี่ยวข้อง
    // ในกรณีที่ callback ไม่ได้ผ่านฟังก์ชันนี้โดยตรง แต่มีการอัปเดต DB แล้ว
    // ฟังก์ชันนี้จะแสดงข้อมูลจาก DB เป็นหลัก

    $data['transaction_info'] = [];
    if (!empty($latest_transaction)) {
        $data['transaction_info'] = $latest_transaction;
        // หาก $status_code ไม่ตรงกับสถานะใน DB (เช่น redirect มาที่ 'success' แต่ DB เป็น 'pending')
        // อาจจะต้องอัปเดต $data['status'] ให้ตรงกับ DB เพื่อความถูกต้อง
        if ($status_code !== $latest_transaction['status']) {
            $data['status'] = $latest_transaction['status'];
        }
    }

    $data['body']       = "payment/payment_status";
    $data['page_title'] = $this->lang->line('Payment Status');
    $this->load->view('home_template', $data);
}


// ตัวอย่างการเรียกใช้ใน Payment.php หรือ Callback method:
// redirect('home/payment_status/success', 'location');
// redirect('home/payment_status/failed', 'location');
// redirect('home/payment_status/pending', 'location');
// redirect('home/payment_status/waiting_for_admin_review', 'location');
