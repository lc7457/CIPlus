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

    private $token = '';
    private $code = '';
    private $cipher = '';
    private $key = '';
    private $role = '';
    private $illegalLevel = 0;
    private $mismatch = array();
    private $message = '';

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('oauth_model');
        $this->CI->load->model('oauth_track_model');
        $this->LoadConf();
    }

    // 加载配置文件
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
     * 验证跟踪数据
     * @param array $params
     */
    public function Validate(array $params) {
        if (!empty($params['token']) && !empty($params['timestamp'])) {
            $this->token = $params['token'];
            $data = str_split($this->token, strlen($this->token) - 4);
            $this->cipher = $data[0];
            $this->code = $data[1];
            $data = array(
                //'cipher_text' => $this->cipher,
                'code' => $this->code,
                'expires_in' => $params['timestamp']
            );
            $track = $this->CI->oauth_track_model->row($data);
            if ($track) {
                $this->TrackMatch($track, $params);
                $this->SignedKey($track['key']);
                $this->role = $track['type'];
            }
        } else {
            $this->message = 'Illegal Access';
        }
    }

    // 验证跟踪数据
    private function TrackMatch($track, $params) {
        if ($track['expires_in'] > time()) {
            if ($params['user_agent'] != $track['user_agent']) {
                $this->illegalLevel++;
                array_push($this->mismatch, 'agent');
            } elseif ($params['ip'] != $track['ip']) {
                $this->illegalLevel++;
                array_push($this->mismatch, 'ip');
            } elseif ($params['device'] != $track['device']) {
                $this->illegalLevel++;
                array_push($this->mismatch, 'device');
            }
        } else {
            $this->message = 'time out';
        }
    }

    /**
     * 对Key进行签名，该操作必须在CURL下执行，需要在UA配置文件中添加验证
     *     $robots['xxx'] = 'XXX';
     * @param $key
     */
    private function SignedKey($key) {
        $this->CI->load->library('user_agent');
        if ($this->CI->agent->is_robot() && $this->CI->agent->platform == 'Linux') {
            $agent = explode(' ', $this->CI->agent->agent_string());
            $oauth = $this->CI->oauth_model->row(array('appid' => $agent[0]));
            if ($oauth) {
                $this->CI->load->library('encrypt');
                $this->key = url64_encode($this->CI->encrypt->encode($key, $oauth['secret']));
            }
        }
    }

    // 返回数据
    public function Respond() {
        return array(
            'role' => $this->role,
            'key' => $this->key,
            'illegalLevel' => $this->illegalLevel,
            'handle' => $this->message,
            'mismatch' => $this->mismatch
        );
    }

    /**
     * 启动认证数据跟踪
     * @param $account
     * @param $type
     * @param $data
     * @return string
     */
    public function Track($account, $type, $data) {
        $this->CleanTimeout();
        $this->code = $this->Code();
        $this->cipher = $this->Cipher($data);
        $timestamp = time() + $this->expire_time;
        $whereArr = array(
            'account' => $account,
            'type' => $type,
            'user_agent' => $this->UserAgent(),
            'device' => $this->DeviceInfo(),
            'ip' => $this->IP()
        );
        $dataArr = array(
            'code' => $this->code,
            'cipher_text' => $this->cipher,
            'key' => $this->key,
            'expires_in' => $timestamp
        );
        if ($this->CI->oauth_track_model->count($whereArr) > 0) {
            $this->CI->oauth_track_model->update($dataArr, $whereArr);
        } else {
            $arr = array_merge($whereArr, $dataArr);
            $this->CI->oauth_track_model->insert($arr);
        }
        return array(
            'token' => $this->cipher . $this->code,
            'timestamp' => $timestamp
        );
    }

    // 生成密文密钥
    private function Cipher($data) {
        $data = json_encode($data);
        $this->CI->load->library('encryption');
        $key = $this->CI->encryption->create_key(16);
        $cipher = $this->CI->encryption->encrypt($data, array(
            'cipher' => 'aes-128',
            'mode' => 'cbc',
            'key' => $key,
            'hmac' => false,
        ));
        $this->key = url64_encode($key);
        return url64_encode($cipher);
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

    // 清理超时
    private function CleanTimeout() {
        if ($this->auto_clean) {
            $this->CI->oauth_track_model->delete(array('timestamp<', time()));
        }
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


}