<?php defined('BASEPATH') OR exit('No direct script access allowed');
$config['expire_time'] = 7200; // Token有效周期 单位：秒（second）
$config['refresh_time'] = 900; // Token剩余周期内自动刷新 单位：秒（second）

$config['encryption'] = [ // 加密方法，可配置多种方案
    'simple' => [
        'cipher' => 'aes-128',
        'mode' => 'cbc',
        'key' => '4355efff001b9a11b1bd0598313c5ff1',
        'hmac' => false
    ],
    'v1' => [
        'cipher' => 'aes-256',
        'mode' => 'cbc',
        'key' => '3a535934dfae0972fb87c5ce71b314f2',
        'hmac' => false
    ]
];

$config['header_required'] = ['typ', 'rap'];
$config['payload_required'] = ['uid', 'iat', 'exp'];