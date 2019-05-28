<?php defined('BASEPATH') or exit ('No direct script access allowed');

abstract class Restful_Controller extends CI_Controller {

    public function __construct(array $security = array()) {
        parent::__construct();
        $this->load->add_package_path(PLUGINS_PATH . 'restful');
        $this->load->library('Request');
        $this->validator($security);
    }

    /**
     * Set Respond Code
     * @param $code
     * @param bool $sync
     * @return Request
     */
    public function setCode($code, $sync = true) {
        $this->api->setCode($code, $sync);
        return $this->api;
    }

    /**
     * Set Respond Message
     * @param string $message
     * @return Request
     */
    public function setMessage($message) {
        $this->api->setMessage($message);
        return $this->api;
    }

    /**
     * Set Respond Data
     * @param array $data
     * @return Request
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
     * @return array
     */
    public function requestData() {
        return array_intersect_key($this->api->requestParams(), array_flip(func_get_args()));
    }

    /**
     * 补集过滤
     * @return array
     */
    public function requestFilter() {
        return array_diff_key($this->api->requestParams(), array_flip(func_get_args()));
    }

    /**
     * 返回参数值
     * @param string $key
     * @return array|mixed
     */
    public function requestParam($key = '') {
        return $this->api->requestParams($key);
    }

    // 构造参数验证方法，若存在该特殊方法则进行调用并验证参数
    protected function verifyParamsHandle($key, &$value) {
        $verifyMethod = 'verify_' . strtolower($key);
        if (method_exists($this, $verifyMethod)) {
            $this->api->updateParam($key, $this->$verifyMethod($value));
        }
    }

    /**
     * 构造安全验证器
     * @param $security
     */
    private function validator($security) {
        if (!empty($security) && is_array($security)) {
            foreach ($security as $k => $v) {
                $lib = strtolower('validator_' . $k);
                $this->load->library($lib);
                $lib = $this->load->is_loaded($lib);
                if ($lib && method_exists($this->$lib, $v)) {
                    if (!$this->$lib->$v()) $this->respond(40100);
                } else {
                    log_message('error', "unload ({$lib}) or undefined method ({$v})");
                    $this->respond(50500);
                }
            }
        }
    }

}