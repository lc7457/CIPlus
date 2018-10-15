<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.php';

Class Validator extends \CIPlus\CIClass {
    protected $ip_white_list;
    protected $ip_black_list;
    
    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('validator');
        foreach ($config as $k => $v) {
            $k = strtolower('match_' . $k);
            if (method_exists($this, $k)) {
                $this->$k($v);
            }
        }
    }
    
    /// region <<< IP >>>
    
    /**
     * 验证IP
     * @param string $params [white | black]
     * @return bool
     */
    private function match_ip($params = 'white') {
        $this->CI->load->helper('ip');
        $ip = client_ip();
        $method = strtolower('match_ip_' . $params);
        if (method_exists($this, $method)) {
            $flag = $this->$method($ip);
            if ($flag) return true;
        }
        return $this->illegal();
    }
    
    /**
     * 匹配白名单IP
     * @param $ip
     * @return bool
     */
    private function match_ip_white($ip) {
        foreach ($this->ip_white_list as $k => $v) {
            $flag = (bool)preg_match($v, $ip);
            if ($flag) return true;
        }
        return false;
    }
    
    /**
     * 匹配黑名单IP
     * @param $ip
     * @return bool
     */
    private function match_ip_black($ip) {
        foreach ($this->ip_black_list as $k => $v) {
            $flag = (bool)preg_match($v, $ip);
            if ($flag) return false;
        }
        return true;
    }
    
    /// endregion
    
    /**
     * 非法的访问
     */
    private function illegal() {
        show_error('Illegal Access', 401, 40100);
        return false;
    }
}