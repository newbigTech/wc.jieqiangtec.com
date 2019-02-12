<?php
/**
 * 商品相关接口
 */
define('IN_API', true);
//error_reporting(E_ALL & ~E_WARNING & ~Notice);

$key = 'abcd1234';
// ip限制
//var_dump($_SERVER);exit;
/*if(!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', ''))){
    echo_json('501', '非法访问_IP错误');
}*/

/*$_GPC['time'] = time();
$sign = md5(md5($key) . substr($_GPC['time'],0, 6));*/
//var_dump('$sign==',$sign,$_GPC['time'],!$_GPC['sign'],!$_GPC['time'],strlen($_GPC['time']),strlen($_GPC['time'])<10);exit;
// 验签 md5(md5('密钥')+时间戳前六位)
/*if( (!$_GPC['sign']) || (!$_GPC['time']) || (strlen($_GPC['time'])<10) ){
    echo_json('501', '非法访问_时间错误');
}else{
    if(($_GPC['sign'] !== md5(md5($key) . substr($_GPC['time'],0, 6)))){
        echo_json('501', '非法访问_签名错误');
    }
}*/

/*if(!in_array($a, array('index', 'category', 'detail', 'category', 'orders', 'cancel', 'express', 'confirm', 'buy', 'pay'))){
    echo_json('501', '非法访问_未授权');
}*/

$good = new  Goods();
// 商品列表接口
$good->$a();

/*if($a == 'index'){
    // 商品列表接口
    $good->index();
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
}elseif($a == 'pay'){
    // 支付订单接口
    $good->pay(349, $memeber);
}elseif($a == 'bindMember'){
    // 支付订单接口
    $good->bindMember($memeber);
}*/

class Goods
{
    // 是否调试模式 本地开启调试模式
    public $g_debug   = 1;
    public $g_uniacid = 6; // 6:adjyc 10:融惠联

    // 商品列表接口
    public function index()
    {
        global $_GPC;
        // $condition    = ' WHERE g.`uniacid` = 10';
        $condition = " WHERE 1 AND g.`uniacid` = {$this->g_uniacid} AND `status` > 0 and `checked`=0 and `total`>0 and `deleted`=0 AND goodsid IS NULL  ";
        $join = ' LEFT JOIN  '.tablename('ewei_shop_goods_spec').' s  ON g.id =s.goodsid ';

        // keyword 搜索id 标题 商品编号goodssn keywords	v2 关键词  productsn	商品条码
        if(!empty($_GPC['keyword'])){
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition       .= ' AND (g.`id` = :id or g.`title` LIKE :keyword or g.`keywords` LIKE :keyword or g.`goodssn` LIKE :keyword or g.`productsn` LIKE :keyword ';

            $condition          .= ' )';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
            $params[':id']      = $_GPC['keyword'];
        }

        // 商品分类
        if(!empty($_GPC['cate'])){
            $_GPC['cate'] = intval($_GPC['cate']);
            $condition    .= ' AND FIND_IN_SET(' . $_GPC['cate'] . ',cates)<>0 ';
        }

        $sql           = 'SELECT g.id FROM ' . tablename('ewei_shop_goods') . 'g' . $join.$condition ;
        $total_all     = pdo_fetchall($sql, $params);
        $data['total'] = $total = count($total_all);
        unset($total_all);

        if(!empty($total)){
            $pindex = max(1, intval($_GPC['page']));
            $psize  = $_GPC['page'] ?: 20;

            //            $sql          = 'SELECT g.* FROM ' . tablename('ewei_shop_goods') . 'g' . $condition .  " ORDER BY g.`status` DESC, g.`displayorder` DESC,\r\n                g.`id` DESC LIMIT " . (($pindex - 1) * $psize) . ',' . $psize;
//            $fields       = 'id,pcate,ccate,tcate,type,status,displayorder,title,shorttitle,thumb,unit,description,goodssn,productsn,productprice,marketprice,costprice,total,totalcnf,sales,salesreal,spec,createtime,weight,maxbuy,usermaxbuy,hasoption,dispatch,thumb_url,isnew,ishot,isdiscount,isrecommand,issendfree,istime,timestart,timeend,deleted,updatetime,virtual,ccates,pcates,pcates,ednum,edmoney,edareas,dispatchtype,dispatchid,dispatchprice,cates,minbuy,invoice,repair,seven,minprice,maxprice,province,virtualsend,virtualsendcontent,verifytype,subtitle,checked,minpriceupdated,catesinit3,showtotaladd,thumb_first,keywords,catch_id,catch_url,catch_source,labelname,autoreceive,cannotrefund,presellsendtype';
            $fields       = 'g.id';
            $sql          = 'SELECT ' . $fields . ' FROM ' . tablename('ewei_shop_goods') . 'g' .  $join.$condition .  " ORDER BY g.`status` DESC, g.`displayorder` DESC,\r\n                g.`id` DESC LIMIT " . (($pindex - 1) * $psize) . ',' . $psize;
            $data['list'] = pdo_fetchall($sql, $params);

            foreach($data['list'] as $k => $v){
                $v['thumb'] && $data['list'][$k]['thumb'] = tomedia($v['thumb']);
                // $v['advimg'] && $data['list'][$k]['advimg'] = tomedia($v['advimg']);
            }

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
        $data['list'] = pdo_fetchall('SELECT id,name,thumb,parentid,description,displayorder,enabled,level FROM ' . tablename('ewei_shop_category') . ' WHERE 1 AND uniacid=' . $this->g_uniacid . ' ORDER BY parentid ASC, displayorder DESC ');
        foreach($data['list'] as $k => $v){
            $v['thumb'] && $data['list'][$k]['thumb'] = tomedia($v['thumb']);
            // $v['advimg'] && $data['list'][$k]['advimg'] = tomedia($v['advimg']);
        }

        $data['total'] = count($data['list']);
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
        $data['detail'] = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_goods') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $this->g_uniacid));
        echo_json('200', 'success', $data);
    }

    // 商品订单列表接口
    public function orders()
    {
        global $_GPC;
        /*if($this->g_debug){
            $openid = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }else{
            $openid = $_GPC['uid'];
        }*/


        // 判断手机号
        if(empty($_GPC['mobile'])){
            echo_json('202', 'no mobile', $data);
        }

        // 通过用户手机号查找openid
        $member = $this->get_member_info($_GPC['mobile']);
        if($member){
            $openid = $member['openid'];
        }else{
            echo_json('203', 'no member', $data);
        }
        $condition = " WHERE 1  and openid= '$openid' ";

        // status	状态 -1取消状态（交易关闭），0普通状态（没付款: 待付款 ; 付了款: 待发货），1 买家已付款（待发货），2 卖家已发货（待收货），3 成功（可评价: 等待评价 ; 不可评价 : 交易完成）4 退款申请
        if(isset($_GPC['status'])){
            $condition .= " and status= '{$_GPC['status']}' ";
        }

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
            foreach($data['list'] as $k => $v){
                $data['list'][$k]['goodsid'] = pdo_fetchcolumn("SELECT goodsid FROM ".tablename('ewei_shop_order_goods')." WHERE `orderid` = '{$v['id']}' ORDER BY `id` DESC LIMIT 1");
            }

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
        $data['detail'] = pdo_fetch('SELECT id,status,paytype,isverify,sendtype,sendtime,expresscom,expresssn,express,printstate FROM ' . tablename('ewei_shop_order') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $this->g_uniacid));
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

        /*if($this->g_debug){
            $openid = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }else{
            $openid = $_GPC['uid'];
        }*/

        // 判断手机号
        if(empty($_GPC['mobile'])){
            echo_json('202', 'no mobile', $data);
        }

        // 通过用户手机号查找openid
        $member = $this->get_member_info($_GPC['mobile']);
        if($member){
            $openid = $member['openid'];
        }else{
            echo_json('203', 'no member', $data);
        }

        $order = pdo_fetch('select id,ordersn,openid,status,deductcredit,deductcredit2,deductprice,couponid,isparent,price,dispatchtype,addressid,carrier,paytype,isnewstore,storeid,istrade,createtime from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $this->g_uniacid, ':openid' => $openid));



//        $order = pdo_fetch('select id,ordersn,openid,status,deductcredit,deductcredit2,deductprice,couponid,isparent,price,dispatchtype,addressid,carrier,paytype,isnewstore,storeid,istrade,createtime from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $this->g_uniacid));

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

        /*if($this->g_debug){
            $openid = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }else{
            $openid = $_GPC['uid'];
        }*/

        // 判断手机号
        if(empty($_GPC['mobile'])){
            echo_json('202', 'no mobile', $data);
        }

        // 通过用户手机号查找openid
        $member = $this->get_member_info($_GPC['mobile']);
        if($member){
            $openid = $member['openid'];
        }else{
            echo_json('203', 'no member', $data);
        }

        $order = pdo_fetch('select id,status,openid,couponid,refundstate,refundid,ordersn,price from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $this->g_uniacid, ':openid' => $openid));

//        $order = pdo_fetch('select id,status,openid,couponid,refundstate,refundid,ordersn,price from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $this->g_uniacid));


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


        // 获取用户信息
        //        $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $this->g_uniacid, ':openid' => $openid));

        // 绑定用户信息 mobile
        $member_param = array();
        $res_member   = $this->bindMember($member_param);
        //        var_dump('$res_member==',$res_member);exit;
        $member = $res_member['member'];

        /*if($this->g_debug){
            $openid = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }else{
            $openid = $member['openid'];
        }*/
        $openid = $member['openid'];

        $this->g_uniacid = $this->g_uniacid;


        //        var_dump('$member==',$member,$_W['shopset']['wap']['open']);exit;

        if(!empty($_W['shopset']['wap']['open']) && !empty($_W['shopset']['wap']['mustbind']) && empty($member['mobileverify'])){
            echo_json(0, array('message' => '请先绑定手机', 'url' => mobileUrl('member/bind', NULL, true)));
        }


        //        var_dump('$member22==', $member, $_W['shopset']['wap']['open']);
        //        exit;

        $allow_sale   = true;
        $package      = array();
        $packageprice = 0;

        $merch_array         = array();
        $ismerch             = 0;
        $discountprice_array = array();
        $level               = $this->getLevel($openid);
        $dispatchid          = intval($_GPC['dispatchid']);
        $dispatchtype        = intval($_GPC['dispatchtype']);
        $carrierid           = intval($_GPC['carrierid']);
        $goods               = $_GPC['goods'];

        $allow_sale = false;

        if(empty($goods) || !is_array($goods)){
            // echo_json(0, '未找到任何商品');
            echo_json('201', '未找到任何商品', $data);
        }

//                var_dump('$goods==',$goods);exit;

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
        $saleset = false;

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
        //        var_dump('$goods==',$goods,$_REQUEST);exit;

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
                echo_json(0, '参数错误');
            }

            $sql_condition = '';

            $threensql = '';

            $sql  = 'SELECT id as goodsid,' . $sql_condition . 'title,type,intervalfloor,intervalprice, weight,total,issendfree,isnodiscount, thumb,marketprice,liveprice,cash,isverify,verifytype,' . ' goodssn,productsn,sales,istime,timestart,timeend,hasoption,isendtime,usetime,endtime,ispresell,presellprice,preselltimeend,' . ' usermaxbuy,minbuy,maxbuy,unit,buylevels,buygroups,deleted,unite_total,' . ' status,deduct,manydeduct,`virtual`,discounts,deduct2,ednum,edmoney,edareas,edareas_code,diyformtype,diyformid,diymode,' . ' dispatchtype,dispatchid,dispatchprice,merchid,merchsale,cates,' . ' isdiscount,isdiscount_time,isdiscount_discounts, virtualsend,' . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale ,verifygoodslimittype,verifygoodslimitdate  ' . $threensql . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
            $data = pdo_fetch($sql, array(':uniacid' => $this->g_uniacid, ':id' => $goodsid));

            // var_dump('$data==',$data,$_REQUEST);exit;

            // ispresell	v2 是否预售商品 preselltimeend	v2 预售结束时间
            if((0 < $data['ispresell']) && (($data['preselltimeend'] == 0) || (time() < $data['preselltimeend']))){
                $data['marketprice'] = $data['presellprice'];
            }

            // type	类型 1 实体物品 2 虚拟物品 3 虚拟物品(卡密) 4 批发 10 话费流量充值 20 充值卡
            if($data['type'] != 5){
                $isonlyverifygoods = false;
            }else{
                if(!empty($data['verifygoodslimittype'])){
                    $verifygoodslimitdate = intval($data['verifygoodslimitdate']);

                    if($verifygoodslimitdate < time()){
                        echo_json(0, '商品:"' . $data['title'] . '"的使用时间已失效,无法购买!');
                    }

                    if(($verifygoodslimitdate - 7200) < time()){
                        echo_json(0, '商品:"' . $data['title'] . '"的使用时间即将失效,无法购买!');
                    }
                }
            }


            // status	状态 0 下架 1 上架 2 赠品上架
            if($data['status'] == 2){
                $data['marketprice'] = 0;
            }

            // var_dump('$data==',$data,$_REQUEST);exit;

            if(!empty($data['hasoption'])){
                $opdata = $this->getOption($data['goodsid'], $optionid);
                if(empty($opdata) || empty($optionid)){
                    echo_json(0, '商品' . $data['title'] . '的规格不存在,请到购物车删除该商品重新选择规格!');
                }
            }

            // var_dump('$data==',$data,$_REQUEST);exit;
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
            //            var_dump('$data==',$data,$unit);exit;
            if($data['type'] != 4){
                if(0 < $data['minbuy']){
                    if($goodstotal < $data['minbuy']){
                        echo_json(0, $data['title'] . '_ ' . $data['minbuy'] . $unit . '起售!');
                    }
                }

                if(0 < $data['maxbuy']){
                    if($data['maxbuy'] < $goodstotal){
                        echo_json(0, $data['title'] . '_ 一次限购 ' . $data['maxbuy'] . $unit . '!');
                    }
                }
            }

            if(0 < $data['usermaxbuy']){
                $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=0 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $data['goodsid'], ':uniacid' => $this->g_uniacid, ':openid' => $openid));

                if($data['usermaxbuy'] <= $order_goodscount){
                    echo_json(0, $data['title'] . '_ 最多限购 ' . $data['usermaxbuy'] . $unit . '!');
                }
            }

