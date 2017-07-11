<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
require_once FCPATH . 'plus/CIClass.php';

class AuthToken extends \CIPlus\CIClass {

    const HEADER_TOKEN_KEY = 'HTTP_TOKEN'; //authorization
    const HEADER_ILLEGAL_ACCESS = 'illegal access';

    // compatible mode
    protected $compatible = false; // 是否启动兼容模式
    const COMPATIBLE_TOKEN_KEY = '_token';

    public $token; //全值token序列
    public $header; // token中的header(消息头)
    public $payload; // token中的payload(载荷)
    public $sign; // token中的sign(签名)
    // header key
    public $appid; // 请求应用ID
    public $code; // 访问唯一码
    public $iat; // 开始时间
    public $exp; // 结束时间

    public function __construct() {
        parent::__construct();
        $this->LoadConf('token');
    }

    // 解析 token
    public function AnalyseToken($token = "", $getToken = true) {
        if (empty($token) && $getToken) {
            $token = $this->GetToken();
        }
        if (!empty($token)) {
            $token = explode('.', $token);
        }
        $n = count($token);
        if ($n > 0) {
            $this->header = $token[0];
            $this->AnalyseHeader();
        }
        if ($n === 3) {
            $this->payload = $token[1];
            $this->sign = $token[2];
        }
        return $n === 1 || $n === 3;
    }

    // 解析 token Header
    public function AnalyseHeader($header = null) {
        $header = empty($header) ? url64_decode($this->header) : url64_decode($header);
        $header = json_decode($header, true);
        if (is_array($header)) {
            foreach ($header as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    // 获取access token
    public function GetToken() {
        if (array_key_exists(self::HEADER_TOKEN_KEY, $_SERVER)) {
            $token = $_SERVER[self::HEADER_TOKEN_KEY];
        }
        if (empty($token) && $this->compatible) {
            $token = $this->CI->input->post_get(self::COMPATIBLE_TOKEN_KEY);
        }
        if (empty($token)) {
            show_error('Token was not found', 401, self::HEADER_ILLEGAL_ACCESS);
        } else {
            $this->token = $token;
        }
        return $token;
    }

    // 构造载荷
    public function Payload($data) {
        $data = json_encode($data);
        return url64_encode($data);
    }

    // 构造签名
    public function Sign($header = '', $payload = '', $salt = "CIPlus") {
        $arr = array(
            'header' => empty($header) ? $this->header : $header,
            'payload' => empty($payload) ? $this->payload : $payload,
        );
        return hash_hmac('sha256', implode('.', $arr), $salt);
    }

    // 获取user agent
    public function UserAgent() {
        $this->CI->load->library('user_agent');
        return $this->CI->agent->agent_string();
    }

    // 获取用户 ip
    public function IP() {
        $this->CI->load->helper('IP');
        return client_ip();
    }

    // 获取来访域名
    public function Domain() {
        return $_SERVER['HTTP_HOST'];
    }

    // 获取设备信息
    public function Device() {
        return $this->CI->input->post_get('device');
    }
}