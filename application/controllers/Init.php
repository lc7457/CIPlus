<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Init extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('user_model');
        $this->user_model->add('admin', 'admin@cprap.com', '', '21232f297a57a5a743894a0e4a801fc3');
    }

}
