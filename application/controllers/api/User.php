<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
    const POWER_NONE = array();
    const POWER_USER = array('index', 'edit'); // 必须小写
    const POWER_ADMIN = array('index', 'more', 'edit', 'delete');

    protected $nickname;
    protected $avatar;
    protected $sex;
    protected $language;
    protected $city;
    protected $province;
    protected $country;

    public function __construct() {
        parent::__construct(array(
            'tokenVerifier' => true,
            'strict' => true,
            'checkLogin' => true,
            'checkPower' => true
        ));
        $this->load->model(array(
            'user_model', 'user_info_model'
        ));
        $this->Request();
    }

    public function _remap($method = 'Index', $params = array()) {
        $this->$method($params);
    }

    public function Index() {
        $whereArr = array('uid' => $this->uid);
        $re = $this->user_info_model->row($whereArr);
        $this->SetCode(20000);
        $this->SetData($re);
        $this->Respond();
    }

    public function More() {
        $p = $this->_get('p');
        $n = $this->_get('n') OR 10;
        $p = $p > 1 ? $p : 1;
        $re = $this->user_info_model->result(array(), $p, $n);
        $this->SetCode(20000);
        $this->SetData($re);
        $this->Respond();
    }

    public function Edit() {
        $whereArr = array('uid' => $this->uid);
        $re = $this->user_info_model->update($this->ExtraData(), $whereArr);
        $this->SetCode(20000);
        $this->Respond();
    }

    public function Delete() {

    }

    private function ExtraData() {
        $data = array();
        if (!empty($this->nickname)) {
            $data['nickname'] = $this->nickname;
        }
        if (!empty($this->avatar)) {
            $data['avatar'] = $this->avatar;
        }
        if (!empty($this->sex)) {
            $data['sex'] = $this->sex;
        }
        if (!empty($this->language)) {
            $data['language'] = $this->language;
        }
        if (!empty($this->city)) {
            $data['city'] = $this->city;
        }
        if (!empty($this->province)) {
            $data['province'] = $this->province;
        }
        if (!empty($this->country)) {
            $data['country'] = $this->country;
        }
        return $data;
    }

}