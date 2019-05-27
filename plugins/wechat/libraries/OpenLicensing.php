<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'plus/CIClass.abstract.php';

/**
 * 微信开放平台公众号授权
 * Class WxOpenMpAuth
 */
Class OpenLicensing extends \CIPlus\CIClass {
    
    // 开放平台认证相关API
    const OPEN_AUTH_COMPONENT_ACCESS_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token'; // POST
    const OPEN_AUTH_PRE_CODE = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=%s';
    const OPEN_AUTH_GUIDE_URL = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s';
    const OPEN_AUTH_QUERY_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=%s';
    const OPEN_AUTH_REFRESH_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=%s';
    const OPEN_AUTH_GET_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=%s';
    
    // Json 本地存储名称
    const JSON_OPEN_TICKET = 'wechat_open_ticket'; // json 本地存储的ticket文件名称
    const JSON_OPEN_COMPONENT_ACCESS_TOKEN = 'wechat_open_component_access_token'; // json 本地存储的平台AccessToken文件名称
    
    protected $wechat_open_token;
    protected $wechat_open_aes_key;
    protected $wechat_open_appid;
    protected $wechat_open_secret;
    
    public function __construct(array $config = array()) {
        parent::__construct();
        $this->CI->load->library('JsonStorage');
        $this->loadConf('wechat_open');
    }
    
    //region Step 1 : component_verify_ticket
    
    /**
     * 保存平台Ticket
     * @param $postObj
     */
    public function saveVerifyTicket($postObj) {
        try {
            $this->CI->jsonstorage->save(self::JSON_OPEN_TICKET, json_encode($postObj));
            exit('success');
        } catch (Exception $e) {
            log_message('error', $e);
        }
    }
    
    /**
     * 加载平台Ticket
     * @param bool $toArray
     * @return mixed
     */
    public function loadVerifyTicket($toArray = true) {
        try {
            return $this->CI->jsonstorage->load(self::JSON_OPEN_TICKET, $toArray);
        } catch (Exception $e) {
            log_message('error', $e);
            return false;
        }
    }
    //endregion
    
    //region Step 2 : component_access_token
    
    /**
     * 获取平台 Component_access_token
     * 先从本地存储中获取，若不存在则在服务器远程拉取
     */
    public function getComponentAccessToken() {
        $componentAccessToken = $this->loadComponentAccessToken();
        if (!$componentAccessToken || $componentAccessToken['end_time'] < time()) {
            $componentAccessToken = $this->saveComponentAccessToken();
        }
        return $componentAccessToken['component_access_token'];
    }
    
    /**
     * 保存平台 Component_access_token
     */
    private function saveComponentAccessToken() {
        $ticket = $this->loadVerifyTicket();
        $ticket = $ticket['ComponentVerifyTicket'];
        // 这里有个坑：微信这个接口需要提交的数据为一个完整的json，而不是三个独立的参数
        $params = array(
            'component_appid' => $this->wechat_open_appid,
            'component_appsecret' => $this->wechat_open_secret,
            'component_verify_ticket' => $ticket
        );
        $componentAccessToken = $this->CI->curl->simple_post(self::OPEN_AUTH_COMPONENT_ACCESS_TOKEN, json_encode($params));
        $componentAccessToken = json_decode($componentAccessToken, true);
        if ($componentAccessToken['errcode'] == 0 || $componentAccessToken == null) {
            $componentAccessToken['end_time'] = $componentAccessToken['expires_in'] + time() - 200;
            $this->CI->jsonstorage->save(self::JSON_OPEN_COMPONENT_ACCESS_TOKEN, json_encode($componentAccessToken));
        } else {
            log_message('error', json_encode($componentAccessToken));
        }
        return $componentAccessToken;
    }
    
    /**
     * 加载平台 Component_access_token
     * @param bool $toArray
     * @return bool
     */
    private function loadComponentAccessToken($toArray = true) {
        try {
            return $this->CI->jsonstorage->load(self::JSON_OPEN_COMPONENT_ACCESS_TOKEN, $toArray);
        } catch (Exception $e) {
            log_message('error', $e);
        }
        return false;
    }
    
    //endregion
    
    //region Step 3 : pre_auth_code
    
    /**
     * 获取平台预授权码
     * @param $componentAccessToken
     * @return mixed
     * ===================================================================
     * pre_auth_code:"preauthcode@@@Sk1iUQOH4K.....",
     * expires_in:1800
     */
    public function getPreCode($componentAccessToken = '') {
        if (empty($componentAccessToken)) {
            $componentAccessToken = $this->getComponentAccessToken();
        }
        $url = sprintf(self::OPEN_AUTH_PRE_CODE, $componentAccessToken);
        $params = array(
            'component_appid' => $this->wechat_open_appid
        );
        $preCode = $this->CI->curl->simple_post($url, json_encode($params));
        return json_decode($preCode, true);
    }
    
    //endregion
    
    //region Step 4 : authorizer_access_token
    
    /**
     * 引导公众号授权
     * 必须建立一个页面，引导用户点击链接至当前方法中构造的URL地址
     * @param $preCode
     * @param $reUrl
     * @return string
     */
    public function authCheck($preCode, $reUrl) {
        $url = sprintf(self::OPEN_AUTH_GUIDE_URL, $this->wechat_open_appid, $preCode, $reUrl);
        return $url;
    }
    
    /**
     * 获取公众号授权信息
     * @param $componentAccessToken
     * @param $authCode
     * @return mixed
     */
    public function getAuth($authCode, $componentAccessToken = "") {
        if (empty($componentAccessToken)) {
            $componentAccessToken = $this->getComponentAccessToken();
        }
        $url = sprintf(self::OPEN_AUTH_QUERY_TOKEN, $componentAccessToken);
        $params = array(
            'component_appid' => $this->wechat_open_appid,
            'authorization_code' => $authCode
        );
        $authInfo = $this->CI->curl->simple_post($url, json_encode($params));
        return json_decode($authInfo, true);
    }
    
    //endregion
    
    //region Step 5 : authorizer_refresh_token
    
    /**
     * 获取公众号授权刷新码
     * @param $componentAccessToken
     * @param $authAppid
     * @param $refreshToken
     * @return mixed
     */
    public function refreshAuthAccessToken($authAppid, $refreshToken, $componentAccessToken = "") {
        if (empty($componentAccessToken)) {
            $componentAccessToken = $this->getComponentAccessToken();
        }
        $url = sprintf(self::OPEN_AUTH_REFRESH_TOKEN, $componentAccessToken);
        $params = array(
            'component_appid' => $this->wechat_open_appid,
            'authorizer_appid' => $authAppid,
            'authorizer_refresh_token' => $refreshToken
        );
        $authInfo = $this->CI->curl->simple_post($url, json_encode($params));
        return $authInfo;
    }
    
    //endregion
    
    //region Step 6 : get_authorizer_info
    
    /**
     * 获取公众号基本信息
     * @param $componentAccessToken
     * @param $authAppid
     * @return mixed
     */
    public function getInfo($authAppid, $componentAccessToken = "") {
        if (empty($componentAccessToken)) {
            $componentAccessToken = $this->getComponentAccessToken();
        }
        $url = sprintf(self::OPEN_AUTH_GET_INFO, $componentAccessToken);
        $params = array(
            'component_appid' => $this->wechat_open_appid,
            'authorizer_appid' => $authAppid,
        );
        $authorizerInfo = $this->CI->curl->simple_post($url, json_encode($params));
        return json_decode($authorizerInfo, true);
    }
    
    //endregion
    
    // region 公众账号AccessToken相关
    public function getMpAccessToken($appid) {
        $this->CI->load->model('wechat_open_mp_model');
        $token = $this->CI->wechat_open_mp_model->loadMpInfo($appid);
        if (empty($token) || empty($token['authorizer_access_token'])) {
            log_message('error', 'UnAuthorizer wechat MP');
            return false;
        } else if ($token && $token['authorizer_expires_is'] < time()) {
            $token = $this->refreshAuthAccessToken($appid, $token['authorizer_refresh_token']);
            $this->CI->wechat_open_mp_model->refreshMpToken($appid,$token);
        }
        return $token['authorizer_access_token'];
    }
    
    
    // endregion
}