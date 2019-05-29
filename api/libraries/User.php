<?php defined('BASEPATH') OR exit('No direct script access allowed');


class User {
    public function login(Request $request, Respond $respond) {
        $CI = &get_instance();
        $CI->load->model("user_model");
        $CI->load->helper('regexp');
        if (regexp('account', $request->params('account'))) {
            $uid = $CI->user_model->verify_account($request->params('account'), $request->params('password'));
        } elseif (regexp('email', $request->params('email'))) {
            $uid = $CI->user_model->verify_email($request->params('email'), $request->params('password'));
        } elseif (regexp('cn_phone', $request->params('phone'))) {
            $uid = $CI->user_model->verify_phone($request->params('phone'), $request->params('password'));
        } else {
            $uid = null;
        }
        if ($uid) {
            $respond->setCode(20000);
            $CI->load->library('token/jwt', ['header' => $request->params('header')]);
//            $token = $this->generator->initPayload($uid)->generate();
//            $respond->setData(['token' => $token]);
        }
//        $respond->output();
    }
}