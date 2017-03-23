<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $p = $this->input->get_post('p') OR 1;
        $n = 20;
    }
}