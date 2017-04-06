<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/Number.php';

/**
 * Oauth 验证服务端
 */
class OauthServer {
    const COLUMN_APPID = 'appid';
    const COLUMN_SECRET = 'secret';
    const COLUMN_TOKEN = 'access_token';
    const COLUMN_TIMESTAMP = 'timestamp';

    private $auto_clean = true;
    private $refresh_token = false;
    private $expire_time = 3600;

    private $role = '';
    private $key = '';
    private $illegalLevel = 0;
    private $handle = '';
    private $mismatch = array();

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('oauth_model');
        $this->CI->load->model('oauth_track_model');
        $this->LoadConf();

    }

    /**
     * 加载配置文件
     */
    private function LoadConf() {
        $CI = &get_instance();
        $CI->config->load('oauth', true, true);
        $config = $CI->config->item('oauth');
        if (is_array($config)) {
            foreach ($config as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * 启动认证数据跟踪
     * @param $account
     * @param $type
     * @param $data
     * @return string
     */
    public function Track($account, $type, $data) {
        if ($this->auto_clean) {
            $this->CleanTimeout();
        }
        $code = $this->Code();
        $key = '';
        $data = $this->Cipher(json_encode($data), $key);
        $timestamp = time() + $this->expire_time;
        $whereArr = array(
            'account' => $account,
            'type' => $type,
            'user_agent' => $this->UserAgent(),
            'device' => $this->DeviceInfo(),
            'ip' => $this->IP()
        );
        $dataArr = array(
            'code' => $code,
            'cipher_text' => $data,
            'key' => $key,
            'expires_in' => $timestamp
        );
        if ($this->CI->oauth_track_model->count($whereArr) > 0) {
            $this->CI->oauth_track_model->update($dataArr, $whereArr);
        } else {
            $arr = array_merge($whereArr, $dataArr);
            $this->CI->oauth_track_model->insert($arr);
        }
        return array(
            'token' => $data . $code,
            'timestamp' => $timestamp
        );
    }

    /**
     * 验证跟踪数据
     * @param array $params
     */
    public function Validate(array $params) {
        if (empty($params['token']) || empty($params['timestamp'])) {
            return;
        } else {
            $token = $params['token'];
            $data = str_split($token, strlen($token) - 4);
            $cipherText = $data[0];
            $code = $data[1];
            $data = array(
                //'cipher_text' => $cipherText,
                'code' => $code,
                'expires_in' => $params['timestamp']
            );
            $track = $this->CI->oauth_track_model->row($data);
            if ($track) {
                $this->TrackBack($track, $params);
            } else {
                //$this->handle = $data;
                array_push($this->handle, 'Illegal Access');
            }
        }
    }

    private function TrackBack($track, $params) {
        if ($track['expires_in'] < time()) {
            $this->handle = 'time out';
            //$this->handle = 'Time Out';
            return;
        } else {
            $this->role = $track['type'];
            $this->key = $this->Signed($track['key']);
            if ($params['user_agent'] != $track['user_agent']) {
                $this->illegalLevel++;
                array_push($this->mismatch, 'agent');
                //$this->handle = 'Illegal Agent';
            } elseif ($params['ip'] != $track['ip']) {
                $this->illegalLevel++;
                array_push($this->mismatch, 'ip');
            } elseif ($params['device'] != $track['device']) {
                $this->illegalLevel++;
                array_push($this->mismatch, 'device');
            }
        }
    }

    /**
     * 对Key进行签名，该操作必须在CURL下执行，需要在UA配置文件中添加验证
     *     $robots['xxx'] = 'XXX';
     * @param $key
     * @return string
     */
    private function Signed($key) {
        $this->CI->load->library('user_agent');
        $timestamp = time();
        if ($this->CI->agent->is_robot() && $this->CI->agent->platform == 'Linux') {
            $agent = explode(' ', $this->CI->agent->agent_string());
            $oauth = $this->CI->oauth_model->row(
                array('appid' => $agent[0])
            );
            if ($oauth) {
                $this->CI->load->library('encrypt');
                //$key = $oauth['secret'];
                $key = urlencode($this->CI->encrypt->encode($key, $oauth['secret']));
            } else {
                $key = "";
            }
        }
        return $key;
    }

    public function Respond() {
        return array(
            'role' => $this->role,
            'key' => $this->key,
            'illegalLevel' => $this->illegalLevel,
            'handle' => $this->handle,
            'mismatch' => $this->mismatch
        );
    }

    private function CleanTimeout() {

    }

    /**
     * 随机生成Code编号
     * 四位随机字符串的区间为 46656 - 1679615
     * 为了保证在7200秒有效时间内不重复，随机数加上时间戳的后四位生成随机字符串编码
     */
    private function Code() {
        $number = new CIPlus\Number();
        return $number->encode(rand(5, 166) . substr(time(), -4));
    }

    private function UserAgent() {
        $this->CI->load->library('user_agent');
        return $this->CI->agent->agent_string();
    }

    private function IP() {
        $this->CI->load->helper('IP');
        return client_ip();
    }

    private function DeviceInfo() {
        return $this->CI->input->post_get('_device');
    }

    private function Cipher($data, &$key) {
        $this->CI->load->library('encryption');
        $key = $this->CI->encryption->create_key(16);
        $cipher = $this->CI->encryption->encrypt($data, array(
            'cipher' => 'aes-128',
            'mode' => 'cbc',
            'key' => $key,
            'hmac' => false,
        ));
        $key = bin2hex($key);
//        $text = base64_decode(urldecode($cipher));
//        // 解密测试
//        $text = $this->CI->encryption->decrypt($text, array(
//            'cipher' => 'aes-128',
//            'mode' => 'cbc',
//            'key' => hex2bin($key),
//            'hmac' => false,
//        ));
//        log_message('error', $text);
        $cipher = urlencode(base64_encode($cipher));
        return $cipher;
    }

}