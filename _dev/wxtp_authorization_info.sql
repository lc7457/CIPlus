/*
Navicat MySQL Data Transfer

Source Server         : MySql-Server
Source Server Version : 50525
Source Host           : 192.168.8.98:3306
Source Database       : serv_wx3rd

Target Server Type    : MYSQL
Target Server Version : 50525
File Encoding         : 65001

Date: 2017-04-06 14:38:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wxtp_authorization_info
-- ----------------------------
DROP TABLE IF EXISTS `wxtp_authorization_info`;
CREATE TABLE `wxtp_authorization_info` (
  `authorizer_appid` varchar(20) NOT NULL COMMENT '公众号APPID',
  `authorizer_access_token` varchar(255) DEFAULT NULL COMMENT '公众号授权令牌',
  `authorizer_refresh_token` varchar(255) DEFAULT NULL COMMENT '公众号刷新令牌凭证',
  `authorizer_create_time` int(10) unsigned DEFAULT NULL COMMENT '初次授权时间',
  `authorizer_last_time` int(10) unsigned DEFAULT NULL COMMENT '最后一次授权时间',
  `a1` tinyint(1) unsigned DEFAULT '0' COMMENT '消息管理权限',
  `a2` tinyint(1) unsigned DEFAULT '0' COMMENT '用户管理权限',
  `a3` tinyint(1) unsigned DEFAULT '0' COMMENT '帐号服务权限',
  `a4` tinyint(1) unsigned DEFAULT '0' COMMENT '网页服务权限',
  `a5` tinyint(1) unsigned DEFAULT '0' COMMENT '微信小店权限',
  `a6` tinyint(1) unsigned DEFAULT '0' COMMENT '微信多客服权限',
  `a7` tinyint(1) unsigned DEFAULT '0' COMMENT '群发与通知权限',
  `a8` tinyint(1) unsigned DEFAULT '0' COMMENT '微信卡券权限',
  `a9` tinyint(1) unsigned DEFAULT '0' COMMENT '微信扫一扫权限',
  `a10` tinyint(1) unsigned DEFAULT '0' COMMENT '微信连WIFI权限',
  `a11` tinyint(1) unsigned DEFAULT '0' COMMENT '素材管理权限',
  `a12` tinyint(1) unsigned DEFAULT '0' COMMENT '微信摇周边权限',
  `a13` tinyint(1) unsigned DEFAULT '0' COMMENT '微信门店权限',
  `a14` tinyint(1) unsigned DEFAULT '0' COMMENT '微信支付权限',
  `a15` tinyint(1) unsigned DEFAULT '0' COMMENT '自定义菜单权限',
  PRIMARY KEY (`authorizer_appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信开放平台：公众号授权信息';

-- ----------------------------
-- Table structure for wxtp_authorizer_info
-- ----------------------------
DROP TABLE IF EXISTS `wxtp_authorizer_info`;
CREATE TABLE `wxtp_authorizer_info` (
  `authorizer_appid` varchar(255) NOT NULL,
  `nick_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `head_img` varchar(255) DEFAULT NULL COMMENT '头像',
  `service_type_info` tinyint(1) unsigned DEFAULT NULL COMMENT '公众号类型',
  `verify_type_info` tinyint(1) DEFAULT NULL COMMENT '认证类型',
  `user_name` varchar(255) DEFAULT NULL COMMENT '公众号的原始ID',
  `principal_name` varchar(255) DEFAULT NULL COMMENT '主体名称',
  `alias` varchar(255) DEFAULT NULL COMMENT '公众号所设置的微信号',
  `business_info` text COMMENT '功能的开通状况',
  `qrcode_url` varchar(255) DEFAULT NULL COMMENT '二维码图片地址',
  `open_store` tinyint(1) unsigned DEFAULT '0' COMMENT '是否开通微信门店',
  `open_scan` tinyint(1) unsigned DEFAULT '0' COMMENT '是否开通微信扫商品',
  `open_pay` tinyint(1) unsigned DEFAULT '0' COMMENT '是否开通微信支付',
  `open_card` tinyint(1) unsigned DEFAULT '0' COMMENT '是否开通微信卡券',
  `open_shake` tinyint(1) unsigned DEFAULT '0' COMMENT '是否开通微信摇一摇',
  `idc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`authorizer_appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信开放平台：公众号基本信息';
SET FOREIGN_KEY_CHECKS=1;
