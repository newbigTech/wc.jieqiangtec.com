<?php
// 测试民生订单加密
/*数据项	英文名	类型	出现要求	备注
版本号	version	CHAR(10)	R	默认1.0.0
订单号	billNo	CHAR(32)	R	最长支持32位，订单号必须以5位商户号开始，参考格式如下：           商户号（5位）＋YYYYMMDDHHMMSS（14位）+
商户自定义序号（13位）。订单号总长可小于32位，且不可重复。
交易金额	txAmt	DECIMAL(13,2)	R	例如：111111.23
小数点前不能用逗号分隔。
币种	PayerCurr	CHAR(3)	R	目前仅支持人民币，默认为156
交易日期	txDate	CHAR(8)	R	YYYYMMDD
格式示例：20140826
交易时间	txTime	CHAR(6)	R	HHMMSS
格式示例：102347
商户代码	corpID	CHAR(5)	R	由民生银行统一分配 只能为数字
商户名称	corpName	CHAR(62)	R	字母数字汉字不能有>与< ,汉字编码格式统一为utf-8
二级商户号	subCorpID	CHAR (15)	N	只能字母数字
后台异步通知地址	NotifyUrl	CHAR(240)	R	支付成功后我行异步方式通知商户的地址
只能为url地址
前台跳转地址	JumpUrl	CHAR(240)	R	支付成功后页面方式通知商户的地址
只能为url地址
银行卡号	Account	CHAR(30)	N	输入卡号时为指定此卡进行支付
暂时未开放，送空。
只能为字母数字可以为空
交易详细内容	TransInfo	CHAR(60)	N	订单关联的商品信息
字母数字汉字不能有>与< 可以为空
商户预留信息	Message	CHAR(200)	N	商户自定义信息，原值返回
字母数字汉字不能有>与< 可以为空
支付通道	Channel	CHAR(2)	R	0: PC网关
1: 移动网关控件方式
2: 移动网关WAP方式
3: 手机银行
其中，2无需集成民生付移动端SDK。
本字段请写3
只能为0-3种的一个数字
借贷标示	LoanFlag	CHAR(1)	N	只对客户使用本行卡时生效。
0借记卡
1信用卡
不输表示为都支持
只能为0或者1 可以为空
商品类型	ProductType	CHAR(12)	N	商品类型写1
商品名称	ProductName	CHAR(120)	N	字母数字汉字不能有>与< 可以为空
备注	Remark	CHAR(250)	N	字母数字汉字不能有>与< 可以为空*/
var_dump(111);
//$orderInfo = $this->SignAndEncryptMessage($orderInfo);

$obj_order = new Order();
$order_info = array(
    //版本
    'version' => '1.0.0',
//订单号
    'billNo' => time(),
//订单金额
    'txAmt' => 111111.23,
//货币代码
    'PayerCurr' => 156,
//交易日期（年月日）
    'txDate' => date("Ymd"),
//交易时间(时分秒)
    'txTime' => date("Hms"),
//商户代码
    'corpID' => '09025',
    // 'billNo' => 'corpID . 'txDate . 'txTime . substr('billNo, 8, 8),
    'billNo' => '09025' . date("YmdHms") . substr(time(), 8, 8),
//商户名称
    'corpName' => '融惠联',
//二级商户号
    'subCorpID' => '11313',
//后台异步通知地址
    'NotifyUrl' => "http://shop.rongec.com/ms_lajp/receive_order.php",
//前台跳转地址
    'JumpUrl' => 'http://shop.rongec.com/ms_lajp/',
//银行卡号（放空）
    'Account' => '',
//交易详情内容
    'TransInfo' => '测试商品',
//商户预留信息，原值返回
    'Message' => '测试消息_融惠联',
//支付通道0: PC网关 1: 移动网关控件方式 2: 移动网关WAP方式 3: 手机银行其中，2无需集成民生付移动端SDK。
    'Channel' => '3',
//借贷标示
    'LoanFlag' => '',
    'ProductType' => '1',
    'ProductName' => '商品名称_测试物品',
    'Remark' => '这是一个测试备注',
);
// var_dump('$order_info==',$order_info);
$res = $obj_order->ChinaPayPost($order_info);
exit;

// 民生支付
class Order
{

