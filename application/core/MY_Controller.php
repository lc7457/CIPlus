<?php defined('BASEPATH') or exit ('No direct script access allowed');
require_once APPPATH . 'core/API_Controller.php';

abstract class MY_Controller extends API_Controller {
    protected $uid = '';
    protected $data = array();
    public $isLogin = false;
    public $isAdmin = false;
    private $powerKey = 'none';

    protected $checkToken = true;
    protected $checkLogin = true;
    protected $checkPower = true;

    public function __construct($config) {
        parent::__construct();
        $this->SetConf($config);
        $this->load->library('OauthClient');
        if ($this->checkToken) {
            $this->Verifier();
            if ($this->checkLogin) {
                $this->CheckLogin();
            }
            if ($this->checkPower) {
                $this->CheckPower();
            }
        }
    }

    // 接口验证
    protected function Verifier() {
        $this->oauthclient->Connect();
        if ($this->oauthclient->key) {
            $this->oauthclient->TokenDecode();
            $this->AnalysisData();
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
        echo 'token : ' . $this->oauthclient->token . "<br>";
        echo 'timestamp : ' . $this->oauthclient->timestamp . "<br>";
        echo 'role : ' . $this->oauthclient->role . "<br>";
        echo 'key : ' . $this->oauthclient->key . "<br>";
        echo 'illegalLevel : ' . $this->oauthclient->illegalLevel . "<br>";
        echo 'handle : ' . json_encode($this->oauthclient->handle) . "<br>";
        echo 'security :' . json_encode($this->oauthclient->securityData) . "<br>";
        echo 'mismatch:' . json_encode($this->oauthclient->mismatch) . "<br>";
        echo 'uid:' . $this->uid;
        exit;
    }
}