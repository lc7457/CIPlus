<?php defined('BASEPATH') or exit ('No direct script access allowed');
require_once APPPATH . 'core/API_Controller.php';

abstract class MY_Controller extends API_Controller {

    const PATH_AUTH = APPPATH . 'third_party/auth';

    public function __construct(array $config = array()) {
        parent::__construct();
        $this->SetConf($config);

        $this->load->add_package_path(self::PATH_AUTH);
        $this->load->library('AuthClient');
        $this->Verifier();
    }

    // 接口验证
    protected function Verifier() {
        if ($this->authclient->Connect()) {
            var_dump($this->authclient->Validate()->securityData);
            echo $this->authclient->err;
        } else {
            show_error("Can not connect auth server", '500');
        }
    }

}