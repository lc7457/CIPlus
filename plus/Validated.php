<?php
namespace CIPlus;
/**
 * 数据格式验证
 */
class Validated {

    //static public $rules = array(
    const REGEXP_ID = "/^(\w){4,20}$/"; // ID: 字母、数字、下划线组成
    const REGEXP_PASSWORD = "/^(\S){6,20}$/"; // 密码
    const REGEXP_MD5 = "/^(\S){32}$/"; // md5 编码
    const REGEXP_QQ = "/^[1-9][0-9]{4,}$/"; // QQ号
    const REGEXP_EMAIL = "/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/"; // E-mail
    const REGEXP_URL = "/^(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)([a-zA-Z0-9\-\.\?\,\'\/\\\+&amp;%\$#_]*)?$/"; // 网址
    const REGEXP_COLOR_HEX = "/^#([a-fA-F0-9]){3}(([a-fA-F0-9]){3})?$/"; // 颜色哈希代码
    const REGEXP_IPV4 = "/^(\d|[01]?\d\d|2[0-4]\d|25[0-5])\.(\d|[01]?\d\d|2[0-4] \d|25[0-5])\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])\.(\d|[01]?\d\d|2[0-4] \d|25[0-5])$/"; // IPv4 地址
    const REGEXP_MAC_ADDRESS = "/^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/"; // MAC 地址
    const REGEXP_INTEGER = "/^[-+]?[1-9]\d*\.?[0]*$/"; // 整型数字
    const REGEXP_FLOAT = "/^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$/"; // 浮点型数字
    const REGEXP_ZH_UTF8 = "/[\u4e00-\u9fa5]/"; // 中文字符utf-8编码
    const REGEXP_ZH_TEL = "/\d{3}-\d{8}|\d{4}-\d{7,8}/"; // 中国座机号码
    const REGEXP_ZH_PHONE = "/^1[34578]\d{9}$/"; //中国手机号码
    const REGEXP_ZH_CID = "/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/";// 中国身份证号码
    const REGEXP_ZH_POSTCODE = "/^[1-9]\d{5}(?!\d)$/"; // 中国邮政编码

    /**
     * 正则匹配数据验证
     * @param $rule
     * @param $str
     * @return bool
     */
    public function Regexp($rule, $str) {
        return preg_match(constant('self::REGEXP_' . strtoupper($rule)), $str);
    }

}
