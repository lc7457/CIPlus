<?php
/**
 * CI Class 模板类
 */

namespace CIPlus;

abstract class CIClass {
    protected $CI;
    public function __construct(array $config = array()) {
        $this->CI =& get_instance();
        if (!empty($config)) {
            $this->InitConf($config);
        }
    }

    /**
     * 根据参数初始化类
     * @param $config
     */
    private function InitConf($config) {
        foreach ($config as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    /**
     * 加载CI config配置文件
     * @param $name : 配置文件名称
     * @return array
     */
    protected function LoadConf($name) {
        $this->CI = &get_instance();
        $this->CI->config->load($name, true, true);
        $config = $this->CI->config->item($name);
        if (is_array($config)) {
            foreach ($config as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
        return $config;
    }

    /**
     * 加载插件库
     * @param $plugins
     * @return bool
     */
    protected function LoadPlugins($plugins) {
        if (is_array($plugins)) {
            foreach ($plugins as $item) {
                $this->LoadPlugin($item);
            }
            return true;
        } else {
            $this->LoadPlugin($plugins);
        }
        return false;
    }

    protected function LoadPlugin($plugin) {
        if (is_string($plugin)) {
            $this->CI->load->add_package_path(FCPATH . 'plugins' . DIRECTORY_SEPARATOR . $plugin);
            return true;
        }
        return false;
    }
}