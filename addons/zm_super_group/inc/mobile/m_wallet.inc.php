<?php
//我的钱包
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error'); 
checkauth();


$_member = new MemberAction(true);
$_config = new ConfigAction();

$_member->register(); 
$_historyEarnings = $_member->getTotalEarnings();
$_todayEarnings = $_member->getTodayEarning();
$_depositEarnings = $_member->getMemberInfo(array('money'));
$_share = $_member->share();
$_hintText = $_config->findConfig(array('wallet_hint'));
//引入模板
include $this->template('m_wallet'); 
?>