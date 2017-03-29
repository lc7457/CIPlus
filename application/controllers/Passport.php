<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 全站通行证
 */
class Passport extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('user_agent');
    }

    public function Server() {

    }

    public function Code() {
        $this->load->library('OauthServer');
        $appid = $this->input->get('appid');
        echo $this->oauth->CreateCode($appid);
    }

    public function Token() {
        $this->load->library('OauthServer');
        $code = $this->input->get('code');
        $secret = $this->input->get('secret');
        echo $this->oauth->CreateToken($code, $secret);
    }

    public function User($type = 'login') {
        if ($type === 'login') {
            $this->UserLogin();
        } else {
            $this->UserReg();
        }
    }

    private function UserLogin() {
        $this->load->library('user');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $password = $this->input->post('password');
        $code = $this->user->ValidUser($email, $phone, $password);
        $this->SetCode($code);
        $this->SetData(array('uid' => $this->user->uid));
        $this->DoResponse();
    }

    private function UserReg() {
        $this->load->library('user');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $password = $this->input->post('password');
        $code = $this->user->AddUser($email, $phone, $password);
        $this->SetCode($code);
        $this->DoResponse();
    }

    public function Admin() {
        $this->load->library('admin');
        $admin = $this->post('admin');
        $password = $this->post('password');
        $code = $this->user->ValidUser($admin, $password);
        $this->SetCode($code);
        $this->SetData(array('uid' => $this->user->uid));
        $this->DoResponse();
    }

    public function Refresh() {
        $this->load->library('curl');
        $this->curl->ssl(false);
        echo $this->curl->simple_get('https://passport.jciuc.com/debug/ua');
    }
}