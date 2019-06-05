<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Setting {
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

    // 添加接口
    public function api_add(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $id = $CI->api_model->add(
            $request->params('title'),
            $request->params('path'),
            $request->params('required'),
            $request->params('optional'),
            $request->params('method'),
            $request->params('validated')
        );
        if ($id > 0) {
            $respond->setCode(20000)->setData(
                array('id' => $id)
            );
        }
        return $respond;
    }

    // 编辑接口
    public function api_edit(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $id = $CI->api_model->edit(
            $request->params('id'),
            $request->params('title'),
            $request->params('path'),
            $request->params('required'),
            $request->params('optional'),
            $request->params('method'),
            $request->params('validated')
        );
        if ($id > 0) {
            $respond->setCode(20000)->setData(
                array('id' => $id)
            );
        }
        return $respond;
    }

    // 删除接口
    public function api_del(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $res = $CI->api_model->del($request->params('id'));
        if ($res > 0) {
            $respond->setCode(20000)->setData($res);
        }
        return $respond;
    }

    // 恢复接口
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