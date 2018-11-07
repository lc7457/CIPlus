<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* 允许访问的IP白名单 */
$config['ip_white_list'] = array(
    "/^(127)\.(0)\.(0)\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])$/", // 本地ip
    "/^(192)\.(168)\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])$/", // ipv4 内外ip
);

/* 禁止访问的IP黑名单 */
$config['ip_black_list'] = array();

/* token 获取方式 */
$config['token_sources'] = 'request'; // [header || request]

/* token 获取时的键值 */
$config['token_sources_key'] = 'token';

/* token 验证服务器API */
$config['token_inspect_api'] = 'http://rap.ciplus.com/spi/inspector/token';

/* token 验证方法 */
$config['token_inspect_method'] = 'get'; // [get || post]

/* token 发送验证时的键值 */
$config['token_inspect_key'] = 'token';

/* token 验证应返回的数据类型 */
$config['token_respond_type'] = 'json'; // [json || string]

/* token 验证预期返回的成功数据 */
$config['token_respond_expected'] = array('code' => 20000);