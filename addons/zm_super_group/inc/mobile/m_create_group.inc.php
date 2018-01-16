<?php
//创建社群
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_class = new ClassAction();
$_group = new GroupAction(true);
$_config = new ConfigAction();
$_group->register();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'uploadImg'){
        echo $_group->uploadImageA(); 
        exit;
    }
    if($_GPC['doing'] == 'addGroup'){
        $_status = $_group->add();
        echo $_status ? 1 : 0; 
        exit;
    }
}

 

$_classList = $_class->findAll(); 
$_share = $_group->share();
$_hintText = $_config->findConfig(array('create_group_hint'));
//引入模板
include $this->template('m_create_group');
?>