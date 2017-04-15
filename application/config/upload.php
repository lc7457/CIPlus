<?php defined('BASEPATH') OR exit('No direct script access allowed');
// 上传文件路径
$config['file_save_path'] = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR;
// 临时文件路径
$config['temp_save_path'] = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
// 按用途创建文件夹
$config['create_usage_folder'] = TRUE;
// 按用户标识创建文件夹
$config['create_user_folder'] = FALSE;
// 允许上传的文件夹大小
$config['max_file_size'] = 4096 * 4096 * 10;
// 允许同时上传的文件数
$config['max_file_num'] = 1;
// 提取文件hash
$config['fetch_hash'] = TRUE;
// 上传文件重命名(hash/timestamp)
$config['rename'] = array('type' => 'hash', 'keep_ext' => TRUE);
// 上传文件的用途及其允许的扩展名
$config['usages'] = array(
    'attach' => array(),
    'image' => array('jpg', 'png', 'gif', 'bmp', 'jpeg'),
    'model' => array('obj'),
    'uvw' => array('png'),
);



