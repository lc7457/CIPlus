/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50547
Source Host           : 127.0.0.1:3306
Source Database       : ciplus

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-03-08 17:14:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_base
-- ----------------------------
DROP TABLE IF EXISTS `user_base`;
CREATE TABLE `user_base` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `uid` varchar(50) NOT NULL COMMENT '用户标识',
  `email` varchar(100) DEFAULT NULL COMMENT '注册邮箱',
  `telphone` varchar(20) DEFAULT NULL COMMENT '注册手机',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `version` tinyint(1) unsigned DEFAULT '1' COMMENT '数据版本',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '用户状态',
  PRIMARY KEY (`id`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表：基础表';

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
-- Table structure for user_verify
-- ----------------------------
DROP TABLE IF EXISTS `user_verify`;
CREATE TABLE `user_verify` (
  `uid` varchar(50) DEFAULT NULL,
  `telphone` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `email` varchar(255) DEFAULT NULL COMMENT '电子邮件地址',
  `type` varchar(10) DEFAULT NULL COMMENT '验证类型（telphone；email）',
  `code` varchar(40) DEFAULT NULL COMMENT '验证码',
  `expires` int(10) unsigned DEFAULT NULL COMMENT '有效时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户表：信息认证';
SET FOREIGN_KEY_CHECKS=1;
