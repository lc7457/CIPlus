<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Jsonf文件类
 * ===================================================================
 * 保存Json为文件
 * 读取本地Json文件
 * ===================================================================
 * Version 1.0.0 BETA
 * Create by LeeNux @ 2016-3-2
 * Update by LeeNux @ 2016-3-2
 */
Class Jsonf {
    private $save_path = '';
    private $save_extension = '';
    private $security_extension = "";

    public function __construct($params = array()) {
        // 优先加载系统配置
        $this->LoadConfig();
        // 加载用户配置，可覆盖之前配置
        $this->Init($params);
    }

    /**
     * 加载CI的配置文件
     */
    private function LoadConfig() {
        try {
            $CI = &get_instance();
            $CI->config->load('jsonf', true, true);
            $config = $CI->config->item('jsonf');
            if (count($config) > 0) {
                foreach ($config as $key => $val) {
                    if (isset($this->$key)) {
                        $this->$key = $val;
                    }
                }
            }
        } catch (Exception $e) {
            // this not codeigniter
        }
    }

    /**
     * 初始化用户配置参数
     * @param $params :初始化参数
     */
    public function Init($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * 读取json文件
     * @param $fileName :文件名
     * @param $toArray :是否转为数组
     * @return bool|mixed|string
     */
    public function Read($fileName, $toArray = true) {
        $file_path = $this->save_path . $fileName . '.' . $this->save_extension;
        if (file_exists($file_path)) {
            $json = file_get_contents($file_path);
            if ($toArray) {
                $json = json_decode($json, true);
            }
            return $json;
        } else {
            return false;
        }
    }

    /**
     * 写入json文件
     * @param $fileName
     * @param $str
     * @return bool
     */
    public function Write($fileName, $str) {
        // 将数组转换为json
        if (is_array($str)) {
            $str = json_encode($str);
        } // 将对象转换为json
        elseif (is_object($str)) {
            $str = json_encode($str);
        }
        $file_path = $this->save_path . $fileName . '.' . $this->save_extension;
        $file = fopen($file_path, 'w');
        if (file_exists($file_path)) {
            fwrite($file, $str);
            @chmod($file, 0755);
            fclose($file);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 读取需要保护的json文件
     * @param $fileName
     * @param bool $toArray
     * @return bool|mixed|string
     */
    public function Load($fileName, $toArray = true) {
        $file_path = $this->save_path . $fileName . '.' . $this->security_extension;
        if (file_exists($file_path)) {
            $json = trim(substr(file_get_contents($file_path), 15));
            if ($toArray) {
                $json = json_decode($json, true);
            }
            return $json;
        } else {
            return false;
        }
    }

    /**
     * 写入需要保护的json文件
     * @param $fileName
     * @param $str
     * @return bool
     */
    public function Save($fileName, $str) {
        // 将数组转换为json
        if (is_array($str)) {
            $str = json_encode($str);
        } // 将对象转换为json
        elseif (is_object($str)) {
            $str = json_encode($str);
        }
        $file_path = $this->save_path . $fileName . '.' . $this->security_extension;
        $file = fopen($file_path, 'w');
        if (file_exists($file_path)) {
            fwrite($file, "<?php exit();?>\n" . $str);
            @chmod($file, 0755);
            fclose($file);
            return true;
        } else {
            return false;
        }
    }

}
