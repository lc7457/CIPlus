<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/ValueStore.php';

class Home extends CI_Controller {

    public function index() {
        new CIPlus\ValueStore();
    }
}
