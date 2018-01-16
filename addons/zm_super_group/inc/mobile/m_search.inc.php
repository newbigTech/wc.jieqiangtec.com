<?php
//搜索页面
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();


$_group = new GroupAction(true);
if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'search'){
        $_list = $_group->searchGroup();
        echo $_list ? json_encode($_list) : 0;
        exit;
    }
}
$_share = $_group->share();
$_group->register();
//引入模板
include $this->template('m_search');  
?>