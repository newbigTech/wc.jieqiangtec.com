<?php
/**
 * 商品相关接口
 */
define('IN_API', true);
//error_reporting(E_ALL & ~E_WARNING & ~Notice);

$key = 'abcd1234';
// ip限制
//var_dump($_SERVER);exit;
if(!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', ''))){
    echo_json('501', '非法访问_IP错误');
}

// 验签 md5(md5('密钥')+时间戳前六位)
/*if(!$_GPC['sign'] || !$_GPC['time'] || count($_GPC['time'])<10 ){
    echo_json('501', '非法访问_时间错误');
}else{

    if(!($_GPC['sign'] !== md5(md5($key) . substr(0, 6)))){
        echo_json('501', '非法访问_签名错误');
    }
}*/

if(!in_array($a, array('index', 'category', 'detail', 'category', 'orders', 'cancel', 'express', 'confirm', 'buy'))){
    echo_json('501', '非法访问_未授权');
}

$good = new  Goods();
if($a == 'index'){
    // 商品列表接口
    $good->main();
}elseif($a == 'category'){
    // 商品分类接口
    $good->category();
}elseif($a == 'detail'){
    // 商品详情接口
    $good->detail();
}elseif($a == 'orders'){
    // 商品订单接口
    $good->orders();
}elseif($a == 'buy'){
    // 商品下单接口
    $good->buy();
}elseif($a == 'cancel'){
    // 商品取消下单接口
    $good->cancel();
}elseif($a == 'express'){
    // 商品查看物流接口
    $good->express();
}elseif($a == 'confirm'){
    // 收货确认通知接口
    $good->confirm();
}

class Goods
{
    // 是否调试模式 本地开启调试模式
    public $g_debug   = 1;
    public $g_uniacid = 10;

    // 商品列表接口
    public function main()
    {
        global $_GPC;
        $sqlcondition = $groupcondition = '';
        // $condition    = ' WHERE g.`uniacid` = 10';
        $condition = ' WHERE 1 ';

        $sql           = 'SELECT g.id FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition;
        $total_all     = pdo_fetchall($sql);
        $data['total'] = $total = count($total_all);
        unset($total_all);

        if(!empty($total)){
            $pindex = max(1, intval($_GPC['page']));
            $psize  = $_GPC['page'] ?: 20;

            $sql          = 'SELECT g.* FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition . " ORDER BY g.`status` DESC, g.`displayorder` DESC,\r\n                g.`id` DESC LIMIT " . (($pindex - 1) * $psize) . ',' . $psize;
            $data['list'] = pdo_fetchall($sql);
            //        var_dump($sql, $params,$data);exit;
            echo_json('200', 'success', $data);
        }else{
            echo_json('201', 'no data list', $data);
        }

    }

    // 商品分类接口
    public function category()
    {
        //        $data['list'] = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_category') . ' WHERE uniacid = 10 ORDER BY parentid ASC, displayorder DESC');
        $data['list'] = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_category') . ' WHERE 1 ORDER BY parentid ASC, displayorder DESC');
        echo_json('200', 'success', $data);
    }

    // 商品详情接口
    public function detail()
    {
        global $_GPC;
        $id = intval($_GPC['id']);
        if(!$id){
            echo_json('501', '参数错误', $data);
        }
        $data['detail'] = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_goods') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => 10));
        echo_json('200', 'success', $data);
    }

    // 商品订单列表接口
    public function orders()
    {
        global $_GPC;
        if($this->g_debug){
            $openid = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }else{
            $openid = $_GPC['uid'];
        }

        $condition = " WHERE 1  and openid= '$openid' ";
        // select count(*) from `ims_ewei_shop_order` where 1  and openid='ooyv91cPbLRIz1qaX7Fim_cRfjZk' and ismr=0 and deleted=0 and uniacid='6' and istrade=0  and merchshow=0  and userdeleted=0  ;

        $sql           = 'SELECT id FROM ' . tablename('ewei_shop_order') . $condition;
        $total_all     = pdo_fetchall($sql);
        $data['total'] = $total = count($total_all);
        unset($total_all);

        if(!empty($total)){
            // 页码 每页数
            $pindex = max(1, intval($_GPC['page']));
            $psize  = $_GPC['psize'] ?: 20;

            $data['list'] = pdo_fetchall('select id,addressid,ordersn,price,dispatchprice,status,iscomment,isverify,' . "\n" . 'verified,verifycode,verifytype,iscomment,refundid,expresscom,express,expresssn,finishtime,`virtual`,' . "\n" . 'paytype,expresssn,refundstate,dispatchtype,verifyinfo,merchid,isparent,userdeleted' . "\n" . ' from ' . tablename('ewei_shop_order') . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize);

            //        var_dump($sql, $params,$data);exit;
            echo_json('200', 'success', $data);
        }else{
            echo_json('201', 'no data list', $data);
        }
    }

