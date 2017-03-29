<?php

class Debug extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        phpinfo();
    }

    public function ua() {
        $this->load->library('user_agent');
        echo $this->agent->agent_string();
        echo '<br>';
        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser() . ' ' . $this->agent->version();
        } elseif ($this->agent->is_robot()) {
            $agent = $this->agent->robot();
        } elseif ($this->agent->is_mobile()) {
            $agent = $this->agent->mobile();
        } else {
            $agent = 'Unidentified User Agent';
        }
        echo 'agent: ' . $agent;
        echo '<br>';
        echo 'platform: ' . $this->agent->platform(); // Platform info (Windows, Linux, Mac, etc.)
        echo '<br>';
        echo 'referrer: ' . $this->agent->referrer();
    }


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

    public function Upload() {
        $this->load->view("debug/upload");
    }
}