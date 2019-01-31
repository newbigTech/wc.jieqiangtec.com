<?php
/**
 * 商品相关接口
 */
define('IN_API', true);
error_reporting(E_ALL & ~E_WARNING & ~Notice);

if(!in_array($a, array('index', 'category', 'detail'))){
    echo_json('501', '非法访问');
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
}elseif($a == 'order'){
    // 商品订单接口
    $good->detail();
}elseif($a == 'buy'){
    // 商品下单接口
    $good->buy();
}elseif($a == 'cancel'){
    // 商品取消下单接口
    $good->cancel();
}elseif($a == 'express'){
    // 商品查看物流接口
    $good->express();
}elseif($a == 'express'){
    // 收货确认通知接口
    $good->express();
}

class Goods
{
    // 商品列表接口
    public function main()
    {
        global $_GPC;
        $sqlcondition = $groupcondition = '';
        $condition    = ' WHERE g.`uniacid` = 10';

        $sql           = 'SELECT g.id FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition;
        $total_all     = pdo_fetchall($sql);
        $data['total'] = $total = count($total_all);
        unset($total_all);

        if(!empty($total)){
            $pindex = max(1, intval($_GPC['page']));
            $psize  = 20;

            $sql          = 'SELECT g.* FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition . " ORDER BY g.`status` DESC, g.`displayorder` DESC,\r\n                g.`id` DESC LIMIT " . (($pindex - 1) * $psize) . ',' . $psize;
            $data['list'] = pdo_fetchall($sql);
            //        var_dump($sql, $params,$data);exit;
            echo_json('200', 'success', $data);
        }

    }

    // 商品分类接口
    public function category()
    {
        $data['list'] = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_category') . ' WHERE uniacid = 10 ORDER BY parentid ASC, displayorder DESC');
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

    // 商品订单接口
    public function order()
    {
        global $_GPC;
        $id = intval($_GPC['id']);
        if(!$id){
            echo_json('501', '参数错误', $data);
        }
        $data['detail'] = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_goods') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => 10));
        echo_json('200', 'success', $data);
    }

    // 商品取消下单接口
    public function cancel()
    {
        global $_GPC;
        $id = intval($_GPC['id']);
        if(!$id){
            echo_json('501', '参数错误', $data);
        }
        $data['detail'] = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_goods') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => 10));
        echo_json('200', 'success', $data);
    }

    // 商品查看物流接口
    public function express()
    {
        global $_GPC;
        $id = intval($_GPC['id']);
        if(!$id){
            echo_json('501', '参数错误', $data);
        }
        $data['detail'] = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_goods') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => 10));
        echo_json('200', 'success', $data);
    }


}