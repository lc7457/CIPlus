<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/Number.php';

/**
 * Class Oauth
 */
class Oauth {
    const CODE_EXPIRES_TIME = 300;
    const TOKEN_EXPIRES_TIME = 3600;

    const APPID_COLUMN = 'appid';
    const SECRET_COLUMN = 'secret';
    const CODE_COLUMN = 'code';
    const TOKEN_COLUMN = 'access_token';
    const CODE_EXPIRES_COLUMN = 'code_expires_in';
    const TOKEN_EXPIRES_COLUMN = 'token_expires_in';

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
        $oauth = $this->CI->oauth_base_model->row(array('access_token' => $accessToken));
        return $oauth['token_expires_in'] > time();
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
            $dataArr = array(self::CODE_COLUMN => $code, self::CODE_EXPIRES_COLUMN => time() + self::CODE_EXPIRES_TIME);
            $this->CI->oauth_base_model->update($dataArr, $whereArr);
        }
        return $code;
    }

    /**
     * 生成 Access Token
     * @param $code
     * @param $secret
     * @return mixed
     */
    public function CreateToken($code, $secret) {
        if ($this->ValidAccess($code, $secret)) {
            $token = md5($code . self::TOKEN_EXPIRES_TIME . $secret . time());
            $this->CI->oauth_base_model->update(array(
                self::APPID_COLUMN => $this->appid,
                self::TOKEN_COLUMN => $token,
                self::CODE_COLUMN => '',
                self::TOKEN_EXPIRES_COLUMN => time() + self::TOKEN_EXPIRES_TIME
            ), array(self::APPID_COLUMN => $this->appid));
            return $token;
        }
        return 'invalid';
    }

    /**
     * 验证APPID是否有效
     * @param $appid
     * @return bool
     */
    private function ValidApp($appid) {
        $whereArr = array(self::APPID_COLUMN => $appid);
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
            if ($oauth[self::CODE_EXPIRES_COLUMN] > time()) {
                $this->appid = $oauth[self::APPID_COLUMN];
                return true;
            }
        }
        return false;
    }
}