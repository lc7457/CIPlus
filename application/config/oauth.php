<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['innerIp'] = 'http://127.0.0.1';
$config['outerUrl'] = 'https://your.domain'; // 推荐使用SSL安全链接
$config['entry'] = '/oauth';
$config['channel'] = 'outerUrl';

$config['identity'] = '';
$config['platform'] = 'Linux';
$config['agent'] = '';