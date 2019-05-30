<?php

namespace CIPlus;

abstract class Jwt {
    protected $expire_time;
    protected $refresh_time;

    protected $header = array('typ' => null, 'rap' => null);
    protected $payload = array('id' => null, 'iat' => null, 'exp' => null);

    protected $sheet = array();

    public function __construct(array $config = array()) {
        if (is_array($config)) {
            foreach ($config as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * 生成 Token
     * @param array $header
     * @param array $payload
     * @return string
     */
    public function generator(array $header, array $payload) {
        if ($this->verifyHeader($header) && key_exists('id', $payload)) {
            $header = $this->normHeader($header);
            $payload = $this->normPayload($payload);

            $cipher = $this->encode(json_encode($header)) . "." . $this->encode(json_encode($payload));
            $sign = $this->signed($cipher);

            return $cipher . '.' . $sign;
        } else {
            return null;
        }
    }

    /**
     * 验证 Token
     * @param $token
     * @return bool
     */
    public function validator($token) {
        $tkr = explode('.', $token);
        if (count($tkr) === 3) {
            $header = json_decode($this->decode($tkr[0]), true);
            $payload = json_decode($this->decode($tkr[1]), true);
            $sign = $this->decode($tkr[3]);
            return $this->signed($header, $payload) === $sign;
//            && $this->verifyHeader($tkr[0]) && $this->verifyPayload($tkr[1])) {
//                return $this->signed($tkr[0], $tkr[1]) === $tkr[3];
//            }
        }
        return null;
    }

    /**
     * 设置 Header
     * @param $header
     * @return mixed
     */
    protected function normHeader($header) {
        if (!in_array($header['typ'], array('jwt'))) {
            $header['typ'] = 'jwt';
        }
        if (!key_exists($header['rap'], $this->sheet)) {
            $header['rap'] = 'norm';
        }
        return $header;
    }

    /**
     * 设置 Payload
     * @param $payload
     * @return mixed
     */
    protected function normPayload($payload) {
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expire_time;
        return $payload;
    }

    /**
     * 验证 Header
     * @param $header
     * @return bool
     */
    protected function verifyHeader($header) {
        return $this->header == array_intersect_key($this->header, $header);
    }

    /**
     * 验证 Payload
     * @param $payload
     * @return bool
     */
    protected function verifyPayload($payload) {
        return $this->payload == array_intersect_key($this->payload, $payload)
            && $payload['exp'] > time()
            && $payload['iat'] < time();
    }

    /**
     * 安全编码
     * @param $string
     * @return mixed|string
     */
    protected function encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * 安全解码
     * @param $string
     * @return bool|string
     */
    protected function decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * 签名
     * @param $cipher
     * @return mixed
     */
    abstract function signed($cipher);

}