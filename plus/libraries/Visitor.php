<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once CIPLUS_PATH . 'CIClass.php';

/**
 * 访客类
 * 检测访问用户的基本信息
 * Class Visitor
 */
class Visitor extends \CIPlus\CIClass {
    
    public $env; // 访问环境
    public $ua; // user agent
    
    public function __construct() {
        parent::__construct();
        $this->UserAgent();
        $this->Environment();
        $this->UniqueCode();
    }
    
    // 获取user agent
    public function UserAgent() {
        $this->CI->load->library('user_agent');
        $this->ua = $this->CI->agent->agent_string();
        return $this->ua;
    }
    
    // 访问环境检测
    public function Environment() {
        if (strpos($this->ua, 'MicroMessenger') !== false) {
            $this->env = "wechat";
            return $this->env;
        }
        return "browser";
    }
    
    // 生成唯一码
    public function UniqueCode() {
        $code = empty($_SESSION['code']) ? null : $_SESSION['code'];
        if (empty($code)) {
            $code = md5($this->IP() . time() . rand(1000, 9999));
        }
        $this->CI->session->set_userdata(
            array('code' => $code)
        );
        return $code;
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