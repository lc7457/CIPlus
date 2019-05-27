<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'plus/CIClass.abstract.php';

/**
 * 微信开放平台：微信用户相关接口
 * Class WxOpenUserAuth
 */
Class OpenUser extends \CIPlus\CIClass {
    const GET_CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s&component_appid=%s#wechat_redirect';
    const GET_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=%s&code=%s&grant_type=authorization_code&component_appid=%s&component_access_token=%s';
    const GET_TOKEN_REFRESH_URL = 'https://api.weixin.qq.com/sns/oauth2/component/refresh_token?appid=%s&grant_type=refresh_token&component_appid=%s&component_access_token=%s&refresh_token=%s';
    const GET_USER_INFO_URL = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=%s';
    
    protected $wechat_open_appid = "";
    protected $wechat_open_secret = "";
    
    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('wechat_open');
    }
    
    /**
     * 向微信服务器请求交换AccessToken的code
     * @param $redirectUrl : 接收code重定向地址
     * @param $mpId : 授权公众号
     * @param $type : 应用授权作用域：
     *        1、snsapi_base：不弹出授权页面，直接跳转，只能获取用户openid；
     *        2、snsapi_userinfo：弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息
     * @param $state : 自定义返回值
     */
    public function askAccessCode($redirectUrl, $mpId, $type = "snsapi_userinfo", $state = "GetCode") {
        $url = sprintf(self::GET_CODE_URL, $mpId, urlencode($redirectUrl), $type, $state, $this->wechat_open_appid);
        $this->CI->load->library('JsonStorage');
        redirect($url);
    }
    
    /**
     * 请求用户授权
     * @param string $mpId 公众号APPID
     * @param $code
     * @param string $componentId 开放平台APPID
     * @param $componentAccessToken
     * @return mixed
     */
    public function getAccessToken($mpId, $code, $componentId = '', $componentAccessToken = '') {
        if (empty($componentId)) {
            $componentId = $this->wechat_open_appid;
        }
        if (empty($componentAccessToken)) {
            $this->CI->load->library('OpenLicensing');
            $componentAccessToken = $this->CI->openlicensing->getComponentAccessToken();
        }
        $url = sprintf(self::GET_TOKEN_URL, $mpId, $code, $componentId, $componentAccessToken);
        return $this->CI->curl->simple_get($url);
    }
    
    /**
     * 刷新获取用户授权
     * @param $mpId : 微信公众号APPID
     * @param $refreshToken
     * @param $componentId
     * @param $componentAccessToken
     * @return mixed
     */
    public function refreshAccessToken($mpId, $refreshToken, $componentId = '', $componentAccessToken = '') {
        if (empty($componentId)) {
            $componentId = $this->wechat_open_appid;
        }
        if (empty($componentAccessToken)) {
            $this->CI->load->library('OpenLicensing');
            $componentAccessToken = $this->CI->openlicensing->getComponentAccessToken();
        }
        $url = sprintf(self::GET_TOKEN_REFRESH_URL, $mpId, $componentId, $componentAccessToken, $refreshToken);
        return $this->CI->curl->simple_get($url);
    }
    
    /**
     * 获取用户基本信息
     * @param $accessToken
     * @param $openid
     * @param string $lang
     * @return mixed
     */
    public function getUserInfo($accessToken, $openid, $lang = "zh_CN") {
        $url = sprintf(self::GET_USER_INFO_URL, $accessToken, $openid, $lang);
        return $this->CI->curl->simple_get($url);
    }
    
}