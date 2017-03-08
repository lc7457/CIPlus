<?php
/**
 * 站点默认控制器
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index() {
        $this->load->view('home');
    }
}
