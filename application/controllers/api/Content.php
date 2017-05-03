<?php defined('BASEPATH') or exit ('No direct script access allowed');

class Content extends MY_Controller {

    const TYPE_RTF = array('rtf');
    const TYPE_MEDIA = array('image', 'video', 'audio', 'mixed');

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
        if (in_array($data['type'], self::TYPE_RTF)) {
            $data['content'] = $this->WithRtf();
        } elseif (in_array($data['type'], self::TYPE_MEDIA)) {
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
        $this->Request(array(), array('title', 'keywords', 'p', 'n', 'rc', 'order'));
        $whereArr = $this->FilterData(array('rc'));
        $whereArr['status'] = 1;
        $data['total'] = $this->content_model
            ->like($this->FilterData(array('title', 'keywords')))
            ->count($whereArr);
        $re = $this->content_model
            ->like($this->FilterData(array('title', 'keywords')))
            ->order($this->optional['order'])
            ->result(
                $whereArr,
                $this->optional['p'],
                $this->optional['n']
            );
        if (!empty($re)) {
            $this->SetCode(20000);
            $data['list'] = $re;
        }
        $this->Respond($data);
    }

    public function Add() {
        $this->Request(array('title', 'type'), array('author', 'abstract', 'keywords', 'src', 'cover'), 'post');
        $data = $this->RequestData();
        $data['createtime'] = time();
        $re = $this->content_model->insert($data);
        if ($re > 0) {
            $this->SetCode(20000);
            $this->SetData(array('id' => $re));
        }
        $this->Respond();
    }

    public function Delete() {
        $this->Request(array('id'), array());
        $re = $this->content_model->freeze($this->required['id']);
        if ($re > 0) $this->SetCode(20000);
        $this->Respond();
    }

    public function Edit() {
        $this->Request(array('id'), array('title', 'author', 'abstract', 'keywords', 'src', 'cover', 'rc', 'sort', 'status'), 'post');
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