    // 组装数据
    function ChinaPayPost($order)
    {
        /*//版本
        $version = '1.0.0';
        //订单号
        // $billNo = $orders['order_id'];
        $billNo = time();
        //订单金额
        $txAmt = 111111.23;
        //货币代码
        $PayerCurr = 156;
        //交易日期（年月日）
        $txDate = date("Ymd");
        //交易时间(时分秒)
        $txTime = date("Hms");
        //商户代码
        $corpID = '09025';
        $billNo = $corpID . $txDate . $txTime . substr($billNo, 8, 8);
        //商户名称
        $corpName = '融惠联';
        //二级商户号
        $subCorpID = '11313';
        //后台异步通知地址
        $NotifyUrl = "http://shop.rongec.com/ms_lajp/receive_order.php";
        //前台跳转地址
        $JumpUrl = 'http://shop.rongec.com/ms_lajp/';
        //银行卡号（放空）
        $Account = '';
        //交易详情内容
        $TransInfo = '测试商品';
        //商户预留信息，原值返回
        $Message = '测试消息_融惠联';
        //支付通道0: PC网关 1: 移动网关控件方式 2: 移动网关WAP方式 3: 手机银行其中，2无需集成民生付移动端SDK。
        $Channel = '3';
        //借贷标示
        $LoanFlag = '';
        $ProductType = '1';
        $ProductName = '商品名称_测试物品';
        $Remark = '这是一个测试备注';*/

        /*$orderInfo = array(
            'version' => $order['version'],
            'billNo' => $order['billNo'],
            'txAmt' => $order['txAmt'],
            'PayerCurr' => $order['PayerCurr'],
            'txDate' => $order['txDate'],
            'txTime' => $order['txTime'],
            'corpId' => $order['corpID'],
            'corpName' => $order['corpName'],
            'subCorpId' => $order['subCorpID'],
            'NotifyUrl' => $order['NotifyUrl'],
            'JumpUrl' => $order['JumpUrl'],
            'Account' => $order['Account'],
            'TransInfo' => $order['TransInfo'],
            'Message' => $order['Message'],
            'Channel' => $order['Channel'],
            'LoanFlag' => $order['LoanFlag'],
            'ProductType' => $order['ProductType'],
            'ProductName' => $order['ProductName'],
            'Remark' => $order['Remark']
        );*/
        //按民生付要求对单信息进行组装
        // $orderInfo = implode('|', $order);
        $orderInfo = '1.0.0|09025201506261037231111111111111|10.01|156|20150626|103723|09025|商户名称|11313|http://tongzhi.com|http://tiaozhuan.com||交易详细内容|hh|0|1|1|豆子|备注';

        var_dump('按民生付要求对单信息进行组装$orderInfo==', $orderInfo, '1.0.0|09025201506261037231111111111111|10.01|156|20150626|103723|09025|商户名称|11313|http://tongzhi.com|http://tiaozhuan.com||交易详细内容|hh|0|1|1|豆子|备注');
        //初始化lajp
        $ret = $this->init();
        //对订单信息加密：
        if (isset($ret)) {
            $orderInfo = $this->SignAndEncryptMessage($orderInfo);
            // 注：商户上送支付订单密文时，需在密文后面拼入”|1.0.0”字符串。
            $orderInfo .= '|1.0.0';

        } else {
            echo "初始化失败";
        }

        echo('加密信息$orderInfo=='.'<hr />'. $orderInfo);
        exit;
        //将订单信息post给民生
        $html_text = $this->chinaPaySubmit($orderInfo, 'post', '确定');
        echo $html_text;
    }


    // 初始化
    public function init()
    {
        require_once("php_java.php");//引用LAJP提供的PHP脚本
        try {
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
            return $ret;
        } catch (Exception $e) {
            echo "Err:{$e}<br>";
        }

    }


    // 加密方法
    public function SignAndEncryptMessage($orderInfo)
    {
        require_once("php_java.php");
        //对订单信息按照民生付要求加密
        $base64Plain = base64_encode($orderInfo);
        try {
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
            return $ret;
        } catch (Exception $e) {
            echo "Err:{$e}<br>";
        }
    }


    //将订单消息以post方式传递给民生付网关
    public function chinaPaySubmit($orderInfo, $method, $button_name)
    {
        // $url = self::$payUrl;
        $url = '';
//        $orderInfo = urlencode($orderInfo);
        $sHtml = "<form id='chinasubmit' name='chinasubmit' action='" . $url . "' method='" . $method . "'>";
        $sHtml .= "<input type='hidden' name = 'orderinfo' value='" . $orderInfo . "'/>";
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit'  value='" . $button_name . "' style='display:none;'></form>";
        $sHtml = $sHtml . "<script>document.forms['chinasubmit'].submit();</script>";
        return $sHtml;
    }


}

?>
