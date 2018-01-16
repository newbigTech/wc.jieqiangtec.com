<?php
//已购
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_bought = true;
$_member = new MemberAction(true);
$_member->register();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'findBought'){ 
        $_record = $_member->getBought($_GPC['start'],$_GPC['end'],'',true);
        echo $_record ? json_encode($_record) : 0;
        exit;
    }
}
$_share = $_member->share();
//引入模板
include $this->template('m_bought');
?>