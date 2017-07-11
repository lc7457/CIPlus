<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class AuthVerifier
 * 安全认证核验器
 */
require_once FCPATH . 'plus/CIClass.php';

class AuthVerifier extends \CIPlus\CIClass {

    public $err = 0; // 错误简码
    public $data = array( // 返回给接口服务器的待加密数据 => $cipher
        'version' => '1.0'
    );

    private $security = array(); // 通过token从数据库中读取到的安全信息
    private $authSecret = NULL; // 接口服务器的密钥

    private $api = array();

    protected $strict = false;

    public function __construct() {
        parent::__construct();
        $this->LoadConf('verifier');
        $this->CI->load->model("auth_token_model");
        $this->CI->load->library('AuthToken');
    }

    /**
     * 核验器
     * @param $params : 被核验数据
     */
    public function Verifier($params) {
        /* 验证请求是否合法 */
        $this->VerifyToken($params['token']);
        $this->VerifyCode();
        /* 验证请求参数有效性 */
        if ($this->VerifiableAPI($params['class'], $params['method'])) {
            $this->VerifySign();
            $this->VerifyTime();
            $this->VerifyUserAgent($params['user_agent']);
            $this->VerifyPower();
        }
        $this->VerifyAuth();
        $this->Respond();
    }

    // 1.验证token
    private function VerifyToken($token) {
        // 验证数据格式
        if (!$this->CI->authtoken->AnalyseToken($token, false)) {
            $this->err = 1;
            $this->Respond();
        }
    }

    // 2.验证code
    private function VerifyCode() {
        $this->security = $this->CI->auth_token_model->row(
            array('code' => $this->CI->authtoken->code)
        );
        if (empty($this->security)) {
            $this->err = 2;
            $this->Respond();
        }
    }

    // 3.验证签名
    private function VerifySign() {
        $sign = $this->CI->authtoken->Sign($this->CI->authtoken->header, $this->security['payload'], $this->security['key']);
        if ($sign !== $this->CI->authtoken->sign) {
            $this->err = 3;
            $this->Respond();
        }
    }

    // 4.验证有效时间
    private function VerifyTime() {
        if (time() < $this->security['createtime'] || time() > $this->security['expires_in']) {
            $this->err = 4;
            $this->Respond();
        }
    }

    // 5.验证UserAgent
    private function VerifyUserAgent($clientUA) {
        if (trim($this->security['user_agent']) !== trim($clientUA)) {
            $this->err = 5;
            $this->Respond();
        }
    }

    // 6.验证授权子服务器
    private function VerifyAuth() {
        $this->CI->load->library('user_agent');
        if ($this->CI->agent->is_robot() && $this->CI->agent->platform === 'Linux') {
            $this->CI->load->model('auth_client_model');
            $agent = explode(' ', $this->CI->agent->agent_string());
            $auth = $this->CI->auth_client_model->row(
                array(
                    'auth' => $agent[0],
                    'usable' => 1
                )
            );
            if (!empty($agent[0]) && !empty($auth)) {
                $this->authSecret = $auth['secret'];
                return;
            }
        }
        $this->err = 6;
        $this->Respond();
    }

    // 7.验证当前用户是否有权限
    private function VerifyPower() {
        $uid = $this->security['uid'];
        if (!empty($uid)) {
//            $this->CI->load->model('user_group');
//            $whereArr = array('uid' => $uid);
        }
    }

    // 9.判断当前接口是否需要验证
    private function VerifiableAPI($class, $method) {
        $whereArr = array(
            'class' => $class,
            'method' => $method
        );
        $this->CI->load->model('auth_api_model');
        $api = $this->CI->auth_api_model->row($whereArr);
        if ((empty($api) && $this->strict) || $api['usable'] == 0) {
            $this->err = 9;
            $this->Respond();
        } else {
            $this->api = $api;
        }
        return empty($api) ? 0 : $api['verifiable'];
    }

    /**
     * 返回核验结果
     * @param $err
     */
    private function Respond($err = null) {
        if (!empty($err)) {
            $this->err = $err;
        }
        $respond = array(
            "err" => $this->err,
            "cipher" => $this->Cipher()
        );
        $this->CI->output->set_content_type('application/json');
        $this->CI->output->set_output(json_encode($respond));
        $this->CI->output->_display();
        exit;
    }

    /**
     * 带有安全信息的密文
     */
    public function Cipher() {
        $this->CI->load->library('encrypt');
        $data = json_encode($this->data);
        $data = $this->CI->encrypt->encode($data, $this->authSecret);
        return url64_encode($data);
    }

}