<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.php';

Class Validator_token extends \CIPlus\CIClass {

    protected $token_sources; // token 获取方式
    protected $token_sources_key; // token 获取时的键值
    protected $token_inspect_api; // 检验token的API
    protected $token_inspect_method; // 验证token提交的方法
    protected $token_inspect_key; // 发送token时对应的key
    protected $token_respond_type;
    protected $token_respond_expected;

    private $token; // 获取到的token

    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('validator');
        $this->token = $this->getToken();
    }

    /**
     * 验证JWT格式的token
     * @return bool
     */
    public function jwt() {
        $tkr = explode('.', $this->token);
        if (
            count($tkr) === 3
            && is_json(url64_decode($tkr[0]))
            && is_json(url64_decode($tkr[1]))
        ) {
            return $this->remoteInspect();
        }
        return false;
    }

    // 远程验证token有效性
    private function remoteInspect() {
        $this->CI->load->library('curl');
        $method = 'simple_' . strtolower($this->token_inspect_method);
        $data = array(
            $this->token_inspect_key => $this->token
        );
        $res = $this->CI->curl->$method($this->token_inspect_api, $data);
        return $this->validRespond($res);
    }

    private function validRespond($res) {
        if (!empty($res)) {
            if ($this->token_respond_type === 'json') {
                $json = json_decode($res, true);
                return count($this->token_respond_expected) === count(array_intersect_assoc($this->token_respond_expected, $json));
            } else if ($this->token_respond_type === 'string') {
                return $this->token_respond_expected === $res;
            }
        }
        return false;
    }

    /// region <<< get token >>>

    // 获取Token
    private function getToken() {
        $method = 'getFrom' . ucfirst(strtolower($this->token_sources));
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return null;
    }

    // 从header中获取token
    private function getFromHeader() {
        if (key_exists($this->token_sources_key, $_SERVER)) {
            return $_SERVER[$this->token_sources_key];
        } else {
            return null;
        }
    }

    // 从http request中获取token
    private function getFromRequest() {
        return $this->CI->input->get_post($this->token_sources_key);
    }

    /// endregion
}