    // 商品查看物流接口
    // status 状态 -1取消状态（交易关闭），0普通状态（没付款: 待付款 ; 付了款: 待发货），1 买家已付款（待发货），2 卖家已发货（待收货），3 成功（可评价: 等待评价 ; 不可评价 : 交易完成）4 退款申请
    // paytype	支付类型 1为余额支付 2在线支付 3 货到付款 11 后台付款 21 微信支付 22 支付宝支付 23 银联支付
    // isverify	核销
    // sendtype	v2 发货类型 0 按订单发货 1< 分包发货 （多个快递单号）
    // sendtime	发送时间
    public function express()
    {
        global $_GPC;
        $id = intval($_GPC['id']);
        if(!$id){
            echo_json('501', '参数错误_没有订单号id', $data);
        }
        $data['detail'] = pdo_fetch('SELECT id,status,paytype,isverify,sendtype,sendtime,expresscom,expresssn,express,printstate FROM ' . tablename('ewei_shop_order') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => 10));
        echo_json('200', 'success', $data);
    }

    // 商品取消下单接口
    public function cancel()
    {
        global $_GPC;
        $orderid = intval($_GPC['id']);
        if(!$orderid){
            echo_json('501', '参数错误_没有订单号', $data);
        }

        if($this->g_debug){
            $openid = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }else{
            $openid = $_GPC['uid'];
        }

        $order = pdo_fetch('select id,ordersn,openid,status,deductcredit,deductcredit2,deductprice,couponid,isparent,price,dispatchtype,addressid,carrier,paytype,isnewstore,storeid,istrade,createtime from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $this->g_uniacid, ':openid' => $openid));

        // var_dump('$order==',$order);exit;

        if(empty($order)){
            echo_json('201', '订单未找到', $data);
        }

        // 当日可取消
        /*if(time() > ($order['createtime'] + 86400)){
            echo_json('202', '订单已过一天，不能取消!', $data);
        }*/

        // var_dump('$order==',$order);exit;

        /*if(0 < $order['status']){
            echo_json('202', '订单已支付，不能取消!', $data);
        }*/

        if($order['status'] < 0){
            echo_json('203', '订单已经取消', $data);
        }

        //        var_dump('$order==',$order);exit;
        // 修改库存
        // 自提门店ID
        // 处理订单库存及用户积分情况(赠送积分)
        $this->setStocksAndCredits($orderid, 2, $order);


        // 修改订单状态
        pdo_update('ewei_shop_order', array('status' => -1, 'canceltime' => time(), 'closereason' => trim($_GPC['remark'])), array('id' => $order['id'], 'uniacid' => $this->g_uniacid));

        if(!empty($order['isparent'])){
            pdo_update('ewei_shop_order', array('status' => -1, 'canceltime' => time(), 'closereason' => trim($_GPC['remark'])), array('parentid' => $order['id'], 'uniacid' => $this->g_uniacid));
        }


        echo_json('200', 'success', $data);
    }

    // 收货确认通知接口 传递订单号id和用户uid
    public function confirm()
    {
        global $_GPC;
        $orderid = intval($_GPC['id']);
        if(!$orderid){
            echo_json('501', '参数错误_没有订单号', $data);
        }

        if($this->g_debug){
            $openid = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }else{
            $openid = $_GPC['uid'];
        }


        $order = pdo_fetch('select id,status,openid,couponid,refundstate,refundid,ordersn,price from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $this->g_uniacid, ':openid' => $openid));

        if(empty($order)){
            echo_json('501', '订单未找到', $data);
        }

        /*if ($order['status'] != 2) {
            echo_json('501', '订单不能确认收货', $data);
        }*/
        //        var_dump($order);exit;

        // refundstate	退款状态 0 没有退款 1 退款 2 换货
        if((0 < $order['refundstate']) && !empty($order['refundid'])){
            $change_refund               = array();
            $change_refund['status']     = -2;
            $change_refund['refundtime'] = time();
            pdo_update('ewei_shop_order_refund', $change_refund, array('id' => $order['refundid'], 'uniacid' => $this->g_uniacid));
        }

        pdo_update('ewei_shop_order', array('status' => 3, 'finishtime' => time(), 'refundstate' => 0), array('id' => $order['id'], 'uniacid' => $this->g_uniacid));

        echo_json('200', 'success', $data);
    }

    // 商品下单接口
    public function buy()
    {
        global $_GPC;
        if($this->g_debug){
            $openid = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }else{
            $openid = $_GPC['uid'];
        }

        $uniacid = $this->g_uniacid;

        // 获取用户信息
        $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $this->g_uniacid, ':openid' => $openid));

        //        var_dump('$member==',$member,$_W['shopset']['wap']['open']);exit;

        if(!empty($_W['shopset']['wap']['open']) && !empty($_W['shopset']['wap']['mustbind']) && empty($member['mobileverify'])){
            show_json(0, array('message' => '请先绑定手机', 'url' => mobileUrl('member/bind', NULL, true)));
        }

        // 绑定用户信息
        $member_param = array();
        $this->bindMember($member_param);

//        var_dump('$member22==', $member, $_W['shopset']['wap']['open']);
//        exit;

        $allow_sale   = true;
        $package      = array();
        $packageprice = 0;

        $merch_array            = array();
        $ismerch                = 0;
        $discountprice_array    = array();
        $dispatchid             = intval($_GPC['dispatchid']);
        $dispatchtype           = intval($_GPC['dispatchtype']);
        $carrierid              = intval($_GPC['carrierid']);
        $goods                  = $_GPC['goods'];

        $allow_sale = false;

        if(empty($goods) || !is_array($goods)){
            show_json(0, '未找到任何商品');
        }

        $liveid = intval($_GPC['liveid']);

        $allgoods              = array();
        $tgoods                = array();
        $totalprice            = 0;
        $goodsprice            = 0;
        $grprice               = 0;
        $weight                = 0;
        $taskdiscountprice     = 0;
        $lotterydiscountprice  = 0;
        $discountprice         = 0;
        $isdiscountprice       = 0;
        $merchisdiscountprice  = 0;
        $cash                  = 1;
        $deductprice           = 0;
        $deductprice2          = 0;
        $virtualsales          = 0;
        $dispatch_price        = 0;
        $seckill_price         = 0;
        $seckill_payprice      = 0;
        $seckill_dispatchprice = 0;
        $buyagain_sale         = true;
        $buyagainprice         = 0;
//        $sale_plugin           = com('sale');
        $saleset               = false;

        $isvirtual         = false;
        $isverify          = false;
        $isonlyverifygoods = true;
        $isendtime         = 0;
        $endtime           = 0;
        $verifytype        = 0;
        $isvirtualsend     = false;
        $couponmerchid     = 0;
        $total_array       = array();
        $giftid            = intval($_GPC['giftid']);

        // 传递商品
        foreach($goods as $g){
            if(empty($g)){
                continue;
            }

            $goodsid                        = intval($g['goodsid']);
            $goodstotal                     = intval($g['total']);
            $total_array[$goodsid]['total'] += $goodstotal;
        }



        $goods = $this->wholesaleprice($goods);

        foreach($goods as $g){
            if(empty($g)){
                continue;
            }

            $goodsid    = intval($g['goodsid']);
            $optionid   = intval($g['optionid']);
            $goodstotal = intval($g['total']);

            if($goodstotal < 1){
                $goodstotal = 1;
            }

            if(empty($goodsid)){
                show_json(0, '参数错误');
            }

            $sql_condition = '';

            $threensql = '';

            $sql                 = 'SELECT id as goodsid,' . $sql_condition . 'title,type,intervalfloor,intervalprice, weight,total,issendfree,isnodiscount, thumb,marketprice,liveprice,cash,isverify,verifytype,' . ' goodssn,productsn,sales,istime,timestart,timeend,hasoption,isendtime,usetime,endtime,ispresell,presellprice,preselltimeend,' . ' usermaxbuy,minbuy,maxbuy,unit,buylevels,buygroups,deleted,unite_total,' . ' status,deduct,manydeduct,`virtual`,discounts,deduct2,ednum,edmoney,edareas,edareas_code,diyformtype,diyformid,diymode,' . ' dispatchtype,dispatchid,dispatchprice,merchid,merchsale,cates,' . ' isdiscount,isdiscount_time,isdiscount_discounts, virtualsend,' . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale ,verifygoodslimittype,verifygoodslimitdate  ' . $threensql . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
            $data                = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $goodsid));

            if((0 < $data['ispresell']) && (($data['preselltimeend'] == 0) || (time() < $data['preselltimeend']))){
                $data['marketprice'] = $data['presellprice'];
            }

            if($data['type'] != 5){
                $isonlyverifygoods = false;
            }else{
                if(!empty($data['verifygoodslimittype'])){
                    $verifygoodslimitdate = intval($data['verifygoodslimitdate']);

                    if($verifygoodslimitdate < time()){
                        show_json(0, '商品:"' . $data['title'] . '"的使用时间已失效,无法购买!');
                    }

                    if(($verifygoodslimitdate - 7200) < time()){
                        show_json(0, '商品:"' . $data['title'] . '"的使用时间即将失效,无法购买!');
                    }
                }
            }

            if(!empty($liveid)){
                $isLiveGoods = p('live')->isLiveGoods($data['goodsid'], $liveid);

                if(!empty($isLiveGoods)){
                    $data['marketprice'] = price_format($isLiveGoods['liveprice']);
                }
            }

            if($data['status'] == 2){
                $data['marketprice'] = 0;
            }

            if(!empty($_SESSION['exchange']) && p('exchange')){
                if(empty($data['status']) || !empty($data['deleted'])){
                    show_json(0, $data['title'] . '<br/> 已下架!');
                }
            }

            if(!empty($data['hasoption'])){
                $opdata = m('goods')->getOption($data['goodsid'], $optionid);
                if(empty($opdata) || empty($optionid)){
                    show_json(0, '商品' . $data['title'] . '的规格不存在,请到购物车删除该商品重新选择规格!');
                }
            }

            $rank    = intval($_SESSION[$goodsid . '_rank']);
            $log_id  = intval($_SESSION[$goodsid . '_log_id']);
            $join_id = intval($_SESSION[$goodsid . '_join_id']);


            if($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)){
                $data['is_task_goods'] = 0;
                $tgoods                = false;
            }else{
                $task_goods_data = m('goods')->getTaskGoods($openid, $goodsid, $rank, $log_id, $join_id, $optionid);

                if(empty($task_goods_data['is_task_goods'])){
                    $data['is_task_goods'] = 0;
                }else{
                    $allow_sale                   = false;
                    $tgoods['title']              = $data['title'];
                    $tgoods['openid']             = $openid;
                    $tgoods['goodsid']            = $goodsid;
                    $tgoods['optionid']           = $optionid;
                    $tgoods['total']              = $goodstotal;
                    $data['is_task_goods']        = $task_goods_data['is_task_goods'];
                    $data['is_task_goods_option'] = $task_goods_data['is_task_goods_option'];
                    $data['task_goods']           = $task_goods_data['task_goods'];
                }
            }

            $merchid                          = $data['merchid'];
            $merch_array[$merchid]['goods'][] = $data['goodsid'];

            if(0 < $merchid){
                $ismerch = 1;
            }

            $virtualid     = $data['virtual'];
            $data['stock'] = $data['total'];
            $data['total'] = $goodstotal;

            if($data['cash'] != 2){
                $cash = 0;
            }

            if(!empty($packageid)){
                $cash = $package['cash'];
            }

            $unit = (empty($data['unit']) ? '件' : $data['unit']);
            if($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)){
                $check_buy = plugin_run('seckill::checkBuy', $data['seckillinfo'], $data['title'], $data['unit']);

                if(is_error($check_buy)){
                    show_json(-1, $check_buy['message']);
                }
            }else{
                if($data['type'] != 4){
                    if(0 < $data['minbuy']){
                        if($goodstotal < $data['minbuy']){
                            show_json(0, $data['title'] . '<br/> ' . $data['minbuy'] . $unit . '起售!');
                        }
                    }

                    if(0 < $data['maxbuy']){
                        if($data['maxbuy'] < $goodstotal){
                            show_json(0, $data['title'] . '<br/> 一次限购 ' . $data['maxbuy'] . $unit . '!');
                        }
                    }
                }

                if(0 < $data['usermaxbuy']){
                    $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=0 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $data['goodsid'], ':uniacid' => $uniacid, ':openid' => $openid));

                    if($data['usermaxbuy'] <= $order_goodscount){
                        show_json(0, $data['title'] . '<br/> 最多限购 ' . $data['usermaxbuy'] . $unit . '!');
                    }
                }

                if(!empty($data['is_task_goods'])){
                    if($data['task_goods']['total'] < $goodstotal){
                        show_json(0, $data['title'] . '<br/> 任务活动优惠限购 ' . $data['task_goods']['total'] . $unit . '!');
                    }
                }

                if($data['istime'] == 1){
                    if(time() < $data['timestart']){
                        show_json(0, $data['title'] . '<br/> 限购时间未到!');
                    }

                    if($data['timeend'] < time()){
                        show_json(0, $data['title'] . '<br/> 限购时间已过!');
                    }
                }

                $levelid = intval($member['level']);
                $groupid = intval($member['groupid']);

                if($data['buylevels'] != ''){
                    $buylevels = explode(',', $data['buylevels']);

                    if(!in_array($levelid, $buylevels)){
                        show_json(0, '您的会员等级无法购买<br/>' . $data['title'] . '!');
                    }
                }

                if($data['buygroups'] != ''){
                    $buygroups = explode(',', $data['buygroups']);

                    if(!in_array($groupid, $buygroups)){
                        show_json(0, '您所在会员组无法购买<br/>' . $data['title'] . '!');
                    }
                }
            }

            $sql_condition = '';

            if($data['type'] == 4){
                if(!empty($g['wholesaleprice'])){
                    $data['wholesaleprice'] = intval($g['wholesaleprice']);
                }

                if(!empty($g['goodsalltotal'])){
                    $data['goodsalltotal'] = intval($g['goodsalltotal']);
                }

                $data['marketprice'] == 0;
                $intervalprice = iunserializer($data['intervalprice']);

                foreach($intervalprice as $intervalprice){
                    if($intervalprice['intervalnum'] <= $data['goodsalltotal']){
                        $data['marketprice'] = $intervalprice['intervalprice'];
                    }
                }

                if($data['marketprice'] == 0){
                    show_json(0, $data['title'] . '<br/> ' . $data['minbuy'] . $unit . '起批!');
                }
            }

            if(!empty($optionid)){
                $option = pdo_fetch('select id,title,marketprice,liveprice,presellprice,goodssn,productsn,stock,`virtual`,weight' . $sql_condition . ' from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':goodsid' => $goodsid, ':id' => $optionid));

                if(!empty($option)){
                    if(!empty($_SESSION['exchange']) && p('exchange')){
                        if($option['exchange_stock'] <= 0){
                            show_json(-1, $data['title'] . '<br/>' . $option['title'] . ' 库存不足!');
                        }else{
                            pdo_query('UPDATE ' . tablename('ewei_shop_goods_option') . ' SET exchange_stock = exchange_stock - 1 WHERE id = :id AND uniacid = :uniacid', array(':id' => $optionid, ':uniacid' => $this->g_uniacid));
                        }
                    }else{
                        if($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)){
                        }else{
                            if(empty($data['unite_total'])){
                                $stock_num = $option['stock'];
                            }else{
                                $stock_num = $data['stock'];
                            }

                            if($stock_num != -1){
                                if(empty($stock_num)){
                                    show_json(-1, $data['title'] . '<br/>' . $option['title'] . ' 库存不足!stock=' . $stock_num);
                                }

                                if(!empty($data['unite_total'])){
                                    if(($stock_num - intval($total_array[$goodsid]['total'])) < 0){
                                        show_json(-1, $data['title'] . '<br/>总库存不足!当前总库存为' . $stock_num);
                                    }
                                }
                            }
                        }
                    }

                    $data['optionid']    = $optionid;
                    $data['optiontitle'] = $option['title'];

                    if($data['type'] != 4){
                        $data['marketprice'] = (0 < intval($data['ispresell'])) && ((time() < $data['preselltimeend']) || ($data['preselltimeend'] == 0)) ? $option['presellprice'] : $option['marketprice'];

                        if(!empty($liveid)){
                            $liveOption = p('live')->getLiveOptions($data['goodsid'], $liveid, array($option));
                            if(!empty($liveOption) && !empty($liveOption[0])){
                                $data['marketprice'] = price_format($liveOption[0]['marketprice']);
                            }
                        }

                        $packageoption = array();

                        if($packageid){
                            $packageoption       = pdo_fetch('select packageprice from ' . tablename('ewei_shop_package_goods_option') . "\r\n                                where uniacid = " . $uniacid . ' and goodsid = ' . $goodsid . ' and optionid = ' . $optionid . ' and pid = ' . $packageid . ' ');
                            $data['marketprice'] = $packageoption['packageprice'];
                            $packageprice        += $packageoption['packageprice'];
                        }
                    }

                    $virtualid = $option['virtual'];

                    if(!empty($option['goodssn'])){
                        $data['goodssn'] = $option['goodssn'];
                    }

                    if(!empty($option['productsn'])){
                        $data['productsn'] = $option['productsn'];
                    }

                    if(!empty($option['weight'])){
                        $data['weight'] = $option['weight'];
                    }
                }
            }else{
                if($data['stock'] != -1){
                    if(empty($data['stock'])){
                        show_json(0, $data['title'] . '<br/>库存不足!');
                    }
                }
            }

            $data['diyformdataid'] = 0;
            $data['diyformdata']   = iserializer(array());
            $data['diyformfields'] = iserializer(array());



            if($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)){
                $data['ggprice']              = $gprice = $data['seckillinfo']['price'] * $goodstotal;
                $seckill_payprice             += $gprice;
                $seckill_price                += ($data['marketprice'] * $goodstotal) - $gprice;
                $goodsprice                   += $data['marketprice'] * $goodstotal;
                $data['taskdiscountprice']    = 0;
                $data['lotterydiscountprice'] = 0;
                $data['discountprice']        = 0;
                $data['discountprice']        = 0;
                $data['discounttype']         = 0;
                $data['isdiscountunitprice']  = 0;
                $data['discountunitprice']    = 0;
                $data['price0']               = 0;
                $data['price1']               = 0;
                $data['price2']               = 0;
                $data['buyagainprice']        = 0;
            }else{
                $gprice                       = $data['marketprice'] * $goodstotal;
                $goodsprice                   += $gprice;
                $prices                       = m('order')->getGoodsDiscountPrice($data, $level);
                $data['ggprice']              = $prices['price'];
                $data['taskdiscountprice']    = $prices['taskdiscountprice'];
                $data['lotterydiscountprice'] = $prices['lotterydiscountprice'];
                $data['discountprice']        = $prices['discountprice'];
                $data['discountprice']        = $prices['discountprice'];
                $data['discounttype']         = $prices['discounttype'];
                $data['isdiscountunitprice']  = $prices['isdiscountunitprice'];
                $data['discountunitprice']    = $prices['discountunitprice'];
                $data['price0']               = $prices['price0'];
                $data['price1']               = $prices['price1'];
                $data['price2']               = $prices['price2'];
                $data['buyagainprice']        = $prices['buyagainprice'];
                $buyagainprice                += $prices['buyagainprice'];
                $taskdiscountprice            += $prices['taskdiscountprice'];
                $lotterydiscountprice         += $prices['lotterydiscountprice'];

                if($prices['discounttype'] == 1){
                    $isdiscountprice += $prices['isdiscountprice'];
                    $discountprice   += $prices['discountprice'];

                    if(!empty($data['merchsale'])){
                        $merchisdiscountprice                                  += $prices['isdiscountprice'];
                        $discountprice_array[$merchid]['merchisdiscountprice'] += $prices['isdiscountprice'];
                    }

                    $discountprice_array[$merchid]['isdiscountprice'] += $prices['isdiscountprice'];
                }else{
                    if($prices['discounttype'] == 2){
                        $discountprice                                  += $prices['discountprice'];
                        $discountprice_array[$merchid]['discountprice'] += $prices['discountprice'];
                    }
                }

                $discountprice_array[$merchid]['ggprice'] += $prices['ggprice'];
            }

            $threenprice = json_decode($data['threen'], 1);
            if($threenprice && !empty($threenprice['price'])){
                $data['ggprice'] -= $data['price0'] - $threenprice['price'];
            }else{
                if($threenprice && !empty($threenprice['discount'])){
                    $data['ggprice'] -= ((10 - $threenprice['discount']) / 10) * $data['price0'];
                }
            }

            $merch_array[$merchid]['ggprice'] += $data['ggprice'];
            $totalprice                       += $data['ggprice'];

            // isverify	支持线下核销 Null 0 1 不支持 2 支持
            if($data['isverify'] == 2){
                $isverify   = true;
                $verifytype = $data['verifytype'];
                $isendtime  = $data['isendtime'];

                if($isendtime == 0){
                    if(0 < $data['usetime']){
                        $endtime = time() + (3600 * 24 * intval($data['usetime']));
                    }else{
                        $endtime = 0;
                    }
                }else{
                    $endtime = $data['endtime'];
                }
            }

            if(!empty($data['virtual']) || ($data['type'] == 2) || ($data['type'] == 3) || ($data['type'] == 20)){
                $isvirtual = true;
                if(($data['type'] == 20) && p('ccard')){
                    $ccard = 1;
                }

                if($data['virtualsend']){
                    $isvirtualsend = true;
                }
            }

            if($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)){
            }else{
                /*if((0 < floatval($data['buyagain'])) && empty($data['buyagain_sale'])){
                    if(m('goods')->canBuyAgain($data)){
                        $data['deduct'] = 0;
                        $saleset        = false;
                    }
                }*/


            }

            $virtualsales += $data['sales'];
            $allgoods[]   = $data;
        }

        $grprice = $totalprice;
        if((1 < count($goods)) && !empty($tgoods)){
            show_json(0, '任务活动优惠商品' . $tgoods['title'] . '不能放入购物车下单,请单独购买');
        }

        if(empty($allgoods)){
            show_json(0, '未找到任何商品');
        }

        $couponid = intval($_GPC['couponid']);
        $contype  = intval($_GPC['contype']);
        $wxid     = intval($_GPC['wxid']);
        $wxcardid = $_GPC['wxcardid'];
        $wxcode   = $_GPC['wxcode'];

        if($contype == 1){
            $ref = com_run('wxcard::wxCardGetCodeInfo', $wxcode, $wxcardid);

            if(!is_wxerror($ref)){
                $ref = com_run('wxcard::wxCardConsume', $wxcode, $wxcardid);

                if(is_wxerror($ref)){
                    show_json(0, '您的卡券未到使用日期或已经超出使用次数限制!');
                }
            }else{
                show_json(0, '您的卡券未到使用日期或已经超出使用次数限制!');
            }
        }

        $deductenough = 0;
        $couponprice     = 0;

        // 快递地址
        $addressid = intval($_GPC['addressid']);
        $address   = false;
        if(!empty($addressid) && ($dispatchtype == 0) && !$isonlyverifygoods){
            $address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid   limit 1', array(':uniacid' => $uniacid, ':openid' => $openid, ':id' => $addressid));

            if(empty($address)){
                show_json(0, '未找到地址');
            }else{
                if(empty($address['province']) || empty($address['city'])){
                    show_json(0, '地址请选择省市信息');
                }
            }
        }

        if(!$isvirtual && !$isverify && !$isonlyverifygoods && ($dispatchtype == 0) && !$isonlyverifygoods){
            if(empty($addressid)){
                show_json(0, '请选择地址');
            }

            // 商品价格
            $dispatch_array        = m('order')->getOrderDispatchPrice($allgoods, $member, $address, $saleset, $merch_array, 2);
            $dispatch_price        = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
            $seckill_dispatchprice = $dispatch_array['seckill_dispatch_price'];
            $nodispatch_array      = $dispatch_array['nodispatch_array'];

            if(!empty($nodispatch_array['isnodispatch'])){
                show_json(0, $nodispatch_array['nodispatch']);
            }
        }

        if($isonlyverifygoods){
            $addressid = 0;
        }

        $totalprice -= $deductenough;
        $totalprice += $dispatch_price + $seckill_dispatchprice;
        if($saleset && empty($saleset['dispatchnodeduct'])){
            $deductprice2 += $dispatch_price;
        }

        if(empty($goods[0]['bargain_id'])){
            $deductcredit  = 0;
            $deductmoney   = 0;
            $deductcredit2 = 0;

            if(!empty($saleset['moneydeduct'])){
                if(!empty($_GPC['deduct2'])){
                    $deductcredit2 = $member['credit2'];

                    if(($totalprice - $seckill_payprice) < $deductcredit2){
                        $deductcredit2 = $totalprice - $seckill_payprice;
                    }

                    if($deductprice2 < $deductcredit2){
                        $deductcredit2 = $deductprice2;
                    }
                }

                $totalprice -= $deductcredit2;
            }
        }

        $verifyinfo  = array();
        $verifycode  = '';
        $verifycodes = array();
        if($isverify || $dispatchtype){
            if($isverify){
                if(($verifytype == 0) || ($verifytype == 1)){
                    $verifycode = random(8, true);

                    while(1){
                        $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where verifycode=:verifycode and uniacid=:uniacid limit 1', array(':verifycode' => $verifycode, ':uniacid' => $this->g_uniacid));

                        if($count <= 0){
                            break;
                        }

                        $verifycode = random(8, true);
                    }
                }else{
                    if($verifytype == 2){
                        $totaltimes = intval($allgoods[0]['total']);

                        if($totaltimes <= 0){
                            $totaltimes = 1;
                        }

                        $i = 1;

                        while($i <= $totaltimes){
                            $verifycode = random(8, true);

                            while(1){
                                $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where concat(verifycodes,\'|\' + verifycode +\'|\' ) like :verifycodes and uniacid=:uniacid limit 1', array(':verifycodes' => '%' . $verifycode . '%', ':uniacid' => $this->g_uniacid));

                                if($count <= 0){
                                    break;
                                }

                                $verifycode = random(8, true);
                            }

                            $verifycodes[] = '|' . $verifycode . '|';
                            $verifyinfo[]  = array('verifycode' => $verifycode, 'verifyopenid' => '', 'verifytime' => 0, 'verifystoreid' => 0);
                            ++$i;
                        }
                    }
                }
            }else{
                if($dispatchtype){
                    $verifycode = random(8, true);

                    while(1){
                        $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where verifycode=:verifycode and uniacid=:uniacid limit 1', array(':verifycode' => $verifycode, ':uniacid' => $this->g_uniacid));

                        if($count <= 0){
                            break;
                        }

                        $verifycode = random(8, true);
                    }
                }
            }
        }

        $carrier  = $_GPC['carriers'];
        $carriers = (is_array($carrier) ? iserializer($carrier) : iserializer(array()));

        if($totalprice <= 0){
            $totalprice = 0;
        }

        if(($ismerch == 0) || (($ismerch == 1) && (count($merch_array) == 1))){
            $multiple_order = 0;
        }else{
            $multiple_order = 1;
        }

        if(0 < $ismerch){
            $ordersn = m('common')->createNO('order', 'ordersn', 'ME');
        }else{
            $ordersn = m('common')->createNO('order', 'ordersn', 'SH');
        }

        if(!empty($goods[0]['bargain_id']) && p('bargain')){
            $bargain_act = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_bargain_actor') . ' WHERE id = :id AND openid = :openid ', array(':id' => $goods[0]['bargain_id'], ':openid' => $_W['openid']));

            if(empty($bargain_act)){
                exit('没有这个商品');
            }

            $totalprice = $bargain_act['now_price'] + $dispatch_price;
            $goodsprice = $bargain_act['now_price'];

            if(!pdo_update('ewei_shop_bargain_actor', array('status' => 1), array('id' => $goods[0]['bargain_id'], 'openid' => $_W['openid']))){
                exit('下单失败');
            }

            $ordersn = substr_replace($ordersn, 'KJ', 0, 2);
        }

        $is_package = 0;

        if(!empty($packageid)){
            $goodsprice     = $packageprice;
            $dispatch_price = $package['freight'];
            $totalprice     = $packageprice + $package['freight'];
            $is_package     = 1;
        }


        $order                         = array();
        $order['ismerch']              = $ismerch;
        $order['parentid']             = 0;
        $order['uniacid']              = $uniacid;
        $order['openid']               = $openid;
        $order['ordersn']              = $ordersn;
        $order['price']                = $totalprice;
        $order['oldprice']             = $totalprice;
        $order['grprice']              = $grprice;
        $order['taskdiscountprice']    = $taskdiscountprice;
        $order['lotterydiscountprice'] = $lotterydiscountprice;
        $order['discountprice']        = $discountprice;
        if(!empty($goods[0]['bargain_id']) && p('bargain')){
            $order['discountprice'] = 0;
        }

        $order['isdiscountprice']      = $isdiscountprice;
        $order['merchisdiscountprice'] = $merchisdiscountprice;
        $order['cash']                 = $cash;
        $order['status']               = 0;
        $order['remark']               = trim($_GPC['remark']);
        $order['addressid']            = empty($dispatchtype) ? $addressid : 0;
        $order['goodsprice']           = $goodsprice;
        $order['dispatchprice']        = $dispatch_price + $seckill_dispatchprice;
        if(!is_null($_SESSION['exchangeprice']) && !empty($_SESSION['exchange']) && p('exchange')){
            $order['price']         = $_SESSION['exchangeprice'] + $_SESSION['exchangepostage'];
            $order['ordersn']       = m('common')->createNO('order', 'ordersn', 'DH');
            $order['goodsprice']    = $_SESSION['exchangeprice'];
            $order['dispatchprice'] = $_SESSION['exchangepostage'];
        }

        $order['dispatchtype']         = $dispatchtype;
        $order['dispatchid']           = $dispatchid;
        $order['storeid']              = $carrierid;
        $order['carrier']              = $carriers;
        $order['createtime']           = time();
        $order['olddispatchprice']     = $dispatch_price + $seckill_dispatchprice;
        $order['contype']              = $contype;
        $order['couponid']             = $couponid;
        $order['wxid']                 = $wxid;
        $order['wxcardid']             = $wxcardid;
        $order['wxcode']               = $wxcode;
        $order['couponmerchid']        = $couponmerchid;
        $order['paytype']              = 0;
        $order['deductprice']          = $deductmoney;
        $order['deductcredit']         = $deductcredit;
        $order['deductcredit2']        = $deductcredit2;
        $order['deductenough']         = $deductenough;
        $order['merchdeductenough']    = $merch_enough_total;
        $order['couponprice']          = $couponprice;
        $order['merchshow']            = 0;
        $order['buyagainprice']        = $buyagainprice;
        $order['ispackage']            = $is_package;
        $order['packageid']            = $packageid;
        $order['seckilldiscountprice'] = $seckill_price;
        $order['quickid']              = intval($_GPC['fromquick']);
        $order['liveid']               = $liveid;

        if(!empty($ccard)){
            $order['ccard'] = 1;
        }

        if($multiple_order == 0){
            $order_merchid          = current(array_keys($merch_array));
            $order['merchid']       = intval($order_merchid);
            $order['isparent']      = 0;
            $order['transid']       = '';
            $order['isverify']      = $isverify ? 1 : 0;
            $order['verifytype']    = $verifytype;
            $order['verifyendtime'] = $endtime;
            $order['verifycode']    = $verifycode;
            $order['verifycodes']   = implode('', $verifycodes);
            $order['verifyinfo']    = iserializer($verifyinfo);
            $order['virtual']       = $virtualid;
            $order['isvirtual']     = $isvirtual ? 1 : 0;
            $order['isvirtualsend'] = $isvirtualsend ? 1 : 0;
            $order['invoicename']   = trim($_GPC['invoicename']);
        }else{
            $order['isparent'] = 1;
            $order['merchid']  = 0;
        }

        if(!empty($address)){
            $order['address'] = iserializer($address);
        }

        pdo_insert('ewei_shop_order', $order);
        $orderid = pdo_insertid();

        if(!empty($goods[0]['bargain_id']) && p('bargain')){
            pdo_update('ewei_shop_bargain_actor', array('order' => $orderid), array('id' => $goods[0]['bargain_id'], 'openid' => $_W['openid']));
        }

        if($multiple_order == 0){
            $exchangepriceset = $_SESSION['exchangepriceset'];
            $exchangetitle    = '';

            foreach($allgoods as $goods){
                $order_goods = array();
                if(!empty($bargain_act) && p('bargain')){
                    $goods['total']   = 1;
                    $goods['ggprice'] = $bargain_act['now_price'];
                    pdo_query('UPDATE ' . tablename('ewei_shop_goods') . ' SET sales = sales + 1 WHERE id = :id AND uniacid = :uniacid', array(':id' => $goods['goodsid'], ':uniacid' => $uniacid));
                }

                $order_goods['merchid']    = $goods['merchid'];
                $order_goods['merchsale']  = $goods['merchsale'];
                $order_goods['uniacid']    = $uniacid;
                $order_goods['orderid']    = $orderid;
                $order_goods['goodsid']    = $goods['goodsid'];
                $order_goods['price']      = $goods['marketprice'] * $goods['total'];
                $order_goods['total']      = $goods['total'];
                $order_goods['optionid']   = $goods['optionid'];
                $order_goods['createtime'] = time();
                $order_goods['optionname'] = $goods['optiontitle'];
                $order_goods['goodssn']    = $goods['goodssn'];
                $order_goods['productsn']  = $goods['productsn'];
                $order_goods['realprice']  = $goods['ggprice'];
                $exchangetitle             .= $goods['title'];
                if(p('exchange') && is_array($exchangepriceset)){
                    $order_goods['realprice'] = 0;

                    foreach($exchangepriceset as $ke => $va){
                        if(empty($goods['optionid']) && is_array($va) && ($goods['goodsid'] == $va[0]) && ($va[1] == 0)){
                            $order_goods['realprice'] = $va[2];
                            break;
                        }

                        if(!empty($goods['optionid']) && is_array($va) && ($goods['optionid'] == $va[0]) && ($va[1] == 1)){
                            $order_goods['realprice'] = $va[2];
                            break;
                        }
                    }
                }

                $order_goods['oldprice'] = $goods['ggprice'];

                if($goods['discounttype'] == 1){
                    $order_goods['isdiscountprice'] = $goods['isdiscountprice'];
                }else{
                    $order_goods['isdiscountprice'] = 0;
                }

                $order_goods['openid'] = $openid;

                if($diyform_plugin){
                    if($goods['diyformtype'] == 2){
                        $order_goods['diyformid'] = 0;
                    }else{
                        $order_goods['diyformid'] = $goods['diyformid'];
                    }

                    $order_goods['diyformdata']   = $goods['diyformdata'];
                    $order_goods['diyformfields'] = $goods['diyformfields'];
                }

                if(0 < floatval($goods['buyagain'])){
                    if(!m('goods')->canBuyAgain($goods)){
                        $order_goods['canbuyagain'] = 1;
                    }
                }

                if($goods['seckillinfo'] && ($goods['seckillinfo']['status'] == 0)){
                    $order_goods['seckill']        = 1;
                    $order_goods['seckill_taskid'] = $goods['seckillinfo']['taskid'];
                    $order_goods['seckill_roomid'] = $goods['seckillinfo']['roomid'];
                    $order_goods['seckill_timeid'] = $goods['seckillinfo']['timeid'];
                }

                pdo_insert('ewei_shop_order_goods', $order_goods);
                if($goods['seckillinfo'] && ($goods['seckillinfo']['status'] == 0)){
                    plugin_run('seckill::setSeckill', $goods['seckillinfo'], $goods, $_W['openid'], $orderid, 0, $order['createtime']);
                }
            }
        }else{
            $og_array      = array();
            $ch_order_data = m('order')->getChildOrderPrice($order, $allgoods, $dispatch_array, $merch_array, $sale_plugin, $discountprice_array);

            foreach($merch_array as $key => $value){
                $merchid = $key;

                if(!empty($merchid)){
                    $order_head = 'ME';
                }else{
                    $order_head = 'SH';
                }

                $order['ordersn']              = m('common')->createNO('order', 'ordersn', $order_head);
                $order['merchid']              = $merchid;
                $order['parentid']             = $orderid;
                $order['isparent']             = 0;
                $order['merchshow']            = 1;
                $order['dispatchprice']        = $dispatch_array['dispatch_merch'][$merchid];
                $order['olddispatchprice']     = $dispatch_array['dispatch_merch'][$merchid];
                $order['merchisdiscountprice'] = $discountprice_array[$merchid]['merchisdiscountprice'];
                $order['isdiscountprice']      = $discountprice_array[$merchid]['isdiscountprice'];
                $order['discountprice']        = $discountprice_array[$merchid]['discountprice'];
                $order['price']                = $ch_order_data[$merchid]['price'];
                $order['grprice']              = $ch_order_data[$merchid]['grprice'];
                $order['goodsprice']           = $ch_order_data[$merchid]['goodsprice'];
                $order['deductprice']          = $ch_order_data[$merchid]['deductprice'];
                $order['deductcredit']         = $ch_order_data[$merchid]['deductcredit'];
                $order['deductcredit2']        = $ch_order_data[$merchid]['deductcredit2'];
                $order['merchdeductenough']    = $ch_order_data[$merchid]['merchdeductenough'];
                $order['deductenough']         = $ch_order_data[$merchid]['deductenough'];
                $order['coupongoodprice']      = $discountprice_array[$merchid]['coupongoodprice'];
                $order['couponprice']          = $discountprice_array[$merchid]['deduct'];

                if(empty($order['couponprice'])){
                    $order['couponid']      = 0;
                    $order['couponmerchid'] = 0;
                }else{
                    if(0 < $couponmerchid){
                        if($merchid == $couponmerchid){
                            $order['couponid']      = $couponid;
                            $order['couponmerchid'] = $couponmerchid;
                        }else{
                            $order['couponid']      = 0;
                            $order['couponmerchid'] = 0;
                        }
                    }
                }

                pdo_insert('ewei_shop_order', $order);
                $ch_orderid                       = pdo_insertid();
                $merch_array[$merchid]['orderid'] = $ch_orderid;

                if(0 < $couponmerchid){
                    if($merchid == $couponmerchid){
                        $couponorderid = $ch_orderid;
                    }
                }

                foreach($value['goods'] as $k => $v){
                    $og_array[$v] = $ch_orderid;
                }
            }

            foreach($allgoods as $goods){
                $goodsid                        = $goods['goodsid'];
                $order_goods                    = array();
                $order_goods['parentorderid']   = $orderid;
                $order_goods['merchid']         = $goods['merchid'];
                $order_goods['merchsale']       = $goods['merchsale'];
                $order_goods['orderid']         = $og_array[$goodsid];
                $order_goods['uniacid']         = $uniacid;
                $order_goods['goodsid']         = $goodsid;
                $order_goods['price']           = $goods['marketprice'] * $goods['total'];
                $order_goods['total']           = $goods['total'];
                $order_goods['optionid']        = $goods['optionid'];
                $order_goods['createtime']      = time();
                $order_goods['optionname']      = $goods['optiontitle'];
                $order_goods['goodssn']         = $goods['goodssn'];
                $order_goods['productsn']       = $goods['productsn'];
                $order_goods['realprice']       = $goods['ggprice'];
                $order_goods['oldprice']        = $goods['ggprice'];
                $order_goods['isdiscountprice'] = $goods['isdiscountprice'];
                $order_goods['openid']          = $openid;

                if(0 < floatval($goods['buyagain'])){
                    if(!m('goods')->canBuyAgain($goods)){
                        $order_goods['canbuyagain'] = 1;
                    }
                }

                pdo_insert('ewei_shop_order_goods', $order_goods);
            }
        }


        if(com('coupon') && !empty($orderid)){
            com('coupon')->addtaskdata($orderid);
        }

        if(is_array($carrier)){
            $up = array('realname' => $carrier['carrier_realname'], 'carrier_mobile' => $carrier['carrier_mobile']);
            pdo_update('ewei_shop_member', $up, array('id' => $member['id'], 'uniacid' => $this->g_uniacid));

            if(!empty($member['uid'])){
                load()->model('mc');
                mc_update($member['uid'], $up);
            }
        }

        if($_GPC['fromcart'] == 1){
            pdo_query('update ' . tablename('ewei_shop_member_cart') . ' set deleted=1 where  openid=:openid and uniacid=:uniacid and selected=1 ', array(':uniacid' => $uniacid, ':openid' => $openid));
        }

        if(p('quick') && !empty($_GPC['fromquick'])){
            pdo_update('ewei_shop_quick_cart', array('deleted' => 1), array('quickid' => intval($_GPC['fromquick']), 'uniacid' => $this->g_uniacid, 'openid' => $_W['openid']));
        }

        if(0 < $deductcredit){
            m('member')->setCredit($openid, 'credit1', 0 - $deductcredit, array('0', $_W['shopset']['shop']['name'] . '购物积分抵扣 消费积分: ' . $deductcredit . ' 抵扣金额: ' . $deductmoney . ' 订单号: ' . $ordersn));
        }

        if(0 < $buyagainprice){
            m('goods')->useBuyAgain($orderid);
        }

        if(0 < $deductcredit2){
            m('member')->setCredit($openid, 'credit2', 0 - $deductcredit2, array('0', $_W['shopset']['shop']['name'] . '购物余额抵扣: ' . $deductcredit2 . ' 订单号: ' . $ordersn));
        }

        if(empty($virtualid)){
            m('order')->setStocksAndCredits($orderid, 0);
        }else{
            if(isset($allgoods[0])){
                $vgoods = $allgoods[0];
                pdo_update('ewei_shop_goods', array('sales' => $vgoods['sales'] + $vgoods['total']), array('id' => $vgoods['goodsid']));
            }
        }

        $plugincoupon = com('coupon');

        if($plugincoupon){
            if((0 < $couponmerchid) && ($multiple_order == 1)){
                $oid = $couponorderid;
            }else{
                $oid = $orderid;
            }

            $plugincoupon->useConsumeCoupon($oid);
        }

        if(!empty($tgoods)){
            $rank    = intval($_SESSION[$tgoods['goodsid'] . '_rank']);
            $log_id  = intval($_SESSION[$tgoods['goodsid'] . '_log_id']);
            $join_id = intval($_SESSION[$tgoods['goodsid'] . '_join_id']);
            m('goods')->getTaskGoods($tgoods['openid'], $tgoods['goodsid'], $rank, $log_id, $join_id, $tgoods['optionid'], $tgoods['total']);
            $_SESSION[$tgoods['goodsid'] . '_rank']    = 0;
            $_SESSION[$tgoods['goodsid'] . '_log_id']  = 0;
            $_SESSION[$tgoods['goodsid'] . '_join_id'] = 0;
        }

        m('notice')->sendOrderMessage($orderid);
        com_run('printer::sendOrderMessage', $orderid);
        $pluginc = p('commission');

        if($pluginc){
            if($multiple_order == 0){
                $pluginc->checkOrderConfirm($orderid);
            }else{
                if(!empty($merch_array)){
                    foreach($merch_array as $key => $value){
                        $pluginc->checkOrderConfirm($value['orderid']);
                    }
                }
            }
        }

        unset($_SESSION[$openid . '_order_create']);


        show_json(1, array('orderid' => $orderid));
    }


    /**
     * //处理订单库存及用户积分情况(赠送积分)
     * @param type $orderid
     * @param type $type 0 下单 1 支付 2 取消
     * @param $order 2 取消
     */
    private function setStocksAndCredits($orderid = '', $type = 0, $order)
    {
        // v2 0 普通 1 门店预约订单
        if(!empty($order['istrade'])){
            return NULL;
        }

        if(empty($order['isnewstore'])){
            $newstoreid = 0;
        }else{
            $newstoreid = intval($order['storeid']);
        }
        //        var_dump('$newstoreid==',$newstoreid);exit;

        $param = array();

        if($order['isparent'] == 1){
            $condition               = ' og.parentorderid=:parentorderid';
            $param[':parentorderid'] = $orderid;
        }else{
            $condition         = ' og.orderid=:orderid';
            $param[':orderid'] = $orderid;
        }


        $goods   = pdo_fetchall('select og.goodsid,og.total,g.totalcnf,og.realprice,g.credit,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where ' . $condition, $param);
        $credits = 0;
        //        var_dump('$goods==',$goods,$order);exit;
        foreach($goods as $g){
            $goods_item      = pdo_fetch('select total as goodstotal from' . tablename('ewei_shop_goods') . ' where id=:id  limit 1', array(':id' => $g['goodsid']));
            $g['goodstotal'] = $goods_item['goodstotal'];

            //            var_dump('$goods_item==',$goods_item,$type,$order);exit;


            // totalcnf	减库存方式 0 拍下减库存 1 付款减库存 2 永不减库存
            $stocktype = 0;

            // $type 0 下单 1 支付 2 取消
            if($type == 0){
                if($g['totalcnf'] == 0){
                    $stocktype = -1;
                }
            }else if($type == 1){
                if($g['totalcnf'] == 1){
                    $stocktype = -1;
                }
            }else{
                if($type == 2){
                    if(1 <= $order['status']){
                        if($g['totalcnf'] == 1){
                            $stocktype = 1;
                        }
                    }else{
                        if($g['totalcnf'] == 0){
                            $stocktype = 1;
                        }
                    }
                }
            }

            //            var_dump('totalcnf==',$g['totalcnf'],$stocktype,$order,!empty($stocktype));exit;

            if(!empty($stocktype)){
                // 获取配置 ewei_shop_sysset
                // $data = m('common')->getSysset('trade');
                $set    = pdo_fetch('select * from ' . tablename('ewei_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $this->g_uniacid));
                $allset = iunserializer($set['sets']);
                $data   = $allset[trade];
                //                var_dump('$data==',$data,$stocktype,$order,!empty($stocktype));exit;

                if(!empty($data['stockwarn'])){
                    $stockwarn = intval($data['stockwarn']);
                }else{
                    $stockwarn = 5;
                }

                //                var_dump('$data==',$data,$g['goodstotal'],$order);exit;
                if(!empty($g['optionid'])){
                    $option = m('goods')->getOption($g['goodsid'], $g['optionid']);

                    if(0 < $newstoreid){
                        $store_goods_option = $this->getOneStoreGoodsOption($g['optionid'], $g['goodsid'], $newstoreid);
                        if(empty($store_goods_option)){
                            return NULL;
                        }

                        $option['stock'] = $store_goods_option['stock'];
                    }

                    if(!empty($option) && ($option['stock'] != -1)){
                        $stock = -1;

                        if($stocktype == 1){
                            $stock = $option['stock'] + $g['total'];
                        }else{
                            if($stocktype == -1){
                                $stock = $option['stock'] - $g['total'];
                                ($stock <= 0) && ($stock = 0);
                                if(($stock <= $stockwarn) && ($newstoreid == 0)){
                                    // 库存不足提醒 库存不足5件
                                    // m('notice')->sendStockWarnMessage($g['goodsid'], $g['optionid']);
                                }
                            }
                        }

                        if($stock != -1){
                            if(0 < $newstoreid){
                                pdo_update('ewei_shop_newstore_goods_option', array('stock' => $stock), array('uniacid' => $this->g_uniacid, 'goodsid' => $g['goodsid'], 'id' => $store_goods_option['id']));
                            }else{
                                pdo_update('ewei_shop_goods_option', array('stock' => $stock), array('uniacid' => $this->g_uniacid, 'goodsid' => $g['goodsid'], 'id' => $g['optionid']));
                            }
                        }
                    }
                }


                if(!empty($g['goodstotal']) && ($g['goodstotal'] != -1)){
                    $totalstock = -1;

                    //                    var_dump('$stocktype==',$stocktype);exit;
                    if($stocktype == 1){
                        $totalstock = $g['goodstotal'] + $g['total'];
                    }else{
                        if($stocktype == -1){
                            $totalstock = $g['goodstotal'] - $g['total'];
                            ($totalstock <= 0) && ($totalstock = 0);
                            if(($totalstock <= $stockwarn) && ($newstoreid == 0)){
                                // 库存不足提醒 库存不足5件
                                // m('notice')->sendStockWarnMessage($g['goodsid'], 0);
                            }
                        }
                    }

                    //                    var_dump('$totalstock==',$totalstock);exit;
                    if($totalstock != -1){
                        // 修改库存
                        pdo_update('ewei_shop_goods', array('total' => $totalstock), array('uniacid' => $this->g_uniacid, 'id' => $g['goodsid']));
                    }

                    //                    var_dump('$totalstock==',$totalstock,$g['goodstotal'] , $g['total']);exit;
                }
            }

            $gcredit = trim($g['credit']);

            //            var_dump('$gcredit==',$gcredit,$stocktype,$order,!empty($gcredit));exit;

            if(!empty($gcredit)){
                if(strexists($gcredit, '%')){
                    $credits += intval((floatval(str_replace('%', '', $gcredit)) / 100) * $g['realprice']);
                }else{
                    $credits += intval($g['credit']) * $g['total'];
                }
            }

            if($type == 0){
                if($g['totalcnf'] != 1){
                    pdo_update('ewei_shop_goods', array('sales' => $g['sales'] + $g['total']), array('uniacid' => $this->g_uniacid, 'id' => $g['goodsid']));
                }
            }else{
                if($type == 1){
                    if(1 <= $order['status']){
                        if($g['totalcnf'] != 1){
                            pdo_update('ewei_shop_goods', array('sales' => $g['sales'] - $g['total']), array('uniacid' => $this->g_uniacid, 'id' => $g['goodsid']));
                        }

                        $salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':goodsid' => $g['goodsid'], ':uniacid' => $this->g_uniacid));
                        pdo_update('ewei_shop_goods', array('salesreal' => $salesreal), array('id' => $g['goodsid']));
                    }
                }
            }
        }

        // 用户通知
        /*if(0 < $credits){
            $shopset = m('common')->getSysset('shop');

            if($type == 1){
                m('member')->setCredit($order['openid'], 'credit1', $credits, array(0, $shopset['name'] . '购物积分 订单号: ' . $order['ordersn']));
                m('notice')->sendMemberPointChange($order['openid'], $credits, 0);
            }else{
                if($type == 2){
                    if(1 <= $order['status']){
                        m('member')->setCredit($order['openid'], 'credit1', 0 - $credits, array(0, $shopset['name'] . '购物取消订单扣除积分 订单号: ' . $order['ordersn']));
                        m('notice')->sendMemberPointChange($order['openid'], $credits, 1);
                    }
                }
            }
        }else if($type == 1){
            $money = com_run('sale::getCredit1', $order['openid'], (double)$order['price'], $order['paytype'], 1);

            if(0 < $money){
                m('notice')->sendMemberPointChange($order['openid'], $money, 0);
            }
        }else{
            if($type == 2){
                if(1 <= $order['status']){
                    $money = com_run('sale::getCredit1', $order['openid'], (double)$order['price'], $order['paytype'], 1, 1);

                    if(0 < $money){
                        m('notice')->sendMemberPointChange($order['openid'], $money, 1);
                    }
                }
            }
        }*/
    }


    private function getOneStoreGoodsOption($optionid, $goodsid, $storeid)
    {
        $sql = 'select * from ' . tablename('ewei_shop_newstore_goods_option') . ' where goodsid=:goodsid and storeid=:storeid and optionid=:optionid and uniacid=:uniacid Limit 1';
        return pdo_fetch($sql, array(':goodsid' => $goodsid, ':storeid' => $storeid, ':optionid' => $optionid, ':uniacid' => $this->g_uniacid));
    }

    // 绑定用户信息
    private function bindMember($member_param)
    {

    }


    // 获取商品总价
    private function wholesaleprice($goods)
    {
        $goods2 = array();

        foreach ($goods as $good) {
            if ($good['type'] == 4) {
                if (empty($goods2[$good['goodsid']])) {
                    $intervalprices = array();

                    if (0 < $good['intervalfloor']) {
                        $intervalprices[] = array('intervalnum' => intval($good['intervalnum1']), 'intervalprice' => floatval($good['intervalprice1']));
                    }

                    if (1 < $good['intervalfloor']) {
                        $intervalprices[] = array('intervalnum' => intval($good['intervalnum2']), 'intervalprice' => floatval($good['intervalprice2']));
                    }

                    if (2 < $good['intervalfloor']) {
                        $intervalprices[] = array('intervalnum' => intval($good['intervalnum3']), 'intervalprice' => floatval($good['intervalprice3']));
                    }

                    $goods2[$good['goodsid']] = array('goodsid' => $good['goodsid'], 'total' => $good['total'], 'intervalfloor' => $good['intervalfloor'], 'intervalprice' => $intervalprices);
                }
                else {
                    $goods2[$good['goodsid']]['total'] += $good['total'];
                }
            }
        }

        foreach ($goods2 as $good2) {
            $intervalprices2 = iunserializer($good2['intervalprice']);
            $price = 0;

            foreach ($intervalprices2 as $intervalprice) {
                if ($intervalprice['intervalnum'] <= $good2['total']) {
                    $price = $intervalprice['intervalprice'];
                }
            }

            foreach ($goods as &$good) {
                if ($good['goodsid'] == $good2['goodsid']) {
                    $good['wholesaleprice'] = $price;
                    $good['goodsalltotal'] = $good2['total'];
                }
            }

            unset($good);
        }

        return $goods;
    }


}


