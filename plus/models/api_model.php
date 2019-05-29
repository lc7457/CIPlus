<?php defined('BASEPATH') OR exit('No direct script access allowed');

class api_model extends MY_Model {
    const TB_API = 'api';


    public function __construct() {
        parent::__construct();
    }

    public function by_path($path) {
        $this->db->where('usable', 1);
        return $this->row(self::TB_API, ['path' => $path]);
    }
}