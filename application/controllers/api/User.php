<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function reg_post() {
        $uid = $this->post('uid');
        $password = $this->get('password');
        $data = array('uid' => $uid, 'password' => $password);
        $this->SetData($data);
        $this->DoResponse();
    }

    public function index_delete() {

    }
}