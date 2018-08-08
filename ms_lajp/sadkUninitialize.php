<?php

require_once("php_java.php");//引用LAJP提供的PHP脚本

  
try
{
	 /**
     * 反初始化，可以在证书更新时调用该接口
     */
  $ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::Uninitialize");
     
  echo "{$ret}<br>";
}
catch(Exception $e)
{
  echo "Err:{$e}<br>";
}
?>

 <a href="index.html">return</a>