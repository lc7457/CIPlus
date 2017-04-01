<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_model {
    public function __construct() {
        parent::__construct('user');
    }

    public function CheckUserExist($email = '', $phone = '') {
        $this->db->where('email', $email);
        $this->db->or_where('phone', $phone);
        $query = $this->db->get($this->table);
        return $query->num_rows();
    }
}