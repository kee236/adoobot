<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LineSettings extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('basic'); // Assuming you have a basic model
        $this->lang->load('line'); // Create a line_lang.php file for translations
    }

    public function index() {
        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login_page', 'location');
        }

        $data['body'] = "admin/line/settings"; // Create a view file named settings.php in admin/line/
        $data['page_title'] = $this->lang->line('LINE Settings');
        $get_data = $this->basic->get_data("line_config"); // Create a table named line_config
        $data['settings'] = isset($get_data[0]) ? $get_data[0] : array();

        $this->_viewcontroller($data);
    }

    public function update_settings() {
        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login_page', 'location');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $this->form_validation->set_rules('channel_id', '<b>' . $this->lang->line("Channel ID") . '</b>', 'trim|required');
        $this->form_validation->set_rules('channel_secret', '<b>' . $this->lang->line("Channel Secret") . '</b>', 'trim|required');
        $this->form_validation->set_rules('callback_url', '<b>' . $this->lang->line("Callback URL") . '</b>', 'trim|required|valid_url');
        $this->form_validation->set_rules('bot_basic_id', '<b>' . $this->lang->line("Bot Basic ID") . '</b>', 'trim');
        $this->form_validation->set_rules('liff_id', '<b>' . $this->lang->line("LIFF ID") . '</b>', 'trim');

        if ($this->form_validation->run() === FALSE) {
            return $this->index();
        } else {
            $this->csrf_token_check();

            $update_data = array(
                'channel_id' => strip_tags($this->input->post('channel_id', true)),
                'channel_secret' => strip_tags($this->input->post('channel_secret', true)),
                'callback_url' => strip_tags($this->input->post('callback_url', true)),
                'bot_basic_id' => strip_tags($this->input->post('bot_basic_id', true)),
                'liff_id' => strip_tags($this->input->post('liff_id', true)),
                'status' => $this->input->post('status', true) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $get_data = $this->basic->get_data("line_config");
            if (isset($get_data[0])) {
                $this->basic->update_data("line_config", array("id >" => 0), $update_data);
            } else {
                $update_data['created_at'] = date('Y-m-d H:i:s');
                $this->basic->insert_data("line_config", $update_data);
            }

            $this->session->set_flashdata('success_message', 1);
            redirect('linesettings', 'location');
        }
    }
}
