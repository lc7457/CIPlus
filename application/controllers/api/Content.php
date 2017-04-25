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
        if ($data['type'] === 'rtf') {
            $data['content'] = $this->WithRtf();
        } elseif ($data['type'] === 'media') {
            $data['content'] = $this->WithMedia();
        }
        $this->Respond($data);
    }

    private function WithRtf() {
        $this->load->model('content_rtf_model');
        $whereArr = array('content_id' => $this->required['id']);
        return $this->content_rtf_model->order('sort asc')->result($whereArr);
    }

    private function WithMedia() {
        $this->load->model('content_media_model');
        $whereArr = array('content_id' => $this->required['id']);
        return $this->content_media_model->order('sort asc')->result($whereArr);
    }

    public function More() {
        $this->Request(array(), array('p', 'n', 'rc', 'order'));
        $whereArr = $this->FilterData(array('rc'));
        $whereArr['status'] = 1;
        $re = $this->content_model->order($this->optional['order'])->result(
            $whereArr,
            $this->optional['p'],
            $this->optional['n']
        );
        if (!empty($re)) {
            $this->SetCode(20000);
        }
        $this->Respond($re);
    }

    public function Add() {
        $this->Request(array('title'), array('author', 'abstract', 'keywords', 'link', 'cover', 'type'));
        $data = $this->RequestData();
        $data['createtime'] = time();
        $re = $this->content_model->insert($data);
        if ($re > 0) $this->SetCode(20000);
        $this->Respond();
    }

    public function Delete() {
        $this->Request(array('id'), array());
        $re = $this->content_model->freeze($this->RequestData());
        if ($re > 0) $this->SetCode(20000);
        $this->Respond();
    }

    public function Edit() {
        $this->Request(array('id'), array('title', 'author', 'abstract', 'keywords', 'link', 'cover', 'rc', 'sort', 'status'));
        $dataArr = $this->FilterData(array('id'), false);
        $whereArr = array('id' => $this->required['id']);
        $re = $this->content_model->update($dataArr, $whereArr);
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
            $value = 10;
        return $value;
    }

}