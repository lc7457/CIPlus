<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'] = function () {
    defined("CIPLUS_VERSION") OR define("CIPLUS_VERSION", "1.0");
    defined("CIPLUS_PATH") OR define("CIPLUS_PATH", FCPATH . 'plus' . DIRECTORY_SEPARATOR);
    defined("PLUGINS_PATH") OR define("PLUGINS_PATH", FCPATH . 'plugins' . DIRECTORY_SEPARATOR);
};