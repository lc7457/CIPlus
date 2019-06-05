<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Passport {
    /// 用户登录
    public function login(Request $request, Respond $respond) {
        $CI = &get_instance();
        $CI->load->model("user_model");
        $CI->load->helper('regexp');
        $passport = $request->params('passport');
        if (regexp('account', $passport)) {
            $uid = $CI->user_model->verify_account($passport, $request->params('password'));
        } elseif (regexp('email', $passport)) {
            $uid = $CI->user_model->verify_email($passport, $request->params('password'));
        } elseif (regexp('cn_phone', $passport)) {
            $uid = $CI->user_model->verify_phone($passport, $request->params('password'));
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

    /// 登录信息
    public function info(Request $request, Respond $respond) {
        $CI = &get_instance();
        $CI->load->library('token/jwt');
        $CI->load->model("user_model");
        $CI->load->model("role_model");
        $token = $request->params('token');
        $token = $CI->jwt->validator($token);
        $payload = $token['payload'];
        if ($token && $payload['exp'] > time()) {
            $info = $CI->user_model->getInfo($payload['id']);
            if ($info) {
                $info['roles'] = $CI->role_model->getRoles($payload['id']);
                $respond->setCode(20000)->setData($info);
            }
        } else {
            $respond->setCode(40099);
        }
        return $respond;
    }

    // 注销登录
    public function logout(Request $request, Respond $respond) {
        $respond->setCode(20000);
        return $respond;
    }
}