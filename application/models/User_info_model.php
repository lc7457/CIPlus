<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_info_model extends MY_Model {
    public function __construct() {
        parent::__construct('user_info');
    }
}