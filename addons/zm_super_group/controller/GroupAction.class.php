<?php
class GroupAction extends Action{
    private $_showPage = '';
  
    public function __construct($_openImApi = false){
        parent::__construct($_openImApi);  
    }
    
    public function setAwardRatio(){
        return $this->_M->update('group',array('award_ratio'=>$this->_G['ratio']),array('id'=>$this->_G['groupid']));
    }
    
    public function findShareAward(){
        return $this->_M->selectOne('group',array('id'=>$this->_G['groupid']),array('share_award','award_ratio'));
    }
    
   public function changeShareStatus(){
       if($this->_G['status'] == 'false'){
           return $this->_M->update('group',array('share_award'=>0),array('id'=>$this->_G['groupid']));
       }else{
           return $this->_M->update('group',array('share_award'=>1),array('id'=>$this->_G['groupid']));
       }
   }
    
    function groupShare(){
        $_shares = $this->_M->selectOne('group',array('id'=>$this->_G['groupid']),array('title','brief','header'));
        $_shareHtml = '<script>wx.ready(function(){';
        $_shareHtml .= 'wx.onMenuShareTimeline({';
        $_shareHtml .= 'title: "'.$_shares['title'].'",';
        $_shareHtml .= 'imgUrl:"'.tomedia($_shares['header']).'",'; 
        $_shareHtml .='});';
        $_shareHtml .='wx.onMenuShareAppMessage({';
        $_shareHtml .='title:"'.$_shares['title'].'",';
        $_shareHtml .= 'desc:"'.$_shares['brief'].'",';
        $_shareHtml .= 'imgUrl:"'.tomedia($_shares['header']).'",';
        $_shareHtml .='});';
        $_shareHtml .= '})</script>';
        return $_shareHtml;
    }
    
    public function sendRed(){
        $_redId = $this->createRed();
        if($this->_G['type'] == 1){
            $this->createRedRecord($_redId);
        }
        $_succ = $this->_imApi->group_send_group_msg($this->_U['uid'],$this->_G['groupId'],$_redId);
        if($_succ['ActionStatus'] == 'OK'){
            $_data['uniacid'] = $this->_U['uniacid'];
            $_data['userid'] = $this->_U['uid'];
            $_data['groupid'] = $this->_G['groupId'];
            $_data['type'] = 'TIMRedElem';
            $_data['time'] = time();
            $_data['seq'] = $_succ['MsgSeq'];
            $_data['content'] = $_redId;
            $_msgSucc = $this->_M->insert('msg',$_data);
            return $_msgSucc ? date('H:i:s',$_data['time']) : 0; 
        }
        return 0;
    }
    
    private function createRed(){
        $_data['title'] = $this->_G['title'];
        $_data['sum'] = $this->_G['sum'];
        $_data['number'] = $this->_G['number'];
        $_data['type'] = $this->_G['type'];
        $_data['unit'] = $this->_G['unit'];
        $_data['uniacid'] = $this->_U['uniacid'];
        $this->_M->insert('msg_red',$_data);
        return pdo_insertid();
    }
    
    private function createRedRecord($_redId){
        $_data['red_id'] = $_redId;
        $_data['sum'] = $this->_G['unit'];
        for($i=0;$i<$this->_G['number'];$i++){
            $this->_M->insert('red_record',$_data);
        }
    }
    
    public function findNoReadyEith($_userid,$_groupid,$_start = 0,$_end = 1){
        $_list = $this->_M->selectAll('msg',array('eith'=>$_userid,'groupid'=>"'".$_groupid."'",'is_read'=>0),array('order'=>'ORDER BY id DESC','limit'=>'LIMIT '.$_start.','.$_end),array('userid','content','id'));
        if($_list){
            $_member = $this->_M->selectOne('member',array('userid'=>$_list[0]['userid']),array('nickname','header'));
            $_list[0]['header'] = $_member['header'];
            $_list[0]['content'] = htmlspecialchars_decode($_list[0]['content']); 
            $this->_M->update('msg',array('is_read'=>1),array('id'=>$_list[0]['id']));
        }
        return $_list; 
    }
    
    public function checkBannedTalk($_groupid){
        return $this->_M->selectOne('group',array('id'=>$_groupid,'is_banned_talk'=>1),array('is_banned_talk'));
    }
    
    public function controlTalk($_groupid,$_status){
        $_status = empty($_status) ? 1 : 0;
        return $this->_M->update('group',array('is_banned_talk'=>$_status),array('id'=>$_groupid));
    }
    
    public function deleteOneMsg($_msgid){
        $_msg = $this->_M->selectOne('msg',array('id'=>$_msgid),array('type','content'));
        if($_msg['type'] == 'TIMImageElem'){
            $this->_M->delete('msg_image',array('id'=>$_msg['content']));
        }elseif($_msg['type'] == 'TIMSoundElem'){
            $this->_M->delete('msg_voice',array('id'=>$_msg['content']));            
        }
        return $this->_M->delete('msg',array('id'=>$_msgid)); 
    }
    
    public function findAllMsgContent(){
        if(isset($this->_G['groupid'])){
            $_group = $this->_M->selectOne('group',array('id'=>$this->_G['groupid']),array('group_id'));  
            $_data['groupid'] = "'".$_group['group_id']."'"; 
        }
        $_data['uniacid'] = $this->_U['uniacid'];
        $this->_showPage = $this->page($this->_M->total('msg',$_data));
        $_list = $this->_M->selectAll('msg',$_data,array('order'=>'ORDER BY time DESC','limit'=>'LIMIT '.$this->_limit1.','.$this->_pagesize),array('time','userid','groupid','type','content','id')); 
        foreach($_list as $_key=>$_value){
            $_member = $this->_M->selectOne('member',array('userid'=>$_value['userid']),array('nickname','header'));
            $_group = $this->_M->selectOne('group',array('group_id'=>$_value['groupid']),array('name'));
            if($_value['type'] == 'TIMImageElem'){
                $_img = $this->_M->selectOne('msg_image',array('id'=>$_value['content']),array('min_url')); 
                $_list[$_key]['content'] = $_img['min_url']; 
            }else if($_value['type'] == 'TIMSoundElem'){
                $_list[$_key]['content'] = '语音';
            }else{
                $_list[$_key]['content'] = htmlspecialchars_decode($_value['content']);
            }
            $_list[$_key]['nickname'] = $_member['nickname'];
            $_list[$_key]['header'] = $_member['header'];
            $_list[$_key]['groupName'] = $_group['name']; 
        }
        return $_list; 
    }
    
