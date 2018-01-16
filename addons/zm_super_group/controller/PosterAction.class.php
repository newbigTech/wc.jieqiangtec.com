<?php
class PosterAction extends Action{
    
    public function __construct($_openImApi = false){
        parent::__construct($_openImApi);
    }
    
    public function findPoster(){
         $_scene = $this->isTwoScene(time());  
         $_qrcode = $this->createQrcode($_scene);
         $_keyword = $this->createModuleQrcode($_scene,$_qrcode);  
         $this->createWqKeyword($_keyword);
         $this->createWqQrcode($_scene,$_keyword,$_qrcode);
         $_qrImg = Wx::wxQrCode($_qrcode['ticket']); 
         return $this->_W['attachurl'].strstr($this->createPoster($_qrImg),'images/zm_super_group');
    }
    
    private function createPoster($_qrImg){
        $_systemImgDir = ATTACHMENT_ROOT.'images/zm_super_group';
        if(!file_exists($_systemImgDir)){
            mkdir($_systemImgDir,0777);
        }
        $_qrImg = $this->saveImage($_qrImg,$_systemImgDir.'/'.$this->_U['uid'].'-'.time().'qr.jpg');
        $_config = new ConfigAction();
        $_params = $_config->findConfig(array('poster_bg','q_x','q_y','q_z','h_show','h_x','h_y','h_z','n_show','n_x','n_y','n_z','n_c','t_show','t_x','t_y','t_z','t_c','c_show','c_x','c_y','c_w','c_h'));
        $_group = $this->_M->selectOne('group',array('id'=>$this->_G['groupid']),array('title','cover'));
        
        //模板图像
        $_tpl = ATTACHMENT_ROOT.'/'.$_params['poster_bg'];
        
        //海报图像资源句柄
        $_posterImg = $this->openImg($this->changeImgSize($_tpl,640,1136));
        
        //二维码图像
        $_qrImg = $this->openImg($this->changeImgSize($_qrImg,$_params['q_z']*2,$_params['q_z']*2,true));
        //合并海报和二维码
        imagecopyresampled($_posterImg,$_qrImg,$_params['q_x']*2,$_params['q_y']*2,0,0,$_params['q_z']*2,$_params['q_z']*2,$_params['q_z']*2,$_params['q_z']*2);
        
        if(!empty($_params['h_show'])){
            //头像
            $_headImg = $this->openImg($this->changeImgSize($this->saveImage($this->_U['head'],$_systemImgDir.'/'.$this->_U['uid'].'-headera.jpg'),$_params['h_z']*2,$_params['h_z']*2,true));
            //合并海报和二头像
            imagecopyresampled($_posterImg,$_headImg,$_params['h_x']*2,$_params['h_y']*2,0,0,$_params['h_z']*2,$_params['h_z']*2,$_params['h_z']*2,$_params['h_z']*2);
        } 
        
        
        if(!empty($_params['n_show'])){  
            $_tempFont = substr(substr($_params['n_c'],4),0,-1);
            $_temp = explode(',',$_tempFont);
            //创建一个字体颜色
            $_nicknameColor = imagecolorallocate($_posterImg,$_temp[0],$_temp[1],$_temp[2]); 
            //处理过长用户昵称
            $_userName = mb_strlen($this->_U['nickname'],'utf-8') > 4 ? mb_substr($this->_U['nickname'],0,4,'utf-8').'...' : $this->_U['nickname'];
            //将用户名字合并到海报上
            imagefttext($_posterImg,$_params['n_z'],0,$_params['n_x']*2,$_params['n_y']*2+$_params['n_z'],$_nicknameColor,LOUIE_FONT_DIR.'simhei.ttf',$_userName); 
        }
        
        if(!empty($_params['t_show'])){
            $_tempFont = substr(substr($_params['t_c'],4),0,-1);
            $_temp = explode(',',$_tempFont);
            //创建一个字体颜色
            $_nicknameColor = imagecolorallocate($_posterImg,$_temp[0],$_temp[1],$_temp[2]);
            //处理过长标题
            $_title = mb_strlen($_group['title'],'utf-8') > 20 ? mb_substr($_group['title'],0,20,'utf-8').'...' : $_group['title'];
            //将标题合并到海报上  
            imagefttext($_posterImg,$_params['t_z'],0,$_params['t_x']*2,$_params['t_y']*2+$_params['t_z'],$_nicknameColor,LOUIE_FONT_DIR.'simhei.ttf',$_title);
        } 
        
        if(!empty($_params['c_show'])){
            //社群封面
            $_cover = $this->openImg($this->changeImgCoverSize(ATTACHMENT_ROOT.'/'.$_group['cover'],$_params['c_w']*2,$_params['c_h']*2,true));
            //合并海报
            imagecopyresampled($_posterImg,$_cover,$_params['c_x']*2,$_params['c_y']*2,0,0,$_params['c_w']*2,$_params['c_h']*2,$_params['c_w']*2,$_params['c_h']*2);
        }
        
        
        
        //最终生成海报储存路径
        $_filename =$_systemImgDir.'/poster_'.time().'.jpg'; 
        //生成海报
        imagejpeg($_posterImg,$_filename);  
        //关闭海报资源句柄
        imagedestroy($_posterImg);
        return $_filename;
    }
    
