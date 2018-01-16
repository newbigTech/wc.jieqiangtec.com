<?php
//我的粉丝
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_group = new GroupAction();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'changeShareStatus'){
        echo $_group->changeShareStatus() ? 1 : 0;
        exit;
    }
    
    if($_GPC['doing'] == 'setAwardRatio'){
        echo $_group->setAwardRatio() ? 1 : 0;
        exit;
    }
}

$_param = $_group->findShareAward();




//引入模板
include $this->template('m_set_share'); 
?>