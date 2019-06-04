<?php defined('BASEPATH') OR exit('No direct script access allowed');

class api_model extends MY_Model {
    const TB_API = CIPLUS_DB_PREFIX . 'api';


    public function __construct() {
        parent::__construct();
    }

    /**
     * 通过路径搜索接口
     * @param $path
     * @return mixed
     */
    public function by_path($path) {
        $this->db->where('usable', 1);
        return $this->row(self::TB_API, ['path' => $path]);
    }

    /**
     * 添加接口
     * @param $title
     * @param $path
     * @param array $required
     * @param array $optional
     * @param string $method
     * @param int $validated
     * @return mixed
     */
    public function add($title, $path, $required = array(), $optional = array(), $method = 'request', $validated = 1) {
        $data = array(
            'key' => unique_code(),
            'title' => $title,
            'path' => $path,
            'required' => $required,
            'optional' => $optional,
            'method' => $method,
            'validated' => $validated
        );
        return $this->insert(self::TB_API, $data);
    }

    public function total($title = null) {
        if ($title) $this->db->like('title', $title);
        return $this->count(self::TB_API);
    }

    public function more($title = null, $p = 1, $n = 10) {
        if ($title) $this->db->like('title', $title);
        $this->db->order_by('id desc');
        return $this->result(self::TB_API, $p, $n);
    }

    public function del($id) {
        $data = array("usable" => 0);
        $where = array("id" => $id);
        return $this->update(self::TB_API, $data, $where);
    }

    public function revive($id) {
        $data = array("usable" => 1);
        $where = array("id" => $id);
        return $this->update(self::TB_API, $data, $where);
    }
}