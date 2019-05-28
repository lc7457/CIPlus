<?php defined('BASEPATH') OR exit('No direct script access allowed');

defined("CIPLUS_VERSION") OR define("CIPLUS_VERSION", "2.0");
defined("CIPLUS_PATH") OR define("CIPLUS_PATH", FCPATH . 'plus' . DIRECTORY_SEPARATOR);

class MY_Config extends CI_Config {
    public function __construct() {
        parent::__construct();
    }
}