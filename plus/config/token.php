<?php defined('BASEPATH') OR exit('No direct script access allowed');
$config['expire_time'] = 7200; // Token有效周期 单位：秒（second）
$config['refresh_time'] = 900; // Token剩余周期内自动刷新 单位：秒（second）

$config['cipher'] = 'aes-256';
$config['mode'] = 'cbc';
$config['hmac'] = false;