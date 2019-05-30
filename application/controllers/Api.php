<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->add_package_path(FCPATH . 'api' . DIRECTORY_SEPARATOR);
        $this->load->library('restful/request');
        $this->load->library('restful/respond');
    }

    public function _remap($method, $params = array()) {
        if (count($params) > 0) {
            array_unshift($params, $method); // 将CI的method合并进参数数组
            $method = array_pop($params); // 取最后一个参数作为方法名
            $lib_path = implode('/', $params); // 将参数构造位api类路径
            $this->validator($lib_path, $method);
            $this->load->library($lib_path, null, 'api');
            if (is_callable(array($this->api, $method))) {
                $this->api->$method($this->request, $this->respond);
            }
        } else {
            show_404();
        }
    }

    /**
     * 接口验证器
     * @param $lib_path
     * @param $method
     * @return mixed
     */
    private function validator($lib_path, $method) {
        $this->load->model('api_model');
        $api_path = $lib_path . '/' . $method;
        $api = $this->api_model->by_path($api_path);
        if ($api && $api['validated']) $this->verifyToken();
        return $this->verifyRequest($api);
    }

    private function verifyToken() {
//        $this->load->library('token/validator');
    }

    private function verifyRole() {

    }

    private function verifyRequest($api) {
        if (!$api) show_404();
        $req = json_decode($api['required'], true);
        $opt = json_decode($api['optional'], true);
        $v = $this->request->init($req, $opt, $api['method']);
        if (!$v) {
            $this->respond->invalidRequest();
        }
    }

}