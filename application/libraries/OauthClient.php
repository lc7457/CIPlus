<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Oauth 验证客户端
 */
class OauthClient {
    private $innerIp = '';
    private $outerUrl = '';
    private $entry = '';
    private $channel = '';

    private $identity = '';
    private $platform = 'Linux';
    private $agent = '';

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
        $this->CI->curl->option(CURLOPT_USERAGENT, sprintf('%s (%s) %s', $this->identity, $this->platform, $this->agent));
        $role = $this->CI->curl->simple_post($url, $params);
        $this->AnalyseRole($role);
    }

    private function AnalyseRole($role) {
        print_r($role);
    }

    private function AnalysisParameters() {
        $this->CI->load->library('user_agent');
        $params = array(
            'token' => $this->CI->input->post_get('_token'),
            'timestamp' => $this->CI->input->post_get('_timestamp'),
            'user_agent' => $this->UserAgent(),
            'ip' => $this->IP(),
            'device' => $this->DeviceInfo()
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