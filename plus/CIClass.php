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
}