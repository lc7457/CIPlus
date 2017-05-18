<?php defined('BASEPATH') or exit ('No direct script access allowed');

/**
 * API 控制器
 */
abstract class API_Controller extends CI_Controller {
    const KEY_CODE = 'code';
    const KEY_MESSAGE = 'message';
    const KEY_DATA = 'data';

    // 配置全局参数变量名称：接收API返回数据格式
    const PARAMS_FORMAT = '_format';

    // 详见config/api.php
    private $strict = true; // 是否打开严格模式，打开后除了接口信息其他输出无效
    private $respondFormat = 'json'; // 默认数据格式
    private $supportedFormats = array(); // 可被支持的数据格式

    // API默认返回数据
    private $code = 40000;
    private $message = 'Access API Failed';
    private $data = array();

    protected $required = array(); // 必填参数
    protected $optional = array(); // 选填参数

    public function __construct() {
        parent::__construct();
        $this->lang->load('api_message');
        $this->LoadConf();
        $this->AnalysisParameters();
        $this->load->library('format');
        ob_start();
    }

    // 从CI中加载配置文件
    private function LoadConf() {
        $CI = &get_instance();
        $CI->config->load('api', true, true);
        $config = $CI->config->item('api');
        if (is_array($config)) {
            foreach ($config as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    // 初始化自定义配置,实例化时传入配置数组，可以覆盖CI config
    protected function SetConf(array $config = array()) {
        foreach ($config as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    // Get URL Parameters , 可以覆盖类中配置文件
    private function AnalysisParameters() {
        $this->_format();
    }

    // 配置全局参数变量：API返回数据格式 _get('_format');
    private function _format() {
        $f = strtolower($this->_get(self::PARAMS_FORMAT));
        if (!empty($f) && array_key_exists($f, $this->supportedFormats)) {
            $this->respondFormat = $f;
        }
    }

    // API Code
    protected function SetCode($code, $inDict = true) {
        $this->code = $code;
        if ($inDict) {
            $this->message = $this->lang->line('m' . $code, FALSE);
        }
        return $this;
    }

    // API Message
    protected function SetMessage($message) {
        $this->message = $message;
        return $this;
    }

    // API Data
    protected function SetData(array $data = array()) {
        $this->data = $data;
        return $this;
    }

    // 响应事件，最后执行
    public function Respond() {
        if ($this->strict) {
            ob_end_clean();
            header("Access-Control-Allow-Origin: *");
            header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
        }
        $args = func_get_args();
        foreach ($args as $arg) {
            if (is_numeric($arg)) {
                $this->SetCode($arg);
            } elseif (is_string($arg)) {
                $this->SetMessage($arg);
            } elseif (is_array($arg)) {
                $this->SetData($arg);
            }
        }
        $arr = array(
            self::KEY_CODE => $this->code,
            self::KEY_MESSAGE => $this->message,
            self::KEY_DATA => $this->data
        );
        $this->output->set_content_type($this->supportedFormats[$this->respondFormat]);
        $toFormat = 'to_' . $this->respondFormat;
        $this->output->set_output($this->format->factory($arr)->$toFormat());
        $this->output->_display();
        exit;
    }

    // 使用属性绑定接口数据
    protected function Request($required = array(), $optional = array(), $method = 'request') {
        $method = '_' . $method;
        $params = $this->$method();
        foreach ($required as $key) {
            if (empty($params[$key])) {
                $this->Respond(40001);
            } else {
                $this->VerifyParamsHandle($key, $params[$key]);
                $this->required[$key] = $params[$key];
            }
        }
        foreach ($optional as $key) {
            if (array_key_exists($key, $params)) {
                $this->VerifyParamsHandle($key, $params[$key]);
                $this->optional[$key] = $params[$key];
            }
        }
        return $this;
    }

    // 构造参数验证方法，若存在该特殊方法则进行调用并验证参数
    protected function VerifyParamsHandle($key, &$value) {
        $verifyMethod = 'Verify_' . $key;
        if (method_exists($this, $verifyMethod)) {
            $this->$verifyMethod($value);
        }
    }

    /**
     * 返回所有合法参数
     * @param array $arr 过滤项
     * @return array
     */
    protected function RequestData(array $arr = array()) {
        $r = array_merge($this->required, $this->optional);
        if (!empty($arr)) {
            return array_intersect_key($r, array_flip($arr));
        } else {
            return $r;
        }
    }

    /**
     * 过滤数据
     * @param $arr
     * @param bool $is true：交集；false：差集
     * @return array
     */
    protected function FilterData($arr, $is = true) {
        if ($is) {
            return array_intersect_key($this->RequestData(), array_flip($arr));
        } else {
            return array_diff_key($this->RequestData(), array_flip($arr));
        }
    }

    // get
    protected function _get($key = null) {
        return $this->input->get($key);
    }

    // post
    protected function _post($key = null) {
        return $this->input->post($key);
    }

    // post & get, post优先
    protected function _request($key = null) {
        $get = $this->input->get($key);
        $post = $this->input->post($key);
        return array_merge($get, $post);
    }


}