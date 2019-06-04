<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Setting {
    // 添加接口
    public function api_add(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $CI->api_model->add(
            $request->params('title'),
            $request->params('path'),
            $request->params('required'),
            $request->params('optional'),
            $request->params('method'),
            $request->params('validated')
        );
        return $respond;
    }

    // 接口列表
    public function api_more(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $total = $CI->api_model->total($request->params('title'));
        $data = array(
            'total' => $total,
            'list' => array()
        );
        if ($total > 0) {
            $res = $CI->api_model->more($request->params('title'), $request->params('p'), $request->params('n'));
            $data['list'] = $res;
            $respond->setCode(20000)->setData($data);
        }
        return $respond;
    }

    public function api_edit(Request $request, Respond $respond) {
    }

    public function api_del(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $res = $CI->api_model->del($request->params('id'));
        if ($res > 0) {
            $respond->setCode(20000)->setData($res);
        }
        return $respond;
    }

    public function api_revive(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $res = $CI->api_model->revive($request->params('id'));
        if ($res > 0) {
            $respond->setCode(20000)->setData($res);
        }
        return $respond;
    }
}