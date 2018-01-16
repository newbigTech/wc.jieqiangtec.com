<?php
//关注的群
include substr(dirname(__FILE__),0,-10).'configs/run.inc.php';

if($_W['container'] != 'wechat') message('本程序仅支持在微信中打开','','error');
checkauth();

$_group = new GroupAction(true);
$_group->register();

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'findAttentionGroup'){
        echo json_encode($_group->findAttentionGroup($_GPC['start'],$_GPC['end'])); 
        exit;
    }
    
    if($_GPC['doing'] == 'cancelAttention'){
        $_isSucc = $_group->cancelAttention();
        echo $_isSucc ? 1 : 0; 
        exit;
    }
    
    
}

  
$_share = $_group->share(); 
//引入模板
include $this->template('m_attention_group');
?>