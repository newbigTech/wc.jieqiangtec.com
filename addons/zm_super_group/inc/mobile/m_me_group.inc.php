<?php
//我的社群
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();


$_member = new MemberAction(true);
$_member->register();
if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'findMeGroup'){
        $_list = $_member->findMeGroup($_GPC['start'],$_GPC['end']);
        echo $_list ? json_encode($_list) : 0;
        exit;
    }
    
    
}


$_fansTotal = $_member->findAttentionTotal($_W['member']['uid']); 
$_share = $_member->share();
//引入模板
include $this->template('m_me_group');
?>