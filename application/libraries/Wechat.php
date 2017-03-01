<?php

/**
 * 微信通信类
 */
Class Wechat
{
    private $appid = "";
    private $secret = "";
    private $token = "";
    private $aeskey = "";
    private $mchid = "";

    const WX_GETTOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
    const WX_GETMENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=%s';
    const WX_SETMENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s';
    const WX_WEB_GETCODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';
    const WX_WEB_GETTOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
    const WX_WEB_GETUSERINFO_URL = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
    const WX_JS_TICKEY = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=%s';
    const WX_JS_QYTICKEY = 'https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=%s';

    public $MsgType;
    public $FromUserName;
    public $ToUserName;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library(array('curl', 'jsonf'));
        $this->LoadConfig();
    }

    /**
     * 加载CI的配置文件
     */
    private function LoadConfig()
    {
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
            // this not codeigniter
        }
    }

    /**
     * = = = = = = = = = = = = = = = = = = = =
     *  微信消息接口
     * = = = = = = = = = = = = = = = = = = = =
     */

    /**
     * 获取消息（微信 - > 服务器）
     */
    public function getMsg()
    {
        // get post data, May be due to the different environments
        @$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        // extract post data
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            foreach ($postObj as $k => $v) {
                $this->$k = $v;
            }
            return $this->MsgType;
        } else {
            log_message('error', $postStr);
            exit($postStr);
        }
    }

    /**
     * 回复文字消息（服务器 - > 微信）
     * @param $text :消息
     */
    public function reText($text)
    {
        /* 文本信息回复模板 */
        $tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";
        $time = time();
        $resultStr = sprintf($tpl, $this->FromUserName, $this->ToUserName, $time, $text);
        echo $resultStr;
        exit;
    }

    public function reTxPic($item = array())
    {
        /* 图文消息模板 */
        $tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>%d</ArticleCount>
					<Articles>%s</Articles>
					</xml>";
        $itemTpl = "<item>
					<Title><![CDATA[%s]]></Title> 
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
					</item>";
        $time = time();
        $n = 1;
        $itemStr = "";
        if (isset($item['title'])) {
            $itemStr .= sprintf($itemTpl, $item['title'], $item['image'], $item['urladdress']);
        } else {
            $n = count($item);
            foreach ($item as $k => $v) {
                $itemStr .= sprintf($itemTpl, $v['title'], $v['image'], $v['urladdress']);
            }
        }
        $resultStr = sprintf($tpl, $this->FromUserName, $this->ToUserName, $time, $n, $itemStr);
        echo $resultStr;
        exit;
    }

    /**
     * = = = = = = = = = = = = = = = = = = = =
     *  微信平台接口
     * = = = = = = = = = = = = = = = = = = = =
     */

    /**
     * 获取微信Access Token
     */
    public function GetAccessToken()
    {

        $str = $this->CI->jsonf->load('wx_token', false);
        if (!empty($str)) {
            // access token 经过加密保存
            $tokenJson = json_decode($str, true);
            $time = time();
            if ($time - $tokenJson['timestamp'] > $tokenJson['expires_in']) {
                $tokenJson = $this->GetRemoteAccessToken();
            }
        } else {
            $tokenJson = $this->GetRemoteAccessToken();
        }
        $token = $tokenJson['access_token'];
        return $token;
    }

    /**
     * 从服务器端获取微信Access Token
     */
    private function GetRemoteAccessToken()
    {
        $url = sprintf(self::WX_GETTOKEN_URL, $this->appid, $this->secret);
        $json = $this->CI->curl->simple_get($url);
        $json = json_decode($json, true);
        $json['timestamp'] = time();
        $jsonFile = json_encode($json);
        // 将access token加密后保存
        $this->CI->jsonf->save('wx_token', $jsonFile);
        return $json;
    }

    /**
     * 设置微信菜单
     */
    public function SetCustomMenu()
    {
        $token = $this->GetAccessToken();
        $url = sprintf(self::WX_SETMENU_URL, $token);
        $menu = $this->CI->jsonf->read('wx_menu', false);
        $menu = json_decode($menu, true);
        $params = json_encode($menu['menu'], JSON_UNESCAPED_UNICODE); // 一定要去掉 json_encode的中文转码
        $reJson = $this->CI->curl->simple_post($url, $params);
        return $reJson;
    }

    /**
     * 获取服务器端的菜单设置
     * @param $isSave :是否本地保存
     */
    public function GetRemoteMenu($isSave = false)
    {
        $token = $this->GetAccessToken();
        $url = sprintf(self::WX_GETMENU_URL, $token);
        $reJson = $this->CI->curl->simple_get($url);
        if ($isSave) {
            $re = $this->CI->jsonf->write('wx_menu', $reJson);
            if (!$re) {
                log_message("error", "Save weixin public platform menu error");
            }
        }
        return $reJson;
    }

    /**
     * = = = = = = = = = = = = = = = = = = = =
     *  微信浏览器接口
     * = = = = = = = = = = = = = = = = = = = =
     */

    /**
     * 向微信服务器请求交换AccessToken的code
     * @param $redirectUrl : 接收code重定向地址
     * @param $type : 应用授权作用域：
     *        1、snsapi_base：不弹出授权页面，直接跳转，只能获取用户openid；
     *        2、snsapi_userinfo：弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息
     * @param $state : 自定义返回值
     */
    public function AskAccessCode($redirectUrl, $type = "snsapi_base ", $state = "GetCode")
    {
        $url = sprintf(self::WX_WEB_GETCODE_URL, $this->appid, urlencode($redirectUrl), $type, $state);
        redirect($url);
    }

    /**
     * 获取微信网页端用户access token
     * @param $code :请求的交互用code
     * @return mixed
     */
    public function GetUserToken($code)
    {
        $url = sprintf(self::WX_WEB_GETTOKEN_URL, $this->appid, $this->secret, $code);
        $reInfo = $this->CI->curl->simple_get($url);
        if (!empty($reInfo)) {
            $jsonInfo = json_decode($reInfo, true);
            if (!empty($jsonInfo['access_token'])) {
                return $jsonInfo['access_token'];
            } else {
                log_message('error', $reInfo);
            }
        } else {
            log_message('error', 'Get Wechat User Access Token Error');
        }
        return false;
    }

    /**
     * 获取用户的微信信息
     * @param $userToken
     */
    public function GetUserWxInfo($userToken)
    {
        $url = sprintf(self::WX_WEB_GETUSERINFO_URL, $userToken, $this->appid);
        $userInfo = $this->CI->curl->simple_get($url);
        return $userInfo;
    }

    /**
     * = = = = = = = = = = = = = = = = = = = =
     *  微信JS SDK 接入
     * = = = = = = = = = = = = = = = = = = = =
     */

    // 获取签名
    public function GetSignPackage()
    {
        $jsapiTicket = $this->GetJsApiTicket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->CreateNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array("appId" => $this->appid, "nonceStr" => $nonceStr, "timestamp" => $timestamp, "url" => $url, "signature" => $signature, "rawString" => $string);
        return $signPackage;
    }

    public function CreateNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function GetJsApiTicket()
    {
        $data = $this->CI->jsonf->load('wx_ticket');
        $data = (object)$data;
        if ($data->expire_time < time()) {
            $accessToken = $this->GetAccessToken();
            // 如果是企业号用 WX_JS_QYTICKEY 获取 ticket
            $url = sprintf(self::WX_JS_TICKEY, $accessToken);
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $this->CI->jsonf->save("wx_ticket", json_encode($data));
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
    }

    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    /**
     * = = = = = = = = = = = = = = = = = = = =
     *  微信平台接入认证接口
     * = = = = = = = = = = = = = = = = = = = =
     */

    /*
     * 验证微信API合法性
     */
    public function IsValidAPI()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if ($this->CheckSignature()) {
            echo $echoStr;
            exit;
        }
    }

    /**
     * 构造证书
     */
    private function CheckSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

}