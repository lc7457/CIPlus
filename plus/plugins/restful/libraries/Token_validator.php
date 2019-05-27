<?php defined('BASEPATH') or exit ('No direct script access allowed');
require_once FCPATH . 'plugins' . DIRECTORY_SEPARATOR . 'restful' . DIRECTORY_SEPARATOR . 'Token.abstract.php';

class Token_validator extends Token {
    private $token;

    public function __construct(array $data = array()) {
        parent::__construct();
        if (!empty($data) && key_exists('token', $data) && key_exists('type', $data))
            $this->setToken($data['token'], $data['type']);
    }

    /**
     * 验证 Token 有效性
     * @param null $token
     * @param string $type
     * @return bool
     */
    public function validate($token = null, $type = 'simple') {
        if (empty($this->token) && !empty($token)) {
            $this->setToken($token, $type);
        }
        switch ($this->typ) {
            case 'jwt':
                return $this->validate_jwt();
                break;
            default:
                return $this->simple();
        }
    }

    private function validate_jwt() {
        $tr = explode('.', $this->token);
        return count($tr) === 3
            && $this->parseHeader($tr[0])
            && $this->verifyJwtSign(...$tr)
            && $this->setPayload($tr[1]);
    }

    /**
     * 验证签名是否有效
     * @param $header
     * @param $payload
     * @param $sign
     * @return bool
     */
    public function verifyJwtSign($header, $payload, $sign) {
        return $header . '.' . $payload === $this->decryptSign($sign, $this->rap);
    }

    /**
     * 验证简单格式的 Token
     */
    private function simple() {
        $payload = $this->decryptSign($this->token, 'simple');
        return $this->setPayload($payload);
    }

    /**
     * 获取 Token
     * @return mixed
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * 设置 Token
     * @param $token
     * @param string $type
     * @return $this
     */
    public function setToken($token, $type = 'simple') {
        $this->token = $token;
        $this->typ = $type;
        return $this;
    }

    /**
     * 获取 Payload
     * @param null $key
     * @return bool
     */
    public function getPayload($key = null) {
        if (empty($key)) return $this->payload;
        elseif (key_exists($key, $this->payload)) return $this->payload[$key];
        else return false;
    }

    /**
     * 验证并设置 Token
     * @param $payload
     * @return bool
     */
    public function setPayload($payload) {
        $pr = json_decode(url64_decode($payload), true);
        foreach ($this->payload_required as $item) {
            if (key_exists($item, $pr)) {
                if (property_exists($this, $item)) {
                    $this->$item = $pr[$item];
                }
            } else {
                return false;
            }
        }
        if ($pr['iat'] <= time() && $pr['exp'] > time()) {
            $this->payload = $pr;
            return true;
        } else {
            return false;
        }
    }


    /**
     * 解析签名
     * @param $sign
     * @param string $mode
     * @return bool|string
     */
    private function decryptSign($sign, $mode = 'simple') {
        if (empty($sign) || !key_exists($mode, $this->encryption)) return false;
        return $this->decrypt($sign, $this->encryption[$mode]);
    }
}