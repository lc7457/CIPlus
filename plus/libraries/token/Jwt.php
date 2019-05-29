<?php defined('BASEPATH') or exit ('No direct script access allowed');

class Jwt {
    protected $CI;

    protected $expire_time;
    protected $refresh_time;

    protected $sheet;

    protected $header;
    protected $payload;

    protected $typ; // Token 序列类型
    protected $rap; // Token 加密方法

    protected $id; // 系统身份标识
    protected $iat; // 开始时间
    protected $exp; // 过期时间

    private $key;

    public function __construct() {
        $this->CI =& get_instance();
        $this->loadConf('token');
        $this->key = $this->CI->config->item('encryption_key');
    }

    /**
     * 加载CI config配置文件
     * @param $name
     * @return string|null
     */
    protected function loadConf($name) {
        $this->CI->config->load($name, true, true);
        $config = $this->CI->config->item($name);
        if (is_array($config)) {
            foreach ($config as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
        return $config;
    }


    public function setHeader($header) {
        if ($this->parseHeader($header)) {
            $this->header = $header;
        } else {
            $this->typ = 'jwt';
            $this->rap = 'norm';
            $this->header = json_encode(array(
                'typ' => $this->typ,
                'rap' => $this->rap
            ));
        }
        return $this;
    }

    public function parseHeader($header) {
        if (is_json($header)) {
            $header = json_decode($header, true);
        }
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