    public function searchGroup(){
        $_list = $this->_M->selectAll('group',array('uniacid'=>$this->_U['uniacid'],'status'=>1,'title like'=>"%".$this->_G['keyword']."%"),array('order'=>'ORDER BY id DESC'),array('header','id','title','name','browse_count','enter','price'));
        foreach($_list as $_key=>$_value){
            $_list[$_key]['url'] = murl('entry//m_group_door',array('m'=>'zm_super_group','groupid'=>$_value['id']));
            $_list[$_key]['header'] = tomedia($_value['header']); 
            if($_value['enter'] == 1 || $_value['enter'] == 2){ 
                $_list[$_key]['enterType'] = '免费';
            }else if($_value['enter'] == 3){
                $_list[$_key]['enterType'] = '￥'.$_value['price'];
            }
        }
        return $_list;  
    }   
    
    public function cancelAttention(){
        return $this->_M->update('attention_group',array('status'=>0),array('id'=>$this->_G['attentionId']));
    }
    
    public function findAttentionGroup($_start = 0,$_end = 20){
        $_list = $this->_M->joinLeft(array('attention_group','group'),array('a.groupid','b.id'),array('a.userid'=>$this->_U['uid'],'a.status'=>1),array('order'=>'ORDER BY b.id DESC','limit'=>'LIMIT '.$_start.','.$_end),array('b.title,b.name,a.id,b.header'));
        foreach($_list as $_key=>$_value){
            $_list[$_key]['header'] = tomedia($_value['header']);
        }
        return $_list;  
    }
    
    public function isAttentionGroup(){
        return $this->_M->selectOne('attention_group',array('groupid'=>$this->_G['groupid'],'userid'=>$this->_U['uid'],'status'=>1),array('id'));
    }
    
    public function attentionGroup(){
        $_isAttention = $this->_M->selectOne('attention_group',array('groupid'=>$this->_G['groupid'],'userid'=>$this->_U['uid']),array('id')); 
        if($_isAttention){
            $_isSucc = $this->_M->update('attention_group',array('status'=>1),array('id'=>$_isAttention['id']));  
        }else{
            $_data['uniacid'] = $this->_U['uniacid'];
            $_data['userid'] = $this->_U['uid'];
            $_data['groupid'] = $this->_G['groupid'];
            $_isSucc = $this->_M->insert('attention_group',$_data);
        }
        $_config = new ConfigAction();
        //用户关注设置通知当前用户
        $_tpl = $_config->findConfig(array('check_group_tpl'));
        $_byGroup = $this->_M->selectOne('group',array('id'=>$this->_G['groupid']),array('name','owner'));
        $_openid = mc_uid2openid($this->_U['uid']);
        $_data = array('first'=>array('value'=>'关注社群成功'),
            'keyword1'=>array('value'=>'您关注了社群—'.$_byGroup['name']),
            'keyword2'=>array('value'=>'关注社群提醒')
        ); 
        Wx::tplMessage($_openid,$_tpl['check_group_tpl'],$_data,murl('entry//m_attention_group',array('m'=>'zm_super_group'),false,true));
        
        //用户关注设置通知群管理
        $_openid1 = mc_uid2openid($_byGroup['owner']);
        $_data1 = array('first'=>array('value'=>'您创建的社群被用户关注'),
            'keyword1'=>array('value'=>'您的社群—'.$_byGroup['name'].'被用户关注'),
            'keyword2'=>array('value'=>'关注社群提醒')
        );
        Wx::tplMessage($_openid1,$_tpl['check_group_tpl'],$_data1,murl('entry//m_group_door',array('m'=>'zm_super_group','groupid'=>$this->_G['groupid']),false,true));
        
        return true;
    }
   
    public function findRoomGorup(){
        $_group = $this->_M->selectOne('group',array('id'=>$this->_G['groupid']),array('name','title','id','header','owner','is_banned_talk'));
         $_total = $this->_M->total('group_member',array('group_id'=>$_group['id']));
         $_group['personNum'] = $_total + 1; 
        return $_group;
    }
    
    public function sendVoiceMsg($_voiceId,$_userid,$_groupId,$_duration){
        $_isTalk = $this->_M->selectOne('group',array('group_id'=>$_groupId),array('is_banned_talk','owner'));
        if($_isTalk['owner'] != $_userid){
            if(!empty($_isTalk['is_banned_talk'])){
                return false;
            }
        }
        $_voiceArr = $this->createVoice($_voiceId,$_duration); 
        $_succ = $this->_imApi->group_send_group_msg($_userid,$_groupId,$_voiceArr['voiceid']);
        if($_succ['ActionStatus'] == 'OK'){
            $_data['uniacid'] = $this->_U['uniacid'];
            $_data['userid'] = $_userid;
            $_data['groupid'] = $_groupId;
            $_data['type'] = 'TIMSoundElem';
            $_data['time'] = time();
            $_data['seq'] = $_succ['MsgSeq'];
            $_data['content'] = $_voiceArr['voiceid']; 
            $_voiceArr['msgTime'] = date('H:i:s',$_data['time']);
            $_msgSucc = $this->_M->insert('msg',$_data);
            return $_msgSucc ? $_voiceArr : 0;     
        }
        return 0; 
    }
    
    private function createVoice($_voiceId,$_duration){
        $account_api = WeAccount::create();
        $_filename = $account_api->downloadMedia($_voiceId,true);
        $_data['url'] = $_filename; 
        $_data['uniacid'] = $this->_U['uniacid'];
        $_data['media_id'] = $_voiceId;
        $_data['duration'] = $_duration; 
        $_data['past_time'] = time() + (86400*3); 
        $this->_M->insert('msg_voice',$_data); 
        $_return['voiceid'] = pdo_insertid(); 
        $_return['url'] = $_voiceId; 
        return $_return;
    }
    
    public function findMsgMaxImage($_id){
        $_img = $this->_M->selectOne('msg_image',array('id'=>$_id),array('max_url','max_width','max_height'));
        $_img['max_url'] = tomedia($_img['max_url']);
        return $_img;
    }
    
