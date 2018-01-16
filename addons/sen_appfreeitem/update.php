<?php
pdo_query("CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `openid` varchar(50) NOT NULL,
  `realname` varchar(20) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `province` varchar(30) NOT NULL,
  `city` varchar(30) NOT NULL,
  `area` varchar(30) NOT NULL,
  `address` varchar(300) NOT NULL,
  `isdefault` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_adv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `indx_weid` (`weid`),
  KEY `indx_enabled` (`enabled`),
  KEY `indx_displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `projectid` int(11) NOT NULL,
  `from_user` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_openid` (`from_user`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(1000) NOT NULL DEFAULT '' COMMENT '分类名称',
  `thumb` varchar(1000) NOT NULL DEFAULT '' COMMENT '分类图片',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `isrecommand` int(10) DEFAULT '0',
  `answer` varchar(500) NOT NULL DEFAULT '' COMMENT '答案',
  `fenshu` float(7,2) DEFAULT NULL COMMENT '分数',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_dispatch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `dispatchname` varchar(50) DEFAULT '',
  `dispatchtype` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `firstprice` decimal(10,2) DEFAULT '0.00',
  `secondprice` decimal(10,2) DEFAULT '0.00',
  `firstweight` int(11) DEFAULT '0',
  `secondweight` int(11) DEFAULT '0',
  `express` int(11) DEFAULT '0',
  `description` text,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启',
  PRIMARY KEY (`id`),
  KEY `indx_weid` (`weid`),
  KEY `indx_displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_express` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `express_name` varchar(50) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `express_price` varchar(10) DEFAULT '',
  `express_area` varchar(100) DEFAULT '',
  `express_url` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `indx_weid` (`weid`),
  KEY `indx_displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `openid` varchar(50) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1为维权，2为告擎',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态0未解决，1用户同意，2用户拒绝',
  `feedbackid` varchar(30) NOT NULL COMMENT '投诉单号',
  `transid` varchar(30) NOT NULL COMMENT '订单号',
  `reason` varchar(1000) NOT NULL COMMENT '理由',
  `solution` varchar(1000) NOT NULL COMMENT '期待解决方案',
  `remark` varchar(1000) NOT NULL COMMENT '备注',
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_weid` (`weid`),
  KEY `idx_feedbackid` (`feedbackid`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_transid` (`transid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `from_user` varchar(50) NOT NULL,
  `ordersn` varchar(20) NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `price` varchar(10) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '-1取消状态，0普通状态，1为已付款，2为已发货，3为成功',
  `state` tinyint(3) DEFAULT NULL,
  `sendtype` tinyint(1) unsigned NOT NULL COMMENT '1为快递，2为自提',
  `paytype` tinyint(1) unsigned NOT NULL COMMENT '1为余额，2为在线，3为到付',
  `transid` varchar(30) NOT NULL DEFAULT '0' COMMENT '微信支付单号',
  `return_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `remark` varchar(1000) NOT NULL DEFAULT '',
  `addressid` int(10) unsigned NOT NULL,
  `expresscom` varchar(30) NOT NULL DEFAULT '',
  `expresssn` varchar(50) NOT NULL DEFAULT '',
  `express` varchar(200) NOT NULL DEFAULT '',
  `item_price` decimal(10,2) DEFAULT '0.00',
  `dispatchprice` decimal(10,2) DEFAULT '0.00',
  `dispatch` int(10) DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL,
  `Answer` text,
  `shouhuodata` varchar(255) DEFAULT NULL,
  `fenshu` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_name` (`from_user`),
  KEY `from_user` (`from_user`),
  KEY `fenshu` (`fenshu`)
) ENGINE=MyISAM AUTO_INCREMENT=14046 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_order_ws` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned DEFAULT NULL,
  `from_user` varchar(50) DEFAULT NULL,
  `nickname` varchar(20) NOT NULL DEFAULT '',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `ordersn` int(10) unsigned DEFAULT NULL,
  `price` decimal(10,2) unsigned DEFAULT NULL,
  `paytype` varchar(10) DEFAULT NULL,
  `transid` varchar(20) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `pid` int(10) unsigned DEFAULT NULL,
  `createtime` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_project` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `from_user` varchar(50) DEFAULT NULL,
  `displayorder` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `cpnumber` int(10) DEFAULT '0',
  `donenum` int(10) unsigned NOT NULL DEFAULT '0',
  `myprice` int(11) unsigned DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `finish_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `starttime` int(10) unsigned DEFAULT '0',
  `deal_days` int(10) unsigned NOT NULL,
  `tjqian` int(11) unsigned DEFAULT NULL,
  `tjhou` int(11) unsigned DEFAULT NULL,
  `ishot` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isrecommand` tinyint(1) unsigned DEFAULT '0',
  `pcate` int(10) unsigned NOT NULL DEFAULT '0',
  `ccate` int(10) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(255) DEFAULT '',
  `content` text NOT NULL,
  `nosubuser` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0暂停1正常2停止',
  `wtname` varchar(10000) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `subsurl` varchar(500) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `direct` tinyint(1) NOT NULL DEFAULT '0',
  `show_type` int(10) DEFAULT '0',
  `type` tinyint(10) DEFAULT '0',
  `lianxiren` varchar(20) DEFAULT '',
  `tel` int(10) DEFAULT NULL,
  `fenshu` int(11) DEFAULT NULL,
  `share_title` varchar(255) DEFAULT NULL,
  `share_img` varchar(255) DEFAULT NULL,
  `share_content` varchar(255) DEFAULT NULL,
  `share_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_project_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `displayorder` int(10) unsigned NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` varchar(2000) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `limit_num` int(10) unsigned NOT NULL,
  `donenum` int(10) unsigned NOT NULL DEFAULT '0',
  `repaid_day` int(10) unsigned NOT NULL,
  `return_type` tinyint(1) unsigned NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `dispatch` int(10) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT NULL,
  `rtitle` varchar(255) DEFAULT NULL,
  `from_user` varchar(255) DEFAULT NULL,
  `oid` int(11) DEFAULT NULL,
  `pid` int(10) unsigned DEFAULT NULL,
  `content` text,
  `tijiaotime` datetime DEFAULT NULL,
  `is_display` tinyint(1) unsigned DEFAULT NULL,
  `images` varchar(10000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_report_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `displayorder` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_rule` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `wid` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sen_appfreeitem_share` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uinacid` int(10) unsigned NOT NULL,
  `reply_id` int(10) unsigned NOT NULL,
  `share_from` varchar(50) NOT NULL,
  `share_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
");
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `weid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `openid` varchar(50) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'realname')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `realname` varchar(20) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'mobile')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `mobile` varchar(11) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'province')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `province` varchar(30) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'city')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `city` varchar(30) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'area')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `area` varchar(30) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'address')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `address` varchar(300) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'isdefault')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `isdefault` tinyint(3) unsigned NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_address')) {
	if(!pdo_fieldexists('sen_appfreeitem_address',  'deleted')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_address')." ADD `deleted` tinyint(3) unsigned NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_adv')) {
	if(!pdo_fieldexists('sen_appfreeitem_adv',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_adv')." ADD `id` int(11) NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_adv')) {
	if(!pdo_fieldexists('sen_appfreeitem_adv',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_adv')." ADD `weid` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_adv')) {
	if(!pdo_fieldexists('sen_appfreeitem_adv',  'advname')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_adv')." ADD `advname` varchar(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_adv')) {
	if(!pdo_fieldexists('sen_appfreeitem_adv',  'link')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_adv')." ADD `link` varchar(255) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_adv')) {
	if(!pdo_fieldexists('sen_appfreeitem_adv',  'thumb')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_adv')." ADD `thumb` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_adv')) {
	if(!pdo_fieldexists('sen_appfreeitem_adv',  'displayorder')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_adv')." ADD `displayorder` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_adv')) {
	if(!pdo_fieldexists('sen_appfreeitem_adv',  'enabled')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_adv')." ADD `enabled` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_cart')) {
	if(!pdo_fieldexists('sen_appfreeitem_cart',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_cart')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_cart')) {
	if(!pdo_fieldexists('sen_appfreeitem_cart',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_cart')." ADD `weid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_cart')) {
	if(!pdo_fieldexists('sen_appfreeitem_cart',  'projectid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_cart')." ADD `projectid` int(11) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_cart')) {
	if(!pdo_fieldexists('sen_appfreeitem_cart',  'from_user')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_cart')." ADD `from_user` varchar(50) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `weid` int(10) unsigned NOT NULL  DEFAULT 0 COMMENT '所属帐号';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'name')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `name` varchar(1000) NOT NULL   COMMENT '分类名称';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'thumb')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `thumb` varchar(1000) NOT NULL   COMMENT '分类图片';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'parentid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `parentid` int(10) unsigned NOT NULL  DEFAULT 0 COMMENT '上级分类ID,0为第一级';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'isrecommand')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `isrecommand` int(10)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'answer')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `answer` varchar(500) NOT NULL   COMMENT '答案';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'fenshu')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `fenshu` float(7,2)    COMMENT '分数';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'displayorder')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `displayorder` tinyint(3) unsigned NOT NULL  DEFAULT 0 COMMENT '排序';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_category',  'enabled')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_category')." ADD `enabled` tinyint(1) unsigned NOT NULL  DEFAULT 1 COMMENT '是否开启';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `id` int(11) NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `weid` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'dispatchname')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `dispatchname` varchar(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'dispatchtype')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `dispatchtype` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'displayorder')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `displayorder` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'firstprice')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `firstprice` decimal(10,2)   DEFAULT 0.00 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'secondprice')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `secondprice` decimal(10,2)   DEFAULT 0.00 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'firstweight')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `firstweight` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'secondweight')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `secondweight` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'express')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `express` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'description')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `description` text    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_dispatch')) {
	if(!pdo_fieldexists('sen_appfreeitem_dispatch',  'enabled')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_dispatch')." ADD `enabled` tinyint(1) unsigned NOT NULL  DEFAULT 1 COMMENT '是否开启';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_express')) {
	if(!pdo_fieldexists('sen_appfreeitem_express',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_express')." ADD `id` int(11) NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_express')) {
	if(!pdo_fieldexists('sen_appfreeitem_express',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_express')." ADD `weid` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_express')) {
	if(!pdo_fieldexists('sen_appfreeitem_express',  'express_name')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_express')." ADD `express_name` varchar(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_express')) {
	if(!pdo_fieldexists('sen_appfreeitem_express',  'displayorder')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_express')." ADD `displayorder` int(11)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_express')) {
	if(!pdo_fieldexists('sen_appfreeitem_express',  'express_price')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_express')." ADD `express_price` varchar(10)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_express')) {
	if(!pdo_fieldexists('sen_appfreeitem_express',  'express_area')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_express')." ADD `express_area` varchar(100)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_express')) {
	if(!pdo_fieldexists('sen_appfreeitem_express',  'express_url')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_express')." ADD `express_url` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `weid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `openid` varchar(50) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'type')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `type` tinyint(1) unsigned NOT NULL  DEFAULT 1 COMMENT '1为维权，2为告擎';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'status')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `status` tinyint(1) NOT NULL  DEFAULT 0 COMMENT '状态0未解决，1用户同意，2用户拒绝';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'feedbackid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `feedbackid` varchar(30) NOT NULL   COMMENT '投诉单号';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'transid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `transid` varchar(30) NOT NULL   COMMENT '订单号';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'reason')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `reason` varchar(1000) NOT NULL   COMMENT '理由';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'solution')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `solution` varchar(1000) NOT NULL   COMMENT '期待解决方案';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'remark')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `remark` varchar(1000) NOT NULL   COMMENT '备注';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_feedback')) {
	if(!pdo_fieldexists('sen_appfreeitem_feedback',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_feedback')." ADD `createtime` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `weid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'from_user')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `from_user` varchar(50) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'ordersn')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `ordersn` varchar(20) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'pid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `pid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'item_id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `item_id` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'price')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `price` varchar(10) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'status')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `status` tinyint(4) NOT NULL  DEFAULT 0 COMMENT '-1取消状态，0普通状态，1为已付款，2为已发货，3为成功';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'state')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `state` tinyint(3)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'sendtype')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `sendtype` tinyint(1) unsigned NOT NULL   COMMENT '1为快递，2为自提';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'paytype')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `paytype` tinyint(1) unsigned NOT NULL   COMMENT '1为余额，2为在线，3为到付';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'transid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `transid` varchar(30) NOT NULL  DEFAULT 0 COMMENT '微信支付单号';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'return_type')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `return_type` tinyint(1) unsigned NOT NULL  DEFAULT 1 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'remark')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `remark` varchar(1000) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'addressid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `addressid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'expresscom')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `expresscom` varchar(30) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'expresssn')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `expresssn` varchar(50) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'express')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `express` varchar(200) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'item_price')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `item_price` decimal(10,2)   DEFAULT 0.00 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'dispatchprice')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `dispatchprice` decimal(10,2)   DEFAULT 0.00 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'dispatch')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `dispatch` int(10)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `createtime` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'Answer')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `Answer` text    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'shouhuodata')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `shouhuodata` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order')) {
	if(!pdo_fieldexists('sen_appfreeitem_order',  'fenshu')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order')." ADD `fenshu` int(11)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `weid` int(10) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'from_user')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `from_user` varchar(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'nickname')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `nickname` varchar(20) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'avatar')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `avatar` varchar(255) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'ordersn')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `ordersn` int(10) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'price')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `price` decimal(10,2) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'paytype')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `paytype` varchar(10)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'transid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `transid` varchar(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'status')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `status` tinyint(1) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'remark')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `remark` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'pid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `pid` int(10) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_order_ws')) {
	if(!pdo_fieldexists('sen_appfreeitem_order_ws',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_order_ws')." ADD `createtime` int(10) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `weid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'from_user')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `from_user` varchar(50)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'displayorder')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `displayorder` int(10) unsigned NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'title')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `title` varchar(100) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'cpnumber')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `cpnumber` int(10)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'donenum')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `donenum` int(10) unsigned NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'myprice')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `myprice` int(11) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'price')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `price` decimal(10,2) NOT NULL  DEFAULT 0.00 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'finish_price')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `finish_price` decimal(10,2) NOT NULL  DEFAULT 0.00 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'starttime')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `starttime` int(10) unsigned   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'deal_days')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `deal_days` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'tjqian')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `tjqian` int(11) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'tjhou')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `tjhou` int(11) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'ishot')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `ishot` tinyint(1) unsigned NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'isrecommand')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `isrecommand` tinyint(1) unsigned   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'pcate')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `pcate` int(10) unsigned NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'ccate')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `ccate` int(10) unsigned NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'thumb')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `thumb` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'content')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `content` text NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'nosubuser')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `nosubuser` tinyint(1) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'status')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `status` tinyint(1) unsigned NOT NULL  DEFAULT 0 COMMENT '0暂停1正常2停止';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'wtname')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `wtname` varchar(10000)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'reason')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `reason` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `createtime` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'subsurl')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `subsurl` varchar(500)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'url')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `url` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'direct')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `direct` tinyint(1) NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'show_type')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `show_type` int(10)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'type')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `type` tinyint(10)   DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'lianxiren')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `lianxiren` varchar(20)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'tel')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `tel` int(10)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'fenshu')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `fenshu` int(11)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'share_title')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `share_title` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'share_img')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `share_img` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'share_content')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `share_content` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project')) {
	if(!pdo_fieldexists('sen_appfreeitem_project',  'share_url')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project')." ADD `share_url` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `weid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'displayorder')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `displayorder` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'pid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `pid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'price')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `price` decimal(10,2) NOT NULL  DEFAULT 0.00 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'description')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `description` varchar(2000) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'thumb')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `thumb` varchar(255) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'limit_num')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `limit_num` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'donenum')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `donenum` int(10) unsigned NOT NULL  DEFAULT 0 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'repaid_day')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `repaid_day` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'return_type')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `return_type` tinyint(1) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'delivery_fee')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `delivery_fee` decimal(10,2) NOT NULL  DEFAULT 0.00 COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'dispatch')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `dispatch` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_project_item')) {
	if(!pdo_fieldexists('sen_appfreeitem_project_item',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_project_item')." ADD `createtime` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `weid` int(11)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'rtitle')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `rtitle` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'from_user')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `from_user` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'oid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `oid` int(11)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'pid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `pid` int(10) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'content')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `content` text    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'tijiaotime')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `tijiaotime` datetime    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'is_display')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `is_display` tinyint(1) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report')) {
	if(!pdo_fieldexists('sen_appfreeitem_report',  'images')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report')." ADD `images` varchar(10000)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_report_category',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report_category')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_report_category',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report_category')." ADD `uniacid` int(10) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_report_category',  'title')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report_category')." ADD `title` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_report_category')) {
	if(!pdo_fieldexists('sen_appfreeitem_report_category',  'displayorder')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_report_category')." ADD `displayorder` int(10) unsigned    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_rule')) {
	if(!pdo_fieldexists('sen_appfreeitem_rule',  'Id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_rule')." ADD `Id` int(11) NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_rule')) {
	if(!pdo_fieldexists('sen_appfreeitem_rule',  'wid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_rule')." ADD `wid` int(11)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_rule')) {
	if(!pdo_fieldexists('sen_appfreeitem_rule',  'content')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_rule')." ADD `content` varchar(255)    COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_share')) {
	if(!pdo_fieldexists('sen_appfreeitem_share',  'id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_share')." ADD `id` int(10) unsigned NOT NULL auto_increment  COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_share')) {
	if(!pdo_fieldexists('sen_appfreeitem_share',  'uinacid')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_share')." ADD `uinacid` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_share')) {
	if(!pdo_fieldexists('sen_appfreeitem_share',  'reply_id')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_share')." ADD `reply_id` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_share')) {
	if(!pdo_fieldexists('sen_appfreeitem_share',  'share_from')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_share')." ADD `share_from` varchar(50) NOT NULL   COMMENT '';");
	}	
}
if(pdo_tableexists('sen_appfreeitem_share')) {
	if(!pdo_fieldexists('sen_appfreeitem_share',  'share_time')) {
		pdo_query("ALTER TABLE ".tablename('sen_appfreeitem_share')." ADD `share_time` int(10) unsigned NOT NULL   COMMENT '';");
	}	
}
