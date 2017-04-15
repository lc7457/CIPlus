<?php

class Image extends CI_Controller {

    const STORAGE_PATH = 'storage';

    private $image;

    public function __construct() {
        parent::__construct();
        $this->load->model('storage_model');
    }

    public function Show($id = null, $size = "") {
        if (empty($id)) {
            show_404();
        }
        $this->image = $this->storage_model->row(array('id' => $id));
        $this->ResizeImg();
    }

    private function ResizeImg() {
        $config['image_library'] = 'gd2';
        $config['source_image'] = self::STORAGE_PATH . $this->image['url'];
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = 720;
        $config['height'] = 720;
        $this->load->library('image_lib', $config);
//        $name = explode('.', $this->file['name']);
//        $this->file['name'] = $name[0] . '_thumb.' . $name[1];
        $re = $this->image_lib->resize();

        var_dump($re);
    }
}