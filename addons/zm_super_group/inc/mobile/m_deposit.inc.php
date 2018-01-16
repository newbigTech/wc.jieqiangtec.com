<?php
//提现
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_member = new MemberAction(true);
$_config = new ConfigAction();
$_member->register();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'deposit'){
        echo json_encode($_member->deposit());
        exit;
    }
}

$_param = $_config->findConfig(array('deposit_charge','deposit_limit'));   
$_depositMoney = $_member->getMemberInfo(array('money'));
$_share = $_member->share();
//引入模板
include $this->template('m_deposit');
?>