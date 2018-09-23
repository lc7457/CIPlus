<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 腾讯图像验证码服务（免费）
 * Class QQCaptcha
 */
class QQCaptcha extends \CIPlus\CIClass {
    const URL = 'https://ssl.captcha.qq.com/ticket/verify';

    protected $aid = '';
    protected $secret = '';

    public function __construct(array $config = array()) {
        parent::__construct();
        $this->CI->load->library('visitor');
        $this->loadConf('qq_captcha');
    }

    public function verify($ticket, $randStr) {
        if (ENVIRONMENT === "development") {
            return true;
        }
        $params = array(
            'aid' => $this->aid,
            'AppSecretKey' => $this->secret,
            'Ticket' => $ticket,
            'Randstr' => $randStr,
            'UserIP' => $this->CI->visitor->ip()
        );
        $re = $this->CI->curl->simple_get(self::URL, $params);
        $re = json_decode($re, true);
        return $re['response'] === "1";
    }
}