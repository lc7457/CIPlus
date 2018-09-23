<?php
require_once FCPATH . 'plus/CIClass.php';

class ParseMsg extends \CIPlus\CIClass {
    
    public $appid;
    
    public $MsgType;
    public $FromUserName;
    public $ToUserName;
    public $Content;
    public $Event;
    
    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('wechat_open');
        $this->CI->load->library('PostData');
        $this->CI->lang->load('message');
    }
    
    /**
     * 解析消息
     * @param $postObj
     * @param $appid
     */
    public function parse($postObj, $appid) {
        foreach ($postObj as $k => $v) {
            $this->$k = $v;
        }
        $this->appid = $appid;
        $type = $this->MsgType;
        $method = "_WX" . ucfirst($type);
        if (method_exists($this, $method)) {
            $this->$method();
            exit;
        } else {
            log_message('error', $method);
            $this->default_msg();
        }
        exit;
    }
    
    // 默认消息
    private function default_msg() {
        $text = lang('wx_welcome');
        $this->reText($text);
    }
    
    // 文本消息
    private function _WXText() {
        switch ($this->Content) {
            // 全网发布认证消息
            case 'TESTCOMPONENT_MSG_TYPE_TEXT':
                $this->reText('TESTCOMPONENT_MSG_TYPE_TEXT_callback');
                break;
            default:
                $this->default_msg();
                log_message('error', $this->Content);
                return;
        }
    }
    
    // 事件消息
    private function _WXEvent() {
        switch ($this->Event) {
            // 全网发布认证消息
            case 'LOCATION':
                $this->reText('LOCATION');
                break;
            case 'CLICK':
                break;
            default:
                $this->default_msg();
                log_message('error', $this->Content);
                return;
        }
    }
    
    /**
     * 回复文字消息（服务器 - > 微信）
     * @param $text :消息
     */
    public function reText($text) {
        /* 文本信息回复模板 */
        $tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";
        $time = time();
        $resultStr = sprintf($tpl, $this->FromUserName, $this->ToUserName, $time, $text);
        $resultStr = $this->CI->postdata->EncryptMsg($resultStr);
        echo $resultStr;
//        exit;
    }
    
    // 回复图文消息
    public function reTxPic($item = array()) {
        /* 图文消息模板 */
        $tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>%d</ArticleCount>
					<Articles>%s</Articles>
					</xml>";
        $itemTpl = "<item>
					<Title><![CDATA[%s]]></Title> 
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
					</item>";
        $time = time();
        $n = 1;
        $itemStr = "";
        if (isset($item['title'])) {
            $itemStr .= sprintf($itemTpl, $item['title'], $item['image'], $item['urladdress']);
        } else {
            $n = count($item);
            foreach ($item as $k => $v) {
                $itemStr .= sprintf($itemTpl, $v['title'], $v['image'], $v['urladdress']);
            }
        }
        $resultStr = sprintf($tpl, $this->FromUserName, $this->ToUserName, $time, $n, $itemStr);
        $resultStr = $this->CI->postdata->EncryptMsg($resultStr);
        log_message('error', $resultStr);
        echo $resultStr;
        exit;
    }
}