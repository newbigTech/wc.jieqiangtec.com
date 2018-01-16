<?php
//推广管理
//引入运行文件
include substr(dirname(__FILE__),0,-7).'configs/run.inc.php';

$_config = new ConfigAction();

if(isset($_GPC['send_poster'])){
    
    $_config->setPoster() ? message('设置成功！','referer','succ') : message('设置失败','referer','error');
}

if(isset($_GPC['doing'])){
    if($_GPC['doing'] == 'poster'){
        $_poster = true; 
        $_param = $_config->findConfig(array('poster_bg','q_x','q_y','q_z','h_show','h_x','h_y','h_z','n_show','n_x','n_y','n_z','n_c','t_show','t_x','t_y','t_z','t_c','c_show','c_x','c_y','c_w','c_h'));
    }
}else{
    $_record = true;
    $_invite = new InviteAction();
    $_list = $_invite->findGreneralizeRecord();
}

include $this->template('w_generalize'); 