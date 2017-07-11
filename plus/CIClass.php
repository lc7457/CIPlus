<?php
/**
 * CI Class 模板类
 */

namespace CIPlus;

abstract class CIClass {
    public function __construct(array $params = array()) {
        $this->CI =& get_instance();
        if (!empty($params)) {
            $this->Init($params);
        }
    }

    /**
     * 初始化类
     * @param $params
     */
    private function Init($params) {
    }

    /**
     * 加载CI config配置文件
     * @param $name : 配置文件名称
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
    }
}