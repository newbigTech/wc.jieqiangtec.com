<?php
class MemberAction extends Action{
    private $_showPage = '';
    
    public function __construct($_openImApi = false){
        parent::__construct($_openImApi);
    } 
    
    public function findMeGroup($_start,$_end){
        $_list = $this->_M->selectAll('group',array('uniacid'=>$this->_U['uniacid'],'owner'=>$this->_U['uid'],'class_id !='=>0),array('order'=>'ORDER BY id DESC','limit'=>'LIMIT '.$_start.','.$_end),array('id','header','title','name','enter','price','browse_count','status'));
        foreach($_list as $_key=>$_value){
            $_list[$_key]['header'] = tomedia($_value['header']);
            $_list[$_key]['url'] = murl('entry//m_group_door',array('m'=>'zm_super_group','groupid'=>$_value['id']));
            if($_value['status'] > 1){
                if($_value['status'] == 2){
                    $_list[$_key]['strStatus'] = '<span class="price" style="color:#666;">审核中</span>';
                }elseif($_value['status'] == 3){
                    $_list[$_key]['strStatus'] = '<span class="price" style="color:#FFA500;">未通过</span>';
                }
            }else{
                if($_value['enter'] == 2){
                    $_list[$_key]['strStatus'] = '<span class="price" style="color:#FFA500;">免费</span>';
                }elseif($_value['enter'] == 3){
                    $_list[$_key]['strStatus'] = '<span class="price">￥'.$_value['price'].'</span>';
                }
            }
        }
        return $_list;
    }
    
    public function findDepositRecord($_start = 0,$_end = 20,$_userid = '',$_isAjax = false){
        if(empty($_userid)) $_userid = $this->_U['uid'];
        $_record = $this->_M->selectAll('deposit',array('uniacid'=>$this->_U['uniacid'],'userid'=>$_userid),array('order'=>'ORDER BY time DESC','limit'=>'LIMIT '.$_start.','.$_end),array('time','status','money'));
        foreach($_record as $_key=>$_value){
            $_record[$_key]['time'] = date('Y-m-d',$_value['time']);
            if($_value['status'] == 0){
                $_record[$_key]['strStatus'] = '未提现';
            }else if($_record['status'] == 1){
                $_record[$_key]['strStatus'] = '已提现';
            }else{
                $_record[$_key]['strStatus'] = '提现失败';
            } 
        }
        return $_record; 
    } 
    
    public function payRecord(){
        $this->_showPage = $this->page($this->_M->total('deal_record',array('uniacid'=>$this->_U['uniacid'])));
        $_list = $this->_M->selectAll('deal_record',array('uniacid'=>$this->_U['uniacid']),array('order'=>'ORDER BY time DESC','limit'=>'LIMIT '.$this->_limit1.','.$this->_pagesize),array('groupid','pay_user','price','status','time'));
        foreach($_list as $_key=>$_value){
            $_member = $this->_M->selectOne('member',array('userid'=>$_value['pay_user']),array('nickname','header'));
            $_group = $this->_M->selectOne('group',array('id'=>$_value['groupid']),array('name'));
            $_list[$_key]['nickname'] = $_member['nickname'];
            $_list[$_key]['header'] = $_member['header'];
            $_list[$_key]['groupName'] = $_group['name'];
        }
        return $_list;
    }
    
