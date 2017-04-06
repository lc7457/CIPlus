<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class wxtp_authorizer_info_model extends MY_Model {
    public function __construct() {
        parent::__construct('wxtp_authorizer_info');
        $this->db = $this->load->database('default', TRUE);
    }
}