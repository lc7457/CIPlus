<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/REST_Controller.php';

class Welcome extends REST_Controller
{
    public function index()
    {
        $this->load->view('welcome_message');
    }
}