    public function findDepositList(){
        $_data['a.uniacid'] = $_page['uniacid'] = $this->_U['uniacid'];
        $_data['a.status'] = $_page['status'] = $this->_G['depositStatus'];
        $this->_showPage = $this->page($this->_M->total('deposit',$_page));   
        $_list = $this->_M->joinLeft(array('deposit','member'),array('a.userid','b.userid'),$_data,array('order'=>'ORDER BY a.time DESC','limit'=>'LIMIT '.$this->_limit1.','.$this->_pagesize),array('a.money,a.status,a.time,a.charge_money,a.id,b.nickname'));
        return $_list;
    } 
    
    
    public function deposit(){
        $_userMoney = $this->getMemberInfo(array('money'));
        if($_userMoney['money'] < $this->_G['depostiMoney']){
            $_return['status'] = 0;   //当前用户余额不足
            return $_return;
        }
        
        
        $this->_M->updateMath('member',array('money'=>'--'.$this->_G['depositMoney'].'--'),array('userid'=>$this->_U['uid']));
        $_config = new ConfigAction();
        $_charge = $_config->findConfig(array('deposit_charge'));
        $_deposit['uniacid'] = $this->_U['uniacid'];
        $_deposit['userid'] = $this->_U['uid'];
        $_deposit['money'] = $this->_G['depositMoney'];
        $_deposit['charge_money'] = $_deposit['money'] * ($_charge['deposit_charge'] / 100);
        $_deposit['time'] = time();
        $_succ = $this->_M->insert('deposit',$_deposit); 
        if($_succ){
            $_config = new ConfigAction();
            //用户提现审核通知
            $_tpl = $_config->findConfig(array('check_group_tpl'));
            $_openid = mc_uid2openid($this->_U['uid']);
            $_data = array('first'=>array('value'=>'提现正在处理中'),
                'keyword1'=>array('value'=>'您本次提现'.$this->_G['depositMoney'].'元，手续费为'.$_deposit['charge_money'].'元'),
                'keyword2'=>array('value'=>'提现状态通知')
            );
            Wx::tplMessage($_openid,$_tpl['check_group_tpl'],$_data,murl('entry//m_deposit_record',array('m'=>'zm_super_group'),false,true)); 
            $_return['status'] = 1;
            return $_return;   
        }
    }
    
    public function getBought($_start = 0,$_end = 20,$_userid = '',$_isAjax = false){  
        if(empty($_userid)) $_userid = $this->_U['uid'];
        $_record = $this->_M->joinLeft(array('deal_record','group'),array('a.groupid','b.id'),array('a.uniacid'=>$this->_U['uniacid'],'a.pay_user'=>$_userid,'a.status'=>1,'take_user !='=>0),array('order'=>'ORDER BY a.time DESC','limit'=>'LIMIT '.$_start.','.$_end),array('a.price,a.time,b.title,b.name,b.header,b.id'));
        if($_isAjax){
            foreach($_record as $_key=>$_value){
                $_record[$_key]['time'] = date('Y-m-d H:i:s',$_value['time']);  
                if(mb_strlen($_value['title']) > 25){
                    $_record[$_key]['title'] = mb_substr($_value['title'],0,25).'...'; 
                }
                $_record[$_key]['header'] = tomedia($_value['header']);
                $_record[$_key]['url'] = murl('entry//m_group_door',array('m'=>'zm_super_group','groupid'=>$_value['id']));
            }
        }
        return $_record; 
    }
    
    public function getEarningsRecord($_start = 0,$_end = 20,$_userid = '',$_isAjax = false){
        if(empty($_userid)) $_userid = $this->_U['uid'];
        $_record = $this->_M->joinLeftRight(array('deal_record','group','member'),array('a.groupid','b.id','a.pay_user','c.userid'),array('a.uniacid'=>$this->_U['uniacid'],'a.take_user'=>$_userid,'a.status'=>1),array('order'=>'ORDER BY a.time DESC','limit'=>'LIMIT '.$_start.','.$_end),array('a.time,a.price,b.title,c.header,c.nickname'));
        if($_isAjax){
            foreach($_record as $_key=>$_value){
                 $_record[$_key]['time'] = date('Y-m-d',$_value['time']);
            }
        }
        return $_record;
    }
    
