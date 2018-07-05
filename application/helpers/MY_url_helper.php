<?php
//url base64编码
if (!function_exists('url64_encode')) {
function url64_encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
    return $data;
    }
}

//url base64解码
if (!function_exists('url64_decode')) {
function url64_decode($string) {
    $data = str_replace(array('-', '_'), array('+', '/'), $string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}
}
if (!function_exists('add_get_params')) {
    function add_get_params($url, $params) {
        $url = parse_url($url);
        if (key_exists('query', $url)) {
            $u = array();
            parse_str($url['query'], $u);
            $params = array_merge($u, $params);
        }
        return '//' . $url['host'] . $url['path'] . '?' . http_build_query($params);
    }
}