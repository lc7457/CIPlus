<?php

class Access extends CI_Controller {

    public function Code() {
        $this->load->library('oauth');
        $appid = $this->input->get('appid');
        echo $this->oauth->CreateCode($appid);
    }

    public function Token() {
        $this->load->library('oauth');
        $code = $this->input->get('code');
        $secret = $this->input->get('secret');
        echo $this->oauth->CreateToken($code, $secret);
    }
}