    public function getDealRecord($_start = 0,$_end = 20,$_userid = '',$_isAjax = false){
        if(empty($_userid)) $_userid = $this->_U['uid'];  
        $_record = $this->_M->selectAll('deal_record',array('uniacid'=>$this->_U['uniacid'],'status'=>1,'pay_user'=>$_userid),array('order'=>'ORDER BY time DESC','limit'=>'LIMIT '.$_start.','.$_end),array('time','price','pay_user'),false,array('take_user'=>$_userid));
        foreach($_record as $_key=>$_value){ 
           if($_isAjax){
                if($_value['pay_user'] == $_userid){
                    $_record[$_key]['price'] = '-'.$_value['price'];
                }else{
                    $_record[$_key]['price'] = '+'.$_value['price'];
                }
                $_record[$_key]['time'] = date('Y-m-d',$_value['time']);
            }else{
                if($_value['pay_user'] == $_userid){
                    $_record[$_key]['isPay'] = 1;
                }else{
                    $_record[$_key]['isPay'] = 0;
                }
           }  
        }  
        return $_record; 
    }
    
    
    public function getMemberInfo(Array $_return,$_userid = ''){
        if(empty($_userid)) $_userid = $this->_U['uid'];
        return $this->_M->selectOne('member',array('userid'=>$_userid),$_return);
    }
    
    public function getTodayEarning($_userid = ''){
        if(empty($_userid)) $_userid = $this->_U['uid']; 
        $_todayTime = strtotime(date('Y-m-d'));
        $_tomorrowTime = $_todayTime + 86400;
        return $this->_M->getSum('deal_record','price',array('uniacid'=>$this->_U['uniacid'],'take_user'=>$_userid,'time >='=>$_todayTime,'time <'=>$_tomorrowTime,'status'=>1));
    } 
    
    public function getTotalEarnings($_userid = ''){
        if(empty($_userid)) $_userid = $this->_U['uid']; 
        return $this->_M->getSum('deal_record','price',array('uniacid'=>$this->_U['uniacid'],'take_user'=>$_userid,'status'=>1));
    } 
    
    public function changeMemberStatus(){
        if(empty($this->_G['status'])){
            $_data['status'] = 1;
        }else{
            $_data['status'] = 0;
        }
        return $this->_M->update('member',$_data,array('userid'=>$this->_G['memberid']));
    }
    
    public function getPage(){
        return $this->_showPage;
    }
    
    public function findAllMember(){
        $_data['uniacid'] = $this->_U['uniacid'];
        if(isset($this->_G['search'])){
            $_data['nickname LIKE'] = "%".$this->_G['keyword']."%";
        }
        $this->_showPage = $this->page($this->_M->total('member',$_data));
        $_member = $this->_M->selectAll('member',$_data,array('order'=>'ORDER BY id DESC','limit'=>'LIMIT '.$this->_limit1.','.$this->_pagesize));
        foreach($_member as $_key => $_value){
            $_member[$_key]['groupNum'] = $this->_M->total('group',array('uniacid'=>$this->_U['uniacid'],'owner'=>$_value['userid'],'status'=>1));
            $_member[$_key]['income'] = $this->_M->getSum('deal_record','price',array('take_user'=>$_value['userid'],'status'=>1));
        }
        return $_member; 
    }
    
    /**
     * 获取所有粉丝
     */
    public function findAllFans($_start = 0,$_end = 15,$_userid = ''){
        if(empty($_userid)) $_userid = $this->_U['uid'];
        $_list = $this->_M->joinLeft(array('fans','member'),array('a.from_userid','b.userid'),array('a.by_userid'=>$_userid,'a.status'=>1),array('order'=>'ORDER BY a.id DESC','limit'=>'LIMIT '.$_start.','.$_end),array('b.nickname,b.header'));
        if($this->_G['requestType'] == 'ajax'){
            foreach($_list as $_key=>$_value){
                $_list[$_key]['header'] = tomedia($_value['header']);    
            }
        }
        return $_list;
    } 
    
    /**
     * ajax搜索用户昵称，返回结果
     */
    public function ajaxSearchNickname(){
        $_user = $this->_M->selectAll('member',array('uniacid'=>$this->_U['uniacid'],'nickname'=>"'".$this->_G['nickname']."'"),array('order'=>'ORDER BY id DESC'),array('nickname','userid','header'));
        return $_user ? json_encode($_user) : 0;
    }
    
