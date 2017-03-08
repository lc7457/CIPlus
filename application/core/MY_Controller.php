<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/REST_Controller.php';

class MY_Controller extends REST_Controller {
    protected $code = 20000;
    protected $message = 'success';
    protected $data = array();

    public function __construct() {
        parent::__construct();
    }

    protected function SetCode($code, $inDict = true) {
        $this->code = $code;
    }

    protected function SetMessage($message, $inDict = true) {
        $this->message = $message;
    }

    protected function SetData(array $data = array()) {
        $this->data = $data;
    }

    protected function DoResponse($status = NULL) {
        $callback = array(
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data
        );
        $this->set_response($callback, $status);
    }


}