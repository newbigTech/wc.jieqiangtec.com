<?php
class Tool{
    
    
    /**
     * [getDecimalsLen 计算小数后的位数]
     * @param  [int] $_num [要计算的数字]
     * @return [type]       [如果是一个小数返回小数后的位数，如果不是小数直接返回false]
     */
    static function getDecimalsLen($_num){
        $_temp = explode('.',$_num);
        if(count($_temp) == 2){
            return strlen($_temp[1]);
        }
        return false;
    }
    


    /**
     * [createCode 生成系统唯一标识符8位]
     * @return [type] [系统唯一标识符 字母+数字]
     */
    static function createCode(){
        return sprintf('%x',crc32(microtime()));
    }


    

    /**
     * [arraySort 将数组按照指定的key值进行排序]
     * @param  Array  $_array [要排序的数组]
     * @param  [string] $_key   [要按照指定的key值]
     * @param  string $_order [排序方法  正序asc   倒序 desc  默认正序]
     * @return [array]         [返回排序后新的数组]
     */
    static function arraySort(Array $_array,$_key,$_order = 'asc'){
        
        $keysvalue = $new_array = array(); 
        foreach ($_array as $k=>$v){
            $keysvalue[$k] = $v[$_key];
        }
        if($_order== 'asc'){
            asort($keysvalue);
        }else{
            arsort($keysvalue);
        }    
        reset($keysvalue);
        foreach ($keysvalue as $k=>$v){
            $new_array[] = $_array[$k];
        }
        return $new_array;
    }
        


    /**
     * [GetIp 获取用户客户端真实IP]
     */
    static public function GetIp(){
        $realip = '';
        $unknown = 'unknown';
        if (isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach($arr as $ip){
                    $ip = trim($ip);
                    if ($ip != 'unknown'){
                        $realip = $ip;
                        break;
                    }
                }
            }else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){
                $realip = $_SERVER['REMOTE_ADDR'];
            }else{
                $realip = $unknown;
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            }else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){
                $realip = getenv("HTTP_CLIENT_IP");
            }else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){
                $realip = getenv("REMOTE_ADDR");
            }else{
                $realip = $unknown;
            }
        }
        $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
        return $realip;
    }
    
    
    /**
     * [ipToCityInfo 通过IP地址获取城市信息，不推荐使用，开启4G网络不准确]
     * @param  string $_ip [IP地址]
     * @return [array]      [返回城市信息]
     */
    static function ipToCityInfo($_ip = ''){
        if(empty($_ip)){
            $_ip = self::GetIp();
        }
        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $_ip);
        if(empty($res)){ return false; }
        $jsonMatches = array();
        preg_match('#\{.+?\}#', $res, $jsonMatches);
        if(!isset($jsonMatches[0])){ return false; }
        $_json = json_decode($jsonMatches[0], true);
        if(isset($_json['ret']) && $_json['ret'] == 1){
            $_json['ip'] = $_ip;
            unset($_json['ret']);
        }else{
            return false;
        }
        return $_json;
    }
    
    
    
    
}