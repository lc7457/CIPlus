<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat_open_mp_model extends MY_Model {
    const TABLE_MP_TOKEN = 'wechat_open_mp_token';
    const TABLE_MP_INFO = 'wechat_open_mp_info';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 保存公众号授权信息
     * @param $authInfo
     * @return mixed
     */
    public function saveMpToken($authInfo) {
        $dataArr = array(
            'authorizer_appid' => $authInfo['authorizer_appid'],
            'authorizer_access_token' => $authInfo['authorizer_access_token'],
            'authorizer_refresh_token' => $authInfo['authorizer_refresh_token'],
            'authorizer_expires_in' => time() + $authInfo['expires_in'] - 120,
            'func_info' => json_encode($authInfo['func_info'])
        );
        return $this->replace(self::TABLE_MP_TOKEN, $dataArr);
    }
    
    /**
     * 读取公众号授权信息
     * @param $appid
     * @return mixed
     */
    public function loadMpToken($appid) {
        $this->db->where('authorizer_appid', $appid);
        return $this->row(self::TABLE_MP_TOKEN);
    }
    
    /**
     * 刷新公众号授权信息
     * @param $appid
     * @param $token
     * @return mixed
     */
    public function refreshMpToken($appid, $token) {
        $dataArr = array(
            'authorizer_access_token' => $token['authorizer_access_token'],
            'authorizer_refresh_token' => $token['authorizer_refresh_token'],
            'authorizer_expires_in' => time() + $token['expires_in'] - 120,
        );
        $this->db->where('authorizer_appid', $appid);
        return $this->update(self::TABLE_MP_TOKEN, $dataArr);
    }
    
    /**
     * 保存公众号基本信息
     * @param $info
     */
    public function saveMpInfo($info) {
        $appid = $info['authorization_info']['authorizer_appid'];
        $info = $info['authorizer_info'];
        $dataArr = array(
            'authorizer_appid' => $appid,
            'nick_name' => $info['nick_name'],
            'head_img' => $info['head_img'],
            'service_type_info' => $info['service_type_info']['id'],
            'verify_type_info' => $info['verify_type_info']['id'],
            'user_name' => $info['user_name'],
            'alias' => $info['alias'],
            'qrcode_url' => $info['qrcode_url'],
            'business_info' => json_encode($info['business_info']),
            'idc' => $info['idc'],
            'principal_name' => $info['principal_name'],
            'open_pay' => $info['business_info']['open_pay'],
            'open_shake' => $info['business_info']['open_shake'],
            'open_scan' => $info['business_info']['open_scan'],
            'open_card' => $info['business_info']['open_card'],
            'open_store' => $info['business_info']['open_store'],
        );
        $whereArr = array(
            'authorizer_appid' => $appid
        );
        $this->db->where($whereArr);
        $n = $this->count(self::TABLE_MP_INFO);
        if ($n > 0) {
            $this->update(self::TABLE_MP_INFO, $dataArr, $whereArr);
        } else {
            $dataArr['create_time'] = time();
            $this->insert(self::TABLE_MP_INFO, $dataArr);
        }
        return;
    }
    
    /**
     * 读取公众号基本信息
     * @param $appid
     * @return mixed
     */
    public function loadMpInfo($appid) {
        $this->db->where('authorizer_appid', $appid);
        return $this->row(self::TABLE_MP_INFO);
    }
}