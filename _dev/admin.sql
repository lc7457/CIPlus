/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50547
Source Host           : 127.0.0.1:3306
Source Database       : ciplus

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-03-30 17:20:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin` varchar(20) NOT NULL COMMENT '管理帐号',
  `password` varchar(255) NOT NULL COMMENT '管理密码',
  `level` tinyint(3) unsigned DEFAULT NULL COMMENT '权限级别',
  PRIMARY KEY (`id`,`admin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表：基础表';

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', '', '', null);

-- ----------------------------
-- Table structure for admin_level
-- ----------------------------
DROP TABLE IF EXISTS `admin_level`;
CREATE TABLE `admin_level` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(50) NOT NULL COMMENT '级别',
  PRIMARY KEY (`id`,`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表：帐号级别';

-- ----------------------------
-- Records of admin_level
-- ----------------------------

-- ----------------------------
-- Table structure for oauth
-- ----------------------------
DROP TABLE IF EXISTS `oauth`;
CREATE TABLE `oauth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `appid` varchar(20) DEFAULT NULL COMMENT '应用ID',
  `secret` varchar(50) DEFAULT NULL COMMENT '密钥',
  `access_token` varchar(50) DEFAULT NULL COMMENT '认证令牌',
  `token_expires_in` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='OAuth表：基础表';

-- ----------------------------
-- Records of oauth
-- ----------------------------
INSERT INTO `oauth` VALUES ('1', 'demo', 'abcdefghijklmnopqrstuvwxyz0123456', 'df6eba218f843e27f21d43fa3dba6c5e', '1489480315');

-- ----------------------------
-- Table structure for oauth_access
-- ----------------------------
DROP TABLE IF EXISTS `oauth_access`;
CREATE TABLE `oauth_access` (
  `code` varchar(10) NOT NULL COMMENT '自增ID',
  `account` varchar(50) NOT NULL COMMENT '帐号',
  `type` varchar(20) NOT NULL COMMENT '帐号类型',
  `ciphertext` blob COMMENT '密文数据',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '访问识别信息',
  `device` varchar(255) DEFAULT NULL COMMENT '设备硬件信息',
  `ip` varchar(25) DEFAULT NULL,
  `expires_in` int(10) DEFAULT NULL COMMENT '有效时间',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of oauth_access
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `uid` varchar(50) NOT NULL COMMENT '用户标识',
  `email` varchar(100) DEFAULT NULL COMMENT '注册邮箱',
  `phone` varchar(20) DEFAULT NULL COMMENT '注册手机',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `version` tinyint(1) unsigned DEFAULT '1' COMMENT '数据版本',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`,`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表：基础表';

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'CU0000000001', 'lichao1005@126.com', null, '1S3zwX8Ycf7Oks+AjYupn3X1x9Y802iYwIiNLvFarXvKVBI053pUv5t37orkfNpx8lW/pRJys9sHcxcx4ceBow==', '1', '0');

-- ----------------------------
-- Table structure for user_info
-- ----------------------------
DROP TABLE IF EXISTS `user_info`;
CREATE TABLE `user_info` (
  `uid` varchar(50) NOT NULL COMMENT '用户id',
  `nickname` varchar(40) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像图片',
  `sex` tinyint(1) unsigned DEFAULT '2' COMMENT '性别（2保密；1男；0女）',
  `language` varchar(10) DEFAULT NULL COMMENT '语言',
  `city` varchar(20) DEFAULT NULL COMMENT '城市',
  `province` varchar(20) DEFAULT NULL COMMENT '省份',
  `country` varchar(20) DEFAULT NULL COMMENT '国家',
  `createtime` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户表：用户资料';

-- ----------------------------
-- Records of user_info
-- ----------------------------

-- ----------------------------
-- Table structure for user_verify
-- ----------------------------
DROP TABLE IF EXISTS `user_verify`;
CREATE TABLE `user_verify` (
  `uid` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `email` varchar(255) DEFAULT NULL COMMENT '电子邮件地址',
  `type` varchar(10) DEFAULT NULL COMMENT '验证类型（telphone；email）',
  `code` varchar(40) DEFAULT NULL COMMENT '验证码',
  `expires` int(10) unsigned DEFAULT NULL COMMENT '有效时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户表：信息认证';

-- ----------------------------
-- Records of user_verify
-- ----------------------------
SET FOREIGN_KEY_CHECKS=1;
