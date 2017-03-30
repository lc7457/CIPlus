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
        $this->CI->curl->simple_post($url, $params);
    }

    private function AnalysisParameters() {
        //$this->_format();
    }
}