<?php

//幻灯inc

//引入运行文件

include substr(dirname(__FILE__),0,-7).'configs/run.inc.php';


//实例化幻灯类

$_slide = new SlideAction();



//添加幻灯

if(isset($_GPC['send_addslide'])) $_slide->addSlide() ? message('添加幻灯成功！','refresh','success') : message('添加幻灯失败！','referer','error'); 



//修改幻灯

if(isset($_GPC['send_updateslide'])) $_slide->updateSlide() ? message('修改幻灯成功！','refresh','success') : message('修改幻灯失败！','referer','error');



//删除幻灯

if(isset($_GPC['deleteslide'])) $_slide->deleteSlide() ? message('删除幻灯成功！',$this->createWebUrl('w_slide'),'success') : message('删除幻灯失败！','referer','error');



//修改幻灯显示状态

if(isset($_GPC['slidestate'])){

    if($_slide->updateSlideState()) message('','referer','success');

} 



//显示全部幻灯

$_allSlide = $_slide->showAllSlide();



//幻灯显示状态转换

foreach($_allSlide as $_key=>$_value){

    $_allSlide[$_key]['show_state'] = empty($_value['state']) ? '未显示' : '显示'; 

}









include $this->template('w_slide');