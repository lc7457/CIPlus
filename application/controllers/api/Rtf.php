<?php defined('BASEPATH') or exit ('No direct script access allowed');

class Rtf extends MY_Controller {
    public function __construct() {
        parent::__construct(array(
            'checkToken' => false,
            'checkLogin' => false,
            'checkPower' => false
        ));
        $this->load->model('content_rtf_model');
    }

    public function Index() {
    }

    public function More() {
        $this->Request(array('content_id'), array('p', 'n'));
        $whereArr['content_id'] = $this->required['content_id'];
        $data['total'] = $this->content_rtf_model->count($whereArr);
        $re = $this->content_rtf_model->result($whereArr, $this->optional['p'], $this->optional['n']);
        if (!empty($re)) {
            $this->SetCode(20000);
            $data['list'] = $re;
            $this->SetData($data);
        }
        $this->Respond();
    }

    public function Add() {
        $this->Request(array('content', 'content_id'), array('title', 'src'), 'post');
        $re = $this->content_rtf_model->insert($this->RequestData());
        if ($re > 0) {
            $this->SetCode(20000);
            $this->SetData(array('id' => $re));
        }
        $this->Respond();
    }

    public function Delete() {
    }

    public function Edit() {
        $this->Request(array('id'), array('title', 'content', 'src', 'sort'), 'post');
        $dataArr = $this->FilterData(array('id'), false);
        $whereArr = array('id' => $this->required['id']);
        $re = $this->content_rtf_model->update($dataArr, $whereArr);
        if ($re > 0) $this->SetCode(20000);
        $this->Respond();
    }

    protected function Verify_p(&$value) {
        if ($value <= 0)
            $value = 1;
        return $value;
    }

    protected function Verify_n(&$value) {
        if ($value <= 0)
            $value = 1;
        return $value;
    }
}