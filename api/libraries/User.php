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
            $CI->load->library('token/jwt');

            $header = json_decode($request->params('header'), true);
            $header = $header ? $header : $CI->jwt->initHeader();

            $payload = array('id' => $uid);

            $token = $CI->jwt->generator($header, $payload);
            $respond->setData(array('token' => $token));
        }
        return $respond;
    }

    public function info(Request $request, Respond $respond) {
        $CI = &get_instance();
        $CI->load->library('token/jwt');
        $CI->load->model("user_model");
        $token = $request->params('token');
        $token = $CI->jwt->validator($token);
        if ($token) {
            $payload = $token['payload'];
            $info = $CI->user_model->getInfo($payload['id']);
            if ($info) {
                $respond->setCode(20000)->setData($info);
            }
        }
        return $respond;
    }
}