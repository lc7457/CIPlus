<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {
    public function __construct() {
        echo "loader";
        parent::__construct();

    }
}