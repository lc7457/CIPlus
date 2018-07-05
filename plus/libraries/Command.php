<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once CIPLUS_PATH . 'CIClass.php';

/**
 * 全局命令
 * Class Command
 * method Get($key)
 * // FOR IDE
 * @method Set($key, $value)    => SetIn.$this->driver
 * @method Load($key)           => LoadFrom.$this->driver
 */
class Command extends \CIPlus\CIClass {
    protected $driver;
    protected $prefix;
    protected $command_white_list;
    
    private $params = array();
    
    public function __construct() {
        parent::__construct();
        $this->loadConf('command');
        $this->loadDriver();
        $this->analyseCommand();
    }
    
    /**
     * 直接从当前获取全局参数
     * @param $key
     * @return string
     */
    public function get($key) {
        $key = $key = $this->ParamsKey($key);
        if (key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return '';
    }
    
    /**
     * 调用入口拦截
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params) {
        if ($method === "set") {
            $method = $method . 'In' . ucfirst($this->driver);
            if (method_exists($this, $method)) {
                return $this->$method($params[0], $params[1]);
            }
            
        } elseif ($method === "load") {
            $method = $method . 'From' . ucfirst($this->driver);
            if (method_exists($this, $method)) {
                return $this->$method($params[0]);
            }
        }
        return null;
    }
    
    /**
     * 加载全局参数会话引擎
     */
    private function loadDriver() {
        if ($this->driver === 'session') {
            $this->CI->load->library('session');
        } elseif ($this->driver === 'cookie') {
//            $this->CI->load->helper('cookie');
        } else {
            show_error('Found Error in Class : Command Driver');
        }
    }
    
    /**
     * 解析全局参数
     */
    private function analyseCommand() {
        $params = $this->CI->input->get();
        foreach ($this->command_white_list as $key) {
            $key = $this->paramsKey($key);
            if (key_exists($key, $params)) {
//                $this->params = array_merge($this->params, array($key => $params[$key]));
                $this->params[$key] = $params[$key];
            }
        }
        $this->saveInDriver();
    }
    
    /**
     * 保存至会话引擎
     */
    private function saveInDriver() {
        $method = 'saveIn' . ucfirst($this->driver);
        $this->$method();
    }
    
    /**
     * 从session中读取
     * @param $key
     * @return mixed
     */
    private function loadFromSession($key) {
        $key = $this->paramsKey($key);
        return $this->CI->session->$key;
    }
    
    /**
     * 从cookie中读取
     * @param $key
     * @return mixed
     */
    private function loadFromCookie($key) {
        $key = $this->paramsKey($key);
        return $this->CI->input->cookie($key);
    }
    
    /**
     * 将参数写入session
     */
    private function saveInSession() {
        $this->CI->session->set_userdata($this->params);
    }
    
    /**
     * 将参数写入cookie
     */
    private function saveInCookie() {
        foreach ($this->params as $key => $value) {
            $this->CI->input->set_cookie($key, $value, 7200);
        }
    }
    
    /**
     * 设置Session中的参数
     * @param $key
     * @param $value
     */
    private function setInSession($key, $value) {
        $key = $this->ParamsKey($key);
        $this->CI->session->set_userdata($key, $value);
    }
    
    /**
     * 设置cookie中的参数
     * @param $key
     * @param $value
     */
    private function setInCookie($key, $value) {
        $key = $this->ParamsKey($key);
        $this->CI->input->set_cookie($key, $value, 7200);
    }
    
    /**
     * 构造全局参数key
     * @param $key
     * @return string
     */
    private function paramsKey($key) {
        return $this->prefix . $key;
    }
    
}