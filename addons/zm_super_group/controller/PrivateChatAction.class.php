<?php 
class PrivateChatAction extends Action{
    private $_user1 = '';
    private $_user2 = '';
    public function __construct($_openImApi = false){
        parent::__construct($_openImApi);
    }
    
    public function findPrivateUser(){
        $_one = $this->_M->selectOne('private',array('group_id'=>$this->_G['groupid']),array('user1','user2'));
        if($_one['user1'] == $this->_U['uid']){
            $_memberId = $_one['user2'];
        }else{
            $_memberId = $_one['user1']; 
        }
        $_member = $this->_M->selectOne('member',array('userid'=>$_memberId),array('nickname'));
        return $_member;
    }
    
    public function isExists($_user1,$_user2){
        $this->_user1 = $_user1;
        $this->_user2 = $_user2;
        $_sql = "SELECT 
                        * 
                   FROM "
                         .tablename('zm_super_group_private')."
                   WHERE
                          (user1 = $this->_user1
                    AND
                          user2 = $this->_user2)
                    OR    
                          (user1 = $this->_user2
                    AND  
                           user2 = $this->_user1)
                    LIMIT
                          1
                         ";
        $_one = pdo_fetch($_sql);
        return $_one;
    }
    
    public function create(){
        $_returnInfo = $this->_imApi->group_create_group('Public',$this->_user1.'&'.$this->_user2,$this->_user1);
        if($_returnInfo['ActionStatus'] == 'OK'){
            $this->_imApi->group_add_group_member($_returnInfo['GroupId'],$this->_user2);
            $_data['uniacid'] = $this->_U['uniacid'];
            $_data['title'] = $this->_user1.'&'.$this->_user2;
            $_data['name'] = $this->_user1.'&'.$this->_user2;
            $_data['group_id'] = $_returnInfo['GroupId'];
            $_data['cover'] = 'private';
            $_data['header'] = 'private';
            $_data['owner'] = $this->_user1;
            $_data['class_id'] = 0;
            $_data['create_time'] = time();
            $this->_M->insert('group',$_data);
            $_groupId = pdo_insertid();
            $_add['member_id'] = $this->_user2;
            $_add['group_id'] = $_private['group_id'] = $_groupId;
            $_private['user1'] = $this->_user1; 
            $_private['user2'] = $this->_user2;
            $_add['uniacid'] = $_private['uniacid'] = $this->_U['uniacid'];
            $this->_M->insert('group_member',$_add);
            $this->_M->insert('private',$_private);
            return $_private['group_id'];
        }
    }

}
?>