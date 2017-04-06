<?php
defined('BASEPATH') or exit ('No direct script access allowed');

/**
 * 收件人信息接口
 * Class Addressee
 */
class Addressee extends MY_Controller {
    private $name;
    private $tel;
    private $national_code;
    private $postal_code;
    private $province;
    private $city;
    private $area;
    private $address;

    private $id;
    private $hash;

    const LIMIT_ADDRESSEE_NUMBER = 10;

    public function __construct() {
        parent::__construct(array(
            'tokenVerifier' => true, // 是否验证token
            'strict' => false //是否开启严格模式，严格模式只能输出API信息
        ));
        $this->load->model('user_addressee_model');
        $this->Request();
    }

    /**
     * 接收数据
     * @param bool $validate
     */
    private function Request($validate = false) {
        $post = $this->_post();
        foreach ($post as $key => $val) {
            if (property_exists($this, $key)) { // php5.3+
                $this->$key = $val;
            }
        }
        if ($validate) {
            $this->Validate();
        }
    }

    /**
     * 主要数据
     * @return array
     */
    private function MainData() {
        return array(
            'name' => $this->name,
            'province' => $this->province,
            'city' => $this->city,
            'area' => $this->area,
            'address' => $this->address,
            'tel' => $this->tel
        );
    }

    /**
     * 附加数据
     * @return array
     */
    private function ExtraData() {
        return array(
            'national_code' => $this->national_code,
            'postal_code' => $this->postal_code
        );
    }

    /**
     * 收货地址hash
     * @return string
     */
    private function AddressHash() {
        return sha1(implode('+', $this->MainData()));
    }

    /**
     * 数据验证
     */
    private function Validate() {
        if (
            empty($this->name) &&
            empty($this->tel) &&
            empty($this->province) &&
            empty($this->city) &&
            empty($this->area) &&
            empty($this->address)
        ) {
            $this->SetCode(40001);
            $this->Respond();
        }
    }


    /**
     * 限制最多地址条数
     */
    private function Limit() {
        $whereArr['uid'] = $this->Uid();
        if ($this->user_addressee_model->count($whereArr) > self::LIMIT_ADDRESSEE_NUMBER) {
            $this->SetCode(40104, false);
            $string = sprintf($this->lang->line('m40104', FALSE), self::LIMIT_ADDRESSEE_NUMBER);
            $this->SetMessage($string);
            $this->Respond();
        }
    }

    private function ExistAddress() {
        $whereArr['hash'] = $this->AddressHash();
        $whereArr['uid'] = $this->Uid();
        if ($this->user_addressee_model->count($whereArr) > 0) {
            $this->SetCode(40002);
            $this->Respond();
        }
    }

    /**
     * 添加信息
     */
    public function Add() {
        $this->CheckUser();
        $this->Validate();
        $this->Limit();
        $whereArr['hash'] = $this->AddressHash();
        $whereArr['uid'] = $this->Uid();
        if (!$this->ExistAddress()) {
            $data = array_merge($this->MainData(), $whereArr, $this->ExtraData());
            $this->user_addressee_model->insert($data);
            $this->SetCode(20000);
        }
        $this->Respond();
    }

    /**
     * 设为默认收货地址
     */
    public function SetDefault() {
        $re = $this->user_addressee_model->update(array('is_default' => 1), array(
            'uid' => $this->Uid(),
            'hash' => $this->hash
        ));
        if ($re) {
            $this->user_addressee_model->update(array('is_default' => 0), array(
                'uid' => $this->Uid(),
                'hash !=' => $this->hash
            ));
            $this->SetCode(20000);
        } else {
            $this->SetCode(40000);
        }
        $this->Respond();
    }

    /**
     * 编辑收件人信息
     */
    public function Edit() {
        $this->CheckUser();
        $this->Validate();
        $whereArr['id'] = $this->id;
        $whereArr['uid'] = $this->Uid();
        $data = array_merge($this->MainData(), $this->ExtraData());
        $data['hash'] = $this->AddressHash();
        if (!$this->ExistAddress()) {
            $re = $this->user_addressee_model->update($data, $whereArr);
            if ($re) {
                $this->SetCode(20000);
            } else {
                $this->SetCode(40000);
            }
        }
        $this->Respond();
    }

    /**
     * 读取单条数据，不指定ID则读取默认收件人信息
     */
    public function Index() {
        $this->CheckUser();
        if (empty($this->id)) {
            $whereArr['is_default'] = 1;
        } else {
            $whereArr['id'] = $this->id;
        }
        $whereArr['uid'] = $this->Uid();
        $row = $this->user_addressee_model->row($whereArr);
        if (empty($row)) {
            $this->SetCode(40000);
        } else {
            $this->SetCode(20000);
            $this->SetData($row);
        }
        $this->Respond();
    }

    /**
     * 读取多条信息
     */
    public function More() {
        $this->CheckUser();
        $whereArr['uid'] = $this->Uid();
        $res = $row = $this->user_addressee_model->result($whereArr, 1, self::LIMIT_ADDRESSEE_NUMBER);
        if (empty($res)) {
            $this->SetCode(40000);
        } else {
            $this->SetCode(20000);
            $this->SetData($res);
        }
        $this->Respond();
    }
}


