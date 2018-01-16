<?php
//会员管理
include substr(dirname(__FILE__),0,-7).'configs/run.inc.php';

$_member = new MemberAction();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'changeStatus'){
        $_member->changeMemberStatus() ? message('','referer','succ') : message('','refresh','error');
    }
}

$_memberList = $_member->findAllMember(); 
$_page = $_member->getPage();


include $this->template('w_member');