<?php defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * 上传文件接口
 */
require_once FCPATH . 'plus/Upload.php';

class Upload extends MY_Controller {

    public $file;
    public $upload;

    protected $file_save_path;

    public function __construct() {
        parent::__construct(array(
            'tokenVerifier' => true, // 是否验证token
            'strict' => true //是否开启严格模式，严格模式只能输出API信息
        ));
        $this->LoadConf();
        $this->CheckLogin();
        $this->load->model('storage_model');
    }

    /**
     * 加载配置文件
     */
    private function LoadConf() {
        $this->config->load('upload', true, true);
        $config = $this->config->item('upload');
        if (is_array($config)) {
            $this->upload = new CIPlus\Upload($config);
            foreach ($config as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        } else {
            $this->SetCode(50001);
            $this->Respond();
        }
    }

    public function Image($type = 'file') {
        $file = $this->input->post('file');
        if (empty($file)) {
            exit;
        }
        if ($type === "file") {
            $this->upload->File($_FILES, 'image');
        } elseif ($type === "stream") {
            $this->upload->Base64($file, 'image');
        }
        $this->file = $this->upload->FileInfo();
    }


}