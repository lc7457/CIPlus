<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Role {
    public function users(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("role_user_model");
        $num = $request->params('n') > 0 ? $request->params('n') : 10;
        $p = $request->params('p') > 0 ? $request->params('p') : 1;
        $offset = $num * ($p - 1);
        $total = $CI->role_user_model->users_total($request->params('key'), $request->params('value'));
        $data = array(
            'total' => $total,
            'users' => array()
        );
        if ($total > 0) {
            $res = $CI->role_user_model->users($num, $offset, $request->params('key'), $request->params('value'));
            $data['users'] = $res;
            $respond->setCode(20000);
        }
        $respond->setData($data);
        return $respond;
    }
}