<?php
//数据监控
include substr(dirname(__FILE__),0,-7).'configs/run.inc.php';

$_group = new GroupAction();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'deleteMsg'){
        $_deleteSucc = $_group->deleteOneMsg($_GPC['deleteid']);
        echo $_deleteSucc ? 1 : 0;  
        exit;
    }
}


$_list = $_group->findAllMsgContent(); 
$_page = $_group->getPage();


include $this->template('w_data'); 