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
    const COLUMN_TOKEN_EXPIRES = 'token_expires_in';

    const TOKEN_EXPIRES_TIME = 3600;

    private $appid;
    private $number;

    private $role = 1;
    private $key = '';
    private $securityLevel = 0;
    private $handle = '';

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('oauth_model');
        $this->LoadConf();
        $this->number = new CIPlus\Number();
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

    public function Validate(array $params = array()) {
        if (empty($params['token']) || empty($params['timestamp'])) {
            return;
        } else {
            $this->handle = json_encode($params);
        }
    }

    public function Respond() {
        return array(
            'role' => $this->role,
            'key' => $this->key,
            'securityLevel' => $this->securityLevel,
            'handle' => $this->handle
        );
    }


}