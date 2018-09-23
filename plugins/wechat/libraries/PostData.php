<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'plus/CIClass.php';

class PostData extends \CIPlus\CIClass {
    protected $wechat_open_token;
    protected $wechat_open_aes_key;
    protected $wechat_open_appid;
    protected $wechat_open_secret;
    
    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('wechat_open');
    }
    
    /**
     * 接收微信服务器推送的PostData数据
     */
    public function receive() {
        @$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        // extract post data
        if (!empty($postStr)) {
            $postStr = $this->decryptMsg($postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            return $postObj;
        } else {
            log_message('error', 'Not received wechat platform post message');
        }
        exit('success');
    }
    
    /**
     * 解密推送消息
     * ===================================================================
     * !!  需要安装PHP-mcrypt扩展
     *
     * @param $postStr
     * @return string
     */
    public function decryptMsg($postStr) {
        require_once FCPATH . 'sdk/WXMsgCrypt/wxBizMsgCrypt.php';
        
        $timeStamp = $this->CI->input->get('timestamp');
        $nonce = $this->CI->input->get('nonce');
        $msg_sign = $this->CI->input->get('msg_signature');
        
        $pc = new WXBizMsgCrypt($this->wechat_open_token, $this->wechat_open_aes_key, $this->wechat_open_appid);
        
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $postStr, $msg);
        if ($errCode !== 0) {
            log_message('error', $errCode);
        }
        return $msg;
    }
    
    public function encryptMsg($text) {
        $encryptMsg = '';
        $timeStamp = $this->CI->input->get('timestamp');
        $nonce = $this->CI->input->get('nonce');
//        $msg_sign = $this->CI->input->get('msg_signature');
        $pc = new WXBizMsgCrypt($this->wechat_open_token, $this->wechat_open_aes_key, $this->wechat_open_appid);
        $errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);
        if ($errCode !== 0) {
            log_message('error', $errCode);
        }
        return $encryptMsg;
    }
}