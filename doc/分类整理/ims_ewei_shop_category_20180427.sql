/*
Navicat MySQL Data Transfer

Source Server         : 39.106.104.26
Source Server Version : 50720
Source Host           : 39.106.104.26:3506
Source Database       : wc2

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2018-04-27 22:40:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ims_ewei_shop_category
-- ----------------------------
DROP TABLE IF EXISTS `ims_ewei_shop_category`;
CREATE TABLE `ims_ewei_shop_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `parentid` int(11) DEFAULT '0',
  `isrecommand` int(10) DEFAULT '0',
  `description` varchar(500) DEFAULT NULL,
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '1',
  `ishome` tinyint(3) DEFAULT '0',
  `advimg` varchar(255) DEFAULT '',
  `advurl` varchar(500) DEFAULT '',
  `level` tinyint(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_parentid` (`parentid`),
  KEY `idx_isrecommand` (`isrecommand`),
  KEY `idx_ishome` (`ishome`)
) ENGINE=MyISAM AUTO_INCREMENT=1193 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ims_ewei_shop_category
-- ----------------------------
INSERT INTO `ims_ewei_shop_category` VALUES ('1174', '6', '手机', '', '0', '0', '手机', '0', '1', '0', 'images/6/2017/09/qJjG2YfkZJD5KKa6TGg3n3jW32j2J2.jpg', '', '1');
INSERT INTO `ims_ewei_shop_category` VALUES ('1175', '6', '红酒', '', '0', '0', '红酒', '0', '1', '0', '', '', '1');
INSERT INTO `ims_ewei_shop_category` VALUES ('1176', '6', '首饰', '', '0', '0', '首饰', '0', '1', '0', '', '', '1');
INSERT INTO `ims_ewei_shop_category` VALUES ('1177', '6', '华为', '', '1174', '1', '华为', '0', '1', '1', '', '', '2');
INSERT INTO `ims_ewei_shop_category` VALUES ('1178', '6', '小米', '', '1174', '0', '小米', '0', '1', '0', '', '', '2');
INSERT INTO `ims_ewei_shop_category` VALUES ('1179', '6', '苹果', '', '1174', '0', '苹果', '0', '1', '0', '', '', '2');
INSERT INTO `ims_ewei_shop_category` VALUES ('1180', '6', 'Mate系列', '', '1177', '0', 'Mate系列', '0', '1', '0', '', '', '3');
INSERT INTO `ims_ewei_shop_category` VALUES ('1181', '6', 'P系列', '', '1177', '0', 'P系列', '0', '1', '0', '', '', '3');
INSERT INTO `ims_ewei_shop_category` VALUES ('1182', '6', 'G系列', '', '1177', '0', 'G系列', '0', '1', '0', '', '', '3');
INSERT INTO `ims_ewei_shop_category` VALUES ('1183', '6', '美妆个护', '', '0', '0', '美妆个护', '0', '1', '0', '', '', '1');
INSERT INTO `ims_ewei_shop_category` VALUES ('1184', '6', '护肤套装', '', '1183', '1', '护肤套装', '0', '1', '1', '', '', '2');
INSERT INTO `ims_ewei_shop_category` VALUES ('1185', '6', '精华/原液', '', '1183', '1', '精华/原液', '0', '1', '1', '', '', '2');
INSERT INTO `ims_ewei_shop_category` VALUES ('1186', '6', '乳液/面霜', '', '1183', '1', '乳液/面霜', '0', '1', '1', '', '', '2');
INSERT INTO `ims_ewei_shop_category` VALUES ('1187', '6', '虚拟商品', '', '0', '0', '商城礼品卡', '0', '1', '0', '', '', '1');
INSERT INTO `ims_ewei_shop_category` VALUES ('1188', '10', '测试', '', '0', '0', '测试', '0', '1', '0', '', '', '1');
INSERT INTO `ims_ewei_shop_category` VALUES ('1189', '10', '数码', '', '0', '0', '数码', '0', '1', '0', 'images/10/2018/01/lQf4BxKEpO0Ao2Ufeee2eY42BP4BXe.jpg', '', '1');
INSERT INTO `ims_ewei_shop_category` VALUES ('1190', '10', '一级分类', '', '0', '0', '一级分类', '0', '1', '0', 'images/10/2018/04/S8Jv5Mr5oG5aoaM7Pz1XEVeWsE5wza.jpg', '', '1');
INSERT INTO `ims_ewei_shop_category` VALUES ('1191', '10', '二级分类', '', '1190', '1', '二级分类', '0', '1', '1', '', '', '2');
INSERT INTO `ims_ewei_shop_category` VALUES ('1192', '10', '三级分类', 'images/10/2018/04/S8Jv5Mr5oG5aoaM7Pz1XEVeWsE5wza.jpg', '1191', '1', '三级分类', '0', '1', '1', '', '', '3');
