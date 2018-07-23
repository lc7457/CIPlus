<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('unique_code')) {
    function unique_code() {
        return md5(uniqid(mt_rand(), true));
    }
}
if (!function_exists('zero_fill')) {
    function zero_fill($num, $len = 10, $type = 0) {
        return str_pad($num, $len, '0', $type);
    }
}