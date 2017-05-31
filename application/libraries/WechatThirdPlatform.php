<?php
/**
 * 微信第三方开放平台接入类
 * ===================================================================
 * 名词解释：
 * --平台：微信第三方开放平台
 * --公众号：接入微信第三方开放平台的微信公众号
 * --用户：普通微信用户
 * Version 0.1.0 BETA
 * Create by LeeNux @ 2017/3/1
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class WechatThirdPlatform {
    const WXTP_AUTH_COMPONENT_ACCESS_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token'; // POST
    const WXTP_AUTH_PRE_CODE = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=%s';
    const WXTP_AUTH_GUIDE_URL = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s';
    const WXTP_AUTH_QUERY_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=%s';
    const WXTP_AUTH_REFRESH_TOKEN = 'https://api.weixin.qq.com /cgi-bin/component/api_authorizer_token?component_access_token=%s';
    const WXTP_AUTH_GET_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=%s';

    const WXTP_WEB_GET_CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s&component_appid=%s#wechat_redirect';
    const WXTP_WEB_GET_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=%s&code=%s&grant_type=authorization_code&component_appid=%s&component_access_token=%s';
    const WXTP_WEB_REFRESH_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/component/refresh_token?appid=%s&grant_type=refresh_token&component_appid=%s&component_access_token=%s&refresh_token=%s';
    const WXTP_WEB_GET_USER_INFO_URL = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=%s';

    const JSONF_WX_TICKEY = 'wx_tickey';
    const JSONF_WX_COMPONENT_ACCESS_TOKEN = 'wx_component_access_token';

    // 为配置文件定义属性，没定义的将不会成功加载
    private $tp_appid = '';
    private $tp_secret = '';
    private $tp_token = '';
    private $tp_aeskey = '';

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->library(array('curl', 'jsonf'));
        $this->LoadConfig();
    }

    /**
     * 加载CI的配置文件
     */
    private function LoadConfig() {
        try {
            $this->CI->config->load('wechat', true, true);
            $config = $this->CI->config->item('wechat');
            if (count($config) > 0) {
                foreach ($config as $key => $val) {
                    if (isset($this->$key)) {
                        $this->$key = $val;
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', $e);
        }
    }

    /**
     * ===================================================================
     * 微信通信辅助工具
     * ===================================================================
     */

    /**
     * 解密微信消息
     * ===================================================================
     * !!  需要安装PHP-mcrypt扩展
     *
     * @param $postStr
     * @return string
     */
    public function DecryptMsg($postStr) {
        require_once FCPATH . 'sdk/WXMsgCrypt/wxBizMsgCrypt.php';

        $timeStamp = $this->CI->input->get('timestamp');
        $nonce = $this->CI->input->get('nonce');
        $msg_sign = $this->CI->input->get('msg_signature');

        $pc = new WXBizMsgCrypt($this->tp_token, $this->tp_aeskey, $this->tp_appid);

        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $postStr, $msg);
        if ($errCode !== 0) {
            log_message('error', $errCode);
        }

        return $msg;
    }

    public function EncryptMsg() {
    }


    /**
     * ===================================================================
     * step 1 : component_verify_ticket
     * ===================================================================
     */


    /**
     * 保存平台Tickey
     * @param $postObj
     */
    public function SaveVerifyTickey($postObj) {
        try {
            $this->CI->jsonf->Save(self::JSONF_WX_TICKEY, json_encode($postObj));
        } catch (Exception $e) {
            log_message('error', $e);
        }
    }

    /**
     * 加载平台Tickey
     * @return mixed
     */
    public function LoadVerifyTickey($toArray = true) {
        try {
            return $this->CI->jsonf->Load(self::JSONF_WX_TICKEY, $toArray);
        } catch (Exception $e) {
            log_message('error', $e);
        }
    }

    /**
     * ===================================================================
     * step 2 : Component_access_token
     * ===================================================================
     */

    /**
     * 获取平台 Component_access_token
     * 先从本地存储中获取，若不存在则在服务器远程拉取
     */
    public function GetComponentAccessToken() {
        $componentAccessToken = $this->LoadComponentAccessToken();
        if ($componentAccessToken['end_time'] < time()) {
            $componentAccessToken = $this->SaveComponentAccessToken();
        }
        return $componentAccessToken['component_access_token'];
    }

    /**
     * 保存平台 Component_access_token
     */
    private function SaveComponentAccessToken() {
        $ticket = $this->LoadVerifyTickey();
        $ticket = $ticket['ComponentVerifyTicket'];
        // 这里有个坑：微信这个接口需要提交的数据为一个完整的json，而不是三个独立的参数
        $params = array(
            'component_appid' => $this->tp_appid,
            'component_appsecret' => $this->tp_secret,
            'component_verify_ticket' => $ticket
        );
        $componentAccessToken = $this->CI->curl->simple_post(self::WXTP_AUTH_COMPONENT_ACCESS_TOKEN, json_encode($params));
        $componentAccessToken = json_decode($componentAccessToken, true);
        if ($componentAccessToken['errcode'] == 0 || $componentAccessToken == null) {
            $componentAccessToken['end_time'] = $componentAccessToken['expires_in'] + time() - 200;
            $this->CI->jsonf->Save(self::JSONF_WX_COMPONENT_ACCESS_TOKEN, json_encode($componentAccessToken));
        }
        return $componentAccessToken;
    }

    /**
     * 加载平台 Component_access_token
     * @param bool $toArray
     * @return bool
     */
    private function LoadComponentAccessToken($toArray = true) {
        try {
            return $this->CI->jsonf->Load(self::JSONF_WX_COMPONENT_ACCESS_TOKEN, $toArray);
        } catch (Exception $e) {
            log_message('error', $e);
        }
        return false;
    }

    /**
     * ===================================================================
     * step 3 : pre_auth_code
     * ===================================================================
     */

    /**
     * 获取平台预授权码
     * @param $componentAccessToken
     * @return mixed
     * ===================================================================
     * pre_auth_code:"preauthcode@@@Sk1iUQOH4K.....",
     * expires_in:1800
     */
    public function GetPreCode($componentAccessToken) {
        $url = sprintf(self::WXTP_AUTH_PRE_CODE, $componentAccessToken);
        $params = array(
            'component_appid' => $this->tp_appid
        );
        return $this->CI->curl->simple_post($url, json_encode($params));
    }

    /**
     * ===================================================================
     * step 4 : authorizer_access_token
     * ===================================================================
     */

    /**
     * 引导公众号授权
     * @param $preCode
     * @param $reUrl
     */
    public function AuthCheck($preCode, $reUrl) {
        $url = sprintf(self::WXTP_AUTH_GUIDE_URL, $this->tp_appid, $preCode, $reUrl);
        redirect($url);
    }

    /**
     * 获取公众号授权信息
     * @param $authCode
     * @param $componentAccessToken
     */
    public function GetAuthAccess($componentAccessToken, $authCode) {
        $url = sprintf(self::WXTP_AUTH_QUERY_TOKEN, $componentAccessToken);
        $params = array(
            'component_appid' => $this->tp_appid,
            'authorization_code' => $authCode
        );
        $authAccessToken = $this->CI->curl->simple_post($url, json_encode($params));
        return $authAccessToken;
    }

    /**
     * ===================================================================
     * step 5 : authorizer_refresh_token
     * ===================================================================
     */
    public function RefreshAuthAccessToken($componentAccessToken, $authAppid, $refreshToken) {
        $url = sprintf(self::WXTP_AUTH_REFRESH_TOKEN, $componentAccessToken);
        $params = array(
            'component_appid' => $this->tp_appid,
            'authorizer_appid' => $authAppid,
            'authorizer_refresh_token' => $refreshToken
        );
        $authAccessToken = $this->CI->curl->simple_post($url, json_encode($params));
        return $authAccessToken;
    }

    /**
     * ===================================================================
     * step 6 : get_authorizer_info
     * ===================================================================
     */

    /**
     * 获取公众号基本信息
     * @param $componentAccessToken
     * @param $authAppid
     * @return mixed
     */
    public function GetAuthorizerInfo($componentAccessToken, $authAppid) {
        $url = sprintf(self::WXTP_AUTH_GET_INFO, $componentAccessToken);
        $params = array(
            'component_appid' => $this->tp_appid,
            'authorizer_appid' => $authAppid,
        );
        $authorizerInfo = $this->CI->curl->simple_post($url, json_encode($params));
        return $authorizerInfo;
    }
}