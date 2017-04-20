<?php defined('BASEPATH') OR exit('No direct script access allowed');
// 严格模式，开启后无法输出其他内容
$config['strict'] = TRUE;
// 默认返回的API数据格式
$config['respondFormat'] = 'json';
// 支持的API数据格式
$config['supportedFormats'] = [
    'json' => 'application/json',
    'array' => 'application/json',
    'csv' => 'application/csv',
    'html' => 'text/html',
    'jsonp' => 'application/javascript',
    'php' => 'text/plain',
    'serialized' => 'application/vnd.php.serialized',
    'xml' => 'application/xml'
];