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
        if ($this->agent->is_browser())
        {
            $agent = $this->agent->browser().' '.$this->agent->version();
        }
        elseif ($this->agent->is_robot())
        {
            $agent = $this->agent->robot();
        }
        elseif ($this->agent->is_mobile())
        {
            $agent = $this->agent->mobile();
        }
        else
        {
            $agent = 'Unidentified User Agent';
        }
        echo $agent;
    }

    public function User() {
        $this->load->library('user');
        $email = $this->post('email');
        $phone = $this->post('phone');
        $password = $this->post('password');
        $code = $this->user->ValidUser($email, $phone, $password);
        $this->SetCode($code);
        $this->SetData(array('uid' => $this->user->uid));
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
        echo $this->curl->simple_get('/passport/server');
    }
}