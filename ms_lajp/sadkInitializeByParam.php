<?php

require_once("php_java.php");//引用LAJP提供的PHP脚本

   
try
{
 
  //效果和配置文件初始化的方法等同
   /**
     * 初始化，调用其他方式之前必须先调用初始化接口，可以在进程启动或者证书更新时调用该接口
     * 
     * @param myPrivateFile
     *            用户私钥文件路径
     * @param myPrivateFilePassword
     *            用户私钥密码
     * @param myCMBCCertFile
     *            民生公钥证书路径
     */
  $privatePath = $_REQUEST['privatePath'];
  $privatePassword = $_REQUEST['privatePassword'];
  $publicPath = $_REQUEST['publicPath'];
  $ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::Initialize", $privatePath,$privatePassword,$publicPath);
  
  echo "{$privatePath}<br>";
  echo "{$ret}<br>";
}
catch(Exception $e)
{
  echo "Err:{$e}<br>";
}
?>

 <a href="index.html">return</a>