<?php

class CIPlusHook {

    public function pre_system() {
        defined("CIPLUS_VERSION") OR define("CIPLUS_VERSION", "2.0");
        defined("CIPLUS_PATH") OR define("CIPLUS_PATH", FCPATH . 'plus' . DIRECTORY_SEPARATOR);
        defined("PLUGINS_PATH") OR define("PLUGINS_PATH", CIPLUS_PATH . 'plugins' . DIRECTORY_SEPARATOR);
    }

    public function post_controller_constructor() {
        $CI = &get_instance();
        $CI->load->library('CIPlus');
    }
}