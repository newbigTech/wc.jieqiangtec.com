<?php
//收益记录
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_member = new MemberAction(true);
$_member->register();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'findEarningsRecord'){
        
        $_record = $_member->getEarningsRecord($_GPC['start'],$_GPC['end'],'',true);
        echo $_record ? json_encode($_record) : 0;
        exit;
    }
}

$_share = $_member->share();
//引入模板
include $this->template('m_earnings');
?>