<?php
require_once FCPATH . 'plus/CIClass.php';

class ParseEvent extends \CIPlus\CIClass {
    
    public $postObj;
    // event key
    public $AppId; // 公众号APPID
    public $InfoType; // 事件消息类型
    
    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('wechat_open');
    }
    
    /**
     * 解析消息
     * @param $postObj
     */
    public function parse($postObj = array()) {
        $this->postObj = $postObj;
        foreach ($postObj as $k => $v) {
            $this->$k = $v;
        }
        $this->parseWechatOpenEvent();
        exit;
    }
    
    private function parseWechatOpenEvent() {
        switch ($this->InfoType) {
            case 'component_verify_ticket':
                $this->CI->load->library('OpenLicensing');
                $this->CI->openlicensing->saveVerifyTicket($this->postObj);
                break;
            case 'authorized':
                break;
            case 'unauthorized':
                break;
            case 'updateauthorized':
                break;
            default:
                log_message('error', $this->InfoType);
                log_message('error', json_encode($this->postObj));
                return;
        }
    }
}