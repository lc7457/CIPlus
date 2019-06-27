<?php


class Profile {
    public function edit_info(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model('user_model');
        $re = $CI->user_model->setInfo($request->payload('id'), array(
                'name' => $request->params('name'),
                'sex' => $request->params('sex'),
                'avatar' => $request->params('avatar'),
                'area' => $request->params('area'),
                'city' => $request->params('city'),
                'province' => $request->params('province'),
                'country' => $request->params('country'),
                'introduction' => $request->params('introduction')
            )
        );
        if ($re) {
            $respond->setCode(20000)->setData($re);
        }
        return $respond;
    }

    public function change_password(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model('user_model');
        if (!empty($request->params('new_password')) && $request->params('new_password') === $request->params('re_password')) {
            $re = $CI->user_model->changePassword($request->payload('id'), $request->params('old_password'), $request->params('new_password'));
            if ($re) {
                $respond->setCode(20000)->setData($re);
            }
        }
        return $respond;
    }

    public function change_email() {
    }

    public function change_phone() {
    }
}