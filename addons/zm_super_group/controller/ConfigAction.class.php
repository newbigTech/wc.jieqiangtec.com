<?php
class ConfigAction extends Action{
    
    public function __construct($_openImApi = false){
            parent::__construct($_openImApi);
        }
    
    
    /**
     * 获取参数值
     * @param array $_return
     * @return [array]
     */
    public function findConfig(Array $_return){
        return $this->_M->selectOne('config',array('uniacid'=>$this->_U['uniacid']),$_return);
    }
    
    /**
     * 设置首页图标是否开启自定义
     */
    public function setIndexIcon(){
        if($this->_G['status'] == 'off'){
            $_status = 'on';
        }else{
            $_status = 'off';
        }
        $_data['index_icon_status'] = $_status;
        return $this->setConfig($_data);
    }
    
    /**
     * 设置我的钱包提示文字
     * @return [type]
     */
    public function setWalletHint(){
        $_data['wallet_hint'] = $this->_G['wallet_hint'];
        $_data['edit_time'] = time();
        return $this->setConfig($_data);
    }
    
    /**
     * 财务中心设置
     */
    public function setFinance(){
        $_data['deposit_limit'] = $this->_G['deposit_limit'];
        $_data['deposit_charge'] = $this->_G['deposit_charge'];
        $_data['edit_time'] = time();
        return $this->setConfig($_data);
    }
    
    /**
     * 设置自定义分享
     */
    public function setShare(){
        $_data['share_title'] = $this->_G['share_title'];
        $_data['share_icon'] = $this->_G['share_icon'];
        $_data['share_description'] = $this->_G['share_description'];
        $_data['share_url'] = $this->_G['share_url'];
        $_data['edit_time'] = time();
        return $this->setConfig($_data);
    }
    
    /**
     * 设置敏感词
     */
    public function setSensitive(){
        $_data['sensitive'] = $this->_G['sensitive'];
        $_data['edit_time'] = time();
        return $this->setConfig($_data);
    }
    
    /**
     * 设置社群艾特频率
     * @return [type]
     */
    public function setEithTplMessageTime(){
        $_data['eith_tpl_message_time'] = $this->_G['eith_tpl_message_time'];
        $_data['edit_time'] = time();
        return $this->setConfig($_data);
    }
    
    /**
     * 创建社群提示文字
     */
    public function setCreateGroupHint(){
        $_data['create_group_hint'] = $this->_G['create_group_hint'];
        $_data['edit_time'] = time();
        return $this->setConfig($_data);
    }
    
    /**
     * 微信商户设置
     */
    public function setPay(){
        $_data['pay_appid'] = $this->_G['pay_appid'];
        $_data['pay_appSecret'] = $this->_G['pay_appSecret'];
        $_data['pay_account'] = $this->_G['pay_account'];
        $_data['pay_key'] = $this->_G['pay_key'];
        $_data['edit_time'] = time();
        return $this->setConfig($_data);
    }
     
    /**
     * 设置模板消息
     */
    public function setTpl(){
        $_data['check_group_tpl'] = $this->_G['check_group_tpl'];
        $_data['edit_time'] = time();
        return $this->setConfig($_data);
    }
    
    
    /**
     * 设置云通信参数
     */
    public function setIm(){
        $_data['im_appid'] = $this->_G['im_appid'];
        $_data['im_account'] = $this->_G['im_account'];
        $_data['im_key'] = $_POST['im_key'];
        $_data['im_accountType'] = $this->_G['im_accountType'];  
        $_data['edit_time'] = time();
        return $this->setConfig($_data);
    }
    
    /**
     * 设置海报
     */
    public function setPoster(){
        $_data['poster_bg'] = $this->_G['poster_bg'];
        $_data['q_x'] = $this->_G['q_x'];
        $_data['q_y'] = $this->_G['q_y'];
        $_data['q_z'] = $this->_G['q_z'];
        $_data['h_show'] = $this->_G['h_show'];
        $_data['h_x'] = $this->_G['h_x'];
        $_data['h_y'] = $this->_G['h_y'];
        $_data['h_z'] = $this->_G['h_z'];
        $_data['n_show'] = $this->_G['n_show'];
        $_data['n_x'] = $this->_G['n_x'];
        $_data['n_y'] = $this->_G['n_y'];
        $_data['n_z'] = $this->_G['n_z'];
        $_data['n_c'] = $this->_G['n_c'];
        $_data['t_show'] = $this->_G['t_show'];
        $_data['t_x'] = $this->_G['t_x'];
        $_data['t_y'] = $this->_G['t_y'];
        $_data['t_z'] = $this->_G['t_z'];
        $_data['t_c'] = $this->_G['t_c'];
        $_data['c_show'] = $this->_G['c_show'];
        $_data['c_x'] = $this->_G['c_x'];
        $_data['c_y'] = $this->_G['c_y'];
        $_data['c_w'] = $this->_G['c_w'];
        $_data['c_h'] = $this->_G['c_h'];
        return $this->setConfig($_data);
    }
    
    
    /**
     * 设置参数
     * @param unknown $_data
     */
    private function setConfig($_data){
        if($this->isSetConfig()){
            return $this->_M->update('config',$_data,array('uniacid'=>$this->_U['uniacid']));
        }else{
            $_data['uniacid'] = $this->_U['uniacid'];
            return $this->_M->insert('config',$_data);
        }
    }
    
    /**
     * Ajax上传云通信签名工具
     */
    public function ajaxUploadTool(){
        load()->func('file');
        if($_FILES['myFile']['error'] == 0){
            $_filedir = ATTACHMENT_ROOT.'zm_super_group/';
            if(!file_exists($_filedir)){
                mkdir($_filedir);
                chmod($_filedir,0777);
            }
            $_fileTooldir = $_filedir.'tool/';
            if(!file_exists($_fileTooldir)){
                mkdir($_fileTooldir);
                chmod($_fileTooldir,0777);
            }
            $_filename = $_fileTooldir.$_FILES['myFile']['name'];
            move_uploaded_file($_FILES['myFile']['tmp_name'],$_filename);
        }
        if(file_exists($_filename)){
            return 1;
        }else{
            return 0;
        }
    }
    
    
    /**
     * Ajax上传云通信私钥
     */
    public function ajaxUploadKey(){
        load()->func('file');
        if($_FILES['myFile']['error'] == 0){
            $_filedir = ATTACHMENT_ROOT.'zm_super_group/';
            if(!file_exists($_filedir)){
                mkdir($_filedir);
                chmod($_filedir,0777);
            }
            $_fileKeydir = $_filedir.'key/';
            if(!file_exists($_fileKeydir)){
                mkdir($_fileKeydir);
                chmod($_fileKeydir,0777);
            }
            $_filename = $_fileKeydir.$this->_U['uniacid'].'_'.$_FILES['myFile']['name'];
            move_uploaded_file($_FILES['myFile']['tmp_name'],$_filename);
        }
        return json_encode(strstr($_filename,'/attachment'));
    }
    
    
    public function uploadCertificate(){
        $_path = ATTACHMENT_ROOT.'zm_super_group/';
        if(!file_exists($_path)) mkdir($_path,0777);
        $_payPath = $_path.'paykey/';
        if(!file_exists($_payPath)) mkdir($_payPath,0777); 
        foreach($_FILES['certificate'] as $_value){
            foreach($_value as $_k=>$_v){
                $_filename = $_payPath.$this->_U['uniacid'].$_FILES['certificate']['name'][$_k];
                move_uploaded_file($_FILES['certificate']['tmp_name'][$_k],$_filename);  
            }
        }
    }
}