<?php
defined('IN_IA') or exit('Access Denied');
define('ZC_ROOT', IA_ROOT . '/addons/sen_appfreeitem');
define('CSS_PATH', '../addons/sen_appfreeitem/template/style/css/');
define('JS_PATH', '../addons/sen_appfreeitem/template/style/js/');
define('IMG_PATH', '../addons/sen_appfreeitem/template/style/images/');

// 引入方法
include '../addons/sen_appfreeitem/inc/core/function/forum.func.php';
include '../addons/sen_appfreeitem/inc/core/function/qiniu.mod.php';
include '../addons/sen_appfreeitem/inc/core/function/functions.php';

// 引入民生加密类
//include '../addons/sen_appfreeitem/inc/core/class/decryptAndCheck.class.php';
//include '../addons/sen_appfreeitem/inc/core/class/php_java.php';
include_once IA_ROOT . '/payment/unionpay/ms_lajp/decryptAndCheck.class.php';
include_once IA_ROOT . '/payment/unionpay/ms_lajp/php_java.php';
//$ms_login_url = "http://197.3.176.26:8000/ecshopMerchantTest/index.jsp";

/*try
{
    $ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::SignAndEncryptMessage", base64_encode($orderid));
    // echo "{$ret}<br>";
}
catch(Exception $e)
{
    echo "Err:{$e}<br>";
}

$order_info = array('orderid' => $ret);
var_dump($order_info);

// 解密
$base64Encode  = $_REQUEST['base64Encode'];
$base64Encode  = trim($base64Encode);
try
{

$ret = lajp_call("cfca.sadk.cmbc.tools.php.PHPDecryptKit::DecryptAndVerifyMessage", $base64Encode);
echo "{$ret}<br>";
// echo "{$base64Encode}<br>";
}
catch(Exception $e)
{
    echo "Err:{$e}<br>";
}

exit;*/


class sen_appfreeitemModuleSite extends WeModuleSite
{
    public function __construct()
    {
        global $_W;
    }

