<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.abstract.php';

Class Validator_ip extends \CIPlus\CIClass {
    protected $ip_white_list;
    protected $ip_black_list;

    private $ip;

    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('validator');
        $this->CI->load->helper('ip');
        $this->ip = client_ip();
    }

    /**
     * 匹配白名单IP
     * @return bool
     */
    public function white() {
        foreach ($this->ip_white_list as $k => $v) {
            $flag = (bool)preg_match($v, $this->ip);
            if ($flag) return true;
        }
        return false;
    }

    /**
     * 匹配黑名单IP
     * @return bool
     */
    public function black() {
        foreach ($this->ip_black_list as $k => $v) {
            $flag = (bool)preg_match($v, $this->ip);
            if ($flag) return false;
        }
        return true;
    }
}