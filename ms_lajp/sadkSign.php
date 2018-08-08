<?php

require_once("php_java.php");//引用LAJP提供的PHP脚本

//需要签名的数据，base64格式
$base64Plain  = $_REQUEST['base64Plain'];
try
{
 
    /**
     *  
     * 对数据进行PKCS#7带原文签名，并将签名结果加密成CMS格式的数字信封。
     * 如果为RSA算法，则签名所采用的HASH算法为SHA-256，加密所采用的算法为3DES_CBC.
     * 如果为SM2算法，则签名所采用的HASH算法为SM3(带Z值)，加密所采用的算法为SM4_CBC，注意：为兼容民生其他版本工具包，SM2加密格式为老国密标准C1||C2||C3。
     *  
     * 
     * @param base64PlainMessage
     *            BASE64编码的待签名加密的原文数据
     * @return base64EnvelopeMessage BASE64编码的签名加密结果
     * 
     */
	$ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::SignAndEncryptMessage", $base64Plain);
  echo "{$ret}<br>";
}
catch(Exception $e)
{
  echo "Err:{$e}<br>";
}
?>
<a href="index.html">return</a>