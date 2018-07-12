<?php
namespace CIPlus;

/**
 * 数字类
 * ============================================================
 * ============================================================
 * Version 2.0.0
 * Create by LeeNux @ 2017-3-1
 */

class Number {
    const DICT_36 = '0123456789abcdefghijklmnopqrstuvwxyz';
    const DICT_62 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * 整型数字 -> N进制编码
     * @param $num
     * @param $rule
     * @param bool $customize
     * @return string
     */
    public function encode($num, $rule = 36, $customize = false) {
        $dict = $customize ? $rule : constant('self::DICT_' . $rule);
        $len = strlen($dict);
        $k = (int)fmod($num, $len);// php使用“%”求余可能会溢出，使用“fmod()”函数
        $str = $dict[$k];
        if ($num >= $len) {
            $num = floor($num / $len);
            $str = $this->Encode($num, $rule) . $str;
        }
        return $str;
    }

    /**
     * N进制编码 -> 整型数字
     * @param $str
     * @param $rule
     * @param bool $customize
     * @return bool|int
     */
    public function decode($str, $rule = 36, $customize = false) {
        $rule = $customize ? $rule : constant('self::DICT_' . $rule);
        $len = strlen($rule);
        $n = strlen($str);
        $x = 0;
        for ($i = $n; $i > 0; $i--) {
            $x += strpos($rule, $str[$n - $i]) * pow($len, $i - 1);
        }
        return $x;
    }

    /**
     * 填充0（零）
     * @param int $num
     * @param int $len
     * @param $type
     * @return string
     */
    public function zerofill($num, $len = 10, $type = 0) {
        return str_pad($num, $len, '0', $type);
    }
}