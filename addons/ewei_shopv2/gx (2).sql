DROP TABLE IF EXISTS `ims_ewei_shop_task_list`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_list` (
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `title` char(50) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `starttime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `demand` int(11) NOT NULL DEFAULT '0',
  `requiregoods` text NOT NULL,
  `picktype` tinyint(1) NOT NULL DEFAULT '0',
  `stop_type` tinyint(1) NOT NULL DEFAULT '0',
  `stop_limit` int(11) NOT NULL DEFAULT '0',
  `stop_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stop_cycle` tinyint(1) NOT NULL DEFAULT '0',
  `repeat_type` tinyint(1) NOT NULL DEFAULT '0',
  `repeat_interval` int(11) NOT NULL DEFAULT '0',
  `repeat_cycle` tinyint(1) NOT NULL DEFAULT '0',
  `reward` text NOT NULL,
  `followreward` text NOT NULL,
  `goods_limit` int(11) NOT NULL DEFAULT '0',
  `notice` text NOT NULL,
  `design_data` text NOT NULL,
  `design_bg` varchar(255) NOT NULL DEFAULT '',
  `native_data` text NOT NULL,
  `native_data2` text,
  `native_data3` text,
  `reward2` text,
  `reward3` text,
  `level2` int(11) NOT NULL DEFAULT '0',
  `level3` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_passive` (`picktype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `ims_ewei_shop_task_log`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(100) NOT NULL DEFAULT '',
  `from_openid` varchar(100) NOT NULL DEFAULT '',
  `join_id` int(11) NOT NULL DEFAULT '0',
  `taskid` int(11) DEFAULT '0',
  `task_type` tinyint(1) NOT NULL DEFAULT '0',
  `subdata` text,
  `recdata` text,
  `createtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ims_ewei_shop_task_poster`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_poster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `days` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `bg` varchar(255) DEFAULT '',
  `data` text,
  `keyword` varchar(255) DEFAULT NULL,
  `resptype` tinyint(1) NOT NULL DEFAULT '0',
  `resptext` text,
  `resptitle` varchar(255) DEFAULT NULL,
  `respthumb` varchar(255) DEFAULT NULL,
  `respdesc` varchar(255) DEFAULT NULL,
  `respurl` varchar(255) DEFAULT NULL,
  `createtime` int(11) DEFAULT NULL,
  `waittext` varchar(255) DEFAULT NULL,
  `oktext` varchar(255) DEFAULT NULL,
  `scantext` varchar(255) DEFAULT NULL,
  `beagent` tinyint(1) NOT NULL DEFAULT '0',
  `bedown` tinyint(1) NOT NULL DEFAULT '0',
  `timestart` int(11) DEFAULT NULL,
  `timeend` int(11) DEFAULT NULL,
  `is_repeat` tinyint(1) DEFAULT '0',
  `getposter` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `starttext` varchar(255) DEFAULT NULL,
  `endtext` varchar(255) DEFAULT NULL,
  `reward_data` text,
  `needcount` int(11) NOT NULL DEFAULT '0',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `poster_type` tinyint(1) DEFAULT '1',
  `reward_days` int(11) DEFAULT '0',
  `titleicon` text,
  `poster_banner` text,
  `is_goods` tinyint(1) DEFAULT '0',
  `autoposter` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ims_ewei_shop_task_poster_qr`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_poster_qr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acid` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(100) NOT NULL,
  `posterid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `sceneid` int(11) NOT NULL DEFAULT '0',
  `mediaid` varchar(255) DEFAULT NULL,
  `ticket` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `createtime` int(11) DEFAULT NULL,
  `qrimg` varchar(1000) DEFAULT NULL,
  `expire` int(11) DEFAULT NULL,
  `endtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ims_ewei_shop_task_qr`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_qr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(100) NOT NULL DEFAULT '',
  `recordid` int(11) NOT NULL DEFAULT '0',
  `sceneid` varchar(255) NOT NULL DEFAULT '',
  `mediaid` varchar(255) NOT NULL DEFAULT '',
  `ticket` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `recordid` (`recordid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ims_ewei_shop_task_record`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `taskid` int(11) NOT NULL DEFAULT '0',
  `tasktitle` varchar(255) NOT NULL,
  `taskimage` varchar(255) NOT NULL DEFAULT '',
  `tasktype` varchar(50) NOT NULL DEFAULT '',
  `task_progress` int(11) NOT NULL DEFAULT '0',
  `task_demand` int(11) NOT NULL DEFAULT '0',
  `openid` char(50) NOT NULL DEFAULT '',
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `picktime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stoptime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `finishtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reward_data` text NOT NULL,
  `followreward_data` text NOT NULL,
  `design_data` text NOT NULL,
  `design_bg` varchar(255) NOT NULL DEFAULT '',
  `require_goods` varchar(255) NOT NULL DEFAULT '',
  `level1` int(11) NOT NULL DEFAULT '0',
  `reward_data1` text NOT NULL,
  `level2` int(11) NOT NULL DEFAULT '0',
  `reward_data2` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `taskid` (`taskid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ims_ewei_shop_task_reward`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `taskid` int(11) NOT NULL DEFAULT '0',
  `tasktitle` char(50) NOT NULL DEFAULT '',
  `tasktype` varchar(50) NOT NULL DEFAULT '',
  `taskowner` char(50) NOT NULL DEFAULT '',
  `ownernickname` char(50) NOT NULL DEFAULT '',
  `recordid` int(11) NOT NULL DEFAULT '0',
  `nickname` char(50) NOT NULL DEFAULT '',
  `headimg` varchar(255) NOT NULL DEFAULT '',
  `openid` char(50) NOT NULL DEFAULT '',
  `reward_type` char(10) NOT NULL DEFAULT '',
  `reward_title` char(50) NOT NULL DEFAULT '',
  `reward_data` decimal(10,2) NOT NULL DEFAULT '0.00',
  `get` tinyint(1) NOT NULL DEFAULT '0',
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `gettime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `senttime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isjoiner` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `level` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `recordid` (`recordid`),
  KEY `taskid` (`taskid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `ims_ewei_shop_task_set`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_set` (
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `entrance` tinyint(1) NOT NULL DEFAULT '0',
  `keyword` varchar(10) NOT NULL DEFAULT '',
  `cover_title` varchar(20) NOT NULL DEFAULT '',
  `cover_img` varchar(255) NOT NULL DEFAULT '',
  `cover_desc` varchar(255) NOT NULL DEFAULT '',
  `msg_pick` text NOT NULL,
  `msg_progress` text NOT NULL,
  `msg_finish` text NOT NULL,
  `msg_follow` text NOT NULL,
  `isnew` tinyint(1) NOT NULL DEFAULT '0',
  `bg_img` varchar(255) NOT NULL DEFAULT '../addons/ewei_shopv2/plugin/task/static/images/sky.png',
  PRIMARY KEY (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



INSERT INTO `ims_ewei_shop_task_set` (`uniacid`, `entrance`, `keyword`, `cover_title`, `cover_img`, `cover_desc`, `msg_pick`, `msg_progress`, `msg_finish`, `msg_follow`, `isnew`, `bg_img`) VALUES
(1, 0, '', '', '', '', '', '', '', '', 1, '../addons/ewei_shopv2/plugin/task/static/images/sky.png');

DROP TABLE IF EXISTS `ims_ewei_shop_task_type`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_type` (
  `id` int(11) NOT NULL,
  `type_key` char(20) NOT NULL DEFAULT '',
  `type_name` char(10) NOT NULL DEFAULT '',
  `description` char(30) NOT NULL DEFAULT '',
  `verb` char(11) NOT NULL DEFAULT '',
  `numeric` tinyint(1) NOT NULL DEFAULT '0',
  `unit` char(10) NOT NULL DEFAULT '',
  `goods` tinyint(1) NOT NULL DEFAULT '0',
  `theme` char(10) NOT NULL DEFAULT '',
  `once` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `ims_ewei_shop_task_type` (`id`, `type_key`, `type_name`, `description`, `verb`, `numeric`, `unit`, `goods`, `theme`, `once`) VALUES
(1, 'poster', '任务海报', '把生成的海报并分享给朋友，朋友扫描并关注公众号即可获得奖励。', '转发海报并吸引', 1, '人关注', 0, 'primary', 0),
(2, 'info_phone', '绑定手机', '在个人中心中，绑定手机号，即可完成任务获得奖励。', '绑定手机', 0, '', 0, 'warning', 0),
(3, 'order_first', '首次购物', '在商城中首次下单，即可获得奖励，必须确认收货。', '首次在商城中下单购物', 0, '', 0, 'warning', 0),
(4, 'recharge_full', '单笔充值满额', '在商城中充值余额，单笔充值满额，即可获得奖励。', '单笔充值满', 1, '元', 0, 'success', 1),
(5, 'order_full', '单笔满额', '在商城中下单，单笔满额即可获得奖励，必须确认收货。', '单笔订单满', 1, '元', 0, 'success', 1),
(6, 'order_all', '累计消费', '在商城中购物消费，累计满额即可获得奖励，无需确认收货。', '购物总消费额达到', 1, '元', 0, 'success', 0),
(7, 'pyramid_money', '分销佣金', '只有分销商可接此任务。累计分销佣金满额，即可完成任务。', '分销商获得佣金金额达', 1, '元', 0, 'primary', 0),
(8, 'pyramid_num', '下级人数', '只有分销商可接此任务。累计下级人数达标，即可完成任务。', '分销商推荐下级人数达', 1, '人', 0, 'primary', 0),
(9, 'comment', '商品好评', '任意给一个商品五星好评，即可完成任务获得奖励。', '给商品好评', 0, '', 0, 'warning', 0),
(10, 'post', '社区发帖', '在社区中发表指定篇帖子，即可完成任务获得奖励。', '在论坛中发表', 1, '篇帖子', 0, 'warning', 0),
(11, 'goods', '购买指定商品', '购买指定商品后即可完成任务，必须确认收货。', '购买指定商品', 0, '', 1, 'info', 0),
(12, 'recharge_count', '累计充值满额', '在商城中充值余额，累计充值满额，即可获得奖励。', '累计充值满', 1, '元', 0, 'success', 0);