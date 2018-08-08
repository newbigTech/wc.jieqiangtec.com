<?php

var_dump(111);
//$orderInfo = $this->SignAndEncryptMessage($orderInfo);
$obj_order = new Order();
$base64Encode = 'MIIFOAYKKoEcz1UGAQQCA6CCBSgwggUkAgECMYGdMIGaAgECgBTg/WiNKypMkiyD0rrJodvcaHzH/TANBgkqgRzPVQGCLQMFAARwWPxPa51JzwFx6upzNYNsAakByl1XGKwYAElAp31vSnnSkzp+uHfVcF982x1Z6XXEGWQGCRfyOv/CywU/0I7EwoVad1ncYK0J66mSeePIZPJskoBvWLmYOZ+oEtlYhnEthu9cz9s12roMXU+w/Acn7DCCBH0GCiqBHM9VBgEEAgEwGwYHKoEcz1UBaAQQk0cmR6d/fW8H216+GG1v/oCCBFDBKzCkfaQfXuQeUU3VN4+qTrkDIBFypoYRi5HmrMH8xC/ZQ9gse4/gGwHOtchUBrI4CNUQrL3FfjAcalx+/D0bsGUdC9Vmi8EMZkLOAQPsZXrlo/WEMCcPrB1hyyfg+Bg55JGYv8JyTVB71R+H9oCv7Y/d3eOiwlhwFXTa4Iiv/amidBPGWe1QE+donpsrsaCunO+u4PeiHJcrYwoVuWVXRVM8jajtlBHxZMmtv2i26TII2Cf8OPZI23c0x06+7aLkYPWVK6GQmL57on0F+sZ+PEk9bu+dP8ZSXc6hdeXE+Ap7jPsk22TCM3JUqVAkOmk57CakxnHxFB63kO+05nLnmcHeQwPdHX98lwJ0RW0/4ErVJtsWW6fJefp63rJMrbFsTXcchaUwHvaYlEpRy9tMQI3PwAhXdKbkgyQdH22RQOKljmAti4SGFYFplkzTR2opvsGRYg/0mWXb6ZXjQdCMEgZrU0WLOtpAAMsGsmSB9RuroI7NJ75fL4gwnBHZhoBxYSjB8wkVKUa+neWaXJoXquThINNyxFzxyjkho+8FKTZsZO/I1lrjRknwhx5lBOy6HUz+2VgtwGX1MaZ3tFTcqUqN9RRe38P8aLfK4+84hlS0g4t3LbbMtC99J4bl7VOLfWvjlqV+XM8ULh2AUYJp+NZHneMgaNeBlC46yoTXDSVEAj9wGsxkJ61gSoU/BNt2DMucXO1jRrQGPRR+vLMS9yWXnKWOULf103Vsy50NbNkkEmOqoEwsUBVE1qh5O0nOWbf0/tXl21jKdVnAo75jv2Xy8P7qCnRv/ImZVFzo8Rpx6ojaEvba/F+1QdvIY4CSMBwHOHtAplkag5KPwFqvPTMY+EpRz1UuyoIrefVM4erF165A2Xly9OJ4x2VIjJGWnBJGal5RHpmBb3mDTuR4sw3LGTEFvpk825ISLjB3DkeSZKhjkzdEMjVtF3Azp0yDtcisODr2sp23/z0O7842HVe6AbQllws+5MybZBkxbmGXkspjBNiy4/l0CH/Z8a1kQmVhAeca6x+b7jXd7XbkUtArXBABNftK/aobgaMBguHMIFJlEvJD579g570BqBphAdGZhD+huxQ+CFjyKVf5vWpSHeEbizMhtAzK1keN3DfwQeV0ink/qk2xa2IaeRtZgIKItz3E7P8vbBTkoCtYbOqP8WbX/g6VLYYOkcjWOxWyhltZxp7seGFbR60sw3L6INFwim74Nd0lImbEVws08PTtFncUOJEyuW+ScZarbqCs1k8XOOg8ffro+wWoiFS1nueP+uKOkdH3owDH/ataFr9iADO127fvkdv6LBK/7KdoPyznCK1ZuFgW0X3AEYi4Tpgea7XBlKA8RCrVqXNC05IZ7IpdCaUkeJ1Lfyn7IPfDKBIiIFrYQvGhsU2RaoEfPrk8S1ntYTsqdYi4SJg9ImiKOncU3nHpB079IJg7sXCVeAJ6vWMSQO+XxxdpPm0=|1.0.0';


