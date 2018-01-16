<?php
//创建推广海报
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();
$_poster = new PosterAction();
$_poster->register();

//引入模板
include $this->template('m_create_poster');
?>