    public function newMsg($_seq,$_groupid){ 
        $_config = new ConfigAction();
        $_sensitive = $_config->findConfig(array('sensitive'));
        $_sensitiveArr = explode(',',$_sensitive['sensitive']);
        $_msg = $this->_M->selectAll('msg',array('seq'=>$_seq,'groupid'=>"'".$_groupid."'"),array('order'=>'ORDER BY seq DESC'),array('userid','type','content','groupid','time','eith','is_read'));  
        $_msgList = array();
        foreach($_msg as $_key=>$_value){
            //判断艾特用户在线，如果在线，更改艾特消息状态为已读，不在发送模板消息通知
            if($this->_U['uid'] == $_value['eith']){
                $this->_M->update('msg',array('is_read'=>1),array('seq'=>$_seq));
            }
            
            if(isset($this->_G['private']) && $this->_G['private'] == 1){ 
                $_privateUser = $this->_M->selectOne('private',array('group_id'=>$this->_G['groupid']),array('user1','user2'));
                if($_privateUser['user1'] == $_value['userid']){
                        if($_privateUser['user2'] == $this->_U['uid']){
                            $this->_M->update('msg',array('is_read'=>1),array('seq'=>$_seq)); 
                        }
                }else if($_privateUser['user2'] == $_value['userid']){
                        if($_privateUser['user1'] == $this->_U['uid']){
                            $this->_M->update('msg',array('is_read'=>1),array('seq'=>$_seq));
                        }
                }
            }  
            
            if(!empty($_value['eith'])){
                $_eithMember = $this->_M->selectOne('member',array('userid'=>$_value['eith']),array('nickname'));
                $_value['content'] = '@'.$_eithMember['nickname'].' '.$_value['content'];
            }
            
            $_msgList[$_key]['seq'] = $_seq;
            $_msgList[$_key]['From_Account'] = $_value['userid'];
            $_msgList[$_key]['MsgContent'] = htmlspecialchars_decode($_value['content']);
            
            $_startToday = mktime(0,0,0,date('m'),date('d'),date('Y'));
            $_msgYear = date('Y',$_value['time']);
            if($_msgYear == date('Y')){
                if($_value['time'] < $_startToday){
                    $_msgList[$_key]['msgTime'] = date('m-d H:i:s',$_value['time']);
                }else{
                    $_msgList[$_key]['msgTime'] = date('H:i:s',$_value['time']);
                }
            }else{
                $_msgList[$_key]['msgTime'] = date('Y-m-d H:i:s',$_value['time']);
            }
            

            if($_value['type'] == 'TIMImageElem'){  
                $_img = $this->_M->selectOne('msg_image',array('id'=>$_value['content']),array('min_url','min_width','min_height'));
                $_msgList[$_key]['min_url'] = tomedia($_img['min_url']); 
                $_msgList[$_key]['min_width'] = $_img['min_width'] / 60;
                $_msgList[$_key]['min_height'] = $_img['min_height'] / 60; 
            }
            if($_value['type'] == 'TIMSoundElem'){
                $_voice = $this->_M->selectOne('msg_voice',array('id'=>$_value['content']),array('media_id','duration'));
                $_msgList[$_key]['MsgContent'] = $_voice['media_id'];  
                $_msgList[$_key]['voiceid'] = $_value['content'];
                $_msgList[$_key]['duration'] = $_voice['duration'];
                $_msgList[$_key]['contentWidth'] = 3 + ($_voice['duration'] / 10);  
            }
            if($_value['type'] == 'TIMRedElem'){
                $_red = $this->_M->selectOne('msg_red',array('id'=>$_value['content']),array('id','title'));
                $_msgList[$_key]['redId'] = $_red['id'];
                $_msgList[$_key]['title'] = $_red['title'];
                $_msgList[$_key]['status'] = 'close'; 
                $_msgList[$_key]['statusText'] = '领取红包';
            }
            if($_value['type'] == 'TIMTextElem'){
                $_msgList[$_key]['MsgContent'] = str_replace($_sensitiveArr,'**',$_msgList[$_key]['MsgContent']);
            }
            $_msgList[$_key]['MsgType'] = $_value['type'];
            $_msgList[$_key]['groupid'] = $_value['groupid'];  
            $_member = $this->_M->selectOne('member',array('userid'=>$_value['userid']),array('nickname','header'));
            $_msgList[$_key]['nickname'] = $_member['nickname'];
            $_msgList[$_key]['header'] = $_member['header'];  
        }
        
        return $_msgList;
    }
    
    public function eithSendTpl($_seq){
        $_msg = $this->_M->selectOne('msg',array('seq'=>$_seq),array('eith','is_read','type','groupid','userid','content'));
        if((!empty($_msg['eith']) && (empty($_msg['is_read'])))){ 
            $_group = $this->_M->selectOne('group',array('group_id'=>$_msg['groupid']),array('id','name'));
            $_config = new ConfigAction();
            $_eithTime = $_config->findConfig(array('eith_tpl_message_time'));
            $_eithRecord = $this->_M->selectOne('eith_record',array('groupid'=>$_group['id'],'eith_user'=>$_msg['eith']),array('time','id'));
            if($_eithRecord && ((time() - $_eithRecord['time']) < $_eithTime['eith_tpl_message_time'])) return;
            $_member = $this->_M->selectOne('member',array('userid'=>$_msg['userid']),array('nickname'));    
            $_tpl = $_config->findConfig(array('check_group_tpl'));  
            $_openid = mc_uid2openid($_msg['eith']); 
            $_tplContent = preg_replace_callback('/\<img .+\/\>/U',function(){
                return '[表情]';
            },htmlspecialchars_decode($_msg['content']));
            $_data = array('first'=>array('value'=>$_member['nickname'].'在社群'.$_group['name'].'@了你！'),
                'keyword1'=>array('value'=>$_tplContent), 
                'keyword2'=>array('value'=>'社群@通知')   
            ); 
            Wx::tplMessage($_openid,$_tpl['check_group_tpl'],$_data,murl('entry//m_room',array('m'=>'zm_super_group','groupid'=>$_group['id']),false,true));
            
            $_record['time'] = time();
            if($_eithRecord){
                $this->_M->update('eith_record',$_record,array('id'=>$_eithRecord['id']));
            }else{
                $_record['eith_user'] = $_msg['eith'];
                $_record['groupid'] = $_group['id'];
                $this->_M->insert('eith_record',$_record); 
            }
        }  
    }
    
    public function sendPicMsg($_userid,$_groupId){
        $_path = $this->uploadImage();
        $_data['uniacid'] = $this->_U['uniacid'];
        $_data['userid'] = $_userid;
        $_data['groupid'] = $_groupId;
        $_data['type'] = 'TIMImageElem';
        $_data['time'] = time();  
        foreach($_path as $_value){
            $_content = $this->picMsgDispose($_value);
            $_succ = $this->_imApi->group_send_group_msg($_userid,$_groupId,$_content);
            if($_succ['ActionStatus'] == 'OK'){
                $_data['content'] = $_content;  
                $_data['seq'] = $_succ['MsgSeq'];
                $_isSucc = $this->_M->insert('msg',$_data); 
            }
        }
        return date('H:i:s',$_data['time']); 
    }
    
