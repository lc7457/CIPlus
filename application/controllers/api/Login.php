<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('user');
    }

    public function Index_get() {
        $this->SetCode(40000);
        $this->DoResponse();
    }

    /**
     * 用户登录
     */
    public function Index_post() {
        $email = $this->post('email');
        $phone = $this->post('phone');
        $password = $this->post('password');
        $code = $this->user->ValidUser($email, $phone, $password);
        $this->SetCode($code);
        $this->DoResponse();
    }
}