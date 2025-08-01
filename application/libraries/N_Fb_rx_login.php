<?php
// ส่วนต่อจากเดิมสำหรับคลาส Fb_rx_login
// ไม่ต้อง include("Facebook/autoload.php") แล้ว เพราะ Composer จัดการการโหลดอัตโนมัติ
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

// คลาส Fb_rx_login (ต่อจากไฟล์เดิม)
class Fb_rx_login
{
    // ... คุณสมบัติและ Constructor จากส่วนก่อนหน้า (fb1.1, fb1.2) ...

    protected $CI;
    public $app_id;
    public $app_secret;
    public $user_access_token;
    public $fb;
    public $default_graph_version;

    private $encrypt_method = "AES-256-CBC";
    private $secret_key;
    private $secret_iv;

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->helper('url');
        $this->CI->load->helper('my_helper'); // helper ที่กำหนดเองของคุณ (รวมถึง spintax_process และ get_domain_only)
        $this->CI->load->library('session');

        $this->CI->load->model('basic');

        $this->secret_key = getenv('FB_ENCRYPT_SECRET_KEY') ?: 't8Mk8fsJMnFw69FGG5';
        $this->secret_iv = getenv('FB_ENCRYPT_SECRET_IV') ?: '9fljzKxZmMmoT358yZ';

        $this->default_graph_version = getenv('FB_GRAPH_VERSION') ?: 'v23.0';

        $this->database_id = $this->CI->session->userdata("fb_rx_login_database_id");

        if ($this->CI->session->userdata("social_login_session_set") == 1) {
            $facebook_config = $this->CI->basic->get_data("facebook_rx_config", array("where" => array("status" => "1"), 'select' => '', 'join' => '', 'limit' => 1, 'start' => NULL, 'order_by' => rand()));

            if (empty($facebook_config)) {
                $this->database_id = '';
            } else {
                $config_id = isset($facebook_config[0]) ? $facebook_config[0]['id'] : 0;
                $this->database_id = $config_id;
                $this->CI->session->unset_userdata('social_login_session_set');
                $this->CI->session->set_userdata('return_configid_used_for_social_login', $config_id);
            }
        }

