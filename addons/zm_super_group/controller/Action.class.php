<?php
class Action{
    protected $_W  = array();   //微擎$_W
    protected $_G = array();   //微擎$_GPC
    protected $_M = null;   //MDB对象
    protected $_page = '';   //当前分页页数
    protected $_pagesize = '';   //每页多少数据
    protected $_limit1 = '';       //Limit初始值
    protected $_U = array();    //大W常用
    protected $_imApi = null;    //云通信API 
    
    protected function __construct($_openImApi){
        global $_W,$_GPC;
        $this->_W = $_W;
        $this->_G = $_GPC;
        $this->_U['uniacid'] =$_W['uniaccount']['uniacid'];
        $this->_U['uid'] = $_W['member']['uid'];
        $this->_U['nickname'] = $_W['fans']['nickname'];
        $this->_U['head'] = $_W['fans']['headimgurl'];
        $this->_U['sex'] = $_W['fans']['sex'];
        $this->_M = MDB::getInstance();
        if(!$this->_U['head']){
            $this->_U['head'] = LOUIE_IMG.'no-header.png';  
        }
        if(!$this->_U['nickname']){
            $this->_U['nickname'] = '未获取'; 
        }
        if($_openImApi){
            $this->_imApi = new TimRestAPI();
            $_imConfig = $this->_M->selectOne('config',array('uniacid'=>$this->_U['uniacid']),array('im_appid','im_account','im_key'));
            $this->_imApi->init($_imConfig['im_appid'],$_imConfig['im_account']); 
            $_path = IA_ROOT.$_imConfig['im_key'];  
            chmod($_path,0777);
            $_signature = $this->_imApi->getSignature(); 
            chmod($_signature,0777);   
            $this->_imApi->generate_user_sig($_imConfig['im_account'],'36000',$_path,$_signature);
        }
    }
    
    //分页
    protected function page($_total){
        $this->_page = max(1, intval($this->_G['page']));
        $this->_pagesize = 10; 
        $this->_limit1 = ($this->_page-1)*$this->_pagesize;
        return pagination($_total,$this->_page,$this->_pagesize);
    } 
    
    /**
     * 是否已经设置过参数
     */
    protected function isSetConfig(){
        return $this->_M->selectOne('config',array('uniacid'=>$this->_U['uniacid']),array('id'));
    }
    
    
    /**
     * 用户是否已经存在
     */
    public function existsUser(){
        return $this->_M->selectOne('member',array('userid'=>$this->_U['uid']),array('id'));
    }
    
    /**
     * 注册用户
     */
    public function register(){
        if($this->existsUser()) return;
        $_isSucc = $this->_imApi->account_import($this->_U['uid'],$this->_U['nickname'],$this->_U['head']);
        if($_isSucc['ActionStatus'] == 'OK'){
            $_data['openid'] = $this->_W['openid'];
            $_data['userid'] = $this->_U['uid'];
            $_data['nickname'] = $this->_U['nickname'];
            $_data['header'] = $this->_U['head']; 
            $_data['uniacid'] = $this->_U['uniacid']; 
            $_data['sex'] = $this->_U['sex'];
            $this->_M->insert('member',$_data);  
        }
    }
    
    
    /**
    
    * 无刷新上传图像专用
    
    */
    
    public function uploadImage(){
        load()->func('file');
        foreach($_FILES['img'] as $_key=>$_value){
            for($i=0;$i<count($_FILES['img']['name']);$i++){
                $_file[$i][$_key] = $_value[$i];
            }
        }
        foreach($_file as $_k=>$_v){
            $_upload = file_upload($_v,'image'); 
            $_path[$_k] = $_upload['path'];
        }
        return $_path; 
    } 
    
    public function share(){
        $_config = new ConfigAction();
        $_shares = $_config->findConfig(array('share_title','share_icon','share_url','share_description'));
        $_shareHtml = '<script>wx.ready(function(){';
        $_shareHtml .= 'wx.onMenuShareTimeline({';
        $_shareHtml .= 'title: "'.$_shares['share_title'].'",';
        $_shareHtml .= 'link: "'.$_shares['share_url'].'",'; 
        $_shareHtml .= 'imgUrl:"'.tomedia($_shares['share_icon']).'",'; 
        $_shareHtml .='});';
        $_shareHtml .='wx.onMenuShareAppMessage({';
        $_shareHtml .='title:"'.$_shares['share_title'].'",';
        $_shareHtml .= 'desc:"'.$_shares['share_description'].'",';  
        $_shareHtml .= 'link:"'.$_shares['share_url'].'",';
        $_shareHtml .= 'imgUrl:"'.tomedia($_shares['share_icon']).'",';  
        $_shareHtml .='});';
        $_shareHtml .= '})</script>';
        return $_shareHtml;
    }
             
}