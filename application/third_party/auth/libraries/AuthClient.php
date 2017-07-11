<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class AuthRequester
 * 安全认证访问器
 */
require_once FCPATH . 'plus/CIClass.php';

class AuthClient extends \CIPlus\CIClass {
    protected $server = '';
    protected $entry = '';

    protected $auth = '';
    protected $secret = '';
    protected $platform = 'Linux';
    protected $agent = 'CIPlus';

    public $request;
    public $err;
    public $securityData = array();

    public function __construct() {
        parent::__construct();
        $this->LoadConf('client');
    }

    // 访问统一接口验证服务端
    public function Connect() {
        $this->CI->load->library('curl');
        $url = $this->server . $this->entry;
        $this->CI->curl->option(CURLOPT_USERAGENT, sprintf('%s (%s) %s', $this->auth, $this->platform, $this->agent));
        $this->CI->curl->ssl(false);
        $this->request = $this->CI->curl->simple_post($url, $this->Params());
        $this->request = json_decode($this->request, true);

        return is_array($this->request);
    }

    // 构造参数
    private function Params() {
        $this->CI->load->library("AuthToken");
        $this->CI->authtoken->AnalyseToken();
        $params = array(
            'token' => $this->CI->authtoken->token,
            'class' => $this->CI->router->class,
            'method' => $this->CI->router->method,
            'user_agent' => $this->CI->authtoken->UserAgent(),
            'ip' => $this->CI->authtoken->IP(),
            'device' => $this->CI->authtoken->Device()
        );
        return $params;
    }


    // 验证数据回调
    public function Validate() {
        $this->err = $this->request['err'];
        if ($this->request['err'] == 0) {
            $this->securityData = $this->DecodeCipher($this->request['cipher']);
        }
        return $this;
    }

    // 解读加密信息
    private function DecodeCipher($cipher) {
        if (!empty($cipher)) {
            $this->CI->load->library('encrypt');
            $cipher = url64_decode($cipher);
            $cipher = $this->CI->encrypt->decode($cipher, $this->secret);
        }
        return $cipher;
    }

}