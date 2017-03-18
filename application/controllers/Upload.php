<?php
require_once FCPATH . 'plus/Upload.php';

class Upload extends CI_Controller {

    public $file;
    public $imgPath;

    public function __construct() {
        parent::__construct();
        $this->config->load('upload', true, true);
        $config = $this->config->item('upload');
        $this->upload = new CIPlus\Upload($config);
        $this->imgPath = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;
    }

    public function Image($type = 'file') {
        $file = $this->input->post('file');
        if (empty($file)) {
            exit;
        }
        $resize = $this->input->post('resize');
        if ($type === "file") {
            $this->upload->File($_FILES, 'image');
        } elseif ($type === "stream") {
            $this->upload->Base64($file, 'image');
        }
        $this->file = $this->upload->FileInfo();
        if ($resize) {
            $this->ResizeImg();
        }
    }

    private function ResizeImg() {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $this->imgPath . $this->file['name'];
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = 1024;
        $config['height'] = 1024;
        //print_r($this -> imgPath . 'thumb');
        $this->load->library('image_lib', $config);
        $name = explode('.', $this->file['name']);
        $this->file['name'] = $name[0] . '_thumb.' . $name[1];
        $re = $this->image_lib->resize();
    }

}