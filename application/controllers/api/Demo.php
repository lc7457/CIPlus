<?php

class Demo extends MY_Controller {
    public function __construct() {
        parent::__construct(array(
            'strict' => false
        ));
    }

    public function Index() {
        $this->Respond(2000, 5000, array(1, 2));
    }
}