    private function picMsgDispose($_image){
        $_img = new Image(ATTACHMENT_ROOT.$_image); 
        $_imgW = $_img->getWidth(); 
        $_imgH = $_img->getHeight();  
        if($_imgW > 360 && (strtolower(substr($_image,-3)) != 'gif')){
               $_min = $_img->createNewImg('min',360);
               $_data['min_url'] = strstr($_min['url'],'images/'.$this->_U['uniacid']);
               $_data['min_width'] = $_min['width'];
               $_data['min_height'] = $_min['height'];
               if($_imgW > 750){ 
                   $_max = $_img->createNewImg('max',750);
                   $_data['max_url'] = strstr($_max['url'],'images/'.$this->_U['uniacid']);
                   $_data['max_width'] = $_max['width'];
                   $_data['max_height'] = $_max['height'];
                   if(is_file(ATTACHMENT_ROOT.$_image)){
                       unlink(ATTACHMENT_ROOT.$_image);
                   }
               }else{
                   $_data['max_url'] = $_image; 
                   $_data['max_width'] = $_imgW;
                   $_data['max_height'] = $_imgH; 
               }
        }else{
                $_data['min_url'] = $_data['max_url'] = $_image;
                $_data['min_width'] = $_data['max_width'] = $_imgW;
                $_data['min_height'] = $_data['max_height'] = $_imgH;   
        } 
        $_data['uniacid'] = $this->_U['uniacid'];      
        $this->_M->insert('msg_image',$_data); 
        return pdo_insertid();
    }
    
    
    public function sendTextMsg($_userid,$_groupId,$_content,$_eith){   
        $_isTalk = $this->_M->selectOne('group',array('group_id'=>$_groupId),array('is_banned_talk','owner'));
        if($_isTalk['owner'] != $_userid){
            if(!empty($_isTalk['is_banned_talk'])){
                return false;
            }
        }       
        $_succ = $this->_imApi->group_send_group_msg($_userid,$_groupId,$_content); 
        if($_succ['ActionStatus'] == 'OK'){ 
            $_data['uniacid'] = $this->_U['uniacid'];
            $_data['userid'] = $_userid;
            $_data['groupid'] = $_groupId;
            $_data['type'] = 'TIMTextElem'; 
            $_data['time'] = time();
            $_data['seq'] = $_succ['MsgSeq'];
            $_data['content'] = $_content;
            $_data['eith'] = $_eith;
            $this->_M->insert('msg',$_data);
            return date('H:i:s',$_data['time']);
        }
    }
    
    public function findHistory($_groupid,$_start,$_end){ 
        //$_msgArr = $this->_M->selectAll('msg',array('uniacid'=>$this->_U['uniacid'],'groupid'=>$_groupid),array('order'=>'ORDER BY id DESC','limit'=>'LIMIT '.$_start.','.$_end),array('userid','type','content'));
        $_msgArr = $this->_M->selectAll('msg',array('uniacid'=>$this->_U['uniacid'],'groupid'=>"'".$_groupid."'"),array('order'=>'ORDER BY id DESC','limit'=>'LIMIT '.$_start.','.$_end),array('userid','type','content','time','eith'));
        $_config = new ConfigAction();
        $_sensitive = $_config->findConfig(array('sensitive'));
        $_sensitiveArr = explode(',',$_sensitive['sensitive']);
        foreach($_msgArr as $_key=>$_value){ 
            $_msgList[$_key]['From_Account'] = $_value['userid']; 
            
            if(!empty($_value['eith'])){
                $_eithMember = $this->_M->selectOne('member',array('userid'=>$_value['eith']),array('nickname'));
                $_value['content'] = '@'.$_eithMember['nickname'].' '.$_value['content'];
            }
            
            $_msgList[$_key]['MsgContent'] = htmlspecialchars_decode($_value['content']);
            $_startToday = mktime(0,0,0,date('m'),date('d'),date('Y')); 
            $_msgYear = date('Y',$_value['time']); 
            if($_msgYear == date('Y')){
                if($_value['time'] < $_startToday){
                    $_msgList[$_key]['msgTime'] = date('m-d H:i:s',$_value['time']);
                }else{
                    $_msgList[$_key]['msgTime'] = date('H:i:s',$_value['time']); 
                }
            }else{
                $_msgList[$_key]['msgTime'] = date('Y-m-d H:i:s',$_value['time']);
            }
            if($_value['type'] == 'TIMTextElem'){
                $_msgList[$_key]['MsgContent'] = str_replace($_sensitiveArr,'**',$_msgList[$_key]['MsgContent']);
            }
            if($_value['type'] == 'TIMImageElem'){
                $_msgList[$_key]['MsgContent'] = $_value['content']; 
                $_img = $this->_M->selectOne('msg_image',array('id'=>$_value['content']),array('min_url','min_width','min_height'));
                $_msgList[$_key]['min_url'] = tomedia($_img['min_url']);
                $_msgList[$_key]['min_width'] = $_img['min_width'] / 60;
                $_msgList[$_key]['min_height'] = $_img['min_height'] / 60;
            }
            if($_value['type'] == 'TIMSoundElem'){
                $_voice = $this->_M->selectOne('msg_voice',array('id'=>$_value['content']),array('media_id','past_time','url','duration'));
                if($_voice['past_time'] <= time()){
                    $account_api = WeAccount::create();
                    $_result = $account_api->uploadMedia(ATTACHMENT_ROOT.$_voice['url'],'voice'); 
                    $_tempData['past_time'] = time() + 86400;
                    $_tempData['media_id'] = $_result['media_id']; 
                    $this->_M->update('msg_voice',$_tempData,array('id'=>$_value['content'])); 
                    $_msgList[$_key]['MsgContent'] = $_result['media_id']; 
                }else{
                    $_msgList[$_key]['MsgContent'] = $_voice['media_id'];
                }    
                $_msgList[$_key]['voiceid'] = $_value['content']; 
                $_msgList[$_key]['duration'] = $_voice['duration'];
                $_msgList[$_key]['contentWidth'] = 3 + ($_voice['duration'] / 10);    
            }
            if($_value['type'] == 'TIMRedElem'){
                $_red = $this->_M->selectOne('msg_red',array('id'=>$_value['content']),array('id','title'));
                $_msgList[$_key]['redId'] = $_red['id'];
                $_msgList[$_key]['title'] = $_red['title'];
                $_isOpen = $this->_M->selectOne('red_open_record',array('userid'=>$this->_U['uid'],'red_id'=>$_red['id']),array('id'));
                $_isRob = $this->_M->selectOne('red_record',array('take_user'=>$this->_U['uid'],'red_id'=>$_red['id']),array('id'));
                if($_isOpen){
                    $_msgList[$_key]['status'] = 'open';
                    $_msgList[$_key]['statusText'] = $_isRob ? '红包已领取' : '领取红包';
                }else{
                    $_msgList[$_key]['status'] = 'close';
                    $_msgList[$_key]['statusText'] = $_isRob ? '红包已领取' : '领取红包'; 
                }
            }
            $_msgList[$_key]['MsgType'] = $_value['type']; 
            $_member = $this->_M->selectOne('member',array('userid'=>$_value['userid']),array('nickname','header'));
            $_msgList[$_key]['nickname'] = $_member['nickname'];
            $_msgList[$_key]['header'] = $_member['header']; 
        }
        
        array_unshift($_msgList,$this->_M->total('msg',array('uniacid'=>$this->_U['uniacid'],'groupid'=>"'".$_groupid."'")));
        return $_msgList;  
    }
    
