<?php defined('BASEPATH') or exit ('No direct script access allowed');

/**
 * 收件人信息接口
 * Class Addressee
 */
class Addressee extends MY_Controller {

    protected $id;
    protected $hash;

    const LIMIT_ADDRESSEE_NUMBER = 10;

    public function __construct() {
        parent::__construct(array(
            'checkToken' => true, // 是否验证token
            'checkLogin' => true,
            'checkPower' => false
        ));
        $this->load->model('user_addressee_model');
    }

    /**
     * 读取单条数据，不指定ID则读取默认收件人信息
     */
    public function Index() {
        $this->Request(array(), array('id'));
        if (empty($this->optional['id'])) {
            $whereArr['is_default'] = 1;
        } else {
            $whereArr['id'] = $this->optional['id'];
        }
        $whereArr['uid'] = $this->uid;
        $row = $this->user_addressee_model->row($whereArr);
        if (!empty($row)) {
            $this->SetCode(20000);
            $this->SetData($row);
        }
        $this->Respond();
    }

    /**
     * 读取多条信息
     */
    public function More() {
        $whereArr['uid'] = $this->uid;
        $res = $row = $this->user_addressee_model->result($whereArr, 1, self::LIMIT_ADDRESSEE_NUMBER);
        if (!empty($res))
            $this->SetCode(20000);
        $this->SetData($res);
        $this->Respond();
    }

    /**
     * 添加信息
     */
    public function Add() {
        $this->Limit();
        $this->Request(
            array('name', 'tel', 'province', 'city', 'area', 'address'),
            array('national_code', 'postal_code')
        );
        $whereArr['hash'] = $this->AddressHash();
        $whereArr['uid'] = $this->uid;
        if (!$this->ExistAddress()) {
            $data = array_merge($this->RequestData(), $whereArr);
            $this->user_addressee_model->insert($data);
            $this->SetCode(20000);
        }
        $this->Respond();
    }

    /**
     * 编辑收件人信息+
     */
    public function Edit() {
        $this->Request(
            array('id', 'name', 'tel', 'province', 'city', 'area', 'address'),
            array('national_code', 'postal_code')
        );
        $whereArr['id'] = $this->required['id'];
        $whereArr['uid'] = $this->uid;
        $data = $this->FilterData(array('id'), false);
        $data['hash'] = $this->AddressHash();
        if (!$this->ExistAddress()) {
            $re = $this->user_addressee_model->update($data, $whereArr);
            if ($re) {
                $this->SetCode(20000);
            }
        }
        $this->Respond();
    }

    /**
     * 收货地址hash
     * @return string
     */
    private function AddressHash() {
        return sha1(implode('+', $this->required));
    }

    /**
     * 限制最多地址条数
     */
    private function Limit() {
        $whereArr['uid'] = $this->uid;
        if ($this->user_addressee_model->count($whereArr) > self::LIMIT_ADDRESSEE_NUMBER) {
            $this->SetCode(40104, false);
            $string = sprintf($this->lang->line('m40104', FALSE), self::LIMIT_ADDRESSEE_NUMBER);
            $this->SetMessage($string);
            $this->Respond();
        }
    }

    /**
     * 验证地址是否存在
     */
    private function ExistAddress() {
        $whereArr['hash'] = $this->AddressHash();
        $whereArr['uid'] = $this->uid;
        if ($this->user_addressee_model->count($whereArr) > 0) {
            $this->Respond(40002);
        }
        return false;
    }


    /**
     * 设为默认收货地址
     */
    public function SetDefault() {
        $this->Request(array('id'));
        $whereArr['uid'] = $this->uid;
        $this->user_addressee_model->update(array('is_default' => 0), $whereArr);
        $whereArr['id'] = $this->required['id'];
        $re = $this->user_addressee_model->update(array('is_default' => 1), $whereArr);
        if ($re) {
            $this->SetCode(20000);
        }
        $this->Respond();
    }

}