    /**
     * 获取关注人数
     */
    public function findAttentionTotal($_userid){
        return $this->_M->total('fans',array('uniacid'=>$this->_U['uniacid'],'by_userid'=>$_userid,'status'=>1));
    }
    
    /**
     * 用户关注状态
     */
    public function attentionStatus($_by_userid,$_from_userid){
        return $this->_M->selectOne('fans',array('uniacid'=>$this->_U['uniacid'],'by_userid'=>$_by_userid,'from_userid'=>$_from_userid,'status'=>1),array('id'));
    }
    
    /**
     * 关注操作
     * @param unknown $_by_userid
     * @param unknown $_from_userid
     */
    public function attention($_by_userid,$_from_userid){
        $_data['uniacid'] = $this->_U['uniacid'];
        $_data['by_userid'] = $_by_userid;
        $_data['from_userid'] = $_from_userid;
        $_exists = $this->_M->selectOne('fans',$_data,array('id'));
        if($_exists){
            $_isSucc = $this->_M->update('fans',array('status'=>$this->_G['change']),array('id'=>$_exists['id']));
        }else{
            $_isSucc = $this->_M->insert('fans',$_data);
        }
        if($this->_G['change']){
        if($_isSucc){
            $_config = new ConfigAction();
            //模板提醒当前用户关注操作
            $_tpl = $_config->findConfig(array('check_group_tpl'));
            $_byMember = $this->_M->selectOne('member',array('userid'=>$_by_userid),array('nickname')); 
            $_openid = mc_uid2openid($this->_U['uid']);
            $_data = array('first'=>array('value'=>'关注成功'),
                'keyword1'=>array('value'=>'您关注了用户—'.$_byMember['nickname']),
                'keyword2'=>array('value'=>'关注用户提醒')
            );
            Wx::tplMessage($_openid,$_tpl['check_group_tpl'],$_data);
            
            //模板提醒被关注操作
            $_openid1 = mc_uid2openid($_by_userid);
            $_formMember = $this->_M->selectOne('member',array('userid'=>$this->_U['uid']),array('nickname'));
            $_data1 = array('first'=>array('value'=>'您的粉丝数量发生变化'),
                'keyword1'=>array('value'=>$_formMember['nickname'].'成为了您的粉丝！'),
                'keyword2'=>array('value'=>'粉丝增长通知')
            ); 
            Wx::tplMessage($_openid1,$_tpl['check_group_tpl'],$_data1,murl('entry//m_fans',array('m'=>'zm_super_group'),false,true));
         }      
        }
        return true;
    }
    
    public function noDeposit(){
        $_deposit = $this->_M->selectOne('deposit',array('id'=>$this->_G['noDeposit']),array('userid','money'));
        $_succ = $this->_M->updateMath('member',array('money'=>'++'.$_deposit['money'].'++'),array('userid'=>$_deposit['userid']));
        return $this->_M->update('deposit',array('status'=>2),array('id'=>$this->_G['noDeposit']));
    }
    
    
    
