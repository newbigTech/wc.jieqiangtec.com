<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
define('IN_MOBILE', true);
require '../../framework/bootstrap.inc.php';
require '../../app/common/bootstrap.app.inc.php';
load()->app('common');
load()->app('template');

$sl = $_GPC['ps'];
$params = @json_decode(base64_decode($sl), true);

$setting = uni_setting($_W['uniacid'], array('payment'));
if (!is_array($setting['payment'])) {
    exit('没有设定支付参数.');
}
$payment = $setting['payment']['unionpay'];
require '__init.php';

if (!empty($_POST) && verify($_POST) && $_POST['respMsg'] == 'success') {
    $sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniontid`=:uniontid';
    $params = array();
    $params[':uniontid'] = $_POST['orderId'];
    $log = pdo_fetch($sql, $params);
    if (!empty($log) && $log['status'] == '0') {
        $log['tag'] = iunserializer($log['tag']);
        $log['tag']['queryId'] = $_POST['queryId'];

        $record = array();
        $record['status'] = 1;
        $record['tag'] = iserializer($log['tag']);
        pdo_update('core_paylog', $record, array('plid' => $log['plid']));
        if ($log['is_usecard'] == 1 && $log['card_type'] == 1 && !empty($log['encrypt_code']) && $log['acid']) {
            load()->classs('coupon');
            $acc = new coupon($log['acid']);
            $codearr['encrypt_code'] = $log['encrypt_code'];
            $codearr['module'] = $log['module'];
            $codearr['card_id'] = $log['card_id'];
            $acc->PayConsumeCode($codearr);
        }
        if ($log['is_usecard'] == 1 && $log['card_type'] == 2) {
            $now = time();
            $log['card_id'] = intval($log['card_id']);
            pdo_query('UPDATE ' . tablename('activity_coupon_record') . " SET status = 2, usetime = {$now}, usemodule = '{$log['module']}' WHERE uniacid = :aid AND couponid = :cid AND uid = :uid AND status = 1 LIMIT 1", array(':aid' => $_W['uniacid'], ':uid' => $log['openid'], ':cid' => $log['card_id']));
        }
    }
    $site = WeUtility::createModuleSite($log['module']);
    if (!is_error($site)) {
        $method = 'payResult';
        if (method_exists($site, $method)) {
            $ret = array();
            $ret['weid'] = $log['uniacid'];
            $ret['uniacid'] = $log['uniacid'];
            $ret['result'] = 'success';
            $ret['type'] = $log['type'];
            $ret['from'] = 'return';
            $ret['tid'] = $log['tid'];
            $ret['uniontid'] = $log['uniontid'];
            $ret['user'] = $log['openid'];
            $ret['fee'] = $log['fee'];
            $ret['tag'] = $log['tag'];
            $ret['is_usecard'] = $log['is_usecard'];
            $ret['card_fee'] = $log['card_fee'];
            $ret['card_id'] = $log['card_id'];
            $site->$method($ret);
            exit('success');
        }
    }
}
$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `plid`=:plid';
$paylog = pdo_fetch($sql, array(':plid' => $params['tid']));
if (!empty($paylog) && $paylog['status'] != '0') {
    exit('这个订单已经支付成功, 不需要重复支付.');
}
$auth = sha1($sl . $paylog['uniacid'] . $_W['config']['setting']['authkey']);
if ($auth != $_GPC['auth']) {
    exit('参数传输错误.');
}
$_W['openid'] = intval($paylog['openid']);

/*$params = array(
    'version' => '5.0.0',
    'encoding' => 'utf-8',
    'certId' => getSignCertId(),
    'txnType' => '01',
    'txnSubType' => '01',
    'bizType' => '000201',
    'frontUrl' => SDK_FRONT_NOTIFY_URL . '?i=' . $_W['uniacid'],
    'backUrl' => SDK_BACK_NOTIFY_URL . '?i=' . $_W['uniacid'],
    'signMethod' => '01',
    'channelType' => '08',
    'accessType' => '0',
    'merId' => SDK_MERID,
    'orderId' => $paylog['uniontid'],
    'txnTime' => date('YmdHis'),
    'txnAmt' => $paylog['fee'] * 100,
    'currencyCode' => '156',
    'defaultPayType' => '0001',
    'reqReserved' => $_W['uniacid'],
);*/

$params = array(
    //版本
    'version' => '1.0.0',
//订单号
//    'billNo' => $paylog['uniontid'],
//订单金额
    'txAmt' => $paylog['fee'],
//货币代码
    'PayerCurr' => 156,
//交易日期（年月日）
    'txDate' => date("Ymd"),
//交易时间(时分秒)
    'txTime' => date("His"),
//商户代码
    'corpID' => SDK_MERID,
    // 'billNo' => 'corpID . 'txDate . 'txTime . substr('billNo, 8, 8),
    'billNo' => SDK_MERID . $paylog['uniontid'],
//商户名称
    'corpName' => '融惠联',
//二级商户号
    'subCorpID' => '',
//后台异步通知地址
    'NotifyUrl' => SDK_BACK_NOTIFY_URL . '?i=' . $_W['uniacid'],
//前台跳转地址
    'JumpUrl' => SDK_FRONT_NOTIFY_URL . '?i=' . $_W['uniacid'],
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

// 加密
//sign($params);

try {
    // 初始化
    // $ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::Initialize", "config/demo.properties_2018");
    /*$ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::Initialize", IA_ROOT."/payment/unionpay/ms_lajp/config/demo.properties");
    var_dump(IA_ROOT."/payment/unionpay/ms_lajp/config/demo.properties",IA_ROOT,$_W['siteroot'],$ret);exit;*/
    /**
     * @param base64PlainMessage
     * BASE64编码的待签名加密的原文数据
     * @return base64EnvelopeMessage BASE64编码的签名加密结果
     *
     */
    // var_dump($params);exit;
    //按民生付要求对单信息进行组装
    $params = implode('|', $params);

    $ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::SignAndEncryptMessage", base64_encode($params));
    // echo "{$ret}<br>";
} catch (Exception $e) {
    // echo "Err:{$e}<br>";
    WeUtility::logging('TODO 民生支付日志', array('error' => $e));
}

//$html_form = create_html($params, SDK_FRONT_TRANS_URL);

$html_form = <<<EOT
                <html>
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf8" />
                <title>支付</title>
                <script type="text/javascript" src="../../payment/unionpay/ms_lajp/js/cmbcForClient.js"></script>
                </head>
                <body>
                <script >
                // loginForComm("http://197.3.176.26:8000/ecshopMerchantTest/index.jsp", "{$url}");
                submitOrderForCash({$ret})
                </script>
                </body>
                </html>
EOT;

echo $html_form;