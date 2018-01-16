<?php
//引入运行文件

include substr(dirname(__FILE__),0,-7).'configs/run.inc.php'; 

$_config = new ConfigAction();
$_member = new MemberAction(); 

if(isset($_GPC['send_set_finance'])){
    $_config->setFinance() ? message('','referer','succ') : message('设置失败！','referer','error');
}

if(isset($_GPC['send_set_wallet_hint'])){
    $_config->setWalletHint() ? message('','referer','succ') : message('设置失败！','referer','error');
}

if(isset($_GPC['passDeposit'])){
    $_status = $_member->getUserDeposit();  
    if($_status == 1){
        message('给用户提现成功！','referer','succ');
    }else{
        message($_status,'','error');
    }
}

if(isset($_GPC['noDeposit'])){  
   $_member->noDeposit() ? message('','referer','succ') : message('拒绝失败！','referer','error');  
}  

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'deposit_list'){
        $_list = $_member->findDepositList();
        $_page = $_member->getPage();
        $_deposit_list = true;  
    }
    
    if($_GPC['doing'] == 'pay_record'){
        $_list = $_member->payRecord(); 
        $_page = $_member->getPage();
        $_pay_record = true;
    }
}else{
    $_setFinance = true;
    $_param = $_config->findConfig(array('deposit_limit','deposit_charge','wallet_hint'));
}
 

include $this->template('w_finance');