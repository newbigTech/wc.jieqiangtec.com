<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Brand_EweiShopV2Page extends MobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
        $brand = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_brand') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY displayorder DESC, id DESC');
        foreach ($brand as $k=>$v) {
            if ($v['enabled'] == 1) {
                $brand[$k]['logo'] = tomedia($v['logo']);
            }
        }


		// var_dump('TODO jieqiangtest==pwd==',getcwd(),__FILE__,$_W['shopset']);exit;
		// $merchid = intval($_GPC['merchid']);
		// $brand_set = $_W['shopset']['brand'];
		// $brand_set['advimg'] = tomedia($brand_set['advimg']);

		/*if ($brand_set['level'] == -1) {
			$this->message('暂时未开启分类', '', 'error');
		}*/

		// $brand = $this->getBrand($brand_set['level'], $merchid);
		$set = m('common')->getSysset('brand');
//         var_dump('TODO jieqiangtest==pwd==',getcwd(),__FILE__,$_GPC['merchid'],$_W['shopset']);exit;
		var_dump($brand,debug_backtrace());exit;
		include $this->template();
	}

	protected function getBrand($level, $merchid = 0)
	{
		$level = intval($level);
		$category = m('shop')->getBrand();
		$category_parent = array();
		$category_children = array();
		$category_grandchildren = array();

		if (0 < $merchid) {
			$merch_plugin = p('merch');
			$merch_data = m('common')->getPluginset('merch');
			if ($merch_plugin && $merch_data['is_openmerch']) {
				$is_openmerch = 1;
			}
			else {
				$is_openmerch = 0;
			}

			if ($is_openmerch) {
				$merch_category = $merch_plugin->getSet('merch_category', $merchid);

				if (!empty($merch_category)) {
					if (!empty($category['parent'])) {
						foreach ($category['parent'] as $key => $value) {
							if (array_key_exists($value['id'], $merch_category)) {
								$category['parent'][$key]['enabled'] = $merch_category[$value['id']];
							}
						}
					}

					if (!empty($category['children'])) {
						foreach ($category['children'] as $key => $value) {
							if (!empty($value)) {
								foreach ($value as $k => $v) {
									if (array_key_exists($v['id'], $merch_category)) {
										$category['children'][$key][$k]['enabled'] = $merch_category[$v['id']];
									}
								}
							}
						}
					}
				}
			}
		}

		foreach ($category['parent'] as $value) {
			if ($value['enabled'] == 1) {
				$value['thumb'] = tomedia($value['thumb']);
				$value['advimg'] = tomedia($value['advimg']);
				$category_parent[$value['parentid']][] = $value;
				if (!empty($category['children'][$value['id']]) && (2 <= $level)) {
					foreach ($category['children'][$value['id']] as $val) {
						if ($val['enabled'] == 1) {
							$val['thumb'] = tomedia($val['thumb']);
							$val['advimg'] = tomedia($val['advimg']);
							$category_children[$val['parentid']][] = $val;
							if (!empty($category['children'][$val['id']]) && (3 <= $level)) {
								foreach ($category['children'][$val['id']] as $v) {
									if ($v['enabled'] == 1) {
										$v['thumb'] = tomedia($v['thumb']);
										$v['advimg'] = tomedia($v['advimg']);
										$category_grandchildren[$v['parentid']][] = $v;
									}
								}
							}
						}
					}
				}
			}
		}

		return array('parent' => $category_parent, 'children' => $category_children, 'grandchildren' => $category_grandchildren);
	}
}

?>
