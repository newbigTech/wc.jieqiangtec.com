<?php
//会员中心
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_member = new MemberAction(true);
$_m_member = true;

$_member->register();
$_share = $_member->share();
//引入模板
include $this->template('m_member');
?>