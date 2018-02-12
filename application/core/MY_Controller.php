<?php defined('BASEPATH') or exit ('No direct script access allowed');
require_once 'API_Controller.php';

abstract class MY_Controller extends API_Controller {
    
    public function __construct(array $config = array()) {
        parent::__construct();
    }
    
}