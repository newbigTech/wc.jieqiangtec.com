<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Brand_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			if (!empty($_GPC['datas'])) {
				$datas = json_decode(html_entity_decode($_GPC['datas']), true);

				if (!is_array($datas)) {
					show_json(0, '品牌保存失败，请重试!');
				}

				$brandids = array();
				$displayorder = count($datas);

				foreach ($datas as $row) {
					$brandids[] = $row['id'];
					pdo_update('ewei_shop_brand', array( 'displayorder' => $displayorder), array('id' => $row['id']));
					--$displayorder;
				}

				if (!empty($brandids)) {
					pdo_query('delete from ' . tablename('ewei_shop_brand') . ' where id not in (' . implode(',', $brandids) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
				}

				plog('shop.brand.edit', '批量修改品牌的层级及排序');
				m('shop')->getCategory(true);
				m('shop')->getAllCategory(true);
				show_json(1);
			}
		}

		$brand = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_brand') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY displayorder DESC, id DESC');
		 // var_dump('TODO jieqiangtest=D:\www\users\wc.jieqiangtec.com\addons\ewei_shopv2\core\web\goods\brand.php=$brand==',$brand);exit;

        // var_dump('TODO jieqiangatest==trance==',debug_backtrace());exit;


		include $this->template();
	}

	public function add()
	{
		$this->post();
	}

	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (!empty($id)) {
			$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_brand') . ' WHERE id = \'' . $id . '\' limit 1');
		}
		else {
			$item = array('displayorder' => 9999);
		}

		if (!empty($item)) {
			$item['url'] = mobileUrl('goods', array('brand' => $item['id']), 1);
			$item['qrcode'] = m('qrcode')->createQrcode($item['url']);
		}

		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'], 'name' => trim($_GPC['name']),'name_eng' => trim($_GPC['name_eng']), 'enabled' => intval($_GPC['enabled']), 'displayorder' => intval($_GPC['displayorder']), 'description' => $_GPC['description'], 'logo' => save_media($_GPC['logo']), 'advurl' => trim($_GPC['advurl']));

			if (!empty($id)) {
				pdo_update('ewei_shop_brand', $data, array('id' => $id));
				load()->func('file');
				file_delete($_GPC['thumb_old']);
				plog('shop.brand.edit', '修改品牌 ID: ' . $id);
			}
			else {
				pdo_insert('ewei_shop_brand', $data);
				$id = pdo_insertid();
				plog('shop.brand.add', '添加品牌 ID: ' . $id.' 品牌名称: ' . trim($_GPC['name']));
			}

			m('shop')->getCategory(true);
			m('shop')->getAllCategory(true);
			show_json(1, array('url' => webUrl('goods/brand')));
		}

		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT id, name FROM ' . tablename('ewei_shop_brand') . ' WHERE id = \'' . $id . '\'');

		if (empty($item)) {
			$this->message('抱歉，品牌不存在或是已经被删除！', webUrl('goods/brand', array('op' => 'display')), 'error');
		}

		pdo_delete('ewei_shop_brand', array('id' => $id), 'OR');
		plog('shop.brand.delete', '删除品牌 ID: ' . $id . ' 品牌名称: ' . $item['name']);
		// m('shop')->getCategory(true);
		show_json(1, array('url' => referer()));
	}

	public function enabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('ewei_shop_brand') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('ewei_shop_brand', array('enabled' => intval($_GPC['enabled'])), array('id' => $item['id']));
			plog('shop.dispatch.edit', ('修改品牌状态<br/>ID: ' . $item['id'] . '<br/>品牌名称: ' . $item['name'] . '<br/>状态: ' . $_GPC['enabled']) == 1 ? '显示' : '隐藏');
		}

		// m('shop')->getCategory(true);
		show_json(1, array('url' => referer()));
	}

	public function query()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and enabled=1 and uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' AND `name` LIKE :keyword';
			$params[':keyword'] = '%' . $kwd . '%';
		}

		$ds = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_brand') . ' WHERE 1 ' . $condition . ' order by displayorder desc,id desc', $params);
		$ds = set_medias($ds, array('thumb', 'logo'));

		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		include $this->template();
	}
}

?>