    private function parseFace($_string){   
        $_pattern = '/\{(1_[\d]{1,3})\}/U'; 
        return preg_replace_callback($_pattern,function($_match){
            $_newString = '<img src="'.LOUIE_FACE.'1/'.$_match[1].'.gif" />';
            return $_newString;
        },$_string);   
        return $_string; 
    }
    
    public function findClient(){
        $this->_imApi = new TimRestAPI();   
        $_imConfig = $this->_M->selectOne('config',array('uniacid'=>$this->_U['uniacid']),array('im_appid','im_key','im_accountType','im_account'));  
        $this->_imApi->init($_imConfig['im_appid'],$this->_U['uid']);
        $_path = IA_ROOT.$_imConfig['im_key'];
        chmod($_path,0777); 
        $_signature = $this->_imApi->getSignature();
        chmod($_signature,0777);
        $_userSig = $this->_imApi->generate_user_sig($this->_U['uid'],'36000',$_path,$_signature);
        $_imConfig['userSig'] = $_userSig[0];
        $_imConfig['identifier'] = $this->_U['uid'];   
        $_imConfig['groupId'] = $this->idToGroupId($this->_G['groupid']);
        return $_imConfig; 
    }
    
    public function findFoureMember($_groupid){
        $_member = $this->_M->joinLeft(array('group_member','member'),array('a.member_id','b.userid'),array('a.group_id'=>$_groupid,'a.status'=>1),array('order'=>'ORDER BY a.id DESC','limit'=>'LIMIT 0,4'),array('b.header'));
        return $_member;
    }
    
    public function findGroupMemberTotal($_groupid){
        return $this->_M->total('group_member',array('status'=>1,'group_id'=>$_groupid));
    }
    
    public function findPrivateChat($_start = 0,$_end = 8){
        $_sql = "
                 SELECT 
                        user1,user2,group_id
                 FROM
                        ".tablename('zm_super_group_private')."
                 WHERE
                        user1 = ".$this->_U['uid']."
                  OR
                        user2 = ".$this->_U['uid']."
                  ORDER BY
                          id DESC
                  LIMIT 
                         $_start,$_end
                ";
         $_list = pdo_fetchall($_sql);
         
         foreach($_list as $_key=>$_value){
             
             if($this->_U['uid'] == $_value['user1']){
                 $_objUser = $_value['user2'];
             }else{
                 $_objUser = $_value['user1'];
             }
             
             $_member = $this->_M->selectOne('member',array('userid'=>$_objUser),array('header','nickname'));
             $_list[$_key]['nickname'] = $_member['nickname'];
             $_list[$_key]['header'] = $_member['header'];
             $_msg = $this->_M->selectAll('msg',array('groupid'=>"'".$this->idToGroupId($_value['group_id'])."'"),array('order'=>'ORDER BY id DESC','limit'=>'LIMIT 1'),array('type','content'));
             if(count($_msg) > 0){
                 if($_msg[0]['type'] == 'TIMTextElem'){
                     $_list[$_key]['newMessage'] = preg_replace('/\<img .+ \/>/U','[表情]',htmlspecialchars_decode($_msg[0]['content'])); 
                 }elseif($_msg[0]['type'] == 'TIMSoundElem'){
                     $_list[$_key]['newMessage'] = '语音';
                 }elseif($_msg[0]['type'] == 'TIMImageElem'){
                     $_list[$_key]['newMessage'] = '图片';
                 }elseif($_msg[0]['type'] == 'TIMRedElem'){
                     $_list[$_key]['newMessage'] = '红包';
                 }
             }else{
                 $_list[$_key]['newMessage'] = '暂无信息';
             }
         }
         return $_list;
    }
    
    
    public function findBrowseRecord($_start = 0,$_end = 8){
        $_record = $this->_M->joinLeft(array('browse_record','group'),array('a.group_id','b.id'),array('a.uniacid'=>$this->_U['uniacid'],'a.userid'=>$this->_U['uid']),array('order'=>'ORDER BY a.time DESC','limit'=>'LIMIT '.$_start.','.$_end),array('b.title,b.id,b.name,b.header'));
        if($this->_G['requestType'] == 'ajax'){
            foreach($_record as $_key=>$_value){
                $_record[$_key]['header'] = tomedia($_value['header']);
            }
        }
        return $_record;
    }
    
    public function addBrowseRecord($_groupid){
        $_data['userid'] = $this->_U['uid'];
        $_data['uniacid'] = $this->_U['uniacid'];
        $_data['group_id'] = $_groupid;
        $_isExists = $this->_M->selectOne('browse_record',$_data,array('id'));
        if($_isExists){
            $this->_M->update('browse_record',array('time'=>time()),$_data);
        }else{
            $_data['time'] = time();
            $this->_M->insert('browse_record',$_data);
        }
    }
    
