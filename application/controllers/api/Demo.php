<?php

class Demo extends MY_Controller {
    public function __construct() {
        parent::__construct(array(
            'checkToken' => false, // ÊÇ·ñÑéÖ¤token
            'checkLogin' => false,
            'checkPower' => false
        ));
    }

    public function Index() {
        $this->Request(array(), array('name', 'age'));
        $arr = $this->RequestData();
        print_r($arr);
    }
    public function Test() {
    }
}