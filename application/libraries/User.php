<?php
require_once FCPATH . 'plus/Validated.php';
require_once FCPATH . 'plus/Number.php';

class User {
    const EMAIL_COLUMN = 'email';
    const PHONE_COLUMN = 'phone';
    const PASSWORD_COLUMN = 'password';
    const VERSION_COLUMN = 'version';

    const PREFIX_UID = 'CU_'; // Ceramic User

    private $email;
    private $phone;
    private $password;

    private $encryptKey = 'ce313c8358eae99bb9c89de695604064'; // 密码二次加密种子
    private $encryptVersion = 1; // 密码二次加密版本

    private $valid;
    private $number;

    public function __construct(array $config = array()) {
        $this->CI =& get_instance();
        $this->CI->load->library('user_agent');
        $this->CI->load->model('user_base_model');
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
            if (isset($this->$key)) {
                $this->$key = $val;
            }
        }
    }

    /**
     * 添加用户
     * @param $email
     * @param $phone
     * @param $password
     * @return int
     */
    public function AddUser($email, $phone, $password) {
        if (!$this->ValidAccount($email, $phone) || !$this->SetPassword($password)) {
            return 40001;
        } else if ($this->CheckUserExist()) {
            return 40101;
        } else {
            $value = array(
                self::EMAIL_COLUMN => $this->email,
                self::PHONE_COLUMN => $this->phone,
                self::PASSWORD_COLUMN => $this->EncryptPassword(),
                self::VERSION_COLUMN => $this->encryptVersion
            );
            $id = $this->CI->user_base_model->insert($value);
            $this->InitUid($id);
            return 20000;
        }
    }

    /**
     * 验证用户帐号密码
     * @param $email
     * @param $phone
     * @param $password
     * @return int
     */
    public function ValidUser($email, $phone, $password) {
        if (!$this->ValidAccount($email, $phone) || !$this->SetPassword($password)) {
            return 40001;
        } else if (!$this->CheckUserExist()) {
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
     * 验证帐号是否合法
     * @param $email
     * @param $phone
     * @return bool
     */
    public function ValidAccount($email, $phone) {
        return $this->SetEmail($email) || $this->SetPhone($phone);
    }

    /**
     * 验证密码
     * @return bool
     */
    private function ValidPassword() {
        $whereArr = array();
        if (!empty($this->email)) {
            $whereArr[self::EMAIL_COLUMN] = $this->email;
        } elseif (!empty($this->phone)) {
            $whereArr[self::PHONE_COLUMN] = $this->phone;
        }
        $ub = $this->CI->user_base_model->row($whereArr);
        return $this->DecryptPassword($ub[self::PASSWORD_COLUMN]) === $this->password;
    }

    /**
     * 验证并设置Email属性
     * @param $email
     * @return bool
     */
    private function SetEmail($email) {
        if ($this->valid->Regexp('email', $email)) {
            $this->email = $email;
            return true;
        }
        return false;
    }

    /**
     * 验证并设置Phone属性
     * @param $phone
     * @return bool
     */
    private function SetPhone($phone) {
        if ($this->valid->Regexp('zh_phone', $phone)) {
            $this->phone = $phone;
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
    private function CheckUserExist() {
        $hasEmail = $this->CI->user_base_model->count(array(self::EMAIL_COLUMN => $this->email));
        $hasPhone = $this->CI->user_base_model->count(array(self::PHONE_COLUMN => $this->phone));
        return $hasEmail > 0 || $hasPhone > 0;
    }

    /**
     * 给对应帐号生成uid
     * @param $n
     * @return string
     */
    private function InitUid($n) {
        $uid = self::PREFIX_UID . $this->number->Zerofill($n);
        $this->CI->user_base_model->update(array('uid' => $uid), array('id' => $n));
        return $uid;
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