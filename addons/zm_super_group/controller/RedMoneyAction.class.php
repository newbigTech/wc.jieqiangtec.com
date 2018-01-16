<?php
class RedMoneyAction extends Action{
    
    public function __construct($_openImApi = false){
            parent::__construct($_openImApi);
        } 
        
    public function findRedDetails(){
        $_red = $this->_M->selectOne('msg_red',array('id'=>$this->_G['redId']),array('number'));
        $_list = $this->_M->selectAll('red_record',array('red_id'=>$this->_G['redId'],'take_user !='=>0),array('take_user','time','sum'));
        $_nowUserSum = 0;
        foreach($_list as $_key=>$_value){
            $_member = $this->_M->selectOne('member',array('userid'=>$_value['take_user']),array('header','nickname'));
            $_list[$_key]['header'] = $_member['header'];
            $_list[$_key]['nickname'] = $_member['nickname'];
            $_list[$_key]['time'] = date('m-d H:i:s',$_value['time']);
            if($_value['take_user'] == $this->_U['uid']){
                $_nowUserSum = $_value['sum'];
            }
        } 
        $_surplus = $_red['number'] - count($_list);
        $_surplusStr = $_red['number'].'个红包，剩余'.$_surplus.'个';
        array_unshift($_list,$_surplusStr,$_nowUserSum);
        return $_list;
    }
    
    /**
     * 将红包发给当前用户
     */
    public function giveRed(){
        $_one = $this->_M->selectOne('red_record',array('red_id'=>$this->_G['redId'],'take_user'=>0),array('id','sum','red_id'));
        $this->_M->update('red_record',array('take_user'=>$this->_U['uid'],'time'=>time()),array('id'=>$_one['id']));
        $this->_M->updateMath('member',array('money'=>'++'.$_one['sum'].'++'),array('userid'=>$this->_U['uid']));
        return $_one;
    }
  
    /**
     * 当前用户是否已经抢过红包
     */
    public function isRob(){
        return $this->_M->selectOne('red_record',array('red_id'=>$this->_G['redId'],'take_user'=>$this->_U['uid']),array('id'));
    }
    
    /**
     * 当前红包是否已被抢空
     */
    public function isEmpty(){
        return $this->_M->selectOne('red_record',array('red_id'=>$this->_G['redId'],'take_user'=>0),array('id'));
    }
    
    
    /**
     * 新增打开红包记录
     */
    public function addOpenRedRecord(){
        $_data['userid'] = $this->_U['uid'];
        $_data['red_id'] = $this->_G['redId'];
        $_one = $this->_M->selectOne('red_open_record',$_data,array('id'));
        if(!$_one) $this->_M->insert('red_open_record',$_data); 
    }
    
    public function changePayState($_orderid){
        $this->_M->update('deal_record',array('status'=>1),array('orderid'=>$_orderid));
    }
        
    public function getWxPayParam($_groupId,$_sum){  
            $_nowTime = time();
            $_orderid = ''.$_nowTime.mt_rand(10000,99999); 
            $_dealRecord['orderid'] = $_orderid;
            $_dealRecord['uniacid'] = $this->_U['uniacid'];
            $_dealRecord['groupid'] = $_groupId;
            $_dealRecord['pay_user'] = $this->_U['uid'];
            $_dealRecord['price'] = $_sum; 
            $_dealRecord['time'] = $_nowTime; 
            $_dealRecord['use'] = 1;
            $this->_M->insert('deal_record',$_dealRecord);
            
            $_config = new ConfigAction();
            $_param = $_config->findConfig(array('pay_appid','pay_account','pay_key'));
            $_data['appid'] = $_param['pay_appid'];
            $_data['mch_id'] = $_param['pay_account'];
            $_data['nonce_str'] = Tool::createCode();
            $_data['body'] = '社群红包';
            $_data['out_trade_no'] = $_orderid; 
            $_data['total_fee'] = $_sum*100;
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
    
    
}