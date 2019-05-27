<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.abstract.php';

Class API extends \CIPlus\CIClass {

    const KEY_CODE = 'code';
    const KEY_MESSAGE = 'message';
    const KEY_DATA = 'data';

    // 详见config/api.php
    protected $strict = true; // 是否打开严格模式，打开后除了接口信息其他输出无效
    protected $cors = false; // 是否开启CORS跨域访问，必须开打严格模式才可以启动
    protected $param_format; // 数据格式参数
    protected $respondFormat = 'json'; // 默认数据格式
    protected $supportedFormats = array(); // 可被支持的数据格式

    // API默认返回数据
    private $code = 40000;
    private $message = 'Access API Failed';
    private $data = array();

    // 接口参数
    private $required = array(); // 必填参数
    private $optional = array(); // 选填参数
    private $params = array(); // 参数集合

    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('api');
        $this->CI->load->library('format');
        $this->CI->lang->load('respond');
        $this->message = lang('m40000');
        ob_start();
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
     * 抛出接口回调数据
     * param int $code 接口参数
     * param string $message 接口参数
     * param array $data 接口参数
     */
    public function respond() {
        $this->_format();
        // 清理输出缓冲区
        if ($this->strict) {
            ob_end_clean();
            // 构造允许跨域 header
            if ($this->cors) {
                header("Access-Control-Allow-Origin: *");
                header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
            }
        }
        // 根据参数类型解析参数
        $args = func_get_args();
        foreach ($args as $arg) {
            if (is_numeric($arg)) {
                $this->setCode($arg);
            } elseif (is_string($arg)) {
                $this->setMessage($arg);
            } elseif (is_array($arg)) {
                $this->setData($arg);
            }
        }
        // 构造回调数据
        $arr = array(
            self::KEY_CODE => $this->code,
            self::KEY_MESSAGE => $this->message,
            self::KEY_DATA => $this->data
        );
        $this->CI->output->set_content_type($this->supportedFormats[$this->respondFormat]);
        $toFormat = 'to_' . $this->respondFormat;
        $this->CI->output->set_output($this->CI->format->factory($arr)->$toFormat());
        $this->CI->output->_display();
        exit;
    }

    /**
     * Set Respond Code
     * @param int $code 代码
     * @param bool $sync 是否同步message
     * @return $this
     */
    public function setCode($code, $sync = true) {
        $this->code = $code;
        if ($sync) {
            $this->message = $this->CI->lang->line('m' . $code, FALSE);
        }
        return $this;
    }

    /**
     * Set Respond Message
     * @param string $message
     * @return $this
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    /**
     * Set Respond Data
     * @param array $data
     * @return $this
     */
    public function setData(array $data = array()) {
        $this->data = $data;
        return $this;
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

    // 修改API数据格式
    private function _format() {
        $f = strtolower($this->_request($this->param_format));
        if (!empty($f) && array_key_exists($f, $this->supportedFormats)) {
            $this->respondFormat = $f;
        }
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