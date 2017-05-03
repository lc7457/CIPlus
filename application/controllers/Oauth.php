<?php

/**
 * Class Oauth
 * 服务认证器
 */
class Oauth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('OauthServer');
        ob_start();
    }

    public function index() {
        $params = array(
            'token' => $this->input->post('token'),
            'timestamp' => $this->input->post('timestamp'),
            'user_agent' => $this->input->post('user_agent'),
            'ip' => $this->input->post('ip'),
            'device' => $this->input->post('device'),
        );
        $this->oauthserver->Validate($params);
        $this->Callback();
    }

    private function Callback() {
        ob_end_clean();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode(
            $this->oauthserver->Respond()
        ));
        $this->output->_display();
        exit;
    }
}