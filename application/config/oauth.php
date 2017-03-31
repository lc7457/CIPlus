<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['inner_ip'] = 'http://127.0.0.1';
$config['outer_url'] = 'https://your.domain'; // 推荐使用SSL安全链接
$config['entry'] = '/oauth';
$config['channel'] = 'outer_url';

$config['appid'] = '';
$config['platform'] = 'Linux';
$config['agent'] = '';

$config['auto_clean'] = true;
$config['refresh_token'] = false;
$config['expires_time'] = 3600;