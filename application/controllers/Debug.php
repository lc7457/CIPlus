<?php

/**
 * Created by PhpStorm.
 * User: 李超
 * Date: 2017/3/10
 * Time: 9:38
 */
class Debug extends CI_Controller {

    public function reg() {
        $this->load->view('debug/reg', array('token' => $this->access_token()));
    }

    public function login() {
        $this->load->view('debug/login', array('token' => $this->access_token()));
    }

    public function access_token() {
        $appid = 'demo';
        $secret = 'abcdefghijklmnopqrstuvwxyz0123456';
        $this->load->library('curl');
        $code = $this->curl->simple_get('api/access/code', array('appid' => $appid));
        echo 'code:' . $code;
        echo '<br>';
        $token = $this->curl->simple_get('api/access/token', array('code' => $code, 'secret' => $secret));
        echo 'token:' . $token;
        return $token;
    }
}