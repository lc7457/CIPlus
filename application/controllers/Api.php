<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->add_package_path(FCPATH . 'api' . DIRECTORY_SEPARATOR);
    }

    public function _remap($method, $params = array()) {
        if (count($params) > 0) {
            array_unshift($params, $method); // 将CI的method合并进参数数组
            $method = array_pop($params); // 取最后一个参数作为方法名
            $lib_path = implode('/', $params); // 将参数构造位api类路径
            $this->verify($lib_path, $method);
            $this->load->library($lib_path, null, 'api');
            if (is_callable(array($this->api, $method))) {
                $this->load->library('restful/request');
                $this->load->library('restful/respond');
                $this->api->$method($this->request, $this->respond);
            }
        } else {
            show_404();
        }
    }

    private function verify($lib_path, $method) {
        $api_path = $lib_path . '/' . $method;

    }

}