<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once CIPLUS_PATH . 'CIClass.php';

/**
 * CIPlus 全局控制类
 * Class CIPlus
 */
class CIPlus extends \CIPlus\CIClass {

    public $gp;

    public function __construct() {
        parent::__construct();
        $this->CI->load->library('Session');
        $this->HandleGlobalParams();
    }

    private function HandleGlobalParams() {
        $this->CI->load->library('GlobalParams');
        $this->gp =& $this->CI->globalparams;
    }
}