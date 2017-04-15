<?php
require_once FCPATH . 'plus/Validated.php';
require_once FCPATH . 'plus/Number.php';

class Admin {
    const COLUMN_ADMIN = 'admin';
    const COLUMN_PASSWORD = 'password';
    const COLUMN_LEVEL = 'level';

    private $admin;
    private $password;
    private $level;

    public $uid;

    private $encryptKey = 'SEED'; // 密码二次加密种子

    public function __construct(array $config = array()) {
        $this->CI =& get_instance();
        $this->CI->load->library('user_agent');
        $this->CI->load->model('admin_model');
        $this->valid = new CIPlus\Validated();
        $this->number = new CIPlus\Number();
        $this->InitConfig($config);
    }

    /**
     * 加载配置文件
     * @param $config
     */
    public function InitConfig($config) {
        foreach ($config as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    /**
     * 添加用户
     * @param $admin
     * @param $password
     * @return int
     */
    public function AddAdmin($admin, $password) {
        if (!$this->SetAdmin($admin) || !$this->SetPassword($password)) {
            return 40001;
        } else if ($this->CheckAdminExist()) {
            return 40101;
        } else {
            $value = array(
                self::COLUMN_ADMIN => $this->admin,
                self::COLUMN_PASSWORD => $this->EncryptPassword(),
            );
            $id = $this->CI->admin_model->insert($value);
            return 20000;
        }
    }


    /**
     * 验证用户帐号密码
     * @param $admin
     * @param $password
     * @return int
     */
    public function ValidUser($admin, $password) {
        if (!$this->SetAdmin($admin) || !$this->SetPassword($password)) {
            return 40001;
        } else if (!$this->CheckAdminExist()) {
            return 40102;
        } else {
            if ($this->ValidPassword()) {
                return 20000;
            } else {
                return 40103;
            }
        }
    }

    /**
     * 验证密码
     * @return bool
     */
    private function ValidPassword() {
        $whereArr = array();
        $whereArr[self::COLUMN_ADMIN] = $this->admin;
        $ub = $this->CI->admin_model->row($whereArr);
        if ($this->DecryptPassword($ub[self::COLUMN_PASSWORD]) === $this->password) {
            $this->uid = $ub[self::COLUMN_ADMIN];
            return true;
        }
        return false;
    }

    /**
     * 验证并设置admin属性
     * @param $admin
     * @return bool
     */
    private function SetAdmin($admin) {
        if ($this->valid->Regexp('id', $admin)) {
            $this->admin = $admin;
            return true;
        }
        return false;
    }

    /**
     * 验证并设置password属性
     * @param $password
     * @return bool
     */
    private function SetPassword($password) {
        if ($this->valid->Regexp('md5', $password)) {
            $this->password = $password;
            return true;
        }
        return false;
    }

    /**
     * 检测用户是否存在
     * @return bool
     */
    private function CheckAdminExist() {
        $hasAdmin = empty($this->admin) ? 0 : $this->CI->admin_model->count(array(self::COLUMN_ADMIN => $this->admin));
        return $hasAdmin > 0;
    }

    /**
     * 对用户密码二次加密
     * @return string
     */
    private function EncryptPassword() {
        $this->CI->load->library('encrypt');
        return $this->CI->encrypt->encode($this->password, $this->encryptKey);
    }

    /**
     * 对用户密码二次解密
     * @param $password
     * @return string
     */
    private function DecryptPassword($password) {
        $this->CI->load->library('encrypt');
        return $this->CI->encrypt->decode($password, $this->encryptKey);
    }
}