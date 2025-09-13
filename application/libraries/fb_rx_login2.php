<?php  
include("Facebook/autoload.php");

class Fb_rx_login
{                                
    public $database_id = ""; 
    public $app_id = "";
    public $app_secret = "";                
    public $user_access_token = "";
    public $fb;
    
    const GRAPH_API_VERSION = 'v20.0';

    function __construct()
    {
        // โค้ดส่วนนี้เหมือนเดิมตามที่ปรับปรุงไว้ในคำตอบก่อนหน้า
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->helper('my_helper');
        $this->CI->load->library('session');
        $this->CI->load->model('basic');
        
        $this->encryption_key = $this->CI->config->item('encryption_key');
        $this->encryption_iv = $this->CI->config->item('encryption_iv');

        $this->database_id = $this->CI->session->userdata("fb_rx_login_database_id");
        
        $this->initializeConfig();
        $this->checkUserAuthorization();
    }

    public function app_initialize($fb_rx_login_database_id)
    {
        // โค้ดส่วนนี้เหมือนเดิมตามที่ปรับปรุงไว้ในคำตอบก่อนหน้า
        $this->database_id = $fb_rx_login_database_id;
        $this->initializeConfig();
    }

    public function login_for_user_access_token($redirect_url = "")
    {        
        $redirect_url = rtrim($redirect_url, '/');
        $helper = $this->fb->getRedirectLoginHelper();

        $permissions = [
            'email',
            'pages_manage_posts',
            'pages_manage_engagement',
            'pages_manage_metadata',
            'pages_read_engagement',
            'pages_show_list',
            'pages_messaging',
            'public_profile',
            'read_insights',
            'business_management'
        ];
        
        if ($this->CI->config->item('instagram_reply_enable_disable') == '1') {
            array_push($permissions, 'instagram_basic', 'instagram_manage_comments', 'instagram_manage_insights', 'instagram_content_publish', 'instagram_manage_messages');
        }

        $loginUrl = $helper->getLoginUrl($redirect_url, $permissions);
        
        return '<a class="btn btn-block btn-social btn-facebook" href="' . htmlspecialchars($loginUrl) . '"><span class="fab fa-facebook"></span> ล็อกอินด้วย Facebook</a>';
    }

