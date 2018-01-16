<?php
//首页
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error'); 
checkauth();




$_class = new ClassAction();
$_member = new MemberAction(true);
$_group = new GroupAction(true); 
$_slide = new SlideAction();
$_config = new ConfigAction();
$_member->register();

if(isset($_GPC['doing']) && $_GPC['doing'] == 'findGroupList'){
    echo $_group->ajaxFindClassGroup($this->createMobileUrl('m_group_door')); 
    exit;
}


$_index = true;
$_classList = $_class->findAll();
$_slideList = $_slide->showMobileSlide();
$_recommendList = $_group->findRecommend();
$_eightHotList = $_group->findHot(0,8);
$_fiveHotList = $_group->findHot(8,5);
$_share = $_member->share();
$_param = $_config->findConfig(array('index_icon_status'));  //是否开启首页导航自定义功能on/off
if($_param['index_icon_status'] == 'on'){
    $_icon = new IconAction();
    $_params = $_icon->find();
}






//引入模板
include $this->template('m_index');
?>