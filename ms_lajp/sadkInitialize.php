<?php

require_once("php_java.php");//引用LAJP提供的PHP脚本
 
try
{
	 
  
	/**
     * 初始化，调用其他方式之前必须先调用初始化接口，可以在进程启动或者证书更新时调用该接口
     * 
     * 通过配置文件初始化，配置文件中必须包含（用户私钥文件路径，用户私钥密码，民生公钥证书路径）
     * 
     * @param myConfigFile 用户配置文件
     *
     * 初始化之后的java对象是全局的，重复初始化会报错，全局初始化一次即可
     */
  $ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::Initialize", "config/demo.properties_2018");
  // $ret = lajp_call("hello.HelloClass::hello", $name);
 
    
  echo "{$ret}<br>";
}
catch(Exception $e)
{
  echo "Err:{$e}<br>";
}
?>

 <a href="index.html">return</a>