<?php
class InviteAction extends Action{
    
    public function __construct($_openImApi = false){
            parent::__construct($_openImApi);
        }
        
    public function responseGroup($_keyword){
        $_qrcode = $this->_M->selectOne('qrcode',array('keyword'=>$_keyword),array('groupid','userid'));
        $_group = $this->_M->selectOne('group',array('id'=>$_qrcode['groupid']),array('title','brief','cover'));
        return array('Title'=>$_group['title'],'Description'=>$_group['brief'],'PicUrl'=>tomedia($_group['cover']),'Url'=>murl('entry//m_group_door',array('m'=>'zm_super_group','superior'=>$_qrcode['userid'],'groupid'=>$_qrcode['groupid']),false,true));
    }
     
    public function binding(){
        if(isset($this->_G['superior'])){ 
            $_isOnGeneralize = $this->_M->selectOne('group',array('share_award'=>1,'id'=>$this->_G['groupid']),array('id'));
            if($_isOnGeneralize){
                $_one = $this->_M->selectOne('member',array('userid'=>$this->_G['superior']),array('id'));
                $_exists = $this->_M->selectOne('group_member',array('group_id'=>$this->_G['groupid'],'member_id'=>$this->_U['uid']),array('id'));
                $_relation = $this->_M->selectOne('relation',array('groupid'=>$this->_G['groupid'],'userid'=>$this->_U['uid']),array('id'));
                if($_exists || (!$_one) || $_relation || ($this->_G['superior'] == $this->_U['uid'])) return false;
                $this->_M->insert('relation',array('parent_user'=>$this->_G['superior'],'userid'=>$this->_U['uid'],'groupid'=>$this->_G['groupid'],'uniacid'=>$this->_U['uniacid']));            
            }
        }  
    }
    
    public function findGreneralizeRecord(){
        $_list = $this->_M->selectAll('relation',array('uniacid'=>$this->_U['uniacid'],'status'=>1),array('order'=>'ORDER BY time DESC'));
        foreach($_list as $_key=>$_value){
            $_user = $this->_M->selectOne('member',array('userid'=>$_value['parent_user']),array('nickname'));
            $_list[$_key]['parent_nickname'] = $_user['nickname'];
            $_byUser = $this->_M->selectOne('member',array('userid'=>$_value['userid']),array('nickname'));
            $_list[$_key]['by_nickname'] = $_byUser['nickname'];
            $_group = $this->_M->selectOne('group',array('id'=>$_value['groupid']),array('title'));
            $_list[$_key]['group'] = $_group['title'];
        }
        return $_list;
    }
    
}