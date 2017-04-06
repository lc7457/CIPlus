<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once FCPATH . "classes/WXMsgCrypt/wxBizMsgCrypt.php";

class Wechat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('WechatThirdPlatform', NULL, 'wxtp');
    }

    public function index() {
        $this->Notify();
    }

    /**
     * ===================================================================
     * 公众号授权模块
     * ===================================================================
     */

    /**
     * 引导用户打开授权页
     */
    public function AuthCheck() {
        $componentAccessToken = $this->wxtp->GetComponentAccessToken();
        $preCode = $this->wxtp->GetPreCode($componentAccessToken);
        $preCode = json_decode($preCode, true);
        $this->wxtp->AuthCheck($preCode['pre_auth_code'], 'http:' . base_url('serv/wechat/AuthAccess'));
    }

    /**
     * 获取公众号授权信息
     */
    public function AuthAccess() {
        $authCode = $this->input->get('auth_code');
        $componentAccessToken = $this->wxtp->GetComponentAccessToken();
        $authAccessToken = $this->wxtp->GetAuthAccess($componentAccessToken, $authCode);
        $authAccessToken = json_decode($authAccessToken, true);
        if (array_key_exists('errcode', $authAccessToken)) {
            log_message('error', json_encode($authAccessToken));
            echo "failed";
        } else {
            $authorizerAppid = '';
            $this->SaveAuthAccess($authAccessToken, $authorizerAppid);
            $this->AuthInfo($authorizerAppid);
            echo "success";
        }
    }

    /**
     * 保存公众号授权信息
     * @param $authAccessToken
     * @param null $AuthorizerAppid
     * @return mixed
     */
    private function SaveAuthAccess($authAccessToken, &$authorizerAppid = null) {
        $this->load->model('wxtp_authorization_info_model');
        $authAccessToken = $authAccessToken['authorization_info'];
        $dataArr = array(
            'authorizer_appid' => $authAccessToken['authorizer_appid'],
            'authorizer_access_token' => $authAccessToken['authorizer_access_token'],
            'authorizer_refresh_token' => $authAccessToken['authorizer_refresh_token'],
            'authorizer_last_time' => time() + $authAccessToken['expires_in'] - 200,
        );
        foreach ($authAccessToken['func_info'] as $k => $v) {
            $dataArr['a' . $v['funcscope_category']['id']] = 1;
        }
        $whereArr = array(
            'authorizer_appid' => $authAccessToken['authorizer_appid']
        );
        if ($authorizerAppid !== null) {
            $authorizerAppid = $authAccessToken['authorizer_appid'];
        }
        $n = $this->wxtp_authorization_info_model->count($whereArr);
        if ($n > 0) {
            return $this->wxtp_authorization_info_model->update($dataArr, $whereArr);
        } else {
            $dataArr['authorizer_create_time'] = time();
            return $this->wxtp_authorization_info_model->insert($dataArr);
        }

    }

    public function AuthInfo($authorizerAppid) {
        $componentAccessToken = $this->wxtp->GetComponentAccessToken();
        $authorizerInfo = $this->wxtp->GetAuthorizerInfo($componentAccessToken, $authorizerAppid);
        $authorizerInfo = json_decode($authorizerInfo, true);
        if (array_key_exists('errcode', $authorizerInfo)) {
            log_message('error', json_encode($authorizerInfo));
        } else {
            $this->SaveAuthInfo($authorizerInfo);
        }
    }

    private function SaveAuthInfo($authorizerInfo) {
        $this->load->model('wxtp_authorizer_info_model');
        $whereArr = array(
            'authorizer_appid' => $authorizerInfo['authorization_info']['authorizer_appid']
        );
        $dataArr = array(
            'authorizer_appid' => $authorizerInfo['authorization_info']['authorizer_appid'],
            'nick_name' => $authorizerInfo['authorizer_info']['nick_name'],
            'head_img' => $authorizerInfo['authorizer_info']['head_img'],
            'service_type_info' => $authorizerInfo['authorizer_info']['service_type_info']['id'],
            'verify_type_info' => $authorizerInfo['authorizer_info']['verify_type_info']['id'],
            'user_name' => $authorizerInfo['authorizer_info']['user_name'],
            'alias' => $authorizerInfo['authorizer_info']['alias'],
            'qrcode_url' => $authorizerInfo['authorizer_info']['qrcode_url'],
            'business_info' => json_encode($authorizerInfo['authorizer_info']['business_info']),
            'idc' => $authorizerInfo['authorizer_info']['idc'],
            'principal_name' => $authorizerInfo['authorizer_info']['principal_name'],
            'open_pay' => $authorizerInfo['authorizer_info']['business_info']['open_pay'],
            'open_shake' => $authorizerInfo['authorizer_info']['business_info']['open_shake'],
            'open_scan' => $authorizerInfo['authorizer_info']['business_info']['open_scan'],
            'open_card' => $authorizerInfo['authorizer_info']['business_info']['open_card'],
            'open_store' => $authorizerInfo['authorizer_info']['business_info']['open_store'],
        );
        $n = $this->wxtp_authorizer_info_model->count($whereArr);
        if ($n > 0) {
            return $this->wxtp_authorizer_info_model->update($dataArr, $whereArr);
        } else {
            return $this->wxtp_authorizer_info_model->insert($dataArr);
        }
    }

    /**
     * 接收平台的心跳验证及消息通知
     */
    public function Notify() {
        @$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        // extract post data
        if (!empty($postStr)) {
            $postStr = $this->wxtp->DecryptMsg($postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->ParseMsg($postObj);
        } else {
            log_message('error', 'Not received wechat platform post message');
        }
        exit('success');
    }

    /**
     * 解析消息数据
     * @param $postObj
     */
    private function ParseMsg($postObj) {
        switch ($postObj->InfoType) {
            case 'component_verify_ticket':
                $this->wxtp->SaveVerifyTickey($postObj);
                break;
            default:
                return;
        }
    }

}