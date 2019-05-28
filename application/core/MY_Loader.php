<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {
    public function __construct() {
        parent::__construct();
        $this->add_package_path(CIPLUS_PATH);
        $this->helper('language');
    }
}