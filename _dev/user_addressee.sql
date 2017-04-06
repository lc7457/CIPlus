/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50547
Source Host           : 127.0.0.1:3306
Source Database       : ciplus

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-04-06 16:55:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_addressee
-- ----------------------------
DROP TABLE IF EXISTS `user_addressee`;
CREATE TABLE `user_addressee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `hash` varchar(50) NOT NULL DEFAULT '' COMMENT '唯一散列标识',
  `uid` varchar(50) DEFAULT NULL COMMENT '用户标识',
  `name` varchar(50) DEFAULT NULL COMMENT '收件人',
  `tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `national_code` int(10) DEFAULT NULL COMMENT '地区代码',
  `postal_code` int(10) DEFAULT NULL COMMENT '邮编',
  `province` varchar(50) DEFAULT NULL COMMENT '省',
  `city` varchar(50) DEFAULT NULL COMMENT '市',
  `area` varchar(50) DEFAULT NULL COMMENT '区县',
  `address` text COMMENT '详细收货地址信息',
  `is_default` tinyint(1) unsigned DEFAULT '0' COMMENT '是否为默认',
  PRIMARY KEY (`id`,`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='用户表：收件人信息';
SET FOREIGN_KEY_CHECKS=1;