    public function doMobileShareData()
    {
        global $_W, $_GPC;
        if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) || strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != "xmlhttprequest") {
            exit('非法访问');
        }
        $id = intval($_GPC['id']);
        $data = array('uinacid' => $_W['uniacid'], 'pid' => $id, 'share_from' => $_GPC['from'], 'share_time' => time(),);
        pdo_insert('sen_appfreeitem_share', $data);
        echo json_encode($data);
    }

    // 发布产品
    public function doMobilePublish()
    {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'choose';
        $settings = $this->module['config'];
        $children = array();
        $category = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_category') . " WHERE weid = '{$_W['uniacid']}' and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }
        if ($operation == 'post1') {
            $project_id = intval($_GPC['project_id']);
            if (!empty($project_id)) {
                $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id=:id AND from_user=:from_user", array(':id' => $project_id, ':from_user' => $_W['openid']));
                if (empty($project)) {
                    unset($project_id);
                }
            }
            if ($_GPC['ajax'] == 1) {
                /*// TODO debug
                if ($settings['ispublish'] != 1) {
                    die(json_encode(array('status' => 0, 'info' => '本站暂未开放产品发布111', 'jump' => $this->createMobileUrl('list'))));
                }*/
//                $insert = array('weid' => $_W['uniacid'], 'from_user' => $_W['openid'], 'displayorder' => 0, 'pcate' => intval($_GPC['cate_id']), 'title' => $_GPC['name'], 'limit_price' => intval($_GPC['limit_price']), 'deal_days' => intval($_GPC['deal_days']), 'thumb' => $_GPC['image'], 'brief' => $_GPC['brief'], 'content' => $_GPC['descript'], 'lianxiren' => $_GPC['lianxiren'], 'qq' => $_GPC['qq'], 'status' => 0, 'createtime' => time(),);
                $insert = array('weid' => $_W['uniacid'], 'from_user' => $_W['openid'], 'displayorder' => 0, 'pcate' => intval($_GPC['cate_id']), 'title' => $_GPC['name'], 'price' => intval($_GPC['limit_price']), 'deal_days' => time(), 'thumb' => $_GPC['image'], 'content' => $_GPC['descript'], 'lianxiren' => $_GPC['lianxiren'], 'tel' => $_GPC['tel'], 'status' => 0, 'createtime' => time(),);
                if (!empty($project_id)) {
                    unset($insert['createtime']);
                    unset($insert['status']);
                    pdo_update('sen_appfreeitem_project', $insert, array('id' => $project_id));
                } else {
                    pdo_insert('sen_appfreeitem_project', $insert);
                    $project_id = pdo_insertid();
                }
                die(json_encode(array('status' => 1, 'info' => $project_id, 'jump' => $this->createMobileUrl('Publish', array('op' => 'post2', 'project_id' => $project_id)))));
            }
        } elseif ($operation == 'post2') {
            $project_id = intval($_GPC['project_id']);
            $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id=:id AND from_user=:from_user", array(':id' => $project_id, ':from_user' => $_W['openid']));
            if (empty($project)) {
                message('抱歉，产品不存在！');
            }
            $item_list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_project_item') . " WHERE weid=:weid AND pid=:pid ORDER BY displayorder DESC", array(':weid' => $_W['uniacid'], ':pid' => $project_id));
            $item_id = intval($_GPC['item_id']);
            if (!empty($item_id)) {
                $pitem = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project_item') . " WHERE id=:id AND pid=:pid", array(':id' => $item_id, ':pid' => $project_id));
                if (empty($pitem)) {
                    unset($item_id);
                }
            }
            if ($_GPC['ajax'] == 1) {
                /*// TODO debug
                if ($settings['ispublish'] != 1) {
                    die(json_encode(array('status' => 0, 'info' => '本站暂未开放产品发布222', 'jump' => $this->createMobileUrl('list'))));
                }*/
                $insert = array('weid' => $_W['uniacid'], 'pid' => $project_id, 'displayorder' => 0, 'price' => $_GPC['price'], 'description' => $_GPC['description'], 'thumb' => $_GPC['image'][0], 'limit_num' => intval($_GPC['limit_user']), 'return_type' => $_GPC['is_delivery'] == 0 ? 2 : 1, 'delivery_fee' => intval($_GPC['delivery_fee']), 'repaid_day' => intval($_GPC['repaid_day']), 'createtime' => time(),);
                if (!empty($item_id)) {
                    unset($insert['createtime']);
                    pdo_update('sen_appfreeitem_project_item', $insert, array('id' => $item_id));
                } else {
                    pdo_insert('sen_appfreeitem_project_item', $insert);
                    $item_id = pdo_insertid();
                }
                die(json_encode(array('status' => 1, 'info' => "保存成功,等待审核", 'jump' => $this->createMobileUrl('Publish', array('op' => 'post2', 'project_id' => $project_id)))));
            }
        } elseif ($operation == 'delete_item') {
            $project_id = intval($_GPC['project_id']);
            $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id=:id AND from_user=:from_user", array(':id' => $project_id, ':from_user' => $_W['openid']));
            if (empty($project)) {
                die(json_encode(array('status' => 0, 'info' => '操作无权限', 'jump' => $this->createMobileUrl('list'))));
            }
            $item_id = intval($_GPC['item_id']);
            $pitem = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project_item') . " WHERE id=:id AND pid=:pid", array(':id' => $item_id, ':pid' => $project_id));
            if (!empty($pitem)) {
                pdo_delete('sen_appfreeitem_project_item', array('id' => $item_id));
                die(json_encode(array('status' => 1, 'info' => '删除成功！', 'jump' => $this->createMobileUrl('Publish', array('op' => 'post2', 'project_id' => $project_id)))));
            } else {
                die(json_encode(array('status' => 0, 'info' => '产品不存在！', 'jump' => $this->createMobileUrl('Publish', array('op' => 'post2', 'project_id' => $project_id)))));
            }
        } elseif ($operation == 'post3') {
            $project_id = intval($_GPC['project_id']);
            $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id=:id AND from_user=:from_user", array(':id' => $project_id, ':from_user' => $_W['openid']));
            if (empty($project)) {
                die(json_encode(array('info' => '产品不存在！', 'jump' => $this->createMobileUrl('Publish', array('op' => 'post2', 'project_id' => $project_id)))));
            }
            $item_num = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sen_appfreeitem_project_item') . " WHERE weid=:weid AND pid=:pid ORDER BY displayorder DESC", array(':weid' => $_W['uniacid'], ':pid' => $project_id));
            if ($item_num == 0) {
                die(json_encode(array('info' => '您至少需要发布一个回报', 'jump' => $this->createMobileUrl('Publish', array('op' => 'post2', 'project_id' => $project_id)))));
            } else {
                pdo_update('sen_appfreeitem_project', array('status' => 1), array('id' => $project_id));
                die(json_encode(array('status' => 1, 'info' => '提交成功，管理员正在审核中', 'jump' => $this->createMobileUrl('list'))));
            }
        }

        include $this->template('publish');
    }

    public function doMobileCategory()
    {
        global $_GPC, $_W;
        $category = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_category') . " WHERE weid = '{$_W['uniacid']}' and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }
        $pagetitle = "全部分类";
        include $this->template('category');
    }

    public function doMobileTip()
    {
        global $_GPC, $_W;
        $moduleconfig = $this->module['config'];
        $title = !empty($moduleconfig['shopname']) ? $moduleconfig['shopname'] : '';
        include $this->template('tip');
    }


    public function doMobileNews()
    {
        global $_W, $_GPC;
        $cateid = intval($_GPC['cateid']);
        $condition = " WHERE uniacid = '{$_W['uniacid']}' ";
        if (!empty($cateid)) {
            $condition .= " AND cateid = '{$cateid}'";
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $sql = 'SELECT * FROM ' . tablename('sen_appfreeitem_report') . $condition . " ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $news = pdo_fetchall($sql, $params);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_report') . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        $category = pdo_fetchall('SELECT * FROM ' . tablename('sen_appfreeitem_report_category') . ' WHERE uniacid = :uniacid ORDER BY displayorder DESC', array(':uniacid' => $_W['uniacid']));
        $title = "报告列表";
        $pagetitle = "报告列表";
        include $this->template('news_list');
    }

    public function doMobileNews_detail()
    {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        if (empty($id)) {
            message('参数错误', $this->createMobileUrl('news'), 'error');
        }
        $article = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_report') . " WHERE id=:id", array(':id' => $id));
        $title = $article['title'] . ' - ' . $this->getnewscategory($article['cateid']);
        $pagetitle = $article['title'];
        include $this->template('news_detail');
    }

    // 首页
    public function doMobileList()
    {
        global $_W, $_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $condition = '';
        if (!empty($_GPC['ccate'])) {
            $cid = intval($_GPC['ccate']);
            $condition .= " AND ccate = '{$cid}'";
            $_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('sen_appfreeitem_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
        } elseif (!empty($_GPC['pcate'])) {
            $cid = intval($_GPC['pcate']);
            $condition .= " AND pcate = '{$cid}'";
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        $children = array();
        $category = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_category') . " WHERE weid = '{$_W['uniacid']}' and parentid=0 and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }

        // 类型 9-全部 0-免邮 1-付邮 2-往期
        $time = time();
        $type = $_GPC['type'];
        // var_dump($type);exit;
        if ($type == 1) {
            $condition .= " and freight>0 ";
        } elseif ($type == 2) {
            $condition .= " and deal_days < $time  ";
        } else {
            if ($type === '0') {
                $condition .= " and freight<=0 ";
            } else {
                $type = 9;
            }
        }

        // 幻灯片
        $advs = pdo_fetchall("select * from " . tablename('sen_appfreeitem_adv') . " where enabled=1 and weid= '{$_W['uniacid']}'");
        $rpindex = max(1, intval($_GPC['rpage']));
        $rpsize = 6;

        // 首页展示
        /*$condition = ' and 1';*/
        $_GET['brand_id'] AND $condition .= ' AND brands = ' . $_GET['brand_id'];
        $rlist = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE weid = '{$_W['uniacid']}' AND status >= '2' and status < '4' and isrecommand = '1' $condition ORDER BY displayorder DESC, finish_price DESC LIMIT " . ($rpindex - 1) * $rpsize . ',' . $rpsize);



        // 热门推荐
        $hot_list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE weid = '{$_W['uniacid']}' AND status >= '2' and status < '4' and ishot = '1'  and deal_days > $time  ORDER BY displayorder DESC, id DESC, finish_price DESC LIMIT 4 ");

        $carttotal = $this->getCartTotal();
        $moduleconfig = $this->module['config'];
        $title = !empty($moduleconfig['shopname']) ? $moduleconfig['shopname'] . ' - 首页' : '免费试用产品列表';
        include $this->template('list');
    }

    //  试用秀
    public function doMobilelist2()
    {
        global $_GPC, $_W;
        $pindex = max(1, intval($_GPC["page"]));
        $psize = 10;

        // 幻灯片
        $advs = pdo_fetchall("select * from " . tablename('sen_appfreeitem_adv') . " where enabled=1 and weid= '{$_W['uniacid']}'");

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'jpcp') {
            $sql = "SELECT r.*,o.ordersn,p.title,p.thumb,m.nickname,m.avatar FROM ims_mc_mapping_fans f,ims_sen_appfreeitem_report as r,ims_sen_appfreeitem_order as o,ims_sen_appfreeitem_project as p,ims_mc_members as m where r.is_display=1 and r.parent_id=0 and r.oid=o.id and r.pid=p.id AND r.from_user = f.openid AND f.uid = m.uid ORDER BY  r.id DESC, p.id DESC";
//            $lists = pdo_fetchall($sql);
            $rlist = pdo_fetchall($sql);
            foreach ($rlist as $key => $val) {
                $rlist[$key]['images'] = iunserializer($val['images']);
            }
            // var_dump('$rlist==',$rlist);exit;
            $title = '试用秀';
        } elseif ($operation == 'display') {
//            $list2 = pdo_fetchall("SELECT * FROM ims_sen_appfreeitem_project where status =4  ORDER BY id DESC  ");
            $rlist = pdo_fetchall("SELECT * FROM ims_sen_appfreeitem_project where status =4  ORDER BY id DESC  ");
            $title = '往期活动';
        }
        include $this->template('list2');
    }

    // 品牌馆
    public function doMobileBrand()
    {
        global $_GPC, $_W;
        // 幻灯片
        $advs = pdo_fetchall("select * from " . tablename('sen_appfreeitem_adv') . " where enabled=1 and weid= '{$_W['uniacid']}'");

        $title = '品牌馆';
        if ($_GET['brand_id']) {
            $brand = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_brand') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY displayorder DESC, id DESC');
            // var_dump('$brand==',$brand);exit;
            include $this->template('brand');
        } else {
            $brand = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_brand') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY displayorder DESC, id DESC');
            // var_dump('$brand==',$brand);exit;
            foreach ($brand as $k => $v) {
                $date = pdo_fetch('SELECT MIN(starttime) as starttime,MAX(deal_days) as deal_days FROM ' . tablename('sen_appfreeitem_project') . ' WHERE brands=' . $v['id'] . ' AND weid = \'' . $_W['uniacid'] . '\' ');
                $brand[$k]['start_time'] = $date['starttime'];
                $brand[$k]['deal_days'] = $date['deal_days'];

                // 没有相关品牌产品的时间
                if (empty($date['starttime']) OR empty($date['deal_days'])) {
                    unset($brand[$k]);
                }
            }

            if ($_GET['test']) {
                include $this->template('brand2');
            } else {
                include $this->template('brand');
            }
        }

    }

    // 规则公告
    public function doMobileRule()
    {
        global $_GPC, $_W;
        // 幻灯片
        $advs = pdo_fetchall("select * from " . tablename('sen_appfreeitem_adv') . " where enabled=1 and weid= '{$_W['uniacid']}'");

        $id = intval($_GPC['id']);
        $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_rule') . " WHERE wid = :wid", array(':wid' => $_W['uniacid']));
        $title = "参与规则";
        include $this->template('rule');
    }


    public function doMobileAjaxShare()
    {
        global $_W;
        $settings = $this->module['config'];
        if ($settings['share_qzfx'] == 1) {
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }


    public function doMobileCxShare()
    {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $share = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_share') . " WHERE uinacid = :uinacid and pid = :pid and share_from =:share_from ", array(':uinacid' => $_W['uniacid'], ':pid' => $id, ':share_from' => $_W['fans']['from_user']));
        $settings = $this->module['config'];
        if (!empty($share)) {
            $data = array('count' => 1, 'content' => $settings['share_content']);
            echo json_encode($data);
        } else {
            $data = array('count' => 0, 'content' => $settings['share_content']);
            echo json_encode($data);
        }
    }

    public function doMobileDetail()
    {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE weid = :weid AND id = :id", array(':weid' => $_W['uniacid'], ':id' => $id));
        if (empty($item)) {
            message("抱歉，产品不存在!", referer(), "error");
        }
        $favournum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_cart') . " WHERE weid = '{$_W['uniacid']}' AND projectid = '{$id}'");
        $isfavour = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_cart') . " WHERE projectid = '{$id}' AND from_user = '{$_W['fans']['from_user']}'");
        $carttotal = $this->getCartTotal();
        $title = $item['title'];
        $moduleconfig = $this->module['config'];
        $sql = "select * from " . tablename('sen_appfreeitem_order_ws') . " WHERE weid='{$_W['uniacid']}' AND pid='{$id}' AND status=1 LIMIT 10";
        $wslist = pdo_fetchall($sql);
        $title = "产品展示";
        include $this->template('detail');
    }


    public function doMobileDetail_more()
    {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $detail = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $id));
        if (empty($detail)) {
            message("抱歉，产品不存在!", referer(), "error");
        }
        $title = $detail['title'];
        $pagetitle = "产品详细说明";
        include $this->template('detail_more');
    }

    public function doMobileWsconfirm()
    {
        global $_W, $_GPC;
        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
        $id = intval($_GPC['id']);
        $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $id));
        if (empty($project)) {
            message("抱歉，该产品不存在!", referer(), "error");
        }
        if (time() <= $project['starttime']) {
            message("抱歉，该产品尚未开始!", referer(), "error");
        } elseif (time() > $project['starttime'] + $project['deal_days'] * 86400) {
            message("抱歉，该产品已经结束!", referer(), "error");
        }
        if (empty($_GPC['pay_money'])) {
            message("请输入支持的金额!", referer(), "error");
        }
        $data = array('weid' => $_W['uniacid'], 'from_user' => $_W['fans']['from_user'], 'nickname' => $_W['fans']['tag']['nickname'], 'avatar' => $_W['fans']['tag']['avatar'], 'ordersn' => date('md') . random(4, 1), 'price' => $_GPC['pay_money'], 'status' => 0, 'remark' => $_GPC['pay_remark'], 'pid' => $id, 'createtime' => TIMESTAMP,);
        pdo_insert('sen_appfreeitem_order_ws', $data);
        $orderid = pdo_insertid();
        header("Location:" . $this->createMobileUrl('pay', array('orderid' => $orderid, 'type' => 'ws')));
    }


    public function doMobileUpload()
    {
        global $_GPC, $_W;
        load()->classs('account');
        $result = array('error' => 'error', 'message' => '', 'data' => '');
        if (empty($_W['acid'])) {
            $sql = "SELECT acid FROM " . tablename('mc_mapping_fans') . " WHERE openid = :openid AND uniacid = :uniacid limit 1";
            $params = array(':openid' => $_W['openid'], ':uniacid' => $_W['uniacid']);
            $_W['acid'] = pdo_fetchcolumn($sql, $params);
        }
        if (empty($_W['acid'])) {
            $result['message'] = '没有找到相关公众账号';
            die(json_encode($result));
        }
        $acid = $_W['acid'];
        $acc = WeAccount::create($acid);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'upload';
        $type = !empty($_GPC['type']) ? $_GPC['type'] : 'image';
        if ($operation == 'upload') {
            if ($type == 'image') {
                $serverId = trim($_GPC['serverId']);
                $localId = trim($_GPC['localId']);
                $media = array();
                $media['media_id'] = $serverId;
                $media['type'] = $type;
                $result['serverId'] = $serverId;
                $result['localId'] = $localId;
                $filename = $acc->downloadMedia($media);
                if (is_error($filename)) {
                    $result['message'] = '上传失败';
                    die(json_encode($result));
                }
                $result['error'] = 'success';
                $result['filename'] = $filename;
                $result['path'] = $_W['attachurl'] . $filename;
                die(json_encode($result));
            }
        } elseif ($operation == 'remove') {
            $file = $_GPC['file'];
            file_delete($file);
            exit(json_encode(array('status' => true)));
        }
    }


    // 确认付款
    public function doMobileConfirm()
    {
        global $_W, $_GPC;

        // var_dump('购买==',$_W, $_GPC);exit;
        // TODO debug
        if ($_GPC['debug']) {
            $_W['fans']['from_user'] = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
            $_W['fans']['nickname'] = 'jieqiang';
        }

        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
        $id = intval($_GPC['id']);
        $op = intval($_GPC['op']);


        $openid = $_W['fans']['from_user'];
        $pd = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE from_user = :from_user and state =:state and pid = :pid", array(':from_user' => $openid, ':state' => '0', ':pid' => $id));
        if ($op == 0) {
            if (!empty($pd)) {
                message("抱歉，你已经提交过申请,请耐心等待!", referer(), "error");
            }
        }
        $settings = $this->module['config'];
        if ($settings['share_qzfx'] == 1) {
            $share = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_share') . " WHERE uinacid = :uinacid and pid = :pid and share_from =:share_from ", array(':uinacid' => $_W['uniacid'], ':pid' => $id, ':share_from' => $_W['fans']['from_user']));
            if (empty($share)) {
                message("请先分享朋友圈!", $this->createMobileUrl('list'), "error");
            }
        }
        $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $id));
        if (empty($project)) {
            message("抱歉，该产品不存在!", referer(), "error");
        }
        if ($project['status'] != 3) {
            message("抱歉，该产品尚未开始!", referer(), "error");
        }
        if (time() <= $project['starttime']) {
            message("抱歉，该产品尚未开始!", referer(), "error");
        } elseif (time() > $project['starttime'] + $project['deal_days'] * 86400) {
            message("抱歉，该产品已经结束!", referer(), "error");
        }
        $my = array();
        $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $id));
        $my = iunserializer($item['wtname']);
        $returnurl = $this->createMobileUrl("confirm", array("id" => $id, "item_id" => $item_id));
        $dispatch = pdo_fetchall("select id,dispatchname,dispatchtype,firstprice,firstweight,secondprice,secondweight from " . tablename("sen_appfreeitem_dispatch") . " WHERE weid = {$_W['uniacid']} order by displayorder desc");
        foreach ($dispatch as & $d) {
            $weight = 0;
            $weight = $item['weight'];
            $price = 0;
            if ($weight <= $d['firstweight']) {
                $price = $d['firstprice'];
            } else {
                $price = $d['firstprice'];
                $secondweight = $weight - $d['firstweight'];
                if ($secondweight % $d['secondweight'] == 0) {
                    $price += (int)($secondweight / $d['secondweight']) * $d['secondprice'];
                } else {
                    $price += (int)($secondweight / $d['secondweight'] + 1) * $d['secondprice'];
                }
            }
            $d['price'] = $price;
        }
        unset($d);
        if (checksubmit('submit')) {
            $address = pdo_fetch("SELECT * FROM " . tablename('mc_member_address') . " WHERE id = :id", array(':id' => intval($_GPC['address'])));
            if (empty($address)) {
                message('抱歉，请您填写收货地址！', $this->createMobileUrl('address', array('from' => 'confirm', 'returnurl' => urlencode($returnurl))), 'error');
            }
            $item_price = $item['price'];
            // 邮费
            $freight = $item['freight'];

            $dispatchid = intval($_GPC['dispatch']);
            $dispatchprice = 0;
            foreach ($dispatch as $d) {
                if ($d['id'] == $dispatchid) {
                    $dispatchprice = $d['price'];
                    $sendtype = $d['dispatchtype'];
                }
            }

            $state;
            if ($op == 0) {
                $state = 0;
            } elseif ($op == 1) {
                $state = 1;
            }
            $ordersn = date('md') . random(4, 1);
            $data = array('weid' => $_W['uniacid'], 'from_user' => $_W['fans']['from_user'], 'ordersn' => $ordersn, 'price' => $item_price + $dispatchprice + $freight, 'freight' => $freight, 'dispatchprice' => $dispatchprice, 'item_price' => $item_price, 'status' => 0, 'state' => $state, 'sendtype' => intval($sendtype), 'dispatch' => 2, 'return_type' => 2, 'Answer' => iserializer($_GPC['Answer']), 'remark' => $_GPC['remark'], 'addressid' => $address['id'], 'pid' => $id, 'item_id' => $item_id, 'createtime' => TIMESTAMP,);
            pdo_insert('sen_appfreeitem_order', $data);
            $orderid = pdo_insertid();
            if ($op == 0) {
                $order = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE ordersn = '$ordersn'");
                $pid = $order['pid'];
                $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = '{$pid}'");
                $address = pdo_fetch("SELECT * FROM " . tablename('mc_member_address') . " WHERE id = :id", array(':id' => $order['addressid']));
                $settings = $this->module['config'];
                if (!empty($settings['kfid']) && !empty($settings['k_templateid'])) {
                    $kfirst = empty($settings['kfirst']) ? '您有一个新的申请试用订单' : $settings['kfirst'];
                    $kfoot = empty($settings['kfoot']) ? '请及时处理，点击可查看详情' : $settings['kfoot'];
                    $kurl = '';
                    $kdata = array('first' => array('value' => $kfirst, 'color' => '#ff510'), 'keyword1' => array('value' => $ordersn, 'color' => '#ff510'), 'keyword2' => array('value' => $project['title'], 'color' => '#ff510'), 'keyword3' => array('value' => $order['price'] . '元', 'color' => '#ff510'), 'keyword4' => array('value' => $address['username'], 'color' => '#ff510'), 'keyword5' => array('value' => '申请试用', 'color' => '#ff510'), 'remark' => array('value' => $kfoot, 'color' => '#ff510'),);
                    $acc = WeAccount::create();
                    $acc->sendTplNotice($settings['kfid'], $settings['k_templateid'], $kdata, $kurl, $topcolor = '#FF683F');
                    $kfirst2 = empty($settings['kfirst']) ? '您的试用订单申请成功' : $settings['kfirst'];
                    $kfoot2 = empty($settings['kfoot']) ? '请耐心等待处理，点击可查看详情' : $settings['kfoot'];
                    $murl = $_W['siteroot'] . 'app' . str_replace('./', '/', $this->createMobileUrl('myorder', array('op' => 'detail', 'orderid' => $order['id'])));
                    $kdata2 = array('first' => array('value' => $kfirst2, 'color' => '#ff510'), 'keyword1' => array('value' => $ordersn, 'color' => '#ff510'), 'keyword2' => array('value' => $project['title'], 'color' => '#ff510'), 'keyword3' => array('value' => $order['price'] . '元', 'color' => '#ff510'), 'keyword4' => array('value' => $address['username'], 'color' => '#ff510'), 'keyword5' => array('value' => '申请试用', 'color' => '#ff510'), 'remark' => array('value' => $kfoot2, 'color' => '#ff510'),);
                    $acc->sendTplNotice($openid, $settings['k_templateid'], $kdata2, $murl, $topcolor = '#FF683F');
                }
                message('申请成功,现在跳转到审核页面...', $this->createMobileUrl('myorder'), 'success');
            } elseif ($op == 1) {
                message('提交订单成功,现在跳转到付款页面...', $this->createMobileUrl('pay', array('orderid' => $orderid)), 'success');
            }
        }
        $profile = fans_search($_W['fans']['from_user'], array('resideprovince', 'residecity', 'residedist', 'address', 'nickname', 'mobile'));
        $row = pdo_fetch("SELECT * FROM " . tablename('mc_member_address') . " WHERE isdefault = 1 and uid = :uid limit 1", array(':uid' => $_W['member']['uid']));
        $carttotal = $this->getCartTotal();
        if ($op == 0) {
            $title = "提交申请";
        } elseif ($op == 1) {
            $title = "结算";
        }
        include $this->template('confirm');
    }

    // 设置属性 收藏，点赞，评论
    public function doMobileSetReportProperty()
    {
        global $_GPC, $_W;
        // TODO debug
        if ($_GPC['debug']) {
            $_W['openid'] = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }

        // var_dump($_W);
        $id = intval($_GPC['id']);
        $pid = intval($_GPC['pid']);
        $type = $_GPC['type'];
        $data = intval($_GPC['data']);
        if (in_array($type, array('1', '2'))) {
            // 之前有记录
            if ($data) {
                pdo_update("sen_appfreeitem_operate", array("is_cancel" => 1), array("report_id" => $id, "weid" => $_W['uniacid'], "type" => $type, "from_user" => $_W['openid']));

                if ($type == 1) {
//                    pdo_update("sen_appfreeitem_report", array("collect_num" => "collect_num  "-1), array("id" => $id, "weid" => $_W['uniacid']));
                    pdo_run("UPDATE `ims_sen_appfreeitem_report`
                                SET `collect_num` = collect_num -1
                                WHERE
                                    `id` = '{$id}'
                                AND `weid` = '{$_W['uniacid']}';");
                } else {
//                    pdo_update("sen_appfreeitem_report", array("zan_num" => "zan_num  "-1), array("id" => $id, "weid" => $_W['uniacid']));
                    pdo_run("UPDATE `ims_sen_appfreeitem_report`
                                SET `zan_num` = zan_num -1
                                WHERE
                                    `id` = '{$id}'
                                AND `weid` = '{$_W['uniacid']}';");
                }

                $data = 0;
            } else {
                pdo_insert('sen_appfreeitem_operate', array("is_cancel" => 0, "report_id" => $id, "pid" => $pid, "weid" => $_W['uniacid'], "type" => $type, "from_user" => $_W['openid'], 'createtime' => time()));

                if ($type == 1) {
//                    pdo_update("sen_appfreeitem_report", array("collect_num" => "collect_num  "+1 ), array("id" => $id, "weid" => $_W['uniacid']));
                    pdo_run("UPDATE `ims_sen_appfreeitem_report`
                                SET `collect_num` = collect_num +1
                                WHERE
                                    `id` = '{$id}'
                                AND `weid` = '{$_W['uniacid']}';");
                } else {
//                    pdo_update("sen_appfreeitem_report", array("zan_num" => "zan_num  "+1), array("id" => $id, "weid" => $_W['uniacid']));
                    pdo_run("UPDATE `ims_sen_appfreeitem_report`
                                SET `zan_num` = zan_num +1
                                WHERE
                                    `id` = '{$id}'
                                AND `weid` = '{$_W['uniacid']}';");
                }
                $data = 1;
            }
            die(json_encode(array("result" => 1, "data" => $data)));
        }

        die(json_encode(array("result" => 0)));
    }


    // 发布报告
    public function doMobileBaogao()
    {
        global $_W, $_GPC;
        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
        // TODO debug
        if ($_GPC['debug']) {
            $_W['fans']['from_user'] = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }

        $id = intval($_GPC['orderid']);
        $openid = $_W['fans']['from_user'];
        // 报告详情 report_id
        if (intval($_GPC['report_id'])) {
            $report_id = intval($_GPC['report_id']);

            // 回复
            if ($_GPC['content']) {
                /*$key = '小姐|吸毒|黄色';
                $item = pdo_fetch("SELECT sensitive_words FROM " . tablename('sen_appfreeitem_rule') . " WHERE wid = :wid", array(':wid' => $_W['uniacid']));*/
                $key = pdo_getcolumn('sen_appfreeitem_rule', array('wid' => $_W['uniacid']), 'sensitive_words');
                $res = $this->check_work($key, $_GPC['content']);
                if ($res) {
                    die(json_encode(array("result" => 1, 'msg' => '包含敏感词：' . $res)));
                } else {
                    $data = array();
                    $data['weid'] = $_W['uniacid'];
                    $data['from_user'] = $openid;
                    $data['oid'] = $id;
                    $data['pid'] = $_GPC['pid'];
                    $data['content'] = $_GPC['content'];
                    // $data['tijiaotime'] = date('y-m-d h:i:s', time());
                    $data['tijiaotime'] = date('Y-m-d H:i:s', time());
                    $data['parent_id'] = $report_id;

                    pdo_insert('sen_appfreeitem_report', $data);
                    pdo_run("UPDATE `ims_sen_appfreeitem_report`
                                SET `reply_num` = reply_num +1
                                WHERE
                                    `id` = '{$report_id}'
                                AND `weid` = '{$_W['uniacid']}';");

                    die(json_encode(array("result" => 1, 'msg' => '回复成功')));
                }
                exit;
            }
            /*$item = pdo_fetch("SELECT r.*,o.ordersn,p.title,p.id as project_id,m.nickname,m.avatar FROM ims_sen_appfreeitem_report as r,ims_sen_appfreeitem_order as o,ims_sen_appfreeitem_project as p,ims_mc_members as m where r.id =" . $report_id . " and r.oid=o.id and r.pid=p.id and r.from_user=m.uid ");*/

            $item = pdo_fetch("SELECT r.*,p.title,p.id as project_id FROM ims_sen_appfreeitem_report as r ,ims_sen_appfreeitem_project as p where r.id =" . $report_id . " and  r.pid=p.id AND parent_id=0 ");

            if (empty($item)) {
                message("抱歉，测评不存在!", referer(), "error");
            }
            $item['images'] = iunserializer($item['images']);

//            var_dump($item['images']);exit;

            // 判断是否收藏和点赞
            $operates = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_operate') . " WHERE weid = :weid and report_id = :report_id and from_user = :from_user and is_cancel = 0 ", array(':weid' => $_W['uniacid'], ':report_id' => $report_id, ':from_user' => $openid));
            // 类型：1 collect收藏  2 zan 点赞
            $item['collect'] = $item['zan'] = 0;
            foreach ($operates as $operate) {
                if ($operate['type'] == 1) {
                    $item['collect'] = 1;
                    break;
                }
            }
            foreach ($operates as $operate) {
                if ($operate['type'] == 2) {
                    $item['zan'] = 1;
                    break;
                }
            }

            $sql = "SELECT
                        r.*, m.avatar,m.nickname
                    FROM
                        ims_sen_appfreeitem_report AS r,
                        ims_mc_members AS m,
                        ims_mc_mapping_fans f
                    WHERE
                        (r.is_display = 1 OR r.from_user= '{$_W['fans']['from_user']}')
                    AND r.parent_id > 0
                    AND r.from_user = f.openid
                    AND f.uid = m.uid
                    AND r.parent_id=$report_id
                    ORDER BY
                        r.id DESC";
//            $lists = pdo_fetchall($sql);
            $reply_list = pdo_fetchall($sql);

            $title = "测评详情";
            include $this->template('baogao_detail');
            exit;
        }

        $order = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id", array(':id' => $id));
        if (empty($order)) {
            message("抱歉，该申请不存在!", referer(), "error");
        }
        if ($order['status'] < 4) {
            message("抱歉，该申请还不能提交报告!", referer(), "error");
        }
        $pd = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_report') . " WHERE from_user = :from_user and oid = :oid", array(':from_user' => $openid, ':oid' => $id));
        // $uid = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = '{$_W['uniacid']}' and openid = '$openid'");
        if ($pd['id'] > 0) {
            message("抱歉，该申请您已经提交过报告了!", referer(), "error");
        }
        unset($d);

        // 上传
        if (checksubmit('submit')) {
            $data = array();
            $data['weid'] = $_W['uniacid'];
            // $data['from_user'] = $uid['uid'];
            $data['from_user'] = $openid;
            $data['rtitle'] = $_GPC['rtitle'];
            $data['oid'] = $id;
            $data['pid'] = $order['pid'];
            $data['content'] = $_GPC['content'];

            // $key = '小姐|吸毒|黄色';
            $key = pdo_getcolumn('sen_appfreeitem_rule', array('wid' => $_W['uniacid']), 'sensitive_words');
            $res = $this->check_work($key, $_GPC['content']);
            if ($res) {
                // die(json_encode(array("result" => 1, 'msg' => '包含敏感词'.$res)));
                message('包含敏感词：' . $res, referer(), "error");
            }

            // $data['tijiaotime'] = date('y-m-d h:i:s', time());
            $data['tijiaotime'] = date('Y-m-d H:i:s', time());

            if (empty($data['rtitle'])) {
                message("请填写标题!", referer(), "error");
            }

            /*if (!empty($_GPC['thumb'])) {
                foreach ($_GPC['thumb'] as $thumb) {
                    $th[] = save_media(tomedia($thumb));
                }
                $data['images'] = iserializer($th);
            }*/

//            var_dump($_REQUEST,$_GPC['image']);exit;
            if (!empty($_GPC['image'])) {
                // $_GPC['image'] = explode(',',$_GPC['image']);
                foreach ($_GPC['image'] as $thumb) {
                    $th[] = save_media(tomedia($thumb));
                }
                $data['images'] = iserializer($th);
            }

//            var_dump($_REQUEST,$data);exit;
            pdo_insert('sen_appfreeitem_report', $data);
            // pdo_update('sen_appfreeitem_order', array('status' => 5));
            pdo_update('sen_appfreeitem_order', array('status' => 5), array('id' => $id));
            message('报告提交成功,返回首页...', $this->createMobileUrl('list'), 'success');
        }
        $carttotal = $this->getCartTotal();
        $pagetitle = $title = "报告提交";

        include $this->template('baogao');
    }

    public function doMobileMyCart()
    {
        global $_W, $_GPC;
        if (empty($_W['openid'])) {
            exit(json_encode(array('status' => 0)));
        }
        $op = $_GPC['op'];
        if ($op == 'add') {
            $pid = intval($_GPC['pid']);
            $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $pid));
            if (empty($project)) {
                exit(json_encode(array('status' => 3, 'info' => "抱歉，该产品不存在或是已经被删除！")));
            }
            $row = pdo_fetch("SELECT id FROM " . tablename('sen_appfreeitem_cart') . " WHERE from_user = :from_user AND weid = '{$_W['uniacid']}' AND projectid = :pid", array(':from_user' => $_W['fans']['from_user'], ':pid' => $pid));
            if ($row == false) {
                $data = array('weid' => $_W['uniacid'], 'projectid' => $pid, 'from_user' => $_W['fans']['from_user'],);
                pdo_insert('sen_appfreeitem_cart', $data);
                $type = 'add';
                die(json_encode(array('status' => 1)));
            } else {
                pdo_delete('sen_appfreeitem_cart', array('id' => $row['id']));
                $type = 'del';
                die(json_encode(array('status' => 2)));
            }
        } else if ($op == 'clear') {
            pdo_delete('sen_appfreeitem_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid']));
            die(json_encode(array("result" => 1)));
        } else if ($op == 'remove') {
            $id = intval($_GPC['id']);
            pdo_delete('sen_appfreeitem_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid'], 'id' => $id));
            die(json_encode(array("result" => 1, "cartid" => $id)));
        } else {
            $list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_cart') . " WHERE  weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'");
            include $this->template('cart');
        }
    }

    // 支付
    public function doMobilePay()
    {
        global $_W, $_GPC;
        $this->checkAuthSession();
        $orderid = intval($_GPC['orderid']);
        if ($_GPC['type'] == 'ws') {
            $order = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order_ws') . " WHERE id = :id", array(':id' => $orderid));
            if ($order['status'] != '0') {
                message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('myorder'), 'error');
            }
            $params['tid'] = 'ws-' . $orderid;
            $params['user'] = $_W['fans']['from_user'];
            $params['fee'] = $order['price'];
            $params['title'] = $_W['account']['name'];
            $params['ordersn'] = $order['ordersn'];
            $params['virtual'] = true;
        } else {
            $order = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id", array(':id' => $orderid));
            /*if ($order['status'] != '0') {
                message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('myorder'), 'error');
            }*/
            $params['tid'] = $orderid;
            $params['user'] = $_W['fans']['from_user'];
            $params['fee'] = $order['price'];
            $params['title'] = $_W['account']['name'];
            $params['ordersn'] = $order['ordersn'];
            $params['virtual'] = $order['return_type'] == 2 ? true : false;
        }

        $res = $this->pay($params);
        var_dump($params, $res);
        exit;

        include $this->template('pay');
    }

    // 前台联系我们
    public function doMobileContactUs()
    {
        global $_W;
        $cfg = $this->module['config'];
        include $this->template('contactus');
    }

    // 我的个人中心
    public function doMobileMyCenter()
    {
        global $_W, $_GPC;
        // TODO debug
        if ($_GPC['debug']) {
            $_W['fans']['from_user'] = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }
        $this->checkAuthSession();
        $type = $_GPC['type'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $where = " weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'";

        if ($type == 1) {
            // 收藏
            /*$list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_operate') . " WHERE type=2 ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');*/
            $list = pdo_fetchall("SELECT o.*,r.content,r.id as report_id,zan_num,collect_num,reply_num  FROM " . tablename('sen_appfreeitem_operate') . " AS o, " . tablename('sen_appfreeitem_report') . " AS r WHERE  o.weid = '{$_W['uniacid']}' AND o.from_user = '{$_W['fans']['from_user']}' AND type=1 AND o.report_id=r.id  AND is_cancel=0 ORDER BY o.id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'o.id');
            foreach ($list as $k => $v) {
                // 类型：1 collect收藏  2 zan 点赞
                $list[$k]['collect'] = 1;
            }
            // var_dump($list);exit;
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_operate') . " WHERE $where AND type=1   AND is_cancel=0 ");
            $pager = pagination($total, $pindex, $psize);
            $title = "我的收藏";
//             var_dump($project,$pager,$list,$total);exit;
            include $this->template('center_collect');

        } elseif ($type == 2) {
            // 点赞
            /*$list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_operate') . " WHERE type=2 ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');*/
            $list = pdo_fetchall("SELECT o.*,r.content,r.id as report_id,zan_num,collect_num,reply_num FROM " . tablename('sen_appfreeitem_operate') . " AS o, " . tablename('sen_appfreeitem_report') . " AS r WHERE  o.weid = '{$_W['uniacid']}' AND o.from_user = '{$_W['fans']['from_user']}' AND type=2 AND o.report_id=r.id  AND is_cancel=0 ORDER BY o.id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'o.id');

            foreach ($list as $k => $v) {
                // 类型：1 collect收藏  2 zan 点赞
                $list[$k]['zan'] = 1;
            }


            // var_dump($list);exit;
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_operate') . " WHERE $where AND type=2   AND is_cancel=0 ");
            // var_dump($list,$total);exit;
            $pager = pagination($total, $pindex, $psize);
            $title = "我的点赞";
//             var_dump($project,$pager,$list,$total);exit;
            include $this->template('center_zan');

        } elseif ($type == 3) {
            // 评论
            $list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_report') . " WHERE $where ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');
            // var_dump($list);exit;
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_report') . " WHERE $where");
            $pager = pagination($total, $pindex, $psize);
            $title = "我的评论";
//             var_dump($project,$pager,$list,$total);exit;
            include $this->template('center_reply');
        }

    }

    // 我的订单
    public function doMobileMyOrder()
    {
        global $_W, $_GPC;
        // $_W['session_id'] = "{$_W['uniacid']}-" . random(20) ;

        // TODO debug
        if ($_GPC['debug']) {
            $_W['fans']['from_user'] = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }else{
            $this->checkAuthSession();
        }
//        var_dump($_GPC['debug']);exit;


        $carttotal = $this->getCartTotal();
//        var_dump('$carttotal==',$carttotal);exit;
        $op = $_GPC['op'];

        if ($op == 'confirm') {
            $orderid = intval($_GPC['orderid']);
            $state = intval($_GPC['state']);
            $order = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id AND from_user = :from_user", array(':id' => $orderid, ':from_user' => $_W['fans']['from_user']));
            if (empty($order)) {
                message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
            }
            // $shdata = date('y-m-d h:i:s', time());
            $shdata = date('Y-m-d H:i:s', time());
            if ($state == 0) {
                pdo_update('sen_appfreeitem_order', array('status' => 4, 'shouhuodata' => $shdata), array('id' => $orderid, 'from_user' => $_W['fans']['from_user']));
            } elseif ($state == 1) {
                pdo_update('sen_appfreeitem_order', array('status' => 5, 'shouhuodata' => $shdata), array('id' => $orderid, 'from_user' => $_W['fans']['from_user']));
            }
            message('确认收货完成！', $this->createMobileUrl('myorder'), 'success');
        } else if ($op == 'detail') {
            $orderid = intval($_GPC['orderid']);
            $pid = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id", array(':id' => $orderid));
            $my = array();
            $items = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $pid['pid']));
            $my = iunserializer($items['wtname']);
            $itemss = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id", array(':id' => $orderid));
            $itemss['Answer'] = iunserializer($itemss['Answer']);
            $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}' and id='{$orderid}' limit 1");
            if (empty($item)) {
                message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
            }
            $address = pdo_fetch("select * from " . tablename('mc_member_address') . " where id=:id limit 1", array(":id" => $item['addressid']));
            $dispatch = pdo_fetch("select id,dispatchname from " . tablename('sen_appfreeitem_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
            include $this->template('order_detail');
        } else {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $status = intval($_GPC['status']);
            // $state = intval($_GPC['state']);

            $where = " weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'";
            /*if ($status == 2) {
                $where .= " and ( status=1 or status=2 )";
            } else {
                $where .= " and status=$status";
            }*/

            // 9：全部  0：试用  1：购买
            $state = $_GPC['state'];
            // var_dump($state);exit;
            if ($state == 1) {
                $where .= " and state=1";
            } else {
                if ($state === '0') {
                    $where .= " and state=0";
                } else {
                    $state = 9;
                }
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE $where ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_order') . " WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'");
            $pager = pagination($total, $pindex, $psize);

            $res_json = array('code'=>200,'msg'=>'请求成功','data'=>array('total'=>$total,'pindex'=>$pindex,'psize'=>$psize,'list'=>$list));
            /*$res_json_order = $this->echojson(200,'请求成功',array('total'=>$total,'pindex'=>$pindex,'psize'=>$psize,'list'=>$list));
            echo($res_json_order);exit;*/
            $pagetitle = "申请状态";
            include $this->template('order');
        }
    }

    // 前台地址
    public function doMobileAddress()
    {
        global $_W, $_GPC;
        $from = $_GPC['from'];
        $returnurl = urldecode($_GPC['returnurl']);
        $this->checkAuthSession();
        $carttotal = $this->getCartTotal();
        $operation = $_GPC['op'];
        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $data = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'username' => $_GPC['username'], 'mobile' => $_GPC['mobile'], 'province' => $_GPC['province'], 'city' => $_GPC['city'], 'district' => $_GPC['district'], 'address' => $_GPC['address'],);
            if (empty($_GPC['username']) || empty($_GPC['mobile']) || empty($_GPC['address'])) {
                message('请输完善您的资料！');
            }
            if (!empty($id)) {
                unset($data['uniacid']);
                unset($data['uid']);
                pdo_update('mc_member_address', $data, array('id' => $id));
                message($id, '', 'ajax');
            } else {
                pdo_update('mc_member_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
                $data['isdefault'] = 1;
                pdo_insert('mc_member_address', $data);
                $id = pdo_insertid();
                if (!empty($id)) {
                    message($id, '', 'ajax');
                } else {
                    message(0, '', 'ajax');
                }
            }
        } elseif ($operation == 'default') {
            $id = intval($_GPC['id']);
            $address = pdo_fetch("select isdefault from " . tablename('mc_member_address') . " where id='{$id}' and uniacid='{$_W['uniacid']}' and uid='{$_W['member']['uid']}' limit 1 ");
            if (!empty($address) && empty($address['isdefault'])) {
                pdo_update('mc_member_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
                pdo_update('mc_member_address', array('isdefault' => 1), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'id' => $id));
            }
            message(1, '', 'ajax');
        } elseif ($operation == 'detail') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id, username, mobile, province, city, district, address FROM " . tablename('mc_member_address') . " WHERE id = :id", array(':id' => $id));
            message($row, '', 'ajax');
        } elseif ($operation == 'remove') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $address = pdo_fetch("select isdefault from " . tablename('mc_member_address') . " where id='{$id}' and uniacid='{$_W['uniacid']}' and uid='{$_W['member']['uid']}' limit 1 ");
                if (!empty($address)) {
                    pdo_delete("mc_member_address", array('id' => $id, 'uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
                    if ($address['isdefault'] == 1) {
                        $maxid = pdo_fetchcolumn("select max(id) as maxid from " . tablename('mc_member_address') . " where uniaicd='{$_W['uniacid']}' and uid='{$_W['member']['uid']}' limit 1 ");
                        if (!empty($maxid)) {
                            pdo_update('mc_member_address', array('isdefault' => 1), array('id' => $maxid, 'uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
                            die(json_encode(array("result" => 1, "maxid" => $maxid)));
                        }
                    }
                }
            }
            die(json_encode(array("result" => 1, "maxid" => 0)));
        } else {
            $profile = fans_search($_W['fans']['from_user'], array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
            $address = pdo_fetchall("SELECT * FROM " . tablename('mc_member_address') . " WHERE uid = :uid", array(':uid' => $_W['member']['uid']));
            $carttotal = $this->getCartTotal();
            $title = $pagetitle = "信息维护";
            include $this->template('address');
        }
    }



    /*后台操作*/
    // 后台
    public function doWebFule()
    {
        global $_GPC, $_W;
        if (checksubmit()) {
            $data = array('wid' => $_W['uniacid'], 'description' => htmlspecialchars_decode($_GPC['description']),);
            pdo_insert('sen_appfreeitem_fule', $data);
            message('操作成功！', referer(), 'success');
        }
    }

    // 后台报告
    public function doWebReport()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        if (!function_exists('filter_url')) {
            function filter_url($params)
            {
                global $_W;
                if (empty($params)) {
                    return '';
                }
                $query_arr = array();
                $parse = parse_url($_W['siteurl']);
                if (!empty($parse['query'])) {
                    $query = $parse['query'];
                    parse_str($query, $query_arr);
                }
                $params = explode(',', $params);
                foreach ($params as $val) {
                    if (!empty($val)) {
                        $data = explode(':', $val);
                        $query_arr[$data[0]] = trim($data[1]);
                    }
                }
                $query_arr['page'] = 1;
                $query = http_build_query($query_arr);
                return './index.php?' . $query;
            }
        }
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            /*$new = pdo_fetch("SELECT r.*,o.ordersn,p.title,m.nickname,m.avatar FROM ims_mc_mapping_fans f, ims_sen_appfreeitem_report as r,ims_sen_appfreeitem_order as o,ims_sen_appfreeitem_project as p,ims_mc_members as m where r.id =" . $id . " and r.oid=o.id and r.pid=p.id AND r.from_user = f.openid AND f.uid = m.uid ");*/
            $new = pdo_fetch("SELECT r.*,p.title,m.nickname,m.avatar FROM ims_mc_mapping_fans f, ims_sen_appfreeitem_report as r,ims_sen_appfreeitem_order as o,ims_sen_appfreeitem_project as p,ims_mc_members as m where r.id =" . $id . "  and r.pid=p.id AND r.from_user = f.openid AND f.uid = m.uid ");
            if (empty($new)) {
                $new = array('is_display' => 1,);
            }
            $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_report') . " WHERE weid = :weid and id = :id", array(':weid' => $_W['uniacid'], ':id' => $id));
            if (empty($item)) {
                message("抱歉，测评不存在!", referer(), "error");
            }
            $item['images'] = iunserializer($item['images']);
            // var_dump($item);exit;
            if (checksubmit()) {
                $data = array('content' => $_GPC['content'], 'is_display' => intval($_GPC['is_display']),);
                if (!empty($new['id'])) {
                    pdo_update('sen_appfreeitem_report', $data, array('id' => $id));
                } else {
                    pdo_insert('sen_appfreeitem_report', $data);
                }
                message('编辑报告成功', $this->createWebUrl('report', array('op' => 'display')), 'success');
            }
            $categorys = pdo_fetchall('SELECT * FROM ' . tablename('sen_appfreeitem_report_category') . ' WHERE uniacid = :uniacid ORDER BY displayorder DESC', array(':uniacid' => $_W['uniacid']));
        } elseif ($operation == 'display') {
            $condition = ' WHERE 1';
            $cateid = intval($_GPC['cateid']);
            $tianjiatime = intval($_GPC['tianjiatime']);
            $title = trim($_GPC['title']);
            $params = array();
            if ($cateid > 0) {
                $condition .= ' AND cateid = :cateid';
                $params[':cateid'] = $cateid;
            }
            if ($tianjiatime > 0) {
                $tianjiatime .= ' AND tianjiatime >= :tianjiatime';
                $params[':tianjiatime'] = strtotime("-{$tianjiatime} days");
            }
            if (!empty($title)) {
                $condition .= " AND title LIKE :title";
                $params[':title'] = "%{$title}%";
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            /*$sql = "SELECT r.*,o.ordersn,p.title,m.nickname,m.avatar FROM ims_mc_mapping_fans f,ims_sen_appfreeitem_report as r,ims_sen_appfreeitem_order as o,ims_sen_appfreeitem_project as p,ims_mc_members as m " . $condition . " and r.oid=o.id and r.pid=p.id AND r.from_user = f.openid AND f.uid = m.uid ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;*/
            // $sql = "SELECT r.*,p.title,m.nickname,m.avatar FROM ims_mc_mapping_fans f,ims_sen_appfreeitem_report as r,ims_sen_appfreeitem_project as p,ims_mc_members as m " . $condition . " and  r.pid=p.id AND r.from_user = f.openid AND f.uid = m.uid ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

            $sql = "SELECT
	r.*, o.ordersn,p.title,m.nickname,
	m.avatar
FROM
	ims_sen_appfreeitem_report AS r
LEFT JOIN ims_sen_appfreeitem_order AS o ON r.oid = o.id
LEFT JOIN ims_sen_appfreeitem_project AS p ON r.pid = p.id
LEFT JOIN ims_mc_mapping_fans f ON r.from_user = f.openid
LEFT JOIN ims_mc_members AS m ON f.uid = m.uid ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
            $news = pdo_fetchall($sql, $params);
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_report') . $condition, $params);
            $pager = pagination($total, $pindex, $psize);
            $categorys = pdo_fetchall('SELECT * FROM ' . tablename('sen_appfreeitem_report_category') . ' WHERE uniacid = :uniacid ORDER BY displayorder DESC', array(':uniacid' => $_W['uniacid']), 'id');
        } elseif ($operation == 'batch_post') {
            if (checksubmit()) {
                if (!empty($_GPC['ids'])) {
                    foreach ($_GPC['ids'] as $k => $v) {
                        $data = array('title' => trim($_GPC['title'][$k]), 'displayorder' => intval($_GPC['displayorder'][$k]), 'click' => intval($_GPC['click'][$k]),);
                        pdo_update('sen_appfreeitem_report', $data, array('id' => intval($v)));
                    }
                    message('编辑新闻列表成功', referer(), 'success');
                }
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            pdo_delete('sen_appfreeitem_report', array('id' => $id));
            message('删除文章成功', referer(), 'success');
        }
        include $this->template('news');
    }

    public function doWebReport_category()
    {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'post') {
            if (checksubmit('submit')) {
                $i = 0;
                if (!empty($_GPC['title'])) {
                    foreach ($_GPC['title'] as $k => $v) {
                        $title = trim($v);
                        if (empty($title)) {
                            continue;
                        }
                        $data = array('uniacid' => $_W['uniacid'], 'title' => $title, 'displayorder' => intval($_GPC['displayorder'][$k]),);
                        pdo_insert('sen_appfreeitem_report_category', $data);
                        $i++;
                    }
                }
                message('修改文章分类成功', $this->createWebUrl('news_category', array('op' => 'display')), 'success');
            }
        } elseif ($operation == 'display') {
            if (checksubmit('submit')) {
                if (!empty($_GPC['ids'])) {
                    foreach ($_GPC['ids'] as $k => $v) {
                        $data = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title'][$k]), 'displayorder' => intval($_GPC['displayorder'][$k]));
                        pdo_update('sen_appfreeitem_report_category', $data, array('id' => intval($v)));
                    }
                    message('修改新闻分类成功', referer(), 'success');
                }
            }
            $data = pdo_fetchall('SELECT * FROM ' . tablename('sen_appfreeitem_report_category') . ' WHERE uniacid = :uniacid ORDER BY displayorder DESC', array(':uniacid' => $_W['uniacid']));
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            pdo_delete('sen_appfreeitem_report_category', array('id' => $id));
            pdo_delete('sen_appfreeitem_report', array('cateid' => $id));
            message('删除分类成功', referer(), 'success');
        }
        include $this->template('news_category');
    }

    // 规则填写
    public function doWebRule()
    {
        global $_GPC, $_W;
        $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_rule') . " WHERE wid = :wid", array(':wid' => $_W['uniacid']));
        if (checksubmit()) {
            $data = array('wid' => $_W['uniacid'], 'content' => htmlspecialchars_decode($_GPC['content']), 'sensitive_words' => htmlspecialchars_decode($_GPC['sensitive_words']), 'add_time' => time());
            if (empty($item['content'])) {
                pdo_insert('sen_appfreeitem_rule', $data);
            } else {
                pdo_update('sen_appfreeitem_rule', $data, array('wid' => $_W['uniacid']));
            }
            message('操作成功！', referer(), 'success');
        }
        include $this->template('rule');
    }

    public function doWebWcorder()
    {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $project_id = intval($_GPC['project_id']);
        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $sql = "select * from " . tablename('sen_appfreeitem_order_ws') . " WHERE weid='{$_W['uniacid']}' AND pid='{$project_id}' AND status=1 LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql, $paras);
            $paytype = array('0' => array('css' => 'default', 'name' => '未支付'), '1' => array('css' => 'danger', 'name' => '余额支付'), '2' => array('css' => 'info', 'name' => '在线支付'), '3' => array('css' => 'warning', 'name' => '货到付款'));
            $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('sen_appfreeitem_order_ws') . " WHERE weid='{$_W['uniacid']}' AND pid='{$project_id}' AND status=1");
            $pager = pagination($total, $pindex, $psize);
            include $this->template('wcorder');
        }
    }

    // 申请管理  订单
    public function doWebOrder()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $projects = pdo_fetchall("SELECT id AS id,title AS name FROM " . tablename('sen_appfreeitem_project') . " WHERE weid='{$_W['uniacid']}' ORDER BY id DESC");
        if (!empty($projects)) {
            $pitems = '';
            foreach ($projects as $key => $value) {
                $pitems[$value['id']] = pdo_fetchall("SELECT id AS id,price AS name FROM " . tablename('sen_appfreeitem_project_item') . " WHERE weid='{$_W['uniacid']}' AND pid='{$value['id']}' ORDER BY id DESC");
            }
        }
        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $status = $_GPC['status'];
            $sendtype = !isset($_GPC['sendtype']) ? 0 : $_GPC['sendtype'];
            $condition = " o.weid = :weid";
            $paras = array(':weid' => $_W['uniacid']);
            if (empty($starttime) || empty($endtime)) {
                $starttime = strtotime('-1 month');
                $endtime = time();
            }
            if ($_GPC['project']['parentid'] != 0) {
                $condition .= " AND o.pid=:pid ";
                $paras[':pid'] = intval($_GPC['project']['parentid']);
                $pid = intval($_GPC['project']['parentid']);
            }
            if ($_GPC['project']['parentid'] != 0 && $_GPC['project']['childid'] != 0) {
                $condition .= " AND o.item_id=:iid ";
                $paras[':iid'] = intval($_GPC['project']['childid']);
                $iid = intval($_GPC['project']['childid']);
            }
            if (!empty($_GPC['time'])) {
                $starttime = strtotime($_GPC['time']['start']);
                $endtime = strtotime($_GPC['time']['end']) + 86399;
                $condition .= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
                $paras[':starttime'] = $starttime;
                $paras[':endtime'] = $endtime;
            }
            if (!empty($_GPC['paytype'])) {
                $condition .= " AND o.paytype = '{$_GPC['paytype']}'";
            } elseif ($_GPC['paytype'] === '0') {
                $condition .= " AND o.paytype = '{$_GPC['paytype']}'";
            }
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND o.ordersn LIKE '%{$_GPC['keyword']}%'";
            }
            if (!empty($_GPC['member'])) {
                $condition .= " AND (a.username LIKE '%{$_GPC['member']}%' or a.mobile LIKE '%{$_GPC['member']}%')";
            }
            if ($status != '') {
                $condition .= " AND o.status = '" . intval($status) . "'";
            }
            if (!empty($sendtype)) {
                $condition .= " AND o.sendtype = '" . intval($sendtype) . "' AND status != '3'";
            }
            if ($_GPC['out_put'] == 'output') {
                $sql = "select o.* , a.username,a.mobile from " . tablename('sen_appfreeitem_order') . " o" . " left join " . tablename('mc_member_address') . " a on o.addressid = a.id " . " where $condition ORDER BY o.status ASC, o.createtime DESC ";
                $list = pdo_fetchall($sql, $paras);
                $paytype = array('0' => array('css' => 'default', 'name' => '未支付'), '1' => array('css' => 'danger', 'name' => '余额支付'), '2' => array('css' => 'info', 'name' => '在线支付'), '3' => array('css' => 'warning', 'name' => '货到付款'));
                $orderstatus = array('-1' => array('css' => 'default', 'name' => '已取消'), '0' => array('css' => 'danger', 'name' => '待审核'), '1' => array('css' => 'black', 'name' => '未通过'), '2' => array('css' => 'info', 'name' => '待发货'), '3' => array('css' => 'warning', 'name' => '待收货'), '4' => array('css' => 'green', 'name' => '已收货'), '5' => array('css' => 'success', 'name' => '已完成'));
                $start = array('0' => array('css' => 'danger', 'name' => '申请试用'), '1' => array('css' => 'info', 'name' => '直接购买'));
                foreach ($list as & $value) {
                    $s = $value['status'];
                    $value['statuscss'] = $orderstatus[$value['status']]['css'];
                    $value['status'] = $orderstatus[$value['status']]['name'];
                    if ($s < 1) {
                        $value['css'] = $paytype[$s]['css'];
                        $value['paytype'] = $paytype[$s]['name'];
                        continue;
                    }
                    $st = $value['start'];
                    $value['startcss'] = $start[$value['start']]['css'];
                    $value['start'] = $start[$value['start']]['name'];
                    if ($st < 1) {
                        $value['css'] = $paytype[$st]['css'];
                        $value['paytype'] = $paytype[$st]['name'];
                        continue;
                    }
                    $value['css'] = $paytype[$value['paytype']]['css'];
                    if ($value['paytype'] == 2) {
                        if (empty($value['transid'])) {
                            $value['paytype'] = '支付宝支付';
                        } else {
                            $value['paytype'] = '微信支付';
                        }
                    } else {
                        $value['paytype'] = $paytype[$value['paytype']]['name'];
                    }
                }
                if (!empty($list)) {
                    foreach ($list as & $row) {
                        $row['dispatch'] = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_dispatch') . " WHERE id = :id", array(':id' => $row['dispatch']));
                    }
                    unset($row);
                }
                if (!empty($list)) {
                    foreach ($list as & $row) {
                        $row['address'] = pdo_fetch("SELECT * FROM " . tablename('mc_member_address') . " WHERE id = :id", array(':id' => $row['addressid']));
                    }
                    unset($row);
                }
                $i = 0;
                foreach ($list as $key => $value) {
                    $project = $this->getproject($value['pid']);
                    $pitem = $this->getitem($value['item_id']);
                    $arr[$i]['ordersn'] = $value['ordersn'];
                    $arr[$i]['title'] = $project['title'];
                    $arr[$i]['item_price'] = $pitem['price'];
                    $arr[$i]['status'] = $value['status'];
                    $arr[$i]['username'] = $value['username'];
                    $arr[$i]['mobile'] = "'" . $value['mobile'];
                    $arr[$i]['address'] = $value['address']['province'] . '-' . $value['address']['city'] . '-' . $value['address']['distinct'] . '-' . $value['address']['address'];
                    $arr[$i]['createtime'] = "'" . date('Y-m-d H:i:s', $value['createtime']);
                    $arr[$i]['dispatchname'] = $value['dispatch']['dispatchname'];
                    $i++;
                    unset($project);
                    unset($pitem);
                }
                $this->exportexcel($arr, array('订单号', '产品名称', '支持金额', '状态', '真实姓名', '电话号码', '地址', '时间', '邮寄方式'), time());
                exit();
            }
            $sql = "select o.* , a.username,a.mobile from " . tablename('sen_appfreeitem_order') . " o" . " left join " . tablename('mc_member_address') . " a on o.addressid = a.id " . " where $condition ORDER BY o.status ASC, o.createtime DESC " . "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql, $paras);
            $paytype = array('0' => array('css' => 'default', 'name' => '未支付'), '1' => array('css' => 'danger', 'name' => '余额支付'), '2' => array('css' => 'info', 'name' => '在线支付'), '3' => array('css' => 'warning', 'name' => '货到付款'));
            $orderstatus = array('-1' => array('css' => 'default', 'name' => '已取消'), '0' => array('css' => 'danger', 'name' => '待审核'), '1' => array('css' => 'black', 'name' => '未通过'), '2' => array('css' => 'info', 'name' => '待发货'), '3' => array('css' => 'warning', 'name' => '待收货'), '4' => array('css' => 'green', 'name' => '已收货'), '5' => array('css' => 'success', 'name' => '已完成'));
            $state = array('0' => array('css' => 'danger', 'name' => '申请试用'), '1' => array('css' => 'info', 'name' => '直接购买'));
            foreach ($list as & $value) {
                $s = $value['status'];
                $value['statuscss'] = $orderstatus[$value['status']]['css'];
                $value['status'] = $orderstatus[$value['status']]['name'];
                $st = $value['state'];
                $value['statecss'] = $state[$value['state']]['css'];
                $value['state'] = $state[$value['state']]['name'];
                $value['css'] = $paytype[$value['paytype']]['css'];
                if ($value['paytype'] == 2) {
                    if (empty($value['transid'])) {
                        $value['paytype'] = '支付宝支付';
                    } else {
                        $value['paytype'] = '微信支付';
                    }
                } else {
                    $value['paytype'] = $paytype[$value['paytype']]['name'];
                }
            }
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_order') . " o " . " left join " . tablename('mc_member_address') . " a on o.addressid = a.id " . " WHERE $condition", $paras);
            $pager = pagination($total, $pindex, $psize);
            if (!empty($list)) {
                foreach ($list as & $row) {
                    $row['dispatch'] = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_dispatch') . " WHERE id = :id", array(':id' => $row['dispatch']));
                }
                unset($row);
            }
        } elseif ($operation == 'detail') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id", array(':id' => $id));
            if (empty($item)) {
                message("抱歉，订单不存在!", referer(), "error");
            }
            $my = array();
            $items = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $item['pid']));
            $my = iunserializer($items['wtname']);
            $itemss = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id", array(':id' => $id));
            $itemss['Answer'] = iunserializer($itemss['Answer']);
            if (checksubmit('confirmsend')) {
                if (!empty($_GPC['isexpress']) && empty($_GPC['expresssn'])) {
                    message('请输入快递单号！');
                }
                $item = pdo_fetch("SELECT transid FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id", array(':id' => $id));
                if (!empty($item['transid'])) {
                    $this->changeWechatSend($id, 1);
                }
                pdo_update('sen_appfreeitem_order', array('status' => 3, 'remark' => $_GPC['remark'], 'express' => $_GPC['express'], 'expresscom' => $_GPC['expresscom'], 'expresssn' => $_GPC['expresssn'],), array('id' => $id));
                message('发货操作成功！', referer(), 'success');
            }
            if (checksubmit('cancelsend')) {
                $item = pdo_fetch("SELECT transid FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id", array(':id' => $id));
                if (!empty($item['transid'])) {
                    $this->changeWechatSend($id, 0, $_GPC['cancelreson']);
                }
                pdo_update('sen_appfreeitem_order', array('status' => 1, 'remark' => $_GPC['remark'],), array('id' => $id));
                message('取消发货操作成功！', referer(), 'success');
            }
            if (checksubmit('finish')) {
                pdo_update('sen_appfreeitem_order', array('status' => 5, 'remark' => $_GPC['remark']), array('id' => $id));
                message('订单操作成功！', referer(), 'success');
            }
            if (checksubmit('cancel')) {
                pdo_update('sen_appfreeitem_order', array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
                message('取消完成订单操作成功！', referer(), 'success');
            }
            if (checksubmit('cancelpay')) {
                pdo_update('sen_appfreeitem_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
                message('取消订单付款操作成功！', referer(), 'success');
            }
            if (checksubmit('nocancelpay')) {
                pdo_update('sen_appfreeitem_order', array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
                message('申请不通过操作成功！', referer(), 'success');
            }
            if (checksubmit('querenshouhuo')) {
                pdo_update('sen_appfreeitem_order', array('status' => 4, 'remark' => $_GPC['remark']), array('id' => $id));
                message('确认收货操作成功！', referer(), 'success');
            }
            if (checksubmit('confrimpay')) {
                if ($item['state'] == 0) {
                    pdo_update('sen_appfreeitem_order', array('status' => 2, 'remark' => $_GPC['remark']), array('id' => $id));
                } elseif ($item['state'] == 1) {
                    pdo_update('sen_appfreeitem_order', array('status' => 2, 'paytype' => 2, 'remark' => $_GPC['remark']), array('id' => $id));
                }
                $order = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE id = '{$id}'");
                $pid = $order['pid'];
                $item_id = $order['item_id'];
                $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = '{$pid}'");
                $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project_item') . " WHERE id = '{$item_id}'");
                pdo_update('sen_appfreeitem_project', array('finish_price' => $project['finish_price'] + $order['item_price'], 'donenum' => $project['donenum'] + 1), array('id' => $pid));
                pdo_update('sen_appfreeitem_project_item', array('donenum' => $item['donenum'] + 1), array('id' => $item_id));
                if ($item['state'] == 0) {
                    message('确认通过操作成功！', referer(), 'success');
                } elseif ($item['state'] == 1) {
                    message('确认订单付款操作成功！', referer(), 'success');
                }
            }
            if (checksubmit('close')) {
                $item = pdo_fetch("SELECT transid FROM " . tablename('sen_appfreeitem_order') . " WHERE id = :id", array(':id' => $id));
                if (!empty($item['transid'])) {
                    $this->changeWechatSend($id, 0, $_GPC['reson']);
                }
                if ($_GPC['tuikuan'] == 1) {
                    $result = $this->sendMoney($item['from_user'], $item['price'], '失败退款');
                    if ($result['code'] != 'SUCCESS') {
                        message('退款失败，失败原因' . $result['msg'], referer(), 'error');
                    }
                }
                pdo_update('sen_appfreeitem_order', array('status' => -1, 'remark' => $_GPC['remark']), array('id' => $id));
                message('订单关闭操作成功！', referer(), 'success');
            }
            if (checksubmit('open')) {
                pdo_update('sen_appfreeitem_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
                message('开启订单操作成功！', referer(), 'success');
            }
            $dispatch = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_dispatch') . " WHERE id = :id", array(':id' => $item['dispatch']));
            if (!empty($dispatch) && !empty($dispatch['express'])) {
                $express = pdo_fetch("select * from " . tablename('sen_appfreeitem_express') . " WHERE id=:id limit 1", array(":id" => $dispatch['express']));
            }
            $item['user'] = pdo_fetch("SELECT * FROM " . tablename('mc_member_address') . " WHERE id = {$item['addressid']}");
        } elseif ($operation == 'delete') {
            $orderid = intval($_GPC['id']);
            if (pdo_delete('sen_appfreeitem_order', array('id' => $orderid))) {
                message('订单删除成功', $this->createWebUrl('order', array('op' => 'display')), 'success');
            } else {
                message('订单不存在或已被删除', $this->createWebUrl('order', array('op' => 'display')), 'error');
            }
        }
        include $this->template('order');
    }

    // 管理产品
    public function doWebProject()
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $category = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        $dispatch = pdo_fetchall("select id,dispatchname,dispatchtype,firstprice,firstweight,secondprice,secondweight from " . tablename("sen_appfreeitem_dispatch") . " WHERE weid = {$_W['uniacid']} order by displayorder desc ");


        // TODO jieqiang 品牌 分类
        $categorys_new = $this->getFullCategory(true);
        $brands_new = $this->getFullBrand(true);

        /*var_dump($brand,$categorys);
        exit;*/

        if (!empty($category)) {
            $children = '';
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $item_id = intval($_GPC['item_id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，产品不存在或是已经删除！', '', 'error');
                }
                $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $id));
                $item['wtname'] = iunserializer($item['wtname']);


                // 商品所有品牌
                $brands = explode(',', $item['brands']);
                $cates = explode(',', $item['cates']);

            }
            if (empty($category)) {
                message('抱歉，请您先添加产品分类！', $this->createWebUrl('category', array('op' => 'post')), 'error');
            }
            $step = intval($_GPC['step']) ? intval($_GPC['step']) : 1;
            if ($step == 1) {
            } elseif ($step == 2) {
                $items = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_project_item') . " WHERE weid = '{$_W['uniacid']}' AND pid = '{$id}' ORDER BY id ASC");
                if ($item_id) {
                    $item_info = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project_item') . " WHERE id = :id", array(':id' => $item_id));
                }
                if (checksubmit('submit')) {
                    if (empty($_GPC['title'])) {
                        message('产品名称必填，请返回修改');
                    }

                    $data = array('weid' => $_W['uniacid'], 'displayorder' => intval($_GPC['displayorder']), 'title' => $_GPC['title'], 'cpnumber' => intval($_GPC['cpnumber']), 'myprice' => intval($_GPC['myprice']), 'price' => $_GPC['price'], 'deal_days' => strtotime($_GPC['deal_days']), 'isrecommand' => intval($_GPC['isrecommand']), 'ishot' => intval($_GPC['ishot']), 'wtname' => iserializer($_GPC['wtname']), 'pcate' => intval($_GPC['pcate']), 'ccate' => intval($_GPC['ccate']), 'tjqian' => intval($_GPC['tjqian']), 'tjhou' => intval($_GPC['tjhou']), 'thumb' => $_GPC['thumb'], 'content' => htmlspecialchars_decode($_GPC['content']), 'nosubuser' => intval($_GPC['nosubuser']), 'subsurl' => trim($_GPC['subsurl']), 'direct' => $_GPC['direct'], 'starttime' => strtotime($_GPC['starttime']), 'show_type' => intval($_GPC['show_type']), 'type' => intval($_GPC['type']), 'lianxiren' => $_GPC['lianxiren'], 'tel' => $_GPC['tel'], 'status' => 3, 'createtime' => TIMESTAMP,);

                    // 品牌
                    $brands = array();
                    $brands = $_GPC['brands'];
                    // 品牌合并
                    $data['brands'] = implode(',', $brands);

                    $cates = $_GPC['cates'];
                    $data['cates'] = implode(',', $cates);

                    // 邮费
                    $data['freight'] = $_GPC['freight'];

                    if (empty($id)) {
                        pdo_insert('sen_appfreeitem_project', $data);
                        $id = pdo_insertid();
                    } else {
                        unset($data['createtime']);
                        pdo_update('sen_appfreeitem_project', $data, array('id' => $id));
                    }
                    message('保存成功！', $this->createWebUrl('project', array('id' => $id, 'op' => 'display')), 'success');
                }
            } elseif ($step == 3) {
                if (checksubmit('display')) {
                    if (!empty($_GPC['displayorder'])) {
                        foreach ($_GPC['displayorder'] as $item_id => $displayorder) {
                            pdo_update('sen_appfreeitem_project_item', array('displayorder' => $displayorder), array('id' => $item_id));
                        }
                        message('排序更新成功！', $this->createWebUrl('project', array('id' => $id, 'op' => 'post', 'step' => '2')), 'success');
                    }
                }
                if (checksubmit('submit')) {
                    $insert = array('weid' => $_W['uniacid'], 'pid' => intval($_GPC['id']), 'displayorder' => intval($_GPC['displayorder']), 'price' => $_GPC['price'], 'description' => htmlspecialchars_decode($_GPC['description']), 'thumb' => $_GPC['thumb'], 'limit_num' => intval($_GPC['limit_num']), 'repaid_day' => intval($_GPC['repaid_day']), 'return_type' => intval($_GPC['return_type']), 'dispatch' => $_GPC['dispatch'], 'createtime' => TIMESTAMP,);
                    if (empty($item_id)) {
                        pdo_insert('sen_appfreeitem_project_item', $insert);
                    } else {
                        unset($insert['createtime']);
                        pdo_update('sen_appfreeitem_project_item', $insert, array('id' => $item_id));
                    }
                    message('保存成功,继续添加', $this->createWebUrl('project', array('id' => $id, 'op' => 'post', 'step' => '2')), 'success');
                }
                $items = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_project_item') . " WHERE weid = '{$_W['uniacid']}' AND pid = '{$id}' ORDER BY id ASC");
                if (empty($items)) {
                    message('您尚未添加产品回报，请返回添加', $this->createWebUrl('project', array('id' => $id, 'op' => 'post', 'step' => '2')), 'error');
                }
            } elseif ($step == 4) {
                if (checksubmit('finish')) {
                    pdo_update('sen_appfreeitem_project', array('status' => 3), array('id' => $id));
                    message('恭喜您，活动已经成功开始！', $this->createWebUrl('project', array('op' => 'display')), 'success');
                } else {
                    message('活动保存成功！', $this->createWebUrl('project', array('op' => 'display')), 'success');
                }
            }
        } elseif ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }
            if (!empty($_GPC['cate_2'])) {
                $cid = intval($_GPC['cate_2']);
                $condition .= " AND ccate = '{$cid}'";
            } elseif (!empty($_GPC['cate_1'])) {
                $cid = intval($_GPC['cate_1']);
                $condition .= " AND pcate = '{$cid}'";
            }
            if (isset($_GPC['status']) && $_GPC['status'] != '') {
                $condition .= " AND status = '" . intval($_GPC['status']) . "'";
            }
            $list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE weid = '{$_W['uniacid']}'  $condition ORDER BY status ASC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_project') . " WHERE weid = '{$_W['uniacid']}'  $condition");
            $pager = pagination($total, $pindex, $psize);
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id, thumb FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，产品不存在或是已经被删除！');
            }
            pdo_delete('sen_appfreeitem_project', array('id' => $id));
            pdo_delete('sen_appfreeitem_project_item', array('pid' => $id));
            message('删除成功！', referer(), 'success');
        } elseif ($operation == 'itemdelete') {
            $id = intval($_GPC['id']);
            $item_id = intval($_GPC['item_id']);
            $row = pdo_fetch("SELECT id, thumb FROM " . tablename('sen_appfreeitem_project_item') . " WHERE id = :id", array(':id' => $item_id));
            if (empty($row)) {
                message('抱歉，产品不存在或是已经被删除！');
            }
            pdo_delete('sen_appfreeitem_project_item', array('id' => $item_id));
            message('删除成功！', $this->createWebUrl('project', array('id' => $id, 'op' => 'post', 'step' => '2')), 'success');
        }
        include $this->template('project');
    }


    // 设置属性
    public function doWebSetProjectProperty()
    {
        global $_GPC, $_W;
        // var_dump(111);exit;
        if ($_GPC['op'] == 'checkproject') {
            $project_id = intval($_GPC['project_id']);
            $status = intval($_GPC['status']);
            $reson = $_GPC['reson'];
            pdo_update("sen_appfreeitem_project", array('status' => $status, 'reason' => $reson, 'starttime' => time()), array("id" => $project_id, "weid" => $_W['uniacid']));
            die(json_encode(array("result" => 1, 'project_id' => $project_id)));
        } else {
            $id = intval($_GPC['id']);
            $type = $_GPC['type'];
            $data = intval($_GPC['data']);
            if (in_array($type, array('hot', 'recommand'))) {
                $data = ($data == 1 ? '0' : '1');
                pdo_update("sen_appfreeitem_project", array("is" . $type => $data), array("id" => $id, "weid" => $_W['uniacid']));
                die(json_encode(array("result" => 1, "data" => $data)));
            }
            if (in_array($type, array('status'))) {
                $data = ($data == 1 ? '0' : '1');
                pdo_update("sen_appfreeitem_project", array($type => $data), array("id" => $id, "weid" => $_W['uniacid']));
                die(json_encode(array("result" => 1, "data" => $data)));
            }
            die(json_encode(array("result" => 0)));
        }
    }


    public function doWebCategory()
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update('sen_appfreeitem_category', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
            }
            $children = array();
            $category = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
            foreach ($category as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($category[$index]);
                }
            }
            include $this->template('category');
        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $category = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_category') . " WHERE id = '$id'");
            } else {
                $category = array('displayorder' => 0,);
            }
            if (!empty($parentid)) {
                $parent = pdo_fetch("SELECT id, name FROM " . tablename('sen_appfreeitem_category') . " WHERE id = '$parentid'");
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入分类名称！');
                }
                $data = array('weid' => $_W['uniacid'], 'name' => $_GPC['catename'], 'thumb' => $_GPC['thumb'], 'enabled' => intval($_GPC['enabled']), 'displayorder' => intval($_GPC['displayorder']), 'isrecommand' => intval($_GPC['isrecommand']), 'parentid' => intval($parentid),);
                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update('sen_appfreeitem_category', $data, array('id' => $id));
                } else {
                    pdo_insert('sen_appfreeitem_category', $data);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
            }
            include $this->template('category');
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $category = pdo_fetch("SELECT id, parentid FROM " . tablename('sen_appfreeitem_category') . " WHERE id = '$id'");
            if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
            }
            pdo_delete('sen_appfreeitem_category', array('id' => $id, 'parentid' => $id), 'OR');
            message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
        }
    }

    public function doWebDispatch()
    {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_dispatch') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                $data = array('weid' => $_W['uniacid'], 'displayorder' => intval($_GPC['displayorder']), 'dispatchtype' => intval($_GPC['dispatchtype']), 'dispatchname' => $_GPC['dispatchname'], 'express' => $_GPC['express'], 'firstprice' => $_GPC['firstprice'], 'firstweight' => $_GPC['firstweight'], 'secondprice' => $_GPC['secondprice'], 'secondweight' => $_GPC['secondweight'], 'description' => $_GPC['description']);
                if (!empty($id)) {
                    pdo_update('sen_appfreeitem_dispatch', $data, array('id' => $id));
                } else {
                    pdo_insert('sen_appfreeitem_dispatch', $data);
                    $id = pdo_insertid();
                }
                message('更新配送方式成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
            }
            $dispatch = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_dispatch') . " WHERE id = '$id' and weid = '{$_W['uniacid']}'");
            $express = pdo_fetchall("select * from " . tablename('sen_appfreeitem_express') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $dispatch = pdo_fetch("SELECT id  FROM " . tablename('sen_appfreeitem_dispatch') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
            if (empty($dispatch)) {
                message('抱歉，配送方式不存在或是已经被删除！', $this->createWebUrl('dispatch', array('op' => 'display')), 'error');
            }
            pdo_delete('sen_appfreeitem_dispatch', array('id' => $id));
            message('配送方式删除成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('dispatch', TEMPLATE_INCLUDEPATH, true);
    }

    public function doWebExpress()
    {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_express') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                if (empty($_GPC['express_name'])) {
                    message('抱歉，请输入物流名称！');
                }
                $data = array('weid' => $_W['uniacid'], 'displayorder' => intval($_GPC['displayorder']), 'express_name' => $_GPC['express_name'], 'express_url' => $_GPC['express_url'], 'express_area' => $_GPC['express_area'],);
                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update('sen_appfreeitem_express', $data, array('id' => $id));
                } else {
                    pdo_insert('sen_appfreeitem_express', $data);
                    $id = pdo_insertid();
                }
                message('更新物流成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
            }
            $express = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_express') . " WHERE id = '$id' and weid = '{$_W['uniacid']}'");
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $express = pdo_fetch("SELECT id  FROM " . tablename('sen_appfreeitem_express') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
            if (empty($express)) {
                message('抱歉，物流方式不存在或是已经被删除！', $this->createWebUrl('express', array('op' => 'display')), 'error');
            }
            pdo_delete('sen_appfreeitem_express', array('id' => $id));
            message('物流方式删除成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('express', TEMPLATE_INCLUDEPATH, true);
    }

    public function doWebAdv()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('sen_appfreeitem_adv') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                $data = array('weid' => $_W['uniacid'], 'advname' => $_GPC['advname'], 'link' => $_GPC['link'], 'enabled' => intval($_GPC['enabled']), 'displayorder' => intval($_GPC['displayorder']), 'thumb' => $_GPC['thumb']);
                if (!empty($id)) {
                    pdo_update('sen_appfreeitem_adv', $data, array('id' => $id));
                } else {
                    pdo_insert('sen_appfreeitem_adv', $data);
                    $id = pdo_insertid();
                }
                message('更新幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
            }
            $adv = pdo_fetch("select * from " . tablename('sen_appfreeitem_adv') . " where id=:id and weid=:weid limit 1", array(":id" => $id, ":weid" => $_W['uniacid']));
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $adv = pdo_fetch("SELECT id  FROM " . tablename('sen_appfreeitem_adv') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
            if (empty($adv)) {
                message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('adv', array('op' => 'display')), 'error');
            }
            pdo_delete('sen_appfreeitem_adv', array('id' => $id));
            message('幻灯片删除成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('adv', TEMPLATE_INCLUDEPATH, true);
    }


    /*函数*/
    public function getprojectdetail($pid)
    {
        global $_GPC, $_W;
        $item = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('sen_appfreeitem_order') . " WHERE weid =:weid and state =:state and from_user =:openid and pid = :pid", array(':weid' => $_W['uniacid'], ':state' => '0', ':openid' => $_W['fans']['from_user'], ':pid' => $pid));
        return $item;
    }

    public function getproject($id)
    {
        $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = :id", array(':id' => $id));
        return $item;
    }

    public function getprojectorder($pid)
    {
        $item = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('sen_appfreeitem_order') . " WHERE pid = :pid", array(':pid' => $pid));
        return $item;
    }

    public function getprojectorder_ws($pid)
    {
        $item = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('sen_appfreeitem_order_ws') . " WHERE pid = :pid AND status=1", array(':pid' => $pid));
        return $item;
    }

    public function getnewscategory($id)
    {
        $item = pdo_fetchcolumn("SELECT title FROM " . tablename('sen_appfreeitem_report_category') . " WHERE id = :id", array(':id' => $id));
        return $item;
    }

    public function getitem($id)
    {
        $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project_item') . " WHERE id = :id", array(':id' => $id));
        return $item;
    }

    // 支付结果
    public function payResult($params)
    {
        global $_W;
        if ($params['result'] == 'success' && $params['from'] == 'return') {
            $fee = intval($params['fee']);
            $data = array('status' => $params['result'] == 'success' ? 2 : 0);
            $paytype = array('credit' => '1', 'wechat' => '2', 'alipay' => '2', 'delivery' => '3');
            $data['paytype'] = $paytype[$params['type']];
            if ($params['type'] == 'wechat') {
                $data['transid'] = $params['tag']['transaction_id'];
            }
            if ($params['type'] == 'delivery') {
                $data['status'] = 2;
            }
            if (substr($params['tid'], 0, 3) == 'ws-') {
                $params['tid'] = str_replace('ws-', '', $params['tid']);
                $order = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order_ws') . " WHERE id = '{$params['tid']}'");
                if ($order['status'] != 2) {
                    pdo_update('sen_appfreeitem_order_ws', $data, array('id' => $params['tid']));
                    $pid = $order['pid'];
                    $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = '{$pid}'");
                    pdo_update('sen_appfreeitem_project', array('finish_price' => $project['finish_price'] + $order['item_price']), array('id' => $pid));
                }
            } else {
                $order = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_order') . " WHERE id = '{$params['tid']}'");
                if ($order['status'] != 2) {
                    pdo_update('sen_appfreeitem_order', $data, array('id' => $params['tid']));
                    $pid = $order['pid'];
                    $item_id = $order['item_id'];
                    $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id = '{$pid}'");
                    $item = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project_item') . " WHERE id = '{$item_id}'");
                    pdo_update('sen_appfreeitem_project', array('finish_price' => $project['finish_price'] + $order['item_price'], 'donenum' => $project['donenum'] + 1), array('id' => $pid));
                    pdo_update('sen_appfreeitem_project_item', array('donenum' => $item['donenum'] + 1), array('id' => $item_id));
                }
                $address = pdo_fetch("SELECT * FROM " . tablename('mc_member_address') . " WHERE id = :id", array(':id' => $order['addressid']));
                $settings = $this->module['config'];
                if (!empty($settings['kfid']) && !empty($settings['k_templateid'])) {
                    $kfirst = empty($settings['kfirst']) ? '您有一个新的订单' : $settings['kfirst'];
                    $kfoot = empty($settings['kfoot']) ? '请及时处理，点击可查看详情' : $settings['kfoot'];
                    $kurl = '';
                    $kdata = array('first' => array('value' => $kfirst, 'color' => '#ff510'), 'keyword1' => array('value' => $order['ordersn'], 'color' => '#ff510'), 'keyword2' => array('value' => $project['title'], 'color' => '#ff510'), 'keyword3' => array('value' => $order['price'] . '元', 'color' => '#ff510'), 'keyword4' => array('value' => $address['username'], 'color' => '#ff510'), 'keyword5' => array('value' => $params['type'], 'color' => '#ff510'), 'remark' => array('value' => $kfoot, 'color' => '#ff510'),);
                    $acc = WeAccount::create();
                    $acc->sendTplNotice($settings['kfid'], $settings['k_templateid'], $kdata, $kurl, $topcolor = '#FF683F');
                }
                if (!empty($settings['m_templateid'])) {
                    $mfirst = empty($settings['mfirst']) ? '支付成功通知' : $settings['mfirst'];
                    $mfoot = empty($settings['mfoot']) ? '点击查看订单详情' : $settings['mfoot'];
                    $murl = $_W['siteroot'] . 'app' . str_replace('./', '/', $this->createMobileUrl('myorder', array('op' => 'detail', 'orderid' => $order['id'])));
                    $mdata = array('first' => array('value' => $mfirst, 'color' => '#ff510'), 'keyword1' => array('value' => $address['username'], 'color' => '#ff510'), 'keyword2' => array('value' => $order['ordersn'], 'color' => '#ff510'), 'keyword3' => array('value' => $order['price'] . '元', 'color' => '#ff510'), 'keyword4' => array('value' => $project['title'], 'color' => '#ff510'), 'remark' => array('value' => $mfoot, 'color' => '#ff510'),);
                    $acc = WeAccount::create();
                    $acc->sendTplNotice($order['from_user'], $settings['m_templateid'], $mdata, $murl, $topcolor = '#FF683F');
                }
                if (!empty($this->module['config']['noticeemail'])) {
                    $body = "<h3>申请产品详情</h3> <br />";
                    $body .= "名称：{$project['title']} <br />";
                    $body .= "<br />支持金额：{$order['price']}元 （已付款）<br />";
                    $body .= "<h3>购买用户详情</h3> <br />";
                    $body .= "真实姓名：{$address['username']} <br />";
                    $body .= "地区：{$address['province']} - {$address['city']} - {$address['district']}<br />";
                    $body .= "详细地址：{$address['address']} <br />";
                    $body .= "手机：{$address['mobile']} <br />";
                    load()->func('communication');
                    ihttp_email($this->module['config']['noticeemail'], '产品申请提醒', $body);
                }
                $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
                $credit = $setting['creditbehaviors']['currency'];
            }
        }
        if ($params['from'] == 'return') {
            if ($params['type'] == $credit) {
                message('支付成功1！', $this->createMobileUrl('myorder'), 'success');
            } else {
                message('支付成功2！', '../../app/' . $this->createMobileUrl('myorder'), 'success');
            }
        }
    }

    // 检查登录信息
    private function checkAuthSession()
    {
        global $_W, $_GPC;
        /*var_dump('TODO debug $_W, $_GPC==', $_W, $_GPC, '$_SESSION==', $_SESSION);
        exit;*/
        // TODO debug
        /*unset($_SESSION['openid']);
        if ($_GPC['debug']) {
            $_SESSION['openid'] = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }*/

        // var_dump($_SERVER);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $return_url = getenv("HTTP_REFERER");

        if (empty($_SESSION['openid'])) {
            // 判断是否有民生信息返回
            $chiperTxt = $_REQUEST['chiperTxt'];
            WeUtility::logging('民生信息返回$chiperTxt==' . $chiperTxt);

            // $chiperTxt = 'plxIaWGVLEwO9uWJRklDyDhWprbTb9rfaHsnGCs/jJ2YabAwvz99ZkBpoahObXxj';
            // $chiperTxt = 'plxIaWGVLEwO9uWJRklDyDhWprbTb9rfaHsnGCs/jJ2YabAwvz99ZkBpoahObXxj22';
            if ($chiperTxt) {
                $keyStr = 'JiYqrz583wzVghMAnsFzbg==';
//DecryptAndCheck::checkWithTimeStamp
//函数功能：使用AES解密，得到以竖线分割的字符串，取出最后一段的时间戳，与当前时间相比。如果之差的绝对值小于$timeStamp，则返回AES解密的报文，否则返回null；异常时也返回null
//入参：  $encryptContent:密文    $keyStr:密钥    $timeStamp：允许的时间差（单位毫秒）
//返回： $encryptContent对应的明文或者null
//$plainTxt = DecryptAndCheck::checkWithTimeStamp($chiperTxt, $keyStr, 500000000.1);
                $plainTxt = DecryptAndCheck::checkWithTimeStamp($chiperTxt, $keyStr, 500000000000.1); // 15.854896	年(yr)
                WeUtility::logging('民生信息返回$chiperTxt==' . $chiperTxt . '==解码$plainTxt==' . $plainTxt);

                // var_dump($chiperTxt,$plainTxt,$res,$plainTxt[0],$_SESSION['openid']);exit;
                if (!$plainTxt) {
                    echo '联合登录有误，请<a href="' . $return_url . '">返回</a>重新操作';
                    exit;
                    // echo"<script>alert('联合登录有误，返回重新操作');history.go(-1);</script>";
                    // var_dump('TODO debug $plainTxt==', $plainTxt);
                } else {
                    // 解密成功
                    $res = explode('|', $plainTxt);
                    var_dump('接口返回成功', '返回文本：' . $plainTxt, '|分割：', $res, $plainTxt[0], $_SESSION['openid']);exit;

                    // 写入数据库
                    $pitem = pdo_fetch("SELECT * FROM " . tablename('mc_members') . " WHERE out_uid=:out_uid ", array(':out_uid' => $res[0]));
                    if (empty($pitem)) {
                        $data = array('out_uid' => $res[0], 'mobile' => $res[1], 'out_uid' => $res[2], 'createtime' => time(),);
                        pdo_insert('mc_members', $data);
                    }
                    $_SESSION['openid'] = $res[0];
                }
            } else {
                // 引入登录js
                // $url = $_W['siteroot'].$_SERVER['REQUEST_URI'];
                // $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                $eof = <<<EOT
                <html>
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf8" />
                <title>登录</title>
                <script type="text/javascript" src="../payment/unionpay/ms_lajp/cmbcForClient.js"></script>
                </head>
                <body>
                <script >
                loginForComm("{$return_url}", "{$url}");
                </script>
                </body>
                </html>
EOT;
                echo $eof;
                exit;
            }


        }
    }

    private function checkAuth()
    {
        global $_W, $_GPC;
        // var_dump('TODO debug $_W, $_GPC==',$_W, $_GPC,'$_SESSION==',$_SESSION);exit;
        // TODO debug
        if ($_GPC['debug']) {
            $_W['openid'] = 'oMaz50jp9G_xRU_JT1jMaxuS5KdY';
        }

        if (empty($_W['openid'])) {
            if (!empty($_W['account']['subscribeurl'])) {
                message('请先关注公众号' . $_W['account']['name'] . '(' . $_W['account']['account'] . ')', $_W['account']['subscribeurl'], 'error');
            } else {
                exit('请先关注公众号' . $_W['account']['name'] . '(' . $_W['account']['account'] . ')');
            }
        }
    }

    public function getCartTotal()
    {
        global $_W;
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sen_appfreeitem_cart') . " WHERE weid = '{$_W['uniacid']}'  AND from_user = '{$_W['fans']['from_user']}'");
        return empty($total) ? 0 : $total;
    }

    protected function exportexcel($data = array(), $title = array(), $filename = 'report')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "GB2312", $v);
            }
            $title = implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
                }
                $data[$key] = implode("\t", $data[$key]);
            }
            echo implode("\n", $data);
        }
    }

    public function getFinishPrice($pid)
    {
        $project = pdo_fetch("SELECT * FROM " . tablename('sen_appfreeitem_project') . " WHERE id=:id", array(':id' => $pid));
        $wc = pdo_fetchcolumn("SELECT SUM(price) FROM " . tablename('sen_appfreeitem_order_ws') . " WHERE pid='{$pid}' AND status=1");
        return $project['finish_price'] + $wc;
    }

    private function sendMoney($openid, $money, $desc = '退款')
    {
        global $_W;
        $uniacid = $_W['uniacid'];
        $api = $this->module['config'];
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
        $pars = array();
        $pars['mch_appid'] = $api['appid'];
        $pars['mchid'] = $api['mchid'];
        $pars['nonce_str'] = random(32);
        $pars['partner_trade_no'] = date('Ymd') . rand(1, 100);
        $pars['openid'] = $openid;
        $pars['check_name'] = 'NO_CHECK';
        $pars['amount'] = $money;
        $pars['desc'] = $desc;
        $pars['spbill_create_ip'] = $api['ip'];
        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v) {
            $string1 .= "{$k}={$v}&";
        }
        $string1 .= "key={$api['password']}";
        $pars['sign'] = strtoupper(md5($string1));
        $xml = array2xml($pars);
        $extras = array();
        $extras['CURLOPT_CAINFO'] = ZC_ROOT . '/cert/rootca.pem.' . $api['pemname'];
        $extras['CURLOPT_SSLCERT'] = ZC_ROOT . '/cert/apiclient_cert.pem.' . $api['pemname'];
        $extras['CURLOPT_SSLKEY'] = ZC_ROOT . '/cert/apiclient_key.pem.' . $api['pemname'];
        load()->func('communication');
        $procResult = null;
        $response = ihttp_request($url, $xml, $extras);
        if ($response['code'] == 200) {
            $responseObj = simplexml_load_string($response['content'], 'SimpleXMLElement', LIBXML_NOCDATA);
            $responseObj = (array)$responseObj;
            $return['code'] = $responseObj['return_code'];
            $return['err_code'] = $responseObj['err_code'];
            $return['msg'] = $responseObj['return_msg'];
            return $return;
        }
    }

    public function sendtempmsg($template_id, $url, $data, $topcolor, $tousers = '')
    {
        load()->func('communication');
        load()->classs('weixin.account');
        $access_token = WeAccount::token();
        if (empty($access_token)) {
            return;
        }
        $postarr = '{"touser":"' . $tousers . '","template_id":"' . $template_id . '","url":"' . $url . '","topcolor":"' . $topcolor . '","data":' . $data . '}';
        $res = ihttp_post('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token, $postarr);
        return true;
    }


    // TODO 获取全部品牌
    public function getFullBrand($fullname = false, $enabled = false)
    {
        global $_W;
        $sql = 'SELECT * FROM ' . tablename('ewei_shop_brand') . ' WHERE uniacid=:uniacid ';

        if ($enabled) {
            $sql .= ' AND enabled=1';
        }
        $sql .= ' ORDER BY displayorder DESC';
        $brand = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
        $brand = set_medias($brand, array('thumb', 'logo'));

        if (empty($brand)) {
            return array();
        }
        return $brand;
    }

    public function getFullCategory($fullname = false, $enabled = false)
    {
        global $_W;
        $allcategory = array();
        $sql = 'SELECT * FROM ' . tablename('ewei_shop_category') . ' WHERE uniacid=:uniacid ';

        if ($enabled) {
            $sql .= ' AND enabled=1';
        }

        $sql .= ' ORDER BY parentid ASC, displayorder DESC';
        $category = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
        $category = set_medias($category, array('thumb', 'advimg'));

        if (empty($category)) {
            return array();
        }

        foreach ($category as &$c) {
            if (empty($c['parentid'])) {
                $allcategory[] = $c;

                foreach ($category as &$c1) {
                    if ($c1['parentid'] != $c['id']) {
                        continue;
                    }

                    if ($fullname) {
                        $c1['name'] = $c['name'] . '-' . $c1['name'];
                    }

                    $allcategory[] = $c1;

                    foreach ($category as &$c2) {
                        if ($c2['parentid'] != $c1['id']) {
                            continue;
                        }

                        if ($fullname) {
                            $c2['name'] = $c1['name'] . '-' . $c2['name'];
                        }

                        $allcategory[] = $c2;

                        foreach ($category as &$c3) {
                            if ($c3['parentid'] != $c2['id']) {
                                continue;
                            }

                            if ($fullname) {
                                $c3['name'] = $c2['name'] . '-' . $c3['name'];
                            }

                            $allcategory[] = $c3;
                        }

                        unset($c3);
                    }

                    unset($c2);
                }

                unset($c1);
            }

            unset($c);
        }

        return $allcategory;
    }


    // 检测敏感词
    function check_work($key = '', $content = '')
    {
        $user_arr = explode('|', $key);
        $r = array();
        foreach ($user_arr as $value) {
            if (strpos($content, $value) !== false) {
                $r[] = $value;
            }
        }
        if ($r) {
            return implode(",", $r);
        } else {
            return false;
        }
    }


    function echojson($code = '', $msg = 0, $data = array()) {
        header('Response-Server: ' . SERVER_NAME);
        $arr = array('code' => $code, 'msg' => $msg, 'data' => $data);
        return json_encode($arr);
        exit();
    }

}

?>