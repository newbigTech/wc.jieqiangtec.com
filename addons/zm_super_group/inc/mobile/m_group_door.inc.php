<?php
//群主页信息
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();


$_group = new GroupAction(true); 
$_memebr = new MemberAction();
$_invite = new InviteAction();
$_group->register(); 


$_invite->binding();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'enterNoCheck'){
      echo $_group->enterNoCheck(); 
      exit;  
    }
    
    if($_GPC['doing'] == 'getWxPayParam'){
        echo $_group->getWxPayParam();  
        exit;
    }
    
    if($_GPC['doing'] == 'attention'){
        $_succ = $_memebr->attention($_GPC['by_userid'],$_W['member']['uid']); 
        echo $_succ ? 1 : 0;
        exit;
    }
    
    if($_GPC['doing'] == 'paySuccDoing'){  
        echo $_group->paySuccDoing();   
        exit; 
    }
    
    if($_GPC['doing'] == 'findPoster'){
        $_poster = new PosterAction();
        echo $_poster->findPoster();
        exit;
    } 
    
}
 

$_group->existsImGroup($_group->idToGroupId($_GPC['groupid']));
$_one = $_group->findOne(); 
if(!$_one) message('该群不存在！',$_SERVER['HTTP_REFERER'],'error');  
$_group->addBrowseCount($_one['id']); 
$_group->addBrowseRecord($_one['id']); 
$_userExistsGroup = $_group->userExistsGroup($_one['id']); 
if($_one['owner'] != $_W['member']['uid']){ 
    $_attentionStatus = $_memebr->attentionStatus($_one['owner'],$_W['member']['uid']); 
}
$_attentionTotal = $_memebr->findAttentionTotal($_one['owner']);
$_groupTotal = $_group->findGroupMemberTotal($_GPC['groupid']);
$_fourMember = $_group->findFoureMember($_GPC['groupid']);
$_share = $_group->groupShare();







//引入模板
include $this->template('m_group_door');
?>