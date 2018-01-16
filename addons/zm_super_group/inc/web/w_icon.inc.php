<?php
//首页图标设置
include substr(dirname(__FILE__),0,-7).'configs/run.inc.php';

$_config = new ConfigAction();
$_icon = new IconAction();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'changeIcon'){
        $_isSucc = $_config->setIndexIcon();
        echo $_isSucc ? 1 : 0;
        exit;
    }
}


if(isset($_GPC['send_icon1'])){
    $_icon->setIcon1() ? message('','referer','succ') : message('设置失败','referer','error');
}

if(isset($_GPC['send_icon2'])){
    $_icon->setIcon2() ? message('','referer','succ') : message('设置失败','referer','error');
}

if(isset($_GPC['send_icon3'])){
    $_icon->setIcon3() ? message('','referer','succ') : message('设置失败','referer','error');
}

if(isset($_GPC['send_icon4'])){
    $_icon->setIcon4() ? message('','referer','succ') : message('设置失败','referer','error');
}


$_params = $_icon->find();

$_param = $_config->findConfig(array('index_icon_status'));

include $this->template('w_icon');