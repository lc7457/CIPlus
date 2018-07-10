<?php defined('BASEPATH') OR exit('No direct script access allowed');
// 允许访问的IP白名单
$config['ip_white_list'] = array(
    "/^(127)\.(0)\.(0)\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])$/", // 本地ip
    "/^(192)\.(168)\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])$/", // ipv4 内外ip
);