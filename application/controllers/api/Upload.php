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
            'tokenVerifier' => false, // 是否验证token
            'strict' => false //是否开启严格模式，严格模式只能输出API信息
        ));
        $this->load->model('storage_model');
        $this->LoadConf();
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
        if ($type === "file") {
            $this->upload->File($_FILES, 'image');
        } elseif ($type === "stream") {
            $file = $this->input->post('file');
            if (empty($file)) {
                exit;
            }
            $this->upload->Base64($file, 'image');
        }
        $re = $this->KeepImage();
    }

    private function KeepImage() {
        $re = false;
        $fileInfo = $this->upload->FileInfo();
        if (!$this->CheckExistFile()) {
            $fileInfo['type'] = 'image';
            $re = $this->storage_model->insert($fileInfo);
        }
        return $re;
    }

    private function CheckExistFile() {
        $fileInfo = $this->upload->FileInfo();
        $num = $this->storage_model->count(array('hash' => $fileInfo['hash']));
        return $num > 0;
    }


}