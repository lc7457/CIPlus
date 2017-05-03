<?php defined('BASEPATH') or exit ('No direct script access allowed');

/**
 * 数据库CRUD操作扩展(models)
 * ============================================================
 * 简化数据库CRUD基本功能操作
 * insert : 插入单条数据（返回执行数据插入时的ID）
 * update : 更新数据（返回成功更新的数据行数）
 * delete : 删除数据（返回被删除的数据行数）
 * row : 查询单条数据（返回结果集数组）
 * result : 查询多条数据（返回结果集二维数组）
 * count : 查询数据条数（返回查询数据行数）
 *
 * @author LeeNux
 * @version 1.0
 */
abstract class MY_Model extends CI_Model {
    // 数据表名称
    protected $table;

    /**
     * 构造函数
     * @param $table :数据表
     */
    public function __construct($table = '', $database = 'default') {
        parent::__construct();
        $this->db = $this->load->database($database, TRUE);
        $this->table = $table;
    }

    /**
     * 查询字段
     * @param string $selected
     * @return $this
     */
    public function select($selected = '*') {
        $this->db->select($selected);
        return $this;
    }

    public function order($by = '') {
        if (!empty($by)) {
            $by = str_replace("@", " ", $by);
            $this->db->order_by($by);
        }
        return $this;
    }

    public function like($column, $string = '', $type = 'both') {
        if (is_array($column)) {
            $this->db->like($column);
        } else {
            $this->db->like($column, $string, $type);
        }
        return $this;
    }

    /**
     * 添加数据
     * @param $dataArr :(array)插入的数据
     * return:当前插入的数据id
     */
    public function insert($dataArr = array()) {
        $this->db->insert($this->table, $dataArr);
        return $this->db->insert_id();
    }

    /**
     * 修改数据
     * @param $dataArr :(array)更新的数据
     * @param $whereArr :(array)更新的条件
     * return:更新的数据条数
     */
    public function update($dataArr, $whereArr) {
        $this->db->where($whereArr);
        $this->db->update($this->table, $dataArr);
        return $this->db->affected_rows();
    }

    /**
     * 删除数据
     * @param $whereArr :(array)删除的条件
     * return:删除的数据条数
     */
    public function delete($whereArr) {
        $this->db->where($whereArr);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }

    /**
     * 查询并返回一条数据
     * @param $whereArr :(array)查询的条件
     * return:查询结果
     */
    public function row($whereArr) {
        $this->db->where($whereArr);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    /**
     * 查询并返回多条数据
     * @param $whereArr :(array)查询的条件
     * @param $num :单页显示的条数
     * @param $page :当前页数
     * @param $orderby :排序条件
     * return:查询结果
     */
    public function result($whereArr, $page = 1, $num = 10) {
        if ($page <= 0) $page = 1;
        $offset = ($page - 1) * $num;
        $this->db->where($whereArr);
        $query = $this->db->get($this->table, $num, $offset);
        return $query->result_array();
    }

    /**
     * 查询数据条数
     * @param $whereArr
     * @return mixed
     */
    public function count($whereArr) {
        $this->db->where($whereArr);
        $query = $this->db->get($this->table);
        return $query->num_rows();
    }

}
