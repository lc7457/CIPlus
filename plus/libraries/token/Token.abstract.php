<?php defined('BASEPATH') or exit ('No direct script access allowed');

abstract class Token extends \CIPlus\CIClass {

    protected $expire_time;
    protected $encryption;

    protected $header_required;
    protected $payload_required;

    protected $header;
    protected $payload;

    protected $typ; // Token 序列类型
    protected $rap; // Token 加密方法

    protected $id; // 系统身份标识
    protected $iat; // 开始时间
    protected $exp; // 过期时间

    public function __construct() {
        parent::__construct();
        $this->loadConf('token');
    }

    /**
     * 解析header
     * @param $header
     * @return bool
     */
    protected function parseHeader($header) {
        $hr = json_decode(url64_decode($header), true);
        foreach ($this->header_required as $item) {
            if (key_exists($item, $hr)) {
                if (property_exists($this, $item)) {
                    $this->$item = $hr[$item];
                }
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * 加密
     * @param $cipher_text
     * @param array $config
     * @return mixed|string
     */
    protected function encrypt($cipher_text, array $config) {
        $this->CI->load->library('encryption');
        $sign = $this->CI->encryption->encrypt($cipher_text, $config);
        return url64_encode($sign);
    }

    /**
     * 解密
     * @param $sign
     * @param array $config
     * @return string
     */
    protected function decrypt($sign, array $config) {
        $this->CI->load->library('encryption');
        $res = $this->CI->encryption->decrypt(url64_decode($sign), $config);
        return $res;
    }
}