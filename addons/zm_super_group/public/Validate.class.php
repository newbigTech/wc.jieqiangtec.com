<?php
/**
 * 数据验证类，静态
 */
class Validate{
   
    //是否为空
    static function checkStrNull($_string){
        $_string = trim($_string);
        if(empty($_string)) return true;
        return false;
    }
    
    //验证长度
    static function checkStrLength($_string,$_length,$_type,$_charset='utf-8'){
        $_string = trim($_string);
        switch($_type){
            case 'min':
                if(mb_strlen($_string,$_charset) < $_length) return true;
                return false;
                break;
            case 'max':
                if(mb_strlen($_string,$_charset) > $_length) return true;
                return false;
                break;
            case 'equal':
                if(mb_strlen($_string,$_charset) == $_length) return true;
                return false;
                break;
        }
    }
    
    //验证是否为正整数
    static function checkPositive($_data){
        if($_data >=0 && $_data == floor($_data)) return true;
        return false;
    }
    
    
    //验证是否为手机号
    static function checkPhone($_data){
        $_pattern = '/^1[0-9]{10}$/';
        if(preg_match($_pattern,$_data)) return true;
        return false;
    }
    
    
    
    
    
    
}