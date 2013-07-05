/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50509
 Source Host           : localhost
 Source Database       : kebap

 Target Server Type    : MySQL
 Target Server Version : 50509
 File Encoding         : utf-8

 Date: 07/05/2013 11:49:12 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `deneme`
-- ----------------------------
DROP TABLE IF EXISTS `deneme`;
CREATE TABLE `deneme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `body` text NOT NULL,
  `language` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `deneme`
-- ----------------------------
BEGIN;
INSERT INTO `deneme` VALUES ('1', 'Soner', null, '', '1'), ('2', 'Tamer', null, '', '1'), ('3', 'Tuna', null, '', '1'), ('4', 'Test', null, '', '1'), ('5', 'John Deep', null, '', '1'), ('8', 'Navicat', '2', '', '1'), ('9', 'Alt Sayfa', '2', '', '1'), ('10', 'Ozan', '2', '', '1'), ('11', 'Tahir', '3', '', '1');
COMMIT;

-- ----------------------------
--  Table structure for `elemanlar`
-- ----------------------------
DROP TABLE IF EXISTS `elemanlar`;
CREATE TABLE `elemanlar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `deneme_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `elemanlar`
-- ----------------------------
BEGIN;
INSERT INTO `elemanlar` VALUES ('1', 'eleman1', '2'), ('2', 'elaman2', '2'), ('3', 'elaman3', '2'), ('4', 'Selim', '1'), ('5', 'Yavuz', '1'), ('6', 'Hasan', '1'), ('7', 'Tuna', '3');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