    /**
     * จัดการ Callback จากการล็อกอินเพื่อรับ User Access Token
     * @param string $redirect_url URL ที่ใช้ในการ Redirect
     * @param bool $withEmail ดึงข้อมูลอีเมลหรือไม่
     * @return array ข้อมูลผู้ใช้และ Long-lived Access Token
     */
    public function login_callback($redirect_url = "", $withEmail = true)
    {
        $redirect_url = rtrim($redirect_url, '/');
        $helper = $this->fb->getRedirectLoginHelper();
        $fields = $withEmail ? 'id,name,email' : 'id,name';

        try {
            $accessToken = $helper->getAccessToken($redirect_url);
            $longLivedAccessToken = $helper->getLongLivedAccessToken($accessToken);
            
            $response = $this->fb->get("/me?fields={$fields}", $longLivedAccessToken);
            $user = $response->getGraphUser()->asArray();

            $user['access_token_set'] = (string) $longLivedAccessToken;

            return $user;
        } catch (Facebook\Exceptions\FacebookResponseException | Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'status' => '0',
                'message' => $e->getMessage()
            ];
        }
    }

    public function app_id_secret_check()
    {
        return ($this->app_id == '' || $this->app_secret == '') ? 'not_configured' : null;
    }

    /**
     * ตรวจสอบความถูกต้องของ Access Token
     * @param string $access_token Access Token ที่ต้องการตรวจสอบ (ถ้าไม่ระบุ จะใช้ $this->user_access_token)
     * @return bool true ถ้า Access Token ถูกต้อง, false ถ้าไม่
     */
    function access_token_validity_check($access_token = null)
    {
        $token = $access_token ?? $this->user_access_token;
        if (empty($token)) {
            return false;
        }
        
        try {
            $response = $this->fb->get("/debug_token?input_token={$token}&access_token={$this->app_id}|{$this->app_secret}");
            $debugToken = $response->getGraphNode();
            return $debugToken->getField('is_valid') && $debugToken->getField('app_id') === $this->app_id;
        } catch (Exception $e) {
            return false;
        }
    }

    public function get_page_list($access_token = "")
    {
        try {
            $request = $this->fb->get("/me/accounts?fields=cover,picture,id,name,access_token&limit=400", $access_token);
            return $request->getGraphList()->asArray();
        } catch (Facebook\Exceptions\FacebookResponseException | Facebook\Exceptions\FacebookSDKException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
    
    public function get_page_insight_info($access_token, $metrics, $page_id)
    {
        $from = date('Y-m-d', strtotime('-28 days'));
        $to = date('Y-m-d', strtotime('-1 day'));
        
        try {
            $request = $this->fb->get("/{$page_id}/insights?metric={$metrics}&since={$from}&until={$to}", $access_token);
            return $request->getGraphList()->asArray();
        } catch (Facebook\Exceptions\FacebookResponseException | Facebook\Exceptions\FacebookSDKException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function get_group_list($access_token = "")
    {
        try {
            $request = $this->fb->get('/me/groups?fields=cover,picture,id,name&limit=400&admin_only=1', $access_token);
            return $request->getGraphList()->asArray();
        } catch (Facebook\Exceptions\FacebookResponseException | Facebook\Exceptions\FacebookSDKException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * เรียกใช้งาน cURL เพื่อส่งคำขอ API
     * @param string $url URL ของ API
     * @param array $options ตัวเลือกสำหรับ cURL
     * @return array ผลลัพธ์จากการเรียก API
     */
    private function run_curl($url, $options = [])
    {
        $ch = curl_init();
        $defaultOptions = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => ["Content-type: application/json"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => "Mozilla/5.0",
            CURLOPT_SSL_VERIFYPEER => true
        ];
        
        curl_setopt_array($ch, $defaultOptions + $options);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode !== 200 || !empty($error)) {
            return ['error' => true, 'message' => $error ?: "HTTP Error: $httpCode"];
        }
        
        return json_decode($result, true);
    }
    
    public function send_user_roll_access($app_id, $user_id, $user_access_token)
    {
        $url = "https://graph.facebook.com/" . self::GRAPH_API_VERSION . "/{$app_id}/roles";
        $options = [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode(['user' => $user_id, 'role' => 'testers', 'access_token' => $user_access_token])
        ];
        return $this->run_curl($url, $options);
    }

    public function block_person_from_commenting($page_id, $commenter_id, $page_access_token)
    {
        $url = "https://graph.facebook.com/" . self::GRAPH_API_VERSION . "/{$page_id}/blocked";
        $options = [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode(['user' => $commenter_id, 'access_token' => $page_access_token])
        ];
        return $this->run_curl($url, $options);
    }

    public function get_metrics_page_post($post_id, $page_access_token)
    {
        $url = "https://graph.facebook.com/" . self::GRAPH_API_VERSION . "/{$post_id}/insights?metric=post_reactions_like_total,post_reactions_love_total,post_reactions_wow_total&access_token={$page_access_token}";
        return $this->run_curl($url);
    }

    public function get_videolist_from_fb_page($page_id, $access_token)
    {
        $url = "https://graph.facebook.com/" . self::GRAPH_API_VERSION . "/$page_id/videos?access_token=$access_token&fields=is_crossposting_eligible,description,created_time,permalink_url,picture";
        return $this->run_curl($url);
    }

    public function get_crosspost_whitelisted_pages($page_id, $access_token)
    {
        $url = "https://graph.facebook.com/" . self::GRAPH_API_VERSION . "/$page_id/crosspost_whitelisted_pages?access_token=$access_token&limit=200";
        return $this->run_curl($url);
    }

    public function get_postlist_from_fb_page($page_id, $access_token)
    {
        try {
            $request = $this->fb->get("{$page_id}/posts?fields=id,message,permalink_url,picture,created_time&limit=50", $access_token);
            $response = $request->getGraphList()->asArray();
            return ['data' => $response];
        } catch (Facebook\Exceptions\FacebookResponseException | Facebook\Exceptions\FacebookSDKException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    function get_meta_tag_fb($url)
    {  
        $response = $this->run_curl($url);
        
        if (isset($response['error'])) {
            return ['error' => true, 'message' => $response['message']];
        }

        $html = json_encode($response);
        
        $doc = new DOMDocument();
        @$doc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $html);
        
        $title = '';
        $nodes = $doc->getElementsByTagName('title');
        if (isset($nodes->item(0)->nodeValue)) {
            $title = $nodes->item(0)->nodeValue;
        }
        
        $metaData = ['title' => $title, 'image' => '', 'description' => '', 'author' => ''];
        $metas = $doc->getElementsByTagName('meta');

        foreach ($metas as $meta) {
            $property = $meta->getAttribute('property');
            $name = $meta->getAttribute('name');
            $content = $meta->getAttribute('content');

            if ($property == 'og:title') {
                $metaData['title'] = $content;
            } else if ($property == 'og:image') {
                $metaData['image'] = $content;
            } else if ($property == 'og:description') {
                $metaData['description'] = $content;
            } else if ($name == 'author') {
                $metaData['author'] = $content;
            } else if ($name == 'description' && empty($metaData['description'])) {
                $metaData['description'] = $content;
            }
        }
        
        return $metaData;
    }

    // ฟังก์ชันที่เกี่ยวกับ License ควรถูกย้ายออกไป
    // และควรปรับปรุงการแสดงผลให้เป็นมิตรกับผู้ใช้
    public function view_loader()
    {
        if (strpos(base_url(), 'localhost') !== FALSE) {
            return true;
        }

        if (file_exists(APPPATH.'config/licence.txt')) {
            $config_existing_content = file_get_contents(APPPATH.'config/licence.txt');
            $config_decoded_content = json_decode($config_existing_content, true);
            $purchase_code = $config_decoded_content['purchase_code'];
            $base_url = base_url();
            
            // ควรใช้ Helper function get_domain_only() และนำเข้า Library ด้วยตนเอง
            $domain_name = get_domain_only($base_url);

            // โค้ดนี้มีความเสี่ยงสูง ควรปรับปรุงให้ไม่ลบไฟล์
            $url = "http://xeroneit.net/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$domain_name}&item_name=FBInboxer";

            $credentials = $this->get_general_content_with_checking_library($url);
            $decoded_credentials = json_decode($credentials, true);

            if (isset($decoded_credentials['error']) || $decoded_credentials['content']['status'] !== 'success') {
                // แทนที่จะลบไฟล์ ควรแสดงผลข้อความแจ้งเตือนที่ชัดเจน
                // หรือ redirect ไปยังหน้า License เพื่อให้ผู้ใช้ป้อนข้อมูลใหม่
                return false;
            }
        } else {
            // หากไฟล์ License หายไป ควรแสดงผลข้อความแจ้งเตือนที่ชัดเจน
            return false;
        }
    }

    public function get_general_content_with_checking_library($url, $proxy = "")
    {
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible;)",
            CURLOPT_AUTOREFERER => false,
            CURLOPT_CONNECTTIMEOUT => 7,
            CURLOPT_REFERER => 'http://'.$url,
            CURLOPT_FAILONERROR => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 50,
            CURLOPT_POST => 0,
            CURLOPT_SSL_VERIFYPEER => true
        ];
        
        curl_setopt_array($ch, $options);
        
        $content = curl_exec($ch);
        $response = ['content' => $content];
        
        $res = curl_getinfo($ch);
        if ($res['http_code'] != 200 || !empty(curl_error($ch))) {
            $response['error'] = 'error';
        }
        curl_close($ch);
        
        return json_encode($response);
    }
}
