<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Oauth 验证客户端
 */
class OauthClient {
    private $inner_ip = '';
    private $outer_url = '';
    private $entry = '';
    private $channel = '';

    private $appid = '';
    private $secret = '';
    private $platform = 'Linux';
    private $agent = '';

    public $token = '';
    public $timestamp = 0;
    public $role = '';
    public $key = '';
    public $illegalLevel = 0;
    public $handle = '';
    public $mismatch = array();
    public $securityData = '';

    public function __construct() {
        $this->CI =& get_instance();
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

    public function Access() {
        $url = $this->channel;
        $url = $this->$url . $this->entry;
        $params = $this->AnalysisParameters();
        $this->CI->load->library('curl');
        $this->CI->curl->option(CURLOPT_USERAGENT, sprintf('%s (%s) %s', $this->appid, $this->platform, $this->agent));
        $this->CI->curl->ssl(false);
        $access = $this->CI->curl->simple_post($url, $params);

        $access = json_decode($access, true);
        $this->role = $access['role'];
        $this->key = $this->AnalyseKey($access['key']);
        $this->illegalLevel = $access['illegalLevel'];
        $this->handle = $access['handle'];
        $this->mismatch = $access['mismatch'];
    }

    public function AnalyseToken() {
        $this->CI->load->library('encryption');
        $data = str_split($this->token, strlen($this->token) - 4);
        $token = base64_decode($data[0]);
        $res = $this->CI->encryption->decrypt($token, array(
            'cipher' => 'aes-128',
            'mode' => 'cbc',
            'key' => hex2bin($this->key),
            'hmac' => false
        ));
        $this->securityData = json_decode($res, true);
        return $this->securityData;
    }

    private function AnalyseKey($key) {
        if (!empty($key)) {
            $this->CI->load->library('encrypt');
            $key = urldecode($key);
            $key = $this->CI->encrypt->decode($key, $this->secret);
        }
        return $key;
    }

    private function AnalysisParameters() {
        $this->token = $this->CI->input->post_get('_token');
        $this->timestamp = $this->CI->input->post_get('_timestamp');
        $this->CI->load->library('user_agent');
        $params = array(
            'token' => $this->token,
            'timestamp' => $this->timestamp,
            'user_agent' => $this->UserAgent(),
            'ip' => $this->IP(),
            'device' => $this->DeviceInfo() OR ''
        );
        return $params;
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