<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oauth_token_model extends MY_model {
    public function __construct() {
        parent::__construct('oauth_token');
    }
}