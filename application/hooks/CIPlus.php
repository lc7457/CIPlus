<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CIPlus 全局工具加载类
 * Class CIPlus
 */
class CIPlus {
    protected $CI;
    
    public function __construct() {
        // 必须定义在post_controller_constructor之后
        $this->CI =& get_instance();
    }
    
    public function autoload() {
        $this->CI->load->add_package_path(CIPLUS_PATH);
        // 全局加载工具类
        $this->CI->load->library('Command');
        $this->CI->load->library('Curl');
        // 全局加载辅助函数
        $this->CI->load->helper('plus');
        // 全局加载语言文件
//        $this->CI->lang->load();
    }
    
}