            if(!empty($data['is_task_goods'])){
                if($data['task_goods']['total'] < $goodstotal){
                    echo_json(0, $data['title'] . '_ 任务活动优惠限购 ' . $data['task_goods']['total'] . $unit . '!');
                }
            }

            if($data['istime'] == 1){
                if(time() < $data['timestart']){
                    echo_json(0, $data['title'] . '_ 限购时间未到!');
                }

                if($data['timeend'] < time()){
                    echo_json(0, $data['title'] . '_ 限购时间已过!');
                }
            }
            $levelid = intval($member['level']);
            $groupid = intval($member['groupid']);
            //            var_dump('$levelid==',$levelid,$levelid,$member);exit;

            if($data['buylevels'] != ''){
                $buylevels = explode(',', $data['buylevels']);

                if(!in_array($levelid, $buylevels)){
                    echo_json(0, '您的会员等级无法购买_' . $data['title'] . '!');
                }
            }

            if($data['buygroups'] != ''){
                $buygroups = explode(',', $data['buygroups']);

                if(!in_array($groupid, $buygroups)){
                    echo_json(0, '您所在会员组无法购买_' . $data['title'] . '!');
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
                    echo_json(0, $data['title'] . '_ ' . $data['minbuy'] . $unit . '起批!');
                }
            }

            //            var_dump('$optionid==',$optionid);exit;

