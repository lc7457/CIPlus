<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.php';

/**
 * CIPlus 全局工具加载类
 * Class CIPlus
 */
class CIPlus extends \CIPlus\CIClass {

    public function __construct() {
        parent::__construct();
        $this->LoadConf('ciplus');
        // 全局加载工具类
        $this->CI->load->library('GlobalParams', null, 'gp');
        $this->CI->load->library('Curl');
        // 全局加载语言文件
//        $this->CI->lang->load();
    }

}