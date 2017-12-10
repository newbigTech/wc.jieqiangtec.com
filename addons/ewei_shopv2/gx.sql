CREATE TABLE IF NOT EXISTS `ims_ewei_shop_exhelper_esheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `express` varchar(50) DEFAULT '',
  `code` varchar(20) NOT NULL DEFAULT '',
  `datas` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `ims_ewei_shop_exhelper_senduser` ADD COLUMN `province` varchar(30) NOT NULL DEFAULT '';
ALTER TABLE `ims_ewei_shop_exhelper_senduser` ADD COLUMN `city` varchar(30) NOT NULL DEFAULT '';
ALTER TABLE `ims_ewei_shop_exhelper_senduser` ADD COLUMN `area` varchar(30) NOT NULL DEFAULT '';

ALTER TABLE `ims_ewei_shop_exhelper_sys` ADD COLUMN `ebusiness` varchar(20) NOT NULL DEFAULT '';
ALTER TABLE `ims_ewei_shop_exhelper_sys` ADD COLUMN `apikey` varchar(50) NOT NULL DEFAULT '';



CREATE TABLE IF NOT EXISTS `ims_ewei_shop_exhelper_esheet_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `esheetid` int(11) NOT NULL DEFAULT '0',
  `esheetname` varchar(255) NOT NULL DEFAULT '',
  `customername` varchar(50) NOT NULL DEFAULT '',
  `customerpwd` varchar(50) NOT NULL DEFAULT '',
  `monthcode` varchar(50) NOT NULL DEFAULT '',
  `sendsite` varchar(50) NOT NULL DEFAULT '',
  `paytype` tinyint(3) NOT NULL DEFAULT '1',
  `templatesize` varchar(10) NOT NULL DEFAULT '',
  `isnotice` tinyint(3) NOT NULL DEFAULT '0',
  `merchid` int(11) NOT NULL DEFAULT '0',
  `issend` tinyint(3) NOT NULL DEFAULT '1',
  `isdefault` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_isdefault` (`isdefault`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `ims_ewei_shop_exhelper_esheet` (`id`, `name`, `express`, `code`, `datas`) VALUES
(1, '顺丰', '', 'SF', 'a:2:{i:0;a:4:{s:5:"style";s:9:"二联150";s:4:"spec";s:33:"（宽100mm高150mm切点90/60）";s:4:"size";s:3:"150";s:9:"isdefault";i:1;}i:1;a:4:{s:5:"style";s:9:"三联210";s:4:"spec";s:38:"（宽100mm 高210mm 切点90/60/60）";s:4:"size";s:3:"210";s:9:"isdefault";i:0;}}'),
(2, '百世快递', '', 'HTKY', 'a:2:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:0;}i:1;a:4:{s:5:"style";s:9:"二联183";s:4:"spec";s:37:"（宽100mm 高183mm 切点87/5/91）";s:4:"size";s:3:"183";s:9:"isdefault";i:1;}}'),
(3, '韵达', '', 'YD', 'a:2:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:0;}i:1;a:4:{s:5:"style";s:9:"二联203";s:4:"spec";s:36:"（宽100mm 高203mm 切点152/51）";s:4:"size";s:3:"203";s:9:"isdefault";i:1;}}'),
(4, '申通', '', 'STO', 'a:2:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}i:1;a:4:{s:5:"style";s:9:"二联150";s:4:"spec";s:35:"（宽100mm 高150mm 切点90/60）";s:4:"size";s:3:"150";s:9:"isdefault";i:0;}}'),
(5, '圆通', '', 'YTO', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}}'),
(6, 'EMS', '', 'EMS', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联150";s:4:"spec";s:33:"（宽100mm高150mm切点90/60）";s:4:"size";s:3:"150";s:9:"isdefault";i:1;}}'),
(7, '中通', '', 'ZTO', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}}'),
(8, '德邦', '', 'DBL', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联177";s:4:"spec";s:34:"（宽100mm高177mm切点107/70）";s:4:"size";s:3:"177";s:9:"isdefault";i:1;}}'),
(9, '优速', '', 'UC', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}}'),
(10, '宅急送', '', 'ZJS', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联120";s:4:"spec";s:33:"（宽100mm高116mm切点98/10）";s:4:"size";s:3:"120";s:9:"isdefault";i:1;}}'),
(11, '京东', '', 'JD', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联110";s:4:"spec";s:33:"（宽100mm高110mm切点60/50）";s:4:"size";s:3:"110";s:9:"isdefault";i:1;}}'),
(12, '信丰', '', 'XFEX', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联150";s:4:"spec";s:33:"（宽100mm高150mm切点90/60）";s:4:"size";s:3:"150";s:9:"isdefault";i:1;}}'),
(13, '全峰', '', 'QFKD', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}}'),
(14, '跨越速运', '', 'KYSY', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联137";s:4:"spec";s:34:"（宽100mm高137mm切点101/36）";s:4:"size";s:3:"137";s:9:"isdefault";i:1;}}'),
(15, '安能', '', 'ANE', 'a:1:{i:0;a:4:{s:5:"style";s:9:"三联180";s:4:"spec";s:37:"（宽100mm高180mm切点110/30/40）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}}'),
(16, '快捷', '', 'FAST', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}}'),
(17, '国通', '', 'GTO', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}}'),
(18, '天天', '', 'HHTT', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}}'),
(19, '中铁快运', '', 'ZTKY', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联150";s:4:"spec";s:33:"（宽100mm高150mm切点90/60）";s:4:"size";s:3:"150";s:9:"isdefault";i:1;}}'),
(20, '邮政快递包裹', '', 'YZPY', 'a:1:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:1;}}');

