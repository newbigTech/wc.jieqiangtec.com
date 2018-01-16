<?php
//社群聊天页面
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_group = new GroupAction(true); 
$_group->register();



if(isset($_GPC['doing'])){
    //拉取群历史消息
    if($_GPC['doing'] == 'findHistoryMsg'){ 
        $_msgList = json_encode($_group->findHistory($_GPC['groupId'],$_GPC['start'],$_GPC['end']));
        echo $_msgList;  
        exit;   
    } 
    
    //发送文本消息
    if($_GPC['doing'] == 'sendTextMsg'){ 
        $_succ = $_group->sendTextMsg($_GPC['userId'],$_GPC['groupId'],$_GPC['content'],$_GPC['eith']);
        echo $_succ ? $_succ : 0;
        exit; 
    }
    
    //发送图片消息
    if($_GPC['doing'] == 'sendPicMsg'){
         echo $_group->sendPicMsg($_GPC['userId'],$_GPC['groupId']); 
         exit;
    } 
    
    //新消息
    if($_GPC['doing'] == 'newMsg'){
        $_msgList = json_encode($_group->newMsg($_GPC['seq'],$_GPC['groupId'])); 
        echo $_msgList;   
        exit;
    }
    
    //是否有艾特，是否模板消息提示
    if($_GPC['doing'] == 'isEithTpl'){
        $_group->eithSendTpl($_GPC['seq']); 
        exit;
    }
    
    //查看大图消息
    if($_GPC['doing'] == 'showMaxImage'){
        $_img = $_group->findMsgMaxImage($_GPC['msgImgId']); 
        echo $_img ? json_encode($_img) : 0;
        exit;
    }
    
    //发送语音消息
    if($_GPC['doing'] == 'sendVoiceMsg'){
        $_voice = $_group->sendVoiceMsg($_GPC['voiceId'],$_GPC['userId'],$_GPC['groupId'],$_GPC['duration']);
        echo $_voice ? json_encode($_voice) : 0;  
        exit; 
    }
    
    //更改发红包订单支付状态
    if($_GPC['doing'] == 'changePayState'){
        $_redMoney = new RedMoneyAction();
        $_redMoney->changePayState($_GPC['orderId']);
        exit;
    }
    
    //发送红包消息
    if($_GPC['doing'] == 'sendRed'){
        $_isSucc = $_group->sendRed();
        echo $_isSucc ? $_isSucc : 0;
        exit;
    }
    
    //新增打开记录
    if($_GPC['doing'] == 'clickRed'){
        $_redMoney = new RedMoneyAction();
        $_redMoney->addOpenRedRecord();
        if($_redMoney->isRob()){
            echo 1;  //当前用户已经抢过红包
            exit;
         } 
        echo $_redMoney->isEmpty() ? 2 : 0;  //2=当前红包没有空，0=当前红包已经空
        exit;
    }
    
    //被抢红包的详情
    if($_GPC['doing'] == 'findRedDetails'){
        $_redMoney = new RedMoneyAction();
        $_list = $_redMoney->findRedDetails();
        echo json_encode($_list);
        exit;
    }
    
    //关注社群
    if($_GPC['doing'] == 'attentionGroup'){
        $_attentionStatus = $_group->attentionGroup();
        echo $_attentionStatus ? 1 : 0; 
        exit; 
    }
    
    //开启关闭禁言操作
    if($_GPC['doing'] == 'controlTalk'){
        $_isScuu = $_group->controlTalk($_GPC['groupId'],$_GPC['talkStatus']);
        echo $_isScuu ? 1 : 0;
        exit;
    }
    
    //验证社群是否禁言
    if($_GPC['doing'] == 'checkBannedTalk'){
        $_isBanned = $_group->checkBannedTalk($_GPC['groupId']);
        echo $_isBanned ? 1 : 0;
        exit;
    }
    
    //获取当前用户未读艾特消息
    if($_GPC['doing'] == 'findNoReadyEith'){
        $_noReadyEith = $_group->findNoReadyEith($_GPC['userId'],$_GPC['groupId'],$_GPC['start'],$_GPC['end']);
        echo $_noReadyEith[0] ? json_encode($_noReadyEith[0]) : 0;
        exit;
    }
    
    //创建红包订单
    if($_GPC['doing'] == 'createRed'){
        $_redMoney = new RedMoneyAction();
        echo $_redMoney->getWxPayParam($_GPC['groupId'],$_GPC['sum']);
        exit;
    }
    
    //私聊模板消息提醒
    if($_GPC['doing'] == 'privateTpl'){
        echo $_group->privateTpl($_GPC['seq']); 
        exit;
    }
    
}


if(!$_group->userExistsGroup($_GPC['groupid'])){
    if(!$_group->isOwner()){
        header('Location:'.$this->createMobileUrl('m_group_door',array('groupid'=>$_GPC['groupid'])));
    }
} 

//私聊
if(isset($_GPC['chatType'])){
    
    if($_GPC['chatType'] == 'private'){
        $_private = new PrivateChatAction(true);
        //用户之间的社群是否已经创建
        $_isCreate = $_private->isExists($_GPC['user1'],$_GPC['user2']);
        //已经存在
        if($_isCreate){
            $_privateid = $_isCreate['group_id'];
        }else{
            //不存在，创建私聊社群
            $_privateid = $_private->create();
        }
        header('Location:'.$this->createMobileUrl('m_room',array('groupid'=>$_privateid,'private'=>1)));
    }
}
    


//社群资料
$_imInfo = json_encode($_group->findClient());

//获取社群头像、名称、标题、成员人数
$_groupInfo = $_group->findRoomGorup();

//当前用户是否关注该社群
$_isAttention = $_group->isAttentionGroup();

//是否为私聊
$_isPrivate = isset($_GPC['private']) ? true : false;

if($_isPrivate){
    $_private = new PrivateChatAction();
    $_privateUser = $_private->findPrivateUser();
    $_title = $_privateUser['nickname'];
}else{
    $_share = $_group->groupShare();
}






//引入模板
include $this->template('m_room'); 
?>