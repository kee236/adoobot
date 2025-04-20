<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TikTokSettings extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('basic'); // Assuming you have a basic model
        $this->lang->load('tiktok'); // Create a tiktok_lang.php file for translations
    }

    public function index() {
        if ($this->session->userdata('user_type') != 'Admin') {
            redirect('home/login_page', 'location');
        }

        $data['body'] = "admin/tiktok/settings"; // Create a view file named settings.php in admin/tiktok/
        $data['page_title'] = $this->lang->line('TikTok Settings');
        $get_data = $this->basic->get_data("tiktok_config"); // Create a table named tiktok_config
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

        $this->form_validation->set_rules('app_name', '<b>' . $this->lang->line("App Name") . '</b>', 'trim');
        $this->form_validation->set_rules('client_key', '<b>' . $this->lang->line("Client Key") . '</b>', 'trim|required');
        $this->form_validation->set_rules('client_secret', '<b>' . $this->lang->line("Client Secret") . '</b>', 'trim|required');
        $this->form_validation->set_rules('redirect_uri', '<b>' . $this->lang->line("Redirect URI") . '</b>', 'trim|required|valid_url');

        if ($this->form_validation->run() === FALSE) {
            return $this->index();
        } else {
            $this->csrf_token_check();

            $update_data = array(
                'app_name' => strip_tags($this->input->post('app_name', true)),
                'client_key' => strip_tags($this->input->post('client_key', true)),
                'client_secret' => strip_tags($this->input->post('client_secret', true)),
                'redirect_uri' => strip_tags($this->input->post('redirect_uri', true)),
                'status' => $this->input->post('status', true) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $get_data = $this->basic->get_data("tiktok_config");
            if (isset($get_data[0])) {
                $this->basic->update_data("tiktok_config", array("id >" => 0), $update_data);
            } else {
                $update_data['created_at'] = date('Y-m-d H:i:s');
                $this->basic->insert_data("tiktok_config", $update_data);
            }

            $this->session->set_flashdata('success_message', 1);
            redirect('tiktoksettings', 'location');
        }
    }
}
