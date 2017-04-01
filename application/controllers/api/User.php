<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'core/API_Controller.php';

class User extends API_Controller {
    public function __construct() {
        parent::__construct(array(
            'tokenVerifier' => true,
            'strict' => false
        ));
    }

    public function index() {
        $s = $this->oauthclient->AnalyseToken();

        var_dump($s);

    }
}