<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Resource {
    public $code = 40000;

    // 全部接口
    public function upload(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->config->load('uploader', true, true);
        $config = $CI->config->item('uploader');
        $CI->load->library("uploader", $config);
        $CI->uploader->set_callback($this, '_error');
        $bool = $CI->uploader->file($_FILES, $request->params('type'));
        if ($bool) {
            $respond->setCode(20000);
            $respond->setData(['url' => $CI->uploader->url]);
        } else {
            $respond->setCode($this->code);
        }
        return $respond;
    }

    // 接口列表
    public function download(Request $request, Respond $respond) {
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

    public function _error($type) {
        switch ($type) {
            case 'illegalNumber':
                $this->code = 41601;
                break;
            case 'illegalFile':
                $this->code = 41701;
                break;
            case 'illegalExt':
                $this->code = 41501;
                break;
            case 'illegalSize':
                $this->code = 41301;
                break;
            case 'nonexistentPath':
                $this->code = 41201;
                break;
            case 'notWritablePath':
                $this->code = 40101;
                break;
            default:
                $this->code = 40000;
        }
    }
}