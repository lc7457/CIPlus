<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class oauth_base_model extends MY_model {
    public function __construct() {
        parent::__construct('oauth_base');
    }
}
