<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class wxtp_authorization_info_model extends MY_Model {
    public function __construct() {
        parent::__construct('wxtp_authorization_info');
        $this->db = $this->load->database('default', TRUE);
    }
}