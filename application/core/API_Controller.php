<?php

/**
 * API 控制器
 */
abstract class API_Controller extends CI_Controller {
    const KEY_CODE = 'code';
    const KEY_MESSAGE = 'message';
    const KEY_DATA = 'data';

    const PARAMS_FORMAT = '_format';

    private $tokenVerifier = true; // 是否验证token
    private $strict = true; // 是否打开严格模式，打开后除了接口信息其他输出无效
    private $respondFormat = 'json'; // 默认数据格式
    private $supportedFormats = array(); // 可被支持的数据格式


    private $code = 40000;
    private $message = 'Access API Failed';
    private $data = array();

    public function __construct(array $config = array()) {
        parent::__construct();
        $this->lang->load('api_message');
        $this->LoadConf();
        $this->SetConf($config);
        $this->AnalysisParameters();
        $this->load->library('format');
        if ($this->tokenVerifier) {
            $this->load->library('OauthClient');
            $this->Verifier();
        }
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
    private function SetConf(array $config = array()) {
        foreach ($config as $key => $val) {
            if (isset($this->$key)) {
                $this->$key = $val;
            }
        }
    }

    // Get URL Parameters , 可以覆盖类中配置文件
    private function AnalysisParameters() {
        $this->_format();
    }

    // 接口验证
    private function Verifier() {
        $this->oauthclient->Access();
    }

    // _get('_format');
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
    }

    // API Message
    protected function SetMessage($message) {
        $this->message = $message;
    }

    // API Data
    protected function SetData(array $data = array()) {
        $this->data = $data;
    }

    // 响应事件，最后执行
    public function Respond() {
        if ($this->strict) {
           // ob_end_clean();
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

    // get
    protected function _get($key = '') {
        return $this->input->get($key);
    }

    // post
    protected function _post($key = '') {
        return $this->input->post($key);
    }

    // post & get
    protected function _request($key = '') {
        return $this->input->post_get($key);
    }

}