    //返回打开并且返回指定图片的资源句柄
    private function openImg($_filename){
        list($_w,$_h,$_t) = getimagesize($_filename);
        switch($_t){
            case 1:
                return imagecreatefromgif($_filename);
                break;
            case 2:
                return imagecreatefromjpeg($_filename);
                break;
            case 3:
                return imagecreatefrompng($_filename);
                break;
        }
    }
    
    /**
     * 处理图像的大小,并且返回新图地址
     * @param string $_filename   文件路径
     * @param int $_n_w                生成的图像宽度
     * @param int $_n_h                 生成的图像高度
     * @param boolean $_create   是否保存到本地服务器，如果是外部地址则布尔值为true
     */
    private function changeImgSize($_filename,$_n_w,$_n_h,$_create=false){
        //打开图像
        $_img = $this->openImg($_filename);
        //获取原来图像的宽和高
        list($_w,$_h) = getimagesize($_filename);
        //创建新图像资源句柄
        $_newImg = imagecreatetruecolor($_n_w,$_n_h);
        //重采样拷贝部分图像并调整大小
        imagecopyresampled($_newImg,$_img,0,0,0,0,$_n_w,$_n_h,$_w,$_h);
        if($_create) $_filename = IA_ROOT.'/attachment/images/zm_publicthb/qrcode_'.$this->_U['uid'].'.jpg';
        //重新生成图像
        imagejpeg($_newImg,$_filename);
        //关闭老图资源句柄
        imagedestroy($_img);
        //关闭新图资源句柄
        imagedestroy($_newImg);
        //返回新图地址
        return $_filename;
    }
    
    /**
     * 处理封面图像的大小,并且返回新图地址
     * @param string $_filename   文件路径
     * @param int $_n_w                生成的图像宽度
     * @param int $_n_h                 生成的图像高度
     * @param boolean $_create   是否保存到本地服务器，如果是外部地址则布尔值为true
     */
    private function changeImgCoverSize($_filename,$_n_w,$_n_h,$_create=false){
        //打开图像
        $_img = $this->openImg($_filename);
        //获取原来图像的宽和高
        list($_w,$_h) = getimagesize($_filename);
        //创建新图像资源句柄
        $_newImg = imagecreatetruecolor($_n_w,$_n_h);
        //重采样拷贝部分图像并调整大小
        if($_w > $_n_w){
            $_x = ($_w - $_n_w) / 2;
        }else{
            $_x = 0;   
        }
        if($_h > $_n_h){
            $_y = ($_h - $_n_h) / 2;
        }else{
            $_y = 0;
        }
        imagecopyresampled($_newImg,$_img,0,0,$_x,$_y,$_n_w,$_n_h,$_n_w,$_n_h); 
        if($_create) $_filename = IA_ROOT.'/attachment/images/zm_publicthb/qrcode_'.$this->_U['uid'].'.jpg';
        //重新生成图像
        imagejpeg($_newImg,$_filename);
        //关闭老图资源句柄
        imagedestroy($_img);
        //关闭新图资源句柄
        imagedestroy($_newImg);
        //返回新图地址
        return $_filename;
    }
    
    
    /**
     * 创建微擎二维码数据
     * @param unknown $_scene
     * @param unknown $_keyword
     * @param unknown $_qrcode
     */
    private function createWqQrcode($_scene,$_keyword,$_qrcode){
        $_data['uniacid'] = $this->_U['uniacid'];
        $_data['acid'] = $this->_W['acid'];
        $_data['qrcid'] = $_scene;
        $_data['name'] = $_data['keyword'] = $_keyword;
        $_data['model'] = 1;
        $_data['ticket'] = $_qrcode['ticket'];
        $_data['expire'] = 2592000;
        $_data['subnum'] = 0;
        $_data['createtime'] = time();
        $_data['status'] = 1;
        $_data['url'] = $_qrcode['url'];
        $this->_M->insert('qrcode',$_data,true);
    }
    
