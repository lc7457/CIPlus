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
        $this->load->model('oauth_track_model');
        $r = $this->oauth_track_model->row(array(
            'key' => $this->oauthclient->key
        ));
        echo urldecode($r['cipher_text']);
        echo "<br>";
        $this->oauthclient->AnalyseToken();
    }
}