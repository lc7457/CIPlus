<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once PLUGINS_PATH . 'ai' . DIRECTORY_SEPARATOR . '/QQ.php';

Class QQ_Vision extends QQ {
    
    const FACE_FUSION = 'https://api.ai.qq.com/fcgi-bin/ptu/ptu_facemerge';
    
    public function __construct(array $config = array()) {
        parent::__construct();
    }
    
    /**
     * 人脸融合
     * @param $path
     * @param $model
     * @return bool|mixed
     */
    public function faceFusion($path, $model) {
        $data = file_get_contents($path);
        $base64 = base64_encode($data);
        $params = array(
            'app_id' => $this->app_id,
            'image' => $base64,
            'model' => $model,
            'time_stamp' => strval(time()),
            'nonce_str' => strval(rand()),
            'sign' => '',
        );
        $params['sign'] = $this->getReqSign($params);
        return $this->doHttpPost(self::FACE_FUSION, $params);
    }
}