            if(!empty($optionid)){
                $option = pdo_fetch('select id,title,marketprice,liveprice,presellprice,goodssn,productsn,stock,`virtual`,weight' . $sql_condition . ' from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $this->g_uniacid, ':goodsid' => $goodsid, ':id' => $optionid));

                if(!empty($option)){
                    if(!empty($_SESSION['exchange']) && p('exchange')){
                        if($option['exchange_stock'] <= 0){
                            echo_json(-1, $data['title'] . '_' . $option['title'] . ' 库存不足!');
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
                                    echo_json(-1, $data['title'] . '_' . $option['title'] . ' 库存不足!stock=' . $stock_num);
                                }

                                if(!empty($data['unite_total'])){
                                    if(($stock_num - intval($total_array[$goodsid]['total'])) < 0){
                                        echo_json(-1, $data['title'] . '_总库存不足!当前总库存为' . $stock_num);
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
                            $packageoption       = pdo_fetch('select packageprice from ' . tablename('ewei_shop_package_goods_option') . "\r\n                                where uniacid = " . $this->g_uniacid . ' and goodsid = ' . $goodsid . ' and optionid = ' . $optionid . ' and pid = ' . $packageid . ' ');
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
                        echo_json(0, $data['title'] . '_库存不足!');
                    }
                }
            }

            $data['diyformdataid'] = 0;
            $data['diyformdata']   = iserializer(array());
            $data['diyformfields'] = iserializer(array());


            $gprice     = $data['marketprice'] * $goodstotal;
            $goodsprice += $gprice;
            $prices     = $this->getGoodsDiscountPrice($data, $level);
            //            var_dump('$data22==',$data, $level,$prices);exit;

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
            //            var_dump('$data223==',$data, $level,$prices);exit;

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

            $virtualsales += $data['sales'];
            $allgoods[]   = $data;
        }

        $grprice = $totalprice;
        if((1 < count($goods)) && !empty($tgoods)){
            echo_json(0, '任务活动优惠商品' . $tgoods['title'] . '不能放入购物车下单,请单独购买');
        }

        if(empty($allgoods)){
            echo_json(0, '未找到任何商品');
        }

        $couponid = intval($_GPC['couponid']);
        $contype  = intval($_GPC['contype']);
        $wxid     = intval($_GPC['wxid']);
        $wxcardid = $_GPC['wxcardid'];
        $wxcode   = $_GPC['wxcode'];

        $deductenough = 0;
        $couponprice  = 0;


        // 快递地址
        $addressid = intval($res_member['addres_id']);
        $address   = false;

//                var_dump('$data223==',$data, $addressid);exit;

        if(!empty($addressid) && ($dispatchtype == 0) && !$isonlyverifygoods){
            $address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid   limit 1', array(':uniacid' => $this->g_uniacid, ':openid' => $openid, ':id' => $addressid));

            if(empty($address)){
                echo_json(0, '未找到地址');
            }else{
                if(empty($address['province']) || empty($address['city'])){
                    echo_json(0, '地址请选择省市信息');
                }
            }
        }

        //        var_dump('$data2235==',$data, $addressid,!$isvirtual && !$isverify && !$isonlyverifygoods && ($dispatchtype == 0) && !$isonlyverifygoods);exit;


        if(!$isvirtual && !$isverify && !$isonlyverifygoods && ($dispatchtype == 0) && !$isonlyverifygoods){

            // TODO debug 地址没有
            /*if(empty($addressid)){
                echo_json(0, '请选择地址');
            }*/

            //            var_dump('$data2235==',$data, $addressid,!$isvirtual && !$isverify && !$isonlyverifygoods && ($dispatchtype == 0) && !$isonlyverifygoods);exit;

            // TODO debug商品价格
            $dispatch_array        = $this->getOrderDispatchPrice($allgoods, $member, $address, $saleset, $merch_array, 2);

            $dispatch_price        = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
            $seckill_dispatchprice = $dispatch_array['seckill_dispatch_price'];
            $nodispatch_array      = $dispatch_array['nodispatch_array'];

            if(!empty($nodispatch_array['isnodispatch'])){
                echo_json(0, $nodispatch_array['nodispatch']);
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

        //        var_dump('empty($goods[0][\'bargain_id\'])==',empty($goods[0]['bargain_id']));exit;

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

        //        var_dump('$isverify==',$isverify || $dispatchtype);exit;

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

        //        var_dump('$ismerch==',$ismerch);exit;

        if(0 < $ismerch){
            $ordersn = $this->createNO('order', 'ordersn', 'ME');
        }else{
            $ordersn = $this->createNO('order', 'ordersn', 'SH');
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
        $order['uniacid']              = $this->g_uniacid;
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

        //        var_dump('$multiple_order==',$multiple_order);exti;

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
        }

        if(!empty($address)){
            $order['address'] = iserializer($address);
        }

        pdo_insert('ewei_shop_order', $order);
        $orderid = pdo_insertid();

        if(!empty($goods[0]['bargain_id']) && p('bargain')){
            pdo_update('ewei_shop_bargain_actor', array('order' => $orderid), array('id' => $goods[0]['bargain_id'], 'openid' => $_W['openid']));
        }


        //        var_dump('$multiple_order==',$multiple_order);exit;
        if($multiple_order == 0){
            $exchangepriceset = $_SESSION['exchangepriceset'];
            $exchangetitle    = '';

            foreach($allgoods as $goods){
                $order_goods = array();


                $order_goods['merchid']    = $goods['merchid'];
                $order_goods['merchsale']  = $goods['merchsale'];
                $order_goods['uniacid']    = $this->g_uniacid;
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

                $order_goods['oldprice'] = $goods['ggprice'];

                if($goods['discounttype'] == 1){
                    $order_goods['isdiscountprice'] = $goods['isdiscountprice'];
                }else{
                    $order_goods['isdiscountprice'] = 0;
                }

                $order_goods['openid'] = $openid;
                pdo_insert('ewei_shop_order_goods', $order_goods);

            }
        }


        if($_GPC['fromcart'] == 1){
            pdo_query('update ' . tablename('ewei_shop_member_cart') . ' set deleted=1 where  openid=:openid and uniacid=:uniacid and selected=1 ', array(':uniacid' => $this->g_uniacid, ':openid' => $openid));
        }


        if(empty($virtualid)){
            $this->setStocksAndCredits($orderid, 0);
        }else{
            if(isset($allgoods[0])){
                $vgoods = $allgoods[0];
                pdo_update('ewei_shop_goods', array('sales' => $vgoods['sales'] + $vgoods['total']), array('id' => $vgoods['goodsid']));
            }
        }

//        var_dump($orderid, $member);exit;
        $this->pay($orderid, $member);

        echo_json(200, 'buy success', array('orderid' => $orderid));
    }

    // 支付订单
    public function pay1($orderid = '349', $member)
    {
        global $_W;
        global $_GPC;

        //        $order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $this->g_uniacid, ':openid' => $openid));

        $order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $this->g_uniacid));

        // istrade	v2 0 普通 1 门店预约订单
        // status 状态 -1取消状态（交易关闭），0普通状态（没付款: 待付款 ; 付了款: 待发货），1 买家已付款（待发货），2 卖家已发货（待收货），3 成功（可评价: 等待评价 ; 不可评价 : 交易完成）4 退款申请
        $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $this->g_uniacid, ':module' => 'ewei_shopv2', ':tid' => $order['ordersn']));


        $seckill_goods = pdo_fetchall('select goodsid,optionid,seckill from  ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid and seckill=1 ', array(':uniacid' => $this->g_uniacid, ':orderid' => $orderid));

        if(!empty($log) && ($log['status'] == '0')){
            pdo_delete('core_paylog', array('plid' => $log['plid']));
            $log = NULL;
        }

        // 插入支付记录
        if(empty($log)){
            $log = array('uniacid' => $this->g_uniacid, 'openid' => $member['uid'], 'module' => 'ewei_shopv2', 'tid' => $order['ordersn'], 'fee' => $order['price'], 'status' => 0);
            pdo_insert('core_paylog', $log);
            $plid = pdo_insertid();
        }

        $set = $this->getSysset(array('shop', 'pay'), $this->g_uniacid);

        //        var_dump('$set==',$set);exit;

        $set['pay']['weixin']     = !empty($set['pay']['weixin_sub']) ? 1 : $set['pay']['weixin'];
        $set['pay']['weixin_jie'] = !empty($set['pay']['weixin_jie_sub']) ? 1 : $set['pay']['weixin_jie'];
        $param_title              = $set['shop']['name'] . '订单';
        $credit                   = array('success' => false);
        if(isset($set['pay']) && ($set['pay']['credit'] == 1)){
            $credit = array('success' => true, 'current' => $member['credit2']);
        }

        $order['price'] = floatval($order['price']);

        $setting = uni_setting($this->g_uniacid, array('payment'));

        $sec = $this->getSec($this->g_uniacid);
        $sec = iunserializer($sec['sec']);

        $wechat = array('success' => false);
        $jie    = intval($_GPC['jie']);

        $alipay = array('success' => false);

        /*var_dump('$seckill_goods==',empty($seckill_goods) && empty($ispeerpay));exit;*/