    public function addBrowseCount($_groupid){
        $this->_M->updateMath('group',array('browse_count'=>'++1++'),array('id'=>$_groupid));
    }
    
    
    public function paySuccDoing(){
        $_config = new ConfigAction();
        $_tpl = $_config->findConfig(array('check_group_tpl'));
        
        $_deal = $this->_M->selectOne('deal_record',array('orderid'=>$this->_G['orderid']),array('price','take_user','groupid'));
        $this->_M->update('deal_record',array('status'=>1),array('orderid'=>$this->_G['orderid'])); 
        $_price = $_deal['price'];
        //查询是否有推广记录
        $_isGeneralize = $this->_M->selectOne('relation',array('userid'=>$this->_U['uid'],'status'=>0,'groupid'=>$_deal['groupid']),array('parent_user','id'));
        if(($_price >= 1) && $_isGeneralize){
            $_group = $this->_M->selectOne('group',array('id'=>$_deal['groupid']),array('award_ratio','title'));
            $_award = round($_price * ($_group['award_ratio'] / 100),2); 
            $this->_M->update('relation',array('status'=>1,'time'=>time(),'award'=>$_award),array('id'=>$_isGeneralize['id']));
            $this->_M->updateMath('member',array('money'=>'++'.$_award.'++'),array('userid'=>$_isGeneralize['parent_user']));
            //模板提醒推广收益  
            $_openid = mc_uid2openid($_isGeneralize['parent_user']);
            $_data = array('first'=>array('value'=>'推广社群收益通知'),
                'keyword1'=>array('value'=>'收益金额'.$_award.'元'), 
                'keyword2'=>array('value'=>'社群收益来自'.$_group['title'])
            );
            Wx::tplMessage($_openid,$_tpl['check_group_tpl'],$_data); 
            $_price = $_price - $_award;
        }
        
        $this->_M->updateMath('member',array('money'=>'++'.$_price.'++'),array('userid'=>$_deal['take_user']));
        $_openid = mc_uid2openid($_deal['take_user']);   
        //模板提醒群管理收益通知
        $_data = array('first'=>array('value'=>'社群收益通知'),
            'keyword1'=>array('value'=>'收益金额'.$_price.'元'),    
            'keyword2'=>array('value'=>'社群收益')  
        );
        Wx::tplMessage($_openid,$_tpl['check_group_tpl'],$_data,murl('entry//m_earnings',array('m'=>'zm_super_group'),false,true)); 
        
        //模板提醒用户付费入群提醒
        $_openid1 = mc_uid2openid($this->_U['uid']);
        $_data1 = array('first'=>array('value'=>' 付费入群提醒'),
            'keyword1'=>array('value'=>'您成功支付'.$_deal['price'].'元'), 
            'keyword2'=>array('value'=>'付费入群') 
        );
        Wx::tplMessage($_openid1,$_tpl['check_group_tpl'],$_data1,murl('entry//m_group_door',array('m'=>'zm_super_group','groupid'=>$_deal['orderid']),false,true)); 
        
        return $this->enterNoCheck(); 
    } 
    
    public function enterNoCheck(){
        $_groupid = $this->idToGroupId($this->_G['groupid']);
        $_returnInfo = $this->_imApi->group_add_group_member($_groupid,$this->_U['uid']);
        if($_returnInfo['ActionStatus'] == 'OK'){
            $_enterInfo = $_returnInfo['MemberList'][0]['Result'];
            if($_enterInfo == 0) return 0;  //添加成员失败 
            if($_enterInfo == 2) return 2;  //该成员已经在群组中了
            if($_enterInfo == 1){
                $_data['uniacid'] = $this->_U['uniacid'];
                $_data['group_id'] = $this->_G['groupid'];
                $_data['member_id'] = $this->_U['uid'];
                $_insert = $this->_M->insert('group_member',$_data);
                return $_insert ? 1 : 3;   //1=添加成功，3=添加失败 
            }
        }
    }
    
    public function getWxPayParam(){
        $_group = $this->_M->selectOne('group',array('id'=>$this->_G['groupId']),array('price','name','owner'));
        $_nowTime = time();
        $_orderid = ''.$_nowTime.mt_rand(10000,99999); 
        $_dealRecord['orderid'] = $_orderid;
        $_dealRecord['uniacid'] = $this->_U['uniacid'];
        $_dealRecord['groupid'] = $this->_G['groupId'];
        $_dealRecord['pay_user'] = $this->_U['uid'];
        $_dealRecord['take_user'] = $_group['owner'];
        $_dealRecord['price'] = $_group['price'];
        $_dealRecord['time'] = $_nowTime; 
        $this->_M->insert('deal_record',$_dealRecord);
        
        $_config = new ConfigAction();
        $_param = $_config->findConfig(array('pay_appid','pay_account','pay_key'));
        $_data['appid'] = $_param['pay_appid'];
        $_data['mch_id'] = $_param['pay_account'];
        $_data['nonce_str'] = Tool::createCode();
        $_data['body'] = '社群<'.$_group['name'].'>入群支付';
        $_data['out_trade_no'] = $_orderid; 
        $_data['total_fee'] = $_group['price']*100;
        $_data['spbill_create_ip'] =$this->_W['clientip'];
        $_data['notify_url'] = substr($this->_W['siteroot'],0,-1).$this->_W['script_name'];
        $_data['trade_type'] = 'JSAPI';
        $_data['openid'] = $this->_W['openid'];
        ksort($_data);
        foreach($_data as $_key=>$_value){
            $_stringA .= $_key.'='.$_value.'&';
        }
        $_string = $_stringA.'key='.$_param['pay_key'];
        $_data['sign'] = strtoupper(md5($_string));
        $_xml = array2xml($_data,1); 
        $_payReturn = $this->wxHttpsRequestPem('https://api.mch.weixin.qq.com/pay/unifiedorder',$_xml);
        $_return = xml2array($_payReturn); 
        if($_return['return_code'] == 'SUCCESS'){
                $_jsApi['appId'] = $_param['pay_appid'];
                $_jsApi['timeStamp'] =''.time();
                $_jsApi['nonceStr'] = Tool::createCode();
                $_jsApi['package'] = 'prepay_id='.$_return['prepay_id'];
                $_jsApi['signType'] = 'MD5'; 
                ksort($_jsApi);
                $_stringB = '';
                foreach($_jsApi as $_key=>$_value){
                    $_stringB .= $_key.'='.$_value.'&';
                }
                $_stringTwo = $_stringB.'key='.$_param['pay_key'];   
                $_jsApi['paySign'] = strtoupper(md5($_stringTwo));
                $_jsApi['orderid'] = $_orderid;  
                return json_encode($_jsApi);  
        }
    }
    
    
    public function userExistsGroup($_groupid,$_userid = ''){
        if(empty($_userid)) $_userid = $this->_U['uid'];
        return $this->_M->selectOne('group_member',array('uniacid'=>$this->_U['uniacid'],'group_id'=>$_groupid,'member_id'=>$_userid),array('id'));
    }
    
    
    public function ajaxCheck(){
        if($this->_G['result'] == 'pass'){
            $_succ = $this->_M->update('group',array('status'=>1),array('id'=>$this->_G['groupid']));
            if($_succ){
                $_one = $this->_M->selectOne('group',array('id'=>$this->_G['groupid']),array('owner','title'));
                $_openid = $this->_M->selectOne('member',array('userid'=>$_one['owner']),array('openid'));
                $_config = new ConfigAction();
                $_tpl = $_config->findConfig(array('check_group_tpl'));
                $_data = array('first'=>array('value'=>'社群审核通过','color'=>'#000'),
                    'keyword1'=>array('value'=>$_one['title']),
                    'keyword2'=>array('value'=>'社群审核通过') 
                );
                Wx::tplMessage($_openid['openid'],$_tpl['check_group_tpl'],$_data,murl('entry//m_me_group',array('m'=>'zm_super_group'),false,true));   
                return 1;
            }
            return 0;
        }   
        
        if($this->_G['result'] == 'down'){
            $_succ = $this->_M->update('group',array('status'=>3),array('id'=>$this->_G['groupid']));
            if($_succ){
                $_one = $this->_M->selectOne('group',array('id'=>$this->_G['groupid']),array('owner','title'));
                $_openid = $this->_M->selectOne('member',array('userid'=>$_one['owner']),array('openid'));
                $_config = new ConfigAction();
                $_tpl = $_config->findConfig(array('check_group_tpl'));
                $_data = array('first'=>array('value'=>'社群审核未通过','color'=>'#000'),
                    'keyword1'=>array('value'=>$_one['title']),
                    'keyword2'=>array('value'=>'请更改社群信息')  
                ); 
                Wx::tplMessage($_openid['openid'],$_tpl['check_group_tpl'],$_data,murl('entry//m_me_group',array('m'=>'zm_super_group'),false,true));
                return 1;
            }
            return 0;
        }
    }
    
    
    public function idToGroupId($_id = ''){
        $_id = empty($_id) ? $this->_G['groupid'] : $_id;
        $_one = $this->_M->selectOne('group',array('id'=>$_id),array('group_id'));
        return $_one['group_id'];
    }
    
