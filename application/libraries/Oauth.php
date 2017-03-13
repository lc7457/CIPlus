<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/Number.php';

/**
 * Class Oauth
 */
class Oauth {
    const CODE_EXPIRES_TIME = 300;

    const APPID_COLUMN = 'appid';
    const SECRET_COLUMN = 'secret';
    const CODE_COLUMN = 'code';
    const EXPIRES_COLUMN = 'expires_in';

    private $appid;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('oauth_base_model');
        $this->number = new CIPlus\Number();
    }

    /**
     * 验证 Access Token 有效性
     * @param $accessToken
     * @return bool
     */
    public function ValidToken($accessToken) {
        return false;
    }

    /**
     * 生成 Oauth Code
     * @param $appid
     * @return string
     */
    public function CreateCode($appid) {
        $code = '';
        if ($this->ValidApp($appid)) {
            $this->appid = $appid;
            $n = mt_rand(1000000000, 9999999999);
            $code = $this->number->Encode($n);
            $whereArr = array(self::APPID_COLUMN => $appid);
            $dataArr = array(self::CODE_COLUMN => $code, self::EXPIRES_COLUMN => time() + self::CODE_EXPIRES_TIME);
            $this->CI->oauth_base_model->update($dataArr, $whereArr);
        }
        return $code;
    }

    /**
     * 生成 Access Token
     * @param $code
     * @param $secret
     */
    public function CreateToken($code, $secret) {
        if ($this->ValidAccess($code, $secret)) {
            echo $this->appid;
        }
    }

    /**
     * 验证APPID是否有效
     * @param $appid
     * @return bool
     */
    private function ValidApp($appid) {
        $whereArr = array('appid' => $appid);
        return $this->CI->oauth_base_model->count($whereArr) > 0;
    }

    /**
     * 验证请求是否有效
     * @param $code
     * @param $secret
     * @return bool
     */
    private function ValidAccess($code, $secret) {
        $whereArr = array(self::CODE_COLUMN => $code, self::SECRET_COLUMN => $secret);
        if ($this->CI->oauth_base_model->count($whereArr) > 0) {
            $oauth = $this->CI->oauth_base_model->row($whereArr);
            if ($oauth[self::EXPIRES_COLUMN] > time()) {
                $this->appid = $oauth[self::APPID_COLUMN];
                return true;
            }
        }
        return false;
    }
}