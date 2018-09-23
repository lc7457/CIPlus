<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'plus/CIClass.php';

class JsSdk extends \CIPlus\CIClass {
    const GET_JS_TICKET_URL = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi';
    // Json 本地存储名称
    const JSON_JS_TICKET = 'wechat_js_ticket__'; // json 本地存储的ticket文件名称
    
    public function __construct(array $config = array()) {
        parent::__construct();
        $this->CI->load->library('JsonStorage');
        $this->loadConf('wechat_open');
    }
    
    public function signature($appid, $timestamp, $nonceStr, $url) {
        $string = "jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s";
        $jsapi_ticket = $this->getJsTicket($appid);
        $signature = sprintf($string, $jsapi_ticket, $nonceStr, $timestamp, $url);
        return sha1($signature);
    }
    
    /**
     * 获取凭证
     * @param $appid
     * @return mixed
     */
    public function getJsTicket($appid) {
        $ticket = $this->loadJsTicket($appid);
        if ($ticket['end_time'] < time()) {
            $ticket = $this->saveJsTicket($appid);
        }
        return $ticket['ticket'];
    }
    
    /**
     * 远程请求并缓存凭证
     * @param $appid
     * @return mixed
     */
    private function saveJsTicket($appid) {
        $this->CI->load->library('OpenLicensing');
        $token = $this->CI->openlicensing->getMpAccessToken($appid);
        $url = sprintf(self::GET_JS_TICKET_URL, $token);
        $ticket = $this->CI->curl->simple_get($url);
        $ticket = json_decode($ticket, true);
        if ($ticket['errcode'] === 0) {
            $ticket['end_time'] = $ticket['expires_in'] + time() - 300;
            $this->CI->jsonstorage->save(self::JSON_JS_TICKET . $appid, json_encode($ticket));
        }
        return $ticket;
    }
    
    /**
     * 读取缓存凭证
     * @param $appid
     * @param bool $toArray
     * @return bool|mixed|string
     */
    private function loadJsTicket($appid, $toArray = true) {
        try {
            return $this->CI->jsonstorage->load(self::JSON_JS_TICKET . $appid, $toArray);
        } catch (Exception $e) {
            log_message('error', $e);
        }
        return false;
    }
}