    public function findOne(){
        $_one = $this->_M->selectOne('group',array('id'=>$this->_G['groupid']));
        $_owner = $this->_M->selectOne('member',array('userid'=>$_one['owner']),array('nickname','header'));
        $_class = $this->_M->selectOne('class',array('id'=>$_one['class_id']),array('name'));
        $_one['ownerNickname'] = $_owner['nickname'];
        $_one['ownerHeader'] = $_owner['header'];
        $_one['className'] = $_class['name'];
        return $_one;
    }

    
    public function findHot($_limit1,$_limit2){
        $_list = $this->_M->selectAll('group',array('uniacid'=>$this->_U['uniacid'],'status'=>1),array('order'=>'ORDER BY browse_count DESC','limit'=>'LIMIT '.$_limit1.','.$_limit2));
        return $_list;
    }
    
    public function setBoutique(){
        if($this->_G['nowstatus'] == 0){
            $_param['boutique'] = 1;
        }else{
            $_param['boutique'] = 0;
        }
        $_setSucc = $this->_M->update('group',$_param,array('id'=>$this->_G['groupid']));
        return $_setSucc ? 2 : 0;
    }
    
    public function findRecommend(){
        $_list = $this->_M->selectAll('group',array('uniacid'=>$this->_U['uniacid'],'recommend'=>1,'status'=>1));
        return $_list;
    }
    
    
    public function setRecommend(){
        $_nowNum = $this->_M->total('group',array('uniacid'=>$this->_U['uniacid'],'recommend'=>'1')); 
        if($this->_G['nowstatus'] == 0){
            $_param['recommend'] = 1;
            if($_nowNum >= 5) return 1;  //等于1表示每日推荐个数已经上线，每日推荐上限为5个
        }else{
            $_param['recommend'] = 0;
        }
        $_setSucc = $this->_M->update('group',$_param,array('id'=>$this->_G['groupid']));
        return $_setSucc ? 2 : 0;
    } 
     
    public function getPage(){
        return $this->_showPage;
    }
    
    
    public function ajaxFindClassGroup($_mobileUrl = ''){
        if(isset($this->_G['classid'])){
            $_list = $this->_M->selectAll('group',array('uniacid'=>$this->_U['uniacid'],'class_id'=>$this->_G['classid'],'status'=>1),array('order'=>'ORDER BY id DESC','limit'=>'LIMIT '.$this->_G['start'].','.$this->_G['end']));    
        }
        if(isset($this->_G['classstr'])){
            if($this->_G['classstr'] == 'free'){
                $_data['enter <'] = 3;
            }
            if($this->_G['classstr'] == 'lowPrice'){
                $_data['enter'] = 3;
                $_data['price <'] = 5; 
            }
            if($this->_G['classstr'] == 'boutique'){
                $_data['boutique'] = 1;
            }
            $_data['uniacid'] = $this->_U['uniacid'];
            $_data['status'] = 1;
            $_data['class_id !='] = 0; 
            $_list = $this->_M->selectAll('group',$_data,array('order'=>'ORDER BY id DESC','limit'=>'LIMIT '.$this->_G['start'].','.$this->_G['end']));
        }
        foreach($_list as $_key=>$_value){
            $_list[$_key]['img_dir'] = $this->_W['attachurl'];
            if($_value['enter'] <= 2){ 
                $_list[$_key]['str_price'] = '免费';
            }else if($_value['enter'] == 3){
                $_list[$_key]['str_price'] = '￥'.$_value['price'];
            }
            $_href = $_mobileUrl.'&groupid='.$_value['id'];
            $_list[$_key]['href'] = $_href;
        } 
        return $_list ? json_encode($_list) : 0;
    }
    
    
    public function ajaxFindOne(){
        $_one = $this->_M->selectOne('group',array('id'=>$this->_G['groupid'])); 
        $_member = $this->_M->selectOne('member',array('userid'=>$_one['owner']),array('nickname','header'));
        $_class = $this->_M->selectOne('class',array('id'=>$_one['class_id']),array('name'));
        $_one['ownerNickname'] = $_member['nickname'];
        $_one['ownerHeader'] = $_member['header'];
        $_one['className'] = $_class['name'];
        $_one['img_dir'] = $this->_W['attachurl'];
        if($_one['enter'] == 1){
            $_one['str_enter'] = '验证进入';
            $_one['str_price'] = '';
        }else if($_one['enter'] == 2){
            $_one['str_enter'] = '无需验证';
            $_one['str_price'] = '';
        }else if($_one['enter'] == 3){
            $_one['str_enter'] = '付费进入';
            $_one['str_price'] = '<dt>付费金额</dt><dd>￥'.$_one['price'].'</dd>'; 
        }
        return $_one ? json_encode($_one) : 0;
    }
    
    
    public function delete(){
        $_imStatus = $this->_imApi->group_destroy_group($this->_G['groupid']);     
        if($_imStatus['ActionStatus'] == 'OK'){
            return $this->_M->delete('group',array('group_id'=>$this->_G['groupid'])); 
        }
        return false;
    } 
    
    
    public function findAll($_status = 1){
        if(isset($this->_G['find'])){
            if($this->_G['find'] == 'recommend'){
                $_data['recommend'] = 1;
            }
            if($this->_G['find'] == 'boutique'){
                $_data['boutique'] = 1;
            }
        }
        $_data['uniacid'] = $this->_U['uniacid'];
        $_data['status'] = $_status;
        $_data['class_id !='] = 0;  
        $this->_showPage = $this->page($this->_M->total('group',$_data));
        $_list = $this->_M->selectAll('group',$_data,array('order'=>'ORDER BY id DESC','limit'=>'LIMIT '.$this->_limit1.','.$this->_pagesize));   
        foreach($_list as $_key=>$_value){
            $this->existsImGroup($_value['group_id']);   
            $_owner = $this->_M->selectOne('member',array('userid'=>$_value['owner']),array('nickname','header'));
            $_list[$_key]['ownerNickname'] = $_owner['nickname']; 
        }
        return $_list; 
    }
    
