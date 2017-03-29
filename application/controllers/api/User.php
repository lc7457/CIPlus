<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'core/API_Controller.php';

class User extends API_Controller {
    public function __construct() {
        parent::__construct(array(
            'tokenVerifier' => true
        ));
    }

    public function index() {
//        $p = $this->input->get_post('p') OR 1;
//        $n = 20;
        $this->Respond();
    }
}