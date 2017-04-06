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
        echo 'token : ' . $this->oauthclient->token . "<br>";
        echo 'timestamp : ' . $this->oauthclient->timestamp . "<br>";
        echo 'role : ' . $this->oauthclient->role . "<br>";
        echo 'key : ' . $this->oauthclient->key . "<br>";
        echo 'illegalLevel : ' . $this->oauthclient->illegalLevel . "<br>";
        echo 'handle : ' . json_encode($this->oauthclient->handle) . "<br>";
        echo 'security :' . json_encode($this->oauthclient->securityData) . "<br>";
        echo 'mismatch :' . json_encode($this->oauthclient->mismatch);
    }
}