        if(empty($seckill_goods) && empty($ispeerpay)){

            var_dump('pay==', isset($set['pay']) && ($set['pay']['alipay'] == 1));
            exit;


            list(, $payment) = m('common')->public_build();

            if($payment['type'] == '4'){
                $params = array('service' => 'pay.alipay.native', 'body' => $param_title, 'out_trade_no' => $log['tid'], 'total_fee' => $order['price']);

                if(!empty($order['ordersn2'])){
                    $params['out_trade_no'] = $log['tid'] . '_B';
                }else{
                    $params['out_trade_no'] = $log['tid'] . '_borrow';
                }

                $AliPay = m('pay')->build($params, $payment, 0);
                if(!empty($AliPay) && !is_error($AliPay)){
                    $alipay['url']     = urlencode($AliPay['code_url']);
                    $alipay['success'] = true;
                }
            }

            if(!empty($order['addressid'])){
                $cash = array('success' => ($order['cash'] == 1) && isset($set['pay']) && ($set['pay']['cash'] == 1) && ($order['isverify'] == 0) && ($order['isvirtual'] == 0));
            }

            $haveverifygood = m('order')->checkhaveverifygoods($orderid);
        }else{
            $cash = array('success' => false);
        }

        $payinfo = array('orderid' => $orderid, 'ordersn' => $log['tid'], 'credit' => $credit, 'alipay' => $alipay, 'wechat' => $wechat, 'cash' => $cash, 'money' => $order['price']);

        if(is_h5app()){
            $payinfo = array('wechat' => !empty($sec['app_wechat']['merchname']) && !empty($set['pay']['app_wechat']) && !empty($sec['app_wechat']['appid']) && !empty($sec['app_wechat']['appsecret']) && !empty($sec['app_wechat']['merchid']) && !empty($sec['app_wechat']['apikey']) && (0 < $order['price']) ? true : false, 'alipay' => !empty($set['pay']['app_alipay']) && !empty($sec['app_alipay']['public_key']) ? true : false, 'mcname' => $sec['app_wechat']['merchname'], 'aliname' => empty($_W['shopset']['shop']['name']) ? $sec['app_wechat']['merchname'] : $_W['shopset']['shop']['name'], 'ordersn' => $log['tid'], 'money' => $order['price'], 'attach' => $this->g_uniacid . ':0', 'type' => 0, 'orderid' => $orderid, 'credit' => $credit, 'cash' => $cash);

            if(!empty($order['ordersn2'])){
                $var                = sprintf('%02d', $order['ordersn2']);
                $payinfo['ordersn'] .= 'GJ' . $var;
            }
        }

        return true;

    }

    // 支付订单
    public function pay($orderid, $member)
    {
        global $_W;
        global $_GPC;

        $order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $this->g_uniacid));

        $_SESSION['peerpay'] = NULL;

        $set                      = $this->getSysset(array('shop', 'pay'));
        $set['pay']['weixin']     = !empty($set['pay']['weixin_sub']) ? 1 : $set['pay']['weixin'];
        $set['pay']['weixin_jie'] = !empty($set['pay']['weixin_jie_sub']) ? 1 : $set['pay']['weixin_jie'];
        // $member = m('member')->getMember($openid, true);

        $go_flag = 0;
        if(empty($order['istrade']) && (1 <= $order['status'])){
            $go_flag = 1;
        }

        if(!empty($order['istrade'])){
            if((1 < $order['status']) || (($order['status'] == 1) && ($order['tradestatus'] == 2))){
                $go_flag = 1;
            }
        }

        //        var_dump('$order==',$order);exit;

        //        $type = $_GPC['type'];
        $type = 'credit';

        if(!in_array($type, array('wechat', 'alipay', 'credit', 'cash'))){
            echo_json(0, '未找到支付方式');
        }


        // 插入支付记录
        if(empty($log)){
            $log = array('uniacid' => $this->g_uniacid, 'openid' => $member['uid'], 'module' => 'ewei_shopv2', 'tid' => $order['ordersn'], 'fee' => $order['price'], 'status' => 0);
            pdo_insert('core_paylog', $log);
            $plid = pdo_insertid();

            $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $this->g_uniacid, ':module' => 'ewei_shopv2', ':tid' => $order['ordersn']));

        }


//                var_dump('$log==',(empty($order['isnewstore']) || empty($order['storeid'])) && empty($order['istrade']),'$order==',$order);exit;


        if((empty($order['isnewstore']) || empty($order['storeid'])) && empty($order['istrade'])){
            $order_goods = pdo_fetchall('select og.id,g.title, og.goodsid,og.optionid,g.total as stock,og.total as buycount,g.status,g.deleted,g.maxbuy,g.usermaxbuy,g.istime,g.timestart,g.timeend,g.buylevels,g.buygroups,g.totalcnf,og.seckill from  ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $this->g_uniacid, ':orderid' => $orderid));

//                        var_dump('$order_goods==',$order_goods);exit;

            foreach($order_goods as $data){
                if(empty($data['status']) || !empty($data['deleted'])){
                    echo_json(0, $data['title'] . '_ 已下架!');
                }


                $unit = (empty($data['unit']) ? '件' : $data['unit']);


                if(0 < $data['minbuy']){
                    echo_json(0, $data['title'] . '_ ' . $data['min'] . $unit . '起售!', mobileUrl('order'));
                }

                if(0 < $data['maxbuy']){
                    echo_json(0, $data['title'] . '_ 一次限购 ' . $data['maxbuy'] . $unit . '!');
                }

//                                var_dump('$order_goods==',$order_goods,$data);exit;

                if(0 < $data['usermaxbuy']){
                    $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=1 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $data['goodsid'], ':uniacid' => $this->g_uniacid, ':openid' => $openid));

                    if($data['usermaxbuy'] <= $order_goodscount){
                        echo_json(0, $data['title'] . '_ 最多限购 ' . $data['usermaxbuy'] . $unit);
                    }
                }

                if($data['istime'] == 1){
                    if(time() < $data['timestart']){
                        echo_json(0, $data['title'] . '_ 限购时间未到!');
                    }

                    if($data['timeend'] < time()){
                        echo_json(0, $data['title'] . '_ 限购时间已过!');
                    }
                }

                if($data['buylevels'] != ''){
                    $buylevels = explode(',', $data['buylevels']);

                    if(!in_array($member['level'], $buylevels)){
                        echo_json(0, '您的会员等级无法购买_' . $data['title'] . '!');
                    }
                }

                if($data['buygroups'] != ''){
                    $buygroups = explode(',', $data['buygroups']);

                    if(!in_array($member['groupid'], $buygroups)){
                        echo_json(0, '您所在会员组无法购买_' . $data['title'] . '!');
                    }
                }


                if($data['totalcnf'] == 1){
                    if(!empty($data['optionid'])){
                        $option = pdo_fetch('select id,title,marketprice,goodssn,productsn,stock,`virtual` from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $this->g_uniacid, ':goodsid' => $data['goodsid'], ':id' => $data['optionid']));

                        if(!empty($option)){
                            if($option['stock'] != -1){
                                if(empty($option['stock'])){
                                    echo_json(0, $data['title'] . '_' . $option['title'] . ' 库存不足!');
                                }
                            }
                        }
                    }else{
                        if($data['stock'] != -1){
                            if(empty($data['stock'])){
                                echo_json(0, $data['title'] . '_库存不足!');
                            }
                        }
                    }
                }
            }
        }

