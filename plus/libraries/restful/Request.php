<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.abstract.php';

Class Request extends \CIPlus\CIClass {

    // 接口参数
    private $required = array(); // 必填参数
    private $optional = array(); // 选填参数
    private $params = array(); // 参数集合

    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('restful');
    }

    /**
     * 获取接口参数
     * @param array $required
     * @param array $optional
     * @param string $method
     * @return array
     */
    public function request($required = array(), $optional = array(), $method = 'request') {
        $method = '_' . $method;
        $params = $this->$method();
        foreach ($required as $key) {
            if (array_key_exists($key, $params)) {
                $this->required[$key] = $params[$key];
            } else {
                $this->respond(40001);
            }
        }
        foreach ($optional as $key) {
            $this->optional[$key] = $params[$key];
        }
        $this->params = array_merge($this->required, $this->optional);
        return $this->params;
    }

    /**
     * 返回请求参数
     * @param $key
     * @return array|mixed
     */
    public function requestParams($key = '') {
        if (empty($key)) {
            return $this->params;
        } elseif (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        } else {
            return null;
        }
    }

    /**
     * 更新接口参数数据
     * @param $key
     * @param $value
     * @return mixed
     */
    public function updateParam($key, $value) {
        $this->params[$key] = $value;
        return $value;
    }


    // get method
    protected function _get($key = null) {
        return $this->CI->input->get($key);
    }

    // post method
    protected function _post($key = null) {
        return $this->CI->input->post($key);
    }

    // post or get method
    protected function _request($key = null) {
        $data = NULL;
        if (empty($key)) {
            $get = $this->CI->input->get($key);
            $post = $this->CI->input->post($key);
            $data = array_merge($get, $post);
        } else {
            $data = $this->CI->input->post_get($key);
        }
        return $data;
    }

}