    //后台提现，给用户提现
    public function getUserDeposit(){
        //获取基础设置商户资料
        $_config = new ConfigAction();
        $_pay = $_config->findConfig(array('pay_appid','pay_appSecret','pay_account','pay_key'));
        
        //获取提现数据
        $_deposit = $this->_M->selectOne('deposit',array('id'=>$this->_G['passDeposit']),array('money','userid','charge_money'));
        //通过useri找到提现用户的OPenid
        $_openid = $this->_M->selectOne('member',array('userid'=>$_deposit['userid']),array('openid'));

        $_data['mch_appid'] = $_pay['pay_appid'];
        $_data['mchid'] = $_pay['pay_account']; 
        $_data['nonce_str'] = Tool::createCode();
        $_data['partner_trade_no'] = md5(uniqid(time(),true));
        $_data['openid'] = $_openid['openid'];
        $_data['check_name'] = 'NO_CHECK';
        $_data['amount'] = ($_deposit['money'] - $_deposit['charge_money']) * 100;
        $_data['desc'] = '提现';
        $_data['spbill_create_ip'] = $_SERVER['SERVER_ADDR']; 
        ksort($_data);
        foreach($_data as $_k=>$_v){
            $_string .= $_k.'='.$_v.'&';
        }
        $_sign = $_string.'key='.$_pay['pay_key'];
        $_data['sign'] = strtoupper(MD5($_sign));
        $_xml = array2xml($_data);
        $_check = $this->wxHttpsRequestPem('https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',$_xml);
        $_louiexml = new SimpleXMLElement($_check);
        if($_louiexml->result_code == 'FAIL'){
            $_status = $_louiexml->err_code;
            if($_status == 'NOAUTH'){
                return '报错信息：'.$_status.'　您的公众号没有该权限！';
            }else if($_status == 'AMOUNT_LIMIT'){
                return '报错信息：'.$_status.'　每次提现金额必须大于1元！';
            }else if($_status =='PARAM_ERROR'){
                return '报错信息：'.$_status.'　参数错误，请检查配置信息！';
            }else if($_status == 'OPENID_ERROR'){
                return '报错信息：'.$_status.'　请检查公众号appid和提现用户的openid！';
            }else if($_status == 'NOTENOUGH'){
                return '报错信息：'.$_status.'　您的微信商户余额不足，请先充值！';
            }else if($_status == 'SYSTEMERROR'){
                return '报错信息：'.$_status.'　系统繁忙，请稍后再试！';
            }else if($_status == 'NAME_MISMATCH'){
                return '报错信息：'.$_status.'　请求参数里填写了需要检验姓名，但是输入了错误的姓名，请检查用户姓名是否正确！';
            }else if($_status == 'SIGN_ERROR'){
                return '报错信息：'.$_status.'　签名错误，没有按照文档要求进行签名！';
            }else if($_status == 'XML_ERROR'){
                return '报错信息：'.$_status.'　Post请求数据不是合法的xml格式内容！';
            }else if($_status == 'FATAL_ERROR'){
                return '报错信息：'.$_status.'　两次请求商户单号一样，但是参数不一致！';
            }else if($_status == 'CA_ERROR'){
                return '报错信息：'.$_status.'　请求没带证书或者带上了错误的证书！';
            }else if($_status == 'V2_ACCOUNT_SIMPLE_BAN'){
                return '报错信息：'.$_status.'　无法给非实名用户付款，请先指导用户在微信支付内进行绑卡实名！';
            }else{  
                return '未知错误';   
            }
        }else{  
            $_isSucc = $this->_M->update('deposit',array('status'=>1),array('id'=>$this->_G['passDeposit']));    
            if($_isSucc){
                $_config = new ConfigAction();
                //用户提现审核通知
                $_tpl = $_config->findConfig(array('check_group_tpl'));
                $_data = array('first'=>array('value'=>'您的提现处理成功'),
                    'keyword1'=>array('value'=>'您本次提现'.$_deposit['money'].'元，手续费为'.$_deposit['charge_money'].'元，实际到账'.($_deposit['money'] - $_deposit['charge_money']).'元'),
                    'keyword2'=>array('value'=>'提现状态通知')
                );
                Wx::tplMessage($_openid['openid'],$_tpl['check_group_tpl'],$_data,murl('entry//m_deposit_record',array('m'=>'zm_super_group'),false,true));
            }
            return true;
        }
    }
    
    

    
    private function wxHttpsRequestPem($url, $vars, $second=30,$aHeader=array()){
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        //以下两种方式需选择一种
        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,ATTACHMENT_ROOT.'zm_super_group/paykey/'.$this->_U['uniacid'].'apiclient_cert.pem');
    
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,ATTACHMENT_ROOT.'zm_super_group/paykey/'.$this->_U['uniacid'].'apiclient_key.pem');
        curl_setopt($ch,CURLOPT_CAINFO,'PEM');
        curl_setopt($ch,CURLOPT_CAINFO,ATTACHMENT_ROOT.'zm_super_group/paykey/'.$this->_U['uniacid'].'rootca.pem');
        //第二种方式，两个文件合成一个.pem文件
        //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');
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
    
    
    
    
}