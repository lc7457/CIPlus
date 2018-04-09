<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.php';

Class Auth extends \CIPlus\CIClass {
    
    const HEADER_KEY = 'HTTP_TOKEN';
    const REQUEST_KEY = '_token';
    private $token = null;
    
    public $status = false;
    
    public function __construct(array $config = array()) {
        parent::__construct();
        $this->Capture();
    }
    
    // 获取Token
    // TODO: 验证token有效性
    public function Capture() {
        $this->token = $this->CaptureFromHeader();
        $this->status = $this->token === "cifuwu";
    }
    
    // 从header中获取token
    private function CaptureFromHeader() {
        if (key_exists(self::HEADER_KEY, $_SERVER)) {
            return $_SERVER[self::HEADER_KEY];
        } else {
            return null;
        }
    }
    
    // 从http request中获取token
    private function CaptureFromRequest() {
        return $this->CI->input->get_post(self::REQUEST_KEY);
    }
}