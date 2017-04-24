/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50547
Source Host           : 127.0.0.1:3306
Source Database       : ciplus

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-04-24 17:23:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for content
-- ----------------------------
DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `title` varchar(64) DEFAULT NULL COMMENT '新闻标题',
  `author` varchar(20) DEFAULT NULL COMMENT '作者',
  `abstract` varchar(100) DEFAULT NULL COMMENT '摘要',
  `keywords` varchar(255) DEFAULT NULL COMMENT '关键词、分类、标签',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `link` text COMMENT '原文链接',
  `cover` text COMMENT '封面图片',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态(0冻结，1正常）',
  `type` varchar(20) DEFAULT NULL COMMENT '内容类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='内容表：基础表';

-- ----------------------------
-- Table structure for content_media
-- ----------------------------
DROP TABLE IF EXISTS `content_media`;
CREATE TABLE `content_media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `title` varchar(50) DEFAULT NULL COMMENT '子标题',
  `src` varchar(255) DEFAULT NULL COMMENT '媒体资源',
  `type` varchar(10) DEFAULT NULL COMMENT '内容类型',
  `link` text COMMENT '源链接',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `language` varchar(20) DEFAULT '' COMMENT '语言类型',
  `content_id` int(10) unsigned DEFAULT NULL COMMENT '内容ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='内容表：多媒体';

-- ----------------------------
-- Table structure for content_rtf
-- ----------------------------
DROP TABLE IF EXISTS `content_rtf`;
CREATE TABLE `content_rtf` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `title` varchar(50) DEFAULT NULL COMMENT '子标题',
  `content` text COMMENT '内容',
  `type` varchar(10) DEFAULT NULL COMMENT '内容类型',
  `link` text COMMENT '源链接',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `language` varchar(20) DEFAULT '' COMMENT '语言类型',
  `content_id` int(10) unsigned DEFAULT NULL COMMENT '内容ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='内容表：富文本';
SET FOREIGN_KEY_CHECKS=1;
