<?php
defined('BASEPATH') OR exit('No direct script access allowed');


require_once APPPATH . 'core/REST_Controller.php';

class MY_Controller extends REST_Controller {
    protected $code = 20000;
    protected $message = 'success';
    protected $data = array();

    public function __construct($validToken = true) {
        parent::__construct();
        $this->lang->load('api_message');
        if ($validToken) {
            $this->ValidToken();
        }
    }

    public function ValidToken() {
        $this->load->library('oauth');
        $accessToken = $this->input->post_get('access_token');
        if (!$this->oauth->ValidToken($accessToken)) {
            $this->SetCode(40098);
            $this->DoResponse();
            ob_end_clean();
        }
    }

    protected function SetCode($code, $inDict = true) {
        $this->code = $code;
        if ($inDict) {
            $this->message = $this->lang->line('m' . $code, FALSE);
        }
    }

    protected function SetMessage($message) {
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
        $this->output->_display();
        exit();
    }

}