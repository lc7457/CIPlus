<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.php';

Class Validator extends \CIPlus\CIClass {
    protected $ip_white_list;
    
    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('validator');
        foreach ($config as $k => $v) {
            $k = strtolower($k);
            if (method_exists($this, $k)) {
                $this->$k($v);
            }
        }
    }
    
    /**
     * 验证IP
     * @param null $params
     * @return bool
     */
    private function ip($params = null) {
        $this->CI->load->helper('ip');
        $ip = client_ip();
        foreach ($this->ip_white_list as $k => $v) {
            $flat = (bool)preg_match($v, $ip);
            if ($flat) return true;
        }
        show_404();
        return false;
    }
    
}