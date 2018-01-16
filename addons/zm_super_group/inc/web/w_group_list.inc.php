<?php
//社群列表
include substr(dirname(__FILE__),0,-7).'configs/run.inc.php';

$_group = new GroupAction(true);
$_class = new ClassAction();

if(isset($_GPC['send_add_group'])){
    $_group->add() ? message('','refresh','succ') : message('新增社群失败！','referer','error');
}

if(isset($_GPC['send_update_group'])){
    $_group->update() ? message('','refresh','succ') : message('修改社群失败！','referer','error');
}

if(isset($_GPC['doing']) && $_GPC['doing'] == 'searchUser'){
    $_member = new MemberAction();
    echo $_member->ajaxSearchNickname();
    exit;
}

if(isset($_GPC['doing']) && $_GPC['doing'] == 'delete'){
    $_group->delete() ? message('',$this->createWebUrl('w_group_list'),'succ') : message('解散群组失败！','referer','error');
    exit;
} 

if(isset($_GPC['doing']) && $_GPC['doing'] == 'oneGroup'){
    echo $_group->ajaxFindOne();
    exit;
}

if(isset($_GPC['doing']) && $_GPC['doing'] == 'setRecommend'){
    echo $_group->setRecommend();
    exit; 
}

if(isset($_GPC['doing']) && $_GPC['doing'] == 'setBoutique'){
    echo $_group->setBoutique();
    exit; 
} 



$_classList = $_class->findAll();
$_groupList = $_group->findAll();
$_page = $_group->getPage();

include $this->template('w_group_list');


