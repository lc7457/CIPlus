<?php

/**
 * Created by PhpStorm.
 * User: 李超
 * Date: 2017/3/10
 * Time: 9:38
 */
class Debug extends CI_Controller {

    public function reg() {
        $this->load->view('debug/reg');
    }

    public function login() {
        $this->load->view('debug/login');
    }
}