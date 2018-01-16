<?php
//创建社群
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_class = new ClassAction();
$_group = new GroupAction(true);
$_group->register();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'uploadImg'){
        echo $_group->uploadImageA(); 
        exit;
    }
    if($_GPC['doing'] == 'editorGroup'){
        $_status = $_group->update();
        echo $_status ? 1 : 0;
        exit;
    }
}


$_classList = $_class->findAll(); 
$_share = $_group->share();
$_oneGroup = $_group->findOne();

//引入模板
include $this->template('m_editor_group'); 
?>