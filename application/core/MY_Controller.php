<?php
defined('BASEPATH') or exit ('No direct script access allowed');
require_once APPPATH . 'core/API_Controller.php';

abstract class MY_Controller extends API_Controller {
    protected $uid = '';
    protected $data = array();
    public $isLogin = false;
    public $isAdmin = false;

    public function __construct($config) {
        parent::__construct($config);
        $this->AnalysisData();
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
        } elseif ($this->oauthclient->role === 'user') {
            $this->isAdmin = false;
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
     * @param bool $respond
     * @return bool
     */
    public function CheckLogin($respond = true) {
        if ($respond && !$this->isLogin) {
            $this->SetCode('40098');
            $this->Respond();
        }
        return $this->isLogin;
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