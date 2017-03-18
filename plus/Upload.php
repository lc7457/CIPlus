<?php
namespace CIPlus;

/**
 * 上传类
 * ============================================================
 * 主要方法：
 * File($files, $usage = '');
 * MutilFiles($files,$usage='');
 * Base64($stream, $usage = '');
 * ============================================================
 * Version 2.0.0
 * Create by LeeNux @ 2016-1-14
 * Update by LeeNux @ 2016-9-17
 */

class Upload {
    //upload config start
    private $file_save_path = '';
    private $temp_save_path = '';
    private $create_usage_folder = TRUE;
    private $create_user_folder = FALSE;
    private $max_file_size = 0;
    private $max_file_num = 1;
    private $fetch_hash = TRUE;
    private $rename = array();
    private $usages = array();
    //upload config end
    private $file;
    private $usage;
    private $exts;
    private $url = '/';

    public $uid;
    public $newName;

    public function __construct($params = array()) {
        $this->LoadUserConfig($params);
    }

    /**
     * 加载用户自定义配置参数
     * @param array $params
     */
    public function LoadUserConfig($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * 单文件上传
     * @param $files :文件
     * @param $usage : 文件用途（参考配置文件）
     */
    public function File($files, $usage = 'attach') {
        $file = array_keys($files);
        $fileNum = count($file);
        if ($fileNum > 1) {
            $this->_error('illegalNum');
        } else {
            $this->file = $files[$file[0]];
            $this->IsLegalFile();
            $this->IsLegalUsage($usage);
            $this->UploadFile();
        }
    }

    /**
     * 多文件上传
     * @param $files :文件
     * @param $usage : 文件用途（参考配置文件）
     */
    public function MultiFiles($files, $usage = 'attach') {
        $file = array_keys($files);
        $fileNum = count($file);

        if ($fileNum > $this->max_file_num) {
            $this->_error('illegalNum');
        } else {

        }
    }

    /**
     * 以base64字节流上传
     * @param $stream : 文件完整的base64编码字节流
     * @param $usage : 文件用途（参考配置文件）
     */
    public function Base64($stream, $usage = 'attach') {
        if (empty($stream)) {
            $this->_error('illegalFile');
        }
        $this->IsLegalUsage($usage);
        $stream = explode(',', $stream);
        $info = explode(';', $stream[0]);
        $mime = explode(':', $info[0]);
        $mime = $mime[1];
        $base64 = $stream[1];
        $tmpFile = tempnam($this->temp_save_path, 'tem0');
        $file = fopen($tmpFile, 'w+');
        fwrite($file, base64_decode($base64));
        fclose($file);
        $ext = $this->GetExtFromMime($mime);
        $this->file['name'] = 'temp.' . $ext;
        $this->file['type'] = $mime;
        $this->file['tmp_name'] = $tmpFile;
        $this->file['error'] = 0;
        $this->file['size'] = filesize($tmpFile);
        $this->UploadFile();
    }

    /**
     * 获取当前上传的文件信息
     * @return mixed
     */
    public function FileInfo() {
        $this->file['url'] = $this->url;
        unset($this->file['tmp_name']);
        return $this->file;
    }

    /**
     * 获取当前上传的文件路径
     * @return mixed
     */
    public function FilePath() {
        return $this->file_save_path . $this->file['name'];
    }

    // 安全性检测
    private function IsLegalFile() {
        $filename = $this->file['tmp_name'];
        if (is_uploaded_file($filename) === TRUE) {
            return TRUE;
        } else {
            $this->_error('illegalFile');
            return FALSE;
        }
    }

    // 文件用途检测
    private function IsLegalUsage($usage) {
        if (array_key_exists($usage, $this->usages)) {
            $this->exts = $this->usages[$usage];
            $this->usage = $usage;
            return true;
        } else {
            $this->_error('undefineUsage');
        };
    }

    // 文件上传
    private function UploadFile() {
        $this->IsValidFormat($this->exts);
        $this->IsValidSize();
        $this->IsValidPath();
        $this->FileHash();
        $this->FileRename();
        $this->GetImageSize();
        $this->MoveFileToSavePath();
    }

    // 检测上传文件的大小
    private function IsValidSize() {
        if ($this->file['size'] > $this->max_file_size) {
            $this->_error('illegalSize', $this->max_file_size);
            return FALSE;
        } else {
            return TRUE;
        }
    }

    // 检测上传文件的格式
    private function IsValidFormat($format) {
        $arr = explode('.', $this->file['name']);
        $ext = strtolower(end($arr));
        if (!empty($format)) {
            if (!in_array($ext, $format)) {
                $this->_error('illegalFormat');
                return FALSE;
            }
        }
        $this->file['ext'] = $ext;
        return TRUE;
    }

    //检测上传路径
    private function IsValidPath() {
        $this->IsValidFolder();
        if ($this->create_usage_folder) {
            $this->file_save_path .= $this->usage . DIRECTORY_SEPARATOR;
            $this->url .= $this->usage . '/';
            $this->IsValidFolder();
        }
        if ($this->create_user_folder) {
            $this->file_save_path .= $this->uid . DIRECTORY_SEPARATOR;
            $this->url .= $this->uid . '/';
            $this->IsValidFolder();
        }
        if (!is_writable($this->file_save_path)) {
            $this->_error('unWritable');
            return FALSE;
        }
        return TRUE;
    }

    // 检测上传文件夹
    private function IsValidFolder() {
        if (!is_dir($this->file_save_path)) {
            mkdir($this->file_save_path);
        }
        if (!is_dir($this->file_save_path)) {
            $this->_error('illegalPath');
            return FALSE;
        }
        return TRUE;
    }

    // 通过文件base64 的mime类型判断文件扩展名
    private function GetExtFromMime($mime) {
        $mimes = get_mimes();
        $ext = array_search($mime, $mimes);
        if (empty($ext)) {
            foreach ($mimes as $k => $v) {
                if (is_array($v)) {
                    if (in_array($mime, $v)) {
                        $ext = $k;
                        break;
                    }
                }
            }
        }
        return $ext;
    }

    // 提取文件散列
    private function FileHash() {
        if ($this->fetch_hash) {
            $FileHash = sha1_file($this->file['tmp_name']);
            $this->file['hash'] = $FileHash;
            return $FileHash;
        }
    }

    // 重命名
    private function FileRename() {
        $config = $this->rename;
        if (!empty($config['type'])) {
            $type = 'RN_' . strtolower($config['type']);
            $newName = $this->$type();
            $this->file['localName'] = $this->file['name'];
            $this->file['name'] = $newName;
        }
        if ($config['keep_ext']) {
            $this->file['name'] .= '.' . $this->file['ext'];
        }
        $this->url .= $this->file['name'];
    }

    // 返回文件散列
    private function RN_hash() {
        if (empty($this->file['hash'])) {
            $FileHash = sha1_file($this->file['tmp_name']);
        } else {
            $FileHash = $this->file['hash'];
        }
        return $FileHash;
    }

    // 返回时间戳
    private function RN_timestamp() {
        return time() . rand(1000, 9999);
    }

    // 将上传的文件移动到上传目录
    private function MoveFileToSavePath() {
        $ufo = $this->file_save_path . $this->file['name'];
        if (rename($this->file['tmp_name'], $ufo) === false) {
            $this->_error('wtf');
            return FALSE;
        }
        @unlink($this->file['tmp_name']);
        @chmod($ufo, 0755);
        return TRUE;
    }

    // 获取上传图片的尺寸
    private function GetImageSize() {
        $size = @getimagesize($this->file['tmp_name']);
        if (isset($size)) {
            $this->file['width'] = $size[0];
            $this->file['height'] = $size[1];
            return true;
        } else {
            return false;
        }
    }

    // 各种报错
    private function _error($e, $message = '') {
        http_response_code(500);
        switch ($e) {
            case ("illegalFile") :
                exit('Upload: Illegal file');
                break;
            case ("undefineUsage") :
                exit('Upload: Undefine usage');
                break;
            case ("illegalSize") :
                exit('Upload: Illegal size');
                break;
            case ("illegalNum") :
                exit('Upload: Illegal number');
                break;
            case("illegalFormat") :
                exit('Upload: Illegal format');
                break;
            case("illegalPath") :
                exit('Upload: Illegal path');
                break;
            case("unWritable") :
                exit('Upload: not writable');
                break;
            case("wtf") :
                exit('Upload: WTF');
                break;
            default :
                show_404();
        }
    }

}
