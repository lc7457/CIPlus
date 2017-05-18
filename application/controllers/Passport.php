<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 全站通行证
 */
require_once APPPATH . 'core/API_Controller.php';

class Passport extends API_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('user_agent');
        $this->load->library('OauthServer');
    }
    
    public function User($type = 'login') {
        if ($type === 'login') {
            $this->UserLogin();
        } else if ($type === 'reg') {
            $this->UserReg();
        }
    }

    private function UserLogin() {
        $this->load->library('user');
        $email = $this->_post('email');
        $phone = $this->_post('phone');
        $password = $this->_post('password');
        $code = $this->user->ValidUser($email, $phone, $password);
        $this->SetCode($code);
        $data = array('uid' => $this->user->uid);
        $this->SetSessionData($data);
        $data = $this->oauthserver->Track($this->user->uid, 'user', $data);
        $this->SetData($data);
        $this->Respond();
    }

    private function UserReg() {
        $this->load->library('user');
        $email = $this->_post('email');
        $phone = $this->_post('phone');
        $password = $this->_post('password');
        $code = $this->user->AddUser($email, $phone, $password);
        $this->SetCode($code);
        $this->Respond();
    }

    public function Admin($type = 'login') {
        if ($type === 'login') {
            $this->AdminLogin();
        } else if ($type === 'reg') {
            $this->AdminReg();
        }
    }

    private function AdminLogin() {
        $this->load->library('admin');
        $admin = $this->_post('admin');
        $password = $this->_post('password');
        $code = $this->admin->ValidUser($admin, $password);
        $this->SetCode($code);
        $data = array('uid' => $admin);
        $data = $this->oauthserver->Track($this->admin->uid, 'admin', $data);
        $this->SetData($data);
        $this->SetSessionData($data);
        $this->Respond();
    }

    private function AdminReg() {
        $this->load->library('admin');
        $admin = $this->_post('admin');
        $password = $this->_post('password');
        $code = $this->admin->AddAdmin($admin, $password);
        $this->SetCode($code);
        $this->Respond();
    }

    public function Refresh() {
    }
    private function SetSessionData($data) {
        $this->load->library('session');
        $this->session->set_userdata($data);
        return true;
    }
    public function Session() {
        $this->load->library('session');
        $data = $this->session->all_userdata();
        echo json_encode($data);
        exit;
    }
}