<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once CIPLUS_PATH . 'CIClass.php';

/**
 * CIPlus 全局控制类
 * Class CIPlus
 */
class CIPlus extends \CIPlus\CIClass {

    public function __construct() {
        parent::__construct();
        $this->PreLoad();
    }

    // 预加载
    private function PreLoad() {
        $this->CI->load->library('Session');
        $this->CI->load->library('GlobalParams', null, 'gp');
    }

    // 初始化
    private function Init() {
    }



}