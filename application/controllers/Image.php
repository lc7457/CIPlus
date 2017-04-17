<?php defined('BASEPATH') or exit ('No direct script access allowed');

/**
 * 图像显示类
 * Class Image
 */
class Image extends CI_Controller {

    const FOLDER_IMAGE = 'image';
    const CATCH_TIME = 1440; // 缓存一天
    const DEFAULT_THUMB_SIZE = 720;

    private $file_save_path;
    private $temp_save_path;
    private $thumb_folder;
    private $thumb_factor;

    private $image_name;
    private $image_path;
    private $source_image;
    private $size;
    private $dim;
    private $thumb_path;
    private $thumb_save = true;

    public function __construct() {
        parent::__construct();
        $this->LoadConf();
        $this->load->model('storage_model');
    }

    /**
     * 加载配置文件
     */
    private function LoadConf() {
        $this->config->load('upload', true, true);
        $config = $this->config->item('upload');
        foreach ($config as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    public function Show($size = 'origin', $name = '', $dim = 'width') {
        $path = $this->file_save_path . self::FOLDER_IMAGE . DIRECTORY_SEPARATOR . $name;
        $this->size = $size;
        $this->dim = $dim;
        if (!file_exists($path)) {
            show_404();
        } else {
            $this->image_name = $name;
            $this->image_path = $this->source_image = $path;
        }
        if ($this->size !== 'origin') {
            $this->ResizeImg();
        }
        $this->ShowImage();
    }

    private function ShowImage() {
        if (self::CATCH_TIME > 0) {
            $this->output->cache(self::CATCH_TIME);
        }
        $type = explode('.', $this->image_name);
        $this->output->set_content_type($type[1])->set_output(file_get_contents($this->image_path));
        $this->output->_display();
        if (!$this->thumb_save) {
            @unlink($this->thumb_path);
        }
        exit;
    }

    private function ResizeImg() {
        $thumb_folder = $this->file_save_path . self::FOLDER_IMAGE . DIRECTORY_SEPARATOR . $this->thumb_folder . DIRECTORY_SEPARATOR;
        if (!is_dir($thumb_folder)) {
            mkdir($thumb_folder);
        }
        $this->size = is_numeric($this->size) ? $this->size : self::DEFAULT_THUMB_SIZE;
        $thumb_folder = $thumb_folder . $this->size . DIRECTORY_SEPARATOR;
        if (in_array($this->size, $this->thumb_factor)) {
            if (!is_dir($thumb_folder)) {
                mkdir($thumb_folder);
            }
            $this->thumb_path = $thumb_folder . $this->image_name;
            if (!file_exists($this->thumb_path)) {
                $this->CreateThumb();
            }
        } else {
            $this->CreateTempThumb();
        }
        $this->image_path = $this->thumb_path;
    }

    // 创建缩略图
    private function CreateThumb() {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $this->source_image;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['file_permissions'] = 0664;
        $config['width'] = $this->size;
        $config['height'] = $this->size;
        $config['master_dim'] = $this->dim;
        $config['new_image'] = $this->thumb_path;
        $config['thumb_marker'] = '';
        $this->load->library('image_lib', $config);
        return $this->image_lib->resize();
    }

    // 创建缩略图
    private function CreateTempThumb() {
        $this->thumb_path = $this->temp_save_path . $this->image_name;
        $config['image_library'] = 'gd2';
        $config['source_image'] = $this->source_image;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['file_permissions'] = 0664;
        $config['width'] = $this->size;
        $config['height'] = $this->size;
        $config['master_dim'] = $this->dim;
        $config['new_image'] = $this->thumb_path;
        $config['thumb_marker'] = '';
        $this->load->library('image_lib', $config);
        $this->thumb_save = false;
        return $this->image_lib->resize();
    }

}