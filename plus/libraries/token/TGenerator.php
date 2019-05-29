<?php defined('BASEPATH') or exit ('No direct script access allowed');
require_once './Token.abstract.php';

class TGenerator extends Token {
    public function __construct(array $data = array()) {
        parent::__construct();
        $this->init($data);
    }

    /**
     * 通过配置文件初始化
     * 目前必须包含header
     * @param array $data
     * @return $this
     */
    public function init(array $data) {
        if (is_array($data) && key_exists('header', $data)) {
            $this->header = $data['header'];
            $this->parseHeader($this->header);
        }
        return $this;
    }

    /**
     * 根据配置生成 Token
     * @return bool|mixed|string
     */
    public function generate() {
        switch ($this->typ) {
            case 'jwt':
                return $this->generate_jwt();
                break;
            default:
                return $this->simple();
        }
    }

    /**
     * 生成 JWT 格式的 Token
     * @return bool|string
     */
    public function generate_jwt() {
        if (!empty($this->rap) && key_exists($this->rap, $this->encryption)) {
            $cipher_text = $this->header . '.' . $this->getPayload(true);
            $sign = $this->encrypt($cipher_text, $this->encryption[$this->rap]);
            return $cipher_text . '.' . $sign;
        } else {
            return false;
        }
    }

    /**
     * 通过ID生成简单的 Token
     * @return bool|mixed|string
     */
    public function simple() {
        if (!key_exists('simple', $this->encryption)) return false;
        return $this->encrypt($this->getPayload(true), $this->encryption['simple']);
    }

    /**
     * 初始化化payload
     * @param $id
     * @return mixed|string
     */
    public function initPayload($id) {
        $this->payload['id'] = $id;
        $this->payload['iat'] = time();
        $this->payload['exp'] = time() + $this->expire_time;
        return $this;
    }

    //设置 Payload
    public function setPayload() {
        $count = func_num_args();
        if ($count === 1 && is_array(func_get_arg(0))) {
            foreach (func_get_arg(0) as $k => $v) {
                $this->payload[$k] = $v;
            }
        } elseif ($count === 2 && is_string(func_get_arg(0))) {
            $this->payload[func_get_arg(0)] = func_get_arg(1);
        }
        return $this;
    }

    // 获取 Payload
    public function getPayload($encode = false) {
        if ($encode) {
            return url64_encode(json_encode($this->payload));
        } else {
            return $this->payload;
        }
    }

}