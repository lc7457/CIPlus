/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : 127.0.0.1:3306
 Source Schema         : ciplus

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 01/06/2019 17:59:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for api
-- ----------------------------
DROP TABLE IF EXISTS `api`;
CREATE TABLE `api`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `key` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '唯一标识',
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '显示名称',
  `path` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '访问路径',
  `required` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '[]' COMMENT '必要参数',
  `optional` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '[]' COMMENT '可选参数',
  `method` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'request' COMMENT '请求方法[request/get/post]',
  `validated` tinyint(1) NOT NULL DEFAULT 1 COMMENT '需要验证',
  `usable` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否可用',
  PRIMARY KEY (`id`, `key`) USING BTREE,
  INDEX `key`(`key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '接口表：#####' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of api
-- ----------------------------
INSERT INTO `api` VALUES (1, 'login', '用户登录', 'user/login', '[\"password\"]', '[\"account\",\"email\",\"phone\",\"header\"]', 'request', 0, 1);
INSERT INTO `api` VALUES (2, 'cert', '登录凭证', 'user/cert', '[\"token\"]', '[]', 'request', 0, 1);
INSERT INTO `api` VALUES (3, 'reg', '用户注册', 'user/reg', '[\"password\"]', '[\"account\",\"email\",\"phone\",\"repassword\"]', 'request', 0, 1);

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序列ID',
  `key` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '唯一标识',
  `name` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '显示名称',
  `descript` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`, `key`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `key`(`key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '角色表：#####' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES (1, 'admin', '管理员', '系统超级管理员');
INSERT INTO `role` VALUES (2, 'manager', '管理员', '系统管理员');

-- ----------------------------
-- Table structure for role_api
-- ----------------------------
DROP TABLE IF EXISTS `role_api`;
CREATE TABLE `role_api`  (
  `role_key` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色KEY',
  `api_key` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '接口KEY',
  INDEX `role_key`(`role_key`) USING BTREE,
  INDEX `api_key`(`api_key`) USING BTREE,
  CONSTRAINT `api_key` FOREIGN KEY (`api_key`) REFERENCES `api` (`key`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_key` FOREIGN KEY (`role_key`) REFERENCES `role` (`key`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '角色表：接口权限' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of role_api
-- ----------------------------
INSERT INTO `role_api` VALUES ('admin', 'login');
INSERT INTO `role_api` VALUES ('admin', 'cert');
INSERT INTO `role_api` VALUES ('admin', 'reg');

-- ----------------------------
-- Table structure for role_users
-- ----------------------------
DROP TABLE IF EXISTS `role_users`;
CREATE TABLE `role_users`  (
  `role_key` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色KEY',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '角色表：用户组' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of role_users
-- ----------------------------
INSERT INTO `role_users` VALUES ('admin', 1000000000);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `account` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '账号',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'Email',
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `usable` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '账号状态',
  `create_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000000001 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户表：#####' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1000000000, 'admin', 'admin@cprap.com', '', 'b34e50b8b8b4b1fe831a20e37db6285b38adb5c0510afdd7dd62ac5a8412e5be4685b5a8408cf3cfc7b70058be2309a759009be6273ccc0a44f3fa57391abc80qfPIU29n4I6A0e8kEPx/RvuHex93c9Bl9sN9X333S7kspgczaGFXuC00KBoBvctArV/3yC0h8oErepMIFMFKBg==', 1, 0);

-- ----------------------------
-- Table structure for user_info
-- ----------------------------
DROP TABLE IF EXISTS `user_info`;
CREATE TABLE `user_info`  (
  `id` int(10) UNSIGNED NOT NULL COMMENT '成员ID',
  `name` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '名称',
  `sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别（0-保密；1-男；2-女）',
  `avatar` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '头像',
  `area` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '地区',
  `city` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '城市',
  `province` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '省份',
  `country` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '国家',
  `introduction` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '简介',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户表：基本信息' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of user_info
-- ----------------------------
INSERT INTO `user_info` VALUES (1000000000, '系统管理员', 1, '/avatar/90755a7a80f5cd4591ab1ed6eec9379229ddb319.jpg', '360203', '360200', '360000', '86', '我是神');

SET FOREIGN_KEY_CHECKS = 1;
