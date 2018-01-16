<?php

class IconAction extends Action{

   
    public function __construct($_openImApi = false){
        parent::__construct($_openImApi);
    }
    
    public function setIcon1(){
        $_data['icon1_icon'] = $this->_G['icon1_icon'];
        $_data['icon1_name'] = $this->_G['icon1_name'];
        $_data['icon1_url'] = $this->_G['icon1_url'];
        return $this->setIcon($_data);
    }
    
    public function setIcon2(){
        $_data['icon2_icon'] = $this->_G['icon2_icon'];
        $_data['icon2_name'] = $this->_G['icon2_name'];
        $_data['icon2_url'] = $this->_G['icon2_url'];
        return $this->setIcon($_data);
    } 
    
    public function setIcon3(){
        $_data['icon3_icon'] = $this->_G['icon3_icon'];
        $_data['icon3_name'] = $this->_G['icon3_name'];
        $_data['icon3_url'] = $this->_G['icon3_url'];
        return $this->setIcon($_data);
    }
    
    public function setIcon4(){
        $_data['icon4_icon'] = $this->_G['icon4_icon'];
        $_data['icon4_name'] = $this->_G['icon4_name'];
        $_data['icon4_url'] = $this->_G['icon4_url'];
        return $this->setIcon($_data);
    }
    
    public function find(){ 
        return $this->_M->selectOne('icon',array('uniacid'=>$this->_U['uniacid']));
    }

   
    private function setIcon($_data){
        $_one = $this->_M->selectOne('icon',array('uniacid'=>$this->_U['uniacid']),array('id'));
        if($_one){
            return $this->_M->update('icon',$_data,array('uniacid'=>$this->_U['uniacid']));            
        }else{
            $_data['uniacid'] = $this->_U['uniacid']; 
            return $this->_M->insert('icon',$_data);
        }
    } 
    

}