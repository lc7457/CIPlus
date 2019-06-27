<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {
    const TB_USER = CIPLUS_DB_PREFIX . 'user';
    const TB_USER_INFO = CIPLUS_DB_PREFIX . 'user_info';

    public function __construct() {
        parent::__construct();
    }

    /**
     * 验证账号
     * @param $account
     * @param $password
     * @return int|null
     */
    public function verify_account($account, $password) {
        $this->db->where('usable', 1);
        $this->db->where('account', $account);
        $user = $this->row(self::TB_USER);
        return $this->compare_password($user, $password);
    }

    /**
     * 验证email
     * @param $email
     * @param $password
     * @return int|null
     */
    public function verify_email($email, $password) {
        $this->db->where('usable', 1);
        $this->db->where('email', $email);
        $user = $this->row(self::TB_USER);
        return $this->compare_password($user, $password);
    }

    /**
     * 验证手机号
     * @param $phone
     * @param $password
     * @return int|null
     */
    public function verify_phone($phone, $password) {
        $this->db->where('usable', 1);
        $this->db->where('phone', $phone);
        $user = $this->row(self::TB_USER);
        return $this->compare_password($user, $password);
    }

    /**
     * 比对密码
     * @param $user
     * @param $password
     * @return int|null
     */
    private function compare_password($user, $password) {
        log_message('error', $this->decrypt($user['password']));
        if ($this->decrypt($user['password']) === $password) {
            return $user['id'];
        }
        return null;
    }

    /**
     * 添加用户账号
     * @param $account
     * @param $email
     * @param $phone
     * @param $password
     * @return mixed
     */
    public function add($account, $email, $phone, $password) {
        $data = array(
            'account' => $account,
            'email' => $email,
            'phone' => $phone,
            'password' => $this->encrypt($password),
            'create_time' => time()
        );
        $re = $this->insert(self::TB_USER, $data);
        return $re;
    }

    /**
     * 获取用户相关信息
     * @param $id
     * @return bool|mixed
     */
    public function getInfo($id) {
        if (!empty($id)) {
            $select = 'tb2.account,tb2.email,tb2.phone,tb2.create_time,tb1.name,tb1.avatar,tb1.introduction,tb1.sex,tb1.area,tb1.city,tb1.province,tb1.country';
            $select = str_replace(array('tb1', 'tb2'), array(self::TB_USER_INFO, self::TB_USER), $select);
            $join = 'tb1.id = tb2.id';
            $join = str_replace(array('tb1', 'tb2'), array(self::TB_USER_INFO, self::TB_USER), $join);
            $this->db
                ->select($select)
                ->from(self::TB_USER_INFO)
                ->join(self::TB_USER, $join, 'LEFT')
                ->where(self::TB_USER . '.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        }
        return [];
    }

    /**
     * 判断是否存在用户信息
     * @param $id
     * @return bool
     */
    public function simpleInfo($id) {
        return $this->row(self::TB_USER_INFO, array('id' => $id));
    }

    /**
     * 设置用户相关信息
     * @param $id
     * @param $info
     * @return mixed
     */
    public function setInfo($id, array $info) {
        $info['id'] = $id;
        $info['sex'] = empty($info['sex']) ? 0 : $info['sex'];
        $info['avatar'] = empty($info['avatar']) ? '/' : $info['avatar'];
        $info['country'] = empty($info['country']) ? '86' : $info['country'];
        return $this->replace(self::TB_USER_INFO, $info);
    }

    /**
     * 修改密码
     * @param $id
     * @param $old_password
     * @param $new_password
     * @return bool|mixed
     */
    public function changePassword($id, $old_password, $new_password) {
        $this->db->where('id', $id);
        $user = $this->row(self::TB_USER);
        if ($this->compare_password($user, $old_password)) {
            return $this->update(self::TB_USER, ['password' => $this->encrypt($new_password)], ['id' => $id]);
        }
        return false;
    }

    /**
     * 对成员密码二次加密
     * @param $password
     * @return string
     */
    private function encrypt($password) {
        $this->load->library('encryption');
        $this->encryption->initialize(array(
                'key' => null,
                'cipher' => 'aes-256',
                'mode' => 'cbc'
            )
        );
        return $this->encryption->encrypt($password);
    }

    /**
     * 对成员密码二次解密
     * @param $password
     * @return string
     */
    private function decrypt($password) {
        $this->load->library('encryption');
        $this->encryption->initialize(array(
                'key' => null,
                'cipher' => 'aes-256',
                'mode' => 'cbc'
            )
        );
        return $this->encryption->decrypt($password);
    }
}