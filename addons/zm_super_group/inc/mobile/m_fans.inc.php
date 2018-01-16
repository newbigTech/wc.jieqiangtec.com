<?php
//我的粉丝
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_member = new MemberAction(true);
$_member->register();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'ajaxFindFans'){
        $_fans = $_member->findAllFans($_GPC['start'],$_GPC['end']);
        echo $_fans ? json_encode($_fans) : 0;
        exit;
    }
}


$_fansTotal = $_member->findAttentionTotal($_W['member']['uid']);
$_fansList = $_member->findAllFans();

$_share = $_member->share();
//引入模板
include $this->template('m_fans');
?>