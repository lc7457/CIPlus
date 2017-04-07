<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
    private $power = array(
        'open' => array(),
        'user' => array(),
        'admin' => array('more')
    );

    public function __construct() {
        parent::__construct(array(
            'tokenVerifier' => false,
            'strict' => true
        ));
    }

    public function __call($method = 'Index', $params) {
        $this->$method($params);
    }

    public function Index() {

    }

    public function More() {

    }

    public function Add() {

    }

    public function Edit() {

    }

    public function Delete() {

    }

}