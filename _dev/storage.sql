/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50547
Source Host           : 127.0.0.1:3306
Source Database       : ciplus

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-04-15 16:33:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for storage
-- ----------------------------
DROP TABLE IF EXISTS `storage`;
CREATE TABLE `storage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `hash` varchar(50) DEFAULT NULL COMMENT '文件HASH',
  `name` varchar(50) DEFAULT NULL COMMENT '文件名',
  `url` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `ext` varchar(20) DEFAULT NULL COMMENT '文件后缀',
  `size` int(10) unsigned DEFAULT NULL,
  `localName` varchar(255) DEFAULT NULL COMMENT '本地文件名',
  `type` varchar(20) DEFAULT NULL COMMENT '文件类型',
  `width` int(10) unsigned DEFAULT NULL COMMENT '宽度',
  `height` int(10) unsigned DEFAULT NULL COMMENT '高度',
  `error` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='存储表：基础表';
SET FOREIGN_KEY_CHECKS=1;
