<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once PLUGINS_PATH . 'restful' . DIRECTORY_SEPARATOR . 'Restful_Controller.php';

class RestfulApi extends Restful_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->request([], ['name']);
        $this->respond(20000, $this->requestParam());
    }
}