    /**
     * 创建微擎响应关键词
     * @param unknown $_keyword
     */
    private function createWqKeyword($_response){
        $_key['uniacid'] = $this->_U['uniacid'];
        $_key['name'] = $_response;
        $_key['module'] = 'zm_super_group';
        $_key['displayorder'] = 0;
        $_key['status'] = 1;
        $this->_M->insert('rule',$_key,true);
        $rid = pdo_insertid();
        $_keyword['rid'] = $rid;
        $_keyword['uniacid'] = $_key['uniacid'];
        $_keyword['module'] = $_key['module'];
        $_keyword['content'] = $_key['name'];
        $_keyword['type'] = 1;
        $_Keyword['displayorder'] = 0;
        $_keyword['status'] = 1;
        $this->_M->insert('rule_keyword',$_keyword,true);
    }
    
    /**
     * 创建模块二维码数据
     * @param unknown $_scene
     * @param unknown $_qrcode
     */
    private function createModuleQrcode($_scene,$_qrcode){
        $_module['uniacid'] = $this->_U['uniacid'];
        $_module['userid'] = $this->_U['uid'];
        $_module['groupid'] = $this->_G['groupid'];
        $_module['scene_id'] = $_scene;
        $_module['keyword'] = $_module['name'] = Tool::createCode();
        $_module['time'] = time()+$_qrcode['expire_seconds'];
        $_module['url'] = $_qrcode['url'];
        $_module['ticket'] = $_qrcode['ticket'];
        $this->_M->insert('qrcode',$_module);
        return $_module['keyword'];
    }
    
    /**
     * 创建微信二维码数据
     * @param unknown $_scene
     */
    private function createQrcode($_scene){
        $_obj = WeAccount::create($this->_W['acid']);
        //生成scene_id,时间戳，生成微信生成二维码数据
        $_wxData['scene_id'] = $_scene;
        $_wxData['expire_seconds'] = 2592000;
        $_wxData['action_name'] = 'QR_SCENE';
        $_wxData['action_info']['scene']['scene_id'] = $_wxData['scene_id'];
        return $_obj->barCodeCreateDisposable($_wxData);
    }
    
    
    /**
     * 查询官方qrcode表是否有重复的值，如果有加上一个1，直到没有重复值
     * @param unknown $_sceneId
     * @return unknown
     */
    private function isTwoScene($_sceneId){
        //查询官方二维码表是否已经存在该值
        $_exist = $this->_M->selectOne('qrcode',array('qrcid'=>$_sceneId),array('qrcid'),true);
        //如果存在在原有的值上加1
        if($_exist) self::isTwoScene($_sceneId+1);
        return $_sceneId;
    }
    
    /**
     * 获取远程图片，并且下载到本地
     * @param string $url    //远程图片地址
     * return string   //返回本地图片地址
     */
    function saveImage($url,$filename) {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        ob_start ();
        curl_exec ( $ch );
        $return_content = ob_get_contents ();
        ob_end_clean ();
        $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        $fp= @fopen($filename,"a"); //将文件绑定到流 25
        fwrite($fp,$return_content); //写入文件
        //关闭文件资源句柄
        fclose($fp);
        return $filename;
    }
    
    
}