        if ($this->database_id != '') {
            $facebook_config = $this->CI->basic->get_data("facebook_rx_config", array("where" => array("id" => $this->database_id)));
            if (isset($facebook_config[0])) {
                $config_data = $facebook_config[0];

                if (isset($config_data['developer_access']) && $config_data['developer_access'] == '1') {
                    $key = hash('sha256', $this->secret_key);
                    $iv = substr(hash('sha256', $this->secret_iv), 0, 16);
                    $this->app_id = openssl_decrypt(base64_decode($config_data["api_id"]), $this->encrypt_method, $key, 0, $iv);
                    $this->app_secret = openssl_decrypt(base64_decode($config_data["api_secret"]), $this->encrypt_method, $key, 0, $iv);
                } else {
                    $this->app_id = $config_data["api_id"];
                    $this->app_secret = $config_data["api_secret"];
                }
                $this->user_access_token = $config_data["user_access_token"];

                $this->initialize_facebook_sdk($this->app_id, $this->app_secret);

            } else {
                $this->fallback_to_env_config();
            }
        } else {
            $this->fallback_to_env_config();
        }
    }

    private function fallback_to_env_config()
    {
        $env_app_id = getenv('FB_APP_ID');
        $env_app_secret = getenv('FB_APP_SECRET');

        if ($env_app_id && $env_app_secret) {
            $this->app_id = $env_app_id;
            $this->app_secret = $env_app_secret;
            $this->user_access_token = null;
            $this->initialize_facebook_sdk($this->app_id, $this->app_secret);
        } else {
            $this->fb = null;
            error_log("Facebook SDK: ไม่พบการตั้งค่า App ID และ Secret (ทั้งจาก DB และ .env)");
        }
    }

    private function initialize_facebook_sdk($app_id, $app_secret)
    {
        if (empty($app_id) || empty($app_secret)) {
            error_log("Facebook SDK: App ID หรือ Secret ว่างเปล่า ไม่สามารถเริ่มต้น SDK ได้");
            $this->fb = null;
            return;
        }

        try {
            $this->fb = new Facebook([
                'app_id' => $app_id,
                'app_secret' => $app_secret,
                'default_graph_version' => $this->default_graph_version,
                'fileUpload' => true
            ]);
        } catch (FacebookSDKException $e) {
            error_log("Facebook SDK Initialization Error: " . $e->getMessage());
            $this->fb = null;
        }
    }

    public function app_initialize($fb_rx_login_database_id)
    {
        $this->database_id = $fb_rx_login_database_id;
        $facebook_config = $this->CI->basic->get_data("facebook_rx_config", array("where" => array("id" => $this->database_id)));

        if (isset($facebook_config[0])) {
            $config_data = $facebook_config[0];

            if (isset($config_data['developer_access']) && $config_data['developer_access'] == '1') {
                $key = hash('sha256', $this->secret_key);
                $iv = substr(hash('sha256', $this->secret_iv), 0, 16);
                $this->app_id = openssl_decrypt(base64_decode($config_data["api_id"]), $this->encrypt_method, $key, 0, $iv);
                $this->app_secret = openssl_decrypt(base64_decode($config_data["api_secret"]), $this->encrypt_method, $key, 0, $iv);
            } else {
                $this->app_id = $config_data["api_id"];
                $this->app_secret = $config_data["api_secret"];
            }
            $this->user_access_token = $config_data["user_access_token"];

            $this->initialize_facebook_sdk($this->app_id, $this->app_secret);
        } else {
            $this->fb = null;
            throw new Exception("ไม่พบการตั้งค่า Facebook app ID: {$fb_rx_login_database_id}");
        }
    }

    function login_for_user_access_token($redirect_url = "", $additional_permissions = [])
    {
        if ($this->fb === null) {
            throw new Exception("Facebook SDK ไม่ได้เริ่มต้น กรุณาตรวจสอบ App ID หรือ Secret.");
        }

        $redirect_url = rtrim($redirect_url, '/');
        $helper = $this->fb->getRedirectLoginHelper();

        $permissions = [
            'email', 'pages_manage_posts', 'pages_manage_engagement',
            'pages_manage_metadata', 'pages_read_engagement', 'pages_show_list',
            'pages_messaging', 'public_profile', 'read_insights', 'business_management'
        ];

        if ($this->CI->config->item('instagram_reply_enable_disable') == '1') {
            array_push($permissions, 'instagram_basic', 'instagram_manage_comments', 'instagram_manage_insights', 'instagram_content_publish', 'instagram_manage_messages');
        }

        $permissions = array_unique(array_merge($permissions, $additional_permissions));

        $loginUrl = $helper->getLoginUrl($redirect_url, $permissions);

        return htmlspecialchars($loginUrl);
    }

    public function login_callback_without_email($redirect_url = "")
    {
        if ($this->fb === null) {
            return ['status' => '0', 'message' => 'Facebook SDK ไม่ได้เริ่มต้น กรุณาตรวจสอบ App ID หรือ Secret.'];
        }

        $redirect_url = rtrim($redirect_url, '/');
        $helper = $this->fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken($redirect_url);

            if (!$accessToken) {
                return ['status' => '0', 'message' => 'ไม่สามารถรับ Access Token ได้ ผู้ใช้อาจปฏิเสธสิทธิ์ หรือ state ไม่ถูกต้อง.'];
            }

            $longLivedAccessToken = $this->create_long_lived_access_token((string) $accessToken);

            $response = $this->fb->get('/me?fields=id,name', $longLivedAccessToken);
            $user = $response->getGraphUser()->asArray();

            $user["access_token_set"] = $longLivedAccessToken;
            $user["status"] = "1";
            return $user;

        } catch (FacebookResponseException $e) {
            return ['status' => '0', 'message' => 'Graph API คืนข้อผิดพลาด: ' . $e->getMessage()];
        } catch (FacebookSDKException $e) {
            return ['status' => '0', 'message' => 'Facebook SDK คืนข้อผิดพลาด: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['status' => '0', 'message' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด: ' . $e->getMessage()];
        }
    }

    public function login_callback($redirect_url = "")
    {
        if ($this->fb === null) {
            return ['status' => '0', 'message' => 'Facebook SDK ไม่ได้เริ่มต้น กรุณาตรวจสอบ App ID หรือ Secret.'];
        }

        $redirect_url = rtrim($redirect_url, '/');
        $helper = $this->fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken($redirect_url);

            if (!$accessToken) {
                return ['status' => '0', 'message' => 'ไม่สามารถรับ Access Token ได้ ผู้ใช้อาจปฏิเสธสิทธิ์ หรือ state ไม่ถูกต้อง.'];
            }

            $longLivedAccessToken = $this->create_long_lived_access_token((string) $accessToken);

            $response = $this->fb->get('/me?fields=id,name,email', $longLivedAccessToken);
            $user = $response->getGraphUser()->asArray();

            $user["access_token_set"] = $longLivedAccessToken;
            $user["status"] = "1";
            return $user;

        } catch (FacebookResponseException $e) {
            return ['status' => '0', 'message' => 'Graph API คืนข้อผิดพลาด: ' . $e->getMessage()];
        } catch (FacebookSDKException $e) {
            return ['status' => '0', 'message' => 'Facebook SDK คืนข้อผิดพลาด: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['status' => '0', 'message' => 'เกิดข้อผิดพลาดที่ไม่คาดคิด: ' . $e->getMessage()];
        }
    }

    public function app_id_secret_check()
    {
        if (empty($this->app_id) || empty($this->app_secret)) return 'not_configured';
        return null;
    }

    function access_token_validity_check()
    {
        if ($this->fb === null || !$this->user_access_token) {
            return false;
        }

        try {
            $response = $this->fb->get(
                '/debug_token?input_token=' . $this->user_access_token,
                $this->app_id . '|' . $this->app_secret
            );
            $debugToken = $response->getGraphNode();

            return $debugToken['is_valid'] && (!$debugToken['expires_at'] || $debugToken['expires_at'] > time());

        } catch (FacebookResponseException $e) {
            error_log("ข้อผิดพลาดในการตรวจสอบ Access Token (Graph): " . $e->getMessage());
            return false;
        } catch (FacebookSDKException $e) {
            error_log("ข้อผิดพลาดในการตรวจสอบ Access Token (SDK): " . $e->getMessage());
            return false;
        }
    }

    function access_token_validity_check_for_user($access_token)
    {
        if ($this->fb === null || !$access_token) {
            return false;
        }
        try {
            $response = $this->fb->get(
                '/debug_token?input_token=' . $access_token,
                $this->app_id . '|' . $this->app_secret
            );
            $debugToken = $response->getGraphNode();

            return $debugToken['is_valid'] && (!$debugToken['expires_at'] || $debugToken['expires_at'] > time());

        } catch (FacebookResponseException $e) {
            error_log("ข้อผิดพลาดในการตรวจสอบ Access Token (Graph สำหรับผู้ใช้): " . $e->getMessage());
            return false;
        } catch (FacebookSDKException $e) {
            error_log("ข้อผิดพลาดในการตรวจสอบ Access Token (SDK สำหรับผู้ใช้): " . $e->getMessage());
            return false;
        }
    }

    public function create_long_lived_access_token($short_lived_user_token)
    {
        if ($this->fb === null || empty($short_lived_user_token)) {
            return '';
        }
        try {
            $oAuth2Client = $this->fb->getOAuth2Client();
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($short_lived_user_token);
            return (string) $longLivedAccessToken;
        } catch (FacebookResponseException $e) {
            error_log("ข้อผิดพลาดในการสร้าง Long-lived Token (Graph): " . $e->getMessage());
            return '';
        } catch (FacebookSDKException $e) {
            error_log("ข้อผิดพลาดในการสร้าง Long-lived Token (SDK): " . $e->getMessage());
            return '';
        }
    }

    public function facebook_api_call($url)
    {
        if ($this->fb === null) {
            return ['error' => ['message' => 'Facebook SDK ไม่ได้เริ่มต้น.']];
        }

        $parsed_url = parse_url($url);
        $path = $parsed_url['path'] ?? '';
        parse_str($parsed_url['query'] ?? '', $query_params);
        $access_token_from_url = $query_params['access_token'] ?? $this->user_access_token;

        $method = 'GET';

        try {
            $response = $this->fb->get($path, $access_token_from_url);
            return $response->getGraphNode()->asArray();
        } catch (FacebookResponseException $e) {
            error_log("ข้อผิดพลาดในการเรียก Facebook API (Graph): " . $e->getMessage());
            return ['error' => ['message' => $e->getMessage(), 'code' => $e->getCode()]];
        } catch (FacebookSDKException $e) {
            error_log("ข้อผิดพลาดในการเรียก Facebook API (SDK): " . $e->getMessage());
            return ['error' => ['message' => $e->getMessage(), 'code' => $e->getCode()]];
        }
    }

    public function get_page_list($access_token = "")
    {
        if ($this->fb === null || empty($access_token)) {
            return ['error' => '1', 'message' => 'Facebook SDK ไม่ได้เริ่มต้น หรือ Access Token หายไป.'];
        }

        try {
            $response = $this->fb->get(
                '/me/accounts?fields=cover,emails,picture,id,name,url,username,access_token,instagram_business_account',
                $access_token
            );
            $pages = $response->getGraphList()->asArray();

            foreach ($pages as $key => $page) {
                if (isset($page['instagram_business_account']) && isset($page['instagram_business_account']['id'])) {
                    try {
                        $ig_response = $this->fb->get(
                            '/' . $page['instagram_business_account']['id'] . '?fields=username,profile_picture_url',
                            $page['access_token']
                        );
                        $pages[$key]['instagram_business_account_details'] = $ig_response->getGraphNode()->asArray();
                    } catch (FacebookResponseException $e) {
                        error_log("ไม่สามารถดึงรายละเอียด IG สำหรับเพจ " . $page['name'] . ": " . $e->getMessage());
                    } catch (FacebookSDKException $e) {
                        error_log("ไม่สามารถดึงรายละเอียด IG สำหรับเพจ " . $page['name'] . ": " . $e->getMessage());
                    }
                }
            }

            return $pages;

        } catch (FacebookResponseException $e) {
            return ['error' => '1', 'message' => 'Graph API คืนข้อผิดพลาด: ' . $e->getMessage()];
        } catch (FacebookSDKException $e) {
            return ['error' => '1', 'message' => 'Facebook SDK คืนข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    public function get_page_insight_info($access_token, $metrics, $page_id)
    {
        if ($this->fb === null) {
            throw new Exception("Facebook SDK ไม่ได้เริ่มต้น.");
        }
        if (empty($page_id) || empty($metrics) || empty($access_token)) {
            throw new InvalidArgumentException("Page ID, metrics, หรือ Access Token ว่างเปล่า.");
        }

        $from = date('Y-m-d', strtotime(date('Y-m-d') . ' -28 day'));
        $to   = date('Y-m-d', strtotime(date("Y-m-d") . '-1 day'));

        try {
            $request = $this->fb->get("/{$page_id}/insights?metric={$metrics}&since={$from}&until={$to}", $access_token);
            $response = $request->getGraphList()->asArray();
            return $response;
        } catch (FacebookResponseException $e) {
            error_log("ข้อผิดพลาดในการดึง Page Insight (Graph): " . $e->getMessage());
            throw new Exception("ไม่สามารถดึง Page Insight ได้: " . $e->getMessage());
        } catch (FacebookSDKException $e) {
            error_log("ข้อผิดพลาดในการดึง Page Insight (SDK): " . $e->getMessage());
            throw new Exception("ข้อผิดพลาด SDK ในการดึง Page Insight: " . $e->getMessage());
        }
    }

    public function get_group_list($access_token = "")
    {
        if ($this->fb === null) {
            throw new Exception("Facebook SDK ไม่ได้เริ่มต้น.");
        }
        if (empty($access_token)) {
            throw new InvalidArgumentException("Access Token ว่างเปล่า.");
        }

        try {
            $request = $this->fb->get('/me/groups?fields=cover,picture,id,name&limit=400&admin_only=1', $access_token);
            $response_group = $request->getGraphList()->asArray();
            return $response_group;
        } catch (FacebookResponseException $e) {
            error_log("ข้อผิดพลาดในการดึงรายการกลุ่ม (Graph): " . $e->getMessage());
            throw new Exception("ไม่สามารถดึงรายการกลุ่มได้: " . $e->getMessage());
        } catch (FacebookSDKException $e) {
            error_log("ข้อผิดพลาดในการดึงรายการกลุ่ม (SDK): " . $e->getMessage());
            throw new Exception("ข้อผิดพลาด SDK ในการดึงรายการกลุ่ม: " . $e->getMessage());
        }
    }

    public function send_user_roll_access($app_id, $user_id, $user_access_token)
    {
        if ($this->fb === null) {
            throw new Exception("Facebook SDK ไม่ได้เริ่มต้น.");
        }
        if (empty($app_id) || empty($user_id) || empty($user_access_token)) {
            throw new InvalidArgumentException("App ID, User ID, หรือ User Access Token ว่างเปล่า.");
        }

        try {
            $response = $this->fb->post(
                "/{$app_id}/roles",
                [
                    'user' => $user_id,
                    'role' => 'testers'
                ],
                $user_access_token
            );
            return $response->getGraphNode()->asArray();
        } catch (FacebookResponseException $e) {
            error_log("ข้อผิดพลาดในการกำหนดบทบาทผู้ใช้ (Graph): " . $e->getMessage());
            throw new Exception("ไม่สามารถกำหนดบทบาทผู้ใช้ได้: " . $e->getMessage());
        } catch (FacebookSDKException $e) {
            error_log("ข้อผิดพลาดในการกำหนดบทบาทผู้ใช้ (SDK): " . $e->getMessage());
            throw new Exception("ข้อผิดพลาด SDK ในการกำหนดบทบาทผู้ใช้: " . $e->getMessage());
        }
    }

    public function block_person_from_commenting($page_id, $commenter_id, $page_access_token)
    {
        if ($this->fb === null) {
            throw new Exception("Facebook SDK ไม่ได้เริ่มต้น.");
        }
        if (empty($page_id) || empty($commenter_id) || empty($page_access_token)) {
            throw new InvalidArgumentException("Page ID, Commenter ID, หรือ Page Access Token ว่างเปล่า.");
        }

        try {
            $response = $this->fb->post(
                "/{$page_id}/blocked",
                [
                    'user' => $commenter_id
                ],
                $page_access_token
            );
            return $response->getGraphNode()->asArray();
        } catch (FacebookResponseException $e) {
            error_log("ข้อผิดพลาดในการบล็อกผู้ใช้จากการคอมเมนต์ (Graph): " . $e->getMessage());
            throw new Exception("ไม่สามารถบล็อกผู้ใช้จากการคอมเมนต์ได้: " . $e->getMessage());
        } catch (FacebookSDKException $e) {
            error_log("ข้อผิดพลาดในการบล็อกผู้ใช้จากการคอมเมนต์ (SDK): " . $e->getMessage());
            throw new Exception("ข้อผิดพลาด SDK ในการบล็อกผู้ใช้จากการคอมเมนต์: " . $e->getMessage());
        }
    }

    public function get_metrics_page_post($post_id, $page_access_token)
    {
        if ($this->fb === null)
// Assuming this is part of a class structure in a CodeIgniter or similar framework.

// Updated to use Facebook Graph API v23.0

        public function app_info_graber($app_id='',$app_secret='')
        {
                // Changed API version from v4.0 to v23.0
                // Removed 'photo_url' as it's not a standard field for an 'app' object.
                // You might need to check if 'link' and 'category' are still relevant or if you need specific permissions.
                // For app information, typically 'name', 'id', and perhaps 'app_domains' or 'app_type' are relevant.
                $url = "https://graph.facebook.com/v23.0/".$app_id."?access_token=".$app_id."|".$app_secret."&fields=name,link,id,category";
                $headers = array("Content-type: application/json");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Can be uncommented if redirects are expected
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Setting this to false is insecure and not recommended for production.
                                                                 // For production, ensure you have proper CA certs and set to true.
                curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  // Cookies are generally not needed for stateless Graph API calls.
                curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); // Can be removed if not explicitly managing session cookies.
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:89.0) Gecko/20100101 Firefox/89.0");
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Duplicate, can remove one.

                $st=curl_exec($ch);
                curl_close($ch); // Always close cURL handle

                $result=json_decode($st,TRUE);
                return $result;
        }


        // ================== webhook enable disable ==============//
        // Array([success] => 1)
        public function enable_bot($page_id='',$post_access_token='')
        {
                if($page_id=='' || $post_access_token=='')
                {
                        return array('success'=>0,'error'=>$this->CI->lang->line("Something went wrong, please try again."));
                        // exit(); // Avoid exit() in library/class methods; return error instead.
                }
                try
                {
                        $params=array();
                        $params['subscribed_fields']= array("messages","messaging_optins","messaging_postbacks","messaging_referrals","feed","message_echoes");
                        // Assuming $this->fb is an instance of the Facebook PHP SDK, it should handle API versioning internally if configured correctly.
                        // If not, ensure the SDK is configured for v23.0.
                        $response = $this->fb->post("{$page_id}/subscribed_apps",$params,$post_access_token);
                        $response = $response->getGraphObject()->asArray();
                        $response['error']='';
                        return $response;
                }
                catch (Exception $e)
                {
                        return array('success'=>0,'error'=>$e->getMessage());
                }
        }

        // Array([success] => 1)
        public function disable_bot($page_id='',$post_access_token='')
        {
                if($page_id=='' || $post_access_token=='')
                {
                        return array('success'=>0,'error'=>$this->CI->lang->line("Something went wrong, please try again."));
                        // exit(); // Avoid exit() in library/class methods; return error instead.
                }
                try
                {
                        // Assuming $this->fb is an instance of the Facebook PHP SDK, it should handle API versioning internally if configured correctly.
                        $response = $this->fb->delete("{$page_id}/subscribed_apps",array(),$post_access_token);
                        $response = $response->getGraphObject()->asArray();
                        $response['error']='';
                        return $response;
                }
                catch (Exception $e)
                {
                        return array('success'=>0,'error'=>$e->getMessage());
                }
        }

        /* Delete Persistent Menu */
        public function delete_persistent_menu($post_access_token='',$media_type='')
        {
                if($media_type=="") $media_type="fb";

                // Updated API version from v4.0 to v23.0
                if($media_type=="fb")
                        $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$post_access_token}";
                else
                        $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$post_access_token}&platform=instagram";

                $get_started_data='{"fields":["persistent_menu"]}'; // This payload is correct for deleting specific fields.

                $ch = curl_init();
                 $headers = array("Content-type: application/json; charset=UTF-8");

                 curl_setopt($ch, CURLOPT_URL, $url);
                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                 curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data);

                 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Insecure for production.
                 curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                 curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                 $st=curl_exec($ch);
                 curl_close($ch);
                 $result=json_decode($st,TRUE);
                 return $result;
        }

        /* Delete get Started Button */
        public function delete_get_started_button($post_access_token='')
        {
                // Updated API version from v4.0 to v23.0
                $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$post_access_token}";
                $get_started_data='{"fields":["get_started"]}';

                $ch = curl_init();
                 $headers = array("Content-type: application/json");

                 curl_setopt($ch, CURLOPT_URL, $url);
                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                 curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data);

                 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Insecure for production.
                 curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                 curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                 $st=curl_exec($ch);
                 curl_close($ch);
                 $result=json_decode($st,TRUE);

                 if(isset($result["result"]))
                {
                        $result["result"]=$this->CI->lang->line(trim($result["result"]));
                        $result['success']=1;
                }
                if(isset($result["error"]))
                {
                        $result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
                        $result['success']=0;
                }
                return $result;
        }


        /* Add get Started Button */
        public function add_get_started_button($post_access_token='')
        {
                // Updated API version from v4.0 to v23.0
                $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$post_access_token}";
                $get_started_data='{"get_started":{"payload":"GET_STARTED_PAYLOAD"}}';

                $ch = curl_init();
                 $headers = array("Content-type: application/json");

                 curl_setopt($ch, CURLOPT_URL, $url);
                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                 curl_setopt($ch,CURLOPT_POST,1);
                 curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data);

                 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Insecure for production.
                 curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                 curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070101 Firefox/2.0.0.3");
                 $st=curl_exec($ch);
                 curl_close($ch);
                 $result=json_decode($st,TRUE);
                 if(isset($result["result"]))
                {
                        $result["result"]=$this->CI->lang->line(trim($result["result"]));
                        $result['success']=1;
                }
                if(isset($result["error"]))
                {
                        $result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
                        $result['success']=0;
                }
                return $result;
        }


        public function set_welcome_message($post_access_token='',$welcome_message='')
        {
                if($welcome_message=='') return false;

                // Updated API version from v4.0 to v23.0
                $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$post_access_token}";
                $get_started_data=array
                (
                        'greeting'=>array(0=>array("locale"=>"default","text"=>$welcome_message))
                );
                $get_started_data=json_encode($get_started_data);

                $ch = curl_init();
                 $headers = array("Content-type: application/json");

                 curl_setopt($ch, CURLOPT_URL, $url);
                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                 curl_setopt($ch,CURLOPT_POST,1);
                 curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data);

                 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Insecure for production.
                 curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                 curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                 $st=curl_exec($ch);
                 curl_close($ch);
                 $result=json_decode($st,TRUE);
                 if(isset($result["result"]))
                {
                        $result["result"]=$this->CI->lang->line(trim($result["result"]));
                        $result['success']=1;
                }
                if(isset($result["error"]))
                {
                        $result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
                        $result['success']=0;
                }

                return $result;
        }

        public function unset_welcome_message($post_access_token='')
        {
                // Updated API version from v4.0 to v23.0
                $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$post_access_token}";
                $get_started_data='{"fields":["greeting"]}';

                $ch = curl_init();
                 $headers = array("Content-type: application/json");

                 curl_setopt($ch, CURLOPT_URL, $url);
                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                 curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data);

                 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Insecure for production.
                 curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                 curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                 $st=curl_exec($ch);
                 curl_close($ch);
                 $result=json_decode($st,TRUE);

                 if(isset($result["result"]))
                {
                        $result["result"]=$this->CI->lang->line(trim($result["result"]));
                        $result['success']=1;
                }
                if(isset($result["error"]))
                {
                        $result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
                        $result['success']=0;
                }
                return $result;
        }


        /* Add Persistent Menu */
        public function add_persistent_menu($post_access_token='',$menu_content_json='',$media_type='')
        {
                if($media_type=="") $media_type="fb";

                // Updated API version from v4.0 to v23.0
                if($media_type=="fb")
                        $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$post_access_token}";
                else
                        $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$post_access_token}&platform=instagram";

                $get_started_data=$menu_content_json; // This is a generic name; consider renaming to $menu_payload or similar.

                $ch = curl_init();
                 $headers = array("Content-type: application/json");

                 curl_setopt($ch, CURLOPT_URL, $url);
                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                 curl_setopt($ch,CURLOPT_POST,1);
                 curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data);

                 // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Insecure for production.
                 curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                 curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                 $st=curl_exec($ch);
                 curl_close($ch);
                 $result=json_decode($st,TRUE);
                return $result;
        }



        function get_page_review_status($post_access_token='')
        {
                // Updated API version from v4.0 to v23.0
                $url="https://graph.facebook.com/v23.0/me/messaging_feature_review?access_token={$post_access_token}";
                $headers = array("Content-type: application/json");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Insecure for production.
                curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                $st=curl_exec($ch);
                curl_close($ch);

                $result=json_decode($st,TRUE);

                return $result;
        }


        //https://developers.facebook.com/docs/messenger-platform/send-messages/broadcast-messages/estimate-reach/
        function start_reach_estimation($post_access_token='')
        {
                // Updated API version from v4.0 to v23.0
                $url="https://graph.facebook.com/v23.0/me/broadcast_reach_estimations?access_token={$post_access_token}&method=post";
                $ch = curl_init();
                $headers = array("Content-type: application/json");

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                // curl_setopt($ch,CURLOPT_POST,1); // This method implicitly sets POST if data is provided. Given the URL has method=post, it's likely a GET request with a query param.
                //curl_setopt($ch,CURLOPT_POSTFIELDS,$message); // No message needed for starting estimation, typically.
                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Insecure for production.
                curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                $st=curl_exec($ch);
                curl_close($ch);

                $result=json_decode($st,TRUE);

                return $result;
        }



        function reach_estimation_count($reach_estimation_id='',$post_access_token='')
        {
                // Updated API version from v4.0 to v23.0
                $url="https://graph.facebook.com/v23.0/{$reach_estimation_id}?access_token={$post_access_token}";
                $ch = curl_init();
                $headers = array("Content-type: application/json");

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WARNING: Insecure for production.
                curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                $st=curl_exec($ch);
                curl_close($ch);

                $result=json_decode($st,TRUE);

                return $result;
        }

