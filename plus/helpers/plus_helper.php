<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('unique_code')) {
    /**
     * 构造唯一的随机字符串
     * @return string
     */
    function unique_code() {
        return md5(uniqid(mt_rand(), true));
    }
}

if (!function_exists('is_json')) {
    /**
     * 判断是否为合法的JSON字符串
     * @param $string
     * @return bool
     */
    function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('zero_fill')) {
    /**
     * 字符串填充零
     * @param string $num 被填充的字符串
     * @param int $len 总长度
     * @param int $type 填充位置
     * @return string
     */
    function zero_fill($num, $len = 10, $type = 0) {
        return str_pad($num, $len, '0', $type);
    }
}

if (!function_exists('build_request_form')) {
    /**
     * 建立跳转请求表单
     * @param string $url 数据提交跳转到的URL
     * @param array $data 请求参数数组
     * @param string $method 提交方式：post或get 默认post
     * @return string 提交表单的HTML文本
     */
    function build_request_form($url, $data, $method = 'post') {
        $sHtml = "<form id='requestForm' name='requestForm' action='" . $url . "' method='" . $method . "'>";
        foreach ($data as $key => $val) {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "' />";
        }
        $sHtml = $sHtml . "<input type='submit' value='submit' style='display:none;'></form>";
        $sHtml = $sHtml . "<script>document.forms['requestForm'].submit();</script>";
        return $sHtml;
    }
}