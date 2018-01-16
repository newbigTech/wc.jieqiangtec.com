<?php
class ClassAction extends Action{
    
    public function __construct($_openImApi = false){
            parent::__construct($_openImApi);
        }
    
    /**
     * 排序
     * @return boolean
     */
    public function sort(){
        foreach($this->_G['sort'] as $_key=>$_value){
            $this->_M->update('class',array('sort'=>$_value),array('id'=>$_key));
        }
        return true;
    }
    
    /**
     * 删除分类
     */
    public function delete(){
        return $this->_M->delete('class',array('id'=>$this->_G['classId']));
    }
    
    
    /**
     * 修改分类
     * @return [type]
     */
    public function update(){
        $_data['name'] = $this->_G['name'];
        return $this->_M->update('class',$_data,array('id'=>$this->_G['classId']));
    }
    
    
    /**
     * 获取所有分类
     */
    public function findAll(){
       $_class = $this->_M->selectAll('class',array('uniacid'=>$this->_U['uniacid']),array('order'=>'ORDER BY sort ASC'),array('id','name','sort')); 
       return $_class; 
    }
    
    /**
     * 分类名称是否存在
     * @param string $_className
     * @return [array]
     */
    public function nameExists($_className = ''){
        if(empty($_className)) $_className = $this->_G['name'];
        return $this->_M->selectOne('class',array('uniacid'=>$this->_U['uniacid'],'name'=>$_className),array('id'));
    }
    
    
    /**
     * 新增分类
     */
    public function add(){
        $_data['uniacid'] = $this->_U['uniacid'];
        $_data['name'] = $this->_G['name'];
        $this->_M->insert('class',$_data);
        $_classId = pdo_insertid();
        return $this->_M->update('class',array('sort'=>$_classId),array('id'=>$_classId));
    }
}