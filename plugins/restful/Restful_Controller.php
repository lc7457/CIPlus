<?php defined('BASEPATH') or exit ('No direct script access allowed');

abstract class Restful_Controller extends CI_Controller {
    
    public function __construct(array $security = array()) {
        parent::__construct();
        $this->load->add_package_path(PLUGINS_PATH . 'restful');
        $this->load->library('API');
        $this->load->library('Validator', $security);
    }
    
    /**
     * Set Respond Code
     * @param $code
     * @param bool $sync
     * @return API
     */
    public function setCode($code, $sync = true) {
        $this->api->setCode($code, $sync);
        return $this->api;
    }
    
    /**
     * Set Respond Message
     * @param string $message
     * @return API
     */
    public function setMessage($message) {
        $this->api->setMessage($message);
        return $this->api;
    }
    
    /**
     * Set Respond Data
     * @param array $data
     * @return API
     */
    public function setData(array $data = array()) {
        $this->api->setData($data);
        return $this->api;
    }
    
    // 响应事件，最后执行
    public function respond() {
        $args = func_get_args();
        $this->api->respond(...$args);
    }
    
    // 使用属性绑定接口数据
    protected function request($required = array(), $optional = array(), $method = 'request') {
        $params = $this->api->request($required, $optional, $method);
        foreach ($params as $k => $v) {
            $this->verifyParamsHandle($k, $v);
        }
        return $this->api;
    }
    
    /**
     * 返回合法参数
     * @param array $arr 交集过滤
     * @return array
     */
    public function requestData(array $arr = array()) {
        $r = $this->api->requestParams();
        if (!empty($arr)) {
            return array_intersect_key($r, array_flip($arr));
        } else {
            return $r;
        }
    }
    
    /**
     * 补集过滤
     * @param array $arr 不需要的数据项
     * @return array
     */
    public function requestFilter(array $arr = array()) {
        return array_diff_key($this->api->requestParams(), array_flip($arr));
    }
    
    // 返回参数值
    public function requestParam($key = '') {
        return $this->api->requestParams($key);
    }
    
    // 构造参数验证方法，若存在该特殊方法则进行调用并验证参数
    protected function verifyParamsHandle($key, &$value) {
        $verifyMethod = 'verify_' . $key;
        if (method_exists($this, $verifyMethod)) {
            $this->$verifyMethod($value);
        }
    }
}