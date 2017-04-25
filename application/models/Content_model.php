<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Content_model extends MY_model {
    public function __construct() {
        parent::__construct('content');
    }

    // 冻结数据
    public function freeze($whereIn) {
        $dataArr['status'] = 0;
        $this->db->where_in($whereIn);
        $this->db->update($this->table, $dataArr);
        return $this->db->affected_rows();
    }
}
