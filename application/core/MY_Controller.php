<?php
defined('BASEPATH') or exit ('No direct script access allowed');
require_once APPPATH . 'core/API_Controller.php';

abstract class MY_Controller extends API_Controller {
    public function __construct($config) {
        parent::__construct($config);
    }

    public function Uid() {
        $data = is_array($this->oauthclient->securityData) ? $this->oauthclient->securityData : json_decode($this->oauthclient->securityData, true);
        if (is_array($data) && array_key_exists('uid', $data)) {
            return $data['uid'];
        }
        return false;
    }

    public function CheckUser($respond = true) {
        $uid = $this->Uid();
        $bool = !empty($uid);
        if ($respond && !$bool) {
            $this->SetCode('40098');
            $this->Respond();
        }
        return $bool;
    }

    public function Debug() {
        echo 'token : ' . $this->oauthclient->token . "\n" . "<br>";
        echo 'timestamp : ' . $this->oauthclient->timestamp . "\n" . "<br>";
        echo 'role : ' . $this->oauthclient->role . "\n" . "<br>";
        echo 'key : ' . $this->oauthclient->key . "\n" . "<br>";
        echo 'illegalLevel : ' . $this->oauthclient->illegalLevel . "\n" . "<br>";
        echo 'handle : ' . json_encode($this->oauthclient->handle) . "\n" . "<br>";
        echo 'security :' . json_encode($this->oauthclient->securityData) . "\n" . "<br>";
        echo 'mismatch:' . json_encode($this->oauthclient->mismatch) . "\n" . "<br>";
    }
}