<?php

class SlideAction extends Action{

    

    public function __construct($_openImApi = false){

        parent::__construct($_openImApi);

    }

    

    //新增幻灯

   public function addSlide(){

        $_data['slide_pic'] = $this->_G['slide_pic'];

        $_data['slide_url'] = $this->_G['slide_url'];

        $_data['uniacid'] = $this->_W['uniaccount']['uniacid'];

        return $this->_M->insert('slide',$_data);

   }

   

   

   

   //显示幻灯

   public function showAllSlide(){

       return $this->_M->selectAll('slide',array('uniacid'=>$this->_W['uniaccount']['uniacid']));

   }

   

   //前端显示幻灯

   public function showMobileSlide(){

       return $this->_M->selectAll('slide',array('uniacid'=>$this->_U['uniacid'],'state'=>'1'));

   }

   

   //修改幻灯

   public function updateSlide(){

       $_data['slide_pic'] = $this->_G['slide_pic'];

       $_data['slide_url'] = $this->_G['slide_url'];

       return $this->_M->update('slide',$_data,array('uniacid'=>$this->_W['uniaccount']['uniacid'],'id'=>$this->_G['slide_id']));

   }

   

   //删除幻灯

   public function deleteSlide(){

       return $this->_M->delete('slide',array('uniacid'=>$this->_W['uniaccount']['uniacid'],'id'=>$this->_G['deleteslide']));

   }

   

   //修改幻灯显示状态

   public function updateSlideState(){

      return empty($this->_G['slidestate']) ? $this->_M->update('slide',array('state'=>1),array('uniacid'=>$this->_W['uniaccount']['uniacid'],'id'=>$this->_G['slideid'])) : $this->_M->update('slide',array('state'=>0),array('uniacid'=>$this->_W['uniaccount']['uniacid'],'id'=>$this->_G['slideid']));

   }

    

}