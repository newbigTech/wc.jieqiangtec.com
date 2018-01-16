<?php
//浏览过的群
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();


$_group = new GroupAction(true);
$_group->register();
if(isset($_GPC['doing']) && $_GPC['doing'] == 'ajaxFindBrowseRecord'){
    $_record = $_group->findBrowseRecord($_GPC['start'],$_GPC['end']);
    echo $_record ? json_encode($_record) : 0; 
    exit;
}
$_recordList = $_group->findBrowseRecord();


$_share = $_group->share();
//引入模板
include $this->template('m_browse_record');
?>