<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * @category controller
 * class home
 */

class Home extends CI_Controller
{

    /**
     * load constructor
     * @access public
     * @return void
     */
    public $using_media_type = '';
    public $module_access;
    public $team_access = [];
    public $language;
    public $is_rtl;
    public $user_id;
    public $real_user_id;
    public $is_manager = 0;
    public $team_allowed_pages = [];
    public $is_demo;

    public $is_ad_enabled;
    public $is_ad_enabled1;
    public $is_ad_enabled2;
    public $is_ad_enabled3;
    public $is_ad_enabled4;

    public $ad_content1;
    public $ad_content1_mobile;
    public $ad_content2;
    public $ad_content3;
    public $ad_content4;
    public $app_product_id;
    public $APP_VERSION;
    public $strict_ajax_call = true;


    public function __construct()
    {
        parent::__construct();
        set_time_limit(0);
        $this->load->helpers(array('my_helper', 'addon_helper', 'bot_helper'));

        $is_rtl = $this->config->item("is_rtl");
        if (!empty($is_rtl) && $is_rtl == '1') $this->is_rtl = TRUE;
        else $this->is_rtl = FALSE;

        $is_demo = $this->config->item("is_demo");
        if ($is_demo == "") $is_demo = "0";
        $this->is_demo = $is_demo;

        $this->language = "";
        $this->_language_loader();

        $this->is_ad_enabled = false;
        $this->is_ad_enabled1 = false;
        $this->is_ad_enabled2 = false;
        $this->is_ad_enabled3 = false;
        $this->is_ad_enabled4 = false;

        $this->ad_content1 = "";
        $this->ad_content1_mobile = "";
        $this->ad_content2 = "";
        $this->ad_content3 = "";
        $this->ad_content4 = "";
        $this->app_product_id = 28;
        $this->APP_VERSION = "";

        $this->using_media_type = get_media_type();

        ignore_user_abort(TRUE);

        $seg = $this->uri->segment(2);

    // 1. Optimize Installation Check
    $excluded_segments = ["installation", "installation_action", "central_webhook_callback", "webhook_callback_main"];
    if (!in_array($this->uri->segment(2), $excluded_segments) && file_exists(APPPATH . 'install.txt')) {
        redirect('home/installation', 'location');
    }

    if ($seg != "installation" && $seg != "installation_action") {
        // ... logic อื่นๆ ...

        // 4. Session Usage
        $user_id = $this->session->userdata('user_id');
        $real_user_id = $this->session->userdata('real_user_id');
        $is_manager = $this->session->userdata('is_manager');

        if ($this->session->userdata('logged_in') == 1) {
            $this->load->database(); // 2. Conditional Database Loading
            $package_info = $is_manager == 1 ? $this->session->userdata("role_info") : $this->session->userdata("package_info");
            // ...
        }

        // 5. Affiliate Cookie Security
        $this->load->helper('cookie');
        $affiliateid = $this->input->get('ref', TRUE); // Sanitize input
        if ($affiliateid) {
            $visitor_cookie = array(
                "name" => "affiliate_id",
                "value" => $affiliateid,
                "expire" => 604800
            );
            set_cookie($visitor_cookie);

            $aff_id = (int)$affiliateid; // Or just use $affiliateid if no specific conversion needed
            $visitorip = $this->input->ip_address(); // CodeIgniter's method for IP
            $this->basic->insert_data("affiliate_visitors_action", ['affiliate_id' => $aff_id, 'type' => 'click', 'ip_address' => $visitorip, 'clicked_time' => date("Y-m-d H:i:s")]);
        }

        // 6. HTTPS Enforcement
        if ($this->config->item('force_https') == '1') {
            force_https(); // CodeIgniter's function
        }

        // 7. CSRF Token (Enable in config.php)
        if ($this->session->userdata('csrf_token_session') == "") {
            $this->session->set_userdata('csrf_token_session', bin2hex(random_bytes(32))); // Consider CodeIgniter's CSRF
        }
    }


function handle_cors() {
    $allowed_origins = ['https://ismartai.com', 'https://api.ismartai.com'];
    $allowed_methods = ['GET', 'POST', 'OPTIONS'];
    $allowed_headers = ['X-Requested-With', 'Content-Type', 'Authorization'];

    if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Methods: ' . implode(', ', $allowed_methods));
        header('Access-Control-Allow-Headers: ' . implode(', ', $allowed_headers));
        header('Access-Control-Allow-Credentials: true'); // ถ้าใช้ cookies
    }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// ใน controller:
handle_cors();

// ... โค้ด API ...


    public function _time_zone_list()
    {
        return $timezones =
            array(
                'Asia/Bangkok' => '(GMT+7:00) Asia/Bangkok (Thailand Standard Time)',
                'Asia/Jakarta' => '(GMT+7:00) Asia/Jakarta (West Indonesia Time)',
                'Asia/Ho_Chi_Minh' => '(GMT+7:00) Asia/Ho_Chi_Minh (Vietnam Standard Time)',

                'Asia/Singapore' => '(GMT+8:00) Asia/Singapore (Singapore Standard Time)',
                'Asia/Kuala_Lumpur' => '(GMT+8:00) Asia/Kuala_Lumpur (Malaysia Standard Time)',
                'Asia/Manila' => '(GMT+8:00) Asia/Manila (Philippines Standard Time)',
                'Asia/Taipei' => '(GMT+8:00) Asia/Taipei (Taiwan Standard Time)',
                'Asia/Shanghai' => '(GMT+8:00) Asia/Shanghai (China Standard Time)',
                'Asia/Hong_Kong' => '(GMT+8:00) Asia/Hong_Kong (Hong Kong Standard Time)',

                'Asia/Seoul' => '(GMT+9:00) Asia/Seoul (Korea Standard Time)',
                'Asia/Tokyo' => '(GMT+9:00) Asia/Tokyo (Japan Standard Time)',

                'Asia/Kolkata' => '(GMT+5:30) Asia/Kolkata (India Standard Time)',

                // เพิ่มเติมสำหรับครอบคลุมกรณีอื่นๆ (อาจมีผู้ใช้นอกเหนือจากกลุ่มเป้าหมายหลัก)
                'Etc/GMT' => '(GMT+0:00) Etc/GMT (Greenwich Mean Time)',
                'Etc/UTC' => '(GMT+0:00) Etc/UTC (Universal Coordinated Time)',
                'America/Los_Angeles' => '(GMT-8:00) America/Los_Angeles (Pacific Standard Time)', // ตัวอย่างเขตเวลาอเมริกา
                'Europe/London' => '(GMT+0:00) Europe/London (British Summer Time)', // ตัวอย่างเขตเวลา Europe
            );
    }



    protected function get_country_names()
    {
        $countries = $this->get_country_iso_phone_currecncy('country');
        $country_dropdown = array("" => $this->lang->line("Select Country"));
        foreach ($countries as $iso => $name) {
            $flag_path = base_url("assets/img/flag/" . strtolower($iso) . ".png");
            $country_dropdown[$iso] = '<img src="' . $flag_path . '" style="width: 24px; height: 16px; margin-right: 5px; vertical-align: middle;">' . $name;
        }
        return $country_dropdown;
    }

    protected function get_language_names()
    {
        $array_languages = array(
            'ar-XA' => 'Arabic',
            'bg' => 'Bulgarian',
            'hr' => 'Croatian',
            'cs' => 'Czech',
            'da' => 'Danish',
            'de' => 'German',
            'el' => 'Greek',
            'en' => 'English',
            'et' => 'Estonian',
            'es' => 'Spanish',
            'fi' => 'Finnish',
            'fr' => 'French',
            'in' => 'Indonesian',
            'ga' => 'Irish',
            'hi' => 'Hindi', // แก้ไขจาก 'hr' เป็น 'hi'
            'hu' => 'Hungarian',
            'he' => 'Hebrew',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'lv' => 'Latvian',
            'lt' => 'Lithuanian',
            'nl' => 'Dutch',
            'no' => 'Norwegian',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'sv' => 'Swedish',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sr-CS' => 'Serbian',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'uk-UA' => 'Ukrainian',
            'zh-chs' => 'Chinese (Simplified)',
            'zh-cht' => 'Chinese (Traditional)'
        );

        $language_dropdown = array("" => $this->lang->line("Select Language"));
        foreach ($array_languages as $code => $name) {
            // สร้างชื่อไฟล์ไอคอนธงชาติจากโค้ดภาษา (อาจต้องปรับตามมาตรฐานชื่อไฟล์ของคุณ)
            $flag_code = strtolower(substr($code, 0, 2));
            $flag_path = base_url("assets/img/flag/" . $flag_code . ".png");
            $language_dropdown[$code] = '<img src="' . $flag_path . '" style="width: 24px; height: 16px; margin-right: 5px; vertical-align: middle;">' . $name;
        }
        return $language_dropdown;
    }
