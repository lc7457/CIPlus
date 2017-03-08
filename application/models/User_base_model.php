<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_base_model extends MY_model {
    public function __construct() {
        parent::__construct('user_base');
    }
}