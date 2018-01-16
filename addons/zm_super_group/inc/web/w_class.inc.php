<?php
//群分类管理
include substr(dirname(__FILE__),0,-7).'configs/run.inc.php';

$_class = new ClassAction();

if(isset($_GPC['send_add_class'])){
    if($_class->nameExists()) message('分类名称已存在！','referer','error');
    $_class->add() ? message('','refresh','succ') : message('添加失败！','referer','error');
}

if(isset($_GPC['send_update_class'])){
    if($_class->nameExists()) message('分类名称已存在！','referer','error'); 
    $_class->update() ? message('','refresh','succ') : message('修改失败！','referer','error');
}

if(isset($_GPC['send_sort'])){
    if($_class->sort()) message('','refresh','succ');
}

if(isset($_GPC['doing']) && $_GPC['doing'] == 'delete'){
     $_class->delete() ? message('',$this->createWebUrl('w_class'),'succ') : message('删除失败！','referer','error'); 
 }

$_list = $_class->findAll();

include $this->template('w_class');


