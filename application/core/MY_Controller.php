<?php
defined('BASEPATH') or exit ('No direct script access allowed');
require_once APPPATH . 'core/API_Controller.php';

abstract class MY_Controller extends API_Controller {
    protected $uid = '';
    protected $data = array();
    public $isLogin = false;
    public $isAdmin = false;
    private $powerKey = 'none';
    private $tokenVerifier = true;

    protected $checkLogin = false;
    protected $checkPower = false;

    public function __construct($config) {
        parent::__construct($config);
        $this->SetConf($config);
        $this->AnalysisData();
        if ($this->tokenVerifier) {
            $this->Verifier();
        }
        if ($this->checkLogin) {
            $this->CheckLogin();
        }
        if ($this->checkPower) {
            $this->CheckPower();
        }
    }

    /**
     * 接收数据
     */
    protected function Request() {
        $post = $this->_request();
        foreach ($post as $key => $val) {
            if (property_exists($this, $key)) { // php5.3+
                $this->$key = $val;
            }
        }
    }

    /**
     * 解析关键数据
     */
    private function AnalysisData() {
        $this->data = is_array($this->oauthclient->securityData) ? $this->oauthclient->securityData : json_decode($this->oauthclient->securityData, true);
        $this->UserType();
        $this->Uid();
    }

    /**
     * 检测用户类型
     * @return string
     */
    private function UserType() {
        if ($this->oauthclient->role === 'admin') {
            $this->isAdmin = true;
            $this->powerKey = 'admin';
        } elseif ($this->oauthclient->role === 'user') {
            $this->isAdmin = false;
            $this->powerKey = 'user';
        } else {
            $isLogin = false;
        }
        return $this->oauthclient->role;
    }

    /**
     * 解析UID
     */
    private function Uid() {
        if (is_array($this->data) && array_key_exists('uid', $this->data)) {
            $this->uid = $this->data['uid'];
            $this->isLogin = true;
        }
    }

    /**
     * 检测是否已经登录
     */
    public function CheckLogin() {
        if (!$this->isLogin) {
            $this->SetCode(40098);
            $this->Respond();
        }
    }

    /**
     * 检测是否具有权限
     */
    public function CheckPower() {
        $power = constant($this->router->class . "::POWER_" . strtoupper($this->powerKey));
        $key = strtolower($this->router->method);
        if (!in_array($key, $power)) {
            $this->SetCode(40097);
            $this->Respond();
        }
    }

    public function Debug() {
        echo 'token : ' . $this->oauthclient->token . "\n";
        echo 'timestamp : ' . $this->oauthclient->timestamp . "\n";
        echo 'role : ' . $this->oauthclient->role . "\n" . "<br>";
        echo 'key : ' . $this->oauthclient->key . "\n" . "<br>";
        echo 'illegalLevel : ' . $this->oauthclient->illegalLevel . "\n";
        echo 'handle : ' . json_encode($this->oauthclient->handle) . "\n";
        echo 'security :' . json_encode($this->oauthclient->securityData) . "\n";
        echo 'mismatch:' . json_encode($this->oauthclient->mismatch) . "\n";
    }
}