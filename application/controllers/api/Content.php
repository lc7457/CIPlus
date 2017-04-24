<?php defined('BASEPATH') or exit ('No direct script access allowed');

class Content extends MY_Controller {

    public function __construct() {
        parent::__construct(array(
            'checkToken' => false,
            'checkLogin' => false,
            'checkPower' => false
        ));
        $this->load->model('content_model');
    }

    public function Index() {
        $this->Request(array('id'), array('author'));
        $data = $this->content_model->row($this->required);
        if (!empty($data)) {
            $this->SetCode(20000);
        }
        $this->Respond($data);
    }

    public function More() {
        $this->Request(array());
    }

}