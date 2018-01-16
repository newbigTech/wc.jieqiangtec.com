<?php
//建群审核
include substr(dirname(__FILE__),0,-7).'configs/run.inc.php';

$_group = new GroupAction(true);


if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'findOneGroup'){
        echo $_group->ajaxFindOne();
        exit;
    }
    
    if($_GPC['doing'] == 'checkGroup'){
        echo $_group->ajaxCheck();
        exit;
    }
}

$_groupList = $_group->findAll(2);
$_page = $_group->getPage();

include $this->template('w_check'); 


