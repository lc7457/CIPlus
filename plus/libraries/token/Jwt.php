<?php defined('BASEPATH') or exit ('No direct script access allowed');
include_once CIPLUS_PATH . 'Jwt.abstract.php';

class Jwt extends CIPlus\Jwt {
    protected $CI;

    private $key;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('token', true, true);
        $config = $this->CI->config->item('token');
        $this->key = $this->CI->config->item('encryption_key');
        parent::__construct($config);
    }

    public function signed($header, $payload) {
        $this->CI->load->library('encryption');
        $cipher = $this->encode($header) . "." . $this->encode($payload);
        $alg = $this->sheet[$header['alg']];
        $alg['key'] = $this->key;
        $sign = $this->CI->encryption->encrypt($cipher, $alg);
        return $cipher . "." . url64_encode($sign);
    }

    public function decrypt($token) {
        $this->CI->load->library('encryption');
        $tkr = explode('.', $token);
        if (count($tkr) === 3) {
            $header = $this->decode($tkr[0]);
            $payload = $this->decode($tkr[1]);
            $sign = url64_decode($tkr[2]);

            $alg = $this->sheet[$header['alg']];
            $alg['key'] = $this->key;

            if ($this->CI->encryption->decrypt($sign) === $tkr[0] . '.' . $tkr[1]) {
                return array(
                    'header' => $header,
                    'payload' => $payload,
                    'sign' => $sign
                );
            }
        }


        return array(
            'header' => $header,
            'payload' => $payload,
            'sign' => $sign
        );
    }

    public function refresh($header, $payload) {
        // TODO: Implement refresh() method.
    }

    public function encode(array $array) {
        return url64_encode(json_encode($array));
    }

    public function decode($str) {
        return json_decode(url64_decode($str), true);
    }


}