<?php defined('BASEPATH') OR exit('No direct script access allowed');

class role_model extends MY_Model {
    const TB_ROLE = 'role';
    const TB_ROLE_USERS = 'role_users';
    const TB_ROLE_API = 'role_api';

    public function __construct() {
        parent::__construct();
    }

    /**
     * 获取用户权限数组
     * @param $user_id
     * @return array
     */
    public function getRoles($user_id) {
        $this->db->where('user_id', $user_id);
        $res = $this->result_all(self::TB_ROLE_USERS);
        if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
            $roles = array_column($res, 'role_key');
        } else {
            $roles = array();
            foreach ($roles as $item) {
                array_push($item['role_key']);
            }
        }
        return $roles;
    }

    /**
     * 验证接口权限
     * @param $user_id
     * @param $api_key
     * @return bool
     */
    public function verify($user_id, $api_key) {
        $this->db->where('api_key', $api_key);
        $roles = $this->getRoles($user_id);
        $res = $this->result_all(self::TB_ROLE_API);
        $arr = array_intersect_key($roles, $res);
        return count($arr) > 0;
    }
}