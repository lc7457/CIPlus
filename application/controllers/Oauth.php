<?php

/**
 * Class Oauth
 * 服务认证器
 */
class Oauth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('OauthServer');
    }
}