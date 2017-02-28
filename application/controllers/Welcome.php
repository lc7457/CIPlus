<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/REST_Controller.php';

class Welcome extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }

    public function index()
    {
        echo "hhh";
        $this->load->view('welcome_message');
    }
}
