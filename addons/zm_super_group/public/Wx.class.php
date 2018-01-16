<?php
/**
 * 微信常用方法，静态
 */
class Wx{

    /**
     * [wxAccessToken 获取微信AccessToken]
     * @param  [string] $appId     [公众号appid]
     * @param  [string] $appSecret [公众号appSecret]
     * @return [string]            [返回公众号AccessToken]
     */
    static public function wxAccessToken($appId,$appSecret){
        $url= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appSecret;
        $result= self::wxHttpsRequest($url);
        //print_r($result);
        $jsoninfo= json_decode($result, true);
        $access_token   = $jsoninfo["access_token"];
        return $access_token;
    }
    
    

    /**
     * [wxHttpsRequest 发送微信请求]
     * @param  [string] $url  [请求接口API地址]
     * @param  [xml] $data [xml格式的请求数据]
     * @return [xml]       [返回请求内容]
     */
    static public function wxHttpsRequest($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    
    
    /****************************************************
     *  微信生成二维码ticket
     ****************************************************/
    static public function wxQrCodeTicket($jsonData,$wxAccessToken){
        //$wxAccessToken  = $this->wxAccessToken();
        $url        = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$wxAccessToken;
        $result         = self::wxHttpsRequest($url,$jsonData);
        return $result;
    }
    
    
    /****************************************************
     *  微信通过ticket获取二维码
     ****************************************************/
    static public function wxQrCode($ticket){
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
        return $url;
    }
    
    
    /**
     * [wxJs 微信JS引入以及配置]
     * @param  string      $_debug   [开发开启开发模式]  默认false不开启   true开启
     * @param  Array|array $_apiList [微信JS功能列表：参照微信开发文档jssdk附录2]
     * @return [type]                [返回微信JS代码]
     */
    static public function wxJs($_debug = 'false',Array $_apiList = array()){
        global $_W;
        if(count($_apiList) != 0){
            foreach($_apiList as $_value){
                $_apiStr .= "'".$_value."',";
            }
            $_apiStr = substr($_apiStr,0,-1);
        }
        $_wxJs = "<script src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>";
        $_wxJs .= "<script>\r\n";
        $_wxJs .= "wx.config({\r\n"; 
        $_wxJs .= "debug:$_debug ,\r\n";
        $_wxJs .= "appId:'{$_W['account']['jssdkconfig']['appId']}',\r\n";
        $_wxJs .=" timestamp:'{$_W['account']['jssdkconfig']['timestamp']}',\r\n";
        $_wxJs .="nonceStr:'{$_W['account']['jssdkconfig']['nonceStr']}',\r\n";
        $_wxJs .="signature:'{$_W['account']['jssdkconfig']['signature']}',\r\n";
        $_wxJs .="jsApiList:[$_apiStr]\r\n";
        $_wxJs .="});\r\n";
        $_wxJs .= "</script>\r\n";
        return $_wxJs;
    }
    
    
    
    
    //模板消息
    /**
     * [tplMessage 模板消息]
     * @param  [string] $_openid [用户openid]
     * @param  [string] $_tplid  [tplID]
     * @param  [array] $_data   [模板消息内容]
     * @return [type]          [返回响应用户的模板消息]
     */
    static function tplMessage($_openid,$_tplid,$_data,$_url = null){
        global $_W;
        load()->classs('weixin.account');     
        $_tpl = new WeiXinAccount($_W['account']);    
        $_tpl->sendTplNotice($_openid,$_tplid,$_data,$_url);  
    }
    
    
}