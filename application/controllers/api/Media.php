<?php defined('BASEPATH') or exit ('No direct script access allowed');

class Media extends MY_Controller {
    public function __construct() {
        parent::__construct(array(
            'checkToken' => false,
            'checkLogin' => false,
            'checkPower' => false
        ));
        $this->load->model('content_media_model');
    }

    public function Index() {
        $this->Request(array('id'));
        $re = $this->content_media_model->row($this->required);
        if (!empty($re)) {
            $this->SetCode(20000);
            $this->SetData($re);
        }
        $this->Respond();
    }

    public function More() {
        $this->Request(array('content_id'), array('p', 'n'));
        $whereArr['content_id'] = $this->required['content_id'];
        $data['total'] = $this->content_media_model->count($whereArr);
        $re = $this->content_media_model->result($whereArr, $this->optional['p'], $this->optional['n']);
        if (!empty($re)) {
            $this->SetCode(20000);
            $data['list'] = $re;
            $this->SetData($data);
        }
        $this->Respond();
    }

    public function Add() {
        $this->Request(array('src', 'type', 'content_id'), array('title', 'link'));
        $re = $this->content_media_model->insert($this->RequestData());
        if ($re > 0) {
            $this->SetCode(20000);
            $this->SetData(array('id' => $re));
        }
        $this->Respond();
    }

    protected function Verify_p(&$value) {
        if ($value <= 0)
            $value = 1;
        return $value;
    }

    protected function Verify_n(&$value) {
        if ($value <= 0)
            $value = 100;
        return $value;
    }
}