?>


        /*** Subscription based message sent  https://developers.facebook.com/docs/messenger-platform/send-messages/message-tags ***/
        function send_non_promotional_message_subscription($message='[]',$post_access_token='')
        {
                $url = "https://graph.facebook.com/v23.0/me/messages?access_token={$post_access_token}";

                $ch = curl_init();
                $headers = array("Content-type: application/json");

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$message);
                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                $st=curl_exec($ch);

                $result=json_decode($st,TRUE);

                return $result;
        }


        //calls fb api using post variable and json header
        function call_api_post($json='',$url='',$delete=false)
        {
                $ch = curl_init();
                $headers = array("Content-type: application/json");
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                if($delete)        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                if($json!="")
                {
                        curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
                        curl_setopt($ch,CURLOPT_POST,1);
                }
                //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                $st=curl_exec($ch);
                $result=json_decode($st,TRUE);
                return $result;
        }


        // Array([result] => Successfully updated whitelisted domains)
        public function domain_whitelist($access_token='',$domain='')
        {
                if($access_token=='' || $domain=='')
                {
                        return array('status'=>'0','result'=>$this->CI->lang->line("Something went wrong, please try again."));
                        exit();
                }

                // Fetch all current whitelisted domains
                $url = "https://graph.facebook.com/v23.0/me/messenger_profile?fields=whitelisted_domains&access_token={$access_token}";
                $ch = curl_init();
                 $headers = array("Content-type: application/json");
                 curl_setopt($ch, CURLOPT_URL, $url);
                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                 $st=curl_exec($ch);
                 $result=json_decode($st,TRUE);

                 if(isset($result["error"]))
                 {
                         $result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
                         $result['status']='0';
                         return $result;
                 }

                 $current_whitelisted_domains= isset($result['data'][0]['whitelisted_domains']) ? $result['data'][0]['whitelisted_domains'] : array();
                 $current_whitelisted_domains[]=$domain;

                 $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$access_token}";
                 $whitelisted_domains_data['whitelisted_domains']= $current_whitelisted_domains;
                 $whitelisted_domains_data=json_encode($whitelisted_domains_data);

                $ch = curl_init();
                 $headers = array("Content-type: application/json");

                 curl_setopt($ch, CURLOPT_URL, $url);
                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                 curl_setopt($ch,CURLOPT_POST,1);
                 curl_setopt($ch,CURLOPT_POSTFIELDS,$whitelisted_domains_data);
                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
                 $st=curl_exec($ch);
                  $result=json_decode($st,TRUE);

                  if(isset($result["result"]))
                 {
                         $result["result"]=$this->CI->lang->line(trim($result["result"]));
                         $result['status']='1';
                 }
                 if(isset($result["error"]))
                 {
                         $result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("Something went wrong, please try again.");
                         $result['status']='0';
                 }

                 return $result;
        }

        // page_messages_reported_conversations_by_report_type_unique and page_messages_active_threads_unique metrics have been depreciated
        function get_analytics_data($access_token="",$from_date="",$to_date='')
        {
                $url = "https://graph.facebook.com/v23.0/me/insights/?metric=page_messages_total_messaging_connections,page_messages_new_conversations_unique,page_messages_blocked_conversations_unique,page_messages_reported_conversations_unique&access_token={$access_token}&since={$from_date}&until={$to_date}";
            $ch = curl_init();
            $headers = array("Content-type: application/json");
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
            $st=curl_exec($ch);
            $result=json_decode($st,TRUE);
            return $result;
        }

        function create_label($page_access_token="",$label="") //{"id": 1712444532121303}
        {
                $url="https://graph.facebook.com/v23.0/me/custom_labels?access_token={$page_access_token}";
                $json=json_encode(array("page_label_name"=>$label));
                return $this->call_api_post($json,$url);
        }

        function assign_label($page_access_token='',$psid='',$label_id='') //{"success": true}
        {
                $url="https://graph.facebook.com/v23.0/{$label_id}/label?access_token={$page_access_token}";
                $json=json_encode(array("user"=>$psid));
                return $this->call_api_post($json,$url);
        }

        function deassign_label($page_access_token='',$psid='',$label_id='')//{"success": true}
        {
                $url="https://graph.facebook.com/v23.0/{$label_id}/label?access_token={$page_access_token}";
                $json=json_encode(array("user"=>$psid));
                return $this->call_api_post($json,$url,true);
        }

        function delete_label($page_access_token='',$label_id='')//{"success": true}
        {
                $url="https://graph.facebook.com/v23.0/{$label_id}?access_token={$page_access_token}";
                return $this->call_api_post('',$url,true);
        }

        function retrieve_label($page_access_token='')
        {
                $url="https://graph.facebook.com/v23.0/me/custom_labels?fields=page_label_name&access_token={$page_access_token}&limit=200";
                return $this->call_api_post('',$url,false);
        }

        function retrieve_level_of_psid($psid,$page_access_token){

                $url="https://graph.facebook.com/v23.0/{$psid}/custom_labels?fields=page_label_name&access_token=$page_access_token&limit=200";
                return $this->call_api_post('',$url,false);

        }

        public function get_all_comment_of_post_pagination($post_ids,$post_access_token)
        {
                $url="v23.0/{$post_ids}/comments?order=reverse_chronological&summary=1&limit=400&filter=toplevel";

                $comment_info=array();
                $commenter_info=array();

                $i=0;

                do
                {
                        $response = $this->fb->get($url,$post_access_token);
                        $data =  $response->getGraphEdge()->asArray();
                        $paging_data= $response->getGraphEdge()->getMetaData();

                        foreach($data as $info){

                                $time=  isset($info['created_time'])?(array)$info['created_time']:"";

                                $comment_info[$i]['created_time']=isset($time['date'])?$time['date']:"";
                                $comment_info[$i]['commenter_name']=isset($info['from']['name'])? $info['from']['name']:"";
                                $comment_info[$i]['commenter_id']=isset($info['from']['id'])?$info['from']['id']:"";
                                $comment_info[$i]['message']=isset($info['message'])?$info['message']:"";
                                $comment_info[$i]['comment_id']=isset($info['id'])?$info['id']:"";

                                /* Store Commenter info as unique */

                                if(!isset($commenter_info[$comment_info[$i]['commenter_id']])){
                                        $commenter_info[$comment_info[$i]['commenter_id']]['name']=$comment_info[$i]['commenter_name'];
                                        $commenter_info[$comment_info[$i]['commenter_id']]['last_comment']=$comment_info[$i]['message'];
                                        $commenter_info[$comment_info[$i]['commenter_id']]['last_comment_id']=$comment_info[$i]['comment_id'];
                                        $commenter_info[$comment_info[$i]['commenter_id']]['last_comment_time']=$comment_info[$i]['created_time'];
                                }

                                $i++;
                        }

                        $next= isset($paging_data['paging']['cursors']['after'])?$paging_data['paging']['cursors']['after']:"";

                        if($next!="")
                                $url="v23.0/{$post_ids}/comments?order=reverse_chronological&after={$next}&limit=400&filter=toplevel";
                        else
                                $url="";

                }
                while($url!='');

                $all_info=array();

                $all_info['comment_info']= $comment_info;
                $all_info['commenter_info']= $commenter_info;

                return $all_info;
        }

        function fb_like_comment_share($url,$access_token)
        {

                $url="https://graph.facebook.com/v23.0/?id={$url}&fields=engagement,og_object&access_token={$access_token}";
                $response1=$this->run_curl_for_fb($url);
                $response = json_decode($response1,true);
                if(isset($response['error']['message'])){
                        $response_error['errormessage']= $response['error']['message'];
                        return $response_error;
                }


                if (isset($response['engagement']['share_count']))
                         $get_total_share['total_share'] = $response['engagement']['share_count'];
                else
                        $get_total_share['total_share'] = 0;

                if (isset($response['engagement']['reaction_count']))
                        $get_total_share['total_reaction'] = $response['engagement']['reaction_count'];
                else
                        $get_total_share['total_reaction'] = 0;

                if (isset($response['engagement']['comment_count']))
                        $get_total_share['total_comment'] = $response['engagement']['comment_count'];
                else
                        $get_total_share['total_comment'] = 0;

                if (isset($response['engagement']['comment_plugin_count']))
                        $get_total_share['total_comment_plugin'] = $response['engagement']['comment_plugin_count'];
                else
                        $get_total_share['total_comment_plugin'] = 0;

                $get_total_share['og_id']= isset($response['og_object']['id']) ? $response['og_object']['id']:"";
                $get_total_share['description']=isset($response['og_object']['description']) ? $response['og_object']['description']:"";
                $get_total_share['title']= isset($response['og_object']['title']) ? $response['og_object']['title']:"";
                $get_total_share['type']= isset($response['og_object']['type']) ? $response['og_object']['type']:"";
                $get_total_share['updated_time']= isset($response['og_object']['updated_time']) ? $response['og_object']['updated_time']:"";

                return $get_total_share;
        }

        function update_rul_for_like_share_count($url,$access_token){

        }




        public function location_search($access_token,$keyword,$latitude,$longitude,$distance,$search_limit){

                $keyword=urlencode($keyword);
                $center=$latitude.",".$longitude;

                $url="https://graph.facebook.com/v23.0/search?q={$keyword}&type=place&access_token={$access_token}&fields=id,name,overall_star_rating,website,about,category_list,checkins,cover,description,engagement,hours,is_always_open,is_permanently_closed,payment_options,price_range,rating_count,restaurant_services,is_verified,location,link,phone&center={$center}&distance={$distance}&limit={$search_limit}";
                 $results=$this->facebook_api_call($url);

                 if(isset($results['error']['message']))
                 {
                         $response_error['error_message'] = $results['error']['message'];
                         return $response_error;
                 }

                 if(!is_array($results) || !isset($results['data'])) return array("data"=>array());

                 $final_result[0]=$results['data'];
                 $total_found = count($final_result[0]);

                 $next_page= isset($results['paging']['next']) ? $results['paging']['next']:"" ;

                 for($i=1;$i<=5;$i++){

                         if(!$next_page){
                                break;
                         }

                         $next_page_result        = $this->facebook_api_call($next_page);
                        $final_result[$i]=isset($next_page_result['data']) ? $next_page_result['data']:array();
                        $total_found += count($final_result[$i]);
                        $next_page= isset($next_page_result['paging']['next']) ? $next_page_result['paging']['next']:"" ;
                 }
                $response['total_found']=$total_found;
                $response['data']=$final_result;

                return $response;



        }
        /**
         * Facebook Page insights
         * @param  string $access_token page access token
         * @param  string $page_id      Page id
         * @param  string $from_date    from date
         * @param  string $to_date      to date
         * @return array
         */
        public function page_insights($access_token="",$page_id ="",$from_date="",$to_date="")
        {

                // $from = date('Y-m-d', strtotime(date('Y-m-d').' -28 day'));
                // $to   = date('Y-m-d', strtotime(date("Y-m-d").'-1 day'));

                /* Page Metrics */
                // Note: Always verify the latest available metrics from Facebook Graph API documentation.
                // Some metrics might be deprecated in newer API versions.
                $metrics = 'page_content_activity_by_action_type_unique,page_content_activity,page_content_activity_by_action_type,page_impressions,page_impressions_unique,page_impressions_paid,page_impressions_paid_unique,page_impressions_organic,page_impressions_organic_unique,page_impressions_viral,page_impressions_viral_unique,page_impressions_nonviral,page_impressions_nonviral_unique,page_impressions_by_country_unique,page_engaged_users,page_post_engagements,page_consumptions,page_consumptions_unique,page_places_checkin_total,page_negative_feedback,page_positive_feedback_by_type,page_fans_online_per_day,page_actions_post_reactions_like_total,page_actions_post_reactions_love_total,page_actions_post_reactions_wow_total,page_actions_post_reactions_haha_total,page_actions_post_reactions_sorry_total,page_actions_post_reactions_anger_total,page_total_actions,page_cta_clicks_logged_in_total,page_call_phone_clicks_logged_in_unique,page_get_directions_clicks_logged_in_unique,page_website_clicks_logged_in_unique,page_website_clicks_by_site_logged_in_unique,page_get_directions_clicks_logged_in_by_city_unique,page_fans,page_fans_country,page_fan_adds,page_fans_by_like_source,page_fan_removes,page_fans_by_unlike_source_unique,page_tab_views_login_top,page_views_total,page_views_by_profile_tab_total,page_views_by_site_logged_in_unique,page_views_by_referers_logged_in_unique,page_video_views,page_video_views_paid,page_video_views_organic,page_video_views_autoplayed,page_video_views_click_to_play,page_video_views_unique,page_video_view_time,page_posts_impressions_viral,page_posts_impressions_nonviral,page_posts_impressions_paid,page_posts_impressions_organic,page_posts_impressions';
                try {
                  $request = $this->fb->get("/{$page_id}/insights/{$metrics}?&period=day&since=".$from_date."&until=".$to_date,$access_token);
                  $response['data'] = $request->getGraphList()->asArray();
                  return $response;

                } catch (Facebook\Exceptions\FacebookResponseException $e) {
                  $response['error']='1';
                  $response['message']= $e->getMessage();
                  return $response;


                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        $response['error']='1';
                        $response['message']= $e->getMessage();
                        return $response;
                }


        }


        /* Add Ice Breaker Questions */
        public function add_ice_breakers($post_access_token='',$icebreakers_content_json='',$social_media_type='fb')
        {

                if($social_media_type=='ig'){
                        $url = "https://graph.facebook.com/v23.0/me/messenger_profile?platform=instagram&access_token={$post_access_token}";
                        $icebreakers_content_array=json_decode($icebreakers_content_json,true);
                        $icebreakers_content_array['platform']="instagram";
                        $icebreakers_content_json=json_encode($icebreakers_content_array);
                }
                else
                        $url = "https://graph.facebook.com/v23.0/me/messenger_profile?access_token={$post_access_token}










// Note: This code has been updated to use Instagram Graph API v23.0.
// Please ensure your Facebook App and permissions are configured for this API version.
// Some metrics for insights (e.g., 'impressions' for media created after July 1, 2024)
// may be deprecated in newer API versions. Refer to Meta for Developers documentation for
// the most up-to-date information on available metrics.




// Start instagram function are here
public function instagram_account_check_by_id($page_id='', $page_access_token='')
{
        try
        {
                $request = $this->fb->get("v23.0/{$page_id}?fields=instagram_business_account", $page_access_token);
                $response = $request->getGraphObject()->asArray();
                if(isset($response['instagram_business_account']['id']))
                {
                        $instagram_business_account_id = $response['instagram_business_account']['id'];
                }else
                {
                        $instagram_business_account_id = "";
                }
                return $instagram_business_account_id;
        }

        catch(Facebook\Exceptions\FacebookResponseException $e)
        {
                return $instagram_business_account_id="";
        }
        catch(Facebook\Exceptions\FacebookSDKException $e)
        {
                return $instagram_business_account_id="";
        }
}

public function instagram_account_info($instagram_account_id,$page_access_token)
{
        $request = $this->fb->get("v23.0/{$instagram_account_id}?fields=id,username,followers_count,media_count,website,biography", $page_access_token);
        $response = $request->getGraphObject()->asArray();
        return $response;
}

public function get_postlist_from_instagram_account($instagram_account_id,$page_access_token)
{

        $limit = 100;

        $request = $this->fb->get("v23.0/{$instagram_account_id}/media?fields=id,timestamp,caption,like_count,comments_count,media_type,media_url,permalink,is_comment_enabled&limit={$limit}", $page_access_token);
        $response = $request->getGraphList()->asArray();

        $response= json_encode($response);
        $response=json_decode($response,true);

        $final_data['data']=$response;
        return $final_data;
}


public function instagram_get_post_info_by_id($media_id,$page_access_token)
{
        $request = $this->fb->get("v23.0/{$media_id}?fields=caption,media_type,timestamp,permalink", $page_access_token);
        $response = $request->getGraphObject()->asArray();

        $response= json_encode($response);
        $response=json_decode($response,true);

        //$final_data['data']=$response;
        return $response;

        //$results= json_decode($results,TRUE);
   //return $results;
}

public function instagram_get_media_info_by_comment($commentId, $userAccessToken)
{
        $response = $this->fb->get("v23.0/{$commentId}?fields=media,username,text,like_count", $userAccessToken);
        $data = $response->getGraphObject()->asArray();
        $data = json_encode($data);
    $data = json_decode($data,true);
    return $data;
}

public function instagram_get_all_comment_of_post($post_id,$page_access_token)
{
        $response = $this->fb->get("v23.0/{$post_id}/comments?fields=id,text,timestamp,username&limit=20", $page_access_token);
        $data = $response->getGraphList()->asArray();

        $data = json_encode($data);
    $data = json_decode($data,true);
    return $data;            
}

public function instagram_get_all_comment_of_mention_post($user_id,$comment_id,$user_access_token)
{
        $response = $this->fb->get("v23.0/{$user_id}?fields=mentioned_comment.comment_id({$comment_id}){username,text,timestamp,media{id,media_url,permalink,media_type}}", $user_access_token);
        $data = $response->getGraphObject()->asArray();

        $data = json_encode($data);
    $data = json_decode($data,true);

    return $data;            
}

public function instagram_get_all_comment_of_mention_caption($user_id,$media_id,$user_access_token)
{
        $response = $this->fb->get("v23.0/{$user_id}?fields=mentioned_media.media_id({$media_id}){caption,media_type,username,timestamp,media_url}", $user_access_token);
        $data = $response->getGraphObject()->asArray();

        $data = json_encode($data);
    $data = json_decode($data,true);

    return $data;            
}

public function instagram_get_media_url($user_id,$media_id,$user_access_token)
{
          $response = $this->fb->get("v23.0/{$media_id}?fields=id,media_type,media_url,owner,timestamp,permalink", $user_access_token);
        $data = $response->getGraphObject()->asArray();

        $data = json_encode($data);
    $data = json_decode($data,true);

    return $data;
}

public function instagram_get_hashtag_id($user_id,$tag,$user_access_token)
{
        // The ig_hashtag_search endpoint typically does not require a version prefix in the URL
        $url = "https://graph.facebook.com/ig_hashtag_search?user_id={$user_id}&access_token={$user_access_token}&q={$tag}";
        $results= $this->run_curl_for_fb($url);
        return json_decode($results,TRUE);
}

public function instagram_get_hashtag_result($user_id,$hashtag_id,$result_type,$user_access_token)
{
        $url = "https://graph.facebook.com/v23.0/{$hashtag_id}/{$result_type}?user_id={$user_id}&access_token={$user_access_token}&fields=id,media_url,like_count,comments_count,permalink,caption,media_type&limit=50";
        $results= $this->run_curl_for_fb($url);
        return json_decode($results,TRUE);
}



public function instagram_hide_comment($comment_id,$post_access_token)
{
        $url="https://graph.facebook.com/v23.0/{$comment_id}?method=post&access_token={$post_access_token}&hide=true";
        $results= $this->run_curl_for_fb($url);
        return json_decode($results,TRUE);
}

public function instagram_delete_comment($comment_id,$post_access_token)
{
        $url="https://graph.facebook.com/v23.0/{$comment_id}?access_token={$post_access_token}&method=delete";
        $resuls = $this->run_curl_for_fb($url);
        return json_decode($resuls,TRUE);
}
public function instagram_auto_comment($auto_reply_comment_message, $comment_id, $user_access_token)
{
        $response = $this->fb->post(
            "v23.0/{$comment_id}/replies",
            array (
              "message" => $auto_reply_comment_message
            ),
            $user_access_token
        );
        return $response->getGraphObject()->asArray();
}




public function instagram_direct_auto_comment($message,$object_id,$page_access_token)
{
        $params['message']=$message;
        $response = $this->fb->post("v23.0/{$object_id}/comments",$params,$page_access_token);
        return $response->getGraphObject()->asArray();        
}

public function instagram_auto_mention_comment($auto_reply_comment_message, $media_id, $user_access_token, $user_id, $comment_id)
{
        $auto_reply_comment_message=urlencode($auto_reply_comment_message);
        $url="https://graph.facebook.com/v23.0/{$user_id}/mentions?comment_id={$comment_id}&media_id={$media_id}&message={$auto_reply_comment_message}&access_token={$user_access_token}&method=post";
        $resuls = $this->run_curl_for_fb($url);
        return json_decode($resuls,TRUE);
}
public function instagram_auto_mention_caption_comment($auto_reply_comment_message, $media_id, $user_access_token, $user_id)
{
        $auto_reply_comment_message=urlencode($auto_reply_comment_message);
        $url="https://graph.facebook.com/v23.0/{$user_id}/mentions?&media_id={$media_id}&message={$auto_reply_comment_message}&access_token={$user_access_token}&method=post";
        $resuls = $this->run_curl_for_fb($url);
        return json_decode($resuls,TRUE);
}

public function instagram_business_discovery_data($my_instagram_account_id, $my_user_access_token, $discover_username='')
{
          $response = $this->fb->get(
            "v23.0/{$my_instagram_account_id}?fields=business_discovery.username({$discover_username}){followers_count,media_count}",
            $my_user_access_token
          );
          return $response->getGraphObject()->asArray();
}
public function instagram_business_discovery_media_data($my_instagram_account_id, $my_user_access_token, $discover_username='')
{
        $response = $this->fb->get(
            "v23.0/{$my_instagram_account_id}?fields=business_discovery.username({$discover_username}){followers_count,media_count,media{caption,comments_count,like_count,media_type,media_url,permalink}}",
            $my_user_access_token
          );
          return $response->getGraphObject()->asArray();
}
public function instagram_check_instagram_username($my_instagram_account_id, $my_user_access_token, $discover_username='')
{
        try
        {
                  $response = $this->fb->get(
            "v23.0/$my_instagram_account_id?fields=business_discovery.username($discover_username)",
            $my_user_access_token
                  );
                $report['status'] = 'success';
                return json_encode($report);
        }
        catch(Facebook\Exceptions\FacebookResponseException $e) {
                $report['status'] = 'error';
                $report['message'] = $e->getMessage();
                return json_encode($report);
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $report['status'] = 'error';
                $report['message'] = $e->getMessage();
                return json_encode($report);
        }
}
public function instagram_user_insight($business_account_id='',$metric='',$period='',$access_toke='')
{
        $response = $this->fb->get("v23.0/{$business_account_id}/insights?metric=$metric&period=$period",$access_toke);
        return $response = $response->getGraphList()->asArray();
}

public function instagram_media_insights($media_id='',$metric='',$access_toke='')
{
        try
        {                        
                $response = $this->fb->get("v23.0/{$media_id}/insights?metric=$metric",$access_toke);
                return $response = $response->getGraphList()->asArray();
        }
        catch(Facebook\Exceptions\FacebookResponseException $e) {
                $report['status'] = 'error';
                $report['message'] = $e->getMessage();
                return $report;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $report['status'] = 'error';
                $report['message'] = $e->getMessage();
                return $report;
        }
}



public function instagram_media_comment_enable_disable($media_id,$user_access_token,$is_enable=true)
{
          $response = $this->fb->post(
            "v23.0/{$media_id}",
            array (
              "comment_enabled" => $is_enable
            ),
            $user_access_token
        );
        return $response->getGraphObject()->asArray();


}


// to get the media objects in which a Business or Creator Account has been tagged

public function instagram_tagged_media($business_account_id='',$access_token='')
{
        try
        {                        
                $response = $this->fb->get("v23.0/{$business_account_id}/tags?fields=permalink,media_type,media_url,timestamp,username,caption&limit=100",$access_token);
                return $response = $response->getGraphList()->asArray();
        }
        catch(Facebook\Exceptions\FacebookResponseException $e) {
                $report['status'] = 'error';
                $report['message'] = $e->getMessage();
                return $report;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $report['status'] = 'error';
                $report['message'] = $e->getMessage();
                return $report;
        }
}


// Return array . Index :  id for success .  For error ,  status=error & message=error message. 

public function instagram_create_post($business_account_id='',$type="IMAGE",$media_url='',$caption="",$user_access_token='')
{
        $is_carousel = false;
        if(is_array($media_url) && count($media_url) > 1)
                $is_carousel = true;
        $response = $this->instagram_create_media_container($business_account_id,$type,$media_url,$caption,$user_access_token,$is_carousel);
        $sleep_value = 0;
        if(isset($response['has_video']) && $response['has_video'] == 'yes')
                $sleep_value = 30;
        unset($response['has_video']);

        if(isset($response['status']) && $response['status']=="error"){
                $report['status'] = 'error';
                $report['message'] = $response['message'];
                return $report;
        }

        // $container_id = $response['id'] ?? "";

        $response=$this->instagram_publishing_post_from_container($business_account_id,$response,$user_access_token,$caption,$sleep_value);

        if(isset($response['status']) && $response['status']=="error"){
                $report['status'] = 'error';
                $report['message'] = $response['message'];
                return $report;
        }

        return $response;


}



public function instagram_create_media_container($business_account_id='',$type="IMAGE",$media_url='',$caption="",$user_access_token='',$is_carousel=false){

        // First Create Media Container 
        if(!is_array($media_url))
                $media_urls[0]=$media_url;
        else
                $media_urls=$media_url;

        $container_ids = [];
        $container_ids['has_video'] = 'no';
        $media_urls = array_filter($media_urls);
        foreach ($media_urls as $m_url) 
        {
                $params=array();
                $ext_array = explode('.',$m_url);
                $ext_type = array_pop($ext_array);
                $ext_type = strtolower($ext_type);
                if($ext_type == 'png' || $ext_type == 'jpeg' || $ext_type == 'jpg')
                        $type = "IMAGE";
                else
                        $type = "VIDEO";

                if($type=="IMAGE"){
                        $params['image_url'] = trim($m_url);
                }
                else{
                        $params['video_url'] = trim($m_url);
                        $params['media_type'] = "REELS"; // Assuming Reels for videos based on common practice.
                        $container_ids['has_video'] = 'yes';
                }

                if(isset($caption) && $caption!="" && $is_carousel===false){
                        $params['caption'] = $caption;
                }

                if($is_carousel===true)
                        $params['is_carousel_item'] = true;

                try
                {                        
                        $response = $this->fb->post("v23.0/{$business_account_id}/media",$params,$user_access_token);
                        $container_ids[] = $response->getGraphObject()->asArray()['id'];        
                        if($type == 'VIDEO')
                                sleep(10); // Sleep for video processing
                }
                catch(Facebook\Exceptions\FacebookResponseException $e) {
                        $report['status'] = 'error';
                        $report['message'] = $e->getMessage();
                        return $report;
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        $report['status'] = 'error';
                        $report['message'] = $e->getMessage();
                        return $report;
                }
        }

        return $container_ids;

}
public function instagram_publishing_post_from_container($business_account_id,$creation_id,$user_access_token,$caption='',$sleep_value=0){

        $params=array();
        $no_media = count($creation_id);
        if($no_media == 1)
                $params['creation_id'] = $creation_id[0] ?? '';
        else
        {
                $params_carousel['children'] = $creation_id;
                $params_carousel['media_type'] = 'CAROUSEL';
                $params_carousel['caption'] = $caption;

                try
                {                        
                        $response = $this->fb->post("v23.0/{$business_account_id}/media",$params_carousel,$user_access_token);
                        $params['creation_id']=$response->getGraphObject()->asArray()['id'];                        
                }
                catch(Facebook\Exceptions\FacebookResponseException $e) {
                        $report['status'] = 'error';
                        $report['message'] = $e->getMessage();
                        return $report;
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        $report['status'] = 'error';
                        $report['message'] = $e->getMessage();
                        return $report;
                }


        }

        try
        {        
                if($sleep_value != 0) sleep($sleep_value);
                $response = $this->fb->post("v23.0/{$business_account_id}/media_publish",$params,$user_access_token);
                return $response->getGraphObject()->asArray();                        
        }
        catch(Facebook\Exceptions\FacebookResponseException $e) {
                $report['status'] = 'error';
                $report['message'] = $e->getMessage();
                return $report;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $report['status'] = 'error';
                $report['message'] = $e->getMessage();
                return $report;
        }
}

}

//จบไฟล์ library/fb_rx_login.php
