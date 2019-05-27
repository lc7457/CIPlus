<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat_open_user_model extends MY_Model {
    const TABLE_USER_TOKEN = 'wechat_open_user_token';
    const TABLE_USER_INFO = 'wechat_open_user_info';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 记录用户授权信息
     * @param $auth
     * @param $mpId
     * @return mixed
     */
    public function saveUserToken($auth, $mpId) {
        $auth['authorizer_appid'] = $mpId; // 增加公众号标识
        $auth['expires_in'] += time();
        return $this->replace(self::TABLE_USER_TOKEN, $auth);
    }
    
    /**
     * 通过openid读取用户授权信息
     * @param $openid
     * @return mixed
     */
    public function loadUserTokenWithOpenid($openid) {
        $this->db->where('openid', $openid);
        return $this->row(self::TABLE_USER_TOKEN);
    }
    
    /**
     * 通过公众号openid和unionid读取用户授权信息
     * @param $unionid
     * @param string $appid 微信公众号appid
     * @return mixed
     */
    public function loadUserTokenWithUnionid($unionid, $appid) {
        $this->db->where('unionid', $unionid);
        $this->db->where('authorizer_appid', $appid);
        return $this->row(self::TABLE_USER_TOKEN);
    }
    
    /**
     * 记录用户身份信息
     * @param $info
     * @return mixed
     */
    public function saveUserInfo($info) {
        $info['privilege'] = json_encode($info['privilege']);
        return $this->replace(self::TABLE_USER_INFO, $info);
    }
    
    /**
     * 读取用户身份信息
     * @param string $id Openid OR unionid
     * @return mixed
     */
    public function loadUserInfo($id) {
        $this->db->where('openid', $id);
        $this->db->or_where('unionid', $id);
        return $this->row(self::TABLE_USER_INFO);
    }
}