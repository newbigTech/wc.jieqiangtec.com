<?php
//后台基础设置
include substr(dirname(__FILE__),0,-7).'configs/run.inc.php';

$_config = new ConfigAction();

if(isset($_POST['checkAjaxUploadKey'])){
    echo $_config->ajaxUploadKey();
    exit; 
}

if(isset($_POST['checkAjaxUploadTool'])){
    echo $_config->ajaxUploadTool();
    exit;
}

if(isset($_GPC['send_set_im'])){
    $_config->setIm() ? message('','refresh','succ') : message('设置失败！','referer','error');
}

if(isset($_GPC['send_set_tpl'])){
    $_config->setTpl() ? message('','refresh','succ') : message('设置失败！','referer','error'); 
}

if(isset($_GPC['send_set_pay'])){
    $_config->setPay() ? message('','refresh','succ') : message('设置失败！','referer','error'); 
}

if(isset($_GPC['send_set_share'])){
    $_config->setShare() ? message('','refresh','succ') : message('设置失败！','referer','error');
}

if(isset($_GPC['send_set_create_group_hint'])){
    $_config->setCreateGroupHint() ? message('','refresh','succ') : message('设置失败！','referer','error');
}

if(isset($_GPC['send_set_sensitive'])){
    $_config->setSensitive() ? message('','refresh','succ') : message('设置失败！','referer','error');
}

if(isset($_GPC['send_eith_tpl_message_time'])){
    $_config->setEithTplMessageTime() ? message('','refresh','succ') : message('设置失败！','referer','error');
}

//上传商户证书 
if(isset($_GPC['send_upload'])){
    $_config->uploadCertificate();
}



switch($_GPC['doing']){ 
    case 'im':
         $_im = true;
         $_param = $_config->findConfig(array('im_appid','im_account','im_key','im_accountType')); 
         break;
    case 'pay':
         $_pay = true; 
         $_param = $_config->findConfig(array('pay_appid','pay_appSecret','pay_account','pay_key'));
         break;
    case 'tpl':
         $_tpl = true;
         $_param = $_config->findConfig(array('check_group_tpl')); 
         break;
    default: 
        $_param = $_config->findConfig(array('share_title','share_icon','share_description','share_url','create_group_hint','sensitive','eith_tpl_message_time'));
        $_basic = true;
}

include $this->template('w_config');


