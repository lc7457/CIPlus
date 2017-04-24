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
        $this->Request(array('id'));
        $data = $this->content_model->row($this->RequestData());
        if (!empty($data)) {
            $this->SetCode(20000);
        }
        $this->Respond($data);
    }

    public function More() {
        $this->Request(array(), array('p', 'n'));
        echo $p = $this->optional['p'];
        //echo $n = $this->optional['n'];
    }

    protected function Verify_p($value) {
        $value = 1;
    }

}