//        var_dump('$order_goods==',$order_goods);exit;

        $ps          = array();
        $ps['tid']   = $log['tid'];
        $ps['user']  = $openid;
        $ps['fee']   = $log['fee'];
        $ps['title'] = $log['title'];
        if($type == 'credit'){
            if(empty($set['pay']['credit']) && (0 < $ps['fee'])){
                echo_json(0, '未开启余额支付!');
            }

            if($ps['fee'] < 0){
                echo_json(0, '金额错误');
            }

            // $credits = m('member')->getCredit($openid, 'credit2');
            // TODO debug 默认一千万
            $credits = '10000000';

            if($credits < $ps['fee']){
                echo_json(0, '余额不足,请充值');
            }

            $fee = floatval($ps['fee']);
            /*$result = m('member')->setCredit($openid, 'credit2', 0 - $fee, array($_W['member']['uid'], $_W['shopset']['shop']['name'] . '消费' . $fee));

            if(is_error($result)){
                echo_json(0, $result['message']);
            }*/

            $record           = array();
            $record['status'] = '1';
            $record['type']   = 'cash';
            pdo_update('core_paylog', $record, array('plid' => $log['plid']));
            //            m('order')->setOrderPayType($order['id'], 1, $gpc_ordersn);
            $ret            = array();
            $ret['result']  = 'success';
            $ret['type']    = $log['type'];
            $ret['from']    = 'return';
            $ret['tid']     = $log['tid'];
            $ret['user']    = $log['openid'];
            $ret['fee']     = $log['fee'];
            $ret['weid']    = $log['weid'];
            $ret['uniacid'] = $log['uniacid'];
            @session_start();
            $_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;

//            var_dump('$ret==',$orderid, $ret);exit;

            $pay_result = $this->payResult($ret);

            return true;

            //            echo_json(200,'ok', array('result' => $pay_result));

        }
    }


    /**
     * //处理订单库存及用户积分情况(赠送积分)
     * @param type $orderid
     * @param type $type 0 下单 1 支付 2 取消
     * @param $order 2 取消
     */
    private function setStocksAndCredits($orderid = '', $type = 0, $order = '')
    {
        if(!$order){
            $order = pdo_fetch('select id,ordersn,price,openid,dispatchtype,addressid,carrier,status,isparent,paytype,isnewstore,storeid,istrade from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));
        }


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

    // 绑定用户信息 手机号 地址信息
    public function bindMember($member_param = array())
    {
        global $_GPC;
        // TODO debug
        if($member_param){
            $_GPC = $member_param;
        }

        //        var_dump('$_GPC==',$_GPC);exit;

        // 创建用户
        $mobile     = trim($_GPC['mobile']);
        $verifycode = trim($_GPC['verifycode']);
        $pwd        = trim($_GPC['pwd']);

        if(empty($mobile)){
            echo_json(0, '请输入正确的手机号');
        }

        /*if (empty($verifycode)) {
            echo_json(0, '请输入验证码');
        }

        if (empty($pwd)) {
            echo_json(0, '请输入密码');
        }*/

        $key = '__ewei_shopv2_member_verifycodesession_' . $this->g_uniacid . '_' . $mobile;

        $member = pdo_fetch('select id,openid,mobile,pwd,salt from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and mobileverify=1 and uniacid=:uniacid limit 1', array(':mobile' => $mobile, ':uniacid' => $this->g_uniacid));

        //        var_dump('$member==',$member);exit;

        if(empty($member)){
            $salt = (empty($member) ? '' : $member['salt']);

            if(empty($salt)){
                $salt = $this->getSalt();
            }

            $openid   = (empty($member) ? '' : $member['openid']);
            $nickname = (empty($member) ? '' : $member['nickname']);

            if(empty($openid)){
                $openid   = 'wap_user_' . $this->g_uniacid . '_' . $mobile;
                $nickname = substr($mobile, 0, 3) . 'xxxx' . substr($mobile, 7, 4);
            }

            $data = array('uniacid' => $this->g_uniacid, 'mobile' => $mobile, 'nickname' => $nickname, 'openid' => $openid, 'pwd' => md5($pwd . $salt), 'salt' => $salt, 'createtime' => time(), 'mobileverify' => 1, 'comefrom' => 'mobile');

            // 真实姓名
            $data['realname'] = $_GPC['realname'] ?: 'un_' . $mobile;

            if(empty($member)){
                pdo_insert('ewei_shop_member', $data);
                $member['id'] = pdo_insertid();
            }else{
                pdo_update('ewei_shop_member', $data, array('id' => $member['id']));
            }

            unset($_SESSION[$key]);
            //        var_dump('$member[\'id\']==',$member['id']);exit;
            //        return $member['id'];
        }


        // 绑定地址
        //        $id = intval($_GPC['id']);

        $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and mobileverify=1 and uniacid=:uniacid limit 1', array(':mobile' => $mobile, ':uniacid' => $this->g_uniacid));

        $data_address           = $_GPC['addressdata'];
        $data_address['mobile'] = trim($mobile);

        $data_address['province']        = $_GPC['province'];
        $data_address['city']            = $_GPC['city'];
        $data_address['area']            = $_GPC['area'];
        $data_address['street']          = $_GPC['street'];
        $data_address['address']         = $_GPC['address'];
        $data_address['datavalue']       = trim($data_address['datavalue']);
        $data_address['streetdatavalue'] = trim($data_address['streetdatavalue']);
        $isdefault                       = intval($data_address['isdefault']);
        unset($data_address['isdefault']);
        unset($data_address['areas']);
        $data_address['openid']  = $member['openid'];
        $data_address['uniacid'] = $this->g_uniacid;

        pdo_insert('ewei_shop_member_address', $data_address);
        $addres_id = pdo_insertid();
        return array('member' => $member, 'addres_id' => $addres_id);

    }


    // 获取商品总价
    private function wholesaleprice($goods)
    {
        $goods2 = array();

        foreach($goods as $good){
            if($good['type'] == 4){
                if(empty($goods2[$good['goodsid']])){
                    $intervalprices = array();

                    if(0 < $good['intervalfloor']){
                        $intervalprices[] = array('intervalnum' => intval($good['intervalnum1']), 'intervalprice' => floatval($good['intervalprice1']));
                    }

                    if(1 < $good['intervalfloor']){
                        $intervalprices[] = array('intervalnum' => intval($good['intervalnum2']), 'intervalprice' => floatval($good['intervalprice2']));
                    }

                    if(2 < $good['intervalfloor']){
                        $intervalprices[] = array('intervalnum' => intval($good['intervalnum3']), 'intervalprice' => floatval($good['intervalprice3']));
                    }

                    $goods2[$good['goodsid']] = array('goodsid' => $good['goodsid'], 'total' => $good['total'], 'intervalfloor' => $good['intervalfloor'], 'intervalprice' => $intervalprices);
                }else{
                    $goods2[$good['goodsid']]['total'] += $good['total'];
                }
            }
        }

        foreach($goods2 as $good2){
            $intervalprices2 = iunserializer($good2['intervalprice']);
            $price           = 0;

            foreach($intervalprices2 as $intervalprice){
                if($intervalprice['intervalnum'] <= $good2['total']){
                    $price = $intervalprice['intervalprice'];
                }
            }

            foreach($goods as &$good){
                if($good['goodsid'] == $good2['goodsid']){
                    $good['wholesaleprice'] = $price;
                    $good['goodsalltotal']  = $good2['total'];
                }
            }

            unset($good);
        }

        return $goods;
    }


    /**
     * 获取所有会员等级
     * @return type
     */
    private function getLevels($all = true)
    {
        $condition = '';

        if(!$all){
            $condition = ' and enabled=1';
        }

        return pdo_fetchall('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid' . $condition . ' order by level asc', array(':uniacid' => $this->g_uniacid));
    }

    private function getLevel($openid)
    {
        if(empty($openid)){
            return false;
        }

        return array('levelname' => empty($_S['shop']['levelname']) ? '普通会员' : $_S['shop']['levelname'], 'discount' => empty($_S['shop']['leveldiscount']) ? 10 : $_S['shop']['leveldiscount']);
    }


    // 获取商品折扣价
    private function getGoodsDiscountPrice($g, $level, $type = 0)
    {

        if(!empty($level['id'])){
            $level = pdo_fetch('select * from ' . tablename('ewei_shop_member_level') . ' where id=:id and uniacid=:uniacid and enabled=1 limit 1', array(':id' => $level['id'], ':uniacid' => $this->g_uniacid));
            $level = (empty($level) ? array() : $level);
        }

        if($type == 0){
            $total = $g['total'];
        }else{
            $total = 1;
        }

        $gprice = $g['marketprice'] * $total;

        if(empty($g['buyagain_islong'])){
            $gprice = $g['marketprice'] * $total;
        }

        $buyagain_sale = true;
        $buyagainprice = 0;
        $canbuyagain   = false;

        if(empty($g['is_task_goods'])){
            if(0 < floatval($g['buyagain'])){
                if(m('goods')->canBuyAgain($g)){
                    $canbuyagain = true;

                    if(empty($g['buyagain_sale'])){
                        $buyagain_sale = false;
                    }
                }
            }
        }

        $price                = $gprice;
        $price1               = $gprice;
        $price2               = $gprice;
        $taskdiscountprice    = 0;
        $lotterydiscountprice = 0;

        if(!empty($g['is_task_goods'])){
            $buyagain_sale = false;
            $price         = $g['task_goods']['marketprice'] * $total;

            if($price < $gprice){
                $d_price = abs($gprice - $price);

                if($g['is_task_goods'] == 1){
                    $taskdiscountprice = $d_price;
                }else{
                    if($g['is_task_goods'] == 2){
                        $lotterydiscountprice = $d_price;
                    }
                }
            }
        }

        $discountprice   = 0;
        $isdiscountprice = 0;
        $isd             = false;
        @$isdiscount_discounts = json_decode($g['isdiscount_discounts'], true);
        $discounttype = 0;
        $isCdiscount  = 0;
        $isHdiscount  = 0;
        if($g['isdiscount'] && (time() <= $g['isdiscount_time']) && $buyagain_sale){
            if(is_array($isdiscount_discounts)){
                $key = (!empty($level['id']) ? 'level' . $level['id'] : 'default');
                if(!isset($isdiscount_discounts['type']) || empty($isdiscount_discounts['type'])){
                    if(empty($g['merchsale'])){
                        $isd = trim($isdiscount_discounts[$key]['option0']);

                        if(!empty($isd)){
                            $price1 = $this->getFormartDiscountPrice($isd, $gprice, $total);
                        }
                    }else{
                        $isd = trim($isdiscount_discounts['merch']['option0']);

                        if(!empty($isd)){
                            $price1 = $this->getFormartDiscountPrice($isd, $gprice, $total);
                        }
                    }
                }else if(empty($g['merchsale'])){
                    $isd = trim($isdiscount_discounts[$key]['option' . $g['optionid']]);

                    if(!empty($isd)){
                        $price1 = $this->getFormartDiscountPrice($isd, $gprice, $total);
                    }
                }else{
                    $isd = trim($isdiscount_discounts['merch']['option' . $g['optionid']]);

                    if(!empty($isd)){
                        $price1 = $this->getFormartDiscountPrice($isd, $gprice, $total);
                    }
                }
            }

            if($gprice <= $price1){
                $isdiscountprice = 0;
                $isCdiscount     = 0;
            }else{
                $isdiscountprice = abs($price1 - $gprice);
                $isCdiscount     = 1;
            }
        }

        if(empty($g['isnodiscount']) && $buyagain_sale){
            $discounts = json_decode($g['discounts'], true);

            if(is_array($discounts)){
                $key = (!empty($level['id']) ? 'level' . $level['id'] : 'default');
                if(!isset($discounts['type']) || empty($discounts['type'])){
                    if(!empty($discounts[$key])){
                        $dd = floatval($discounts[$key]);
                        if((0 < $dd) && ($dd < 10)){
                            $price2 = round(($dd / 10) * $gprice, 2);
                        }
                    }else{
                        $dd = floatval($discounts[$key . '_pay'] * $total);
                        $md = floatval($level['discount']);

                        if(!empty($dd)){
                            $price2 = round($dd, 2);
                        }else{
                            if((0 < $md) && ($md < 10)){
                                $price2 = round(($md / 10) * $gprice, 2);
                            }
                        }
                    }
                }else{
                    $isd = trim($discounts[$key]['option' . $g['optionid']]);

                    if(!empty($isd)){
                        $price2 = $this->getFormartDiscountPrice($isd, $gprice, $total);
                    }
                }
            }

            if($gprice <= $price2){
                $discountprice = 0;
                $isHdiscount   = 0;
            }else{
                $discountprice = abs($price2 - $gprice);
                $isHdiscount   = 1;
            }
        }

        if($isCdiscount == 1){
            $price        = $price1;
            $discounttype = 1;
        }else{
            if($isHdiscount == 1){
                $price        = $price2;
                $discounttype = 2;
            }
        }

        $unitprice           = round($price / $total, 2);
        $isdiscountunitprice = round($isdiscountprice / $total, 2);
        $discountunitprice   = round($discountprice / $total, 2);

        if($canbuyagain){
            if(empty($g['buyagain_islong'])){
                $buyagainprice = ($unitprice * (10 - $g['buyagain'])) / 10;
            }else{
                $buyagainprice = ($price * (10 - $g['buyagain'])) / 10;
            }
        }

        $price = $price - $buyagainprice;
        return array('unitprice' => $unitprice, 'price' => $price, 'taskdiscountprice' => $taskdiscountprice, 'lotterydiscountprice' => $lotterydiscountprice, 'discounttype' => $discounttype, 'isdiscountprice' => $isdiscountprice, 'discountprice' => $discountprice, 'isdiscountunitprice' => $isdiscountunitprice, 'discountunitprice' => $discountunitprice, 'price0' => $gprice, 'price1' => $price1, 'price2' => $price2, 'buyagainprice' => $buyagainprice);
    }

    // 格式化商品折扣价
    private function getFormartDiscountPrice($isd, $gprice, $gtotal = 1)
    {
        $price = $gprice;

        if(!empty($isd)){
            if(strexists($isd, '%')){
                $dd = floatval(str_replace('%', '', $isd));
                if((0 < $dd) && ($dd < 100)){
                    $price = round(($dd / 100) * $gprice, 2);
                }
            }else{
                if(0 < floatval($isd)){
                    $price = round(floatval($isd * $gtotal), 2);
                }
            }
        }

        return $price;
    }

    // 获取运费
    public function getOrderDispatchPrice($goods, $member, $address, $saleset = false, $merch_array, $t, $loop = 0)
    {
        global $_W;
//        $area_set              = m('util')->get_area_config_set();
        $new_area              = intval($area_set['new_area']);
        $realprice             = 0;
        $dispatch_price        = 0;
        $dispatch_array        = array();
        $dispatch_merch        = array();
        $total_array           = array();
        $totalprice_array      = array();
        $nodispatch_array      = array();
        $goods_num             = count($goods);
        $seckill_payprice      = 0;
        $seckill_dispatchprice = 0;
        $user_city             = '';
        $user_city_code        = '';

        if(empty($new_area)){
            if(!empty($address)){
                $user_city = $user_city_code = $address['city'];
            }else{
                if(!empty($member['city'])){
                    $user_city = $user_city_code = $member['city'];
                }
            }
        }else{
            if(!empty($address)){
                $user_city      = $address['city'] . $address['area'];
                $user_city_code = $address['datavalue'];
            }
        }

        foreach($goods as $g){
            $realprice                       += $g['ggprice'];
            $dispatch_merch[$g['merchid']]   = 0;
            $total_array[$g['goodsid']]      += $g['total'];
            $totalprice_array[$g['goodsid']] += $g['ggprice'];
        }

        foreach($goods as $g){
            $isnodispatch = 0;
            $sendfree     = false;
            $merchid      = $g['merchid'];

            if($g['type'] == 5){
                $sendfree = true;
            }

            if(!empty($g['issendfree'])){
                $sendfree = true;
            }else{
                if(($g['ednum'] <= $total_array[$g['goodsid']]) && (0 < $g['ednum'])){
                    if(empty($new_area)){
                        $gareas = explode(';', $g['edareas']);
                    }else{
                        $gareas = explode(';', $g['edareas_code']);
                    }

                    if(empty($gareas)){
                        $sendfree = true;
                    }else if(!empty($address)){
                        if(!in_array($user_city_code, $gareas)){
                            $sendfree = true;
                        }
                    }else if(!empty($member['city'])){
                        if(!in_array($member['city'], $gareas)){
                            $sendfree = true;
                        }
                    }else{
                        $sendfree = true;
                    }
                }

                if((floatval($g['edmoney']) <= $totalprice_array[$g['goodsid']]) && (0 < floatval($g['edmoney']))){
                    if(empty($new_area)){
                        $gareas = explode(';', $g['edareas']);
                    }else{
                        $gareas = explode(';', $g['edareas_code']);
                    }

                    if(empty($gareas)){
                        $sendfree = true;
                    }else if(!empty($address)){
                        if(!in_array($user_city_code, $gareas)){
                            $sendfree = true;
                        }
                    }else if(!empty($member['city'])){
                        if(!in_array($member['city'], $gareas)){
                            $sendfree = true;
                        }
                    }else{
                        $sendfree = true;
                    }
                }
            }

            if($g['dispatchtype'] == 1){
                if(!empty($user_city)){
                    if(empty($new_area)){
                        $citys = $this->getAllNoDispatchAreas();
                    }else{
                        $citys = $this->getAllNoDispatchAreas('', 1);
                    }

                    if(!empty($citys)){
                        if(in_array($user_city_code, $citys) && !empty($citys)){
                            $isnodispatch = 1;
                            $has_goodsid  = 0;

                            if(!empty($nodispatch_array['goodid'])){
                                if(in_array($g['goodsid'], $nodispatch_array['goodid'])){
                                    $has_goodsid = 1;
                                }
                            }

                            if($has_goodsid == 0){
                                $nodispatch_array['goodid'][] = $g['goodsid'];
                                $nodispatch_array['title'][]  = $g['title'];
                                $nodispatch_array['city']     = $user_city;
                            }
                        }
                    }
                }

                if((0 < $g['dispatchprice']) && !$sendfree && ($isnodispatch == 0)){
                    $dispatch_merch[$merchid] += $g['dispatchprice'];
                    $dispatch_price += $g['dispatchprice'];
                }
            }else{
                if($g['dispatchtype'] == 0){
                    if(empty($g['dispatchid'])){
                        $dispatch_data = $this->getDefaultDispatch($merchid);
                    }else{
                        $dispatch_data = $this->getOneDispatch($g['dispatchid']);
                    }

                    if(empty($dispatch_data)){
                        $dispatch_data = $this->getNewDispatch($merchid);
                    }

                    if(!empty($dispatch_data)){
                        $isnoarea       = 0;
                        $dkey           = $dispatch_data['id'];
                        $isdispatcharea = intval($dispatch_data['isdispatcharea']);

                        if(!empty($user_city)){
                            if(empty($isdispatcharea)){
                                if(empty($new_area)){
                                    $citys = $this->getAllNoDispatchAreas($dispatch_data['nodispatchareas']);
                                }else{
                                    $citys = $this->getAllNoDispatchAreas($dispatch_data['nodispatchareas_code'], 1);
                                }

                                if(!empty($citys)){
                                    if(in_array($user_city_code, $citys)){
                                        $isnoarea = 1;
                                    }
                                }
                            }else{
                                if(empty($new_area)){
                                    $citys = $this->getAllNoDispatchAreas();
                                }else{
                                    $citys = $this->getAllNoDispatchAreas('', 1);
                                }

                                if(!empty($citys)){
                                    if(in_array($user_city_code, $citys)){
                                        $isnoarea = 1;
                                    }
                                }

                                if(empty($isnoarea)){
                                    $isnoarea = $this->checkOnlyDispatchAreas($user_city_code, $dispatch_data);
                                }
                            }

                            if(!empty($isnoarea)){
                                $isnodispatch = 1;
                                $has_goodsid  = 0;

                                if(!empty($nodispatch_array['goodid'])){
                                    if(in_array($g['goodsid'], $nodispatch_array['goodid'])){
                                        $has_goodsid = 1;
                                    }
                                }

                                if($has_goodsid == 0){
                                    $nodispatch_array['goodid'][] = $g['goodsid'];
                                    $nodispatch_array['title'][]  = $g['title'];
                                    $nodispatch_array['city']     = $user_city;
                                }
                            }
                        }

                        if(!$sendfree && ($isnodispatch == 0)){
                            $areas = unserialize($dispatch_data['areas']);

                            if($dispatch_data['calculatetype'] == 1){
                                $param = $g['total'];
                            }else{
                                $param = $g['weight'] * $g['total'];
                            }

                            if(array_key_exists($dkey, $dispatch_array)){
                                $dispatch_array[$dkey]['param'] += $param;
                            }else{
                                $dispatch_array[$dkey]['data']  = $dispatch_data;
                                $dispatch_array[$dkey]['param'] = $param;
                            }

                            if($seckillinfo && ($seckillinfo['status'] == 0)){
                                if(array_key_exists($dkey, $dispatch_array)){
                                    $dispatch_array[$dkey]['seckillnums'] += $param;
                                }else{
                                    $dispatch_array[$dkey]['seckillnums'] = $param;
                                }
                            }
                        }
                    }
                }
            }
        }

        if(!empty($dispatch_array)){
            $dispatch_info = array();

            foreach($dispatch_array as $k => $v){
                $dispatch_data = $dispatch_array[$k]['data'];
                $param         = $dispatch_array[$k]['param'];
                $areas         = unserialize($dispatch_data['areas']);

                if(!empty($address)){
                    $dprice = $this->getCityDispatchPrice($areas, $address, $param, $dispatch_data);
                }else if(!empty($member['city'])){
                    $dprice = $this->getCityDispatchPrice($areas, $member, $param, $dispatch_data);
                }else{
                    $dprice = $this->getDispatchPrice($param, $dispatch_data);
                }

                $merchid                  = $dispatch_data['merchid'];
                $dispatch_merch[$merchid] += $dprice;

                $dispatch_price += $dprice;

                $dispatch_info[$dispatch_data['id']]['price']     += $dprice;
                $dispatch_info[$dispatch_data['id']]['freeprice'] = intval($dispatch_data['freeprice']);
            }

            if(!empty($dispatch_info)){
                foreach($dispatch_info as $k => $v){
                    if((0 < $v['freeprice']) && ($v['freeprice'] <= $v['price'])){
                        $dispatch_price -= $v['price'];
                    }
                }

                if($dispatch_price < 0){
                    $dispatch_price = 0;
                }
            }
        }

        if(!empty($merch_array)){
            foreach($merch_array as $key => $value){
                $merchid = $key;

                if(0 < $merchid){
                    $merchset = $value['set'];

                    if(!empty($merchset['enoughfree'])){
                        if(floatval($merchset['enoughorder']) <= 0){
                            $dispatch_price           = $dispatch_price - $dispatch_merch[$merchid];
                            $dispatch_merch[$merchid] = 0;
                        }else{
                            if(floatval($merchset['enoughorder']) <= $merch_array[$merchid]['ggprice']){
                                if(empty($merchset['enoughareas'])){
                                    $dispatch_price           = $dispatch_price - $dispatch_merch[$merchid];
                                    $dispatch_merch[$merchid] = 0;
                                }else{
                                    $areas = explode(';', $merchset['enoughareas']);

                                    if(!empty($address)){
                                        if(!in_array($address['city'], $areas)){
                                            $dispatch_price           = $dispatch_price - $dispatch_merch[$merchid];
                                            $dispatch_merch[$merchid] = 0;
                                        }
                                    }else if(!empty($member['city'])){
                                        if(!in_array($member['city'], $areas)){
                                            $dispatch_price           = $dispatch_price - $dispatch_merch[$merchid];
                                            $dispatch_merch[$merchid] = 0;
                                        }
                                    }else{
                                        if(empty($member['city'])){
                                            $dispatch_price           = $dispatch_price - $dispatch_merch[$merchid];
                                            $dispatch_merch[$merchid] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if($saleset){
            if(!empty($saleset['enoughfree'])){
                $saleset_free = 0;

                if($loop == 0){
                    if(floatval($saleset['enoughorder']) <= 0){
                        $saleset_free = 1;
                    }else{
                        if(floatval($saleset['enoughorder']) <= $realprice - $seckill_payprice){
                            if(empty($saleset['enoughareas'])){
                                $saleset_free = 1;
                            }else{
                                if(empty($new_area)){
                                    $areas = explode(';', trim($saleset['enoughareas'], ';'));
                                }else{
                                    $areas = explode(';', trim($saleset['enoughareas_code'], ';'));
                                }

                                if(!empty($user_city_code)){
                                    if(!in_array($user_city_code, $areas)){
                                        $saleset_free = 1;
                                    }
                                }
                            }
                        }
                    }
                }

                if($saleset_free == 1){
                    $is_nofree = 0;
                    $new_goods = array();

                    if(!empty($saleset['goodsids'])){
                        foreach($goods as $k => $v){
                            if(!in_array($v['goodsid'], $saleset['goodsids'])){
                                $new_goods[$k] = $goods[$k];
                                unset($goods[$k]);
                            }else{
                                $is_nofree = 1;
                            }
                        }
                    }

                    if(($is_nofree == 1) && ($loop == 0)){
                        if($goods_num == 1){
                            $new_data1      = $this->getOrderDispatchPrice($goods, $member, $address, $saleset, $merch_array, $t, 1);
                            $dispatch_price = $new_data1['dispatch_price'];
                        }else{
                            $new_data2      = $this->getOrderDispatchPrice($new_goods, $member, $address, $saleset, $merch_array, $t, 1);
                            $dispatch_price = $dispatch_price - $new_data2['dispatch_price'];
                        }
                    }else{
                        if($saleset_free == 1){
                            $dispatch_price = 0;
                        }
                    }
                }
            }
        }

        if($dispatch_price == 0){
            foreach($dispatch_merch as &$dm){
                $dm = 0;
            }

            unset($dm);
        }

        if(!empty($nodispatch_array)){
            $nodispatch = '商品';

            foreach($nodispatch_array['title'] as $k => $v){
                $nodispatch .= $v . ',';
            }

            $nodispatch                       = trim($nodispatch, ',');
            $nodispatch                       .= '不支持配送到' . $nodispatch_array['city'];
            $nodispatch_array['nodispatch']   = $nodispatch;
            $nodispatch_array['isnodispatch'] = 1;
        }

        $data                           = array();
        $data['dispatch_price']         = $dispatch_price + $seckill_dispatchprice;
        $data['dispatch_merch']         = $dispatch_merch;
        $data['nodispatch_array']       = $nodispatch_array;
        $data['seckill_dispatch_price'] = $seckill_dispatchprice;
        return $data;
    }


    // 创建订单号
    public function createNO($table, $field, $prefix)
    {
        $billno = date('YmdHis') . random(6, true);

        while(1){
            $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_' . $table) . ' where ' . $field . '=:billno limit 1', array(':billno' => $billno));

            if($count <= 0){
                break;
            }

            $billno = date('YmdHis') . random(6, true);
        }

        return $prefix . $billno;
    }

    /**
     * 获取配置
     */
    private function getSysset($key = '', $uniacid = 0)
    {
        global $_W;
        global $_GPC;
        $set     = $this->getSetData($uniacid);
        $allset  = iunserializer($set['sets']);
        $retsets = array();

        if(!empty($key)){
            if(is_array($key)){
                foreach($key as $k){
                    $retsets[$k] = isset($allset[$k]) ? $allset[$k] : array();
                }
            }else{
                $retsets = (isset($allset[$key]) ? $allset[$key] : array());
            }

            return $retsets;
        }

        return $allset;
    }

    public function getSetData($uniacid = 0)
    {
        global $_W;

        if(empty($uniacid)){
            $uniacid = $this->g_uniacid;
        }

        $set = pdo_fetch('select * from ' . tablename('ewei_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $uniacid));

        if(empty($set)){
            $set = array();
        }

        return $set;
    }


    public function getSec($uniacid = 0)
    {
        global $_W;

        if(empty($uniacid)){
            $uniacid = $this->g_uniacid;
        }

        $set = pdo_fetch('select sec from ' . tablename('ewei_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $uniacid));

        if(empty($set)){
            $set = array();
        }

        return $set;
    }


    /**
     * 支付成功
     * @global type $_W
     * @param type $params
     */
    public function payResult($params)
    {
        global $_W;
        //                var_dump('$params==',$params);exit;
        $fee         = intval($params['fee']);
        $data        = array('status' => $params['result'] == 'success' ? 1 : 0);
        $ordersn_tid = $params['tid'];
        $ordersn     = rtrim($ordersn_tid, 'TR');
        $order       = pdo_fetch('select id,ordersn, price,openid,dispatchtype,addressid,carrier,status,isverify,deductcredit2,`virtual`,isvirtual,couponid,isvirtualsend,isparent,paytype,merchid,agentid,createtime,buyagainprice,istrade,tradestatus from ' . tablename('ewei_shop_order') . ' where  ordersn=:ordersn and uniacid=:uniacid limit 1', array(':uniacid' => $this->g_uniacid, ':ordersn' => $ordersn));
        $orderid     = $order['id'];
        //        var_dump('$params==', $params, $orderid, $order);exit;

        if($params['from'] == 'return'){
            $address = false;

            if(empty($order['dispatchtype'])){
                $address = pdo_fetch('select realname,mobile,address from ' . tablename('ewei_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
            }

            //            var_dump('$params==', $params, $orderid, $order);exit;


            $carrier = false;
            if(($order['dispatchtype'] == 1) || ($order['isvirtual'] == 1)){
                $carrier = unserialize($order['carrier']);
            }

            //            var_dump('$params==',$params,$order);exit;

            // istrade	v2 0 普通 1 门店预约订单
            if($order['istrade'] == 0){
                if($order['status'] == 0){
                    //                    var_dump('$params==',$params,$order,!empty($order['virtual']) && com('virtual'));exit;
                    if(!empty($order['virtual']) && com('virtual')){
                        return com('virtual')->pay($order);
                    }

                    if($order['isvirtualsend']){
                        return $this->payVirtualSend($order['id']);
                    }

                    $time        = time();
                    $change_data = array();

                    $change_data['status'] = 1;

                    $change_data['paytime'] = $time;

                    if($order['isparent'] == 1){
                        $change_data['merchshow'] = 1;
                    }

                    pdo_update('ewei_shop_order', $change_data, array('id' => $orderid));

                    if($order['isparent'] == 1){
                        $this->setChildOrderPayResult($order, $time, 1);
                    }

                    $this->setStocksAndCredits($orderid, 1);
                }
            }

            return true;
        }

        return false;
    }

    public function getSalt()
    {
        $salt = random(16);

        while(1){
            $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where salt=:salt limit 1', array(':salt' => $salt));

            if($count <= 0){
                break;
            }

            $salt = random(16);
        }

        return $salt;
    }


    // 获取用户信息
    public function get_member_info($mobile)
    {
        $member_info = pdo_fetch('select id,openid,mobile,pwd,salt from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and mobileverify=1 and uniacid=:uniacid limit 1', array(':mobile' => $mobile, ':uniacid' => $this->g_uniacid));
        return $member_info;
    }


    // 获取地区
    public function getAllNoDispatchAreas($areas = array(), $type = 0)
    {
        global $_W;
        $tradeset = $this->getSysset('trade');

        if (empty($type)) {
            $dispatchareas = iunserializer($tradeset['nodispatchareas']);
        }
        else {
            $dispatchareas = iunserializer($tradeset['nodispatchareas_code']);
        }

        $set_citys = array();
        $dispatch_citys = array();

        if (!empty($dispatchareas)) {
            $set_citys = explode(';', trim($dispatchareas, ';'));
        }

        if (!empty($areas)) {
            $areas = iunserializer($areas);

            if (!empty($areas)) {
                $dispatch_citys = explode(';', trim($areas, ';'));
            }
        }

        $citys = array();

        if (!empty($set_citys)) {
            $citys = $set_citys;
        }

        if (!empty($dispatch_citys)) {
            $citys = array_merge($citys, $dispatch_citys);
            $citys = array_unique($citys);
        }

//        var_dump('$citys==',$citys);exit;
        return $citys;
    }


    /**
     * 获取默认快递信息
     */
    public function getDefaultDispatch($merchid = 0)
    {
        $sql = 'select * from ' . tablename('ewei_shop_dispatch') . ' where isdefault=1 and uniacid=:uniacid and merchid=:merchid and enabled=1 Limit 1';
        $params = array(':uniacid' => $this->g_uniacid, ':merchid' => $merchid);
        $data = pdo_fetch($sql, $params);
        return $data;
    }
    

    /**
     * 获取最新的一条快递信息
     */
    public function getNewDispatch($merchid = 0)
    {
        $sql = 'select * from ' . tablename('ewei_shop_dispatch') . ' where uniacid=:uniacid and merchid=:merchid and enabled=1 order by id desc Limit 1';
        $params = array(':uniacid' => $this->g_uniacid, ':merchid' => $merchid);
        $data = pdo_fetch($sql, $params);
        return $data;
    }

    /**
     * 获取商品规格
     * @param type $goodsid
     * @param type $optionid
     * @return type
     */
    public function getOption($goodsid = 0, $optionid = 0)
    {
        return pdo_fetch('select * from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid Limit 1', array(':id' => $optionid, ':uniacid' => $this->g_uniacid, ':goodsid' => $goodsid));
    }
    


}


