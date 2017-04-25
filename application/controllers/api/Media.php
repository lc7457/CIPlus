<?php defined('BASEPATH') or exit ('No direct script access allowed');

class Media extends MY_Controller {
    public function __construct() {
        parent::__construct(array(
            'checkToken' => false,
            'checkLogin' => false,
            'checkPower' => false
        ));
    }

    public function Index() {

    }
}