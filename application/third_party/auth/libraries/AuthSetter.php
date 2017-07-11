<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class AuthSetter
 * 安全认证启动器
 */
require_once FCPATH . 'plus/CIClass.php';

class AuthSetter extends \CIPlus\CIClass {
    const EXPIRE_TIME = 3600;

    public function __construct() {
        parent::__construct();
        $this->CI->load->model("auth_token_model");
        $this->CI->load->library('AuthToken');
    }

    /**
     * =============================================
     * 1、构造验证数据
     * =============================================
     * @param $uid
     * @param $header
     * @param $code
     * @param $data
     * @return string
     */
    public function Setter($code, $uid, $header, $data = array()) {
        $payload = $this->CI->authtoken->Payload($data); // 加密载荷
        $key = $this->CreateKey();
        $sign = $this->CI->authtoken->Sign($header, $payload, $key);
        $expireIn = time() + self::EXPIRE_TIME; // 限定时间
        $expireIn = empty($this->CI->authtoken->exp) ? $expireIn : $this->CI->authtoken->exp;
        $whereArr = array(
            'code' => $code,
        );
        $dataArr = array(
            'uid' => $uid,
            'payload' => $payload,
            'sign' => $sign,
            'key' => $key,
            'ip' => $this->CI->authtoken->IP(),
            'user_agent' => $this->CI->authtoken->UserAgent(),
            'expires_in' => $expireIn
        );
        if ($this->CI->auth_token_model->count($whereArr) > 0) {
            $this->CI->auth_token_model->update($dataArr, $whereArr);
        }
        return implode('.', array($header, $payload, $sign));
    }

    // 构造密盐
    private function CreateKey() {
        return md5(uniqid(mt_rand(), true));
    }

    // 验证Token header
    public function ValidHeader($header, $code) {
        $this->CI->authtoken->AnalyseHeader($header);
        return $this->CI->authtoken->code === $code;
    }

    // 加密签名  *给外部调用
    public function Sign($header = '', $payload = '', $salt = "CIPlus") {
        return $this->CI->authtoken->Sign($header, $payload, $salt);
    }
}