    public function update(){
        $_face_url = $this->_W['attachurl'].$this->_G['cover'];
        if(isset($this->_G['owner'])){
            if($this->_G['owner'] != $this->_G['original_owner']){
                $_addStatus = $this->_imApi->group_add_group_member($this->_G['groupid'],$this->_G['owner'],1);
                $_changeOwner = $this->_imApi->group_change_group_owner($this->_G['groupid'],$this->_G['owner']); 
            }
        }
        $_returnInfo = $this->_imApi->group_modify_group_base_info2($this->_G['groupid'],$this->_G['name'],array('introduction'=>$this->_G['brief'],'face_url'=>$_face_url));
        if($_returnInfo['ActionStatus'] == 'OK'){
            $_data['title'] = $this->_G['title'];
            $_data['name'] = $this->_G['name'];
            $_data['cover'] = $this->_G['cover'];
            $_data['header'] = $this->_G['header'];
            $_data['brief'] = $this->_G['brief'];
            $_data['owner'] = $this->_G['owner'];
            $_data['class_id'] = $this->_G['class_id'];
            $_data['enter'] = $this->_G['enter'];
            $_data['price'] = $this->_G['price']; 
            if(!empty($this->_U['uid'])){
                $_data['status'] = 2;
            }
            return $this->_M->update('group',$_data,array('group_id'=>$this->_G['groupid']));
        }
    } 
    
    
     
    public function add(){
        $_face_url = $this->_W['attachurl'].$this->_G['cover'];
        $_returnInfo = $this->_imApi->group_create_group2('Public',$this->_G['name'],$this->_G['owner'],array('introduction'=>$this->_G['brief'],'face_url'=>$_face_url));
        if($_returnInfo['ActionStatus'] == 'OK'){
            $_data['uniacid'] = $this->_U['uniacid'];
            $_data['title'] = $this->_G['title']; 
            $_data['name'] = $this->_G['name'];
            $_data['group_id'] = $_returnInfo['GroupId']; 
            $_data['cover'] = $this->_G['cover'];
            $_data['header'] = $this->_G['header'];
            $_data['brief'] = $this->_G['brief'];
            $_data['owner'] = $this->_G['owner'];
            $_data['class_id'] = $this->_G['class_id'];
            $_data['create_time'] = time();
            $_data['enter'] = $this->_G['enter'];
            $_data['price'] = $this->_G['price'];   
            if(!empty($this->_U['uid'])){
                $_data['status'] = 2;
            }
            if(!empty($this->_U['uid'])){
                $_isSucc = $this->_M->insert('group',$_data);
                if($_isSucc){
                    $_config = new ConfigAction();
                    //用户关注设置通知当前用户
                    $_tpl = $_config->findConfig(array('check_group_tpl'));
                    $_openid = mc_uid2openid($this->_U['uid']); 
                    $_data = array('first'=>array('value'=>'社群审核中'),
                        'keyword1'=>array('value'=>'您创建的社群—'.$_data['name'].'正在审核中，请耐心等待！'),
                        'keyword2'=>array('value'=>'社群审核通知')
                    );
                    Wx::tplMessage($_openid,$_tpl['check_group_tpl'],$_data);
                }
                return true;
            }else{
                return $this->_M->insert('group',$_data);
            }
        }
    }
    
    
    public function existsImGroup($_groupid){
        $_imState = $this->_imApi->group_get_group_info($_groupid);
        if($_imState['GroupInfo'][0]['ErrorCode'] == '10010'){ 
            $this->_M->delete('group',array('group_id'=>$_groupid));
        }
    }
    
    /**
     * 是否为社群的所有者
     */
    public function isOwner($_groupid = '',$_userid = ''){
        if(empty($_groupid)) $_groupid = $this->_G['groupid'];
        if(empty($_userid)) $_userid = $this->_U['uid'];
        return $this->_M->selectOne('group',array('id'=>$_groupid,'owner'=>$_userid),array('id'));
    }  
    
    
    private function wxHttpsRequestPem($url, $vars, $second=30,$aHeader=array()){
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);   
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
    
    
    public function uploadImageA(){
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
        $_return['dataUrl'] = $_path[0];
        $_return['imgUrl'] = $this->_W['attachurl'].$_path[0];
        return json_encode($_return);
    }
    
    public function privateTpl($_seq){ 
        $_groupId = $this->_M->selectOne('group',array('id'=>$this->_G['groupid']),array('group_id'));
        $_msg = $this->_M->selectOne('msg',array('seq'=>$_seq,'is_read'=>0,'groupid'=>$_groupId['group_id']),array('userid','groupid'));
        
        if($_msg){
            $_groupId = $this->_M->selectOne('group',array('group_id'=>$_msg['groupid']),array('id')); 
            $_privateUser = $this->_M->selectOne('private',array('group_id'=>$_groupId['id']),array('user1','user2'));
       
   
            if($_privateUser['user1'] == $_msg['userid']){
                $_tplUser = $_privateUser['user2'];
            }else if($_privateUser['user2'] == $_msg['userid']){
                $_tplUser = $_privateUser['user1'];  
            }
                 
            $_member = $this->_M->selectOne('member',array('userid'=>$this->_U['uid']),array('nickname')); 
            $_config = new ConfigAction();
            $_tpl = $_config->findConfig(array('check_group_tpl'));
            $_openid = mc_uid2openid($_tplUser);
            
            $_data = array('first'=>array('value'=>'社群消息通知'),
                'keyword1'=>array('value'=>$_member['nickname'].'给您发送了新消息！'),
                'keyword2'=>array('value'=>'点击查看') 
            );
            
           Wx::tplMessage($_openid,$_tpl['check_group_tpl'],$_data,murl('entry//m_room',array('m'=>'zm_super_group','groupid'=>$this->_G['groupid'],'private'=>1),false,true)); 

        }
    }
    
    
}