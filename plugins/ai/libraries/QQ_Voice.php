<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once PLUGINS_PATH . 'ai' . DIRECTORY_SEPARATOR . '/QQ.php';

Class QQ_Voice extends QQ {
    
    public function __construct(array $config = array()) {
        parent::__construct();
    }
    
}