<?php defined('BASEPATH') or exit ('No direct script access allowed');
require_once 'API_Controller.php';

abstract class MY_Controller extends API_Controller {
    
    private $security = true; // 是否进行安全认证
    
    public $status = false;
    
    public $eid = ''; //enterprise id 企业ID
    public $uid = ''; //user id 用户ID
    
    public function __construct(array $config = array()) {
        parent::__construct();
        // 加载配置文件
        foreach ($config as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
        if ($this->security) {
            $this->Security();
        }
    }
    
    // 加载安全验证类
    private function Security() {
        $this->load->library('Auth');
//        if (!$this->auth->status) {
//            $this->Respond(40099);
//        }
    }
    
}