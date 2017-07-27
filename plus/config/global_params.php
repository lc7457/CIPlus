<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['driver'] = 'session'; // 全局参数会话引擎: session || cookie
$config['prefix'] = '_'; // 全局参数前缀

/**
 * 允许的全局参数白名单
 */
$config['global_params_list'] = array(
    'format',
    'global',
    'style'
);