$base64Encode = 'MIIESAYKKoEcz1UGAQQCA6CCBDgwggQ0AgECMYGdMIGaAgECgBTWsfSf9q0WjX496qMJifSHCuotgzANBgkqgRzPVQGCLQMFAARw6uiNp4UcTc9IwdJqXCioBKVceaat7vdt/Iy0QAeduOxcn7ffu0NJPcw0mBUrXkE6vE2zGl00oCq0szflcB6dDjCJBBgHoGGmLxby+pT3ZeetsHzeXnTmcD3KrOsB6mwOHrL6uQwGf9hzPkGnnGEzfzCCA40GCiqBHM9VBgEEAgEwGwYHKoEcz1UBaAQQcjijBgdKp/cBLQWdXr8ZnYCCA2C11r+GeXx779AfA2CeducLVH6LnfkAmOWISeicyRjG6VZmhdBue/m7YDvMon4ZEVT1D+Z/xv4zv7AxpZd7bnwO4OjzmUYQ2DoxcJ3ZZW1mcXotUvad8mRrK40NLq7RdY6+niEJLn68eVny68nHTwCGSsXrwdlfkQ352wW1s1KiEdIxGMKXPsSPtqCKuwAazsM1LPvyColf6sARHkEszK7N0fPwH2QMd/LQtmWLonmcnq5ZAvM/BDBwrlq9nSg8BmB46m/ZOlhRO49df64KSHmVT9H0TP7AxFTZi5+4Lg3EBkBE09XH16U9/igvN+VVYcAUKnVW4ayxqX7uDRpzmyKn8hjgh2OR2S9qzLPv2yGZ3rsC34F+fCoGtWThslNZnlIi25mq6E/wZltxLxZVyvxnszwXdILfHdMIp+lXQY77CsSLruSn9jPNwXFJEt+VCsSRqGt70jJSFjPos6UX+4KCQhPDItaXxXXDnDV7Imi/b6XMTO4k8yy/4v4DOAGJFxev8lVQ3QU465KWN7K6L+2EstaHk1Ha2HNws5y8sfGxrk8gmsGeKsMA71qcYKmBNcOPbdFiZgLN/Ft3U9pqS3umkFXmwow399ef2Lq0IYXyWDoywxbRHvUaWk/Og2axCp3DY10R9yrNUbmtSPpKVNEZQtJ8+BEoW03sAEsrk0SoTEw91D5GbfTm9XAaYU0uWFoQQwqYTpa4tUmzjS7owu2ssRa2dvfdtroGksYmJMc+4l7HKnvCnZCSMI73XUsjHMW4fVzGUACvzlEmNU9nmEWPUXvy261SFET0ZplPEZBW0hGSe3n7k/fR/IfiKGMnayUoYrCTj45ma1V1L7N3eG17uyinKhVTS1bEyX7JKBoW1axgAPJ1ePRbui4//QAeBXmsALjl4sxrSeQd7IHUubOZtpWRuiPH6WesOe7fMv8ltxoSxm2hxhINzXymr4PBOhBG7i82G0dbi6+RDEYZxE7V5BHs5SwgaRs+FU69zRmEGa3G4xt2n4Z9+nu+Fu9Qu7weNSIvZA0JcBYH3gztNv0U6sKgjMaJlVn6+EUPz9MDBP6OJ8IONZjwt0Zdsnzb3RDL9ZEqB1rb25TFrF4pvEPK1d+KwB/SuvDn3jiT5qhggKuzt8wOPyEDzjMQB/mOtAs=';

$base64Encode = base64_encode($base64Encode);
$res = $obj_order->ChinaPayReceive($base64Encode);

/*结果信息按照顺序，并且每项以“|”分隔：
订单号|行内外标示|商户代码|交易金额|交易日期|交易时间|订单状态|商户预留信息|备注
返回格式示例：*/
//$res = '010010000001|0|01001|111.23|20021010|121212|0|这是一个支付|备注';

$res_arr = explode('|', $res);
var_dump('$res_arr==', $res_arr);
exit;

// 民生支付
class Order
{
    // 组装数据
    function ChinaPayReceive($base64Encode)
    {
        //初始化lajp
        $ret = $this->init();
        //对返回信息解密：
        if (isset($ret)) {
            $res = $this->verify($base64Encode);
        } else {
            echo "初始化失败";
        }
        // $res = '010010000001|0|01001|111.23|20021010|121212|0|这是一个支付|备注';
        var_dump('解密信息$res==', $res);
        return $res;

        exit;
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


    // 解密方法
    public function verify($base64Encode)
    {
        require_once("php_java.php");
        try {
            /**
             * 解密和验证签名
             *
             * @param base64EnvelopeMessage
             *            BASE64字符串密文消息
             * @return 执行结果（BASE64字符串）
             *
             */
            $ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::DecryptAndVerifyMessage", $base64Encode);
            return "{$ret}<br>";
        } catch (Exception $e) {
            echo "Err:{$e}<br>";
        }
    }
}

?>
