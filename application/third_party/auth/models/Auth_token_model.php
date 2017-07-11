<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_token_model extends MY_model {
    public function __construct() {
        parent::__construct('_auth_token');
    }
}
