<?php
//提现记录
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_member = new MemberAction(true);
$_member->register();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'findDepositRecord'){
        echo json_encode($_member->findDepositRecord($_GPC['start'],$_GPC['end'],'',true));  
        exit;
    }
}


$_share = $_member->share();
//引入模板
include $this->template('m_deposit_record'); 
?>