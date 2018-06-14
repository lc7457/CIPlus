<?php
/**
 * 获取客户端真实IP地址
 */
if (!function_exists('client_ip')) {
    function client_ip() {
        $ip = 'unknow';
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_XFORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $ip = getenv('HTTP_CLIENT_IP');
            } else {
                $ip = getenv('REMOTE_ADDR');
            }
        }
        if (trim($ip) == "::1") {
            $ip = '127.0.0.1';
        }
        return $ip;
    }
}
/**
 * 判断内网ip
 */
if (!function_exists('is_intranet_ip')) {
    function is_intranet_ip($ip) {
        $reg_intranet_ipv4 = "/^(192)\.(168)\.(8|9|10)\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])$/";
        $flag = (bool) preg_match($reg_intranet_ipv4, $ip);
        return $flag;
    }
}